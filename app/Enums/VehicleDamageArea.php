<?php

namespace App\Enums;

enum VehicleDamageArea: string
{
    case ExteriorCondition = 'exterior_condition';
    case InteriorCondition = 'interior_condition';
    case TiresCondition = 'tires_condition';

    public function label(): string
    {
        return match ($this) {
            self::ExteriorCondition => __('admin.damage_areas.exterior_condition'),
            self::InteriorCondition => __('admin.damage_areas.interior_condition'),
            self::TiresCondition => __('admin.damage_areas.tires_condition'),
        };
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        $options = [];

        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }
}
