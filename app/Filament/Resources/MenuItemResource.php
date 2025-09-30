<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MenuItemResource\Pages;
use App\Models\Menu;
use App\Models\MenuItem;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MenuItemResource extends Resource
{
    protected static ?string $model = MenuItem::class;

    protected static ?int $navigationSort = 2;

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
                Tables\Columns\TextColumn::make('menu.name')
                    ->label('Menu')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('url')
                    ->limit(30),

                Tables\Columns\IconColumn::make('is_external')
                    ->label('External')
                    ->boolean(),

                Tables\Columns\TextColumn::make('target')
                    ->badge(),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Order')
                    ->sortable(),

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
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order', 'asc')
            ->reorderable('sort_order');
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['menu', 'parent']);
    }
}
