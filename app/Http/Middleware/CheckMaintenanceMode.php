<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenanceMode
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Use environment-aware cache TTL (5min in production, 1min in dev)
        $ttl = config('performance.cache.maintenance_mode_ttl', 300);

        // Cache maintenance mode status to reduce database queries
        $isMaintenanceMode = Cache::remember('maintenance_mode_status', $ttl, function () {
            return setting('maintenance_mode', false);
        });

        if ($isMaintenanceMode) {
            // Allow admin users to access the site during maintenance
            if ($request->is('admin*') && auth()->check()) {
                return $next($request);
            }

            // Cache site settings for maintenance page
            $siteSettings = Cache::remember('maintenance_page_settings', $ttl, function () {
                return [
                    'siteName' => setting('site_name', 'CMS-TRS'),
                    'siteDescription' => setting('site_description', 'Content Management System'),
                ];
            });

            // Show maintenance page for all other requests
            return response()->view('maintenance', $siteSettings, 503);
        }

        return $next($request);
    }
}
