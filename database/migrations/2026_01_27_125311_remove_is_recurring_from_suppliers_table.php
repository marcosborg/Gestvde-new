<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasColumn('suppliers', 'is_recurring')) {
            Schema::table('suppliers', function (Blueprint $table) {
                $table->dropColumn('is_recurring');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('suppliers', 'is_recurring')) {
            Schema::table('suppliers', function (Blueprint $table) {
                $table->boolean('is_recurring')->default(false)->after('contact_person');
            });
        }
    }
};
