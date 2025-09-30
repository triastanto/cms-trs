<?php

use App\Models\Menu;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Menu Resource', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    });

    describe('Menu List', function () {
        it('can list menus', function () {
            $menus = Menu::factory()->count(3)->create();

            $response = $this->get('/admin/menus');

            $response->assertStatus(200);
            $response->assertSee($menus->first()->name);
        });

        it('can filter menus by active status', function () {
            Menu::factory()->create(['is_active' => true]);
            Menu::factory()->create(['is_active' => false]);

            $response = $this->get('/admin/menus?tableFilters[is_active][value]=1');

            $response->assertStatus(200);
        });

        it('can filter menus by location', function () {
            Menu::factory()->create(['location' => 'header-primary']);
            Menu::factory()->create(['location' => 'footer-primary']);

            $response = $this->get('/admin/menus?tableFilters[location][value]=header-primary');

            $response->assertStatus(200);
        });
    });

    describe('Menu Creation', function () {
        it('can render the create menu page', function () {
            $response = $this->get('/admin/menus/create');

            $response->assertStatus(200);
            $response->assertSee('Create Menu');
        });
    });

    describe('Menu Editing', function () {
        it('can render the edit menu page', function () {
            $menu = Menu::factory()->create();

            $response = $this->get("/admin/menus/{$menu->id}/edit");

            $response->assertStatus(200);
            $response->assertSee($menu->name);
        });
    });

    describe('Menu Deletion', function () {
        it('can view a menu for deletion', function () {
            $menu = Menu::factory()->create();

            $response = $this->get("/admin/menus/{$menu->id}/edit");

            $response->assertStatus(200);
            $response->assertSee('Delete');
        });
    });
});