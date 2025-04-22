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

        $selectedSy = $request->input('sy', $defaultSy);
        $selectedSem = $request->input('sem', $defaultSem);
        $gradeFilter = $request->input('grade', 'all');

        // Fetch grades matching filters
        $finalGrades = $user->finalGrades()
            ->select(['GS_INDEX', 'GRADE_NAME', 'GRADE', 'CREDIT_EARNED', 'REMARK_INDEX', 'SUB_SEC_INDEX', 'CUR_HIST_INDEX', 'ENCODED_BY'])
            ->when($selectedSy !== 'all', function ($q) use ($selectedSy) {
                [$syFrom, $syTo] = explode('-', $selectedSy);
                $q->whereHas('curriculum', fn($q2) => $q2
                    ->where('SY_FROM', $syFrom)
                    ->where('SY_TO', $syTo)
                );
            })
            ->when($selectedSem !== 'all', function ($q) use ($selectedSem) {
                $q->whereHas('curriculum', fn($q2) => $q2
                    ->where('SEMESTER', $selectedSem)
                );
            })
            ->when($gradeFilter !== 'all', fn($q) => $q->where('GRADE_NAME', $gradeFilter))
            ->with(['subSection.subject', 'subSection', 'remark', 'encodedByUser', 'curriculum'])
            ->valid()
            ->get();

        $termGrades = $user->termGrades()
            ->select(['GS_INDEX', 'GRADE_NAME', 'GRADE', 'CREDIT_EARNED', 'REMARK_INDEX', 'SUB_SEC_INDEX', 'CUR_HIST_INDEX', 'ENCODED_BY'])
            ->when($selectedSy !== 'all', function ($q) use ($selectedSy) {
                [$syFrom, $syTo] = explode('-', $selectedSy);
                $q->whereHas('curriculum', fn($q2) => $q2
                    ->where('SY_FROM', $syFrom)
                    ->where('SY_TO', $syTo)
                );
            })
            ->when($selectedSem !== 'all', function ($q) use ($selectedSem) {
                $q->whereHas('curriculum', fn($q2) => $q2
                    ->where('SEMESTER', $selectedSem)
                );
            })
            ->when($gradeFilter !== 'all', fn($q) => $q->where('GRADE_NAME', $gradeFilter))
            ->with(['subSection.subject', 'subSection', 'remark', 'encodedByUser', 'curriculum'])
            ->valid()
            ->get();

        $allGrades = $termGrades->merge($finalGrades);

        // Build full availableTerms for filters
        $availableTerms = $user->finalGrades()
            ->with('curriculum')
            ->valid()
            ->get()
            ->merge(
                $user->termGrades()->with('curriculum')->valid()->get()
            )
            ->pluck('curriculum')
            ->filter()
            ->unique(fn($c) => "{$c->SY_FROM}-{$c->SY_TO}-{$c->SEMESTER}")
            ->sortByDesc('SY_FROM')
            ->values()
            ->all();

        // Group grades by SY and SEM for rendering
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
            'defaultSy' => $selectedSy,
            'defaultSem' => $selectedSem,
        ]);
    }
}
