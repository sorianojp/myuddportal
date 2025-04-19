<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CurrentSchoolYear;

class MyGradeController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $currentTerm = CurrentSchoolYear::getCurrent();
        $defaultSy = $currentTerm ? "{$currentTerm->CUR_SCHYR_FROM}-{$currentTerm->CUR_SCHYR_TO}" : null;
        $defaultSem = $currentTerm ? (string) $currentTerm->CUR_SEMESTER : null;

        $finalGrades = $user->finalGrades()
        ->select(['GS_INDEX', 'GRADE_NAME', 'GRADE', 'CREDIT_EARNED', 'REMARK_INDEX', 'SUB_SEC_INDEX', 'CUR_HIST_INDEX', 'ENCODED_BY']) // 👈 your main table fields
        ->with([
            'subSection.subject' => function ($q) {
                $q->select('SUB_INDEX', 'SUB_CODE', 'SUB_NAME');
            },
            'subSection' => function ($q) {
                $q->select('SUB_SEC_INDEX', 'SUB_INDEX'); // needed for subject relation
            },
            'remark' => function ($q) {
                $q->select('REMARK_INDEX', 'REMARK');
            },
            'encodedByUser' => function ($q) {
                $q->select('USER_INDEX', 'FNAME', 'MNAME', 'LNAME');
            },
            'curriculum' => function ($q) {
                $q->select('CUR_HIST_INDEX', 'SY_FROM', 'SY_TO', 'SEMESTER');
            },
        ])
        ->valid()
        ->get();


            $termGrades = $user->termGrades()
            ->select(['GS_INDEX', 'GRADE_NAME', 'GRADE', 'CREDIT_EARNED', 'REMARK_INDEX', 'SUB_SEC_INDEX', 'CUR_HIST_INDEX', 'ENCODED_BY']) // only needed fields
            ->with([
                'subSection.subject' => function ($q) {
                    $q->select('SUB_INDEX', 'SUB_CODE', 'SUB_NAME');
                },
                'subSection' => function ($q) {
                    $q->select('SUB_SEC_INDEX', 'SUB_INDEX'); // required to link with subject
                },
                'remark' => function ($q) {
                    $q->select('REMARK_INDEX', 'REMARK');
                },
                'encodedByUser' => function ($q) {
                    $q->select('USER_INDEX', 'FNAME', 'MNAME', 'LNAME');
                },
                'curriculum' => function ($q) {
                    $q->select('CUR_HIST_INDEX', 'SY_FROM', 'SY_TO', 'SEMESTER');
                },
            ])
            ->valid()
            ->get();


        $allGrades = $termGrades->merge($finalGrades);

        $availableTerms = $allGrades->pluck('curriculum')
            ->filter()
            ->unique(fn($c) => "{$c->SY_FROM}-{$c->SY_TO}-{$c->SEMESTER}")
            ->sortByDesc('SY_FROM')
            ->values()
            ->all();

        $groupedGrades = $allGrades
            ->filter(fn($g) => $g->curriculum)
            ->groupBy(fn($g) => "{$g->curriculum->SY_FROM}-{$g->curriculum->SY_TO}");

        $sortedGrouped = collect();

        foreach ($groupedGrades->sortKeysDesc() as $syKey => $gradesInYear) {
            $bySemester = $gradesInYear->groupBy(fn($g) => $g->curriculum->SEMESTER ?? 1);

            foreach ([0, 2, 1] as $sem) {
                if (!isset($bySemester[$sem])) continue;

                $sortedGrades = $bySemester[$sem]->sortBy(function ($grade) {
                    return match ($grade->GRADE_NAME) {
                        'Final' => 0,
                        'Semi-Final' => 1,
                        'Midterm' => 2,
                        'Prelim' => 3,
                        default => 4,
                    };
                });

                $semLabel = match ((int)$sem) {
                    0 => 'Summer',
                    2 => 'Second Semester',
                    1 => 'First Semester',
                    default => 'Unknown',
                };

                $sortedGrouped->put("{$syKey}-{$semLabel}", $sortedGrades->values()->all());
            }
        }

        return Inertia::render('grades/index', [
            'user' => $user,
            'availableTerms' => $availableTerms,
            'finalGroupedGrades' => $sortedGrouped->toArray(),
            'defaultSy' => $defaultSy,
            'defaultSem' => $defaultSem,
        ]);
    }
}
