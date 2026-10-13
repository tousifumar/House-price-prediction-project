<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Locality;
use App\Models\MarketData;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MarketAnalyticsController extends Controller
{
    public function index(Request $request): View
    {
        $city = City::where('slug', 'bangalore')->first();
        $localities = Locality::where('is_active', true)->orderBy('avg_price_per_sqft', 'desc')->get();

        // 12-month aggregated trend across Bangalore
        $monthlyTrends = MarketData::selectRaw('year, month, AVG(avg_price_per_sqft) as avg_rate, AVG(avg_price) as avg_price, SUM(transaction_count) as total_tx')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // Locality comparison data
        $localityNames = $localities->pluck('name')->toArray();
        $localityRates = $localities->pluck('avg_price_per_sqft')->toArray();
        $localityGrowth = $localities->pluck('growth_rate')->toArray();

        // Property type distribution
        $typeDistribution = [
            'Apartment' => Property::where('property_type', 'Apartment')->avg('price') ?: 14500000,
            'Villa' => Property::where('property_type', 'Villa')->avg('price') ?: 32000000,
            'Penthouse' => Property::where('property_type', 'Penthouse')->avg('price') ?: 45000000,
            'Independent House' => Property::where('property_type', 'Independent House')->avg('price') ?: 26000000,
            'Builder Floor' => Property::where('property_type', 'Builder Floor')->avg('price') ?: 11000000,
        ];

        return view('pages.market-analytics', compact(
            'city',
            'localities',
            'monthlyTrends',
            'localityNames',
            'localityRates',
            'localityGrowth',
            'typeDistribution'
        ));
    }
}
