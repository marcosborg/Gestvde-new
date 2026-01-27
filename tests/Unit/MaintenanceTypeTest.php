<?php

use App\Enums\MaintenanceType;

test('it exposes all maintenance type values', function () {
    $values = array_map(fn (MaintenanceType $type): string => $type->value, MaintenanceType::cases());

    expect($values)->toEqualCanonicalizing([
        MaintenanceType::Preventive->value,
        MaintenanceType::Corrective->value,
        MaintenanceType::RepairInteriorDamages->value,
        MaintenanceType::RepairExteriorDamages->value,
    ]);
});
