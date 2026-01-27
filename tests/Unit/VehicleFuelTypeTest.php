<?php

use App\Models\Vehicle;

test('it normalizes fuel types to a lowercase array', function () {
    $vehicle = new Vehicle;

    $vehicle->fuel_type = ['Petrol', 'Diesel', 'petrol', ''];

    expect($vehicle->fuel_type)->toBe(['petrol', 'diesel']);
});

test('it accepts a legacy string fuel type', function () {
    $vehicle = new Vehicle;

    $vehicle->setRawAttributes(['fuel_type' => 'diesel']);

    expect($vehicle->fuel_type)->toBe(['diesel']);
});

test('it accepts json encoded fuel types', function () {
    $vehicle = new Vehicle;

    $vehicle->setRawAttributes(['fuel_type' => '["petrol","lpg"]']);

    expect($vehicle->fuel_type)->toBe(['petrol', 'lpg']);
});

test('it clears fuel type when set to empty', function () {
    $vehicle = new Vehicle;

    $vehicle->fuel_type = '';

    expect($vehicle->fuel_type)->toBe([]);
});
