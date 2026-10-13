<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('locality_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('property_type')->index(); // Apartment, Villa, Independent House, Builder Floor, Penthouse, Plot, Studio
            $table->string('transaction_type')->default('Sale')->index(); // Sale, Rent
            $table->decimal('price', 14, 2);
            $table->decimal('area', 10, 2); // Built-up Area in sq.ft
            $table->decimal('carpet_area', 10, 2)->nullable();
            $table->integer('bedrooms');
            $table->integer('bathrooms');
            $table->integer('balconies')->default(0);
            $table->integer('floor')->default(1);
            $table->integer('total_floors')->default(1);
            $table->integer('property_age')->default(0);
            $table->integer('construction_year')->nullable();
            $table->string('furnishing')->default('Semi-Furnished'); // Unfurnished, Semi-Furnished, Fully Furnished
            $table->string('condition')->default('Good'); // New, Excellent, Good, Average, Needs Renovation
            $table->boolean('parking')->default(true);
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('status')->default('available'); // available, pending, sold
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('property_amenities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->foreignId('amenity_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['property_id', 'amenity_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_amenities');
        Schema::dropIfExists('properties');
    }
};
