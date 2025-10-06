<?php

namespace App\View\Composers;

use App\Services\SettingsService;
use Illuminate\View\View;

class SettingsComposer
{
    protected SettingsService $settingsService;

    public function __construct(SettingsService $settingsService)
    {
        $this->settingsService = $settingsService;
    }

    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        $view->with('settings', $this->settingsService->getAllSettings());
        $view->with('publicSettings', $this->settingsService->getPublicSettings());
    }
}
