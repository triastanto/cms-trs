<?php

namespace Tests\Feature;

use App\Helpers\SettingsHelper;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingsIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_post_uses_settings_for_default_status(): void
    {
        // Set a custom default post status
        SettingsHelper::set('default_post_status', 'published', 'string', 'content', false, 'Default post status');

        $post = Post::factory()->create(['status' => null]);

        $this->assertEquals('published', $post->status);
    }

    public function test_post_uses_settings_for_excerpt_generation(): void
    {
        // Enable auto-excerpt generation
        SettingsHelper::set('auto_generate_excerpts', true, 'boolean', 'content', false, 'Auto generate excerpts');
        SettingsHelper::set('excerpt_length', 100, 'integer', 'content', false, 'Excerpt length');

        // Clear cache to ensure settings are fresh
        SettingsHelper::clearCache();

        $post = Post::factory()->create([
            'content' => 'This is a very long content that should be truncated to the specified length when generating an excerpt automatically.',
            'excerpt' => null,
        ]);

        // Check excerpt length (accounting for ellipsis)
        $this->assertTrue(strlen($post->excerpt) <= 103); // 100 + 3 for ellipsis
        $this->assertStringContainsString('This is a very long content', $post->excerpt);
    }

    public function test_category_uses_settings_for_default_color(): void
    {
        // Set a custom default category color
        SettingsHelper::set('default_category_color', '#FF5733', 'string', 'content', false, 'Default category color');

        $category = Category::factory()->create(['color' => null]);

        $this->assertEquals('#FF5733', $category->color);
    }

    public function test_tag_uses_settings_for_default_color(): void
    {
        // Set a custom default tag color
        SettingsHelper::set('default_tag_color', '#33FF57', 'string', 'content', false, 'Default tag color');

        $tag = Tag::factory()->create(['color' => null]);

        $this->assertEquals('#33FF57', $tag->color);
    }

    public function test_settings_helper_convenience_methods(): void
    {
        // Test site settings
        $this->assertEquals('CMS-TRS', SettingsHelper::siteName());
        $this->assertEquals('Content Management System', SettingsHelper::siteTagline());

        // Test content settings
        $this->assertEquals(10, SettingsHelper::postsPerPage());
        $this->assertEquals(2048, SettingsHelper::maxFileSize());
        $this->assertTrue(SettingsHelper::autoGenerateExcerpts());

        // Test user settings
        $this->assertFalse(SettingsHelper::allowRegistration());
        $this->assertTrue(SettingsHelper::requireEmailVerification());
        $this->assertEquals(8, SettingsHelper::minPasswordLength());
    }

    public function test_settings_group_methods(): void
    {
        // Test getting settings by group
        $seoSettings = SettingsHelper::seoSettings();
        $this->assertArrayHasKey('title', $seoSettings);
        $this->assertArrayHasKey('description', $seoSettings);

        $siteSettings = SettingsHelper::siteSettings();
        $this->assertArrayHasKey('name', $siteSettings);
        $this->assertArrayHasKey('url', $siteSettings);

        $mediaSettings = SettingsHelper::mediaSettings();
        $this->assertArrayHasKey('max_file_size', $mediaSettings);
        $this->assertArrayHasKey('image_quality', $mediaSettings);
    }

    public function test_settings_affect_application_configuration(): void
    {
        // Set custom settings
        SettingsHelper::set('site_name', 'My Custom Site', 'string', 'site', true, 'Site name');
        SettingsHelper::set('timezone', 'America/New_York', 'string', 'general', false, 'Timezone');
        SettingsHelper::set('locale', 'es', 'string', 'general', false, 'Locale');

        // Test that settings are accessible
        $this->assertEquals('My Custom Site', SettingsHelper::siteName());
        $this->assertEquals('America/New_York', SettingsHelper::timezone());
        $this->assertEquals('es', SettingsHelper::locale());
    }

    public function test_maintenance_mode_setting(): void
    {
        // Test maintenance mode is disabled by default
        $this->assertFalse(SettingsHelper::maintenanceMode());

        // Enable maintenance mode
        SettingsHelper::set('maintenance_mode', true, 'boolean', 'general', false, 'Maintenance mode');

        $this->assertTrue(SettingsHelper::maintenanceMode());
    }

    public function test_public_settings_api(): void
    {
        // Create some public settings
        SettingsHelper::set('site_name', 'Public Site', 'string', 'site', true, 'Site name');
        SettingsHelper::set('site_description', 'Public description', 'text', 'site', true, 'Site description');
        SettingsHelper::set('private_setting', 'Private value', 'string', 'general', false, 'Private setting');

        $publicSettings = SettingsHelper::public();

        $this->assertArrayHasKey('site_name', $publicSettings);
        $this->assertArrayHasKey('site_description', $publicSettings);
        $this->assertArrayNotHasKey('private_setting', $publicSettings);
    }
}
