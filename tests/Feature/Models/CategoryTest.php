<?php

use App\Models\Category;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Category Model', function () {
    beforeEach(function () {
        $this->category = Category::factory()->create();
    });

    describe('Basic Attributes', function () {
        it('has the correct fillable attributes', function () {
            $category = new Category();
            $expectedFillable = [
                'name',
                'slug',
                'description',
                'color',
                'parent_id',
                'sort_order',
                'is_active',
            ];

            expect($category->getFillable())->toBe($expectedFillable);
        });

        it('has the correct casts', function () {
            $category = new Category();
            $expectedCasts = [
                'is_active' => 'boolean',
                'sort_order' => 'integer',
            ];

            expect($category->getCasts())->toMatchArray($expectedCasts);
        });
    });

    describe('Hierarchical Relationships', function () {
        it('can have a parent category', function () {
            $parent = Category::factory()->create();
            $child = Category::factory()->create(['parent_id' => $parent->id]);

            expect($child->parent)->toBeInstanceOf(Category::class);
            expect($child->parent->id)->toBe($parent->id);
        });

        it('can have child categories', function () {
            $parent = Category::factory()->create();
            $children = Category::factory()->count(3)->create(['parent_id' => $parent->id]);

            expect($parent->children)->toHaveCount(3);
            expect($parent->children->first())->toBeInstanceOf(Category::class);
        });


        it('orders children by sort_order', function () {
            $parent = Category::factory()->create();
            $child1 = Category::factory()->create(['parent_id' => $parent->id, 'sort_order' => 3]);
            $child2 = Category::factory()->create(['parent_id' => $parent->id, 'sort_order' => 1]);
            $child3 = Category::factory()->create(['parent_id' => $parent->id, 'sort_order' => 2]);

            $children = $parent->children;

            expect($children->first()->id)->toBe($child2->id);
            expect($children->last()->id)->toBe($child1->id);
        });
    });

    describe('Post Relationships', function () {
        it('belongs to many posts', function () {
            $category = Category::factory()->create();
            $posts = Post::factory()->count(3)->create();
            
            $category->posts()->attach($posts->pluck('id'));

            expect($category->posts)->toHaveCount(3);
            expect($category->posts->first())->toBeInstanceOf(Post::class);
        });
    });

    describe('Slug Generation', function () {
        it('generates slug from name on creation', function () {
            $category = Category::create([
                'name' => 'Web Development',
                'is_active' => true,
            ]);

            expect($category->slug)->toBe('web-development');
        });


        it('uses slug as route key', function () {
            $category = Category::factory()->create(['slug' => 'my-category']);

            expect($category->getRouteKeyName())->toBe('slug');
        });
    });

    describe('Scopes', function () {
        it('can scope to active categories', function () {
            Category::factory()->create(['is_active' => true]);
            Category::factory()->create(['is_active' => false]);

            $activeCategories = Category::active()->get();

            expect($activeCategories->count())->toBeGreaterThanOrEqual(1);
            expect($activeCategories->where('is_active', false))->toHaveCount(0);
        });

        it('can scope to root categories', function () {
            $parent = Category::factory()->create();
            $child = Category::factory()->create(['parent_id' => $parent->id]);

            $rootCategories = Category::root()->get();

            expect($rootCategories->count())->toBeGreaterThanOrEqual(1);
            expect($rootCategories->where('parent_id', '!=', null))->toHaveCount(0);
        });
    });

    describe('Full Path', function () {
        it('generates full path for root category', function () {
            $category = Category::factory()->create(['name' => 'Technology']);

            expect($category->full_path)->toBe('Technology');
        });

        it('generates full path for nested category', function () {
            $parent = Category::factory()->create(['name' => 'Technology']);
            $child = Category::factory()->create(['name' => 'Web Development', 'parent_id' => $parent->id]);
            $grandchild = Category::factory()->create(['name' => 'Laravel', 'parent_id' => $child->id]);

            expect($grandchild->full_path)->toBe('Technology > Web Development > Laravel');
        });
    });

    describe('Default Values', function () {
        it('has default color', function () {
            $category = Category::factory()->create();

            expect($category->color)->toBe('#3B82F6');
        });

        it('has default sort order', function () {
            $category = Category::factory()->create();

            expect($category->sort_order)->toBeGreaterThanOrEqual(0);
            expect($category->sort_order)->toBeLessThanOrEqual(100);
        });

        it('is active by default', function () {
            $category = Category::factory()->create();

            expect($category->is_active)->toBeTrue();
        });
    });
});
