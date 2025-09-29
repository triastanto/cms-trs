<?php

namespace App\Filament\Resources\Tags\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class TagsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ColorColumn::make('color')
                    ->label('')
                    ->placeholder('No color'),

                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Slug copied!')
                    ->color('gray')
                    ->fontFamily('mono'),

                TextColumn::make('description')
                    ->label('Description')
                    ->limit(50)
                    ->searchable()
                    ->placeholder('No description'),

                BadgeColumn::make('is_active')
                    ->label('Status')
                    ->getStateUsing(fn ($record) => $record->is_active ? 'Active' : 'Inactive')
                    ->colors([
                        'success' => fn ($state): bool => $state === 'Active',
                        'danger' => fn ($state): bool => $state === 'Inactive',
                    ]),

                TextColumn::make('posts_count')
                    ->label('Posts')
                    ->counts('posts')
                    ->sortable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Status')
                    ->placeholder('All tags')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make()
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('name', 'asc')
            ->emptyStateHeading('No tags yet')
            ->emptyStateDescription('Create your first tag to organize your posts.')
            ->emptyStateIcon('heroicon-o-hashtag');
    }
}
