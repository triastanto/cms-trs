<?php

namespace App\Filament\Widgets;

use App\Helpers\SettingsHelper;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SettingsUsageWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        $settings = SettingsHelper::all();
        $publicSettings = SettingsHelper::public();

        return [
            Stat::make('Total Settings', count($settings))
                ->description('All settings in the system')
                ->descriptionIcon('heroicon-m-cog-6-tooth')
                ->color('primary'),

            Stat::make('Public Settings', count($publicSettings))
                ->description('Settings available via API')
                ->descriptionIcon('heroicon-m-globe-alt')
                ->color('success'),

            Stat::make('Site Name', SettingsHelper::siteName())
                ->description('Current site name')
                ->descriptionIcon('heroicon-m-building-office')
                ->color('info'),

            Stat::make('Posts Per Page', SettingsHelper::postsPerPage())
                ->description('Content pagination setting')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('warning'),
        ];
    }
}
