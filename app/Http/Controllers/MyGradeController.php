<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class MyGradeController extends Controller
{
    public function index()
    {
        $userIndex = Auth::user()->USER_INDEX;

        // Fetch final grades from G_SHEET_FINAL
        $finalGrades = DB::table('G_SHEET_FINAL as g')
            ->select(
                's.SUB_CODE', 's.SUB_NAME', 'g.GRADE_NAME', 'g.GRADE', 'g.CREDIT_EARNED',
                'r.REMARK', 'c.SY_FROM', 'c.SY_TO', 'c.SEMESTER',
                DB::raw("CONCAT(u.LNAME, ', ', u.FNAME, ', ', u.MNAME) AS ENCODED_BY")
            )
            ->leftJoin('E_SUB_SECTION as ss', 'ss.SUB_SEC_INDEX', '=', 'g.SUB_SEC_INDEX')
            ->leftJoin('SUBJECT as s', 's.SUB_INDEX', '=', 'ss.SUB_INDEX')
            ->leftJoin('REMARK_STATUS as r', 'r.REMARK_INDEX', '=', 'g.REMARK_INDEX')
            ->leftJoin('USER_TABLE as u', 'u.USER_INDEX', '=', 'g.ENCODED_BY')
            ->leftJoin('STUD_CURRICULUM_HIST as c', 'c.CUR_HIST_INDEX', '=', 'g.CUR_HIST_INDEX')
            ->where('g.IS_VALID', 1)
            ->where('g.IS_DEL', 0)
            ->where('g.user_index_', $userIndex)
            ->get();

        // Fetch term grades from GRADE_SHEET
        $termGrades = DB::table('GRADE_SHEET as g')
            ->select(
                's.SUB_CODE', 's.SUB_NAME', 'g.GRADE_NAME', 'g.GRADE', 'g.CREDIT_EARNED',
                'r.REMARK', 'c.SY_FROM', 'c.SY_TO', 'c.SEMESTER',
                DB::raw("CONCAT(u.LNAME, ', ', u.FNAME, ', ', u.MNAME) AS ENCODED_BY")
            )
            ->leftJoin('E_SUB_SECTION as ss', 'ss.SUB_SEC_INDEX', '=', 'g.SUB_SEC_INDEX')
            ->leftJoin('SUBJECT as s', 's.SUB_INDEX', '=', 'ss.SUB_INDEX')
            ->leftJoin('REMARK_STATUS as r', 'r.REMARK_INDEX', '=', 'g.REMARK_INDEX')
            ->leftJoin('USER_TABLE as u', 'u.USER_INDEX', '=', 'g.ENCODED_BY')
            ->leftJoin('STUD_CURRICULUM_HIST as c', 'c.CUR_HIST_INDEX', '=', 'g.CUR_HIST_INDEX')
            ->where('g.IS_VALID', 1)
            ->where('g.IS_DEL', 0)
            ->where('g.user_index_', $userIndex)
            ->get();

        $allGrades = collect($finalGrades)->merge($termGrades);

        $grouped = $allGrades->sortByDesc(function ($g) {
            return $g->SY_FROM . '-' . $g->SY_TO . '-' . $g->SEMESTER;
        })->groupBy(function ($g) {
            return $g->SY_FROM . '-' . $g->SY_TO . ' - Sem ' . $g->SEMESTER;
        });

        return Inertia::render('grades/index', [
            'finalGroupedGrades' => $grouped,
        ]);
    }
}