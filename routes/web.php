<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\MyGradeController;
use App\Http\Controllers\SubjectLoadController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\FeeController;

Route::get('/', function () { return Inertia::render('auth/login'); })->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', function () { return Inertia::render('dashboard'); })->name('dashboard');
    Route::get('/mygrades', [MyGradeController::class, 'index'])->name('mygrades');
    Route::get('/subjectload', [SubjectLoadController::class, 'index'])->name('subjectload');
    Route::get('/payments', [PaymentController::class, 'index']);
    Route::get('/fees', [FeeController::class, 'index']);
});




require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
