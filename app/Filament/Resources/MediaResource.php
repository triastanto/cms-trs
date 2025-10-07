<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MediaResource\Pages;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
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
            ->columns(2)
            ->components([
                Section::make('Media Information')
                    ->schema([
                        TextInput::make('file_name')
                            ->label('File Name')
                            ->disabled(),

                        TextInput::make('collection_name')
                            ->label('Collection')
                            ->disabled(),

                        TextInput::make('mime_type')
                            ->label('MIME Type')
                            ->disabled(),

                        TextInput::make('size')
                            ->label('File Size')
                            ->formatStateUsing(fn (int $state): string => number_format($state / 1024, 2).' KB')
                            ->disabled(),

                        TextInput::make('disk')
                            ->label('Storage Disk')
                            ->disabled(),
                    ])
                    ->columns(2),

                Section::make('Related Information')
                    ->schema([
                        TextInput::make('model_type')
                            ->label('Model Type')
                            ->formatStateUsing(fn (string $state): string => class_basename($state))
                            ->disabled(),

                        TextInput::make('model_id')
                            ->label('Model ID')
                            ->disabled(),

                        TextInput::make('created_at')
                            ->label('Uploaded At')
                            ->formatStateUsing(function ($state): string {
                                if (is_string($state)) {
                                    return $state;
                                }

                                return $state?->format('Y-m-d H:i:s') ?? 'N/A';
                            })
                            ->disabled(),

                        TextInput::make('updated_at')
                            ->label('Last Modified')
                            ->formatStateUsing(function ($state): string {
                                if (is_string($state)) {
                                    return $state;
                                }

                                return $state?->format('Y-m-d H:i:s') ?? 'N/A';
                            })
                            ->disabled(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Media Preview')
                    ->description('Preview of the media file')
                    ->icon('heroicon-o-photo')
                    ->schema([
                        \Filament\Forms\Components\Placeholder::make('preview')
                            ->hiddenLabel()
                            ->content(function ($record) {
                                if (! str_starts_with($record->mime_type, 'image/')) {
                                    return new \Illuminate\Support\HtmlString(
                                        '<div class="flex items-center justify-center p-12 bg-gray-50 dark:bg-gray-900 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-700">'.
                                        '<div class="text-center">'.
                                        '<svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">'.
                                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>'.
                                        '</svg>'.
                                        '<p class="mt-2 text-sm text-gray-600 dark:text-gray-400">'.strtoupper(pathinfo($record->file_name, PATHINFO_EXTENSION)).' File</p>'.
                                        '<p class="text-xs text-gray-500 dark:text-gray-500">'.$record->mime_type.'</p>'.
                                        '</div>'.
                                        '</div>'
                                    );
                                }

                                return new \Illuminate\Support\HtmlString(
                                    '<div class="flex justify-center p-6 bg-gray-50 dark:bg-gray-900 rounded-lg">'.
                                    '<img src="'.$record->getUrl().'" alt="'.$record->file_name.'" class="max-w-full max-h-96 rounded-lg shadow-lg" />'.
                                    '</div>'
                                );
                            }),
                    ])
                    ->columnSpanFull()
                    ->collapsible(),

                Section::make('Media Information')
                    ->description('File details and metadata')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        \Filament\Forms\Components\Placeholder::make('file_name')
                            ->label('File Name')
                            ->content(fn ($record) => $record->file_name),

                        \Filament\Forms\Components\Placeholder::make('collection_name')
                            ->label('Collection')
                            ->content(fn ($record) => ucfirst(str_replace('_', ' ', $record->collection_name))),

                        \Filament\Forms\Components\Placeholder::make('mime_type')
                            ->label('MIME Type')
                            ->content(fn ($record) => $record->mime_type),

                        \Filament\Forms\Components\Placeholder::make('size')
                            ->label('File Size')
                            ->content(function ($record) {
                                $sizeInKb = $record->size / 1024;
                                if ($sizeInKb > 1024) {
                                    return number_format($sizeInKb / 1024, 2).' MB';
                                }

                                return number_format($sizeInKb, 2).' KB';
                            }),

                        \Filament\Forms\Components\Placeholder::make('dimensions')
                            ->label('Dimensions')
                            ->content(function ($record) {
                                if (! str_starts_with($record->mime_type, 'image/')) {
                                    return '—';
                                }

                                try {
                                    $path = $record->getPath();
                                    if (file_exists($path)) {
                                        $imageSize = getimagesize($path);
                                        if ($imageSize !== false) {
                                            return $imageSize[0].' × '.$imageSize[1].' px';
                                        }
                                    }
                                } catch (\Exception $e) {
                                    // Silently fail and show fallback
                                }

                                return '—';
                            })
                            ->visible(fn ($record) => str_starts_with($record->mime_type, 'image/')),

                        \Filament\Forms\Components\Placeholder::make('disk')
                            ->label('Storage Disk')
                            ->content(fn ($record) => $record->disk),

                        \Filament\Forms\Components\Placeholder::make('created_at')
                            ->label('Uploaded')
                            ->content(fn ($record) => $record->created_at?->diffForHumans() ?? 'N/A'),

                        \Filament\Forms\Components\Placeholder::make('updated_at')
                            ->label('Last Modified')
                            ->content(fn ($record) => $record->updated_at?->diffForHumans() ?? 'N/A'),
                    ])
                    ->columns(2)
                    ->columnSpanFull()
                    ->collapsible(),

                Section::make('Related Record')
                    ->description('Associated content')
                    ->icon('heroicon-o-link')
                    ->schema(function ($record) {
                        $relatedModel = $record->model;

                        if (! $relatedModel) {
                            return [
                                \Filament\Forms\Components\Placeholder::make('no_relation')
                                    ->hiddenLabel()
                                    ->content(new \Illuminate\Support\HtmlString(
                                        '<div class="text-center py-4 text-gray-500 dark:text-gray-400">'.
                                        '<svg class="mx-auto h-8 w-8 mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">'.
                                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>'.
                                        '</svg>'.
                                        '<p class="text-sm">No associated record</p>'.
                                        '</div>'
                                    ))
                                    ->columnSpanFull(),
                            ];
                        }

                        // Build resource URL
                        $resourceUrl = null;
                        $modelClass = get_class($relatedModel);
                        $resourceMap = [
                            'App\Models\Post' => \App\Filament\Resources\PostResource::class,
                            'App\Models\Category' => \App\Filament\Resources\CategoryResource::class,
                            'App\Models\Tag' => \App\Filament\Resources\TagResource::class,
                        ];

                        if (isset($resourceMap[$modelClass])) {
                            $resourceClass = $resourceMap[$modelClass];
                            $resourceUrl = $resourceClass::getUrl('edit', ['record' => $relatedModel]);
                        }

                        return [
                            \Filament\Forms\Components\Placeholder::make('model_info')
                                ->label('Type')
                                ->content(class_basename($relatedModel)),

                            \Filament\Forms\Components\Placeholder::make('model_title')
                                ->label('Title')
                                ->content(function () use ($relatedModel, $resourceUrl) {
                                    $title = $relatedModel->title ?? $relatedModel->name ?? '—';

                                    if ($resourceUrl) {
                                        return new \Illuminate\Support\HtmlString(
                                            '<a href="'.$resourceUrl.'" class="text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 underline decoration-dotted">'.
                                            $title.
                                            '</a>'
                                        );
                                    }

                                    return $title;
                                }),

                            \Filament\Forms\Components\Placeholder::make('model_id')
                                ->label('ID')
                                ->content('#'.$relatedModel->getKey()),

                            \Filament\Forms\Components\Placeholder::make('model_status')
                                ->label('Status')
                                ->content(fn () => match (true) {
                                    isset($relatedModel->is_published) => $relatedModel->is_published ? 'Published' : 'Draft',
                                    isset($relatedModel->is_active) => $relatedModel->is_active ? 'Active' : 'Inactive',
                                    default => '—'
                                }),
                        ];
                    })
                    ->columns(2)
                    ->columnSpanFull()
                    ->collapsible(),
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
                    ->searchable(['posts.title', 'categories.name']),

                TextColumn::make('size')
                    ->label('File Size')
                    ->formatStateUsing(fn (int $state): string => number_format($state / 1024, 2).' KB')
                    ->sortable()
                    ->alignEnd(),

                TextColumn::make('created_at')
                    ->label('Uploaded')
                    ->dateTime()
                    ->sortable()
                    ->alignEnd(),
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
            ->actions([])
            ->bulkActions([
                BulkActionGroup::make([
                    // DeleteBulkAction removed - deletion disabled via canDelete() method
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
            'view' => Pages\ViewMedia::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }
}
