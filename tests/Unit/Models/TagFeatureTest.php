<?php

use App\Models\Tag;

describe('Tag Model - Feature Tests', function () {
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

    describe('Route Key', function () {
        it('uses slug as route key', function () {
            $tag = new Tag;
            expect($tag->getRouteKeyName())->toBe('slug');
        });
    });

    describe('Relationships', function () {
        it('has posts relationship method', function () {
            $tag = new Tag;
            expect(method_exists($tag, 'posts'))->toBeTrue();
        });
    });

    describe('Model Methods', function () {
        it('has scopeActive method', function () {
            $tag = new Tag;
            expect(method_exists($tag, 'scopeActive'))->toBeTrue();
        });

        // Note: getPostsCountAttribute is not implemented yet
        // This would be added for counting posts associated with tags
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

    describe('Slug Generation', function () {
        it('has boot method for slug generation', function () {
            $tag = new Tag;
            expect(method_exists($tag, 'boot'))->toBeTrue();
        });
    });

    describe('Tag Features', function () {
        it('has fillable attributes defined', function () {
            $tag = new Tag;
            $fillable = $tag->getFillable();

            expect($fillable)->toContain('name');
            expect($fillable)->toContain('slug');
            expect($fillable)->toContain('description');
            expect($fillable)->toContain('color');
            expect($fillable)->toContain('is_active');
        });

        it('has casts defined', function () {
            $tag = new Tag;
            $casts = $tag->getCasts();

            expect($casts)->toHaveKey('is_active');
            expect($casts['is_active'])->toBe('boolean');
        });
    });
});
