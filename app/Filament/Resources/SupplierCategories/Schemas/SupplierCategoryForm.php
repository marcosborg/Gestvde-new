<?php

namespace App\Filament\Resources\SupplierCategories\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SupplierCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->disabled(fn (): bool => auth()->user()?->hasRole('readonly') ?? false)
            ->components([
                Section::make(__('admin.sections.category'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('admin.labels.name'))
                            ->required()
                            ->maxLength(120)
                            ->unique(ignoreRecord: true),
                    ]),
            ]);
    }
}
