<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SettingsService;
use Illuminate\Http\JsonResponse;

class SettingsController extends Controller
{
    public function __construct(
        private SettingsService $settingsService
    ) {}

    /**
     * Get all public settings
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->settingsService->getPublicSettings(),
        ]);
    }

    /**
     * Get a specific public setting
     */
    public function show(string $key): JsonResponse
    {
        $value = $this->settingsService->get($key);

        if ($value === null) {
            return response()->json([
                'success' => false,
                'message' => 'Setting not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'key' => $key,
                'value' => $value,
            ],
        ]);
    }

    /**
     * Get settings by group
     */
    public function group(string $group): JsonResponse
    {
        $settings = $this->settingsService->getSettingsByGroup($group);

        return response()->json([
            'success' => true,
            'data' => $settings,
        ]);
    }
}
