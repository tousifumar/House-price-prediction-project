<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PriceAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'city_id',
        'locality_id',
        'property_type',
        'bedrooms',
        'target_price',
        'condition',
        'is_active',
        'last_triggered_at',
    ];

    protected $casts = [
        'target_price' => 'float',
        'is_active' => 'boolean',
        'last_triggered_at' => 'datetime',
        'bedrooms' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function locality(): BelongsTo
    {
        return $this->belongsTo(Locality::class);
    }
}
