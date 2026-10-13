<?php

namespace Database\Seeders;

use App\Models\Amenity;
use App\Models\City;
use App\Models\Locality;
use App\Models\MlModel;
use App\Models\Prediction;
use App\Models\PredictionFactor;
use App\Models\Property;
use App\Models\User;
use Illuminate\Database\Seeder;

class PropertySeeder extends Seeder
{
    public function run(): void
    {
        $demoUser = User::where('email', 'demo@houseprice.ai')->first();
        $city = City::where('slug', 'bangalore')->first();
        $whitefield = Locality::where('slug', 'whitefield')->first();
        $koramangala = Locality::where('slug', 'koramangala')->first();
        $hsr = Locality::where('slug', 'hsr-layout')->first();
        $model = MlModel::where('version', 'v1.0-xgb')->first();
        $amenities = Amenity::all();

        if (! $whitefield || ! $demoUser) {
            return;
        }

        // Sample Property 1
        $p1 = Property::updateOrCreate(
            ['title' => 'Prestige Lakeside Habitat 3BHK'],
            [
                'user_id' => $demoUser->id,
                'locality_id' => $whitefield->id,
                'property_type' => 'Apartment',
                'transaction_type' => 'Sale',
                'price' => 14200000,
                'area' => 1500,
                'carpet_area' => 1300,
                'bedrooms' => 3,
                'bathrooms' => 2,
                'balconies' => 2,
                'floor' => 8,
                'total_floors' => 18,
                'property_age' => 4,
                'construction_year' => 2022,
                'furnishing' => 'Semi-Furnished',
                'condition' => 'Excellent',
                'parking' => true,
                'latitude' => 12.9698,
                'longitude' => 77.7499,
                'status' => 'available',
            ]
        );
        $p1->amenities()->sync($amenities->take(6)->pluck('id'));

        // Sample Property 2
        if ($koramangala) {
            $p2 = Property::updateOrCreate(
                ['title' => 'Raheja Residency Luxury 3BHK'],
                [
                    'user_id' => $demoUser->id,
                    'locality_id' => $koramangala->id,
                    'property_type' => 'Apartment',
                    'transaction_type' => 'Sale',
                    'price' => 28500000,
                    'area' => 2100,
                    'carpet_area' => 1850,
                    'bedrooms' => 3,
                    'bathrooms' => 3,
                    'balconies' => 3,
                    'floor' => 5,
                    'total_floors' => 12,
                    'property_age' => 2,
                    'construction_year' => 2024,
                    'furnishing' => 'Fully Furnished',
                    'condition' => 'New',
                    'parking' => true,
                    'latitude' => 12.9352,
                    'longitude' => 77.6245,
                    'status' => 'available',
                ]
            );
            $p2->amenities()->sync($amenities->take(10)->pluck('id'));
        }

        // Sample Prediction Record for Demo User
        $prediction = Prediction::updateOrCreate(
            [
                'user_id' => $demoUser->id,
                'property_type' => 'Apartment',
                'locality_id' => $whitefield->id,
                'area' => 1500,
            ],
            [
                'property_id' => $p1->id,
                'model_id' => $model?->id,
                'transaction_type' => 'Sale',
                'city_id' => $city->id,
                'carpet_area' => 1300,
                'bedrooms' => 3,
                'bathrooms' => 2,
                'balconies' => 2,
                'floor' => 8,
                'total_floors' => 18,
                'property_age' => 4,
                'furnishing' => 'Semi-Furnished',
                'parking' => true,
                'latitude' => 12.9698,
                'longitude' => 77.7499,
                'amenities_json' => ['Parking', 'Lift', 'Security', 'Gym', 'Swimming Pool'],
                'input_data' => [
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
                    'total_floors' => 18,
                    'property_age' => 4,
                    'furnishing' => 'Semi-Furnished',
                    'parking' => true,
                    'amenities' => ['Parking', 'Lift', 'Security', 'Gym', 'Swimming Pool'],
                ],
                'predicted_price' => 14200000,
                'min_price' => 13200000,
                'max_price' => 15200000,
                'price_per_sqft' => 9467,
                'confidence_score' => 87.5,
                'currency' => 'INR',
                'user_asking_price' => 14500000,
            ]
        );

        // Factors for sample prediction
        $factors = [
            ['factor_name' => 'Locality Benchmark (Whitefield)', 'factor_type' => 'positive', 'impact_amount' => 4100000, 'impact_percentage' => 28.8, 'description' => 'Prime tech corridor and upcoming Metro connectivity'],
            ['factor_name' => 'Super Built-up Area (1,500 sq.ft)', 'factor_type' => 'positive', 'impact_amount' => 3200000, 'impact_percentage' => 22.5, 'description' => 'High demand floor plate for 3 BHK category'],
            ['factor_name' => 'Bedrooms & Layout (3 BHK)', 'factor_type' => 'positive', 'impact_amount' => 1800000, 'impact_percentage' => 12.6, 'description' => 'Optimal bedroom-to-bathroom ratio'],
            ['factor_name' => 'Premium Amenities (Pool, Gym, Lift)', 'factor_type' => 'positive', 'impact_amount' => 600000, 'impact_percentage' => 4.2, 'description' => 'High amenity score gated society'],
            ['factor_name' => 'Semi-Furnished Interior Status', 'factor_type' => 'positive', 'impact_amount' => 400000, 'impact_percentage' => 2.8, 'description' => 'Fitted modular kitchen and wardrobes'],
            ['factor_name' => 'Property Age (4 Years)', 'factor_type' => 'negative', 'impact_amount' => -500000, 'impact_percentage' => -3.5, 'description' => 'Depreciation factor against new launch benchmarks'],
        ];

        foreach ($factors as $f) {
            PredictionFactor::updateOrCreate(
                [
                    'prediction_id' => $prediction->id,
                    'factor_name' => $f['factor_name'],
                ],
                $f
            );
        }
    }
}
