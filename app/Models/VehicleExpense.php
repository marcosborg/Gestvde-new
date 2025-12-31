<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class VehicleExpense extends Model
{
    protected $guarded = [];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
        'recurring' => 'boolean',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }
}
