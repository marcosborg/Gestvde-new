<?php

namespace App\Enums;

enum ConditionLevel: string
{
    case Good = 'good';
    case Fair = 'fair';
    case Poor = 'poor';

    public function label(): string
    {
        return match ($this) {
            self::Good => __('admin.condition_levels.good'),
            self::Fair => __('admin.condition_levels.fair'),
            self::Poor => __('admin.condition_levels.poor'),
        };
    }

    public function severity(): int
    {
        return match ($this) {
            self::Good => 1,
            self::Fair => 2,
            self::Poor => 3,
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
