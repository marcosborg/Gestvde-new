<?php

namespace App\Filament\Imports;

use App\Models\Vehicle;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;
use Illuminate\Support\Str;

class VehicleImporter extends Importer
{
    protected static ?string $model = Vehicle::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('plate')
                ->requiredMapping()
                ->castStateUsing(fn (mixed $state): string => Str::upper(trim((string) $state)))
                ->rules(['required', 'max:255']),
            ImportColumn::make('brand')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('model')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('year')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('fuel_type')
                ->requiredMapping()
                ->multiple()
                ->castStateUsing(function (mixed $state): array {
                    if (is_array($state)) {
                        return $state;
                    }

                    $value = trim((string) $state);
                    if ($value === '') {
                        return [];
                    }

                    $parts = preg_split('/[;,|]/', $value) ?: [$value];

                    return collect($parts)
                        ->map(fn (string $item): string => strtolower(trim($item)))
                        ->map(fn (string $item): string => match ($item) {
                            'gpl' => 'lpg',
                            default => $item,
                        })
                        ->filter()
                        ->unique()
                        ->values()
                        ->all();
                })
                ->rules(['required', 'array', 'min:1'])
                ->nestedRecursiveRules(['in:petrol,diesel,lpg,electric,hybrid']),
            ImportColumn::make('acquisition_type')
                ->requiredMapping()
                ->rules(['required', 'in:leasing']),
            ImportColumn::make('acquisition_value')
                ->numeric()
                ->rules(['numeric', 'min:0']),
            ImportColumn::make('acquisition_date')
                ->rules(['date']),
            ImportColumn::make('annual_depreciation_percent')
                ->numeric()
                ->rules(['numeric', 'min:0']),
            ImportColumn::make('mileage')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer', 'min:0']),
            ImportColumn::make('weekly_rent')
                ->numeric()
                ->rules(['numeric', 'min:0']),
            ImportColumn::make('status')
                ->requiredMapping()
                ->rules(['required', 'in:rented,available,missing_docs,maintenance,in_fleet']),
            ImportColumn::make('notes'),
            ImportColumn::make('leasing_entry_amount')
                ->numeric()
                ->rules(['numeric', 'min:0']),
            ImportColumn::make('leasing_monthly_installment')
                ->numeric()
                ->rules(['numeric', 'min:0']),
            ImportColumn::make('leasing_initial_installment')
                ->numeric()
                ->rules(['numeric', 'min:0']),
            ImportColumn::make('leasing_contract_number')
                ->rules(['max:255']),
            ImportColumn::make('vehicle_documents'),
            ImportColumn::make('chassis_number')
                ->rules(['max:255']),
            ImportColumn::make('tire_type')
                ->rules(['max:255']),
            ImportColumn::make('motorization_type')
                ->rules(['max:255']),
            ImportColumn::make('seats_count')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('color')
                ->rules(['max:255']),
            ImportColumn::make('gps_id')
                ->rules(['max:255']),
            ImportColumn::make('registration_date')
                ->rules(['date']),
            ImportColumn::make('fleet_insurance_policy_number')
                ->rules(['max:255']),
            ImportColumn::make('dua_number')
                ->rules(['max:255']),
            ImportColumn::make('via_verde_id')
                ->rules(['max:255']),
            ImportColumn::make('fleet_card_number')
                ->rules(['max:255']),
            ImportColumn::make('fleet_card_code')
                ->rules(['max:255']),
        ];
    }

    public function resolveRecord(): Vehicle
    {
        $plate = $this->data['plate'] ?? null;

        if ($plate) {
            return Vehicle::query()->firstOrNew(['plate' => Str::upper((string) $plate)]);
        }

        return new Vehicle;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your vehicle import has completed and '.Number::format($import->successful_rows).' '.str('row')->plural($import->successful_rows).' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to import.';
        }

        return $body;
    }
}
