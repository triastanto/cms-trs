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
        it('can render the create tag page', function () {
            $response = $this->get('/admin/tags/create');

            $response->assertStatus(200);
            $response->assertSee('Create Tag');
        });
    });

    describe('Tag Editing', function () {
        it('can render the edit tag page', function () {
            $tag = Tag::factory()->create();

            $response = $this->get("/admin/tags/{$tag->slug}/edit");

            $response->assertStatus(200);
            $response->assertSee($tag->name);
        });
    });

    describe('Tag Deletion', function () {
        it('can view a tag for deletion', function () {
            $tag = Tag::factory()->create();

            $response = $this->get("/admin/tags/{$tag->slug}/edit");

            $response->assertStatus(200);
            $response->assertSee('Delete');
        });
    });
});
