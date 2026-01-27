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
        Schema::table('vehicle_expenses', function (Blueprint $table) {
            $table->foreignId('supplier_id')
                ->nullable()
                ->constrained('suppliers')
                ->nullOnDelete()
                ->after('vehicle_id');
            $table->foreignId('expense_category_id')
                ->nullable()
                ->constrained('expense_categories')
                ->nullOnDelete()
                ->after('category');
            $table->string('expense_type')->default('fleet')->after('expense_date');
            $table->string('expense_status')->default('unpaid')->after('expense_type');
        });

        $this->makeVehicleNullable();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->makeVehicleNotNullable();

        Schema::table('vehicle_expenses', function (Blueprint $table) {
            $table->dropConstrainedForeignId('supplier_id');
            $table->dropConstrainedForeignId('expense_category_id');
            $table->dropColumn(['expense_type', 'expense_status']);
        });
    }

    private function makeVehicleNullable(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE vehicle_expenses MODIFY vehicle_id BIGINT UNSIGNED NULL');
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE vehicle_expenses ALTER COLUMN vehicle_id DROP NOT NULL');
        } elseif ($driver === 'sqlsrv') {
            DB::statement('ALTER TABLE vehicle_expenses ALTER COLUMN vehicle_id BIGINT NULL');
        }
    }

    private function makeVehicleNotNullable(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE vehicle_expenses MODIFY vehicle_id BIGINT UNSIGNED NOT NULL');
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE vehicle_expenses ALTER COLUMN vehicle_id SET NOT NULL');
        } elseif ($driver === 'sqlsrv') {
            DB::statement('ALTER TABLE vehicle_expenses ALTER COLUMN vehicle_id BIGINT NOT NULL');
        }
    }
};
