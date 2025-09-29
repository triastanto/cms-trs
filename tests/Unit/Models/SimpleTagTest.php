<?php

use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Tag Model - Simple Tests', function () {
    describe('Basic Attributes', function () {
        it('has the correct fillable attributes', function () {
            $tag = new Tag();
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
            $tag = new Tag();
            $expectedCasts = [
                'is_active' => 'boolean',
            ];

            expect($tag->getCasts())->toMatchArray($expectedCasts);
        });
    });

    describe('Route Key', function () {
        it('uses slug as route key', function () {
            $tag = new Tag();
            expect($tag->getRouteKeyName())->toBe('slug');
        });
    });
});
