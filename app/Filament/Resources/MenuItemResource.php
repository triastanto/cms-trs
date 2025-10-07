<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MenuItemResource\Pages;
use App\Models\Menu;
use App\Models\MenuItem;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MenuItemResource extends Resource
{
    protected static ?string $model = MenuItem::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-list-bullet';

    protected static string|\UnitEnum|null $navigationGroup = 'Content Management';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('menu_id')
                    ->label('Menu')
                    ->required()
                    ->options(Menu::pluck('name', 'id'))
                    ->searchable(),

                Forms\Components\Select::make('parent_id')
                    ->label('Parent Menu Item')
                    ->options(function ($get) {
                        $menuId = $get('menu_id');
                        if (! $menuId) {
                            return [];
                        }

                        return MenuItem::where('menu_id', $menuId)
                            ->where('id', '!=', $get('id') ?? 0)
                            ->pluck('title', 'id');
                    })
                    ->searchable(),

                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('url')
                    ->maxLength(255)
                    ->placeholder('/page-slug or https://example.com'),

                Forms\Components\Select::make('target')
                    ->options(MenuItem::getTargetOptions())
                    ->default('_self'),

                Forms\Components\TextInput::make('icon')
                    ->maxLength(255)
                    ->placeholder('heroicon-o-home'),

                Forms\Components\TextInput::make('css_class')
                    ->maxLength(255)
                    ->placeholder('btn-primary'),

                Forms\Components\TextInput::make('sort_order')
                    ->numeric()
                    ->default(0),

                Forms\Components\Toggle::make('is_active')
                    ->default(true),

                Forms\Components\Toggle::make('is_external')
                    ->label('External Link'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->description(function (MenuItem $record): string {
                        if ($record->parent && $record->parent instanceof MenuItem) {
                            return '↳ Child of: '.$record->parent->title;
                        }

                        return 'Root level';
                    }),

                Tables\Columns\TextColumn::make('url')
                    ->limit(30),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('menu_id')
                    ->label('Menu')
                    ->options(Menu::pluck('name', 'id')),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->boolean(),

                Tables\Filters\TernaryFilter::make('is_external')
                    ->boolean(),
            ])
            ->actions([])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order', 'asc')
            ->groups([
                Tables\Grouping\Group::make('menu.name')
                    ->label('Menu')
                    ->collapsible()
                    ->titlePrefixedWithLabel(false),
            ])
            ->defaultGroup('menu.name');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMenuItems::route('/'),
            'create' => Pages\CreateMenuItem::route('/create'),
            'edit' => Pages\EditMenuItem::route('/{record}/edit'),
        ];
    }

    /**
     * @return Builder<MenuItem>
     */
    public static function getEloquentQuery(): Builder
    {
        /** @var Builder<MenuItem> $query */
        $query = parent::getEloquentQuery()
            ->with(['menu', 'parent'])
            ->orderByRaw('CASE WHEN parent_id IS NULL THEN id ELSE parent_id END')
            ->orderByRaw('CASE WHEN parent_id IS NULL THEN 0 ELSE 1 END')
            ->orderBy('sort_order', 'asc');

        return $query;
    }
}
