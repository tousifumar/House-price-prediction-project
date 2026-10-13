<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Property extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'locality_id',
        'title',
        'property_type',
        'transaction_type',
        'price',
        'area',
        'carpet_area',
        'bedrooms',
        'bathrooms',
        'balconies',
        'floor',
        'total_floors',
        'property_age',
        'construction_year',
        'furnishing',
        'condition',
        'parking',
        'latitude',
        'longitude',
        'status',
    ];

    protected $casts = [
        'price' => 'float',
        'area' => 'float',
        'carpet_area' => 'float',
        'bedrooms' => 'integer',
        'bathrooms' => 'integer',
        'balconies' => 'integer',
        'floor' => 'integer',
        'total_floors' => 'integer',
        'property_age' => 'integer',
        'construction_year' => 'integer',
        'parking' => 'boolean',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function locality(): BelongsTo
    {
        return $this->belongsTo(Locality::class);
    }

    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(Amenity::class, 'property_amenities')->withTimestamps();
    }

    public function savedByUsers(): HasMany
    {
        return $this->hasMany(SavedProperty::class);
    }

    public function predictions(): HasMany
    {
        return $this->hasMany(Prediction::class);
    }

    public function getPricePerSqftAttribute(): float
    {
        return $this->area > 0 ? round($this->price / $this->area, 2) : 0;
    }
}
