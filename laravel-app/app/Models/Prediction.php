<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Prediction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'property_id',
        'model_id',
        'property_type',
        'transaction_type',
        'city_id',
        'locality_id',
        'area',
        'carpet_area',
        'bedrooms',
        'bathrooms',
        'balconies',
        'floor',
        'total_floors',
        'property_age',
        'furnishing',
        'parking',
        'latitude',
        'longitude',
        'amenities_json',
        'input_data',
        'predicted_price',
        'min_price',
        'max_price',
        'price_per_sqft',
        'confidence_score',
        'currency',
        'user_asking_price',
    ];

    protected $casts = [
        'area' => 'float',
        'carpet_area' => 'float',
        'bedrooms' => 'integer',
        'bathrooms' => 'integer',
        'balconies' => 'integer',
        'floor' => 'integer',
        'total_floors' => 'integer',
        'property_age' => 'integer',
        'parking' => 'boolean',
        'latitude' => 'float',
        'longitude' => 'float',
        'amenities_json' => 'array',
        'input_data' => 'array',
        'predicted_price' => 'float',
        'min_price' => 'float',
        'max_price' => 'float',
        'price_per_sqft' => 'float',
        'confidence_score' => 'float',
        'user_asking_price' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function locality(): BelongsTo
    {
        return $this->belongsTo(Locality::class);
    }

    public function model(): BelongsTo
    {
        return $this->belongsTo(MlModel::class, 'model_id');
    }

    public function factors(): HasMany
    {
        return $this->hasMany(PredictionFactor::class);
    }

    public function getFormattedPriceAttribute(): string
    {
        return self::formatPriceInIndianSystem($this->predicted_price);
    }

    public function getFormattedMinPriceAttribute(): string
    {
        return self::formatPriceInIndianSystem($this->min_price);
    }

    public function getFormattedMaxPriceAttribute(): string
    {
        return self::formatPriceInIndianSystem($this->max_price);
    }

    public static function formatPriceInIndianSystem(float $price): string
    {
        if ($price >= 10000000) {
            return '₹' . number_format($price / 10000000, 2) . ' Cr';
        } elseif ($price >= 100000) {
            return '₹' . number_format($price / 100000, 2) . ' L';
        }
        return '₹' . number_format($price, 0);
    }
}
