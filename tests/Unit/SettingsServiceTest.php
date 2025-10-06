<?php

use App\Models\Setting;
use App\Services\SettingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

uses(RefreshDatabase::class, TestCase::class);

beforeEach(function () {
    $this->settingsService = app(SettingsService::class);
    Cache::flush();
});

test('can get setting value', function () {
    Setting::create([
        'key' => 'test_setting',
        'value' => 'test_value',
        'type' => 'string',
        'group_name' => 'test',
    ]);

    $value = $this->settingsService->get('test_setting');
    expect($value)->toBe('test_value');
});

test('returns default value when setting not found', function () {
    $value = $this->settingsService->get('non_existent_setting', 'default_value');
    expect($value)->toBe('default_value');
});

test('can set setting value', function () {
    $this->settingsService->set('new_setting', 'new_value', 'string', 'test', true, 'Test setting');

    $this->assertDatabaseHas('settings', [
        'key' => 'new_setting',
        'value' => 'new_value',
        'type' => 'string',
        'group_name' => 'test',
        'is_public' => true,
    ]);
});

test('can update existing setting', function () {
    Setting::create([
        'key' => 'existing_setting',
        'value' => 'old_value',
        'type' => 'string',
        'group_name' => 'test',
    ]);

    $this->settingsService->set('existing_setting', 'new_value', 'string', 'test', false, 'Updated setting');

    $this->assertDatabaseHas('settings', [
        'key' => 'existing_setting',
        'value' => 'new_value',
    ]);

    expect(Setting::count())->toBe(1);
});

test('can get all settings', function () {
    Setting::create([
        'key' => 'setting1',
        'value' => 'value1',
        'type' => 'string',
        'group_name' => 'test',
    ]);

    Setting::create([
        'key' => 'setting2',
        'value' => 'value2',
        'type' => 'string',
        'group_name' => 'test',
    ]);

    $allSettings = $this->settingsService->getAllSettings();
    expect($allSettings)->toHaveCount(2);
    expect($allSettings['setting1'])->toBe('value1');
    expect($allSettings['setting2'])->toBe('value2');
});

test('can get public settings only', function () {
    Setting::create([
        'key' => 'public_setting',
        'value' => 'public_value',
        'type' => 'string',
        'group_name' => 'test',
        'is_public' => true,
    ]);

    Setting::create([
        'key' => 'private_setting',
        'value' => 'private_value',
        'type' => 'string',
        'group_name' => 'test',
        'is_public' => false,
    ]);

    $publicSettings = $this->settingsService->getPublicSettings();
    expect($publicSettings)->toHaveCount(1);
    expect($publicSettings['public_setting'])->toBe('public_value');
    expect($publicSettings)->not->toHaveKey('private_setting');
});

test('can get settings by group', function () {
    Setting::create([
        'key' => 'group1_setting',
        'value' => 'value1',
        'type' => 'string',
        'group_name' => 'group1',
    ]);

    Setting::create([
        'key' => 'group2_setting',
        'value' => 'value2',
        'type' => 'string',
        'group_name' => 'group2',
    ]);

    $group1Settings = $this->settingsService->getSettingsByGroup('group1');
    expect($group1Settings)->toHaveCount(1);
    expect($group1Settings['group1_setting'])->toBe('value1');
    expect($group1Settings)->not->toHaveKey('group2_setting');
});

test('can check if setting exists', function () {
    Setting::create([
        'key' => 'existing_setting',
        'value' => 'value',
        'type' => 'string',
        'group_name' => 'test',
    ]);

    expect($this->settingsService->has('existing_setting'))->toBeTrue();
    expect($this->settingsService->has('non_existent_setting'))->toBeFalse();
});

test('can delete setting', function () {
    Setting::create([
        'key' => 'to_delete',
        'value' => 'value',
        'type' => 'string',
        'group_name' => 'test',
    ]);

    $deleted = $this->settingsService->delete('to_delete');
    expect($deleted)->toBeTrue();
    expect(Setting::where('key', 'to_delete')->exists())->toBeFalse();
});

test('returns false when deleting non-existent setting', function () {
    $deleted = $this->settingsService->delete('non_existent');
    expect($deleted)->toBeFalse();
});

test('can set multiple settings', function () {
    $settings = [
        'setting1' => [
            'value' => 'value1',
            'type' => 'string',
            'group' => 'test',
            'is_public' => true,
            'description' => 'Setting 1',
        ],
        'setting2' => [
            'value' => 'value2',
            'type' => 'integer',
            'group' => 'test',
            'is_public' => false,
            'description' => 'Setting 2',
        ],
    ];

    $this->settingsService->setMultiple($settings);

    expect(Setting::count())->toBe(2);
    expect(Setting::where('key', 'setting1')->first()->value)->toBe('value1');
    expect(Setting::where('key', 'setting2')->first()->value)->toBe('value2');
});

test('caches settings for performance', function () {
    Setting::create([
        'key' => 'cached_setting',
        'value' => 'cached_value',
        'type' => 'string',
        'group_name' => 'test',
    ]);

    // First call should cache the result
    $this->settingsService->getAllSettings();
    expect(Cache::has('settings'))->toBeTrue();

    // Second call should use cache
    $cachedSettings = $this->settingsService->getAllSettings();
    expect($cachedSettings['cached_setting'])->toBe('cached_value');
});

test('clears cache when setting is updated', function () {
    Setting::create([
        'key' => 'cache_test',
        'value' => 'old_value',
        'type' => 'string',
        'group_name' => 'test',
    ]);

    // Populate cache
    $this->settingsService->getAllSettings();
    expect(Cache::has('settings'))->toBeTrue();

    // Update setting should clear cache
    $this->settingsService->set('cache_test', 'new_value');
    expect(Cache::has('settings'))->toBeFalse();
});
