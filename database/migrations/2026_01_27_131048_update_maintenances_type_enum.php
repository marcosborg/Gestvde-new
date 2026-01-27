<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE maintenances MODIFY type ENUM('preventive', 'corrective', 'repair_interior', 'repair_exterior') NOT NULL");
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE maintenances DROP CONSTRAINT IF EXISTS maintenances_type_check');
            DB::statement("ALTER TABLE maintenances ADD CONSTRAINT maintenances_type_check CHECK (type IN ('preventive', 'corrective', 'repair_interior', 'repair_exterior'))");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE maintenances MODIFY type ENUM('preventive', 'corrective') NOT NULL");
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE maintenances DROP CONSTRAINT IF EXISTS maintenances_type_check');
            DB::statement("ALTER TABLE maintenances ADD CONSTRAINT maintenances_type_check CHECK (type IN ('preventive', 'corrective'))");
        }
    }
};
