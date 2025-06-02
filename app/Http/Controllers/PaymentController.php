<?php

namespace App\Http\Controllers;
use App\Models\Payment;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{

    public function index()
    {
        $user = Auth::user();

        // Load all necessary relationships
        $payments = Payment::with([
            'otherSchoolFee',
            'createdBy',
            'cashAdvanceFrom',
            'refund',
            'journalVoucher',
        ])
        ->where('USER_INDEX', $user->USER_INDEX)
        ->where('IS_VALID', 1)
        ->where('IS_DEL', 0)
        ->where('IS_STUD_TEMP', 0)
        ->where('PAYMENT_FOR', '!=', 7)
        ->orderBy('DATE_PAID')
        ->orderBy('CREATE_TIME')
        ->get();

        // Temporary & Permanent banks (you might want to cache this in production)
        $tempBanks = DB::table('FA_UPLOAD_BANK_LIST')->pluck('BANK_CODE', 'BANK_INDEX');
        $permBanks = DB::table('FA_BANK_LIST')->pluck('BANK_CODE', 'BANK_INDEX');

        // Payment type mapping
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

        // Payment For mapping
        $paymentForMap = [
            0 => 'Tuition',
            1 => 'Oth School Fee',
            2 => 'Fine',
            3 => 'School Facility',
            4 => 'Dormitory',
            10 => 'Back Account',
        ];

        // Format payments
        $formattedPayments = $payments->map(function ($payment) use ($paymentTypeMap, $paymentForMap, $tempBanks, $permBanks) {
            $type = $paymentTypeMap[$payment->PAYMENT_TYPE] ?? '';
            $checkInfo = $payment->CHECK_NO ? " #{$payment->CHECK_NO}" : '';

            // Determine Bank Payment
            if ($payment->BANK_POST_INDEX && $payment->IS_BANK_POST) {
                $bankName = $payment->IS_MANUALLY_POSTED
                    ? ($tempBanks[$payment->BANK_POST_INDEX] ?? '')
                    : ($permBanks[$payment->BANK_POST_INDEX] ?? '');

                $type = $payment->IS_MANUALLY_POSTED
                    ? ' - BankPayment(Temp)'
                    : ' - BankPayment(Perm)';
                $type .= $bankName ? " - $bankName" : '';
            }

            // Name and ID if cash advance
            $cashAdvName = null;
            if ($payment->cashAdvanceFrom) {
                $cashAdvName = "{$payment->cashAdvanceFrom->LNAME}, {$payment->cashAdvanceFrom->FNAME} {$payment->cashAdvanceFrom->MNAME} ID:{$payment->cashAdvanceFrom->ID_NUMBER}";
            }

            // Determine description
            if ($payment->PMT_SCH_INDEX == 0) {
                $description = "Enrollment/Downpayment$type";
            } elseif ($payment->PAYMENT_TYPE == 3 && $payment->refund) {
                $description = $payment->refund->REFUND_NOTE;
            } elseif ($payment->PAYMENT_TYPE == 3) {
                $description = " Refunded To " . ($payment->cashAdvanceFrom->ID_NUMBER ?? '');
            } elseif ($payment->PAYMENT_TYPE == 4 && $payment->refund) {
                $description = $payment->refund->REFUND_NOTE;
            } elseif ($payment->PAYMENT_TYPE == 4) {
                $description = " Refund Transferred From " . ($payment->cashAdvanceFrom->ID_NUMBER ?? '');
            } else {
                $feeName = $payment->otherSchoolFee->FEE_NAME ?? ($paymentForMap[$payment->PAYMENT_FOR] ?? 'Unknown');
                $description = $feeName . ($cashAdvName ? " <font size=1>$cashAdvName</font>" : '') . $type . $checkInfo;
            }

            return [
                'PAYMENT_INDEX' => $payment->PAYMENT_INDEX,
                'OR_NUMBER' => $payment->OR_NUMBER ?? $payment->otherSchoolFee->FEE_NAME ?? '',
                'DATE_PAID' => $payment->DATE_PAID,
                'AMOUNT' => $payment->AMOUNT,
                'DESCRIPTION' => $description,
                'PAYMENT_FOR' => $payment->PAYMENT_FOR,
                'POSTED_BY' => $payment->createdBy->ID_NUMBER ?? null,
            ];
        });

        return Inertia::render('payments/index', [
            'payments' => $formattedPayments,
        ]);
    }


        // public function index()
    // {
    //     $user = Auth::user();

    //     $payments = Payment::with('otherSchoolFee')
    //     ->where('USER_INDEX', $user->USER_INDEX)
    //     ->orderByDesc('DATE_PAID')
    //     ->get();

    //     return Inertia::render('payments/index', [
    //         'payments' => $payments->map(function ($payment) {
    //             return [
    //                 'PAYMENT_INDEX' => $payment->PAYMENT_INDEX,
    //                 'OR_NUMBER' => $payment->OR_NUMBER,
    //                 'DATE_PAID' => $payment->DATE_PAID,
    //                 'AMOUNT' => $payment->AMOUNT,
    //                 'AMOUNT_TENDERED' => $payment->AMOUNT_TENDERED,
    //                 'AMOUNT_CHANGE' => $payment->AMOUNT_CHANGE,
    //                 'otherSchoolFee' => $payment->otherSchoolFee ? [
    //                     'FEE_NAME' => $payment->otherSchoolFee->FEE_NAME,
    //                 ] : null,
    //             ];
    //         }),
    //     ]);

    // }

}
