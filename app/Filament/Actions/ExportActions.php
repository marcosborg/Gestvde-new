<?php

namespace App\Filament\Actions;

use Filament\Notifications\Notification;
use Filament\Actions\Action;

class ExportActions
{
    /**
     * @return array<Action>
     */
    public static function make(): array
    {
        return [
            Action::make('export_excel')
                ->label(__('admin.actions.export_excel'))
                ->icon('heroicon-o-arrow-down-tray')
                ->action(function (): void {
                    Notification::make()
                        ->title(__('admin.actions.export_excel'))
                        ->body(__('admin.messages.export_ready'))
                        ->warning()
                        ->send();
                }),
            Action::make('export_pdf')
                ->label(__('admin.actions.export_pdf'))
                ->icon('heroicon-o-document-arrow-down')
                ->action(function (): void {
                    Notification::make()
                        ->title(__('admin.actions.export_pdf'))
                        ->body(__('admin.messages.export_ready'))
                        ->warning()
                        ->send();
                }),
        ];
    }
}
