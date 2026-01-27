<?php

namespace App\Services;

use App\Models\DriverVehicleAssignment;
use App\Models\TvdeWeek;
use Illuminate\Support\Carbon;

class RentalCalculator
{
    /**
     * @return array{total_rent: float, breakdown: array<int, array<string, mixed>>}
     */
    public function calculateForDriverAndWeek(int $driverId, int $tvdeWeekId): array
    {
        $week = TvdeWeek::query()->findOrFail($tvdeWeekId);
        $weekStart = Carbon::parse($week->start_date)->startOfDay();
        $weekEnd = Carbon::parse($week->end_date)->startOfDay();

        $assignments = DriverVehicleAssignment::query()
            ->forDriver($driverId)
            ->overlaps($weekStart, $weekEnd)
            ->with('vehicle')
            ->get();

        $total = 0.0;
        $breakdown = [];

        foreach ($assignments as $assignment) {
            $assignmentStart = Carbon::parse($assignment->start_date)->startOfDay();
            $assignmentEnd = $assignment->end_date ? Carbon::parse($assignment->end_date)->startOfDay() : null;

            $effectiveStart = $assignmentStart->greaterThan($weekStart) ? $assignmentStart : $weekStart;
            $effectiveEnd = $assignmentEnd && $assignmentEnd->lessThan($weekEnd) ? $assignmentEnd : $weekEnd;

            if ($effectiveEnd->lt($effectiveStart)) {
                continue;
            }

            $daysInWeek = $effectiveStart->diffInDays($effectiveEnd) + 1;
            $weeklyRate = (float) ($assignment->weekly_rate_override ?? $assignment->vehicle?->weekly_rent ?? 0);
            $dailyRateRaw = $weeklyRate / 7;
            $subtotal = round($dailyRateRaw * $daysInWeek, 2);

            $total += $subtotal;

            $breakdown[] = [
                'assignment_id' => $assignment->id,
                'vehicle_id' => $assignment->vehicle_id,
                'vehicle_plate' => $assignment->vehicle?->plate,
                'days_in_week' => $daysInWeek,
                'weekly_rate_used' => round($weeklyRate, 2),
                'daily_rate' => round($dailyRateRaw, 2),
                'subtotal' => $subtotal,
            ];
        }

        return [
            'total_rent' => round($total, 2),
            'breakdown' => $breakdown,
        ];
    }
}
