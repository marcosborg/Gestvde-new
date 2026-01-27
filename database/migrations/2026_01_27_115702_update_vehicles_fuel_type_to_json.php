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
            $hasFuelType = Schema::hasColumn('vehicles', 'fuel_type');
            $hasFuelTypeJson = Schema::hasColumn('vehicles', 'fuel_type_json');

            if (! $hasFuelTypeJson) {
                DB::statement('ALTER TABLE vehicles ADD COLUMN fuel_type_json JSON NULL');
                $hasFuelTypeJson = true;
            }

            if ($hasFuelType && $hasFuelTypeJson) {
                DB::statement("
                    UPDATE vehicles
                    SET fuel_type_json = CASE
                        WHEN fuel_type IS NULL OR fuel_type = '' THEN NULL
                        WHEN JSON_VALID(fuel_type) THEN fuel_type
                        ELSE JSON_ARRAY(fuel_type)
                    END
                ");
                DB::statement('ALTER TABLE vehicles DROP COLUMN fuel_type');
            }

            if ($hasFuelTypeJson && ! Schema::hasColumn('vehicles', 'fuel_type')) {
                DB::statement('ALTER TABLE vehicles CHANGE fuel_type_json fuel_type JSON NULL');
            }
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE vehicles ALTER COLUMN fuel_type TYPE jsonb USING to_jsonb(fuel_type)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            $hasFuelType = Schema::hasColumn('vehicles', 'fuel_type');
            $hasFuelTypeString = Schema::hasColumn('vehicles', 'fuel_type_string');

            if (! $hasFuelTypeString) {
                DB::statement('ALTER TABLE vehicles ADD COLUMN fuel_type_string VARCHAR(255) NULL');
                $hasFuelTypeString = true;
            }

            if ($hasFuelType && $hasFuelTypeString) {
                DB::statement("
                    UPDATE vehicles
                    SET fuel_type_string = CASE
                        WHEN fuel_type IS NULL THEN NULL
                        ELSE JSON_UNQUOTE(JSON_EXTRACT(fuel_type, '$[0]'))
                    END
                ");
                DB::statement('ALTER TABLE vehicles DROP COLUMN fuel_type');
            }

            if ($hasFuelTypeString && ! Schema::hasColumn('vehicles', 'fuel_type')) {
                DB::statement('ALTER TABLE vehicles CHANGE fuel_type_string fuel_type VARCHAR(255) NULL');
            }
        } elseif ($driver === 'pgsql') {
            DB::statement('UPDATE vehicles SET fuel_type = fuel_type->>0 WHERE fuel_type IS NOT NULL');
            DB::statement('ALTER TABLE vehicles ALTER COLUMN fuel_type TYPE varchar(255) USING fuel_type::text');
        }
    }
};
