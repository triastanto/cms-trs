<?php

namespace App\Filament\Resources\Tags\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TagForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make('Tag Details')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, $state, mixed $set) {
                                if ($operation !== 'create') {
                                    return;
                                }
                                $set('slug', \Illuminate\Support\Str::slug($state));
                            }),

                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(\App\Models\Tag::class, 'slug', ignoreRecord: true)
                            ->rules(['alpha_dash'])
                            ->helperText('URL friendly version of the name'),
                    ])
                    ->columns(2),

                Section::make('Content')
                    ->schema([
                        Textarea::make('description')
                            ->maxLength(500)
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                Section::make('Settings')
                    ->schema([
                        ColorPicker::make('color')
                            ->helperText('Color used for tag display')
                            ->nullable(),

                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->helperText('Only active tags are shown in post forms'),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }
}
