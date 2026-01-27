<?php

namespace App\Filament\Imports;

use App\Models\Driver;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;
use Illuminate\Support\Str;

class DriverImporter extends Importer
{
    protected static ?string $model = Driver::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('phone')
                ->rules(['max:255']),
            ImportColumn::make('email')
                ->castStateUsing(fn (mixed $state): ?string => filled($state) ? Str::lower(trim((string) $state)) : null)
                ->rules(['nullable', 'email', 'max:255']),
            ImportColumn::make('active')
                ->boolean()
                ->rules(['boolean']),
            ImportColumn::make('company')
                ->relationship(),
            ImportColumn::make('nif')
                ->rules(['max:255']),
            ImportColumn::make('birth_date')
                ->rules(['date']),
            ImportColumn::make('nationality')
                ->rules(['max:255']),
            ImportColumn::make('marital_status')
                ->rules(['max:255']),
            ImportColumn::make('address'),
            ImportColumn::make('notes'),
            ImportColumn::make('documentation'),
            ImportColumn::make('blacklisted')
                ->boolean()
                ->rules(['boolean']),
            ImportColumn::make('on_vacation')
                ->boolean()
                ->rules(['boolean']),
            ImportColumn::make('identity_document_type')
                ->rules(['max:255']),
            ImportColumn::make('identity_document_number')
                ->rules(['max:255']),
            ImportColumn::make('identity_document_valid_until')
                ->rules(['date']),
            ImportColumn::make('sns_number')
                ->rules(['max:255']),
            ImportColumn::make('niss_number')
                ->rules(['max:255']),
            ImportColumn::make('driving_license_number')
                ->rules(['max:255']),
            ImportColumn::make('driving_license_issued_at')
                ->rules(['date']),
            ImportColumn::make('driving_license_valid_until')
                ->rules(['date']),
            ImportColumn::make('driving_license_category')
                ->rules(['max:255']),
            ImportColumn::make('tvde_certificate_number')
                ->rules(['max:255']),
            ImportColumn::make('tvde_certificate_valid_until')
                ->rules(['date']),
            ImportColumn::make('platform_uber')
                ->boolean()
                ->rules(['boolean']),
            ImportColumn::make('platform_bolt')
                ->boolean()
                ->rules(['boolean']),
            ImportColumn::make('platform_other')
                ->rules(['max:255']),
            ImportColumn::make('emergency_contact_name')
                ->rules(['max:255']),
            ImportColumn::make('emergency_contact_phone')
                ->rules(['max:255']),
            ImportColumn::make('bank_account_holder')
                ->rules(['max:255']),
            ImportColumn::make('bank_iban')
                ->rules(['max:255']),
            ImportColumn::make('deposit_amount')
                ->numeric()
                ->rules(['numeric', 'min:0']),
            ImportColumn::make('deposit_paid_at')
                ->rules(['date']),
            ImportColumn::make('deposit_payment_method')
                ->rules(['max:255']),
            ImportColumn::make('activity_started_at')
                ->rules(['date']),
            ImportColumn::make('activity_ended_at')
                ->rules(['date']),
        ];
    }

    public function resolveRecord(): Driver
    {
        $email = $this->data['email'] ?? null;

        if ($email) {
            return Driver::query()->firstOrNew(['email' => (string) $email]);
        }

        return new Driver;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your driver import has completed and '.Number::format($import->successful_rows).' '.str('row')->plural($import->successful_rows).' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to import.';
        }

        return $body;
    }
}
