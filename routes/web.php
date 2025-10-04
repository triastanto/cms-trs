<?php

use Illuminate\Support\Facades\Route;

// Redirect root to Filament admin
Route::redirect('/', '/admin');
