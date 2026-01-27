<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MaintenanceTask extends Model
{
    /** @use HasFactory<\Database\Factories\MaintenanceTaskFactory> */
    use HasFactory;

    protected $guarded = [];

    public function maintenances(): BelongsToMany
    {
        return $this->belongsToMany(Maintenance::class)->withTimestamps();
    }
}
