<?php

namespace App\Enums;

enum VehicleCheckinType: string
{
    case CheckIn = 'check_in';
    case CheckOut = 'check_out';
    case Inspection = 'inspection';

    public function label(): string
    {
        return match ($this) {
            self::CheckIn => __('admin.checkin_types.check_in'),
            self::CheckOut => __('admin.checkin_types.check_out'),
            self::Inspection => __('admin.checkin_types.inspection'),
        };
    }

    /**
     * @return array<string, string>
     */
    public static function formOptions(): array
    {
        return [
            self::CheckIn->value => self::CheckIn->label(),
            self::CheckOut->value => self::CheckOut->label(),
        ];
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
