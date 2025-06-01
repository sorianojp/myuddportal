<?php

namespace App\Http\Controllers;
use App\Models\Payment;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $payments = Payment::with('otherSchoolFee')
        ->where('USER_INDEX', $user->USER_INDEX)
        ->orderByDesc('DATE_PAID')
        ->get();

        return Inertia::render('payments/index', [
            'payments' => $payments->map(function ($payment) {
                return [
                    'PAYMENT_INDEX' => $payment->PAYMENT_INDEX,
                    'OR_NUMBER' => $payment->OR_NUMBER,
                    'DATE_PAID' => $payment->DATE_PAID,
                    'AMOUNT' => $payment->AMOUNT,
                    'AMOUNT_TENDERED' => $payment->AMOUNT_TENDERED,
                    'AMOUNT_CHANGE' => $payment->AMOUNT_CHANGE,
                    'otherSchoolFee' => $payment->otherSchoolFee ? [
                        'FEE_NAME' => $payment->otherSchoolFee->FEE_NAME,
                    ] : null,
                ];
            }),
        ]);

    }
}
