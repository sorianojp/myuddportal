<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\StudentEnrollment;
use App\Models\SubSection;
use App\Models\CurrentSchoolYear;

class SubjectLoadController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $currentTerm = CurrentSchoolYear::getCurrent();
        $defaultSy = $currentTerm ? "{$currentTerm->CUR_SCHYR_FROM}-{$currentTerm->CUR_SCHYR_TO}" : null;
        $defaultSem = $currentTerm ? (string) $currentTerm->CUR_SEMESTER : null;

        $selectedSy = $request->input('sy', $defaultSy);
        $selectedSem = $request->input('sem', $defaultSem);

        // ✅ 1. Build available terms from ALL enrollment records
        $availableTerms = StudentEnrollment::with('subSection')
            ->where('USER_INDEX', $user->USER_INDEX)
            ->valid()
            ->get()
            ->pluck('subSection')
            ->filter()
            ->map(fn($s) => [
                'SY_FROM' => $s->OFFERING_SY_FROM,
                'SY_TO' => $s->OFFERING_SY_TO,
                'SEMESTER' => $s->OFFERING_SEM,
            ])
            ->unique(fn($term) => "{$term['SY_FROM']}-{$term['SY_TO']}-{$term['SEMESTER']}")
            ->sortByDesc('SY_FROM')
            ->values()
            ->all();

        // ✅ 2. Filtered load of subject schedule
        $enrollments = StudentEnrollment::with([
            'subSection' => fn($q) => $q->select([
                'SUB_SEC_INDEX', 'SUB_INDEX', 'SECTION',
                'OFFERING_SY_FROM', 'OFFERING_SY_TO', 'OFFERING_SEM'
            ]),
            'subSection.subject' => fn($q) => $q->select(['SUB_INDEX', 'SUB_CODE', 'SUB_NAME']),
        ])
        ->select(['USER_INDEX', 'SUB_SEC_INDEX'])
        ->valid()
        ->where('USER_INDEX', $user->USER_INDEX)
        ->when($selectedSy !== 'all', function ($q) use ($selectedSy) {
            [$syFrom, $syTo] = explode('-', $selectedSy);
            $q->whereHas('subSection', fn($q2) => $q2
                ->where('OFFERING_SY_FROM', $syFrom)
                ->where('OFFERING_SY_TO', $syTo)
            );
        })
        ->when($selectedSem !== 'all', function ($q) use ($selectedSem) {
            $q->whereHas('subSection', fn($q2) => $q2->where('OFFERING_SEM', $selectedSem));
        })
        ->get();

        $grouped = collect($enrollments)->flatMap(function ($enrollment) {
            $baseSection = $enrollment->subSection;

            $relatedSections = SubSection::with([
                'subject',
                'roomAssigns.roomDetail',
                'facultyLoads.faculty',
            ])
            ->where('SUB_INDEX', $baseSection->SUB_INDEX)
            ->where('SECTION', $baseSection->SECTION)
            ->where('OFFERING_SY_FROM', $baseSection->OFFERING_SY_FROM)
            ->where('OFFERING_SY_TO', $baseSection->OFFERING_SY_TO)
            ->where('OFFERING_SEM', $baseSection->OFFERING_SEM)
            ->valid()
            ->get();

            $scheduleBlocks = [];
            $facultySet = collect();

            foreach ($relatedSections as $section) {
                foreach ($section->roomAssigns as $ra) {
                    $day = date('l', strtotime("Sunday +{$ra->WEEK_DAY} days"));
                    $from = date('g:i A', strtotime("{$ra->HOUR_FROM_24}:00"));
                    $to = date('g:i A', strtotime("{$ra->HOUR_TO_24}:00"));
                    $room = $ra->roomDetail->ROOM_NUMBER ?? 'TBA';
                    $isLab = $ra->IS_LEC ? ' (lab)' : '';
                    $scheduleBlocks[] = "$day: $from - $to$isLab ($room)";
                }

                $latestFaculty = $section->facultyLoads
                    ->sortByDesc(fn($f) => $f->FACULTY_LOAD_ID ?? $f->USER_INDEX)
                    ->first()?->faculty?->getFullNameAttribute();

                if ($latestFaculty) {
                    $facultySet->push($latestFaculty);
                }
            }

            $facultyNames = $facultySet->unique()->implode(', ') ?: 'Unknown Faculty';

            return [[
                'SY_FROM'      => $baseSection->OFFERING_SY_FROM,
                'SY_TO'        => $baseSection->OFFERING_SY_TO,
                'SEMESTER'     => $baseSection->OFFERING_SEM,
                'SUB_CODE'     => $baseSection->subject->SUB_CODE,
                'SUB_NAME'     => $baseSection->subject->SUB_NAME,
                'SECTION'      => $baseSection->SECTION,
                'total_units'  => $relatedSections
                    ->flatMap(fn($sec) => $sec->facultyLoads)
                    ->unique('SUB_SEC_INDEX')
                    ->sum(fn($load) => (float) $load->LOAD_UNIT ?? 0),
                'schedule'     => implode(', ', $scheduleBlocks),
                'faculty_name' => $facultyNames,
            ]];
        });

        $groupedByTerm = $grouped->groupBy(fn($item) => "{$item['SY_FROM']}-{$item['SY_TO']}-S{$item['SEMESTER']}");

        $sortedKeys = $groupedByTerm->keys()
            ->map(fn($key) => [
                'key' => $key,
                'year' => (int) explode('-', $key)[0],
                'semOrder' => match ((int) substr($key, -1)) {
                    0 => 0,
                    2 => 1,
                    1 => 2,
                    default => 3,
                },
            ])
            ->sortByDesc('year')
            ->groupBy('year')
            ->map(fn($group) => $group->sortBy('semOrder'))
            ->flatten(1)
            ->pluck('key');

        $sortedGrouped = $sortedKeys->mapWithKeys(fn($key) => [$key => $groupedByTerm[$key]]);

        return Inertia::render('subjectLoad/index', [
            'enrolledSubjects' => $sortedGrouped,
            'availableTerms' => $availableTerms,
            'defaultSy' => $selectedSy,
            'defaultSem' => $selectedSem,
        ]);
    }
}
