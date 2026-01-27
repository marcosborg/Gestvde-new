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
        Schema::table('vehicle_checkins', function (Blueprint $table) {
            $table->enum('exterior_condition', ['good', 'fair', 'poor'])->nullable()->after('check_type');
            $table->enum('interior_condition', ['good', 'fair', 'poor'])->nullable()->after('exterior_condition');
            $table->enum('tires_condition', ['good', 'fair', 'poor'])->nullable()->after('interior_condition');
            $table->enum('cleanliness', ['clean', 'average', 'dirty'])->nullable()->after('tires_condition');
            $table->enum('fuel_level', ['empty', 'quarter', 'half', 'three_quarters', 'full'])->nullable()->after('cleanliness');
        });

        DB::table('vehicle_checkins')
            ->where('check_type', 'in')
            ->update(['check_type' => 'check_in']);

        DB::table('vehicle_checkins')
            ->where('check_type', 'out')
            ->update(['check_type' => 'check_out']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('vehicle_checkins')
            ->where('check_type', 'check_in')
            ->update(['check_type' => 'in']);

        DB::table('vehicle_checkins')
            ->where('check_type', 'check_out')
            ->update(['check_type' => 'out']);

        Schema::table('vehicle_checkins', function (Blueprint $table) {
            $table->dropColumn([
                'exterior_condition',
                'interior_condition',
                'tires_condition',
                'cleanliness',
                'fuel_level',
            ]);
        });
    }
};
