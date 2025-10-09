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
            Actions\EditAction::make()
                ->label('Edit Metadata')
                ->icon('heroicon-o-pencil')
                ->color('primary'),

            Actions\Action::make('download')
                ->label('Download')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function (Media $record) {
                    return response()->download($record->getPath(), $record->file_name);
                }),

            Actions\Action::make('view_original')
                ->label('View Original')
                ->icon('heroicon-o-eye')
                ->color('gray')
                ->url(fn (Media $record): string => $record->getUrl())
                ->openUrlInNewTab(),

            Actions\Action::make('regenerate_conversions')
                ->label('Regenerate Conversions')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->action(function (Media $record) {
                    try {
                        \Illuminate\Support\Facades\Artisan::call('media-library:regenerate', [
                            'modelType' => get_class($record->model),
                            '--ids' => [$record->id],
                        ]);

                        \Filament\Notifications\Notification::make()
                            ->title('Conversions regenerated successfully')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        \Filament\Notifications\Notification::make()
                            ->title('Failed to regenerate conversions')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                })
                ->requiresConfirmation()
                ->visible(fn (Media $record) => str_starts_with($record->mime_type, 'image/')),

            Actions\DeleteAction::make()
                ->requiresConfirmation()
                ->successRedirectUrl(route('filament.admin.resources.media.index')),
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
