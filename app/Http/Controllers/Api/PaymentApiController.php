<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class PaymentApiController extends Controller
{
    public function index(Request $request)
    {
        $userIndex = $request->input('USER_INDEX');

        $payments = DB::table('FA_STUD_PAYMENT as p')
            ->leftJoin('FA_OTH_SCH_FEE as f', 'p.OTHSCH_FEE_INDEX', '=', 'f.OTHSCH_FEE_INDEX')
            ->leftJoin('FA_STUD_REFUND as r', 'r.REFUND_TO_PMT_INDEX', '=', 'p.PAYMENT_INDEX')
            ->leftJoin('USER_TABLE as u', 'u.USER_INDEX', '=', 'p.CREATED_BY')
            ->leftJoin('USER_TABLE as cash_adv', 'cash_adv.USER_INDEX', '=', 'p.CASH_ADV_FROM_EMP_ID')
            ->where('p.USER_INDEX', $userIndex)
            ->where('p.IS_VALID', 1)
            ->where('p.IS_DEL', 0)
            ->where('p.IS_STUD_TEMP', 0)
            ->where('p.PAYMENT_FOR', '!=', 7)
            ->orderByDesc('p.DATE_PAID')
            ->orderBy('p.CREATE_TIME')
            ->select([
                'p.PAYMENT_INDEX',
                'p.PAYMENT_TYPE',
                'p.PAYMENT_FOR',
                'p.DATE_PAID',
                'p.AMOUNT',
                'p.OR_NUMBER',
                'p.CHECK_NO',
                'p.IS_MANUALLY_POSTED',
                'p.BANK_POST_INDEX',
                'p.IS_BANK_POST',
                'p.PMT_SCH_INDEX',
                'f.FEE_NAME',
                'r.REFUND_NOTE',
                'u.ID_NUMBER as POSTED_BY',
                'cash_adv.ID_NUMBER as CASH_ADV_ID',
                'cash_adv.LNAME as CASH_LNAME',
                'cash_adv.FNAME as CASH_FNAME',
                'cash_adv.MNAME as CASH_MNAME',
            ])->get();

        $tempBanks = DB::table('FA_UPLOAD_BANK_LIST')->pluck('BANK_CODE', 'BANK_INDEX');
        $permBanks = DB::table('FA_BANK_LIST')->pluck('BANK_CODE', 'BANK_INDEX');

        $paymentTypeMap = [
            0 => ' - Cash',
            1 => ' - Check',
            2 => ' - SD',
            3 => ' Refunded',
            4 => ' Refund Transferred',
            5 => '',
            6 => ' - Credit Card',
            7 => ' - E-Pay',
        ];
        $paymentForMap = [
            0 => 'Tuition',
            1 => 'Oth School Fee',
            2 => 'Fine',
            3 => 'School Facility',
            4 => 'Dormitory',
            10 => 'Back Account',
        ];

        $formatted = $payments->map(function ($p) use ($paymentTypeMap, $paymentForMap, $tempBanks, $permBanks) {
            $type = $paymentTypeMap[$p->PAYMENT_TYPE] ?? '';
            $checkInfo = $p->CHECK_NO ? " #{$p->CHECK_NO}" : '';

            if ($p->BANK_POST_INDEX && $p->IS_BANK_POST) {
                $bankName = $p->IS_MANUALLY_POSTED
                    ? ($tempBanks[$p->BANK_POST_INDEX] ?? '')
                    : ($permBanks[$p->BANK_POST_INDEX] ?? '');

                $type = $p->IS_MANUALLY_POSTED
                    ? ' - BankPayment(Temp)'
                    : ' - BankPayment(Perm)';
                $type .= $bankName ? " - $bankName" : '';
            }

            $cashAdvName = null;
            if ($p->CASH_ADV_ID) {
                $cashAdvName = "{$p->CASH_LNAME}, {$p->CASH_FNAME} {$p->CASH_MNAME} ID:{$p->CASH_ADV_ID}";
            }

            if ($p->PMT_SCH_INDEX == 0) {
                $description = "Enrollment/Downpayment$type";
            } elseif ($p->PAYMENT_TYPE == 3 && $p->REFUND_NOTE) {
                $description = $p->REFUND_NOTE;
            } elseif ($p->PAYMENT_TYPE == 3) {
                $description = "Refunded To " . ($p->CASH_ADV_ID ?? '');
            } elseif ($p->PAYMENT_TYPE == 4 && $p->REFUND_NOTE) {
                $description = $p->REFUND_NOTE;
            } elseif ($p->PAYMENT_TYPE == 4) {
                $description = "Refund Transferred From " . ($p->CASH_ADV_ID ?? '');
            } else {
                $feeName = $p->FEE_NAME ?? ($paymentForMap[$p->PAYMENT_FOR] ?? 'Unknown');
                $description = $feeName . ($cashAdvName ? " - $cashAdvName" : '') . $type . $checkInfo;
            }

            return [
                'PAYMENT_INDEX' => $p->PAYMENT_INDEX,
                'OR_NUMBER' => $p->OR_NUMBER ?? $p->FEE_NAME ?? '',
                'DATE_PAID' => $p->DATE_PAID,
                'AMOUNT' => $p->AMOUNT,
                'DESCRIPTION' => $description,
                'POSTED_BY' => $p->POSTED_BY,
            ];
        });

        return response()->json(['payments' => $formatted->values()]);
    }
}
