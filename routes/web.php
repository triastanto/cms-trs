<?php

use App\Http\Controllers\Api\SettingsController;
use Illuminate\Support\Facades\Route;

// Redirect root to Filament admin
Route::redirect('/', '/admin');

// API Routes for public settings
Route::prefix('api/v1')->group(function () {
    Route::get('/settings', [SettingsController::class, 'index']);
    Route::get('/settings/{key}', [SettingsController::class, 'show']);
    Route::get('/settings/group/{group}', [SettingsController::class, 'group']);
});

// Admin-only authentication routes (handled by Filament + Fortify)
// No public authentication routes needed for CMS admin
