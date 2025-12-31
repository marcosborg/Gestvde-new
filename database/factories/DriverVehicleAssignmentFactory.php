<?php

namespace Database\Factories;

use App\Models\Driver;
use App\Models\DriverVehicleAssignment;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

class DriverVehicleAssignmentFactory extends Factory
{
    protected $model = DriverVehicleAssignment::class;

    public function definition(): array
    {
        $startDate = now()->startOfWeek();

        return [
            'driver_id' => Driver::factory(),
            'vehicle_id' => Vehicle::factory(),
            'start_date' => $startDate->toDateString(),
            'end_date' => null,
            'weekly_rate_override' => null,
            'note' => null,
        ];
    }
}
