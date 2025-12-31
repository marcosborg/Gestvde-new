<?php

namespace Database\Seeders;

use App\Models\Driver;
use App\Models\Vehicle;
use App\Models\VehicleRental;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class FleetAssignmentsSeeder extends Seeder
{
    public function run(): void
    {
        if (! class_exists(VehicleRental::class)) {
            return;
        }

        $rentalsTable = (new VehicleRental())->getTable();

        if (! Schema::hasTable($rentalsTable)) {
            return;
        }

        $rentalsColumns = Schema::getColumnListing($rentalsTable);

        $driversData = [
            ['email' => 'diamantino1975@gmail.com', 'vehicle_plate_norm' => 'BZ79IN'],
            ['email' => 'diogotvdeoliveira@outlook.pt', 'vehicle_plate_norm' => 'BZ95IN'],
            ['email' => 'subash.lamichhane.780@gmail.com', 'vehicle_plate_norm' => 'BZ82IN'],
            ['email' => 'califamonte1961@gmail.com', 'vehicle_plate_norm' => 'BZ75IN'],
            ['email' => 'kaustav7m@gmail.com', 'vehicle_plate_norm' => 'BZ81IN'],
            ['email' => 'mahmudul.alam.h@gmail.com', 'vehicle_plate_norm' => 'BZ72IN'],
            ['email' => 'manuelamonteirov@outlook.com', 'vehicle_plate_norm' => 'BZ74IN'],
            ['email' => 'ahsanmushtaq.34@gmail.com', 'vehicle_plate_norm' => 'BZ76IN'],
            ['email' => 'mashraf1133@gmail.com', 'vehicle_plate_norm' => 'BZ61IN'],
            ['email' => 'muhammadhaiderkhan76@gmail.com', 'vehicle_plate_norm' => 'BZ68IN'],
            ['email' => 'muhammadimranpk81@gmail.com', 'vehicle_plate_norm' => 'BZ63IN'],
            ['email' => 'nadeem2345@gmail.com', 'vehicle_plate_norm' => 'BZ78IN'],
            ['email' => 'muzammilali744@gmail.com', 'vehicle_plate_norm' => 'BZ73IN'],
            ['email' => 'rui.franco.86@gmail.com', 'vehicle_plate_norm' => 'BZ70IN'],
            ['email' => 'shahid.iqbal222@gmail.com', 'vehicle_plate_norm' => 'BZ77IN'],
            ['email' => 'ali.shujaat79@gmail.com', 'vehicle_plate_norm' => 'BZ71IN'],
            ['email' => 'suleman.inayat@gmail.com', 'vehicle_plate_norm' => 'BZ80IN'],
            ['email' => 'waqasahmad.ch@gmail.com', 'vehicle_plate_norm' => 'BZ67IN'],
            ['email' => 'waseemakhtar352@gmail.com', 'vehicle_plate_norm' => 'BZ66IN'],
            ['email' => 'zeeshanhaider@gmail.com', 'vehicle_plate_norm' => 'BZ86IN'],
        ];

        $vehicles = Vehicle::query()
            ->select(['id', 'plate'])
            ->get()
            ->mapWithKeys(fn (Vehicle $vehicle): array => [
                self::normalizePlate((string) $vehicle->plate) => $vehicle->id,
            ]);

        $drivers = Driver::query()
            ->select(['id', 'email'])
            ->get()
            ->mapWithKeys(fn (Driver $driver): array => [
                strtolower((string) $driver->email) => $driver->id,
            ]);

        foreach ($driversData as $data) {
            $driverId = $drivers->get(strtolower($data['email']));
            $vehicleId = $vehicles->get(self::normalizePlate($data['vehicle_plate_norm']));

            if (! $driverId || ! $vehicleId) {
                continue;
            }

            $payload = [
                'vehicle_id' => $vehicleId,
                'driver_id' => $driverId,
            ];

            if (in_array('start_date', $rentalsColumns, true)) {
                $payload['start_date'] = now()->subDays(30)->toDateString();
            }

            if (in_array('end_date', $rentalsColumns, true)) {
                $payload['end_date'] = null;
            }

            if (in_array('weekly_price', $rentalsColumns, true)) {
                $payload['weekly_price'] = 0;
            }

            if (in_array('notes', $rentalsColumns, true)) {
                $payload['notes'] = 'Associacao gerada por seed';
            }

            $match = [
                'vehicle_id' => $payload['vehicle_id'],
                'driver_id' => $payload['driver_id'],
            ];

            if (in_array('start_date', $rentalsColumns, true)) {
                $match['start_date'] = $payload['start_date'] ?? now()->subDays(30)->toDateString();
            }

            VehicleRental::query()->updateOrCreate($match, $payload);
        }
    }

    protected static function normalizePlate(string $plate): string
    {
        return strtoupper(preg_replace('/[^A-Z0-9]/i', '', $plate) ?? '');
    }
}
