<?php

namespace Database\Factories;

use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class VehicleFactory extends Factory
{
    protected $model = Vehicle::class;

    public function definition(): array
    {
        $plate = Str::upper($this->faker->unique()->bothify('??##??'));

        return [
            'plate' => $plate,
            'brand' => 'HYUNDAI',
            'model' => 'i10',
            'year' => 2020,
            'fuel_type' => 'petrol',
            'acquisition_type' => 'own',
            'acquisition_value' => null,
            'acquisition_date' => null,
            'annual_depreciation_percent' => null,
            'mileage' => 0,
            'weekly_rent' => 700,
            'status' => 'active',
            'notes' => null,
        ];
    }
}
