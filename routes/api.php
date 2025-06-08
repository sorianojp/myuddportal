<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\GradeApiController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', function (LoginRequest $request) {
    $request->authenticate();

    return response()->json([
        'success' => true,
        'user' => Auth::user(),
    ]);
});


Route::get('/grades', [GradeApiController::class, 'index']);
