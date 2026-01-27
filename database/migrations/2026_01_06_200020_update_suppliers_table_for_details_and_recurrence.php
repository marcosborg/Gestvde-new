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
        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('legal_name')->nullable()->after('name');
            $table->string('commercial_name')->nullable()->after('legal_name');
            $table->text('address')->nullable()->after('nif');
            $table->string('contact_person')->nullable()->after('phone');
            $table->boolean('is_recurring')->default(false)->after('contact_person');
        });

        if (Schema::hasColumn('suppliers', 'nif')) {
            $uniqueIndex = DB::selectOne("
                SELECT COUNT(1) AS count
                FROM information_schema.statistics
                WHERE table_schema = DATABASE()
                  AND table_name = 'suppliers'
                  AND index_name = 'suppliers_nif_unique'
            ");

            if ($uniqueIndex && (int) $uniqueIndex->count > 0) {
                DB::statement('ALTER TABLE suppliers DROP INDEX suppliers_nif_unique');
            }

            DB::statement('ALTER TABLE suppliers MODIFY nif VARCHAR(255) NULL');

            $index = DB::selectOne("
                SELECT COUNT(1) AS count
                FROM information_schema.statistics
                WHERE table_schema = DATABASE()
                  AND table_name = 'suppliers'
                  AND index_name = 'suppliers_nif_index'
            ");

            if (! $index || (int) $index->count === 0) {
                DB::statement('CREATE INDEX suppliers_nif_index ON suppliers (nif)');
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('suppliers', 'nif')) {
            $index = DB::selectOne("
                SELECT COUNT(1) AS count
                FROM information_schema.statistics
                WHERE table_schema = DATABASE()
                  AND table_name = 'suppliers'
                  AND index_name = 'suppliers_nif_index'
            ");

            if ($index && (int) $index->count > 0) {
                DB::statement('ALTER TABLE suppliers DROP INDEX suppliers_nif_index');
            }

            DB::statement('ALTER TABLE suppliers MODIFY nif VARCHAR(255) NOT NULL');
            DB::statement('ALTER TABLE suppliers ADD UNIQUE INDEX suppliers_nif_unique (nif)');
        }

        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn([
                'legal_name',
                'commercial_name',
                'address',
                'contact_person',
                'is_recurring',
            ]);
        });
    }
};
