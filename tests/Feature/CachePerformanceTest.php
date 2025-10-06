<?php

use App\Helpers\MenuHelper;
use App\Models\Category;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Post;
use App\Models\Setting;
use App\Models\User;
use App\Services\SettingsService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

beforeEach(function () {
    // Clear all caches before each test
    Cache::flush();
});

describe('Settings Cache Performance', function () {
    it('caches settings with environment-aware TTL', function () {
        Config::set('performance.cache.settings_ttl', 600); // 10 minutes

        Setting::create([
            'key' => 'test_setting',
            'value' => 'test_value',
            'type' => 'string',
        ]);

        $service = app(SettingsService::class);

        // First call - should hit database
        $value1 = $service->get('test_setting');

        // Second call - should hit cache
        $value2 = $service->get('test_setting');

        expect($value1)->toBe('test_value')
            ->and($value2)->toBe('test_value')
            ->and(Cache::has('settings'))->toBeTrue();
    });

    it('invalidates cache when settings are updated', function () {
        $service = app(SettingsService::class);

        Setting::create([
            'key' => 'test_key',
            'value' => 'initial_value',
            'type' => 'string',
        ]);

        // Cache the initial value
        $initial = $service->get('test_key');
        expect($initial)->toBe('initial_value');

        // Update setting
        $service->set('test_key', 'updated_value');

        // Cache should be cleared
        expect(Cache::has('settings'))->toBeFalse();

        // New value should be fetched
        $updated = $service->get('test_key');
        expect($updated)->toBe('updated_value');
    });

    it('clears critical settings cache on update', function () {
        $service = app(SettingsService::class);

        Cache::put('critical_settings', ['site_name' => 'Old Name'], 3600);

        expect(Cache::has('critical_settings'))->toBeTrue();

        $service->set('site_name', 'New Name');

        expect(Cache::has('critical_settings'))->toBeFalse();
    });
});

describe('Menu Cache Performance', function () {
    it('caches menus with environment-aware TTL', function () {
        Config::set('performance.cache.menu_ttl', 600);

        $menu = Menu::create([
            'name' => 'Test Menu',
            'location' => 'header-primary',
            'is_active' => true,
        ]);

        MenuItem::create([
            'menu_id' => $menu->id,
            'title' => 'Home',
            'url' => '/',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        // First call - should hit database
        $menu1 = MenuHelper::getMenuByLocation('header-primary');

        // Second call - should hit cache
        $menu2 = MenuHelper::getMenuByLocation('header-primary');

        expect($menu1)->not->toBeNull()
            ->and($menu2)->not->toBeNull()
            ->and(Cache::has('menu.location.header-primary'))->toBeTrue();
    });

    it('invalidates menu cache when menu is updated', function () {
        $menu = Menu::create([
            'name' => 'Test Menu',
            'location' => 'footer-primary',
            'is_active' => true,
        ]);

        // Cache the menu
        MenuHelper::getMenuByLocation('footer-primary');
        expect(Cache::has('menu.location.footer-primary'))->toBeTrue();

        // Update menu
        $menu->update(['name' => 'Updated Menu']);

        // Cache should be cleared
        expect(Cache::has('menu.location.footer-primary'))->toBeFalse();
    });

    it('invalidates menu cache when menu item is created', function () {
        $menu = Menu::create([
            'name' => 'Test Menu',
            'location' => 'sidebar',
            'is_active' => true,
        ]);

        // Cache the menu
        MenuHelper::getMenuByLocation('sidebar');
        expect(Cache::has('menu.location.sidebar'))->toBeTrue();

        // Create menu item
        MenuItem::create([
            'menu_id' => $menu->id,
            'title' => 'New Item',
            'url' => '/new',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        // Cache should be cleared
        expect(Cache::has('menu.location.sidebar'))->toBeFalse();
    });

    it('invalidates menu cache when menu item is updated', function () {
        $menu = Menu::create([
            'name' => 'Test Menu',
            'location' => 'mobile',
            'is_active' => true,
        ]);

        $menuItem = MenuItem::create([
            'menu_id' => $menu->id,
            'title' => 'Item',
            'url' => '/item',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        // Cache the menu
        MenuHelper::getMenuByLocation('mobile');
        expect(Cache::has('menu.location.mobile'))->toBeTrue();

        // Update menu item
        $menuItem->update(['title' => 'Updated Item']);

        // Cache should be cleared
        expect(Cache::has('menu.location.mobile'))->toBeFalse();
    });

    it('invalidates menu cache when menu item is deleted', function () {
        $menu = Menu::create([
            'name' => 'Test Menu',
            'location' => 'header-secondary',
            'is_active' => true,
        ]);

        $menuItem = MenuItem::create([
            'menu_id' => $menu->id,
            'title' => 'Item',
            'url' => '/item',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        // Cache the menu
        MenuHelper::getMenuByLocation('header-secondary');
        expect(Cache::has('menu.location.header-secondary'))->toBeTrue();

        // Delete menu item
        $menuItem->delete();

        // Cache should be cleared
        expect(Cache::has('menu.location.header-secondary'))->toBeFalse();
    });
});

describe('Maintenance Mode Cache', function () {
    it('caches maintenance mode status', function () {
        Config::set('performance.cache.maintenance_mode_ttl', 300);

        Setting::create([
            'key' => 'maintenance_mode',
            'value' => '0',
            'type' => 'boolean',
        ]);

        // Simulate middleware check
        $ttl = config('performance.cache.maintenance_mode_ttl', 300);
        $status = Cache::remember('maintenance_mode_status', $ttl, function () {
            return setting('maintenance_mode', false);
        });

        expect($status)->toBeFalse()
            ->and(Cache::has('maintenance_mode_status'))->toBeTrue();
    });

    it('clears maintenance mode cache on settings update', function () {
        $service = app(SettingsService::class);

        Cache::put('maintenance_mode_status', false, 300);
        Cache::put('maintenance_page_settings', ['test' => 'data'], 300);

        expect(Cache::has('maintenance_mode_status'))->toBeTrue();

        $service->set('maintenance_mode', 'true', 'boolean');

        expect(Cache::has('maintenance_mode_status'))->toBeFalse()
            ->and(Cache::has('maintenance_page_settings'))->toBeFalse();
    });
});

describe('Eager Loading Performance', function () {
    it('eager loads relationships for posts', function () {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $post = Post::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);

        // Enable query logging
        \DB::enableQueryLog();

        // Query with eager loading
        $loadedPost = Post::with(['user', 'category', 'tags', 'media'])->first();

        $queries = \DB::getQueryLog();
        \DB::disableQueryLog();

        // Should have minimal queries (not N+1)
        expect($loadedPost->user)->not->toBeNull()
            ->and($loadedPost->category)->not->toBeNull()
            ->and(count($queries))->toBeLessThan(10); // Reasonable query count
    });
});
