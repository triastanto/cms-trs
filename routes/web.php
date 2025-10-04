<?php

use Illuminate\Support\Facades\Route;

// Redirect root to Filament admin
Route::redirect('/', '/admin');

// Admin-only authentication routes (handled by Filament + Fortify)
// No public authentication routes needed for CMS admin
