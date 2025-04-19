<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Models\StudentEnrollment;
use App\Models\SubSection;

class SubjectLoadController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Step 1: Get all enrollments with base subsection & subject
        $enrollments = StudentEnrollment::with('subSection.subject')
            ->valid()
            ->where('USER_INDEX', $user->USER_INDEX)
            ->get();

        // Step 2: Build full subject-section blocks including all related subsections
        $grouped = collect($enrollments)->flatMap(function ($enrollment) {
            $baseSection = $enrollment->subSection;

            // Match all subsections (lec/lab) of the same subject, section, and term
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

            // Schedule strings
            $scheduleBlocks = [];

            // Collect most recent faculty assigned across all subsections
            $facultySet = collect();

            foreach ($relatedSections as $section) {

                $subject = $section->subject;

                foreach ($section->roomAssigns as $ra) {
                    $day = date('l', strtotime("Sunday +{$ra->WEEK_DAY} days"));
                    $from = date('g:i A', strtotime("{$ra->HOUR_FROM_24}:00"));
                    $to = date('g:i A', strtotime("{$ra->HOUR_TO_24}:00"));
                    $room = $ra->roomDetail->ROOM_NUMBER ?? 'TBA';
                    $isLab = $ra->IS_LEC ? ' (lab)' : '';

                    $scheduleBlocks[] = "$day: $from - $to$isLab ($room)";
                }

                // Pick latest faculty (like your SQL logic)
                $latestFaculty = $section->facultyLoads
                    ->sortByDesc(fn ($f) => $f->FACULTY_LOAD_ID ?? $f->USER_INDEX)
                    ->first()?->faculty?->getFullNameAttribute();

                if ($latestFaculty) {
                    $facultySet->push($latestFaculty);
                }
            }

            $facultyNames = $facultySet->unique()->implode(', ') ?: 'Unknown Faculty';

            return [[
                'SY_FROM'       => $baseSection->OFFERING_SY_FROM,
                'SY_TO'         => $baseSection->OFFERING_SY_TO,
                'SEMESTER'      => $baseSection->OFFERING_SEM,
                'SUB_CODE'      => $baseSection->subject->SUB_CODE,
                'SUB_NAME'      => $baseSection->subject->SUB_NAME,
                'SECTION'       => $baseSection->SECTION,
                'total_units' => $relatedSections
    ->flatMap(fn($sec) => $sec->facultyLoads)
    ->unique('SUB_SEC_INDEX')
    ->sum(fn($load) => (float) $load->LOAD_UNIT ?? 0),

                'schedule'      => implode(', ', $scheduleBlocks),
                'faculty_name'  => $facultyNames,
            ]];
        });

        // Step 3: Group by "YYYY-YYYY-S#"
        $groupedByTerm = $grouped->groupBy(function ($item) {
            return "{$item['SY_FROM']}-{$item['SY_TO']}-S{$item['SEMESTER']}";
        });

        // Step 4: Sort terms descending by year and semester (Summer > 2nd > 1st)
        $sortedKeys = $groupedByTerm->keys()
            ->map(fn($key) => [
                'key'      => $key,
                'year'     => (int) explode('-', $key)[0],
                'semOrder' => match ((int) substr($key, -1)) {
                    0 => 0, // Summer
                    2 => 1, // 2nd
                    1 => 2, // 1st
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
        ]);
    }
}
