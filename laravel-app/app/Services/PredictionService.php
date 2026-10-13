<?php

namespace App\Services;

use App\Models\City;
use App\Models\Locality;
use App\Models\MlModel;
use App\Models\Prediction;
use App\Models\PredictionFactor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PredictionService
{
    protected string $mlServiceUrl;
    protected int $timeout;

    public function __construct()
    {
        $this->mlServiceUrl = rtrim(env('ML_SERVICE_URL', 'http://127.0.0.1:8001'), '/');
        $this->timeout = (int) env('ML_SERVICE_TIMEOUT', 15);
    }

    public function predict(array $data, ?int $userId = null): array
    {
        // Resolve City and Locality records for relational tracking
        $city = City::where('name', $data['city'] ?? 'Bangalore')
            ->orWhere('slug', 'bangalore')
            ->first();

        $locality = null;
        if (! empty($data['locality'])) {
            $locality = Locality::where('name', $data['locality'])
                ->orWhere('slug', strtolower(str_replace(' ', '-', $data['locality'])))
                ->first();
        }

        $activeModel = MlModel::where('status', 'active')->first();

        // Prepare payload for Python FastAPI microservice
        $payload = [
            'property_type' => $data['property_type'] ?? 'Apartment',
            'transaction_type' => $data['transaction_type'] ?? 'Sale',
            'city' => $city ? $city->name : ($data['city'] ?? 'Bangalore'),
            'locality' => $locality ? $locality->name : ($data['locality'] ?? 'Whitefield'),
            'area' => (float) $data['area'],
            'carpet_area' => isset($data['carpet_area']) && $data['carpet_area'] ? (float) $data['carpet_area'] : null,
            'bedrooms' => (int) ($data['bedrooms'] ?? 3),
            'bathrooms' => (int) ($data['bathrooms'] ?? 2),
            'balconies' => (int) ($data['balconies'] ?? 1),
            'floor' => (int) ($data['floor'] ?? 1),
            'total_floors' => (int) ($data['total_floors'] ?? 1),
            'property_age' => (int) ($data['property_age'] ?? 0),
            'furnishing' => $data['furnishing'] ?? 'Semi-Furnished',
            'parking' => (bool) ($data['parking'] ?? true),
            'latitude' => isset($data['latitude']) && $data['latitude'] ? (float) $data['latitude'] : ($locality ? (float) $locality->latitude : null),
            'longitude' => isset($data['longitude']) && $data['longitude'] ? (float) $data['longitude'] : ($locality ? (float) $locality->longitude : null),
            'amenities' => $data['amenities'] ?? [],
        ];

        $mlResult = null;
        $isMlLive = false;

        // Call Python FastAPI microservice
        try {
            $response = Http::timeout($this->timeout)->post("{$this->mlServiceUrl}/predict", $payload);

            if ($response->successful()) {
                $mlResult = $response->json();
                $isMlLive = true;
            } else {
                Log::warning('ML service responded with non-200 status', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }
        } catch (\Throwable $e) {
            Log::warning('Prediction ML Service unreachable, using resilient fallback engine', [
                'error' => $e->getMessage(),
            ]);
        }

        // Resilient Fallback Calculation if ML service is down or network timed out
        if (! $mlResult) {
            $mlResult = $this->fallbackValuation($data, $locality);
        }

        $predictedPrice = (float) $mlResult['predicted_price'];
        $minPrice = (float) $mlResult['price_range']['min'];
        $maxPrice = (float) $mlResult['price_range']['max'];
        $pricePerSqft = (float) $mlResult['price_per_sqft'];
        $confidence = (float) ($mlResult['confidence_score'] ?? 87.5);
        $userAsking = isset($data['user_asking_price']) && $data['user_asking_price'] ? (float) $data['user_asking_price'] : null;

        // Persist prediction in database
        $prediction = Prediction::create([
            'user_id' => $userId ?? (Auth::check() ? Auth::id() : null),
            'model_id' => $activeModel ? $activeModel->id : null,
            'property_type' => $payload['property_type'],
            'transaction_type' => $payload['transaction_type'],
            'city_id' => $city ? $city->id : null,
            'locality_id' => $locality ? $locality->id : null,
            'area' => $payload['area'],
            'carpet_area' => $payload['carpet_area'],
            'bedrooms' => $payload['bedrooms'],
            'bathrooms' => $payload['bathrooms'],
            'balconies' => $payload['balconies'],
            'floor' => $payload['floor'],
            'total_floors' => $payload['total_floors'],
            'property_age' => $payload['property_age'],
            'furnishing' => $payload['furnishing'],
            'parking' => $payload['parking'],
            'latitude' => $payload['latitude'],
            'longitude' => $payload['longitude'],
            'amenities_json' => $payload['amenities'],
            'input_data' => $payload,
            'predicted_price' => $predictedPrice,
            'min_price' => $minPrice,
            'max_price' => $maxPrice,
            'price_per_sqft' => $pricePerSqft,
            'confidence_score' => $confidence,
            'currency' => 'INR',
            'user_asking_price' => $userAsking,
        ]);

        // Save influencing factor breakdowns
        $factorsList = [];
        if (! empty($mlResult['influencing_factors'])) {
            foreach ($mlResult['influencing_factors'] as $factor) {
                $savedFactor = PredictionFactor::create([
                    'prediction_id' => $prediction->id,
                    'factor_name' => $factor['factor_name'],
                    'factor_type' => $factor['factor_type'],
                    'impact_amount' => (float) $factor['impact_amount'],
                    'impact_percentage' => (float) ($factor['impact_percentage'] ?? 0),
                    'description' => $factor['description'] ?? '',
                ]);
                $factorsList[] = $savedFactor;
            }
        }

        // Asking Price vs Estimated Value Insight (Section 28)
        $askingPriceInsight = null;
        if ($userAsking) {
            $diffPct = round((($userAsking - $predictedPrice) / $predictedPrice) * 100, 1);
            if ($diffPct > 0) {
                $askingPriceInsight = "The asking price is approximately {$diffPct}% above the estimated market value.";
            } elseif ($diffPct < 0) {
                $askingPriceInsight = "The asking price is approximately " . abs($diffPct) . "% below the estimated market value.";
            } else {
                $askingPriceInsight = "The asking price aligns closely with the estimated market value.";
            }
        }

        return [
            'success' => true,
            'prediction_id' => $prediction->id,
            'is_ml_service_live' => $isMlLive,
            'predicted_price' => $predictedPrice,
            'formatted_price' => Prediction::formatPriceInIndianSystem($predictedPrice),
            'price_range' => [
                'min' => $minPrice,
                'max' => $maxPrice,
                'formatted' => Prediction::formatPriceInIndianSystem($minPrice) . ' — ' . Prediction::formatPriceInIndianSystem($maxPrice),
            ],
            'price_per_sqft' => $pricePerSqft,
            'confidence_score' => $confidence,
            'currency' => 'INR',
            'influencing_factors' => $mlResult['influencing_factors'] ?? [],
            'locality_benchmark' => [
                'locality_name' => $locality ? $locality->name : $payload['locality'],
                'avg_price' => $locality ? $locality->avg_price : ($predictedPrice * 0.95),
                'avg_price_per_sqft' => $locality ? $locality->avg_price_per_sqft : $pricePerSqft,
                'formatted_locality_avg' => $locality ? Prediction::formatPriceInIndianSystem($locality->avg_price) : 'N/A',
            ],
            'asking_price_insight' => $askingPriceInsight,
            'model_info' => [
                'version' => $mlResult['model_version'] ?? ($activeModel ? $activeModel->version : 'v1.0-xgb'),
                'algorithm' => $mlResult['algorithm'] ?? ($activeModel ? $activeModel->algorithm : 'Gradient Boosting Regressor'),
            ],
            'disclaimer' => "House Price Predictor provides an automated estimate based on the data available to the system. Actual property prices may vary based on market conditions, negotiations, property condition, legal status, location-specific factors and other circumstances. This estimate is not a guaranteed valuation or professional financial, legal, or real-estate advice.",
        ];
    }

    protected function fallbackValuation(array $data, ?Locality $locality): array
    {
        $baseRate = $locality ? $locality->avg_price_per_sqft : 8000;
        $area = (float) $data['area'];
        $bhk = (int) ($data['bedrooms'] ?? 3);
        $age = (int) ($data['property_age'] ?? 0);
        $furnishing = $data['furnishing'] ?? 'Semi-Furnished';

        $bhkMultiplier = 1.0 + (($bhk - 2) * 0.05);
        $furnMultiplier = $furnishing === 'Fully Furnished' ? 1.07 : ($furnishing === 'Unfurnished' ? 0.96 : 1.00);
        $ageMultiplier = max(0.75, 1.0 - ($age * 0.012));

        $rawPrice = $area * $baseRate * $bhkMultiplier * $furnMultiplier * $ageMultiplier;
        $predictedPrice = round($rawPrice, -4);
        $pricePerSqft = round($predictedPrice / $area, 2);

        $min = round($predictedPrice * 0.93, -4);
        $max = round($predictedPrice * 1.07, -4);

        return [
            'predicted_price' => $predictedPrice,
            'formatted_price' => Prediction::formatPriceInIndianSystem($predictedPrice),
            'price_range' => [
                'min' => $min,
                'max' => $max,
                'formatted' => Prediction::formatPriceInIndianSystem($min) . ' — ' . Prediction::formatPriceInIndianSystem($max),
            ],
            'price_per_sqft' => $pricePerSqft,
            'confidence_score' => 88.0,
            'influencing_factors' => [
                [
                    'factor_name' => 'Locality Benchmark (' . ($locality ? $locality->name : 'Bangalore') . ')',
                    'factor_type' => 'positive',
                    'impact_amount' => round($predictedPrice * 0.28, 2),
                    'impact_percentage' => 28.0,
                    'description' => 'Locality demand index and micro-market rate.',
                ],
                [
                    'factor_name' => 'Built-up Area (' . (int) $area . ' sq.ft)',
                    'factor_type' => 'positive',
                    'impact_amount' => round($predictedPrice * 0.32, 2),
                    'impact_percentage' => 32.0,
                    'description' => 'Base unit square footage.',
                ],
            ],
            'model_version' => 'v1.0-fallback',
            'algorithm' => 'Algorithmic Locality Regression',
        ];
    }
}
