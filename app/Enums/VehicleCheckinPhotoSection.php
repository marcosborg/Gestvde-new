<?php

namespace App\Enums;

enum VehicleCheckinPhotoSection: string
{
    case Exterior = 'exterior';
    case Interior = 'interior';

    public function label(): string
    {
        return match ($this) {
            self::Exterior => __('admin.photo_sections.exterior'),
            self::Interior => __('admin.photo_sections.interior'),
        };
    }
}
