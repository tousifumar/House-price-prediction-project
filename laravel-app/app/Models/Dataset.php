<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dataset extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'filename',
        'file_path',
        'row_count',
        'column_count',
        'duplicate_count',
        'missing_values_count',
        'invalid_rows_count',
        'status',
        'description',
        'uploaded_by',
    ];

    protected $casts = [
        'row_count' => 'integer',
        'column_count' => 'integer',
        'duplicate_count' => 'integer',
        'missing_values_count' => 'integer',
        'invalid_rows_count' => 'integer',
    ];

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function mlModels(): HasMany
    {
        return $this->hasMany(MlModel::class);
    }
}
