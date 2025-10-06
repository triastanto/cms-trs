<?php

namespace App\Helpers;

use App\Services\SettingsService;

class SettingsHelper
{
    protected static ?SettingsService $service = null;

    /**
     * Get the settings service instance
     */
    protected static function getService(): SettingsService
    {
        if (self::$service === null) {
            self::$service = app(SettingsService::class);
        }

        return self::$service;
    }

    /**
     * Get a setting value
     */
    public static function get(string $key, $default = null)
    {
        return self::getService()->get($key, $default);
    }

    /**
     * Set a setting value
     */
    public static function set(string $key, $value, string $type = 'string', string $group = 'general', bool $isPublic = false, ?string $description = null): void
    {
        self::getService()->set($key, $value, $type, $group, $isPublic, $description);
    }

    /**
     * Get all settings
     */
    public static function all(): array
    {
        return self::getService()->getAllSettings();
    }

    /**
     * Get public settings
     */
    public static function public(): array
    {
        return self::getService()->getPublicSettings();
    }

    /**
     * Get settings by group
     */
    public static function group(string $group): array
    {
        return self::getService()->getSettingsByGroup($group);
    }

    /**
     * Check if a setting exists
     */
    public static function has(string $key): bool
    {
        return self::getService()->has($key);
    }

    /**
     * Delete a setting
     */
    public static function delete(string $key): bool
    {
        return self::getService()->delete($key);
    }

    /**
     * Clear settings cache
     */
    public static function clearCache(): void
    {
        self::getService()->clearCache();
    }

    // Convenience methods for common settings

    /**
     * Get site name
     */
    public static function siteName(): string
    {
        return self::get('site_name', 'CMS-TRS');
    }

    /**
     * Get site tagline
     */
    public static function siteTagline(): string
    {
        return self::get('site_tagline', 'Content Management System');
    }

    /**
     * Get site description
     */
    public static function siteDescription(): string
    {
        return self::get('site_description', 'A powerful CMS built with Laravel and Filament');
    }

    /**
     * Get site URL
     */
    public static function siteUrl(): string
    {
        return self::get('site_url', config('app.url'));
    }

    /**
     * Get default meta title
     */
    public static function metaTitle(): string
    {
        return self::get('default_meta_title', self::siteName());
    }

    /**
     * Get default meta description
     */
    public static function metaDescription(): string
    {
        return self::get('default_meta_description', self::siteDescription());
    }

    /**
     * Get posts per page
     */
    public static function postsPerPage(): int
    {
        return self::get('posts_per_page', 10);
    }

    /**
     * Get max file size in KB
     */
    public static function maxFileSize(): int
    {
        return self::get('max_file_size', 2048);
    }

    /**
     * Get max image size in KB
     */
    public static function maxImageSize(): int
    {
        return self::get('max_image_size', 2048);
    }

    /**
     * Get allowed image types
     */
    public static function allowedImageTypes(): string
    {
        return self::get('allowed_image_types', 'jpg,jpeg,png,gif,webp');
    }

    /**
     * Get image quality
     */
    public static function imageQuality(): int
    {
        return self::get('image_quality', 85);
    }

    /**
     * Get thumbnail dimensions
     */
    public static function thumbnailDimensions(): array
    {
        return [
            'width' => self::get('thumbnail_width', 368),
            'height' => self::get('thumbnail_height', 232),
        ];
    }

    /**
     * Get default post status
     */
    public static function defaultPostStatus(): string
    {
        return self::get('default_post_status', 'draft');
    }

    /**
     * Check if auto-generate excerpts is enabled
     */
    public static function autoGenerateExcerpts(): bool
    {
        return self::get('auto_generate_excerpts', true);
    }

    /**
     * Get excerpt length
     */
    public static function excerptLength(): int
    {
        return self::get('excerpt_length', 160);
    }

