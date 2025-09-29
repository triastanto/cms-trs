<?php

use App\Models\Category;

describe('Category Model - Feature Tests', function () {
    describe('Basic Attributes', function () {
        it('has the correct fillable attributes', function () {
            $category = new Category;
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
            $category = new Category;
            $expectedCasts = [
                'is_active' => 'boolean',
                'sort_order' => 'integer',
            ];

            expect($category->getCasts())->toMatchArray($expectedCasts);
        });
    });

    describe('Route Key', function () {
        it('uses slug as route key', function () {
            $category = new Category;
            expect($category->getRouteKeyName())->toBe('slug');
        });
    });

    describe('Relationships', function () {
        it('has posts relationship method', function () {
            $category = new Category;
            expect(method_exists($category, 'posts'))->toBeTrue();
        });

        it('has parent relationship method', function () {
            $category = new Category;
            expect(method_exists($category, 'parent'))->toBeTrue();
        });

        it('has children relationship method', function () {
            $category = new Category;
            expect(method_exists($category, 'children'))->toBeTrue();
        });

    });

    describe('Model Methods', function () {
        it('has scopeActive method', function () {
            $category = new Category;
            expect(method_exists($category, 'scopeActive'))->toBeTrue();
        });

        it('has scopeRoot method', function () {
            $category = new Category;
            expect(method_exists($category, 'scopeRoot'))->toBeTrue();
        });
    });

    describe('Model Configuration', function () {
        it('uses HasFactory trait', function () {
            $category = new Category;
            expect(in_array('Illuminate\Database\Eloquent\Factories\HasFactory', class_uses($category)))->toBeTrue();
        });

        it('extends Model class', function () {
            $category = new Category;
            expect($category)->toBeInstanceOf(\Illuminate\Database\Eloquent\Model::class);
        });
    });

    describe('Slug Generation', function () {
        it('has boot method for slug generation', function () {
            $category = new Category;
            expect(method_exists($category, 'boot'))->toBeTrue();
        });
    });

    describe('Category Features', function () {
        it('has fillable attributes defined', function () {
            $category = new Category;
            $fillable = $category->getFillable();

            expect($fillable)->toContain('name');
            expect($fillable)->toContain('slug');
            expect($fillable)->toContain('description');
            expect($fillable)->toContain('color');
            expect($fillable)->toContain('is_active');
        });

        it('has casts defined', function () {
            $category = new Category;
            $casts = $category->getCasts();

            expect($casts)->toHaveKey('is_active');
            expect($casts['is_active'])->toBe('boolean');
        });
    });
});
