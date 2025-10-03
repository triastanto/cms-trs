<?php

use App\Models\Post;

describe('Post Model - Feature Tests', function () {
    describe('Basic Attributes', function () {
        it('has the correct fillable attributes', function () {
            $post = new Post;
            $expectedFillable = [
                'title',
                'slug',
                'content',
                'excerpt',
                'status',
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

    describe('Relationships', function () {
        it('has user relationship method', function () {
            $post = new Post;
            expect(method_exists($post, 'user'))->toBeTrue();
        });

        it('has category relationship method', function () {
            $post = new Post;
            expect(method_exists($post, 'category'))->toBeTrue();
        });

        // Note: Post model has 'category' (singular) for primary category
        // The many-to-many relationship with categories is not implemented yet

        it('has tags relationship method', function () {
            $post = new Post;
            expect(method_exists($post, 'tags'))->toBeTrue();
        });
    });

    describe('Model Methods', function () {
        it('has getStatusOptions static method', function () {
            expect(method_exists(Post::class, 'getStatusOptions'))->toBeTrue();
        });

        it('has scopePublished method', function () {
            $post = new Post;
            expect(method_exists($post, 'scopePublished'))->toBeTrue();
        });

        it('has scopeDraft method', function () {
            $post = new Post;
            expect(method_exists($post, 'scopeDraft'))->toBeTrue();
        });
    });

    describe('Model Configuration', function () {
        it('uses HasFactory trait', function () {
            $post = new Post;
            expect(in_array('Illuminate\Database\Eloquent\Factories\HasFactory', class_uses($post)))->toBeTrue();
        });

        it('extends Model class', function () {
            $post = new Post;
            expect($post)->toBeInstanceOf(\Illuminate\Database\Eloquent\Model::class);
        });
    });
});
