<?php
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware('auth')->group(function () {
    // Redirect /settings to /settings/profile
    // Route::redirect('settings', 'settings/profile');

    // ✅ Read-only profile page (Inertia)
    Route::get('settings/profile', function () {
        return Inertia::render('settings/profile');
    })->name('profile.view');

    // ✅ Appearance settings
    Route::get('settings/appearance', function () {
        return Inertia::render('settings/appearance');
    })->name('appearance.edit');
});
