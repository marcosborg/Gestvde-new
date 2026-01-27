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
        Schema::table('drivers', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->string('nif')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('nationality')->nullable();
            $table->string('marital_status')->nullable();
            $table->text('address')->nullable();
            $table->text('notes')->nullable();
            $table->text('documentation')->nullable();
            $table->boolean('blacklisted')->default(false);
            $table->boolean('on_vacation')->default(false);
            $table->string('identity_document_type')->nullable();
            $table->string('identity_document_number')->nullable();
            $table->date('identity_document_valid_until')->nullable();
            $table->string('sns_number')->nullable();
            $table->string('niss_number')->nullable();
            $table->string('driving_license_number')->nullable();
            $table->date('driving_license_issued_at')->nullable();
            $table->date('driving_license_valid_until')->nullable();
            $table->string('driving_license_category')->nullable();
            $table->string('tvde_certificate_number')->nullable();
            $table->date('tvde_certificate_valid_until')->nullable();
            $table->boolean('platform_uber')->default(false);
            $table->boolean('platform_bolt')->default(false);
            $table->string('platform_other')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->string('bank_account_holder')->nullable();
            $table->string('bank_iban')->nullable();
            $table->decimal('deposit_amount', 10, 2)->nullable();
            $table->date('deposit_paid_at')->nullable();
            $table->string('deposit_payment_method')->nullable();
            $table->date('activity_started_at')->nullable();
            $table->date('activity_ended_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('company_id');
            $table->dropColumn([
                'nif',
                'birth_date',
                'nationality',
                'marital_status',
                'address',
                'notes',
                'documentation',
                'blacklisted',
                'on_vacation',
                'identity_document_type',
                'identity_document_number',
                'identity_document_valid_until',
                'sns_number',
                'niss_number',
                'driving_license_number',
                'driving_license_issued_at',
                'driving_license_valid_until',
                'driving_license_category',
                'tvde_certificate_number',
                'tvde_certificate_valid_until',
                'platform_uber',
                'platform_bolt',
                'platform_other',
                'emergency_contact_name',
                'emergency_contact_phone',
                'bank_account_holder',
                'bank_iban',
                'deposit_amount',
                'deposit_paid_at',
                'deposit_payment_method',
                'activity_started_at',
                'activity_ended_at',
            ]);
        });
    }
};
