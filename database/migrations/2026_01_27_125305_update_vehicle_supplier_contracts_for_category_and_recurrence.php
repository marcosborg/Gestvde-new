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
        Schema::table('vehicle_supplier_contracts', function (Blueprint $table) {
            $table->enum('category', ['fleet', 'operations', 'administration'])->default('fleet')->after('supplier_id');
            $table->boolean('recurring')->default(false)->after('monthly_cost');
            $table->enum('recurrence_interval', ['monthly', 'quarterly', 'yearly'])->nullable()->after('recurring');
        });

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE vehicle_supplier_contracts MODIFY vehicle_id BIGINT UNSIGNED NULL');
            DB::statement('
                UPDATE vehicle_supplier_contracts AS contracts
                INNER JOIN suppliers ON suppliers.id = contracts.supplier_id
                SET contracts.recurring = suppliers.is_recurring
                WHERE suppliers.is_recurring = 1
            ');
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE vehicle_supplier_contracts ALTER COLUMN vehicle_id DROP NOT NULL');
            DB::statement('
                UPDATE vehicle_supplier_contracts AS contracts
                SET recurring = suppliers.is_recurring
                FROM suppliers
                WHERE suppliers.id = contracts.supplier_id
                  AND suppliers.is_recurring = true
            ');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE vehicle_supplier_contracts MODIFY vehicle_id BIGINT UNSIGNED NOT NULL');
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE vehicle_supplier_contracts ALTER COLUMN vehicle_id SET NOT NULL');
        }

        Schema::table('vehicle_supplier_contracts', function (Blueprint $table) {
            $table->dropColumn([
                'category',
                'recurring',
                'recurrence_interval',
            ]);
        });
    }
};
