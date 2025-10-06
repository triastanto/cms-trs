<?php

use App\Helpers\SettingsHelper;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(RefreshDatabase::class, TestCase::class);

beforeEach(function () {
    // Clear any existing settings
    Setting::truncate();
});

test('setting helper function works', function () {
    Setting::create([
        'key' => 'test_setting',
        'value' => 'test_value',
        'type' => 'string',
        'group_name' => 'test',
    ]);

    expect(setting('test_setting'))->toBe('test_value');
    expect(setting('non_existent', 'default'))->toBe('default');
});

test('settings helper function works', function () {
    Setting::create([
        'key' => 'setting1',
        'value' => 'value1',
        'type' => 'string',
        'group_name' => 'group1',
    ]);

    Setting::create([
        'key' => 'setting2',
        'value' => 'value2',
        'type' => 'string',
        'group_name' => 'group2',
    ]);

    $allSettings = settings();
    expect($allSettings)->toHaveCount(2);

    $group1Settings = settings('group1');
    expect($group1Settings)->toHaveCount(1);
    expect($group1Settings['setting1'])->toBe('value1');
});

test('public_settings helper function works', function () {
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

    $publicSettings = public_settings();
    expect($publicSettings)->toHaveCount(1);
    expect($publicSettings['public_setting'])->toBe('public_value');
    expect($publicSettings)->not->toHaveKey('private_setting');
});

test('SettingsHelper convenience methods work', function () {
    Setting::create([
        'key' => 'site_name',
        'value' => 'My Site',
        'type' => 'string',
        'group_name' => 'site',
        'is_public' => true,
    ]);

    Setting::create([
        'key' => 'posts_per_page',
        'value' => '15',
        'type' => 'integer',
        'group_name' => 'content',
        'is_public' => true,
    ]);

    Setting::create([
        'key' => 'max_file_size',
        'value' => '2048',
        'type' => 'integer',
        'group_name' => 'media',
        'is_public' => false,
    ]);

    expect(SettingsHelper::siteName())->toBe('My Site');
    expect(SettingsHelper::postsPerPage())->toBe(15);
    expect(SettingsHelper::maxFileSize())->toBe(2048);
});

test('SettingsHelper returns default values when settings not found', function () {
    expect(SettingsHelper::siteName())->toBe('CMS-TRS');
    expect(SettingsHelper::postsPerPage())->toBe(10);
    expect(SettingsHelper::maxFileSize())->toBe(2048);
    expect(SettingsHelper::autoGenerateExcerpts())->toBeTrue();
    expect(SettingsHelper::allowRegistration())->toBeFalse();
});

test('SettingsHelper handles different data types correctly', function () {
    Setting::create([
        'key' => 'boolean_setting',
        'value' => '1',
        'type' => 'boolean',
        'group_name' => 'test',
    ]);

    Setting::create([
        'key' => 'integer_setting',
        'value' => '42',
        'type' => 'integer',
        'group_name' => 'test',
    ]);

    Setting::create([
        'key' => 'json_setting',
        'value' => json_encode(['key' => 'value']),
        'type' => 'json',
        'group_name' => 'test',
    ]);

    expect(SettingsHelper::get('boolean_setting'))->toBeTrue();
    expect(SettingsHelper::get('integer_setting'))->toBe(42);
    expect(SettingsHelper::get('json_setting'))->toBe(['key' => 'value']);
});

test('SettingsHelper can set and get settings', function () {
    SettingsHelper::set('new_setting', 'new_value', 'string', 'test', true, 'Test setting');

    expect(SettingsHelper::get('new_setting'))->toBe('new_value');
    expect(SettingsHelper::has('new_setting'))->toBeTrue();
});

test('SettingsHelper can delete settings', function () {
    Setting::create([
        'key' => 'to_delete',
        'value' => 'value',
        'type' => 'string',
        'group_name' => 'test',
    ]);

    expect(SettingsHelper::has('to_delete'))->toBeTrue();

    SettingsHelper::delete('to_delete');

    expect(SettingsHelper::has('to_delete'))->toBeFalse();
});

test('SettingsHelper can clear cache', function () {
    Setting::create([
        'key' => 'cached_setting',
        'value' => 'cached_value',
        'type' => 'string',
        'group_name' => 'test',
    ]);

    // This should not throw an exception
    SettingsHelper::clearCache();
    expect(true)->toBeTrue();
});
