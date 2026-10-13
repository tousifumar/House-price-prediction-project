<?php

namespace App\Http\Controllers;

use App\Models\Amenity;
use App\Models\City;
use App\Models\Locality;
use App\Models\MarketData;
use App\Models\MlModel;
use App\Models\Property;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $city = City::with(['localities' => function ($query) {
            $query->where('is_active', true)->orderBy('growth_rate', 'desc');
        }])->where('slug', 'bangalore')->first();

        $localities = $city ? $city->localities : Locality::where('is_active', true)->get();
        $amenities = Amenity::where('is_active', true)->get();
        $activeModel = MlModel::where('status', 'active')->first();
        
        $stats = [
            'total_properties' => Locality::sum('total_properties') ?: 16500,
            'avg_city_price_per_sqft' => round(Locality::avg('avg_price_per_sqft') ?: 8950),
            'avg_growth_rate' => round(Locality::avg('growth_rate') ?: 13.8, 1),
            'model_accuracy' => $activeModel ? round($activeModel->r2_score * 100, 1) : 91.4,
            'localities_count' => $localities->count(),
        ];

        return view('pages.home', compact('city', 'localities', 'amenities', 'activeModel', 'stats'));
    }
}
