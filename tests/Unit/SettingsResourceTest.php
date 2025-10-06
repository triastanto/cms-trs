<?php

use App\Filament\Resources\SettingsResource;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(RefreshDatabase::class, TestCase::class);

test('settings resource has correct model', function () {
    expect(SettingsResource::getModel())->toBe(Setting::class);
});

test('settings resource has correct navigation icon', function () {
    expect(SettingsResource::getNavigationIcon())->not->toBeNull();
});

test('settings resource has correct navigation group', function () {
    expect(SettingsResource::getNavigationGroup())->toBe('System');
});

test('settings resource has correct record title attribute', function () {
    expect(SettingsResource::getRecordTitleAttribute())->toBe('key');
});

test('settings resource can be instantiated', function () {
    $resource = new SettingsResource;
    expect($resource)->toBeInstanceOf(SettingsResource::class);
});
