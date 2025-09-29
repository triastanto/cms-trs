<?php

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Category Model - Simple Tests', function () {
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
});
