<?php

namespace Database\Seeders;

use App\Models\Driver;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class FleetDriversSeeder extends Seeder
{
    public function run(): void
    {
        $driversData = [
            ['name' => 'Diamantino Silva', 'status' => 'Ativo', 'phone' => '351916994024', 'email' => 'diamantino1975@gmail.com', 'vehicle_plate' => 'BZ-79-IN', 'vehicle_plate_norm' => 'BZ79IN', 'vehicle_brand' => 'Hyundai', 'vehicle_model' => 'I10', 'platforms' => 'Bolt'],
            ['name' => 'Diogo Oliveira', 'status' => 'Ativo', 'phone' => '351965620508', 'email' => 'diogotvdeoliveira@outlook.pt', 'vehicle_plate' => 'BZ-95-IN', 'vehicle_plate_norm' => 'BZ95IN', 'vehicle_brand' => 'Hyundai', 'vehicle_model' => 'I10', 'platforms' => 'Bolt'],
            ['name' => 'Ghanshyam Lamichhane', 'status' => 'Ativo', 'phone' => '351920421770', 'email' => 'subash.lamichhane.780@gmail.com', 'vehicle_plate' => 'BZ-82-IN', 'vehicle_plate_norm' => 'BZ82IN', 'vehicle_brand' => 'Hyundai', 'vehicle_model' => 'I10', 'platforms' => 'Bolt'],
            ['name' => 'João Pires', 'status' => 'Ativo', 'phone' => '351962581513', 'email' => 'califamonte1961@gmail.com', 'vehicle_plate' => 'BZ-75-IN', 'vehicle_plate_norm' => 'BZ75IN', 'vehicle_brand' => 'Hyundai', 'vehicle_model' => 'I10', 'platforms' => 'Bolt'],
            ['name' => 'Kaustav Biswas', 'status' => 'Ativo', 'phone' => '351920061456', 'email' => 'kaustav7m@gmail.com', 'vehicle_plate' => 'BZ-81-IN', 'vehicle_plate_norm' => 'BZ81IN', 'vehicle_brand' => 'Hyundai', 'vehicle_model' => 'I10', 'platforms' => 'Bolt'],
            ['name' => 'Mahmudul Hasan', 'status' => 'Ativo', 'phone' => '351927930157', 'email' => 'mahmudul.alam.h@gmail.com', 'vehicle_plate' => 'BZ-72-IN', 'vehicle_plate_norm' => 'BZ72IN', 'vehicle_brand' => 'Hyundai', 'vehicle_model' => 'I10', 'platforms' => 'Bolt'],
            ['name' => 'Manuela Monteiro', 'status' => 'Ativo', 'phone' => '351919382534', 'email' => 'manuelamonteirov@outlook.com', 'vehicle_plate' => 'BZ-74-IN', 'vehicle_plate_norm' => 'BZ74IN', 'vehicle_brand' => 'Hyundai', 'vehicle_model' => 'I10', 'platforms' => 'Bolt'],
            ['name' => 'Muhammad Ahsan', 'status' => 'Ativo', 'phone' => '351920097913', 'email' => 'ahsanmushtaq.34@gmail.com', 'vehicle_plate' => 'BZ-76-IN', 'vehicle_plate_norm' => 'BZ76IN', 'vehicle_brand' => 'Hyundai', 'vehicle_model' => 'I10', 'platforms' => 'Bolt'],
            ['name' => 'Muhammad Ashraf', 'status' => 'Ativo', 'phone' => '351927243167', 'email' => 'mashraf1133@gmail.com', 'vehicle_plate' => 'BZ-61-IN', 'vehicle_plate_norm' => 'BZ61IN', 'vehicle_brand' => 'Hyundai', 'vehicle_model' => 'I10', 'platforms' => 'Bolt'],
            ['name' => 'Muhammad Haider', 'status' => 'Ativo', 'phone' => '351965102986', 'email' => 'muhammadhaiderkhan76@gmail.com', 'vehicle_plate' => 'BZ-68-IN', 'vehicle_plate_norm' => 'BZ68IN', 'vehicle_brand' => 'Hyundai', 'vehicle_model' => 'I10', 'platforms' => 'Bolt'],
            ['name' => 'Muhammad Imran', 'status' => 'Ativo', 'phone' => '351962469479', 'email' => 'muhammadimranpk81@gmail.com', 'vehicle_plate' => 'BZ-63-IN', 'vehicle_plate_norm' => 'BZ63IN', 'vehicle_brand' => 'Hyundai', 'vehicle_model' => 'I10', 'platforms' => 'Bolt'],
            ['name' => 'Muhammad Nadeem', 'status' => 'Ativo', 'phone' => '351920207296', 'email' => 'nadeem2345@gmail.com', 'vehicle_plate' => 'BZ-78-IN', 'vehicle_plate_norm' => 'BZ78IN', 'vehicle_brand' => 'Hyundai', 'vehicle_model' => 'I10', 'platforms' => 'Bolt'],
            ['name' => 'Muzammil Ali', 'status' => 'Ativo', 'phone' => '351925090249', 'email' => 'muzammilali744@gmail.com', 'vehicle_plate' => 'BZ-73-IN', 'vehicle_plate_norm' => 'BZ73IN', 'vehicle_brand' => 'Hyundai', 'vehicle_model' => 'I10', 'platforms' => 'Bolt'],
            ['name' => 'Rui Franco', 'status' => 'Ativo', 'phone' => '351913659877', 'email' => 'rui.franco.86@gmail.com', 'vehicle_plate' => 'BZ-70-IN', 'vehicle_plate_norm' => 'BZ70IN', 'vehicle_brand' => 'Hyundai', 'vehicle_model' => 'I10', 'platforms' => 'Bolt'],
            ['name' => 'Shahid Iqbal', 'status' => 'Ativo', 'phone' => '351962828349', 'email' => 'shahid.iqbal222@gmail.com', 'vehicle_plate' => 'BZ-77-IN', 'vehicle_plate_norm' => 'BZ77IN', 'vehicle_brand' => 'Hyundai', 'vehicle_model' => 'I10', 'platforms' => 'Bolt'],
            ['name' => 'Shujaat Ali', 'status' => 'Ativo', 'phone' => '351966733920', 'email' => 'ali.shujaat79@gmail.com', 'vehicle_plate' => 'BZ-71-IN', 'vehicle_plate_norm' => 'BZ71IN', 'vehicle_brand' => 'Hyundai', 'vehicle_model' => 'I10', 'platforms' => 'Bolt'],
            ['name' => 'Suleman Inayat', 'status' => 'Ativo', 'phone' => '351969317199', 'email' => 'suleman.inayat@gmail.com', 'vehicle_plate' => 'BZ-80-IN', 'vehicle_plate_norm' => 'BZ80IN', 'vehicle_brand' => 'Hyundai', 'vehicle_model' => 'I10', 'platforms' => 'Bolt'],
            ['name' => 'Waqas Ahmad', 'status' => 'Ativo', 'phone' => '351963305822', 'email' => 'waqasahmad.ch@gmail.com', 'vehicle_plate' => 'BZ-67-IN', 'vehicle_plate_norm' => 'BZ67IN', 'vehicle_brand' => 'Hyundai', 'vehicle_model' => 'I10', 'platforms' => 'Bolt'],
            ['name' => 'Waseem Akhtar', 'status' => 'Ativo', 'phone' => '351963782365', 'email' => 'waseemakhtar352@gmail.com', 'vehicle_plate' => 'BZ-66-IN', 'vehicle_plate_norm' => 'BZ66IN', 'vehicle_brand' => 'Hyundai', 'vehicle_model' => 'I10', 'platforms' => 'Bolt'],
            ['name' => 'Zeeshan Haider', 'status' => 'Ativo', 'phone' => '351962213782', 'email' => 'zeeshanhaider@gmail.com', 'vehicle_plate' => 'BZ-86-IN', 'vehicle_plate_norm' => 'BZ86IN', 'vehicle_brand' => 'Hyundai', 'vehicle_model' => 'I10', 'platforms' => 'Bolt'],
        ];

        $driversTable = (new Driver())->getTable();
        $columns = Schema::getColumnListing($driversTable);

        foreach ($driversData as $data) {
            $payload = $this->buildDriverPayload($data, $columns);

            if (in_array('email', $columns, true)) {
                Driver::query()->updateOrCreate(
                    ['email' => $data['email']],
                    $payload
                );
                continue;
            }

            Driver::query()->updateOrCreate(
                ['name' => $data['name'], 'phone' => $data['phone']],
                $payload
            );
        }
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  array<int, string>  $columns
     * @return array<string, mixed>
     */
    protected function buildDriverPayload(array $data, array $columns): array
    {
        $payload = [];

        if (in_array('name', $columns, true)) {
            $payload['name'] = $data['name'];
        }

        if (in_array('phone', $columns, true)) {
            $payload['phone'] = $data['phone'];
        }

        if (in_array('email', $columns, true)) {
            $payload['email'] = $data['email'];
        }

        if (in_array('active', $columns, true)) {
            $payload['active'] = $data['status'] === 'Ativo';
        } elseif (in_array('status', $columns, true)) {
            $payload['status'] = $data['status'];
        }

        return $payload;
    }
}
