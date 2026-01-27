<?php

namespace App\Enums;

enum CleanlinessLevel: string
{
    case Clean = 'clean';
    case Average = 'average';
    case Dirty = 'dirty';

    public function label(): string
    {
        return match ($this) {
            self::Clean => __('admin.cleanliness_levels.clean'),
            self::Average => __('admin.cleanliness_levels.average'),
            self::Dirty => __('admin.cleanliness_levels.dirty'),
        };
    }

    public function severity(): int
    {
        return match ($this) {
            self::Clean => 1,
            self::Average => 2,
            self::Dirty => 3,
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
