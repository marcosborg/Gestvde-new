<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->disabled(fn (): bool => auth()->user()?->hasRole('readonly') ?? false)
            ->components([
                Section::make(__('admin.sections.user_details'))
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->label(__('admin.labels.name'))
                                ->required()
                                ->maxLength(150),
                            TextInput::make('email')
                                ->label(__('admin.labels.email'))
                                ->email()
                                ->required()
                                ->unique(ignoreRecord: true)
                                ->maxLength(150),
                            TextInput::make('password')
                                ->label(__('admin.labels.password'))
                                ->password()
                                ->revealable()
                                ->minLength(8)
                                ->required(fn (string $context): bool => $context === 'create')
                                ->dehydrated(fn (?string $state): bool => filled($state)),
                            TextInput::make('password_confirmation')
                                ->label(__('admin.labels.password_confirmation'))
                                ->password()
                                ->revealable()
                                ->same('password')
                                ->required(fn (string $context): bool => $context === 'create')
                                ->dehydrated(false),
                            Select::make('roles')
                                ->label(__('admin.labels.role'))
                                ->relationship('roles', 'name')
                                ->multiple()
                                ->preload()
                                ->visible(fn (): bool => auth()->user()?->hasRole('admin') ?? false)
                                ->columnSpanFull(),
                        ]),
                    ]),
            ]);
    }
}
