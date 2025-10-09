<?php

use App\Http\Controllers\Api\SettingsController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RssController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

// Frontend Routes (with page caching)
Route::middleware('cache.page:60')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');

    // Blog Routes
    Route::prefix('blog')->name('blog.')->group(function () {
        Route::get('/', [BlogController::class, 'index'])->name('index');
        Route::get('/search', [BlogController::class, 'search'])->name('search');
        Route::get('/category/{category:slug}', [BlogController::class, 'category'])->name('category');
        Route::get('/tag/{tag:slug}', [BlogController::class, 'tag'])->name('tag');
        Route::get('/{post:slug}', [BlogController::class, 'show'])->name('show');
    });
});

// SEO Routes (with longer cache)
Route::middleware('cache.page:1440')->group(function () {
    Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
    Route::get('/rss', [RssController::class, 'index'])->name('rss');
});

// API Routes for public settings
Route::prefix('api/v1')->group(function () {
    Route::get('/settings', [SettingsController::class, 'index']);
    Route::get('/settings/{key}', [SettingsController::class, 'show']);
    Route::get('/settings/group/{group}', [SettingsController::class, 'group']);
});

// Admin-only authentication routes (handled by Filament + Fortify)
// No public authentication routes needed for CMS admin
