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
        Schema::create('vehicle_checkins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles')->cascadeOnDelete();
            $table->foreignId('driver_id')->nullable()->constrained('drivers')->nullOnDelete();
            $table->string('check_type');
            $table->dateTime('occurred_at');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('vehicle_checkin_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('checkin_id')->constrained('vehicle_checkins')->cascadeOnDelete();
            $table->string('position');
            $table->string('path');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('vehicle_damages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('checkin_id')->constrained('vehicle_checkins')->cascadeOnDelete();
            $table->string('zone');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('vehicle_damage_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('damage_id')->constrained('vehicle_damages')->cascadeOnDelete();
            $table->string('path');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_damage_photos');
        Schema::dropIfExists('vehicle_damages');
        Schema::dropIfExists('vehicle_checkin_photos');
        Schema::dropIfExists('vehicle_checkins');
    }
};
