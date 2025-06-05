<?php

namespace App\Http\Controllers;
use App\Models\CurrentSchoolYear;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $userIndex = Auth::user()->USER_INDEX;
        $currentTerm = CurrentSchoolYear::getCurrent();
        $syFrom = $currentTerm->CUR_SCHYR_FROM;
        $syTo = $currentTerm->CUR_SCHYR_TO;
        $semester = $currentTerm->CUR_SEMESTER;

        $schedule = DB::select("
     WITH EnrolledLecture AS (
    SELECT 
        EFC.USER_INDEX,
        EFC.SY_FROM,
        EFC.SY_TO,
        EFC.CURRENT_SEMESTER,
        EFC.CUR_INDEX,
        EFC.SUB_SEC_INDEX AS LEC_SUB_SEC_INDEX,
        ESS.SECTION,
        ESS.SUB_INDEX
    FROM ENRL_FINAL_CUR_LIST EFC
    JOIN E_SUB_SECTION ESS ON ESS.SUB_SEC_INDEX = EFC.SUB_SEC_INDEX
    WHERE 
        EFC.IS_VALID = 1 AND EFC.IS_DEL = 0
        AND ESS.IS_VALID = 1 AND ESS.IS_DEL = 0
        AND ESS.IS_LEC = 0
)
SELECT 
    S.SUB_CODE,
    S.SUB_NAME,
    ESS.SECTION,
    ERD.ROOM_NUMBER,
    ERA.WEEK_DAY,
    FORMAT(
        DATEADD(MINUTE, ERA.MINUTE_FROM, DATEADD(HOUR, ERA.HOUR_FROM + CASE WHEN ERA.AMPM_FROM = 1 THEN 12 ELSE 0 END, 0)), 
        'hh:mmtt'
    ) AS TIME_FROM,
    FORMAT(
        DATEADD(MINUTE, ERA.MINUTE_TO, DATEADD(HOUR, ERA.HOUR_TO + CASE WHEN ERA.AMPM_TO = 1 THEN 12 ELSE 0 END, 0)), 
        'hh:mmtt'
    ) AS TIME_TO,
    ESS.IS_LEC,
    ESS.SUB_SEC_INDEX
FROM (
    SELECT * FROM EnrolledLecture
    UNION
    SELECT 
        EL.USER_INDEX,
        EL.SY_FROM,
        EL.SY_TO,
        EL.CURRENT_SEMESTER,
        EL.CUR_INDEX,
        ESS.SUB_SEC_INDEX,
        ESS.SECTION,
        ESS.SUB_INDEX
    FROM EnrolledLecture EL
    JOIN E_SUB_SECTION ESS ON
        ESS.SECTION = EL.SECTION
        AND ESS.SUB_INDEX = EL.SUB_INDEX
        AND ESS.OFFERING_SY_FROM = EL.SY_FROM
        AND ESS.OFFERING_SEM = EL.CURRENT_SEMESTER
        AND ESS.IS_LEC = 1
        AND ESS.IS_VALID = 1 AND ESS.IS_DEL = 0
) AS AllSec
JOIN E_SUB_SECTION ESS ON ESS.SUB_SEC_INDEX = AllSec.LEC_SUB_SEC_INDEX
JOIN SUBJECT S ON S.SUB_INDEX = AllSec.SUB_INDEX
JOIN E_ROOM_ASSIGN ERA ON ERA.SUB_SEC_INDEX = ESS.SUB_SEC_INDEX AND ERA.IS_VALID = 1 AND ERA.IS_DEL = 0
JOIN E_ROOM_DETAIL ERD ON ERD.ROOM_INDEX = ERA.ROOM_INDEX
WHERE 
    AllSec.USER_INDEX = $userIndex
                    AND AllSec.SY_FROM = $syFrom
                AND AllSec.SY_TO = $syTo
                AND AllSec.CURRENT_SEMESTER = $semester
ORDER BY 
    S.SUB_CODE, ESS.IS_LEC, ERA.WEEK_DAY, ERA.HOUR_FROM;

        ");

        return Inertia::render('schedule/index', [
            'schedule' => $schedule,
        ]);
    }
}
