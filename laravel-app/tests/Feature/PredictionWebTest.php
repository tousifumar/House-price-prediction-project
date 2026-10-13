<?php

namespace Tests\Feature;

use App\Models\Locality;
use App\Models\Prediction;
use App\Models\Property;
use App\Models\SavedProperty;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PredictionWebTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $this->user = User::where('email', 'demo@houseprice.ai')->first();
    }

    public function test_predict_page_renders_successfully(): void
    {
        $response = $this->get('/predict');
        $response->assertStatus(200);
        $response->assertSee('Evaluate Property Market Worth');
        $response->assertSee('Property Specifications');
        $response->assertSee('Bangalore');
    }

    public function test_submitting_prediction_form_redirects_to_results_dashboard(): void
    {
        $payload = [
            'property_type' => 'Apartment',
            'transaction_type' => 'Sale',
            'city' => 'Bangalore',
            'locality' => 'Whitefield',
            'area' => 1650,
            'carpet_area' => 1350,
            'bedrooms' => 3,
            'bathrooms' => 2,
            'balconies' => 2,
            'floor' => 6,
            'total_floors' => 14,
            'property_age' => 3,
            'furnishing' => 'Semi-Furnished',
            'parking' => 1,
            'user_asking_price' => 16000000,
        ];

        $response = $this->actingAs($this->user)->post('/predict', $payload);

        $this->assertDatabaseHas('predictions', [
            'user_id' => $this->user->id,
            'area' => 1650,
            'property_type' => 'Apartment',
        ]);

        $prediction = Prediction::where('user_id', $this->user->id)->orderByDesc('id')->first();
        $response->assertRedirect(route('predictions.show', $prediction->id));
        $response->assertSessionHas('success');
    }

    public function test_prediction_results_dashboard_renders_metrics_and_explainability(): void
    {
        $prediction = Prediction::create([
            'user_id' => $this->user->id,
            'property_type' => 'Apartment',
            'transaction_type' => 'Sale',
            'city_id' => 1,
            'locality_id' => 1,
            'area' => 1500,
            'carpet_area' => 1250,
            'bedrooms' => 3,
            'bathrooms' => 2,
            'balconies' => 1,
            'floor' => 4,
            'total_floors' => 10,
            'property_age' => 2,
            'furnishing' => 'Semi-Furnished',
            'parking' => true,
            'input_data' => ['area' => 1500, 'bedrooms' => 3],
            'predicted_price' => 14500000,
            'min_price' => 13775000,
            'max_price' => 15225000,
            'price_per_sqft' => 9667,
            'confidence_score' => 91.5,
            'currency' => 'INR',
            'user_asking_price' => 15000000,
        ]);

        $response = $this->actingAs($this->user)->get(route('predictions.show', $prediction->id));

        $response->assertStatus(200);
        $response->assertSee('Automated Valuation Model (AVM) Estimate');
        $response->assertSee('₹1.45 Cr');
        $response->assertSee('90% Statistical Confidence Range');
        $response->assertSee('Market Benchmark Comparison');
        $response->assertSee('Download Executive PDF');
    }

    public function test_pdf_report_generates_valid_pdf_download(): void
    {
        $prediction = Prediction::create([
            'user_id' => $this->user->id,
            'property_type' => 'Apartment',
            'transaction_type' => 'Sale',
            'city_id' => 1,
            'locality_id' => 1,
            'area' => 1400,
            'bedrooms' => 3,
            'bathrooms' => 2,
            'floor' => 5,
            'total_floors' => 12,
            'property_age' => 1,
            'input_data' => ['area' => 1400, 'bedrooms' => 3],
            'predicted_price' => 13500000,
            'min_price' => 12825000,
            'max_price' => 14175000,
            'price_per_sqft' => 9643,
            'confidence_score' => 92.0,
            'currency' => 'INR',
        ]);

        $response = $this->actingAs($this->user)->get(route('predictions.pdf', $prediction->id));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_prediction_history_page_requires_auth_and_renders(): void
    {
        // Unauthenticated redirect
        $guestRes = $this->get('/predictions');
        $guestRes->assertRedirect('/login');

        // Authenticated access
        $authRes = $this->actingAs($this->user)->get('/predictions');
        $authRes->assertStatus(200);
        $authRes->assertSee('Your Valuation History');
    }

    public function test_saved_properties_module(): void
    {
        $property = Property::first();

        // Save property
        $saveRes = $this->actingAs($this->user)->post('/saved-properties', [
            'property_id' => $property->id,
            'notes' => 'Great investment potential with metro connectivity',
        ]);
        $saveRes->assertSessionHas('success');

        $this->assertDatabaseHas('saved_properties', [
            'user_id' => $this->user->id,
            'property_id' => $property->id,
        ]);

        // View saved properties
        $viewRes = $this->actingAs($this->user)->get('/saved-properties');
        $viewRes->assertStatus(200);
        $viewRes->assertSee('Saved Properties Portfolio');
        $viewRes->assertSee('Great investment potential');

        // Delete saved property
        $saved = SavedProperty::where('user_id', $this->user->id)->first();
        $delRes = $this->actingAs($this->user)->delete(route('saved-properties.destroy', $saved->id));
        $delRes->assertSessionHas('success');
        $this->assertDatabaseMissing('saved_properties', ['id' => $saved->id]);
    }

    public function test_property_comparison_matrix_renders(): void
    {
        $properties = Property::take(2)->get();
        $response = $this->get(route('compare', ['ids' => $properties->pluck('id')->toArray()]));

        $response->assertStatus(200);
        $response->assertSee('Property Comparison Matrix');
        $response->assertSee('Asking / Listed Price');
        $response->assertSee('Price per Sq.Ft');
    }

    public function test_market_analytics_page_renders_with_trends(): void
    {
        $response = $this->get('/market-analytics');
        $response->assertStatus(200);
        $response->assertSee('Bangalore Market Analytics');
        $response->assertSee('Bangalore 12-Month Rate Trajectory');
        $response->assertSee('Micro-Market Price Comparison');
    }

    public function test_price_alerts_lifecycle(): void
    {
        $locality = Locality::first();

        // Create alert
        $createRes = $this->actingAs($this->user)->post('/price-alerts', [
            'locality_id' => $locality->id,
            'property_type' => 'Apartment',
            'bedrooms' => 3,
            'target_price' => 12000000,
            'condition' => 'below',
        ]);
        $createRes->assertRedirect(route('price-alerts.index'));
        $createRes->assertSessionHas('success');

        $this->assertDatabaseHas('price_alerts', [
            'user_id' => $this->user->id,
            'locality_id' => $locality->id,
            'target_price' => 12000000,
        ]);

        // View alerts page
        $viewRes = $this->actingAs($this->user)->get('/price-alerts');
        $viewRes->assertStatus(200);
        $viewRes->assertSee('Property Price Alerts');
        $viewRes->assertSee('Monitoring Active');
    }
}
