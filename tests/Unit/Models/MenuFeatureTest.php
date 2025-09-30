<?php

use App\Models\Menu;

describe('Menu Model - Feature Tests', function () {
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

    describe('Relationships', function () {
        it('has menuItems relationship method', function () {
            $menu = new Menu;
            expect(method_exists($menu, 'menuItems'))->toBeTrue();
        });

        it('has rootMenuItems relationship method', function () {
            $menu = new Menu;
            expect(method_exists($menu, 'rootMenuItems'))->toBeTrue();
        });
    });

    describe('Model Methods', function () {
        it('has scopeActive method', function () {
            $menu = new Menu;
            expect(method_exists($menu, 'scopeActive'))->toBeTrue();
        });

        it('has getByLocation method', function () {
            $menu = new Menu;
            expect(method_exists($menu, 'getByLocation'))->toBeTrue();
        });

        it('has getLocations method', function () {
            $menu = new Menu;
            expect(method_exists($menu, 'getLocations'))->toBeTrue();
        });
    });

    describe('Model Configuration', function () {
        it('uses HasFactory trait', function () {
            $menu = new Menu;
            expect(in_array('Illuminate\Database\Eloquent\Factories\HasFactory', class_uses($menu)))->toBeTrue();
        });

        it('extends Model class', function () {
            $menu = new Menu;
            expect($menu)->toBeInstanceOf(\Illuminate\Database\Eloquent\Model::class);
        });
    });

    describe('Menu Features', function () {
        it('has fillable attributes defined', function () {
            $menu = new Menu;
            $fillable = $menu->getFillable();

            expect($fillable)->toContain('name');
            expect($fillable)->toContain('location');
            expect($fillable)->toContain('description');
            expect($fillable)->toContain('is_active');
        });

        it('has casts defined', function () {
            $menu = new Menu;
            $casts = $menu->getCasts();

            expect($casts)->toHaveKey('is_active');
            expect($casts['is_active'])->toBe('boolean');
        });
    });
});