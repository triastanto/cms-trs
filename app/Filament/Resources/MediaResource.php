<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MediaResource\Pages;
use BackedEnum;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Artisan;
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

                Section::make('Image Details')
                    ->description('Custom properties and metadata for this media item')
                    ->icon('heroicon-o-tag')
                    ->schema([
                        TextInput::make('custom_properties.title')
                            ->label('Image Title')
                            ->placeholder('Enter a title for this image')
                            ->maxLength(255)
                            ->helperText('This title will be displayed in galleries and media listings'),

                        Textarea::make('custom_properties.alt_text')
                            ->label('Alt Text')
                            ->placeholder('Enter alternative text for accessibility')
                            ->rows(2)
                            ->maxLength(500)
                            ->helperText('Important for SEO and accessibility'),

                        Textarea::make('custom_properties.caption')
                            ->label('Caption')
                            ->placeholder('Enter a caption for this image')
                            ->rows(2)
                            ->maxLength(500)
                            ->helperText('Optional caption that can be displayed with the image'),
                    ])
                    ->columns(1)
                    ->visible(fn ($record) => str_starts_with($record->mime_type ?? '', 'image/')),

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
                    ->description('Preview of the media file and all conversions')
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

                                // Get all generated conversions
                                $generatedConversions = $record->generated_conversions ?? [];
                                $conversions = [];

                                foreach ($generatedConversions as $name => $status) {
                                    if ($status) {
                                        $conversions[] = $name;
                                    }
                                }

                                // Build HTML for original + conversions
                                $html = '<div class="space-y-6">';

                                // Original image
                                $html .= '<div class="space-y-2">';
                                $html .= '<h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Original</h3>';
                                $html .= '<div class="flex justify-center p-4 bg-gray-50 dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700">';
                                $html .= '<img src="'.$record->getUrl().'" alt="'.$record->file_name.'" class="max-w-full max-h-96 rounded-lg shadow-lg" />';
                                $html .= '</div>';

                                // Get dimensions
                                try {
                                    $path = $record->getPath();
                                    if (file_exists($path)) {
                                        $imageSize = getimagesize($path);
                                        if ($imageSize !== false) {
                                            $html .= '<p class="text-xs text-center text-gray-500 dark:text-gray-400">';
                                            $html .= $imageSize[0].' × '.$imageSize[1].' px';
                                            $html .= '</p>';
                                        }
                                    }
                                } catch (\Exception $e) {
                                }

                                $html .= '</div>';

                                // Show conversions if any
                                if (! empty($conversions)) {
                                    $html .= '<div class="grid grid-cols-1 md:grid-cols-2 gap-4">';

                                    foreach ($conversions as $conversion) {
                                        $html .= '<div class="space-y-2">';
                                        $html .= '<h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 capitalize">'.ucfirst($conversion).'</h3>';
                                        $html .= '<div class="flex justify-center p-4 bg-gray-50 dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700">';
                                        $html .= '<img src="'.$record->getUrl($conversion).'" alt="'.$record->file_name.' ('.$conversion.')" class="max-w-full h-auto rounded-lg shadow" />';
                                        $html .= '</div>';

                                        // Get conversion dimensions
                                        try {
                                            $conversionPath = $record->getPath($conversion);
                                            if (file_exists($conversionPath)) {
                                                $conversionSize = getimagesize($conversionPath);
                                                if ($conversionSize !== false) {
                                                    $html .= '<p class="text-xs text-center text-gray-500 dark:text-gray-400">';
                                                    $html .= $conversionSize[0].' × '.$conversionSize[1].' px';
                                                    $html .= '</p>';
                                                }
                                            }
                                        } catch (\Exception $e) {
                                        }

                                        $html .= '</div>';
                                    }

                                    $html .= '</div>';
                                }

                                $html .= '</div>';

                                return new \Illuminate\Support\HtmlString($html);
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

                        \Filament\Forms\Components\Placeholder::make('image_title')
                            ->label('Image Title')
                            ->content(fn ($record) => $record->getCustomProperty('title') ?? 'No title set')
                            ->visible(fn ($record) => str_starts_with($record->mime_type, 'image/')),

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
                                ->content(fn () => match (get_class($relatedModel)) {
                                    'App\Models\Post' => ucfirst($relatedModel->status ?? 'draft'),
                                    'App\Models\Category', 'App\Models\Tag' => $relatedModel->is_active ? 'Active' : 'Inactive',
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
                    ->label('Preview')
                    ->getStateUsing(function (Media $record): ?string {
                        // Only show image for image types
                        if (! str_starts_with($record->mime_type, 'image/')) {
                            return null;
                        }

                        // Try to get thumb conversion, fallback to original
                        try {
                            return $record->hasGeneratedConversion('thumb')
                                ? $record->getUrl('thumb')
                                : $record->getUrl();
                        } catch (\Exception $e) {
                            return $record->getUrl();
                        }
                    })
                    ->circular()
                    ->size(50)
                    ->defaultImageUrl(url('data:image/svg+xml,'.rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>'))),

                TextColumn::make('file_name')
                    ->label('File Name')
                    ->searchable(['file_name', 'custom_properties->title', 'custom_properties->alt_text'])
                    ->sortable()
                    ->limit(30)
                    ->description(fn (Media $record): ?string => $record->getCustomProperty('title'))
                    ->tooltip(fn (Media $record): string => $record->file_name),

                TextColumn::make('mime_type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match (true) {
                        str_starts_with($state, 'image/') => 'success',
                        str_starts_with($state, 'video/') => 'info',
                        str_starts_with($state, 'application/pdf') => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => strtoupper(str_replace(['image/', 'video/', 'application/'], '', $state)))
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('collection_name')
                    ->label('Collection')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'post_images' => 'primary',
                        'featured_image' => 'success',
                        'gallery' => 'info',
                        'thumbnail' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucwords(str_replace('_', ' ', $state)))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('is_featured')
                    ->label('Featured')
                    ->badge()
                    ->getStateUsing(fn (Media $record): bool => $record->getCustomProperty('is_featured') === true)
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Yes' : 'No')
                    ->color(fn (bool $state): string => $state ? 'success' : 'gray')
                    ->toggleable(),

                TextColumn::make('model.title')
                    ->label('Used In')
                    ->getStateUsing(function (Media $record) {
                        $model = $record->model;
                        if (! $model) {
                            return 'N/A';
                        }

                        $type = match (get_class($model)) {
                            'App\Models\Post' => 'Post',
                            'App\Models\Category' => 'Category',
                            'App\Models\Tag' => 'Tag',
                            default => 'Unknown'
                        };

                        $name = $model->title ?? $model->name ?? (string) $model->getKey();

                        return $type.': '.$name;
                    })
                    ->limit(30)
                    ->searchable(['posts.title', 'categories.name', 'tags.name'])
                    ->toggleable(),

                TextColumn::make('size')
                    ->label('Size')
                    ->formatStateUsing(fn (int $state): string => $state > 1048576
                            ? number_format($state / 1048576, 2).' MB'
                            : number_format($state / 1024, 2).' KB'
                    )
                    ->sortable()
                    ->alignEnd()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Uploaded')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
            ])
            ->filters([
                SelectFilter::make('collection_name')
                    ->label('Collection')
                    ->options([
                        'post_images' => 'Post Images',
                        'featured_image' => 'Featured Image (Legacy)',
                        'gallery' => 'Gallery (Legacy)',
                        'thumbnail' => 'Thumbnail',
                    ])
                    ->multiple(),

                SelectFilter::make('model_type')
                    ->label('Related To')
                    ->options([
                        'App\Models\Post' => 'Posts',
                        'App\Models\Category' => 'Categories',
                        'App\Models\Tag' => 'Tags',
                    ])
                    ->multiple(),

                SelectFilter::make('mime_type')
                    ->label('File Type')
                    ->options([
                        'image/jpeg' => 'JPEG',
                        'image/png' => 'PNG',
                        'image/gif' => 'GIF',
                        'image/webp' => 'WebP',
                        'image/svg+xml' => 'SVG',
                        'application/pdf' => 'PDF',
                    ])
                    ->multiple(),

                Filter::make('has_title')
                    ->label('Has Title')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('custom_properties->title'))
                    ->toggle(),

                Filter::make('is_featured')
                    ->label('Featured Images')
                    ->query(fn (Builder $query): Builder => $query->where('custom_properties->is_featured', true))
                    ->toggle(),

                Filter::make('created_at')
                    ->form([
                        DatePicker::make('uploaded_from')
                            ->label('Uploaded from'),
                        DatePicker::make('uploaded_until')
                            ->label('Uploaded until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['uploaded_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['uploaded_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),

                Filter::make('size')
                    ->form([
                        TextInput::make('min_size')
                            ->label('Min Size (KB)')
                            ->numeric(),
                        TextInput::make('max_size')
                            ->label('Max Size (KB)')
                            ->numeric(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['min_size'],
                                fn (Builder $query, $size): Builder => $query->where('size', '>=', $size * 1024),
                            )
                            ->when(
                                $data['max_size'],
                                fn (Builder $query, $size): Builder => $query->where('size', '<=', $size * 1024),
                            );
                    }),
            ])
            ->actions([])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('regenerate_conversions')
                        ->label('Regenerate Conversions')
                        ->icon('heroicon-o-arrow-path')
                        ->color('warning')
                        ->action(function (Collection $records) {
                            $records->each(function (Media $record) {
                                if (str_starts_with($record->mime_type, 'image/')) {
                                    try {
                                        Artisan::call('media-library:regenerate', [
                                            'modelType' => get_class($record->model),
                                            '--ids' => [$record->id],
                                        ]);
                                    } catch (\Exception $e) {
                                        // Continue with other records
                                    }
                                }
                            });

                            Notification::make()
                                ->title('Conversions regenerated')
                                ->body($records->count().' media items processed')
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('update_collection')
                        ->label('Move to Collection')
                        ->icon('heroicon-o-folder')
                        ->color('info')
                        ->form([
                            Select::make('collection_name')
                                ->label('Collection')
                                ->options([
                                    'post_images' => 'Post Images',
                                    'gallery' => 'Gallery',
                                    'thumbnail' => 'Thumbnail',
                                ])
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            $records->each(function (Media $record) use ($data) {
                                $record->collection_name = $data['collection_name'];
                                $record->save();
                            });

                            Notification::make()
                                ->title('Collection updated')
                                ->body($records->count().' media items moved to '.$data['collection_name'])
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('set_metadata')
                        ->label('Set Metadata')
                        ->icon('heroicon-o-tag')
                        ->color('primary')
                        ->form([
                            TextInput::make('title')
                                ->label('Title')
                                ->maxLength(255),
                            Textarea::make('alt_text')
                                ->label('Alt Text')
                                ->rows(2)
                                ->maxLength(500),
                            Textarea::make('caption')
                                ->label('Caption')
                                ->rows(2)
                                ->maxLength(500),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            $records->each(function (Media $record) use ($data) {
                                if (filled($data['title'])) {
                                    $record->setCustomProperty('title', $data['title']);
                                }
                                if (filled($data['alt_text'])) {
                                    $record->setCustomProperty('alt_text', $data['alt_text']);
                                }
                                if (filled($data['caption'])) {
                                    $record->setCustomProperty('caption', $data['caption']);
                                }
                                $record->save();
                            });

                            Notification::make()
                                ->title('Metadata updated')
                                ->body($records->count().' media items updated')
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalDescription('Are you sure you want to delete these media items? This action cannot be undone.'),
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
}
