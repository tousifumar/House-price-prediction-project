<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('saved_properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'property_id']);
        });

        Schema::create('price_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('city_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('locality_id')->nullable()->constrained()->nullOnDelete();
            $table->string('property_type')->nullable();
            $table->integer('bedrooms')->nullable();
            $table->decimal('target_price', 14, 2);
            $table->string('condition')->default('below'); // below, above
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_triggered_at')->nullable();
            $table->timestamps();
        });

        Schema::create('market_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')->constrained()->cascadeOnDelete();
            $table->foreignId('locality_id')->constrained()->cascadeOnDelete();
            $table->string('property_type')->default('Apartment');
            $table->integer('year');
            $table->integer('month');
            $table->decimal('avg_price', 14, 2);
            $table->decimal('avg_price_per_sqft', 10, 2);
            $table->integer('transaction_count')->default(0);
            $table->decimal('yoy_growth', 5, 2)->default(0);
            $table->decimal('demand_index', 4, 2)->default(5.0); // scale of 1-10
            $table->timestamps();

            $table->unique(['locality_id', 'property_type', 'year', 'month'], 'market_data_loc_type_period_idx');
        });

        Schema::create('admin_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action'); // e.g., 'dataset.upload', 'model.train', 'model.activate'
            $table->string('entity_type')->nullable(); // 'Dataset', 'MlModel', 'User'
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->json('payload')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_logs');
        Schema::dropIfExists('market_data');
        Schema::dropIfExists('price_alerts');
        Schema::dropIfExists('saved_properties');
    }
};
