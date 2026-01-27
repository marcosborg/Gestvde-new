<?php

namespace App\Services;

use App\Models\DriverVehicleAssignment;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class DriverVehicleAssignmentService
{
    public function ensureNoOverlapFor(
        int $driverId,
        int $vehicleId,
        string | Carbon $startDate,
        string | Carbon | null $endDate,
        ?int $ignoreId = null
    ): void {
        $startDate = Carbon::parse($startDate)->startOfDay();
        $endDate = $endDate ? Carbon::parse($endDate)->startOfDay() : null;

        if ($endDate && $endDate->lt($startDate)) {
            throw ValidationException::withMessages([
                'end_date' => ['Data de fim nao pode ser anterior a data de inicio.'],
            ]);
        }

        $driverOverlap = DriverVehicleAssignment::query()
            ->forDriver($driverId)
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->overlaps($startDate, $endDate)
            ->exists();

        if ($driverOverlap) {
            throw ValidationException::withMessages([
                'driver_id' => ['O motorista ja tem uma atribuicao nesse periodo.'],
            ]);
        }

        $vehicleOverlap = DriverVehicleAssignment::query()
            ->forVehicle($vehicleId)
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->overlaps($startDate, $endDate)
            ->exists();

        if ($vehicleOverlap) {
            throw ValidationException::withMessages([
                'vehicle_id' => ['A viatura ja esta atribuida nesse periodo.'],
            ]);
        }
    }
}
