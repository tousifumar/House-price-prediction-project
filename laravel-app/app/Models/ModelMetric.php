<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ModelMetric extends Model
{
    use HasFactory;

    protected $fillable = [
        'model_id',
        'metric_name',
        'metric_value',
        'dataset_split',
        'chart_data',
    ];

    protected $casts = [
        'metric_value' => 'float',
        'chart_data' => 'array',
    ];

    public function mlModel(): BelongsTo
    {
        return $this->belongsTo(MlModel::class, 'model_id');
    }
}
