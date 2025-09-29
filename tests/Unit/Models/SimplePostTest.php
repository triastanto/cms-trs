<?php

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Post Model - Simple Tests', function () {
    describe('Basic Attributes', function () {
        it('has the correct fillable attributes', function () {
            $post = new Post;
            $expectedFillable = [
                'title',
                'slug',
                'content',
                'excerpt',
                'status',
                'featured_image',
                'meta_title',
                'meta_description',
                'published_at',
                'user_id',
                'category_id',
            ];

            expect($post->getFillable())->toBe($expectedFillable);
        });

        it('has the correct casts', function () {
            $post = new Post;
            $expectedCasts = [
                'published_at' => 'datetime',
                'created_at' => 'datetime',
                'updated_at' => 'datetime',
            ];

            expect($post->getCasts())->toMatchArray($expectedCasts);
        });
    });

    describe('Status Options', function () {
        it('returns correct status options', function () {
            $statusOptions = Post::getStatusOptions();

            expect($statusOptions)->toBe([
                'draft' => 'Draft',
                'published' => 'Published',
                'archived' => 'Archived',
            ]);
        });
    });

    describe('Route Key', function () {
        it('uses slug as route key', function () {
            $post = new Post;
            expect($post->getRouteKeyName())->toBe('slug');
        });
    });
});
