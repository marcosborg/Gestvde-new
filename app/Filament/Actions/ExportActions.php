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
                ->label('Exportar Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(function (): void {
                    Notification::make()
                        ->title('Exportar Excel')
                        ->body('Estrutura de exportacao pronta. Implementar geracao do ficheiro quando necessario.')
                        ->warning()
                        ->send();
                }),
            Action::make('export_pdf')
                ->label('Exportar PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->action(function (): void {
                    Notification::make()
                        ->title('Exportar PDF')
                        ->body('Estrutura de exportacao pronta. Implementar geracao do ficheiro quando necessario.')
                        ->warning()
                        ->send();
                }),
        ];
    }
}
