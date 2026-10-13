<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Country;
use App\Models\Locality;
use App\Models\State;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $country = Country::firstOrCreate(
            ['code' => 'IN'],
            [
                'name' => 'India',
                'currency' => 'INR',
                'currency_symbol' => '₹',
                'is_active' => true,
            ]
        );

        $state = State::firstOrCreate(
            ['country_id' => $country->id, 'code' => 'KA'],
            [
                'name' => 'Karnataka',
                'is_active' => true,
            ]
        );

        $city = City::firstOrCreate(
            ['state_id' => $state->id, 'slug' => 'bangalore'],
            [
                'name' => 'Bangalore',
                'latitude' => 12.9716,
                'longitude' => 77.5946,
                'is_active' => true,
            ]
        );

        $localities = [
            [
                'name' => 'Whitefield',
                'slug' => 'whitefield',
                'pincode' => '560066',
                'latitude' => 12.9698,
                'longitude' => 77.7499,
                'avg_price' => 14500000,
                'avg_price_per_sqft' => 8400,
                'total_properties' => 2450,
                'growth_rate' => 13.5,
                'location_score' => 9.1,
            ],
            [
                'name' => 'Electronic City',
                'slug' => 'electronic-city',
                'pincode' => '560100',
                'latitude' => 12.8452,
                'longitude' => 77.6602,
                'avg_price' => 8500000,
                'avg_price_per_sqft' => 5800,
                'total_properties' => 1980,
                'growth_rate' => 11.2,
                'location_score' => 8.6,
            ],
            [
                'name' => 'HSR Layout',
                'slug' => 'hsr-layout',
                'pincode' => '560102',
                'latitude' => 12.9121,
                'longitude' => 77.6446,
                'avg_price' => 21000000,
                'avg_price_per_sqft' => 11200,
                'total_properties' => 1540,
                'growth_rate' => 16.4,
                'location_score' => 9.4,
            ],
            [
                'name' => 'Koramangala',
                'slug' => 'koramangala',
                'pincode' => '560034',
                'latitude' => 12.9352,
                'longitude' => 77.6245,
                'avg_price' => 28500000,
                'avg_price_per_sqft' => 13500,
                'total_properties' => 1120,
                'growth_rate' => 14.8,
                'location_score' => 9.6,
            ],
            [
                'name' => 'Indiranagar',
                'slug' => 'indiranagar',
                'pincode' => '560038',
                'latitude' => 12.9784,
                'longitude' => 77.6408,
                'avg_price' => 32000000,
                'avg_price_per_sqft' => 15000,
                'total_properties' => 980,
                'growth_rate' => 15.2,
                'location_score' => 9.7,
            ],
            [
                'name' => 'Sarjapur Road',
                'slug' => 'sarjapur-road',
                'pincode' => '560035',
                'latitude' => 12.9237,
                'longitude' => 77.6835,
                'avg_price' => 13500000,
                'avg_price_per_sqft' => 7900,
                'total_properties' => 2100,
                'growth_rate' => 15.8,
                'location_score' => 9.0,
            ],
            [
                'name' => 'Marathahalli',
                'slug' => 'marathahalli',
                'pincode' => '560037',
                'latitude' => 12.9591,
                'longitude' => 77.6974,
                'avg_price' => 11500000,
                'avg_price_per_sqft' => 7200,
                'total_properties' => 1650,
                'growth_rate' => 10.9,
                'location_score' => 8.7,
            ],
            [
                'name' => 'Hebbal',
                'slug' => 'hebbal',
                'pincode' => '560024',
                'latitude' => 13.0358,
                'longitude' => 77.5970,
                'avg_price' => 19500000,
                'avg_price_per_sqft' => 10400,
                'total_properties' => 1420,
                'growth_rate' => 14.1,
                'location_score' => 9.2,
            ],
            [
                'name' => 'Yelahanka',
                'slug' => 'yelahanka',
                'pincode' => '560064',
                'latitude' => 13.1007,
                'longitude' => 77.5963,
                'avg_price' => 12500000,
                'avg_price_per_sqft' => 6900,
                'total_properties' => 1780,
                'growth_rate' => 12.8,
                'location_score' => 8.8,
            ],
            [
                'name' => 'Bannerghatta Road',
                'slug' => 'bannerghatta-road',
                'pincode' => '560076',
                'latitude' => 12.8984,
                'longitude' => 77.5996,
                'avg_price' => 12000000,
                'avg_price_per_sqft' => 7100,
                'total_properties' => 1890,
                'growth_rate' => 11.5,
                'location_score' => 8.7,
            ],
        ];

        foreach ($localities as $loc) {
            Locality::updateOrCreate(
                ['city_id' => $city->id, 'slug' => $loc['slug']],
                $loc
            );
        }
    }
}
