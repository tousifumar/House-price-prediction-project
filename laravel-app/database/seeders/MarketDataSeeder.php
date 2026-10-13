<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Locality;
use App\Models\MarketData;
use Illuminate\Database\Seeder;

class MarketDataSeeder extends Seeder
{
    public function run(): void
    {
        $city = City::where('slug', 'bangalore')->first();
        if (! $city) {
            return;
        }

        $localities = Locality::where('city_id', $city->id)->get();

        $currentYear = (int) date('Y');
        $currentMonth = (int) date('n');

        foreach ($localities as $loc) {
            $basePricePerSqft = $loc->avg_price_per_sqft;

            for ($i = 11; $i >= 0; $i--) {
                $month = $currentMonth - $i;
                $year = $currentYear;
                if ($month <= 0) {
                    $month += 12;
                    $year -= 1;
                }

                $growthFactor = 1 - ($i * 0.009);
                $monthPriceSqft = round($basePricePerSqft * $growthFactor, 2);
                $avgPrice = round($monthPriceSqft * 1450, 2);

                MarketData::updateOrCreate(
                    [
                        'locality_id' => $loc->id,
                        'property_type' => 'Apartment',
                        'year' => $year,
                        'month' => $month,
                    ],
                    [
                        'city_id' => $city->id,
                        'avg_price' => $avgPrice,
                        'avg_price_per_sqft' => $monthPriceSqft,
                        'transaction_count' => rand(45, 180),
                        'yoy_growth' => $loc->growth_rate,
                        'demand_index' => round($loc->location_score + (rand(-4, 4) / 10), 1),
                    ]
                );
            }
        }
    }
}
