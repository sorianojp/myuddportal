<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Models\StudentEnrollment;
use App\Models\SubSection;
use App\Models\CurrentSchoolYear;

class SubjectLoadController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $currentTerm = CurrentSchoolYear::getCurrent();
        $defaultSy = $currentTerm ? "{$currentTerm->CUR_SCHYR_FROM}-{$currentTerm->CUR_SCHYR_TO}" : null;
        $defaultSem = $currentTerm ? (string) $currentTerm->CUR_SEMESTER : null;

        $enrollments = StudentEnrollment::with([
            'subSection' => function ($q) {
                $q->select([
                    'SUB_SEC_INDEX', 'SUB_INDEX', 'SECTION',
                    'OFFERING_SY_FROM', 'OFFERING_SY_TO', 'OFFERING_SEM'
                ]);
            },
            'subSection.subject' => function ($q) {
                $q->select(['SUB_INDEX', 'SUB_CODE', 'SUB_NAME']);
            },
        ])
            ->select(['USER_INDEX', 'SUB_SEC_INDEX']) // Needed fields only
            ->valid()
            ->where('USER_INDEX', $user->USER_INDEX)
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

        $groupedByTerm = $grouped->groupBy(function ($item) {
            return "{$item['SY_FROM']}-{$item['SY_TO']}-S{$item['SEMESTER']}";
        });

        $sortedKeys = $groupedByTerm->keys()
            ->map(fn($key) => [
                'key'      => $key,
                'year'     => (int) explode('-', $key)[0],
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

        $sortedGrouped = $sortedKeys->mapWithKeys(
            fn($key) => [$key => $groupedByTerm[$key]]
        );

        return Inertia::render('subjectLoad/index', [
            'enrolledSubjects' => $sortedGrouped,
            'defaultSy' => $defaultSy,
            'defaultSem' => $defaultSem,
        ]);
    }
}
