<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dataset;
use App\Models\Locality;
use App\Models\MlModel;
use App\Models\Prediction;
use App\Models\Property;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $stats = [
            'total_users' => User::count(),
            'total_predictions' => Prediction::count(),
            'total_properties' => Property::count(),
            'total_localities' => Locality::count(),
            'active_model' => MlModel::where('status', 'active')->first(),
            'avg_predicted_price' => Prediction::avg('predicted_price') ?: 0,
            'total_datasets' => Dataset::count(),
        ];

        $recentPredictions = Prediction::with(['user', 'locality'])->latest()->take(6)->get();
        $recentUsers = User::latest()->take(5)->get();
        $models = MlModel::latest()->get();

        return view('admin.dashboard', compact('stats', 'recentPredictions', 'recentUsers', 'models'));
    }
}
