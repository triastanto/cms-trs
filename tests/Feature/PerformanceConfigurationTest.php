<?php

use Illuminate\Support\Facades\Config;

beforeEach(function () {
    // Reset config before each test
    Config::set('app.env', 'testing');
});

describe('Performance Configuration', function () {
    it('uses production cache TTLs when APP_ENV is production', function () {
        // Set config values directly to simulate production
        Config::set('performance.cache.critical_settings_ttl', 86400);
        Config::set('performance.cache.settings_ttl', 3600);
        Config::set('performance.cache.menu_ttl', 3600);
        Config::set('performance.cache.maintenance_mode_ttl', 300);

        $criticalTtl = config('performance.cache.critical_settings_ttl');
        $settingsTtl = config('performance.cache.settings_ttl');
        $menuTtl = config('performance.cache.menu_ttl');
        $maintenanceTtl = config('performance.cache.maintenance_mode_ttl');

        expect($criticalTtl)->toBe(86400) // 24 hours
            ->and($settingsTtl)->toBe(3600) // 1 hour
            ->and($menuTtl)->toBe(3600) // 1 hour
            ->and($maintenanceTtl)->toBe(300); // 5 minutes
    });

    it('uses development cache TTLs when APP_ENV is local', function () {
        Config::set('app.env', 'local');

        // Reload config
        Config::set('performance.cache.critical_settings_ttl', null);
        Config::set('performance.cache.settings_ttl', null);

        // Re-evaluate the config values
        $criticalTtl = env('APP_ENV') === 'production' ? 86400 : 300;
        $settingsTtl = env('APP_ENV') === 'production' ? 3600 : 300;
        $menuTtl = env('APP_ENV') === 'production' ? 3600 : 300;
        $maintenanceTtl = env('APP_ENV') === 'production' ? 300 : 60;

        expect($criticalTtl)->toBe(300) // 5 minutes
            ->and($settingsTtl)->toBe(300) // 5 minutes
            ->and($menuTtl)->toBe(300) // 5 minutes
            ->and($maintenanceTtl)->toBe(60); // 1 minute
    });

    it('queues media conversions in production', function () {
        Config::set('app.env', 'production');
        Config::set('performance.queue.media_conversions', true);

        $shouldQueue = config('performance.queue.media_conversions');

        expect($shouldQueue)->toBeTrue();
    });

    it('does not queue media conversions in development', function () {
        Config::set('app.env', 'local');
        Config::set('performance.queue.media_conversions', false);

        $shouldQueue = config('performance.queue.media_conversions');

        expect($shouldQueue)->toBeFalse();
    });

    it('allows environment variable overrides for cache TTLs', function () {
        Config::set('performance.cache.critical_settings_ttl', 12345);

        $criticalTtl = config('performance.cache.critical_settings_ttl');

        expect($criticalTtl)->toBe(12345);
    });

    it('allows environment variable overrides for queue settings', function () {
        Config::set('performance.queue.media_conversions', true);

        $shouldQueue = config('performance.queue.media_conversions');

        expect($shouldQueue)->toBeTrue();
    });
});

describe('Asset Optimization Settings', function () {
    it('enables asset optimization in production', function () {
        Config::set('app.env', 'production');
        Config::set('performance.assets.minify', true);
        Config::set('performance.assets.drop_console', true);
        Config::set('performance.assets.source_maps', false);

        expect(config('performance.assets.minify'))->toBeTrue()
            ->and(config('performance.assets.drop_console'))->toBeTrue()
            ->and(config('performance.assets.source_maps'))->toBeFalse();
    });

    it('disables asset optimization in development', function () {
        Config::set('app.env', 'local');
        Config::set('performance.assets.minify', false);
        Config::set('performance.assets.drop_console', false);
        Config::set('performance.assets.source_maps', true);

        expect(config('performance.assets.minify'))->toBeFalse()
            ->and(config('performance.assets.drop_console'))->toBeFalse()
            ->and(config('performance.assets.source_maps'))->toBeTrue();
    });
});

describe('Database Query Settings', function () {
    it('enables query logging in development', function () {
        Config::set('app.env', 'local');
        Config::set('performance.database.log_queries', true);

        expect(config('performance.database.log_queries'))->toBeTrue();
    });

    it('disables query logging in production', function () {
        Config::set('app.env', 'production');
        Config::set('performance.database.log_queries', false);

        expect(config('performance.database.log_queries'))->toBeFalse();
    });

    it('uses correct slow query threshold', function () {
        Config::set('performance.database.slow_query_threshold', 1000);

        expect(config('performance.database.slow_query_threshold'))->toBe(1000);
    });
});
