<?php

namespace App\Filament\Resources\UserResource\Schemas;

use App\Models\Role;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('User Information')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('avatar')
                            ->label('Profile Picture')
                            ->collection('avatar')
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '1:1',
                            ])
                            ->maxSize(2048)
                            ->helperText('Upload a profile picture for this user'),
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        TextInput::make('password')
                            ->password()
                            ->required(fn (string $context): bool => $context === 'create')
                            ->dehydrated(fn ($state) => filled($state))
                            ->dehydrateStateUsing(fn ($state) => \Illuminate\Support\Facades\Hash::make($state))
                            ->rule(\Illuminate\Validation\Rules\Password::default())
                            ->helperText('Leave blank to keep current password when editing'),
                    ])
                    ->columns(2),

                Section::make('Account Status')
                    ->schema([
                        Toggle::make('email_verified_at')
                            ->label('Email Verified')
                            ->helperText('Check if user has verified their email address')
                            ->dehydrated(false)
                            ->afterStateHydrated(function (Toggle $component, $state) {
                                $component->state($state !== null);
                            })
                            ->dehydrateStateUsing(function ($state, $record) {
                                return $state ? now() : null;
                            }),
                        DateTimePicker::make('email_verified_at')
                            ->label('Email Verified At')
                            ->visible(fn ($record) => $record?->email_verified_at !== null)
                            ->disabled(),
                    ])
                    ->columns(2),

                // Two-Factor Authentication section removed for simplicity

                Section::make('Role Assignment')
                    ->schema([
                        Select::make('role')
                            ->label('User Role')
                            ->options([
                                'super-admin' => 'Super Admin - Full system access',
                                'content-manager' => 'Content Manager - Content management only',
                            ])
                            ->default('content-manager')
                            ->required()
                            ->helperText('Super Admin: Full access. Content Manager: Content management only.')
                            ->dehydrated(false)
                            ->afterStateHydrated(function (Select $component, $state, $record) {
                                if ($record) {
                                    $userRole = $record->roles()->first();
                                    $component->state($userRole->slug ?? 'content-manager');
                                }
                            })
                            ->dehydrateStateUsing(function ($state, $record) {
                                if ($record && $state) {
                                    // Remove all existing roles
                                    $record->roles()->detach();
                                    // Assign new role
                                    $record->assignRole($state);
                                }
                            }),
                    ]),
            ]);
    }
}
