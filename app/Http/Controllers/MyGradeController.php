<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MyGradeController extends Controller
{
    public function index()
{
    $user = Auth::user();

    $finalGrades = $user->finalGrades()
        ->with(['subSection.subject', 'remark', 'encodedByUser', 'curriculum'])
        ->valid()
        ->get();

    $termGrades = $user->termGrades()
        ->with(['subSection.subject', 'remark', 'encodedByUser', 'curriculum'])
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
    ]);
}

}
