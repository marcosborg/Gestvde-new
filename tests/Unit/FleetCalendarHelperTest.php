<?php

use App\Support\FleetCalendarHelper;
use Illuminate\Support\Carbon;

test('it formats titles with plate prefixes', function () {
    expect(FleetCalendarHelper::formatTitle('12-AB-34', 'Insurance'))
        ->toBe('12-AB-34 · Insurance')
        ->and(FleetCalendarHelper::formatTitle(null, 'Insurance'))
        ->toBe('Insurance');
});

test('it resolves display dates based on notice window', function () {
    $rangeStart = Carbon::parse('2026-01-01');
    $rangeEnd = Carbon::parse('2026-01-31');

    $dueDate = Carbon::parse('2026-02-05');
    $displayDate = FleetCalendarHelper::resolveDisplayDate($dueDate, 10, $rangeStart, $rangeEnd);

    expect($displayDate?->toDateString())->toBe('2026-01-26');
});

test('it detects dates within notice windows', function () {
    $today = Carbon::parse('2026-01-10');
    $dueDate = Carbon::parse('2026-01-15');

    expect(FleetCalendarHelper::isWithinNoticeWindow($dueDate, 5, $today))->toBeTrue()
        ->and(FleetCalendarHelper::isWithinNoticeWindow($dueDate, 2, $today))->toBeFalse();
});
