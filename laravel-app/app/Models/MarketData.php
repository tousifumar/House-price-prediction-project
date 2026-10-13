<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketData extends Model
{
    use HasFactory;

    protected $table = 'market_data';

    protected $fillable = [
        'city_id',
        'locality_id',
        'property_type',
        'year',
        'month',
        'avg_price',
        'avg_price_per_sqft',
        'transaction_count',
        'yoy_growth',
        'demand_index',
    ];

    protected $casts = [
        'year' => 'integer',
        'month' => 'integer',
        'avg_price' => 'float',
        'avg_price_per_sqft' => 'float',
        'transaction_count' => 'integer',
        'yoy_growth' => 'float',
        'demand_index' => 'float',
    ];

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function locality(): BelongsTo
    {
        return $this->belongsTo(Locality::class);
    }
}
