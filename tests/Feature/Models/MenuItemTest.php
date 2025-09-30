<?php

use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('MenuItem Model', function () {
    beforeEach(function () {
        $this->menu = Menu::factory()->create();
        $this->menuItem = MenuItem::factory()->create(['menu_id' => $this->menu->id]);
    });

    describe('Basic Attributes', function () {
        it('has the correct fillable attributes', function () {
            $menuItem = new MenuItem;
            $expectedFillable = [
                'menu_id',
                'parent_id',
                'title',
                'url',
                'target',
                'icon',
                'css_class',
                'sort_order',
                'is_active',
                'is_external',
            ];

            expect($menuItem->getFillable())->toBe($expectedFillable);
        });

        it('has the correct casts', function () {
            $menuItem = new MenuItem;
            $expectedCasts = [
                'is_active' => 'boolean',
                'is_external' => 'boolean',
                'sort_order' => 'integer',
            ];

            expect($menuItem->getCasts())->toMatchArray($expectedCasts);
        });
    });

    describe('Menu Relationship', function () {
        it('belongs to a menu', function () {
            $menu = Menu::factory()->create();
            $menuItem = MenuItem::factory()->create(['menu_id' => $menu->id]);

            expect($menuItem->menu)->toBeInstanceOf(Menu::class);
            expect($menuItem->menu->id)->toBe($menu->id);
        });
    });

    describe('Hierarchical Relationships', function () {
        it('can have a parent menu item', function () {
            $menu = Menu::factory()->create();
            $parent = MenuItem::factory()->create(['menu_id' => $menu->id]);
            $child = MenuItem::factory()->create(['menu_id' => $menu->id, 'parent_id' => $parent->id]);

            expect($child->parent)->toBeInstanceOf(MenuItem::class);
            expect($child->parent->id)->toBe($parent->id);
        });

        it('can have child menu items', function () {
            $menu = Menu::factory()->create();
            $parent = MenuItem::factory()->create(['menu_id' => $menu->id]);
            $children = MenuItem::factory()->count(3)->create(['menu_id' => $menu->id, 'parent_id' => $parent->id]);

            expect($parent->children)->toHaveCount(3);
            expect($parent->children->first())->toBeInstanceOf(MenuItem::class);
        });

        it('orders children by sort_order', function () {
            $menu = Menu::factory()->create();
            $parent = MenuItem::factory()->create(['menu_id' => $menu->id]);
            $child1 = MenuItem::factory()->create(['menu_id' => $menu->id, 'parent_id' => $parent->id, 'sort_order' => 3]);
            $child2 = MenuItem::factory()->create(['menu_id' => $menu->id, 'parent_id' => $parent->id, 'sort_order' => 1]);
            $child3 = MenuItem::factory()->create(['menu_id' => $menu->id, 'parent_id' => $parent->id, 'sort_order' => 2]);

            $children = $parent->children;

            expect($children->first()->id)->toBe($child2->id);
            expect($children->last()->id)->toBe($child1->id);
        });

        it('can get descendants recursively', function () {
            $menu = Menu::factory()->create();
            $parent = MenuItem::factory()->create(['menu_id' => $menu->id]);
            $child = MenuItem::factory()->create(['menu_id' => $menu->id, 'parent_id' => $parent->id]);
            $grandchild = MenuItem::factory()->create(['menu_id' => $menu->id, 'parent_id' => $child->id]);

            expect($parent->descendants)->toHaveCount(1);
            expect($parent->descendants->first()->id)->toBe($child->id);
        });
    });

    describe('Scopes', function () {
        it('can scope to active menu items', function () {
            $menu = Menu::factory()->create();
            MenuItem::factory()->create(['menu_id' => $menu->id, 'is_active' => true]);
            MenuItem::factory()->create(['menu_id' => $menu->id, 'is_active' => false]);

            $activeItems = MenuItem::active()->get();

            expect($activeItems->count())->toBeGreaterThanOrEqual(1);
            expect($activeItems->where('is_active', false))->toHaveCount(0);
        });

        it('can scope to root menu items', function () {
            $menu = Menu::factory()->create();
            $parent = MenuItem::factory()->create(['menu_id' => $menu->id, 'parent_id' => null]);
            $child = MenuItem::factory()->create(['menu_id' => $menu->id, 'parent_id' => $parent->id]);

            $rootItems = MenuItem::root()->get();

            expect($rootItems->count())->toBeGreaterThanOrEqual(1);
            expect($rootItems->where('parent_id', '!=', null))->toHaveCount(0);
        });
    });

    describe('Helper Methods', function () {
        it('can check if menu item has children', function () {
            $menu = Menu::factory()->create();
            $parent = MenuItem::factory()->create(['menu_id' => $menu->id]);
            $childless = MenuItem::factory()->create(['menu_id' => $menu->id]);
            MenuItem::factory()->create(['menu_id' => $menu->id, 'parent_id' => $parent->id]);

            expect($parent->hasChildren())->toBeTrue();
            expect($childless->hasChildren())->toBeFalse();
        });

        it('can detect external links by is_external flag', function () {
            $menu = Menu::factory()->create();
            $external = MenuItem::factory()->create(['menu_id' => $menu->id, 'is_external' => true]);
            $internal = MenuItem::factory()->create(['menu_id' => $menu->id, 'is_external' => false]);

            expect($external->isExternal())->toBeTrue();
            expect($internal->isExternal())->toBeFalse();
        });

        it('can detect external links by target _blank', function () {
            $menu = Menu::factory()->create();
            $external = MenuItem::factory()->create(['menu_id' => $menu->id, 'target' => '_blank', 'is_external' => false]);

            expect($external->isExternal())->toBeTrue();
        });
    });

    describe('Full URL Attribute', function () {
        it('returns URL as-is for internal links', function () {
            $menu = Menu::factory()->create();
            $menuItem = MenuItem::factory()->create([
                'menu_id' => $menu->id,
                'url' => '/about',
                'is_external' => false,
            ]);

            expect($menuItem->full_url)->toBe('/about');
        });

        it('adds https protocol for external links without protocol', function () {
            $menu = Menu::factory()->create();
            $menuItem = MenuItem::factory()->create([
                'menu_id' => $menu->id,
                'url' => 'example.com',
                'is_external' => true,
            ]);

            expect($menuItem->full_url)->toBe('https://example.com');
        });

        it('preserves protocol for external links with protocol', function () {
            $menu = Menu::factory()->create();
            $menuItem = MenuItem::factory()->create([
                'menu_id' => $menu->id,
                'url' => 'https://example.com',
                'is_external' => true,
            ]);

            expect($menuItem->full_url)->toBe('https://example.com');
        });

        it('returns # for null URLs', function () {
            $menu = Menu::factory()->create();
            $menuItem = MenuItem::factory()->create([
                'menu_id' => $menu->id,
                'url' => null,
                'is_external' => false,
            ]);

            expect($menuItem->full_url)->toBe('#');
        });
    });

    describe('Target Options', function () {
        it('returns array of target options', function () {
            $options = MenuItem::getTargetOptions();

            expect($options)->toBeArray();
            expect($options)->toHaveKey('_self');
            expect($options)->toHaveKey('_blank');
            expect($options)->toHaveKey('_parent');
            expect($options)->toHaveKey('_top');
        });
    });

    describe('Default Values', function () {
        it('is active by default', function () {
            $menu = Menu::factory()->create();
            $menuItem = MenuItem::factory()->create(['menu_id' => $menu->id]);

            expect($menuItem->is_active)->toBeTrue();
        });

        it('is not external by default', function () {
            $menu = Menu::factory()->create();
            $menuItem = MenuItem::factory()->create(['menu_id' => $menu->id]);

            expect($menuItem->is_external)->toBeFalse();
        });

        it('has default sort order', function () {
            $menu = Menu::factory()->create();
            $menuItem = MenuItem::factory()->create(['menu_id' => $menu->id]);

            expect($menuItem->sort_order)->toBeInt();
            expect($menuItem->sort_order)->toBeGreaterThanOrEqual(0);
        });
    });

    describe('CSS Class and Icon', function () {
        it('can have CSS class', function () {
            $menu = Menu::factory()->create();
            $menuItem = MenuItem::factory()->create(['menu_id' => $menu->id, 'css_class' => 'nav-item']);

            expect($menuItem->css_class)->toBe('nav-item');
        });

        it('can have icon', function () {
            $menu = Menu::factory()->create();
            $menuItem = MenuItem::factory()->create(['menu_id' => $menu->id, 'icon' => 'heroicon-o-home']);

            expect($menuItem->icon)->toBe('heroicon-o-home');
        });
    });
});