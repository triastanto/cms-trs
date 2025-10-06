<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenanceMode
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (setting('maintenance_mode', false)) {
            // Allow admin users to access the site during maintenance
            if ($request->is('admin*') && auth()->check()) {
                return $next($request);
            }

            // Show maintenance page for all other requests
            return response()->view('maintenance', [
                'siteName' => setting('site_name', 'CMS-TRS'),
                'siteDescription' => setting('site_description', 'Content Management System'),
            ], 503);
        }

        return $next($request);
    }
}
