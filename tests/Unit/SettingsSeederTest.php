<?php

use App\Models\Setting;
use Database\Seeders\SettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(RefreshDatabase::class, TestCase::class);

test('seeder creates all expected settings', function () {
    $this->artisan('db:seed', ['--class' => SettingsSeeder::class]);

    // Check that we have the expected number of settings
    expect(Setting::count())->toBeGreaterThan(30);

    // Check some key settings exist
    expect(Setting::where('key', 'site_name')->exists())->toBeTrue();
    expect(Setting::where('key', 'site_tagline')->exists())->toBeTrue();
    expect(Setting::where('key', 'posts_per_page')->exists())->toBeTrue();
    expect(Setting::where('key', 'max_file_size')->exists())->toBeTrue();
    expect(Setting::where('key', 'email_from_name')->exists())->toBeTrue();
});

test('seeder creates settings with correct groups', function () {
    $this->artisan('db:seed', ['--class' => SettingsSeeder::class]);

    $siteSettings = Setting::where('group_name', 'site')->get();
    expect($siteSettings)->toHaveCount(5); // site_name, site_tagline, site_description, site_keywords, site_url

    $seoSettings = Setting::where('group_name', 'seo')->get();
    expect($seoSettings)->toHaveCount(5); // default_meta_title, default_meta_description, etc.

    $mediaSettings = Setting::where('group_name', 'media')->get();
    expect($mediaSettings)->toHaveCount(6); // max_file_size, max_image_size, etc.

    $contentSettings = Setting::where('group_name', 'content')->get();
    expect($contentSettings)->toHaveCount(6); // default_post_status, posts_per_page, etc.
});

test('seeder creates settings with correct types', function () {
    $this->artisan('db:seed', ['--class' => SettingsSeeder::class]);

    $stringSettings = Setting::where('type', 'string')->get();
    expect($stringSettings)->toHaveCount(18); // Various string settings

    $booleanSettings = Setting::where('type', 'boolean')->get();
    expect($booleanSettings)->toHaveCount(6); // Various boolean settings

    $integerSettings = Setting::where('type', 'integer')->get();
    expect($integerSettings)->toHaveCount(9); // Various integer settings

    $textSettings = Setting::where('type', 'text')->get();
    expect($textSettings)->toHaveCount(2); // site_description, default_meta_description
});

test('seeder creates public and private settings correctly', function () {
    $this->artisan('db:seed', ['--class' => SettingsSeeder::class]);

    $publicSettings = Setting::where('is_public', true)->get();
    expect($publicSettings)->toHaveCount(11); // Public settings for API access

    $privateSettings = Setting::where('is_public', false)->get();
    expect($privateSettings)->toHaveCount(24); // Private settings for internal use
});

test('seeder can be run multiple times without duplicates', function () {
    $this->artisan('db:seed', ['--class' => SettingsSeeder::class]);
    $firstCount = Setting::count();

    $this->artisan('db:seed', ['--class' => SettingsSeeder::class]);
    $secondCount = Setting::count();

    expect($secondCount)->toBe($firstCount);
});

test('seeder creates settings with descriptions', function () {
    $this->artisan('db:seed', ['--class' => SettingsSeeder::class]);

    $settingsWithDescriptions = Setting::whereNotNull('description')->get();
    expect($settingsWithDescriptions)->toHaveCount(35);

    $siteNameSetting = Setting::where('key', 'site_name')->first();
    expect($siteNameSetting->description)->toBe('The name of your website');
});

test('seeder creates settings with correct default values', function () {
    $this->artisan('db:seed', ['--class' => SettingsSeeder::class]);

    $siteName = Setting::where('key', 'site_name')->first();
    expect($siteName->value)->toBe('CMS-TRS');

    $postsPerPage = Setting::where('key', 'posts_per_page')->first();
    expect($postsPerPage->value)->toBe('10');

    $maxFileSize = Setting::where('key', 'max_file_size')->first();
    expect($maxFileSize->value)->toBe('2048');

    $allowRegistration = Setting::where('key', 'allow_registration')->first();
    expect($allowRegistration)->not->toBeNull();
    expect($allowRegistration->type)->toBe('boolean');
    expect($allowRegistration->value)->toBe('0'); // false
});
