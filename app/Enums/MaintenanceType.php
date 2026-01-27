<?php

namespace App\Enums;

enum MaintenanceType: string
{
    case Preventive = 'preventive';
    case Corrective = 'corrective';
    case RepairInteriorDamages = 'repair_interior';
    case RepairExteriorDamages = 'repair_exterior';

    public function label(): string
    {
        return match ($this) {
            self::Preventive => __('admin.maintenance_types.preventive'),
            self::Corrective => __('admin.maintenance_types.corrective'),
            self::RepairInteriorDamages => __('admin.maintenance_types.repair_interior'),
            self::RepairExteriorDamages => __('admin.maintenance_types.repair_exterior'),
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
