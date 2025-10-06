<?php

namespace App\Providers;

use App\Services\SettingsService;
use Illuminate\Support\ServiceProvider;

class SettingsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(SettingsService::class, function ($app) {
            return new SettingsService;
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Only load settings for web requests, not console or API
        if ($this->app->runningInConsole() || $this->app->runningUnitTests()) {
            return;
        }

        // Lazy-load settings only when needed (on actual web requests)
        $this->app->booted(function () {
            // Skip for API routes to improve API performance
            if (request()->is('api/*')) {
                return;
            }

            $this->loadCriticalSettings();
        });
    }

    /**
     * Load only critical settings that affect application behavior.
     */
    private function loadCriticalSettings(): void
    {
        // Use environment-aware cache TTL (24h in production, 5min in dev)
        $ttl = config('performance.cache.critical_settings_ttl', 86400);

        $criticalSettings = \Cache::remember('critical_settings', $ttl, function () {
            // Check if settings table exists
            if (! \Schema::hasTable('settings')) {
                return [];
            }

            $settings = app(SettingsService::class);

            return [
                'site_name' => $settings->get('site_name'),
                'timezone' => $settings->get('timezone'),
                'locale' => $settings->get('locale'),
                'email_from_name' => $settings->get('email_from_name'),
                'email_from_address' => $settings->get('email_from_address'),
            ];
        });

        // Apply only critical settings to config
        if (! empty($criticalSettings['site_name'])) {
            config(['app.name' => $criticalSettings['site_name']]);
        }

        if (! empty($criticalSettings['timezone'])) {
            config(['app.timezone' => $criticalSettings['timezone']]);
        }

        if (! empty($criticalSettings['locale'])) {
            config(['app.locale' => $criticalSettings['locale']]);
        }

        if (! empty($criticalSettings['email_from_name'])) {
            config(['mail.from.name' => $criticalSettings['email_from_name']]);
        }

        if (! empty($criticalSettings['email_from_address'])) {
            config(['mail.from.address' => $criticalSettings['email_from_address']]);
        }

        // Non-critical settings can be loaded on-demand via setting() helper
    }
}
