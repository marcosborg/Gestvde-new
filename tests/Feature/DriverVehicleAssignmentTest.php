<?php

namespace Tests\Feature;

use App\Models\Driver;
use App\Models\DriverVehicleAssignment;
use App\Models\TvdeWeek;
use App\Models\Vehicle;
use App\Services\RentalCalculator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class DriverVehicleAssignmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_driver_cannot_have_overlaps(): void
    {
        $driver = Driver::factory()->create();
        $vehicleA = Vehicle::factory()->create();
        $vehicleB = Vehicle::factory()->create();

        DriverVehicleAssignment::create([
            'driver_id' => $driver->id,
            'vehicle_id' => $vehicleA->id,
            'start_date' => '2025-01-01',
            'end_date' => '2025-01-07',
        ]);

        $this->expectException(ValidationException::class);

        DriverVehicleAssignment::create([
            'driver_id' => $driver->id,
            'vehicle_id' => $vehicleB->id,
            'start_date' => '2025-01-05',
            'end_date' => '2025-01-10',
        ]);
    }

    public function test_vehicle_cannot_have_overlaps(): void
    {
        $driverA = Driver::factory()->create();
        $driverB = Driver::factory()->create();
        $vehicle = Vehicle::factory()->create();

        DriverVehicleAssignment::create([
            'driver_id' => $driverA->id,
            'vehicle_id' => $vehicle->id,
            'start_date' => '2025-02-01',
            'end_date' => '2025-02-07',
        ]);

        $this->expectException(ValidationException::class);

        DriverVehicleAssignment::create([
            'driver_id' => $driverB->id,
            'vehicle_id' => $vehicle->id,
            'start_date' => '2025-02-05',
            'end_date' => '2025-02-08',
        ]);
    }

    public function test_assignment_mid_week_charges_only_days(): void
    {
        $driver = Driver::factory()->create();
        $vehicle = Vehicle::factory()->create(['weekly_rent' => 700]);

        $week = TvdeWeek::create([
            'start_date' => '2025-01-06',
            'end_date' => '2025-01-12',
        ]);

        DriverVehicleAssignment::create([
            'driver_id' => $driver->id,
            'vehicle_id' => $vehicle->id,
            'start_date' => '2025-01-08',
            'end_date' => '2025-01-10',
        ]);

        $calculator = new RentalCalculator();
        $result = $calculator->calculateForDriverAndWeek($driver->id, $week->id);

        $this->assertSame(300.0, $result['total_rent']);
        $this->assertCount(1, $result['breakdown']);
        $this->assertSame(3, $result['breakdown'][0]['days_in_week']);
    }

    public function test_week_swap_sums_assignments_correctly(): void
    {
        $driver = Driver::factory()->create();
        $vehicleA = Vehicle::factory()->create(['weekly_rent' => 700]);
        $vehicleB = Vehicle::factory()->create(['weekly_rent' => 350]);

        $week = TvdeWeek::create([
            'start_date' => '2025-01-06',
            'end_date' => '2025-01-12',
        ]);

        DriverVehicleAssignment::create([
            'driver_id' => $driver->id,
            'vehicle_id' => $vehicleA->id,
            'start_date' => '2025-01-06',
            'end_date' => '2025-01-08',
        ]);

        DriverVehicleAssignment::create([
            'driver_id' => $driver->id,
            'vehicle_id' => $vehicleB->id,
            'start_date' => '2025-01-09',
            'end_date' => '2025-01-12',
        ]);

        $calculator = new RentalCalculator();
        $result = $calculator->calculateForDriverAndWeek($driver->id, $week->id);

        $this->assertSame(500.0, $result['total_rent']);
        $this->assertCount(2, $result['breakdown']);
    }

    public function test_open_end_assignment_calculates_until_week_end(): void
    {
        Carbon::setTestNow(Carbon::parse('2025-01-09'));

        $driver = Driver::factory()->create();
        $vehicle = Vehicle::factory()->create(['weekly_rent' => 700]);

        $week = TvdeWeek::create([
            'start_date' => '2025-01-06',
            'end_date' => '2025-01-12',
        ]);

        DriverVehicleAssignment::create([
            'driver_id' => $driver->id,
            'vehicle_id' => $vehicle->id,
            'start_date' => '2025-01-09',
            'end_date' => null,
        ]);

        $calculator = new RentalCalculator();
        $result = $calculator->calculateForDriverAndWeek($driver->id, $week->id);

        $this->assertSame(400.0, $result['total_rent']);
        $this->assertSame(4, $result['breakdown'][0]['days_in_week']);
    }
}