    /**
     * Get default category color
     */
    public static function defaultCategoryColor(): string
    {
        return self::get('default_category_color', '#3B82F6');
    }

    /**
     * Get default tag color
     */
    public static function defaultTagColor(): string
    {
        return self::get('default_tag_color', '#6B7280');
    }

    /**
     * Get email from name
     */
    public static function emailFromName(): string
    {
        return self::get('email_from_name', 'CMS-TRS');
    }

    /**
     * Get email from address
     */
    public static function emailFromAddress(): string
    {
        return self::get('email_from_address', 'noreply@yourdomain.com');
    }

    /**
     * Check if registration is allowed
     */
    public static function allowRegistration(): bool
    {
        return self::get('allow_registration', false);
    }

    /**
     * Check if email verification is required
     */
    public static function requireEmailVerification(): bool
    {
        return self::get('require_email_verification', true);
    }

    /**
     * Get minimum password length
     */
    public static function minPasswordLength(): int
    {
        return self::get('min_password_length', 8);
    }

    /**
     * Get session lifetime in minutes
     */
    public static function sessionLifetime(): int
    {
        return self::get('session_lifetime', 120);
    }

    /**
     * Get admin panel theme
     */
    public static function adminPanelTheme(): string
    {
        return self::get('admin_panel_theme', 'light');
    }

    /**
     * Check if maintenance mode is enabled
     */
    public static function maintenanceMode(): bool
    {
        return self::get('maintenance_mode', false);
    }

    /**
     * Get timezone
     */
    public static function timezone(): string
    {
        return self::get('timezone', 'UTC');
    }

    /**
     * Get locale
     */
    public static function locale(): string
    {
        return self::get('locale', 'en');
    }

    /**
     * Get SEO settings as array
     */
    public static function seoSettings(): array
    {
        return [
            'title' => self::get('default_meta_title', self::siteName()),
            'description' => self::get('default_meta_description', self::siteDescription()),
            'keywords' => self::get('default_meta_keywords', ''),
            'google_analytics_id' => self::get('google_analytics_id', ''),
            'google_search_console_verification' => self::get('google_search_console_verification', ''),
        ];
    }

    /**
     * Get site settings as array
     */
    public static function siteSettings(): array
    {
        return [
            'name' => self::siteName(),
            'tagline' => self::siteTagline(),
            'description' => self::siteDescription(),
            'url' => self::siteUrl(),
            'keywords' => self::get('site_keywords', ''),
        ];
    }

    /**
     * Get media settings as array
     */
    public static function mediaSettings(): array
    {
        return [
            'max_file_size' => self::maxFileSize(),
            'max_image_size' => self::maxImageSize(),
            'allowed_image_types' => self::allowedImageTypes(),
            'image_quality' => self::imageQuality(),
            'thumbnail_dimensions' => self::thumbnailDimensions(),
        ];
    }

    /**
     * Get content settings as array
     */
    public static function contentSettings(): array
    {
        return [
            'default_post_status' => self::defaultPostStatus(),
            'posts_per_page' => self::postsPerPage(),
            'auto_generate_excerpts' => self::autoGenerateExcerpts(),
            'excerpt_length' => self::excerptLength(),
            'default_category_color' => self::defaultCategoryColor(),
            'default_tag_color' => self::defaultTagColor(),
        ];
    }

    /**
     * Get user settings as array
     */
    public static function userSettings(): array
    {
        return [
            'allow_registration' => self::allowRegistration(),
            'require_email_verification' => self::requireEmailVerification(),
            'min_password_length' => self::minPasswordLength(),
            'session_lifetime' => self::sessionLifetime(),
        ];
    }

    /**
     * Get admin settings as array
     */
    public static function adminSettings(): array
    {
        return [
            'theme' => self::adminPanelTheme(),
            'show_stats_widget' => self::get('show_stats_widget', true),
            'show_recent_posts_widget' => self::get('show_recent_posts_widget', true),
        ];
    }
}
