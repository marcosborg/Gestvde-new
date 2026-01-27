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
        Schema::table('vehicle_checkin_photos', function (Blueprint $table) {
            $table->enum('section', ['exterior', 'interior'])->default('exterior')->after('checkin_id');
        });

        DB::table('vehicle_checkin_photos')
            ->where('position', 'interior')
            ->update(['section' => 'interior']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicle_checkin_photos', function (Blueprint $table) {
            $table->dropColumn('section');
        });
    }
};
