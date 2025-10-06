<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cache Time-To-Live (TTL) Settings
    |--------------------------------------------------------------------------
    |
    | Configure cache durations based on environment. Production uses longer
    | TTLs for better performance, while development uses shorter TTLs for
    | faster iteration and debugging.
    |
    */

    'cache' => [
        // Critical settings cache (loaded on boot)
        'critical_settings_ttl' => env('CACHE_CRITICAL_SETTINGS_TTL', env('APP_ENV') === 'production' ? 86400 : 300), // 24h vs 5min

        // General settings cache
        'settings_ttl' => env('CACHE_SETTINGS_TTL', env('APP_ENV') === 'production' ? 3600 : 300), // 1h vs 5min

        // Menu cache
        'menu_ttl' => env('CACHE_MENU_TTL', env('APP_ENV') === 'production' ? 3600 : 300), // 1h vs 5min

        // Maintenance mode cache
        'maintenance_mode_ttl' => env('CACHE_MAINTENANCE_TTL', env('APP_ENV') === 'production' ? 300 : 60), // 5min vs 1min
    ],

    /*
    |--------------------------------------------------------------------------
    | Queue Settings
    |--------------------------------------------------------------------------
    |
    | Control which operations should be queued. In production, heavy
    | operations are queued for better performance. In development,
    | synchronous execution makes debugging easier.
    |
    */

    'queue' => [
        // Queue media conversions (thumbnails, previews)
        'media_conversions' => env('QUEUE_MEDIA_CONVERSIONS', env('APP_ENV') === 'production'),

        // Queue email notifications
        'email_notifications' => env('QUEUE_EMAIL_NOTIFICATIONS', env('APP_ENV') === 'production'),

        // Queue bulk operations
        'bulk_operations' => env('QUEUE_BULK_OPERATIONS', env('APP_ENV') === 'production'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Database Query Optimization
    |--------------------------------------------------------------------------
    |
    | Enable/disable query optimizations. Eager loading is always enabled,
    | but query logging can be controlled per environment.
    |
    */

    'database' => [
        // Enable query logging in development
        'log_queries' => env('LOG_QUERIES', env('APP_ENV') === 'local'),

        // Query logging threshold (ms) - log queries slower than this
        'slow_query_threshold' => env('SLOW_QUERY_THRESHOLD', 1000),
    ],

    /*
    |--------------------------------------------------------------------------
    | Asset Optimization
    |--------------------------------------------------------------------------
    |
    | Control frontend asset optimization. Production enables aggressive
    | optimization while development preserves debugging tools.
    |
    */

    'assets' => [
        // Enable asset minification
        'minify' => env('MINIFY_ASSETS', env('APP_ENV') === 'production'),

        // Remove console.log statements
        'drop_console' => env('DROP_CONSOLE', env('APP_ENV') === 'production'),

        // Enable source maps
        'source_maps' => env('SOURCE_MAPS', env('APP_ENV') !== 'production'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Debug Settings
    |--------------------------------------------------------------------------
    |
    | Performance debugging tools available in non-production environments.
    |
    */

    'debug' => [
        // Show cache hit/miss information
        'show_cache_stats' => env('SHOW_CACHE_STATS', env('APP_ENV') === 'local'),

        // Enable performance profiling
        'enable_profiling' => env('ENABLE_PROFILING', env('APP_ENV') === 'local'),
    ],

];
