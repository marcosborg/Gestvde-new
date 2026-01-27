<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'active' => 'boolean',
        'blacklisted' => 'boolean',
        'on_vacation' => 'boolean',
        'platform_uber' => 'boolean',
        'platform_bolt' => 'boolean',
        'documentation' => 'array',
        'birth_date' => 'date',
        'identity_document_valid_until' => 'date',
        'driving_license_issued_at' => 'date',
        'driving_license_valid_until' => 'date',
        'tvde_certificate_valid_until' => 'date',
        'deposit_paid_at' => 'date',
        'activity_started_at' => 'date',
        'activity_ended_at' => 'date',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function rentals(): HasMany
    {
        return $this->hasMany(VehicleRental::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(DriverVehicleAssignment::class);
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function checkins(): HasMany
    {
        return $this->hasMany(VehicleCheckin::class);
    }
}
