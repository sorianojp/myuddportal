<?php

namespace App\Http\Controllers;
use App\Models\Fee;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Http\Request;

class FeeController extends Controller
{
    public function index()
    {
        $user = Auth::user(); // Get logged-in user

        // Fetch all payment rows where USER_INDEX matches
        $fees = Fee::where('USER_INDEX', $user->USER_INDEX)
        ->orderBy('CREATE_DATE')
        ->get();

        return Inertia::render('fees/index', [
            'fees' => $fees
        ]);
    }
}
