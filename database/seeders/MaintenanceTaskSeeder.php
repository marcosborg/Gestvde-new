<?php

namespace Database\Seeders;

use App\Models\MaintenanceTask;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MaintenanceTaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tasks = [
            'Oil filter and oil change',
            'Service 1',
            'Service 2',
            'Front tires change',
            'Rear tires change',
        ];

        foreach ($tasks as $task) {
            MaintenanceTask::query()->updateOrCreate(
                ['slug' => Str::slug($task)],
                ['name' => $task],
            );
        }
    }
}
