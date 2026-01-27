<?php

namespace App\Support;

use App\Models\DriverVehicleAssignment;
use App\Services\DriverVehicleAssignmentService;
use Closure;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Validation\ValidationException;

class DriverVehicleAssignmentRules
{
    public static function overlapRule(Closure $driverIdResolver, Closure $vehicleIdResolver): Closure
    {
        return function (Get $get, ?DriverVehicleAssignment $record) use ($driverIdResolver, $vehicleIdResolver): Closure {
            return function (string $attribute, $value, Closure $fail) use ($get, $record, $driverIdResolver, $vehicleIdResolver): void {
                $driverId = $driverIdResolver($get);
                $vehicleId = $vehicleIdResolver($get);
                $startDate = $get('start_date');
                $endDate = $get('end_date');

                if (! $driverId || ! $vehicleId || ! $startDate) {
                    return;
                }

                try {
                    app(DriverVehicleAssignmentService::class)->ensureNoOverlapFor(
                        (int) $driverId,
                        (int) $vehicleId,
                        $startDate,
                        $endDate,
                        $record?->id
                    );
                } catch (ValidationException $exception) {
                    $messages = $exception->errors();
                    $first = collect($messages)->flatten()->first();

                    if ($first) {
                        $fail($first);
                    }
                }
            };
        };
    }
}
