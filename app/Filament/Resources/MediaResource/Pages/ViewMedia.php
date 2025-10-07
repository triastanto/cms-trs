<?php

namespace App\Filament\Resources\MediaResource\Pages;

use App\Filament\Resources\MediaResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ViewMedia extends ViewRecord
{
    protected static string $resource = MediaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('download')
                ->label('Download')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(function (Media $record) {
                    return response()->download($record->getPath(), $record->file_name);
                }),
            Actions\Action::make('view_original')
                ->label('View Original')
                ->icon('heroicon-o-eye')
                ->url(fn (Media $record): string => $record->getUrl())
                ->openUrlInNewTab(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // You can add widgets here if needed
        ];
    }

    public function getTitle(): string
    {
        /** @var Media $record */
        $record = $this->getRecord();
        return $record->file_name;
    }

    protected function getFooterWidgets(): array
    {
        return [
            // You can add footer widgets here if needed
        ];
    }
}
