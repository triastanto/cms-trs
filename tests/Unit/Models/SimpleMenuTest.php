<?php

use App\Models\Menu;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Menu Model - Simple Tests', function () {
    describe('Basic Attributes', function () {
        it('has the correct fillable attributes', function () {
            $menu = new Menu;
            $expectedFillable = [
                'name',
                'location',
                'description',
                'is_active',
            ];

            expect($menu->getFillable())->toBe($expectedFillable);
        });

        it('has the correct casts', function () {
            $menu = new Menu;
            $expectedCasts = [
                'is_active' => 'boolean',
            ];

            expect($menu->getCasts())->toMatchArray($expectedCasts);
        });
    });
});
