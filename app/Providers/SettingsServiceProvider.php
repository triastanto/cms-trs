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
        // Bind settings to application configuration
        $this->app->booted(function () {
            // Check if settings table exists to avoid errors during testing
            if (! \Schema::hasTable('settings')) {
                return;
            }

            $settings = app(SettingsService::class);

            // Override app configuration with settings
            if ($settings->has('site_name')) {
                config(['app.name' => $settings->get('site_name')]);
            }

            if ($settings->has('timezone')) {
                config(['app.timezone' => $settings->get('timezone')]);
            }

            if ($settings->has('locale')) {
                config(['app.locale' => $settings->get('locale')]);
            }

            // Email configuration
            if ($settings->has('email_from_name')) {
                config(['mail.from.name' => $settings->get('email_from_name')]);
            }

            if ($settings->has('email_from_address')) {
                config(['mail.from.address' => $settings->get('email_from_address')]);
            }

            if ($settings->has('email_reply_to')) {
                config(['mail.reply_to.address' => $settings->get('email_reply_to')]);
            }

            // Session configuration
            if ($settings->has('session_lifetime')) {
                config(['session.lifetime' => $settings->get('session_lifetime')]);
            }

            // Media library configuration
            if ($settings->has('max_file_size')) {
                config(['media-library.max_file_size' => $settings->get('max_file_size') * 1024]); // Convert KB to bytes
            }

            if ($settings->has('image_quality')) {
                // This would need to be implemented in media conversions
                // For now, we'll store it in a custom config
                config(['media-library.image_quality' => $settings->get('image_quality')]);
            }
        });
    }
}
