<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->decimal('leasing_entry_amount', 12, 2)->nullable();
            $table->decimal('leasing_monthly_installment', 12, 2)->nullable();
            $table->decimal('leasing_initial_installment', 12, 2)->nullable();
            $table->string('leasing_contract_number')->nullable();
            $table->json('vehicle_documents')->nullable();
            $table->string('chassis_number')->nullable();
            $table->string('tire_type')->nullable();
            $table->string('motorization_type')->nullable();
            $table->unsignedSmallInteger('seats_count')->nullable();
            $table->string('color')->nullable();
            $table->string('gps_id')->nullable();
            $table->date('registration_date')->nullable();
            $table->string('fleet_insurance_policy_number')->nullable();
            $table->string('dua_number')->nullable();
            $table->string('via_verde_id')->nullable();
            $table->string('fleet_card_number')->nullable();
            $table->string('fleet_card_code')->nullable();
        });

        DB::statement("ALTER TABLE vehicles MODIFY acquisition_type ENUM('own', 'third_party', 'leasing') NOT NULL");
        DB::table('vehicles')->update(['acquisition_type' => 'leasing']);
        DB::statement("ALTER TABLE vehicles MODIFY acquisition_type ENUM('leasing') NOT NULL");

        DB::statement("ALTER TABLE vehicles MODIFY status ENUM('active', 'inactive', 'sold', 'rented', 'available', 'missing_docs', 'maintenance', 'in_fleet') NOT NULL");
        DB::table('vehicles')->where('status', 'active')->update(['status' => 'available']);
        DB::table('vehicles')->whereIn('status', ['inactive', 'sold'])->update(['status' => 'in_fleet']);
        DB::statement("ALTER TABLE vehicles MODIFY status ENUM('rented', 'available', 'missing_docs', 'maintenance', 'in_fleet') NOT NULL DEFAULT 'available'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE vehicles MODIFY status ENUM('active', 'inactive', 'sold', 'rented', 'available', 'missing_docs', 'maintenance', 'in_fleet') NOT NULL");
        DB::table('vehicles')->where('status', 'available')->update(['status' => 'active']);
        DB::table('vehicles')->whereIn('status', ['rented', 'missing_docs', 'maintenance', 'in_fleet'])->update(['status' => 'inactive']);
        DB::statement("ALTER TABLE vehicles MODIFY status ENUM('active', 'inactive', 'sold') NOT NULL DEFAULT 'active'");

        DB::statement("ALTER TABLE vehicles MODIFY acquisition_type ENUM('own', 'third_party', 'leasing') NOT NULL");
        DB::table('vehicles')->update(['acquisition_type' => 'own']);
        DB::statement("ALTER TABLE vehicles MODIFY acquisition_type ENUM('own', 'third_party') NOT NULL");

        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn([
                'leasing_entry_amount',
                'leasing_monthly_installment',
                'leasing_initial_installment',
                'leasing_contract_number',
                'vehicle_documents',
                'chassis_number',
                'tire_type',
                'motorization_type',
                'seats_count',
                'color',
                'gps_id',
                'registration_date',
                'fleet_insurance_policy_number',
                'dua_number',
                'via_verde_id',
                'fleet_card_number',
                'fleet_card_code',
            ]);
        });
    }
};
