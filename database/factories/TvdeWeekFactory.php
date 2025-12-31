<?php

namespace Database\Factories;

use App\Models\TvdeWeek;
use Illuminate\Database\Eloquent\Factories\Factory;

class TvdeWeekFactory extends Factory
{
    protected $model = TvdeWeek::class;

    public function definition(): array
    {
        $start = now()->startOfWeek();

        return [
            'start_date' => $start->toDateString(),
            'end_date' => $start->copy()->addDays(6)->toDateString(),
        ];
    }
}
