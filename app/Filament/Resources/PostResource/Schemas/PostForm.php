<?php

namespace App\Filament\Resources\PostResource\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Post Details')
                    ->schema([
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
                            ->required()
                            ->columnSpanFull(),

                        DateTimePicker::make('published_at')
                            ->label('Publish Date')
                            ->nullable()
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                Section::make('Author')
                    ->schema([
                        Select::make('user_id')
                            ->label('Author')
                            ->relationship('user', 'name')
                            ->required()
                            ->default(auth()->id())
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                Section::make('Categorization')
                    ->schema([
                        Select::make('category_id')
                            ->label('Category')
                            ->relationship('category', 'name', fn ($query) => $query->where('is_active', true))
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->columnSpanFull(),

                        Select::make('tags')
                            ->label('Tags')
                            ->relationship('tags', 'name', fn ($query) => $query->where('is_active', true))
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

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
                    ->collapsible(),

                Section::make('Media')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('featured_image')
                            ->label('Featured Image')
                            ->collection('featured_image')
                            ->image()
                            ->required(false)
                            ->helperText('Click to browse or drag and drop an image file. Thumbnails will be automatically generated.')
                            ->columnSpanFull(),

                        SpatieMediaLibraryFileUpload::make('gallery')
                            ->label('Gallery Images')
                            ->collection('gallery')
                            ->image()
                            ->multiple()
                            ->reorderable()
                            ->helperText('Upload multiple images for a gallery')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

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
                    ->collapsible(),
            ]);
    }
}
