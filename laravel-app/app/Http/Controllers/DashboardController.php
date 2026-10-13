<?php

namespace App\Http\Controllers;

use App\Models\Locality;
use App\Models\Prediction;
use App\Models\Property;
use App\Models\SavedProperty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();

        $predictions = Prediction::with(['locality', 'city', 'factors'])
            ->where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        $savedProperties = SavedProperty::with(['property.locality'])
            ->where('user_id', $user->id)
            ->latest()
            ->take(4)
            ->get();

        $stats = [
            'total_predictions' => Prediction::where('user_id', $user->id)->count(),
            'saved_properties_count' => SavedProperty::where('user_id', $user->id)->count(),
            'price_alerts_count' => $user->priceAlerts()->count(),
            'avg_estimated_value' => Prediction::where('user_id', $user->id)->avg('predicted_price') ?: 0,
        ];

        return view('dashboard.index', compact('user', 'predictions', 'savedProperties', 'stats'));
    }
}
