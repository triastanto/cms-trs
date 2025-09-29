<?php

use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Tag Resource', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    });

    describe('Tag List', function () {
        it('can list tags', function () {
            $tags = Tag::factory()->count(3)->create();

            $response = $this->get('/admin/tags');

            $response->assertStatus(200);
            $response->assertSee($tags->first()->name);
        });

        it('can filter tags by active status', function () {
            Tag::factory()->create(['is_active' => true]);
            Tag::factory()->create(['is_active' => false]);

            $response = $this->get('/admin/tags?tableFilters[is_active][value]=1');

            $response->assertStatus(200);
        });
    });

    describe('Tag Creation', function () {
        it('can create a new tag', function () {
            $tagData = [
                'name' => 'Laravel',
                'slug' => 'laravel',
                'description' => 'Laravel framework',
                'color' => '#FF2D20',
                'is_active' => true,
            ];

            $response = $this->post('/admin/tags', $tagData);

            $response->assertRedirect();
            $this->assertDatabaseHas('tags', [
                'name' => 'Laravel',
                'slug' => 'laravel',
            ]);
        });
    });

    describe('Tag Editing', function () {
        it('can edit a tag', function () {
            $tag = Tag::factory()->create();

            $response = $this->get("/admin/tags/{$tag->id}/edit");

            $response->assertStatus(200);
            $response->assertSee($tag->name);
        });

        it('can update a tag', function () {
            $tag = Tag::factory()->create();

            $updateData = [
                'name' => 'Updated Tag',
                'slug' => 'updated-tag',
                'description' => 'Updated description',
                'color' => '#00FF00',
                'is_active' => true,
            ];

            $response = $this->put("/admin/tags/{$tag->id}", $updateData);

            $response->assertRedirect();
            $this->assertDatabaseHas('tags', [
                'id' => $tag->id,
                'name' => 'Updated Tag',
                'slug' => 'updated-tag',
            ]);
        });
    });

    describe('Tag Deletion', function () {
        it('can delete a tag', function () {
            $tag = Tag::factory()->create();

            $response = $this->delete("/admin/tags/{$tag->id}");

            $response->assertRedirect();
            $this->assertDatabaseMissing('tags', [
                'id' => $tag->id,
            ]);
        });
    });
});
