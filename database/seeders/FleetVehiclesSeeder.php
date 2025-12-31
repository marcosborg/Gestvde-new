<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class FleetVehiclesSeeder extends Seeder
{
    public function run(): void
    {
        $vehiclesData = [
            ['brand' => 'HYUNDAI', 'model' => 'i10 Comfort', 'vin' => 'NLHDN51CXSZ388921', 'plate' => 'BZ61IN', 'plate_norm' => 'BZ61IN', 'status' => 'Rented'],
            ['brand' => 'HYUNDAI', 'model' => 'i10 Comfort', 'vin' => 'NLHDN51CXSZ388918', 'plate' => 'BZ63IN', 'plate_norm' => 'BZ63IN', 'status' => 'Rented'],
            ['brand' => 'HYUNDAI', 'model' => 'i10 Comfort', 'vin' => 'NLHDN51C9SZ388974', 'plate' => 'BZ66IN', 'plate_norm' => 'BZ66IN', 'status' => 'Available'],
            ['brand' => 'HYUNDAI', 'model' => 'i10 Comfort', 'vin' => 'NLHDN51C7SZ388973', 'plate' => 'BZ67IN', 'plate_norm' => 'BZ67IN', 'status' => 'In maintenance'],
            ['brand' => 'HYUNDAI', 'model' => 'i10 Comfort', 'vin' => 'NLHDN51C3SZ388923', 'plate' => 'BZ68IN', 'plate_norm' => 'BZ68IN', 'status' => 'Rented'],
            ['brand' => 'HYUNDAI', 'model' => 'i10 Comfort', 'vin' => 'NLHDN51C8SZ388985', 'plate' => 'BZ70IN', 'plate_norm' => 'BZ70IN', 'status' => 'Available'],
            ['brand' => 'HYUNDAI', 'model' => 'i10 Comfort', 'vin' => 'NLHDN51C5SZ388989', 'plate' => 'BZ71IN', 'plate_norm' => 'BZ71IN', 'status' => 'Available'],
            ['brand' => 'HYUNDAI', 'model' => 'i10 Comfort', 'vin' => 'NLHDN51C3SZ388986', 'plate' => 'BZ72IN', 'plate_norm' => 'BZ72IN', 'status' => 'Rented'],
            ['brand' => 'HYUNDAI', 'model' => 'i10 Comfort', 'vin' => 'NLHDN51C5SZ388988', 'plate' => 'BZ73IN', 'plate_norm' => 'BZ73IN', 'status' => 'Available'],
            ['brand' => 'HYUNDAI', 'model' => 'i10 Comfort', 'vin' => 'NLHDN51C0SZ388925', 'plate' => 'BZ74IN', 'plate_norm' => 'BZ74IN', 'status' => 'Rented'],
            ['brand' => 'HYUNDAI', 'model' => 'i10 Comfort', 'vin' => 'NLHDN51C9SZ388976', 'plate' => 'BZ75IN', 'plate_norm' => 'BZ75IN', 'status' => 'Rented'],
            ['brand' => 'HYUNDAI', 'model' => 'i10 Comfort', 'vin' => 'NLHDN51C6SZ388975', 'plate' => 'BZ76IN', 'plate_norm' => 'BZ76IN', 'status' => 'Rented'],
            ['brand' => 'HYUNDAI', 'model' => 'i10 Comfort', 'vin' => 'NLHDN51C6SZ388979', 'plate' => 'BZ77IN', 'plate_norm' => 'BZ77IN', 'status' => 'Rented'],
            ['brand' => 'HYUNDAI', 'model' => 'i10 Comfort', 'vin' => 'NLHDN51C0SZ388978', 'plate' => 'BZ78IN', 'plate_norm' => 'BZ78IN', 'status' => 'Rented'],
            ['brand' => 'HYUNDAI', 'model' => 'i10 Comfort', 'vin' => 'NLHDN51C1SZ388980', 'plate' => 'BZ79IN', 'plate_norm' => 'BZ79IN', 'status' => 'Rented'],
            ['brand' => 'HYUNDAI', 'model' => 'i10 Comfort', 'vin' => 'NLHDN51C3SZ388983', 'plate' => 'BZ80IN', 'plate_norm' => 'BZ80IN', 'status' => 'Rented'],
            ['brand' => 'HYUNDAI', 'model' => 'i10 Comfort', 'vin' => 'NLHDN51C6SZ388984', 'plate' => 'BZ81IN', 'plate_norm' => 'BZ81IN', 'status' => 'Rented'],
            ['brand' => 'HYUNDAI', 'model' => 'i10 Comfort', 'vin' => 'NLHDN51C6SZ388981', 'plate' => 'BZ82IN', 'plate_norm' => 'BZ82IN', 'status' => 'Rented'],
            ['brand' => 'HYUNDAI', 'model' => 'i10 Comfort', 'vin' => 'NLHDN51C1SZ388982', 'plate' => 'BZ83IN', 'plate_norm' => 'BZ83IN', 'status' => 'Available'],
            ['brand' => 'HYUNDAI', 'model' => 'i10 Comfort', 'vin' => 'NLHDN51C8SZ388987', 'plate' => 'BZ84IN', 'plate_norm' => 'BZ84IN', 'status' => 'Available'],
            ['brand' => 'HYUNDAI', 'model' => 'i10 Comfort', 'vin' => 'NLHDN51C9SZ388990', 'plate' => 'BZ85IN', 'plate_norm' => 'BZ85IN', 'status' => 'Available'],
            ['brand' => 'HYUNDAI', 'model' => 'i10 Comfort', 'vin' => 'NLHDN51C3SZ388992', 'plate' => 'BZ86IN', 'plate_norm' => 'BZ86IN', 'status' => 'Available'],
            ['brand' => 'HYUNDAI', 'model' => 'i10 Comfort', 'vin' => 'NLHDN51C0SZ388993', 'plate' => 'BZ87IN', 'plate_norm' => 'BZ87IN', 'status' => 'Available'],
            ['brand' => 'HYUNDAI', 'model' => 'i10 Comfort', 'vin' => 'NLHDN51C1SZ388994', 'plate' => 'BZ88IN', 'plate_norm' => 'BZ88IN', 'status' => 'Available'],
            ['brand' => 'HYUNDAI', 'model' => 'i10 Comfort', 'vin' => 'NLHDN51C0SZ388995', 'plate' => 'BZ89IN', 'plate_norm' => 'BZ89IN', 'status' => 'Available'],
            ['brand' => 'HYUNDAI', 'model' => 'i10 Comfort', 'vin' => 'NLHDN51C0SZ388996', 'plate' => 'BZ90IN', 'plate_norm' => 'BZ90IN', 'status' => 'Available'],
            ['brand' => 'HYUNDAI', 'model' => 'i10 Comfort', 'vin' => 'NLHDN51C0SZ388997', 'plate' => 'BZ91IN', 'plate_norm' => 'BZ91IN', 'status' => 'Available'],
            ['brand' => 'HYUNDAI', 'model' => 'i10 Comfort', 'vin' => 'NLHDN51C0SZ388998', 'plate' => 'BZ92IN', 'plate_norm' => 'BZ92IN', 'status' => 'Available'],
            ['brand' => 'HYUNDAI', 'model' => 'i10 Comfort', 'vin' => 'NLHDN51C0SZ388999', 'plate' => 'BZ93IN', 'plate_norm' => 'BZ93IN', 'status' => 'Available'],
            ['brand' => 'HYUNDAI', 'model' => 'i10 Comfort', 'vin' => 'NLHDN51C0SZ389000', 'plate' => 'BZ94IN', 'plate_norm' => 'BZ94IN', 'status' => 'Available'],
            ['brand' => 'HYUNDAI', 'model' => 'i10 Comfort', 'vin' => 'NLHDN51C0SZ389001', 'plate' => 'BZ95IN', 'plate_norm' => 'BZ95IN', 'status' => 'Rented'],
            ['brand' => 'HYUNDAI', 'model' => 'i10 Comfort', 'vin' => 'NLHDN51C0SZ389002', 'plate' => 'BZ96IN', 'plate_norm' => 'BZ96IN', 'status' => 'Available'],
            ['brand' => 'HYUNDAI', 'model' => 'i10 Comfort', 'vin' => 'NLHDN51C0SZ389003', 'plate' => 'BZ97IN', 'plate_norm' => 'BZ97IN', 'status' => 'Available'],
            ['brand' => 'HYUNDAI', 'model' => 'i10 Comfort', 'vin' => 'NLHDN51C0SZ389004', 'plate' => 'BZ98IN', 'plate_norm' => 'BZ98IN', 'status' => 'Available'],
            ['brand' => 'HYUNDAI', 'model' => 'i10 Comfort', 'vin' => 'NLHDN51C0SZ389005', 'plate' => 'BZ99IN', 'plate_norm' => 'BZ99IN', 'status' => 'Available'],
            ['brand' => 'TESLA', 'model' => 'Model 3', 'vin' => '5YJ3E7EA9MF123456', 'plate' => 'AA11BB', 'plate_norm' => 'AA11BB', 'status' => 'Available'],
            ['brand' => 'TESLA', 'model' => 'Model 3', 'vin' => '5YJ3E7EA9MF654321', 'plate' => 'CC22DD', 'plate_norm' => 'CC22DD', 'status' => 'Available'],
            ['brand' => 'TOYOTA', 'model' => 'Prius', 'vin' => 'JTDKB20U993123456', 'plate' => 'EE33FF', 'plate_norm' => 'EE33FF', 'status' => 'Available'],
            ['brand' => 'TOYOTA', 'model' => 'Prius', 'vin' => 'JTDKB20U993654321', 'plate' => 'GG44HH', 'plate_norm' => 'GG44HH', 'status' => 'Available'],
            ['brand' => 'HYUNDAI', 'model' => 'Ioniq', 'vin' => 'KMHC05LH8HU123456', 'plate' => 'II55JJ', 'plate_norm' => 'II55JJ', 'status' => 'Available'],
            ['brand' => 'HYUNDAI', 'model' => 'Ioniq', 'vin' => 'KMHC05LH8HU654321', 'plate' => 'KK66LL', 'plate_norm' => 'KK66LL', 'status' => 'Available'],
            ['brand' => 'KIA', 'model' => 'Niro', 'vin' => 'KNDCC3LC1J5123456', 'plate' => 'MM77NN', 'plate_norm' => 'MM77NN', 'status' => 'Available'],
            ['brand' => 'KIA', 'model' => 'Niro', 'vin' => 'KNDCC3LC1J5654321', 'plate' => 'OO88PP', 'plate_norm' => 'OO88PP', 'status' => 'Available'],
            ['brand' => 'RENAULT', 'model' => 'Zoe', 'vin' => 'VF1AG000012345678', 'plate' => 'QQ99RR', 'plate_norm' => 'QQ99RR', 'status' => 'Available'],
            ['brand' => 'RENAULT', 'model' => 'Zoe', 'vin' => 'VF1AG000087654321', 'plate' => 'SS00TT', 'plate_norm' => 'SS00TT', 'status' => 'Available'],
            ['brand' => 'PEUGEOT', 'model' => 'e-208', 'vin' => 'VR3UBZKXZMT123456', 'plate' => 'UU11VV', 'plate_norm' => 'UU11VV', 'status' => 'Available'],
            ['brand' => 'PEUGEOT', 'model' => 'e-208', 'vin' => 'VR3UBZKXZMT654321', 'plate' => 'WW22XX', 'plate_norm' => 'WW22XX', 'status' => 'Available'],
            ['brand' => 'NISSAN', 'model' => 'Leaf', 'vin' => '1N4AZ0CP9DC123456', 'plate' => 'YY33ZZ', 'plate_norm' => 'YY33ZZ', 'status' => 'Available'],
            ['brand' => 'NISSAN', 'model' => 'Leaf', 'vin' => '1N4AZ0CP9DC654321', 'plate' => 'AB12CD', 'plate_norm' => 'AB12CD', 'status' => 'Available'],
        ];

        $vehiclesTable = (new Vehicle())->getTable();
        $columns = Schema::getColumnListing($vehiclesTable);

        $existing = Vehicle::query()
            ->select(['id', 'plate'])
            ->get()
            ->mapWithKeys(fn (Vehicle $vehicle): array => [
                self::normalizePlate((string) $vehicle->plate) => $vehicle,
            ]);

        foreach ($vehiclesData as $data) {
            $payload = $this->buildVehiclePayload($data, $columns);
            $normalizedPlate = self::normalizePlate($data['plate']);

            $record = $existing->get($normalizedPlate);

            if ($record) {
                $record->fill($payload);
                $record->save();
                continue;
            }

            Vehicle::query()->create($payload);
        }
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  array<int, string>  $columns
     * @return array<string, mixed>
     */
    protected function buildVehiclePayload(array $data, array $columns): array
    {
        $payload = [];

        if (in_array('plate', $columns, true)) {
            $payload['plate'] = $data['plate'];
        }

        if (in_array('brand', $columns, true)) {
            $payload['brand'] = $data['brand'];
        }

        if (in_array('model', $columns, true)) {
            $payload['model'] = $data['model'];
        }

        if (in_array('year', $columns, true)) {
            $payload['year'] = 2020;
        }

        if (in_array('fuel_type', $columns, true)) {
            $payload['fuel_type'] = $this->guessFuelType($data['brand'], $data['model']);
        }

        if (in_array('acquisition_type', $columns, true)) {
            $payload['acquisition_type'] = 'own';
        }

        if (in_array('status', $columns, true)) {
            $payload['status'] = $this->mapVehicleStatus($data['status']);
        }

        if (in_array('mileage', $columns, true)) {
            $payload['mileage'] = 0;
        }

        if (in_array('notes', $columns, true)) {
            $payload['notes'] = 'Seed: ' . $data['vin'];
        }

        return $payload;
    }

    protected function mapVehicleStatus(string $status): string
    {
        $normalized = strtolower(trim($status));

        return match ($normalized) {
            'rented', 'available' => 'active',
            'in maintenance' => 'inactive',
            default => 'active',
        };
    }

    protected function guessFuelType(string $brand, string $model): string
    {
        $brand = strtolower($brand);
        $model = strtolower($model);

        if (str_contains($brand, 'tesla') || str_contains($model, 'leaf') || str_contains($model, 'zoe') || str_contains($model, 'e-')) {
            return 'electric';
        }

        if (str_contains($model, 'prius') || str_contains($model, 'ioniq') || str_contains($model, 'niro')) {
            return 'hybrid';
        }

        return 'petrol';
    }

    protected static function normalizePlate(string $plate): string
    {
        return strtoupper(preg_replace('/[^A-Z0-9]/i', '', $plate) ?? '');
    }
}
