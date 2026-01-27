<?php

use App\Models\Document;
use App\Models\Event;
use App\Models\Maintenance;
use App\Models\Vehicle;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('events:sync', function () {
    $maintenanceEvents = 0;
    $documentEvents = 0;

    $maintenances = Maintenance::query()
        ->whereNotNull('next_due_date')
        ->get();

    foreach ($maintenances as $maintenance) {
        Event::query()->updateOrCreate(
            [
                'event_type' => 'maintenance',
                'description' => 'source:maintenance:' . $maintenance->id,
            ],
            [
                'vehicle_id' => $maintenance->vehicle_id,
                'title' => $maintenance->description ?: 'Maintenance due',
                'event_date' => $maintenance->next_due_date,
                'notify_before_days' => 0,
                'completed' => $maintenance->status === 'completed',
            ]
        );

        $maintenanceEvents++;
    }

    $documents = Document::query()
        ->whereNotNull('valid_until')
        ->get();

    foreach ($documents as $document) {
        $vehicleId = $document->documentable_type === Vehicle::class
            ? $document->documentable_id
            : null;

        Event::query()->updateOrCreate(
            [
                'event_type' => 'document',
                'description' => 'source:document:' . $document->id,
            ],
            [
                'vehicle_id' => $vehicleId,
                'title' => $document->title,
                'event_date' => $document->valid_until,
                'notify_before_days' => $document->notify_before_days ?? 0,
                'completed' => false,
            ]
        );

        $documentEvents++;
    }

    $this->info("Synced {$maintenanceEvents} maintenance events and {$documentEvents} document events.");
})->purpose('Sync maintenance and document events to the calendar.');
