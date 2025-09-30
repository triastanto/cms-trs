<?php

use App\Models\MenuItem;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('MenuItem Model - Simple Tests', function () {
    describe('Basic Attributes', function () {
        it('has the correct fillable attributes', function () {
            $menuItem = new MenuItem;
            $expectedFillable = [
                'menu_id',
                'parent_id',
                'title',
                'url',
                'target',
                'icon',
                'css_class',
                'sort_order',
                'is_active',
                'is_external',
            ];

            expect($menuItem->getFillable())->toBe($expectedFillable);
        });

        it('has the correct casts', function () {
            $menuItem = new MenuItem;
            $expectedCasts = [
                'is_active' => 'boolean',
                'is_external' => 'boolean',
                'sort_order' => 'integer',
            ];

            expect($menuItem->getCasts())->toMatchArray($expectedCasts);
        });
    });
});
