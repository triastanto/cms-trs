<?php

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('can get all public settings', function () {
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

    $response = $this->getJson('/api/v1/settings');

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'data' => [
                'public_setting' => 'public_value',
            ],
        ])
        ->assertJsonMissing(['private_setting']);
});

test('can get specific public setting', function () {
    Setting::create([
        'key' => 'test_setting',
        'value' => 'test_value',
        'type' => 'string',
        'group_name' => 'test',
        'is_public' => true,
    ]);

    $response = $this->getJson('/api/v1/settings/test_setting');

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'data' => [
                'key' => 'test_setting',
                'value' => 'test_value',
            ],
        ]);
});

test('returns 404 for non-existent setting', function () {
    $response = $this->getJson('/api/v1/settings/non_existent');

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
            'message' => 'Setting not found',
        ]);
});

test('can get settings by group', function () {
    Setting::create([
        'key' => 'group1_setting',
        'value' => 'value1',
        'type' => 'string',
        'group_name' => 'group1',
        'is_public' => true,
    ]);

    Setting::create([
        'key' => 'group2_setting',
        'value' => 'value2',
        'type' => 'string',
        'group_name' => 'group2',
        'is_public' => true,
    ]);

    $response = $this->getJson('/api/v1/settings/group/group1');

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'data' => [
                'group1_setting' => 'value1',
            ],
        ])
        ->assertJsonMissing(['group2_setting']);
});

test('returns empty array for non-existent group', function () {
    $response = $this->getJson('/api/v1/settings/group/non_existent');

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'data' => [],
        ]);
});

test('handles different data types correctly', function () {
    Setting::create([
        'key' => 'string_setting',
        'value' => 'string_value',
        'type' => 'string',
        'group_name' => 'test',
        'is_public' => true,
    ]);

    Setting::create([
        'key' => 'boolean_setting',
        'value' => '1',
        'type' => 'boolean',
        'group_name' => 'test',
        'is_public' => true,
    ]);

    Setting::create([
        'key' => 'integer_setting',
        'value' => '42',
        'type' => 'integer',
        'group_name' => 'test',
        'is_public' => true,
    ]);

    Setting::create([
        'key' => 'json_setting',
        'value' => json_encode(['key' => 'value']),
        'type' => 'json',
        'group_name' => 'test',
        'is_public' => true,
    ]);

    $response = $this->getJson('/api/v1/settings');

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'data' => [
                'string_setting' => 'string_value',
                'boolean_setting' => true,
                'integer_setting' => 42,
                'json_setting' => ['key' => 'value'],
            ],
        ]);
});

test('api endpoints are accessible without authentication', function () {
    Setting::create([
        'key' => 'public_setting',
        'value' => 'public_value',
        'type' => 'string',
        'group_name' => 'test',
        'is_public' => true,
    ]);

    $response = $this->getJson('/api/v1/settings');
    $response->assertStatus(200);

    $response = $this->getJson('/api/v1/settings/public_setting');
    $response->assertStatus(200);

    $response = $this->getJson('/api/v1/settings/group/test');
    $response->assertStatus(200);
});
