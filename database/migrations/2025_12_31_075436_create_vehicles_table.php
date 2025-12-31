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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('plate')->unique();
            $table->string('brand');
            $table->string('model');
            $table->unsignedSmallInteger('year');
            $table->string('fuel_type');
            $table->enum('acquisition_type', ['own', 'third_party']);
            $table->decimal('acquisition_value', 12, 2)->nullable();
            $table->date('acquisition_date')->nullable();
            $table->decimal('annual_depreciation_percent', 5, 2)->nullable();
            $table->unsignedInteger('mileage')->default(0);
            $table->enum('status', ['active', 'inactive', 'sold'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
