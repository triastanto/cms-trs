<?php

use App\Models\Post;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Tag Model', function () {
    beforeEach(function () {
        $this->tag = Tag::factory()->create();
    });

    describe('Basic Attributes', function () {
        it('has the correct fillable attributes', function () {
            $tag = new Tag;
            $expectedFillable = [
                'name',
                'slug',
                'description',
                'color',
                'is_active',
            ];

            expect($tag->getFillable())->toBe($expectedFillable);
        });

        it('has the correct casts', function () {
            $tag = new Tag;
            $expectedCasts = [
                'is_active' => 'boolean',
            ];

            expect($tag->getCasts())->toMatchArray($expectedCasts);
        });
    });

    describe('Relationships', function () {
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
                'name' => 'Web Development',
                'is_active' => true,
            ]);

            expect($tag->slug)->toBe('web-development');
        });

        it('does not overwrite existing slug on creation', function () {
            $tag = Tag::create([
                'name' => 'Web Development',
                'slug' => 'custom-slug',
                'is_active' => true,
            ]);

            expect($tag->slug)->toBe('custom-slug');
        });

        it('updates slug only when name changes and slug is empty', function () {
            $tag = Tag::create([
                'name' => 'Original Name',
                'is_active' => true,
            ]);

            $originalSlug = $tag->slug;

            // Update name with existing slug - should not change
            $tag->update(['name' => 'New Name']);
            expect($tag->slug)->toBe($originalSlug);

            // Update name with empty slug - should update
            $tag->slug = '';
            $tag->save();
            $tag->update(['name' => 'Another Name']);
            expect($tag->slug)->toBe('another-name');
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

            expect($activeTags->count())->toBeGreaterThanOrEqual(1);
            expect($activeTags->where('is_active', false))->toHaveCount(0);
        });
    });

    describe('Model Configuration', function () {
        it('uses HasFactory trait', function () {
            $tag = new Tag;
            expect(in_array('Illuminate\Database\Eloquent\Factories\HasFactory', class_uses($tag)))->toBeTrue();
        });

        it('extends Model class', function () {
            $tag = new Tag;
            expect($tag)->toBeInstanceOf(\Illuminate\Database\Eloquent\Model::class);
        });
    });

    describe('Default Values', function () {
        it('can be created with minimal data', function () {
            $tag = Tag::create([
                'name' => 'Test Tag',
            ]);

            expect($tag->name)->toBe('Test Tag');
            expect($tag->slug)->toBe('test-tag');
        });
    });
});
