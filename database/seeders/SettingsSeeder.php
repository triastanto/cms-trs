<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // HIGH PRIORITY - Site/General Settings
            'site_name' => [
                'value' => 'CMS-TRS',
                'type' => 'string',
                'group' => 'site',
                'is_public' => true,
                'description' => 'The name of your website',
            ],
            'site_tagline' => [
                'value' => 'Content Management System',
                'type' => 'string',
                'group' => 'site',
                'is_public' => true,
                'description' => 'The tagline or subtitle of your website',
            ],
            'site_description' => [
                'value' => 'A powerful CMS built with Laravel and Filament',
                'type' => 'text',
                'group' => 'site',
                'is_public' => true,
                'description' => 'A brief description of your website',
            ],
            'site_keywords' => [
                'value' => 'cms, laravel, filament, content management',
                'type' => 'string',
                'group' => 'site',
                'is_public' => true,
                'description' => 'Keywords for SEO',
            ],
            'site_url' => [
                'value' => 'https://yourdomain.com',
                'type' => 'string',
                'group' => 'site',
                'is_public' => true,
                'description' => 'The main URL of your website',
            ],
            'timezone' => [
                'value' => 'UTC',
                'type' => 'string',
                'group' => 'general',
                'is_public' => false,
                'description' => 'Default timezone for the application',
            ],
            'locale' => [
                'value' => 'en',
                'type' => 'string',
                'group' => 'general',
                'is_public' => false,
                'description' => 'Default locale for the application',
            ],
            'maintenance_mode' => [
                'value' => false,
                'type' => 'boolean',
                'group' => 'general',
                'is_public' => false,
                'description' => 'Enable maintenance mode',
            ],

            // HIGH PRIORITY - SEO Settings
            'default_meta_title' => [
                'value' => 'CMS-TRS - Content Management System',
                'type' => 'string',
                'group' => 'seo',
                'is_public' => true,
                'description' => 'Default meta title for pages',
            ],
            'default_meta_description' => [
                'value' => 'A powerful content management system built with Laravel and Filament',
                'type' => 'text',
                'group' => 'seo',
                'is_public' => true,
                'description' => 'Default meta description for pages',
            ],
            'default_meta_keywords' => [
                'value' => 'cms, laravel, filament, content management',
                'type' => 'string',
                'group' => 'seo',
                'is_public' => true,
                'description' => 'Default meta keywords for pages',
            ],
            'google_analytics_id' => [
                'value' => '',
                'type' => 'string',
                'group' => 'seo',
                'is_public' => true,
                'description' => 'Google Analytics tracking ID',
            ],
            'google_search_console_verification' => [
                'value' => '',
                'type' => 'string',
                'group' => 'seo',
                'is_public' => true,
                'description' => 'Google Search Console verification code',
            ],

            // HIGH PRIORITY - Media Settings
            'max_file_size' => [
                'value' => 2048,
                'type' => 'integer',
                'group' => 'media',
                'is_public' => false,
                'description' => 'Maximum file size in KB',
            ],
            'max_image_size' => [
                'value' => 2048,
                'type' => 'integer',
                'group' => 'media',
                'is_public' => false,
                'description' => 'Maximum image size in KB',
            ],
            'allowed_image_types' => [
                'value' => 'jpg,jpeg,png,gif,webp',
                'type' => 'string',
                'group' => 'media',
                'is_public' => false,
                'description' => 'Allowed image file types',
            ],
            'image_quality' => [
                'value' => 85,
                'type' => 'integer',
                'group' => 'media',
                'is_public' => false,
                'description' => 'Image compression quality (1-100)',
            ],
            'thumbnail_width' => [
                'value' => 368,
                'type' => 'integer',
                'group' => 'media',
                'is_public' => false,
                'description' => 'Thumbnail width in pixels',
            ],
            'thumbnail_height' => [
                'value' => 232,
                'type' => 'integer',
                'group' => 'media',
                'is_public' => false,
                'description' => 'Thumbnail height in pixels',
            ],

            // HIGH PRIORITY - Content Settings
            'default_post_status' => [
                'value' => 'draft',
                'type' => 'string',
                'group' => 'content',
                'is_public' => false,
                'description' => 'Default status for new posts',
            ],
            'posts_per_page' => [
                'value' => 10,
                'type' => 'integer',
                'group' => 'content',
                'is_public' => true,
                'description' => 'Number of posts per page',
            ],
            'auto_generate_excerpts' => [
                'value' => true,
                'type' => 'boolean',
                'group' => 'content',
                'is_public' => false,
                'description' => 'Automatically generate excerpts from content',
            ],
            'excerpt_length' => [
                'value' => 160,
                'type' => 'integer',
                'group' => 'content',
                'is_public' => false,
                'description' => 'Length of auto-generated excerpts',
            ],
            'default_category_color' => [
                'value' => '#3B82F6',
                'type' => 'string',
                'group' => 'content',
                'is_public' => false,
                'description' => 'Default color for new categories',
            ],
            'default_tag_color' => [
                'value' => '#6B7280',
                'type' => 'string',
                'group' => 'content',
                'is_public' => false,
                'description' => 'Default color for new tags',
            ],

            // MEDIUM PRIORITY - Email Settings
            'email_from_name' => [
                'value' => 'CMS-TRS',
                'type' => 'string',
                'group' => 'email',
                'is_public' => false,
                'description' => 'Default sender name for emails',
            ],
            'email_from_address' => [
                'value' => 'noreply@yourdomain.com',
                'type' => 'string',
                'group' => 'email',
                'is_public' => false,
                'description' => 'Default sender email address',
            ],
            'email_reply_to' => [
                'value' => 'support@yourdomain.com',
                'type' => 'string',
                'group' => 'email',
                'is_public' => false,
                'description' => 'Reply-to email address',
            ],

            // MEDIUM PRIORITY - User/Authentication Settings
            'allow_registration' => [
                'value' => false,
                'type' => 'boolean',
                'group' => 'user',
                'is_public' => false,
                'description' => 'Allow new user registration',
            ],
            'require_email_verification' => [
                'value' => true,
                'type' => 'boolean',
                'group' => 'user',
                'is_public' => false,
                'description' => 'Require email verification for new users',
            ],
            'min_password_length' => [
                'value' => 8,
                'type' => 'integer',
                'group' => 'user',
                'is_public' => false,
                'description' => 'Minimum password length',
            ],
            'session_lifetime' => [
                'value' => 120,
                'type' => 'integer',
                'group' => 'user',
                'is_public' => false,
                'description' => 'Session lifetime in minutes',
            ],

            // MEDIUM PRIORITY - Admin Panel Settings
            'admin_panel_theme' => [
                'value' => 'light',
                'type' => 'string',
                'group' => 'admin',
                'is_public' => false,
                'description' => 'Admin panel theme (light, dark, auto)',
            ],
            'show_stats_widget' => [
                'value' => true,
                'type' => 'boolean',
                'group' => 'admin',
                'is_public' => false,
                'description' => 'Show statistics widget on dashboard',
            ],
            'show_recent_posts_widget' => [
                'value' => true,
                'type' => 'boolean',
                'group' => 'admin',
                'is_public' => false,
                'description' => 'Show recent posts widget on dashboard',
            ],
        ];

        foreach ($settings as $key => $config) {
            Setting::updateOrCreate(
                ['key' => $key],
                [
                    'value' => $config['value'],
                    'type' => $config['type'],
                    'group_name' => $config['group'],
                    'is_public' => $config['is_public'],
                    'description' => $config['description'],
                ]
            );
        }
    }
}
