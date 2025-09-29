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
        it('can create a new category', function () {
            $categoryData = [
                'name' => 'Technology',
                'slug' => 'technology',
                'description' => 'Posts about technology',
                'color' => '#3B82F6',
                'sort_order' => 1,
                'is_active' => true,
            ];

            $response = $this->post('/admin/categories', $categoryData);

            $response->assertRedirect();
            $this->assertDatabaseHas('categories', [
                'name' => 'Technology',
                'slug' => 'technology',
            ]);
        });

        it('can create a child category', function () {
            $parent = Category::factory()->create();
            $categoryData = [
                'name' => 'Web Development',
                'slug' => 'web-development',
                'parent_id' => $parent->id,
                'is_active' => true,
            ];

            $response = $this->post('/admin/categories', $categoryData);

            $response->assertRedirect();
            $this->assertDatabaseHas('categories', [
                'name' => 'Web Development',
                'parent_id' => $parent->id,
            ]);
        });
    });

    describe('Category Editing', function () {
        it('can edit a category', function () {
            $category = Category::factory()->create();

            $response = $this->get("/admin/categories/{$category->id}/edit");

            $response->assertStatus(200);
            $response->assertSee($category->name);
        });

        it('can update a category', function () {
            $category = Category::factory()->create();

            $updateData = [
                'name' => 'Updated Category',
                'slug' => 'updated-category',
                'description' => 'Updated description',
                'color' => '#FF0000',
                'is_active' => true,
            ];

            $response = $this->put("/admin/categories/{$category->id}", $updateData);

            $response->assertRedirect();
            $this->assertDatabaseHas('categories', [
                'id' => $category->id,
                'name' => 'Updated Category',
                'slug' => 'updated-category',
            ]);
        });
    });

    describe('Category Deletion', function () {
        it('can delete a category', function () {
            $category = Category::factory()->create();

            $response = $this->delete("/admin/categories/{$category->id}");

            $response->assertRedirect();
            $this->assertDatabaseMissing('categories', [
                'id' => $category->id,
            ]);
        });
    });
});
