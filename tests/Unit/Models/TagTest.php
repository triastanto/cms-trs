<?php

use App\Models\Tag;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Tag Model', function () {
    beforeEach(function () {
        $this->tag = Tag::factory()->create();
    });

    describe('Basic Attributes', function () {
        it('has the correct fillable attributes', function () {
            $tag = new Tag();
            $expectedFillable = [
                'name',
                'slug',
                'color',
                'description',
                'is_active',
            ];

            expect($tag->getFillable())->toBe($expectedFillable);
        });

        it('has the correct casts', function () {
            $tag = new Tag();
            $expectedCasts = [
                'is_active' => 'boolean',
            ];

            expect($tag->getCasts())->toMatchArray($expectedCasts);
        });
    });

    describe('Post Relationships', function () {
        it('belongs to many posts', function () {
            $tag = Tag::factory()->create();
            $posts = Post::factory()->count(3)->create();
            
            $tag->posts()->attach($posts->pluck('id'));

            expect($tag->posts)->toHaveCount(3);
            expect($tag->posts->first())->toBeInstanceOf(Post::class);
        });
    });

    describe('Slug Generation', function () {
        it('generates slug from name on creation', function () {
            $tag = Tag::create([
                'name' => 'Laravel Framework',
                'is_active' => true,
            ]);

            expect($tag->slug)->toBe('laravel-framework');
        });

        it('updates slug when name changes', function () {
            $tag = Tag::factory()->create(['name' => 'Original Name']);

            $tag->update(['name' => 'Updated Name']);

            expect($tag->fresh()->slug)->toBe('updated-name');
        });

        it('uses slug as route key', function () {
            $tag = Tag::factory()->create(['slug' => 'my-tag']);

            expect($tag->getRouteKeyName())->toBe('slug');
        });
    });

    describe('Scopes', function () {
        it('can scope to active tags', function () {
            Tag::factory()->create(['is_active' => true]);
            Tag::factory()->create(['is_active' => false]);

            $activeTags = Tag::active()->get();

            expect($activeTags)->toHaveCount(1);
            expect($activeTags->first()->is_active)->toBeTrue();
        });
    });

    describe('Posts Count', function () {
        it('returns correct posts count', function () {
            $tag = Tag::factory()->create();
            $posts = Post::factory()->count(5)->create();
            
            $tag->posts()->attach($posts->pluck('id'));

            expect($tag->posts_count)->toBe(5);
        });

        it('returns zero when no posts', function () {
            $tag = Tag::factory()->create();

            expect($tag->posts_count)->toBe(0);
        });
    });

    describe('Default Values', function () {
        it('has default color', function () {
            $tag = Tag::factory()->create();

            expect($tag->color)->toBe('#6B7280');
        });

        it('is active by default', function () {
            $tag = Tag::factory()->create();

            expect($tag->is_active)->toBeTrue();
        });
    });

    describe('Tag Creation', function () {
        it('can create tag with all attributes', function () {
            $tag = Tag::create([
                'name' => 'PHP',
                'slug' => 'php',
                'color' => '#777BB4',
                'description' => 'PHP programming language',
                'is_active' => true,
            ]);

            expect($tag->name)->toBe('PHP');
            expect($tag->slug)->toBe('php');
            expect($tag->color)->toBe('#777BB4');
            expect($tag->description)->toBe('PHP programming language');
            expect($tag->is_active)->toBeTrue();
        });

        it('can create tag with minimal attributes', function () {
            $tag = Tag::create([
                'name' => 'JavaScript',
            ]);

            expect($tag->name)->toBe('JavaScript');
            expect($tag->slug)->toBe('javascript');
            expect($tag->color)->toBe('#6B7280');
            expect($tag->is_active)->toBeTrue();
        });
    });

    describe('Tag Updates', function () {
        it('can update tag attributes', function () {
            $tag = Tag::factory()->create();

            $tag->update([
                'name' => 'Updated Name',
                'color' => '#FF0000',
                'description' => 'Updated description',
            ]);

            expect($tag->fresh()->name)->toBe('Updated Name');
            expect($tag->fresh()->slug)->toBe('updated-name');
            expect($tag->fresh()->color)->toBe('#FF0000');
            expect($tag->fresh()->description)->toBe('Updated description');
        });

        it('can deactivate tag', function () {
            $tag = Tag::factory()->create(['is_active' => true]);

            $tag->update(['is_active' => false]);

            expect($tag->fresh()->is_active)->toBeFalse();
        });
    });
});
