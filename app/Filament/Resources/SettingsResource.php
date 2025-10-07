<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SettingsResource\Pages;
use App\Models\Setting;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use UnitEnum;

class SettingsResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static BackedEnum|string|null $navigationIcon = Heroicon::Cog6Tooth;

    protected static string|UnitEnum|null $navigationGroup = 'System';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'key';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('key')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                TextColumn::make('group_name')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'general' => 'gray',
                        'site' => 'blue',
                        'seo' => 'green',
                        'media' => 'purple',
                        'content' => 'orange',
                        'email' => 'red',
                        'user' => 'indigo',
                        'admin' => 'yellow',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('value')
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();

                        return strlen($state) > 50 ? $state : null;
                    }),

                IconColumn::make('is_public')
                    ->boolean()
                    ->label('Public'),
            ])
            ->filters([
                SelectFilter::make('group_name')
                    ->options([
                        'general' => 'General',
                        'site' => 'Site',
                        'seo' => 'SEO',
                        'media' => 'Media',
                        'content' => 'Content',
                        'email' => 'Email',
                        'user' => 'User',
                        'admin' => 'Admin',
                    ]),

                SelectFilter::make('type')
                    ->options([
                        'string' => 'String',
                        'boolean' => 'Boolean',
                        'integer' => 'Integer',
                        'json' => 'JSON',
                        'text' => 'Text',
                    ]),

                TernaryFilter::make('is_public')
                    ->label('Public Settings')
                    ->placeholder('All settings')
                    ->trueLabel('Public only')
                    ->falseLabel('Private only'),
            ])
            ->actions([])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('group_name', 'asc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSettings::route('/'),
            'create' => Pages\CreateSetting::route('/create'),
            'edit' => Pages\EditSetting::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return auth()->user()->hasRole('super-admin');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->hasRole('super-admin');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->hasRole('super-admin');
    }
}
