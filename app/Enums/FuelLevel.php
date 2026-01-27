<?php

namespace App\Enums;

enum FuelLevel: string
{
    case Full = 'full';
    case ThreeQuarters = 'three_quarters';
    case Half = 'half';
    case Quarter = 'quarter';
    case Empty = 'empty';

    public function label(): string
    {
        return match ($this) {
            self::Full => __('admin.fuel_levels.full'),
            self::ThreeQuarters => __('admin.fuel_levels.three_quarters'),
            self::Half => __('admin.fuel_levels.half'),
            self::Quarter => __('admin.fuel_levels.quarter'),
            self::Empty => __('admin.fuel_levels.empty'),
        };
    }

    public function severity(): int
    {
        return match ($this) {
            self::Full => 1,
            self::ThreeQuarters => 2,
            self::Half => 3,
            self::Quarter => 4,
            self::Empty => 5,
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
