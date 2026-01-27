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
        Schema::table('vehicle_damages', function (Blueprint $table) {
            $table->foreignId('maintenance_id')->nullable()->after('checkin_id')->constrained('maintenances')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicle_damages', function (Blueprint $table) {
            $table->dropConstrainedForeignId('maintenance_id');
        });
    }
};
