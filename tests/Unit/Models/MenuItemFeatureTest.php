<?php

use App\Models\MenuItem;

describe('MenuItem Model - Feature Tests', function () {
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

    describe('Relationships', function () {
        it('has menu relationship method', function () {
            $menuItem = new MenuItem;
            expect(method_exists($menuItem, 'menu'))->toBeTrue();
        });

        it('has parent relationship method', function () {
            $menuItem = new MenuItem;
            expect(method_exists($menuItem, 'parent'))->toBeTrue();
        });

        it('has children relationship method', function () {
            $menuItem = new MenuItem;
            expect(method_exists($menuItem, 'children'))->toBeTrue();
        });

        it('has descendants relationship method', function () {
            $menuItem = new MenuItem;
            expect(method_exists($menuItem, 'descendants'))->toBeTrue();
        });
    });

    describe('Model Methods', function () {
        it('has scopeActive method', function () {
            $menuItem = new MenuItem;
            expect(method_exists($menuItem, 'scopeActive'))->toBeTrue();
        });

        it('has scopeRoot method', function () {
            $menuItem = new MenuItem;
            expect(method_exists($menuItem, 'scopeRoot'))->toBeTrue();
        });

        it('has hasChildren method', function () {
            $menuItem = new MenuItem;
            expect(method_exists($menuItem, 'hasChildren'))->toBeTrue();
        });

        it('has isExternal method', function () {
            $menuItem = new MenuItem;
            expect(method_exists($menuItem, 'isExternal'))->toBeTrue();
        });

        it('has getTargetOptions method', function () {
            $menuItem = new MenuItem;
            expect(method_exists($menuItem, 'getTargetOptions'))->toBeTrue();
        });
    });

    describe('Model Configuration', function () {
        it('uses HasFactory trait', function () {
            $menuItem = new MenuItem;
            expect(in_array('Illuminate\Database\Eloquent\Factories\HasFactory', class_uses($menuItem)))->toBeTrue();
        });

        it('extends Model class', function () {
            $menuItem = new MenuItem;
            expect($menuItem)->toBeInstanceOf(\Illuminate\Database\Eloquent\Model::class);
        });
    });

    describe('MenuItem Features', function () {
        it('has fillable attributes defined', function () {
            $menuItem = new MenuItem;
            $fillable = $menuItem->getFillable();

            expect($fillable)->toContain('menu_id');
            expect($fillable)->toContain('parent_id');
            expect($fillable)->toContain('title');
            expect($fillable)->toContain('url');
            expect($fillable)->toContain('is_active');
            expect($fillable)->toContain('is_external');
        });

        it('has casts defined', function () {
            $menuItem = new MenuItem;
            $casts = $menuItem->getCasts();

            expect($casts)->toHaveKey('is_active');
            expect($casts['is_active'])->toBe('boolean');
            expect($casts)->toHaveKey('is_external');
            expect($casts['is_external'])->toBe('boolean');
            expect($casts)->toHaveKey('sort_order');
            expect($casts['sort_order'])->toBe('integer');
        });
    });
});