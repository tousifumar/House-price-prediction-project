<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PredictionFactor extends Model
{
    use HasFactory;

    protected $fillable = [
        'prediction_id',
        'factor_name',
        'factor_type',
        'impact_amount',
        'impact_percentage',
        'description',
    ];

    protected $casts = [
        'impact_amount' => 'float',
        'impact_percentage' => 'float',
    ];

    public function prediction(): BelongsTo
    {
        return $this->belongsTo(Prediction::class);
    }
}
