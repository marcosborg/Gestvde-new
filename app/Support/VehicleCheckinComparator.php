<?php

namespace App\Support;

use App\Enums\ConditionLevel;
use App\Enums\VehicleDamageArea;

class VehicleCheckinComparator
{
    /**
     * @return array<int, array{area: VehicleDamageArea, from: ConditionLevel, to: ConditionLevel}>
     */
    public static function detectNewDamages(array $previous, array $current): array
    {
        $pairs = [
            ['area' => VehicleDamageArea::ExteriorCondition, 'field' => 'exterior_condition'],
            ['area' => VehicleDamageArea::InteriorCondition, 'field' => 'interior_condition'],
            ['area' => VehicleDamageArea::TiresCondition, 'field' => 'tires_condition'],
        ];

        $damages = [];

        foreach ($pairs as $pair) {
            $area = $pair['area'];
            $field = $pair['field'];
            $previousValue = $previous[$field] ?? null;
            $currentValue = $current[$field] ?? null;

            if (! ($previousValue instanceof ConditionLevel) || ! ($currentValue instanceof ConditionLevel)) {
                continue;
            }

            if ($currentValue->severity() > $previousValue->severity()) {
                $damages[] = [
                    'area' => $area,
                    'from' => $previousValue,
                    'to' => $currentValue,
                ];
            }
        }

        return $damages;
    }
}
