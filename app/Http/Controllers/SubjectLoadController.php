<?php
namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Models\StudentEnrollment;

class SubjectLoadController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 1) pull in everything via Eloquent
        $enrollments = StudentEnrollment::with([
            'subSection.subject',
            'subSection.roomAssigns.roomDetail',
            'subSection.facultyLoads.faculty',
        ])
        ->valid()
        ->where('USER_INDEX', $user->USER_INDEX)
        ->get();

        // 2) flatten into the same shape your old raw query gave you
        $flat = $enrollments->flatMap(function($e) {
            $sec = $e->subSection;
            return $sec->roomAssigns->map(function($ra) use ($e, $sec) {
                return (object)[
                    'SY_FROM'          => $e->SY_FROM,
                    'SY_TO'            => $e->SY_TO,
                    'CURRENT_SEMESTER' => $e->CURRENT_SEMESTER,
                    'SUB_CODE'         => $sec->subject->SUB_CODE,
                    'SUB_NAME'         => $sec->subject->SUB_NAME,
                    'SECTION'          => $sec->SECTION,
                    'tot_acad_unit'    => $sec->subject->tot_acad_unit,
                    'WEEK_DAY'         => $ra->WEEK_DAY,
                    'HOUR_FROM_24'     => $ra->HOUR_FROM_24,
                    'HOUR_TO_24'       => $ra->HOUR_TO_24,
                    'ROOM_NUMBER'      => $ra->roomDetail->ROOM_NUMBER ?? null,
                    'faculty_name'     => optional(
                        $sec->facultyLoads->first()?->faculty
                    )->getFullNameAttribute(),
                ];
            });
        });

        // 3) group by “YYYY-YYYY-S#”
        $rawGrouped = $flat->groupBy(fn($item) =>
            "{$item->SY_FROM}-{$item->SY_TO}-S{$item->CURRENT_SEMESTER}"
        );

        // 4) sort by year desc, then sem order Summer>2nd>1st
        $sortedKeys = collect($rawGrouped->keys())
            ->map(fn($key) => [
                'key'      => $key,
                'year'     => (int) explode('-', $key)[0],
                'semOrder' => match ((int)substr($key, -1)) {
                    0 => 0, 2 => 1, 1 => 2, default => 3
                },
            ])
            ->sortByDesc('year')
            ->groupBy('year')
            ->map(fn($grp) => $grp->sortBy('semOrder'))
            ->flatten(1)
            ->pluck('key');

        $sortedGrouped = $sortedKeys
            ->mapWithKeys(fn($k) => [ $k => $rawGrouped[$k] ]);

        // 5) hand off to Inertia
        return Inertia::render('subjectLoad/index', [
            'enrolledSubjects' => $sortedGrouped,
        ]);
    }
}
