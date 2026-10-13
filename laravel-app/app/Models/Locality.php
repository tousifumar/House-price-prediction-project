<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Locality extends Model
{
    use HasFactory;

    protected $fillable = [
        'city_id',
        'name',
        'slug',
        'pincode',
        'latitude',
        'longitude',
        'avg_price',
        'avg_price_per_sqft',
        'total_properties',
        'growth_rate',
        'location_score',
        'is_active',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'avg_price' => 'float',
        'avg_price_per_sqft' => 'float',
        'total_properties' => 'integer',
        'growth_rate' => 'float',
        'location_score' => 'float',
        'is_active' => 'boolean',
    ];

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }

    public function predictions(): HasMany
    {
        return $this->hasMany(Prediction::class);
    }

    public function marketData(): HasMany
    {
        return $this->hasMany(MarketData::class);
    }
}
