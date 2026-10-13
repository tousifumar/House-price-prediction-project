<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\Locality;
use App\Models\Prediction;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PredictionApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_prediction_api_returns_valuation_with_factors(): void
    {
        $payload = [
            'property_type' => 'Apartment',
            'transaction_type' => 'Sale',
            'city' => 'Bangalore',
            'locality' => 'Whitefield',
            'area' => 1500,
            'carpet_area' => 1300,
            'bedrooms' => 3,
            'bathrooms' => 2,
            'balconies' => 2,
            'floor' => 8,
            'total_floors' => 15,
            'property_age' => 5,
            'furnishing' => 'Semi-Furnished',
            'parking' => true,
            'amenities' => ['Gym', 'Lift', 'Security'],
        ];

        $response = $this->postJson('/api/predict', $payload);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'prediction_id',
                'predicted_price',
                'formatted_price',
                'price_range' => ['min', 'max', 'formatted'],
                'price_per_sqft',
                'confidence_score',
                'currency',
                'influencing_factors',
                'locality_benchmark',
                'model_info' => ['version', 'algorithm'],
                'disclaimer',
            ],
        ]);

        $data = $response->json('data');
        $this->assertGreaterThan(0, $data['predicted_price']);
        $this->assertLessThan($data['price_range']['max'], $data['price_range']['min']);
        $this->assertNotEmpty($data['influencing_factors']);

        // Verify database persistence
        $this->assertDatabaseHas('predictions', [
            'id' => $data['prediction_id'],
            'property_type' => 'Apartment',
            'area' => 1500,
        ]);

        $this->assertDatabaseHas('prediction_factors', [
            'prediction_id' => $data['prediction_id'],
        ]);
    }

    public function test_prediction_api_calculates_asking_price_insight(): void
    {
        $payload = [
            'property_type' => 'Apartment',
            'transaction_type' => 'Sale',
            'city' => 'Bangalore',
            'locality' => 'Whitefield',
            'area' => 1500,
            'bedrooms' => 3,
            'bathrooms' => 2,
            'furnishing' => 'Semi-Furnished',
            'user_asking_price' => 18000000,
        ];

        $response = $this->postJson('/api/predict', $payload);

        $response->assertStatus(200);
        $this->assertNotNull($response->json('data.asking_price_insight'));
        $this->assertStringContainsString('asking price is approximately', $response->json('data.asking_price_insight'));
    }

    public function test_prediction_api_validates_required_fields(): void
    {
        $response = $this->postJson('/api/predict', [
            // Missing area, bedrooms, locality, etc.
            'property_type' => 'Apartment',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['city', 'locality', 'area', 'bedrooms', 'bathrooms', 'furnishing']);
    }

    public function test_locations_and_localities_apis_return_data(): void
    {
        $locRes = $this->getJson('/api/locations');
        $locRes->assertStatus(200);
        $locRes->assertJsonStructure(['success', 'data']);

        $city = City::first();
        $localitiesRes = $this->getJson("/api/localities?city_id={$city->id}");
        $localitiesRes->assertStatus(200);
        $this->assertGreaterThan(0, $localitiesRes->json('count'));

        $amenitiesRes = $this->getJson('/api/amenities');
        $amenitiesRes->assertStatus(200);
        $this->assertCount(15, $amenitiesRes->json('data'));
    }

    public function test_properties_api_supports_filtering(): void
    {
        $response = $this->getJson('/api/properties?property_type=Apartment&bedrooms=3');
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data', 'pagination']);
        $this->assertGreaterThan(0, count($response->json('data')));
    }
}
