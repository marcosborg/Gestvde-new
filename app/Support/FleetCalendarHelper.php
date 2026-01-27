<?php

namespace App\Support;

use Illuminate\Support\Carbon;

class FleetCalendarHelper
{
    public static function formatTitle(?string $prefix, string $title): string
    {
        $prefix = $prefix ? trim($prefix) : null;

        return $prefix ? $prefix.' · '.$title : $title;
    }

    public static function resolveDisplayDate(Carbon $dueDate, int $noticeDays, Carbon $rangeStart, Carbon $rangeEnd): ?Carbon
    {
        if ($dueDate->betweenIncluded($rangeStart, $rangeEnd)) {
            return $dueDate;
        }

        $noticeDays = max(0, $noticeDays);

        if ($noticeDays === 0) {
            return null;
        }

        $noticeDate = $dueDate->copy()->subDays($noticeDays);

        return $noticeDate->betweenIncluded($rangeStart, $rangeEnd) ? $noticeDate : null;
    }

    public static function isWithinNoticeWindow(Carbon $dueDate, int $noticeDays, ?Carbon $today = null): bool
    {
        $noticeDays = max(0, $noticeDays);

        if ($noticeDays === 0) {
            return false;
        }

        $today ??= now()->startOfDay();
        $daysUntil = $today->diffInDays($dueDate, false);

        return $daysUntil >= 0 && $daysUntil <= $noticeDays;
    }
}
