<?php

use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Menu Model', function () {
    beforeEach(function () {
        $this->menu = Menu::factory()->create();
    });

    describe('Basic Attributes', function () {
        it('has the correct fillable attributes', function () {
            $menu = new Menu;
            $expectedFillable = [
                'name',
                'location',
                'description',
                'is_active',
            ];

            expect($menu->getFillable())->toBe($expectedFillable);
        });

        it('has the correct casts', function () {
            $menu = new Menu;
            $expectedCasts = [
                'is_active' => 'boolean',
            ];

            expect($menu->getCasts())->toMatchArray($expectedCasts);
        });
    });

    describe('Menu Item Relationships', function () {
        it('has many menu items', function () {
            $menu = Menu::factory()->create();
            $menuItems = MenuItem::factory()->count(3)->create(['menu_id' => $menu->id]);

            expect($menu->menuItems)->toHaveCount(3);
            expect($menu->menuItems->first())->toBeInstanceOf(MenuItem::class);
        });

        it('orders menu items by sort_order', function () {
            $menu = Menu::factory()->create();
            $item1 = MenuItem::factory()->create(['menu_id' => $menu->id, 'sort_order' => 3]);
            $item2 = MenuItem::factory()->create(['menu_id' => $menu->id, 'sort_order' => 1]);
            $item3 = MenuItem::factory()->create(['menu_id' => $menu->id, 'sort_order' => 2]);

            $menuItems = $menu->menuItems;

            expect($menuItems->first()->id)->toBe($item2->id);
            expect($menuItems->last()->id)->toBe($item1->id);
        });

        it('can get root menu items only', function () {
            $menu = Menu::factory()->create();
            $rootItem = MenuItem::factory()->create(['menu_id' => $menu->id, 'parent_id' => null]);
            $childItem = MenuItem::factory()->create(['menu_id' => $menu->id, 'parent_id' => $rootItem->id]);

            expect($menu->rootMenuItems)->toHaveCount(1);
            expect($menu->rootMenuItems->first()->id)->toBe($rootItem->id);
        });
    });

    describe('Scopes', function () {
        it('can scope to active menus', function () {
            Menu::factory()->create(['is_active' => true]);
            Menu::factory()->create(['is_active' => false]);

            $activeMenus = Menu::active()->get();

            expect($activeMenus->count())->toBeGreaterThanOrEqual(1);
            expect($activeMenus->where('is_active', false))->toHaveCount(0);
        });
    });

    describe('Static Methods', function () {
        it('can get menu by location', function () {
            $menu = Menu::factory()->create(['location' => 'header-primary', 'is_active' => true]);

            $foundMenu = Menu::getByLocation('header-primary');

            expect($foundMenu)->not->toBeNull();
            expect($foundMenu->id)->toBe($menu->id);
        });

        it('returns null for non-existent location', function () {
            $foundMenu = Menu::getByLocation('non-existent-location');

            expect($foundMenu)->toBeNull();
        });

        it('returns null for inactive menu location', function () {
            Menu::factory()->create(['location' => 'header-primary', 'is_active' => false]);

            $foundMenu = Menu::getByLocation('header-primary');

            expect($foundMenu)->toBeNull();
        });

        it('returns predefined locations', function () {
            $locations = Menu::getLocations();

            expect($locations)->toBeArray();
            expect($locations)->toHaveKey('header-primary');
            expect($locations)->toHaveKey('footer-primary');
            expect($locations)->toHaveKey('sidebar');
            expect($locations)->toHaveKey('mobile');
        });
    });

    describe('Default Values', function () {
        it('is active by default', function () {
            $menu = Menu::factory()->create();

            expect($menu->is_active)->toBeTrue();
        });

        it('can be created as inactive', function () {
            $menu = Menu::factory()->create(['is_active' => false]);

            expect($menu->is_active)->toBeFalse();
        });
    });

    describe('Location Management', function () {
        it('can create menu for different locations', function () {
            $headerMenu = Menu::factory()->create(['location' => 'header-primary']);
            $footerMenu = Menu::factory()->create(['location' => 'footer-primary']);
            $sidebarMenu = Menu::factory()->create(['location' => 'sidebar']);

            expect($headerMenu->location)->toBe('header-primary');
            expect($footerMenu->location)->toBe('footer-primary');
            expect($sidebarMenu->location)->toBe('sidebar');
        });
    });

    describe('Menu Description', function () {
        it('can have a description', function () {
            $menu = Menu::factory()->create(['description' => 'This is a test menu']);

            expect($menu->description)->toBe('This is a test menu');
        });

        it('can have null description', function () {
            $menu = Menu::factory()->create(['description' => null]);

            expect($menu->description)->toBeNull();
        });
    });
});
