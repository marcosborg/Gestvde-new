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
        if (! Schema::hasTable('images')) {
            Schema::create('images', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('stock_id')->nullable();
                $table->unsignedBigInteger('imageable_id')->nullable();
                $table->string('imageable_type')->nullable();
                $table->string('path');
                $table->string('disk')->nullable();
                $table->string('type')->nullable();
                $table->text('description')->nullable();
                $table->timestamps();
            });

            return;
        }

        Schema::table('images', function (Blueprint $table) {
            if (! Schema::hasColumn('images', 'imageable_id')) {
                $table->unsignedBigInteger('imageable_id')->nullable();
            }

            if (! Schema::hasColumn('images', 'imageable_type')) {
                $table->string('imageable_type')->nullable();
            }

            if (! Schema::hasColumn('images', 'type')) {
                $table->string('type')->nullable();
            }

            if (! Schema::hasColumn('images', 'description')) {
                $table->text('description')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('images')) {
            return;
        }

        Schema::table('images', function (Blueprint $table) {
            if (Schema::hasColumn('images', 'description')) {
                $table->dropColumn('description');
            }

            if (Schema::hasColumn('images', 'type')) {
                $table->dropColumn('type');
            }

            if (Schema::hasColumn('images', 'imageable_type')) {
                $table->dropColumn('imageable_type');
            }

            if (Schema::hasColumn('images', 'imageable_id')) {
                $table->dropColumn('imageable_id');
            }
        });
    }
};
