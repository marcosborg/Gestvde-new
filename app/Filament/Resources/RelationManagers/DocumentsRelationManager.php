<?php

namespace App\Filament\Resources\RelationManagers;

use App\Models\Document;
use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class DocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';

    protected static ?string $title = null;

    protected static string $ownerResource;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('admin.sections.documentation'))
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('title')
                                ->label(__('admin.labels.title'))
                                ->required()
                                ->maxLength(150),
                            Select::make('doc_type')
                                ->label(__('admin.labels.type'))
                                ->options([
                                    'inspection' => __('admin.document_types.inspection'),
                                    'insurance' => __('admin.document_types.insurance'),
                                    'tax' => __('admin.document_types.tax'),
                                    'contract' => __('admin.document_types.contract'),
                                    'other' => __('admin.document_types.other'),
                                ])
                                ->required(),
                            DatePicker::make('valid_until')
                                ->label(__('admin.labels.validity'))
                                ->helperText(__('admin.help_texts.document_validity')),
                            TextInput::make('notify_before_days')
                                ->label(__('admin.labels.advance_notice_days'))
                                ->numeric()
                                ->minValue(0)
                                ->default(0)
                                ->helperText(__('admin.help_texts.document_notify')),
                        ]),
                        FileUpload::make('file_path')
                            ->label(__('admin.labels.document'))
                            ->disk('public')
                            ->directory('documents')
                            ->downloadable()
                            ->openable()
                            ->columnSpanFull(),
                        Textarea::make('notes')
                            ->label(__('admin.labels.notes'))
                            ->rows(3)
                            ->maxLength(2000),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(__('admin.labels.title'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('doc_type')
                    ->label(__('admin.labels.type'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => __('admin.document_types.'.$state))
                    ->sortable(),
                TextColumn::make('valid_until')
                    ->label(__('admin.labels.validity'))
                    ->date()
                    ->badge()
                    ->placeholder('-')
                    ->color(fn (Document $record): string => $record->isExpired()
                        ? 'danger'
                        : ($record->isExpiringSoon() ? 'warning' : 'gray'))
                    ->sortable(),
                TextColumn::make('notify_before_days')
                    ->label(__('admin.labels.advance_notice_days'))
                    ->numeric()
                    ->toggleable(),
                TextColumn::make('file_path')
                    ->label(__('admin.labels.document'))
                    ->formatStateUsing(fn (?string $state): string => $state ? basename($state) : '-')
                    ->url(fn (Document $record): ?string => $record->file_path ? Storage::url($record->file_path) : null)
                    ->openUrlInNewTab()
                    ->toggleable(),
                TextColumn::make('notes')
                    ->label(__('admin.labels.notes'))
                    ->limit(40)
                    ->toggleable(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->after(function (Document $record): void {
                        $record->syncCalendarEvent();
                    }),
            ])
            ->actions([
                EditAction::make()
                    ->after(function (Document $record): void {
                        $record->syncCalendarEvent();
                    }),
                DeleteAction::make()
                    ->before(function (Document $record): void {
                        $record->deleteCalendarEvent();
                    }),
            ]);
    }

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('admin.labels.documentation');
    }

    public function getAuthorizationResponse(string $action, ?Model $record = null): Response
    {
        $resource = static::$ownerResource ?? null;

        if (! $resource) {
            return parent::getAuthorizationResponse($action, $record);
        }

        $permission = FilamentShield::getResourcePolicyActionsWithPermissions($resource)[$action] ?? null;

        if (! $permission) {
            return Response::deny();
        }

        $user = auth()->user();

        return $user && $user->can($permission) ? Response::allow() : Response::deny();
    }
}
