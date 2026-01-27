<?php

use App\Models\VehicleSupplierContract;

test('it casts recurring to boolean', function () {
    $contract = new VehicleSupplierContract;

    $contract->recurring = 1;

    expect($contract->recurring)->toBeTrue();
});
