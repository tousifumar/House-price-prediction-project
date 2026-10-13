<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MlModel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'version',
        'algorithm',
        'dataset_id',
        'model_path',
        'preprocessor_path',
        'mae',
        'rmse',
        'r2_score',
        'mape',
        'hyperparameters',
        'feature_importance',
        'status',
        'trained_at',
    ];

    protected $casts = [
        'mae' => 'float',
        'rmse' => 'float',
        'r2_score' => 'float',
        'mape' => 'float',
        'hyperparameters' => 'array',
        'feature_importance' => 'array',
        'trained_at' => 'datetime',
    ];

    public function dataset(): BelongsTo
    {
        return $this->belongsTo(Dataset::class);
    }

    public function metrics(): HasMany
    {
        return $this->hasMany(ModelMetric::class, 'model_id');
    }

    public function predictions(): HasMany
    {
        return $this->hasMany(Prediction::class, 'model_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
