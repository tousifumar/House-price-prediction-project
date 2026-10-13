<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('predictions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('property_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('model_id')->nullable()->constrained('ml_models')->nullOnDelete();
            $table->string('property_type')->index();
            $table->string('transaction_type')->default('Sale');
            $table->foreignId('city_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('locality_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('area', 10, 2);
            $table->decimal('carpet_area', 10, 2)->nullable();
            $table->integer('bedrooms');
            $table->integer('bathrooms');
            $table->integer('balconies')->default(0);
            $table->integer('floor')->default(1);
            $table->integer('total_floors')->default(1);
            $table->integer('property_age')->default(0);
            $table->string('furnishing')->default('Semi-Furnished');
            $table->boolean('parking')->default(true);
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->json('amenities_json')->nullable();
            $table->json('input_data'); // Complete raw input payload for reproducible predictions
            $table->decimal('predicted_price', 14, 2);
            $table->decimal('min_price', 14, 2);
            $table->decimal('max_price', 14, 2);
            $table->decimal('price_per_sqft', 10, 2);
            $table->decimal('confidence_score', 5, 2)->default(85.0); // e.g. 87.0%
            $table->string('currency', 10)->default('INR');
            $table->decimal('user_asking_price', 14, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('prediction_factors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prediction_id')->constrained()->cascadeOnDelete();
            $table->string('factor_name'); // Location, Area, Bedrooms, Amenities, etc.
            $table->string('factor_type')->default('positive'); // positive, negative, neutral
            $table->decimal('impact_amount', 14, 2); // e.g. +4100000 or -500000
            $table->decimal('impact_percentage', 5, 2)->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prediction_factors');
        Schema::dropIfExists('predictions');
    }
};
