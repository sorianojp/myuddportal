<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\GradeApiController;
use App\Http\Controllers\Api\PaymentApiController;
use App\Http\Controllers\Api\ScheduleApiController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

use Illuminate\Validation\ValidationException;

Route::post('/login', function (LoginRequest $request) {
    try {
        $request->authenticate();

        return response()->json([
            'success' => true,
            'user' => Auth::user(),
        ]);
    } catch (ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid credentials. Please check your ID and password.',
        ], 422);
    }
});


Route::get('/payments', [PaymentApiController::class, 'index']);
Route::get('/grades', [GradeApiController::class, 'index']);
Route::get('/schedule', [ScheduleApiController::class, 'index']);