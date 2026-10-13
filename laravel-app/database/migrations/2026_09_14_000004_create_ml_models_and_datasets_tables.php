<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('datasets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('filename');
            $table->string('file_path');
            $table->integer('row_count')->default(0);
            $table->integer('column_count')->default(0);
            $table->integer('duplicate_count')->default(0);
            $table->integer('missing_values_count')->default(0);
            $table->integer('invalid_rows_count')->default(0);
            $table->string('status')->default('uploaded'); // uploaded, valid, invalid, imported, archived
            $table->text('description')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('ml_models', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('version')->unique(); // v1.0, v1.1
            $table->string('algorithm'); // Linear Regression, Random Forest, XGBoost, etc.
            $table->foreignId('dataset_id')->nullable()->constrained('datasets')->nullOnDelete();
            $table->string('model_path')->nullable();
            $table->string('preprocessor_path')->nullable();
            $table->decimal('mae', 14, 2)->nullable();
            $table->decimal('rmse', 14, 2)->nullable();
            $table->decimal('r2_score', 6, 4)->nullable();
            $table->decimal('mape', 6, 4)->nullable();
            $table->json('hyperparameters')->nullable();
            $table->json('feature_importance')->nullable();
            $table->string('status')->default('draft')->index(); // draft, active, archived
            $table->timestamp('trained_at')->nullable();
            $table->timestamps();
        });

        Schema::create('model_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('model_id')->constrained('ml_models')->cascadeOnDelete();
            $table->string('metric_name');
            $table->decimal('metric_value', 14, 4);
            $table->string('dataset_split')->default('test'); // train, val, test
            $table->json('chart_data')->nullable(); // actual vs predicted, residual data
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('model_metrics');
        Schema::dropIfExists('ml_models');
        Schema::dropIfExists('datasets');
    }
};
