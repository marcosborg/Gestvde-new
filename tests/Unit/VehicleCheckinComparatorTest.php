<?php

use App\Enums\ConditionLevel;
use App\Enums\VehicleDamageArea;
use App\Support\VehicleCheckinComparator;

test('it detects worsening conditions', function () {
    $previous = [
        'exterior_condition' => ConditionLevel::Good,
        'interior_condition' => ConditionLevel::Fair,
        'tires_condition' => ConditionLevel::Good,
    ];

    $current = [
        'exterior_condition' => ConditionLevel::Poor,
        'interior_condition' => ConditionLevel::Fair,
        'tires_condition' => ConditionLevel::Fair,
    ];

    $damages = VehicleCheckinComparator::detectNewDamages($previous, $current);

    expect($damages)->toHaveCount(2)
        ->and($damages[0]['area'])->toBe(VehicleDamageArea::ExteriorCondition)
        ->and($damages[1]['area'])->toBe(VehicleDamageArea::TiresCondition);
});
