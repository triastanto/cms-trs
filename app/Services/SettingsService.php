<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingsService
{
    protected string $cacheKey = 'settings';

    /**
     * Get cache TTL based on environment
     */
    protected function getCacheTtl(): int
    {
        return config('performance.cache.settings_ttl', 3600);
    }

    /**
     * Get a setting value by key
     */
    public function get(string $key, $default = null)
    {
        $settings = $this->getAllSettings();

        return $settings[$key] ?? $default;
    }

    /**
     * Set a setting value
     */
    public function set(string $key, $value, string $type = 'string', string $group = 'general', bool $isPublic = false, ?string $description = null): void
    {
        $setting = Setting::updateOrCreate(
            ['key' => $key],
            [
                'value' => $this->formatValue($value, $type),
                'type' => $type,
                'group_name' => $group,
                'is_public' => $isPublic,
                'description' => $description,
            ]
        );

        $this->clearCache();
    }

    /**
     * Get all settings as an array
     */
    public function getAllSettings(): array
    {
        return Cache::remember(
            $this->cacheKey,
            $this->getCacheTtl(),
            fn () => Setting::all()
                ->mapWithKeys(fn ($setting) => [$setting->key => $setting->getTypedValue()])
                ->toArray()
        );
    }

    /**
     * Get public settings only
     */
    public function getPublicSettings(): array
    {
        return Cache::remember(
            $this->cacheKey.'.public',
            $this->getCacheTtl(),
            fn () => Setting::public()
                ->get()
                ->mapWithKeys(fn ($setting) => [$setting->key => $setting->getTypedValue()])
                ->toArray()
        );
    }

    /**
     * Get settings by group
     */
    public function getSettingsByGroup(string $group): array
    {
        return Cache::remember(
            $this->cacheKey.".group.{$group}",
            $this->getCacheTtl(),
            fn () => Setting::byGroup($group)
                ->get()
                ->mapWithKeys(fn ($setting) => [$setting->key => $setting->getTypedValue()])
                ->toArray()
        );
    }

    /**
     * Check if a setting exists
     */
    public function has(string $key): bool
    {
        return $this->get($key) !== null;
    }

    /**
     * Delete a setting
     */
    public function delete(string $key): bool
    {
        $deleted = Setting::where('key', $key)->delete();
        if ($deleted) {
            $this->clearCache();
        }

        return $deleted > 0;
    }

    /**
     * Clear all settings cache
     */
    public function clearCache(): void
    {
        Cache::forget($this->cacheKey);
        Cache::forget($this->cacheKey.'.public');
        Cache::forget('critical_settings'); // Clear critical settings cache
        Cache::forget('maintenance_mode_status'); // Clear maintenance mode cache
        Cache::forget('maintenance_page_settings'); // Clear maintenance page settings cache

        // Clear group caches
        $groups = Setting::distinct()->pluck('group_name');
        foreach ($groups as $group) {
            Cache::forget($this->cacheKey.".group.{$group}");
        }
    }

    /**
     * Format value based on type
     */
    protected function formatValue($value, string $type): string
    {
        return match ($type) {
            'boolean' => $value ? '1' : '0',
            'json' => is_array($value) ? json_encode($value) : $value,
            default => (string) $value,
        };
    }

    /**
     * Bulk set settings
     */
    public function setMultiple(array $settings): void
    {
        foreach ($settings as $key => $config) {
            $this->set(
                $key,
                $config['value'] ?? null,
                $config['type'] ?? 'string',
                $config['group'] ?? 'general',
                $config['is_public'] ?? false,
                $config['description'] ?? null
            );
        }
    }
}
