<?php

use App\Helpers\SettingsHelper;

if (! function_exists('setting')) {
    /**
     * Get a setting value
     *
     * @param  mixed  $default
     * @return mixed
     */
    function setting(string $key, $default = null)
    {
        return SettingsHelper::get($key, $default);
    }
}

if (! function_exists('settings')) {
    /**
     * Get all settings or settings by group
     */
    function settings(?string $group = null): array
    {
        if ($group) {
            return SettingsHelper::group($group);
        }

        return SettingsHelper::all();
    }
}

if (! function_exists('public_settings')) {
    /**
     * Get public settings
     */
    function public_settings(): array
    {
        return SettingsHelper::public();
    }
}
