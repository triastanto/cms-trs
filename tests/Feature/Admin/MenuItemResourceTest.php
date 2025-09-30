<?php

use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('MenuItem Resource', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
        $this->menu = Menu::factory()->create();
    });

    describe('MenuItem List', function () {
        it('can list menu items', function () {
            $menuItems = MenuItem::factory()->count(3)->create(['menu_id' => $this->menu->id]);

            $response = $this->get('/admin/menu-items');

            $response->assertStatus(200);
            $response->assertSee($menuItems->first()->title);
        });

        it('can filter menu items by active status', function () {
            MenuItem::factory()->create(['menu_id' => $this->menu->id, 'is_active' => true]);
            MenuItem::factory()->create(['menu_id' => $this->menu->id, 'is_active' => false]);

            $response = $this->get('/admin/menu-items?tableFilters[is_active][value]=1');

            $response->assertStatus(200);
        });

        it('can filter menu items by external status', function () {
            MenuItem::factory()->create(['menu_id' => $this->menu->id, 'is_external' => true]);
            MenuItem::factory()->create(['menu_id' => $this->menu->id, 'is_external' => false]);

            $response = $this->get('/admin/menu-items?tableFilters[is_external][value]=1');

            $response->assertStatus(200);
        });

        it('can filter menu items by menu', function () {
            $menu2 = Menu::factory()->create();
            MenuItem::factory()->create(['menu_id' => $this->menu->id]);
            MenuItem::factory()->create(['menu_id' => $menu2->id]);

            $response = $this->get("/admin/menu-items?tableFilters[menu_id][value]={$this->menu->id}");

            $response->assertStatus(200);
        });
    });

    describe('MenuItem Creation', function () {
        it('can render the create menu item page', function () {
            $response = $this->get('/admin/menu-items/create');

            $response->assertStatus(200);
            $response->assertSee('Create Menu Item');
        });
    });

    describe('MenuItem Editing', function () {
        it('can render the edit menu item page', function () {
            $menuItem = MenuItem::factory()->create(['menu_id' => $this->menu->id]);

            $response = $this->get("/admin/menu-items/{$menuItem->id}/edit");

            $response->assertStatus(200);
            $response->assertSee($menuItem->title);
        });
    });

    describe('MenuItem Deletion', function () {
        it('can view a menu item for deletion', function () {
            $menuItem = MenuItem::factory()->create(['menu_id' => $this->menu->id]);

            $response = $this->get("/admin/menu-items/{$menuItem->id}/edit");

            $response->assertStatus(200);
            $response->assertSee('Delete');
        });
    });
});