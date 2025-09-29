<?php

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Category Resource', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    });

    describe('Category List', function () {
        it('can list categories', function () {
            $categories = Category::factory()->count(3)->create();

            $response = $this->get('/admin/categories');

            $response->assertStatus(200);
            $response->assertSee($categories->first()->name);
        });

        it('can filter categories by active status', function () {
            Category::factory()->create(['is_active' => true]);
            Category::factory()->create(['is_active' => false]);

            $response = $this->get('/admin/categories?tableFilters[is_active][value]=1');

            $response->assertStatus(200);
        });
    });

    describe('Category Creation', function () {
        it('can render the create category page', function () {
            $response = $this->get('/admin/categories/create');

            $response->assertStatus(200);
            $response->assertSee('Create Category');
        });
    });

    describe('Category Editing', function () {
        it('can render the edit category page', function () {
            $category = Category::factory()->create();

            $response = $this->get("/admin/categories/{$category->slug}/edit");

            $response->assertStatus(200);
            $response->assertSee($category->name);
        });
    });

    describe('Category Deletion', function () {
        it('can view a category for deletion', function () {
            $category = Category::factory()->create();

            $response = $this->get("/admin/categories/{$category->slug}/edit");

            $response->assertStatus(200);
            $response->assertSee('Delete');
        });
    });
});
