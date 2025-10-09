<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages\CreatePost;
use App\Filament\Resources\PostResource\Pages\EditPost;
use App\Filament\Resources\PostResource\Pages\ListPosts;
use App\Filament\Resources\PostResource\Schemas\PostForm;
use App\Filament\Resources\PostResource\Tables\PostsTable;
use App\Models\Post;
use BackedEnum;
use Filament\Forms\Components\Placeholder;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static BackedEnum|string|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'title';

    protected static string|UnitEnum|null $navigationGroup = 'Content Management';

    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['user', 'category', 'tags', 'media']);
    }

    public static function form(Schema $schema): Schema
    {
        return PostForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PostsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->schema([
                \Filament\Schemas\Components\Section::make('Post Information')
                    ->schema([
                        Placeholder::make('title')
                            ->label('Title')
                            ->content(fn ($record) => $record->title),

                        Placeholder::make('slug')
                            ->label('Slug')
                            ->content(fn ($record) => $record->slug),

                        Placeholder::make('status')
                            ->label('Status')
                            ->content(fn ($record) => ucfirst($record->status)),

                        Placeholder::make('published_at')
                            ->label('Published At')
                            ->content(fn ($record) => $record->published_at?->format('Y-m-d H:i:s') ?? 'Not published'),
                    ])
                    ->columns(2)
                    ->collapsible(),

                \Filament\Schemas\Components\Section::make('Images')
                    ->schema([
                        Placeholder::make('featured_image_info')
                            ->label('Featured Image')
                            ->content(function ($record) {
                                $featuredImage = $record->getFeaturedImage();
                                if (! $featuredImage) {
                                    return 'No featured image set';
                                }
                                $title = $featuredImage->getCustomProperty('title') ?: 'No title';

                                return $title.' ('.$featuredImage->file_name.')';
                            }),

                        Placeholder::make('gallery_count')
                            ->label('Gallery Images')
                            ->content(function ($record) {
                                $galleryImages = $record->getGalleryImages();
                                $count = $galleryImages->count();
                                if ($count === 0) {
                                    return 'No gallery images';
                                }

                                return $count.' image'.($count > 1 ? 's' : '');
                            }),

                        Placeholder::make('total_images')
                            ->label('Total Images')
                            ->content(function ($record) {
                                return $record->getMedia('post_images')->count().' total';
                            }),
                    ])
                    ->columns(3)
                    ->collapsible(),
            ]);
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
            'index' => ListPosts::route('/'),
            'create' => CreatePost::route('/create'),
            'edit' => EditPost::route('/{record}/edit'),
        ];
    }
}
