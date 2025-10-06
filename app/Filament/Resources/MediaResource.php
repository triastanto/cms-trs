<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MediaResource\Pages;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use UnitEnum;

class MediaResource extends Resource
{
    protected static ?string $model = Media::class;

    protected static BackedEnum|string|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static ?string $recordTitleAttribute = 'file_name';

    protected static string|UnitEnum|null $navigationGroup = 'Content Management';

    protected static ?int $navigationSort = 10;

    protected static ?string $navigationLabel = 'Media';

    protected static ?string $pluralLabel = 'Media';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['model']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                // Read-only resource, no form needed
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('preview')
                    ->label('Image')
                    ->getStateUsing(fn (Media $record): string => $record->getUrl())
                    ->circular()
                    ->size(40),

                TextColumn::make('file_name')
                    ->label('File Name')
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->tooltip(fn (Media $record): string => $record->file_name),

                TextColumn::make('collection_name')
                    ->label('Collection')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'featured_image' => 'success',
                        'gallery' => 'info',
                        'thumbnail' => 'warning',
                        default => 'gray',
                    })
                    ->searchable()
                    ->sortable(),

                TextColumn::make('model_type')
                    ->label('Attached To')
                    ->formatStateUsing(fn (string $state): string => class_basename($state))
                    ->badge()
                    ->color('primary')
                    ->searchable(),

                TextColumn::make('model.title')
                    ->label('Record')
                    ->getStateUsing(function (Media $record) {
                        $model = $record->model;
                        if (! $model) {
                            return 'N/A';
                        }

                        /** @var \App\Models\Post|\App\Models\Category $model */
                        return $model->title ?? $model->name ?? (string) $model->getKey();
                    })
                    ->limit(30)
                    ->searchable(['posts.title', 'categories.name'])
                    ->url(function (Media $record) {
                        $model = $record->model;
                        if (! $model) {
                            return null;
                        }

                        $resourceClass = match (class_basename($model)) {
                            'Post' => \App\Filament\Resources\PostResource::class,
                            'Category' => \App\Filament\Resources\CategoryResource::class,
                            default => null,
                        };

                        if ($resourceClass) {
                            return $resourceClass::getUrl('edit', ['record' => $model]);
                        }

                        return null;
                    }, shouldOpenInNewTab: true),

                TextColumn::make('mime_type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(function (string $state): string {
                        $parts = explode('/', $state);

                        return strtoupper($parts[0]);
                    })
                    ->color(function (string $state): string {
                        $parts = explode('/', $state);
                        $type = $parts[0];

                        return match ($type) {
                            'image' => 'success',
                            'video' => 'info',
                            'application' => 'warning',
                            default => 'gray',
                        };
                    })
                    ->toggleable(),

                TextColumn::make('size')
                    ->label('Size')
                    ->formatStateUsing(fn (int $state): string => number_format($state / 1024, 2).' KB')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Uploaded')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('collection_name')
                    ->label('Collection')
                    ->options([
                        'featured_image' => 'Featured Image',
                        'gallery' => 'Gallery',
                        'thumbnail' => 'Thumbnail',
                    ]),

                SelectFilter::make('model_type')
                    ->label('Model Type')
                    ->options([
                        'App\Models\Post' => 'Post',
                        'App\Models\Category' => 'Category',
                    ]),

                SelectFilter::make('mime_type')
                    ->label('File Type')
                    ->options([
                        'image/jpeg' => 'JPEG',
                        'image/png' => 'PNG',
                        'image/gif' => 'GIF',
                        'image/webp' => 'WebP',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (filled($data['value'])) {
                            return $query->where('mime_type', $data['value']);
                        }

                        return $query;
                    }),
            ])
            ->actions([
                Action::make('view')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->url(fn (Media $record): string => $record->getUrl())
                    ->openUrlInNewTab(),

                Action::make('download')
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function (Media $record) {
                        return response()->download($record->getPath(), $record->file_name);
                    }),

                Action::make('delete')
                    ->label('Delete')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn (Media $record) => $record->delete())
                    ->successNotificationTitle('Media deleted successfully'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotificationTitle('Media deleted successfully'),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('30s');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMedia::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
