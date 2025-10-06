<?php

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(RefreshDatabase::class, TestCase::class);

test('can create setting', function () {
    $setting = Setting::create([
        'key' => 'test_setting',
        'value' => 'test_value',
        'type' => 'string',
        'group_name' => 'test',
        'is_public' => true,
        'description' => 'Test setting',
    ]);

    expect($setting->key)->toBe('test_setting');
    expect($setting->value)->toBe('test_value');
    expect($setting->type)->toBe('string');
    expect($setting->group_name)->toBe('test');
    expect($setting->is_public)->toBeTrue();
});

test('can get typed value for string', function () {
    $setting = Setting::create([
        'key' => 'string_setting',
        'value' => 'test_string',
        'type' => 'string',
        'group_name' => 'test',
    ]);

    expect($setting->getTypedValue())->toBe('test_string');
});

test('can get typed value for boolean', function () {
    $setting = Setting::create([
        'key' => 'boolean_setting',
        'value' => '1',
        'type' => 'boolean',
        'group_name' => 'test',
    ]);

    expect($setting->getTypedValue())->toBeTrue();
});

test('can get typed value for integer', function () {
    $setting = Setting::create([
        'key' => 'integer_setting',
        'value' => '42',
        'type' => 'integer',
        'group_name' => 'test',
    ]);

    expect($setting->getTypedValue())->toBe(42);
});

test('can get typed value for json', function () {
    $jsonData = ['key' => 'value', 'number' => 123];
    $setting = Setting::create([
        'key' => 'json_setting',
        'value' => json_encode($jsonData),
        'type' => 'json',
        'group_name' => 'test',
    ]);

    expect($setting->getTypedValue())->toBe($jsonData);
});

test('public scope works', function () {
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

    $publicSettings = Setting::public()->get();
    expect($publicSettings)->toHaveCount(1);
    expect($publicSettings->first()->key)->toBe('public_setting');
});

test('by group scope works', function () {
    Setting::create([
        'key' => 'test_setting_1',
        'value' => 'value1',
        'type' => 'string',
        'group_name' => 'test_group',
    ]);

    Setting::create([
        'key' => 'test_setting_2',
        'value' => 'value2',
        'type' => 'string',
        'group_name' => 'other_group',
    ]);

    $testGroupSettings = Setting::byGroup('test_group')->get();
    expect($testGroupSettings)->toHaveCount(1);
    expect($testGroupSettings->first()->key)->toBe('test_setting_1');
});

test('value mutator formats boolean correctly', function () {
    $setting = new Setting([
        'key' => 'boolean_test',
        'type' => 'boolean',
        'group_name' => 'test',
    ]);

    $setting->value = true;
    expect($setting->value)->toBe('1');

    $setting->value = false;
    expect($setting->value)->toBe('0');
});

test('value mutator formats json correctly', function () {
    $setting = new Setting([
        'key' => 'json_test',
        'type' => 'json',
        'group_name' => 'test',
    ]);

    $arrayData = ['test' => 'value', 'number' => 123];
    $setting->value = $arrayData;
    expect($setting->value)->toBe(json_encode($arrayData));
});

test('value mutator formats string correctly', function () {
    $setting = new Setting([
        'key' => 'string_test',
        'type' => 'string',
        'group_name' => 'test',
    ]);

    $setting->value = 123;
    expect($setting->value)->toBe('123');
});
