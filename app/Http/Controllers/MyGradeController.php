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

        // Final grades query using DB::select
        $finalGrades = DB::select("
            SELECT 
                s.SUB_CODE, s.SUB_NAME, g.GRADE_NAME, g.GRADE, g.CREDIT_EARNED,
                r.REMARK, c.SY_FROM, c.SY_TO, c.SEMESTER,
                CONCAT(u.LNAME, ', ', u.FNAME, ', ', u.MNAME) AS ENCODED_BY
            FROM G_SHEET_FINAL g
            LEFT JOIN E_SUB_SECTION ss ON ss.SUB_SEC_INDEX = g.SUB_SEC_INDEX
            LEFT JOIN SUBJECT s ON s.SUB_INDEX = ss.SUB_INDEX
            LEFT JOIN REMARK_STATUS r ON r.REMARK_INDEX = g.REMARK_INDEX
            LEFT JOIN USER_TABLE u ON u.USER_INDEX = g.ENCODED_BY
            LEFT JOIN STUD_CURRICULUM_HIST c ON c.CUR_HIST_INDEX = g.CUR_HIST_INDEX
            WHERE g.IS_VALID = 1 AND g.IS_DEL = 0 AND g.user_index_ =  $userIndex
        ");

        // Term grades query using DB::select
        $termGrades = DB::select("
            SELECT 
                s.SUB_CODE, s.SUB_NAME, g.GRADE_NAME, g.GRADE, g.CREDIT_EARNED,
                r.REMARK, c.SY_FROM, c.SY_TO, c.SEMESTER,
                CONCAT(u.LNAME, ', ', u.FNAME, ', ', u.MNAME) AS ENCODED_BY
            FROM GRADE_SHEET g
            LEFT JOIN E_SUB_SECTION ss ON ss.SUB_SEC_INDEX = g.SUB_SEC_INDEX
            LEFT JOIN SUBJECT s ON s.SUB_INDEX = ss.SUB_INDEX
            LEFT JOIN REMARK_STATUS r ON r.REMARK_INDEX = g.REMARK_INDEX
            LEFT JOIN USER_TABLE u ON u.USER_INDEX = g.ENCODED_BY
            LEFT JOIN STUD_CURRICULUM_HIST c ON c.CUR_HIST_INDEX = g.CUR_HIST_INDEX
            WHERE g.IS_VALID = 1 AND g.IS_DEL = 0 AND g.user_index_ =  $userIndex
        ");

        // Merge and group grades
        $allGrades = collect($finalGrades)->merge($termGrades);

        $grouped = $allGrades->sortByDesc(function ($g) {
            return $g->SY_FROM . '-' . $g->SY_TO . '-' . $g->SEMESTER;
        })->groupBy(function ($g) {
            $semesterName = match ((int)$g->SEMESTER) {
                1 => '1st Semester',
                2 => '2nd Semester',
                0 => 'Summer',
                default => 'N/A',
            };
            return $g->SY_FROM . '-' . $g->SY_TO . ' ' . $semesterName;
        });

        return Inertia::render('grades/index', [
            'finalGroupedGrades' => $grouped,
        ]);
    }
}
