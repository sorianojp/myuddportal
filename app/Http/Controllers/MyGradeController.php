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

    $finalGrades = DB::select("
        SELECT
            subj.SUB_CODE,
            subj.SUB_NAME,
            g.CREDIT_EARNED,
            CONCAT(u.LNAME, ', ', u.FNAME, ' ', u.MNAME) AS ENCODED_BY,
            g.GRADE,
            ISNULL(r.REMARK, '-') AS REMARK,
            hist.SY_FROM,
            hist.SY_TO,
            hist.SEMESTER,
            'Final' AS GRADE_NAME
        FROM G_SHEET_FINAL g
        LEFT JOIN cculum_masters cm ON cm.CUR_INDEX = g.CUR_INDEX
        LEFT JOIN cculum_medicine med ON med.CUR_INDEX = g.CUR_INDEX
        LEFT JOIN curriculum cur ON cur.CUR_INDEX = g.CUR_INDEX
        LEFT JOIN SUBJECT subj ON subj.SUB_INDEX = ISNULL(cm.SUB_INDEX, ISNULL(med.MAIN_SUB_INDEX, cur.SUB_INDEX))
        LEFT JOIN FACULTY_LOAD fl ON fl.SUB_SEC_INDEX = g.SUB_SEC_INDEX AND fl.IS_VALID = 1 AND fl.IS_DEL = 0
        LEFT JOIN USER_TABLE u ON u.USER_INDEX = fl.USER_INDEX
        LEFT JOIN REMARK_STATUS r ON r.REMARK_INDEX = g.REMARK_INDEX
        JOIN STUD_CURRICULUM_HIST hist ON hist.CUR_HIST_INDEX = g.CUR_HIST_INDEX
        WHERE g.IS_VALID = 1 AND g.IS_DEL = 0 AND g.USER_INDEX_ = ?
    ", [$userIndex]);

    $termGrades = DB::select("
        SELECT
            subj.SUB_CODE,
            subj.SUB_NAME,
            g.CREDIT_EARNED,
            CONCAT(u.LNAME, ', ', u.FNAME, ' ', u.MNAME) AS ENCODED_BY,
            g.GRADE,
            ISNULL(r.REMARK, '-') AS REMARK,
            hist.SY_FROM,
            hist.SY_TO,
            hist.SEMESTER,
            g.GRADE_NAME
        FROM GRADE_SHEET g
        LEFT JOIN cculum_masters cm ON cm.CUR_INDEX = g.CUR_INDEX
        LEFT JOIN cculum_medicine med ON med.CUR_INDEX = g.CUR_INDEX
        LEFT JOIN curriculum cur ON cur.CUR_INDEX = g.CUR_INDEX
        LEFT JOIN SUBJECT subj ON subj.SUB_INDEX = ISNULL(cm.SUB_INDEX, ISNULL(med.MAIN_SUB_INDEX, cur.SUB_INDEX))
        LEFT JOIN FACULTY_LOAD fl ON fl.SUB_SEC_INDEX = g.SUB_SEC_INDEX AND fl.IS_VALID = 1 AND fl.IS_DEL = 0
        LEFT JOIN USER_TABLE u ON u.USER_INDEX = fl.USER_INDEX
        LEFT JOIN REMARK_STATUS r ON r.REMARK_INDEX = g.REMARK_INDEX
        JOIN STUD_CURRICULUM_HIST hist ON hist.CUR_HIST_INDEX = g.CUR_HIST_INDEX
        WHERE g.IS_VALID = 1 AND g.IS_DEL = 0 AND g.USER_INDEX_ = ?
    ", [$userIndex]);

    $allGrades = collect($finalGrades)
        ->merge($termGrades)
        ->sortBy([
            ['SY_FROM', 'asc'],
            ['SEMESTER', 'asc'],
            ['SUB_CODE', 'asc'],
            ['GRADE_NAME', 'asc'],
        ])
        ->groupBy(fn ($grade) =>
            "{$grade->SY_FROM}-{$grade->SY_TO} | " .
            match ((int) $grade->SEMESTER) {
                1 => '1st Semester',
                2 => '2nd Semester',
                3 => '3rd Semester',
                0 => 'Summer',
                default => "Sem {$grade->SEMESTER}",
            }
        );

    return Inertia::render('grades/index', [
        'finalGroupedGrades' => $allGrades,
    ]);

}

}
