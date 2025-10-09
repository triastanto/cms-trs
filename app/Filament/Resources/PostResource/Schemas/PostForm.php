<?php

namespace App\Filament\Resources\PostResource\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make('Post Details')
                    ->schema([
                        TextInput::make('id')
                            ->label('Post ID')
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpanFull(),

                        TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->columnSpanFull()
                            ->afterStateUpdated(function (string $operation, $state, mixed $set) {
                                if ($operation !== 'create') {
                                    return;
                                }
                                $set('slug', \Illuminate\Support\Str::slug($state));
                            }),

                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(\App\Models\Post::class, 'slug', ignoreRecord: true)
                            ->rules(['alpha_dash'])
                            ->columnSpanFull(),

                        Select::make('status')
                            ->options(\App\Models\Post::getStatusOptions())
                            ->default(fn () => setting('default_post_status', 'draft'))
                            ->required(),

                        DateTimePicker::make('published_at')
                            ->label('Publish Date')
                            ->nullable(),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->columnSpanFull(),

                Section::make('Author')
                    ->schema([
                        Select::make('user_id')
                            ->label('Author')
                            ->relationship('user', 'name')
                            ->required()
                            ->default(auth()->id()),
                    ])
                    ->collapsible()
                    ->columnSpan(1),

                Section::make('Categorization')
                    ->schema([
                        Select::make('category_id')
                            ->label('Category')
                            ->relationship('category', 'name', fn ($query) => $query->where('is_active', true))
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        Select::make('tags')
                            ->label('Tags')
                            ->relationship('tags', 'name', fn ($query) => $query->where('is_active', true))
                            ->multiple()
                            ->searchable()
                            ->preload(),
                    ])
                    ->collapsible()
                    ->columnSpan(1),

                Section::make('Content')
                    ->schema([
                        RichEditor::make('content')
                            ->required()
                            ->columnSpanFull()
                            ->toolbarButtons([
                                'attachFiles',
                                'blockquote',
                                'bold',
                                'bulletList',
                                'codeBlock',
                                'h2',
                                'h3',
                                'italic',
                                'link',
                                'orderedList',
                                'redo',
                                'strike',
                                'underline',
                                'undo',
                            ]),

                        Textarea::make('excerpt')
                            ->label('Excerpt')
                            ->helperText('Brief description of the post. If left empty, it will be generated from content.')
                            ->maxLength(500)
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->columnSpanFull(),

                Section::make('Post Images')
                    ->description('Upload and manage images for this post. Set one as featured.')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('post_images')
                            ->label('Images')
                            ->collection('post_images')
                            ->image()
                            ->multiple()
                            ->reorderable()
                            ->helperText('Upload images for this post. You can set metadata for each image below after saving.')
                            ->columnSpanFull(),

                        Placeholder::make('image_metadata_section')
                            ->label('')
                            ->content(function ($record) {
                                if (! $record || ! $record->exists) {
                                    return new HtmlString(
                                        '<div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">'.
                                        '<p class="text-sm text-blue-700 dark:text-blue-300">Save this post first to set image titles and metadata.</p>'.
                                        '</div>'
                                    );
                                }

                                $media = $record->getMedia('post_images');
                                if ($media->isEmpty()) {
                                    return new HtmlString(
                                        '<div class="bg-gray-50 dark:bg-gray-900/20 border border-gray-200 dark:border-gray-800 rounded-lg p-4">'.
                                        '<p class="text-sm text-gray-700 dark:text-gray-300">No images uploaded yet.</p>'.
                                        '</div>'
                                    );
                                }

                                return new HtmlString('');
                            })
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->columnSpanFull(),

                Section::make('Image Details')
                    ->description('Set titles, alt text, and captions for your images.')
                    ->schema(function ($record) {
                        if (! $record || ! $record->exists) {
                            return [];
                        }

                        $media = $record->getMedia('post_images');
                        if ($media->isEmpty()) {
                            return [];
                        }

                        $fields = [];
                        foreach ($media as $index => $mediaItem) {
                            $isFeatured = $mediaItem->getCustomProperty('is_featured') === true;
                            $mediaId = $mediaItem->id;

                            $fields[] = Section::make('Image '.($index + 1))
                                ->description($mediaItem->file_name)
                                ->schema([
                                    Placeholder::make("preview_{$mediaId}")
                                        ->label('Preview')
                                        ->content(function () use ($mediaItem, $isFeatured) {
                                            // Try to get thumb conversion, fallback to original if not available
                                            try {
                                                $url = $mediaItem->hasGeneratedConversion('thumb')
                                                    ? $mediaItem->getUrl('thumb')
                                                    : $mediaItem->getUrl();
                                            } catch (\Exception $e) {
                                                $url = $mediaItem->getUrl();
                                            }

                                            $badge = $isFeatured
                                                ? '<span class="ml-2 inline-flex items-center px-2 py-1 text-xs font-medium text-green-700 bg-green-100 rounded-full dark:bg-green-900 dark:text-green-300">Featured</span>'
                                                : '';

                                            return new HtmlString(
                                                '<div class="flex items-center gap-3">'.
                                                '<img src="'.$url.'" alt="Preview" class="w-24 h-24 object-cover rounded-lg border-2 border-gray-200 dark:border-gray-700" />'.
                                                $badge.
                                                '</div>'
                                            );
                                        })
                                        ->columnSpanFull(),

                                    TextInput::make("media_title_{$mediaId}")
                                        ->label('Title')
                                        ->placeholder('Enter image title')
                                        ->default($mediaItem->getCustomProperty('title'))
                                        ->maxLength(255)
                                        ->live(onBlur: true)
                                        ->afterStateUpdated(function ($state) use ($mediaItem) {
                                            $mediaItem->setCustomProperty('title', $state);
                                            $mediaItem->save();
                                        }),

                                    Textarea::make("media_alt_{$mediaId}")
                                        ->label('Alt Text')
                                        ->placeholder('Enter alternative text for accessibility')
                                        ->default($mediaItem->getCustomProperty('alt_text'))
                                        ->rows(2)
                                        ->maxLength(500)
                                        ->live(onBlur: true)
                                        ->afterStateUpdated(function ($state) use ($mediaItem) {
                                            $mediaItem->setCustomProperty('alt_text', $state);
                                            $mediaItem->save();
                                        }),

                                    Textarea::make("media_caption_{$mediaId}")
                                        ->label('Caption')
                                        ->placeholder('Enter image caption')
                                        ->default($mediaItem->getCustomProperty('caption'))
                                        ->rows(2)
                                        ->maxLength(500)
                                        ->live(onBlur: true)
                                        ->afterStateUpdated(function ($state) use ($mediaItem) {
                                            $mediaItem->setCustomProperty('caption', $state);
                                            $mediaItem->save();
                                        }),

                                    Placeholder::make("actions_{$mediaId}")
                                        ->label('')
                                        ->content(function () use ($mediaId, $isFeatured) {
                                            if ($isFeatured) {
                                                return new HtmlString(
                                                    '<div class="flex gap-2">'.
                                                    '<span class="inline-flex items-center px-3 py-2 text-sm font-medium text-green-700 bg-green-100 rounded-lg dark:bg-green-900 dark:text-green-300">'.
                                                    '<svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>'.
                                                    'Featured Image'.
                                                    '</span>'.
                                                    '</div>'
                                                );
                                            }

                                            return new HtmlString(
                                                '<button type="button" '.
                                                'wire:click="$dispatch(\'set-featured-image\', { mediaId: '.$mediaId.' })" '.
                                                'class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 focus:ring-4 focus:ring-green-300 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">'.
                                                'Set as Featured'.
                                                '</button>'
                                            );
                                        })
                                        ->columnSpanFull(),
                                ])
                                ->columns(2)
                                ->collapsible();
                        }

                        return $fields;
                    })
                    ->visible(fn ($record) => $record && $record->exists && $record->getMedia('post_images')->isNotEmpty())
                    ->collapsible()
                    ->collapsed(false)
                    ->columnSpanFull(),

                Section::make('SEO')
                    ->schema([
                        TextInput::make('meta_title')
                            ->label('Meta Title')
                            ->maxLength(60)
                            ->helperText('Recommended: 50-60 characters')
                            ->columnSpanFull(),

                        Textarea::make('meta_description')
                            ->label('Meta Description')
                            ->maxLength(160)
                            ->rows(3)
                            ->helperText('Recommended: 150-160 characters')
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->columnSpanFull(),
            ]);
    }
}
