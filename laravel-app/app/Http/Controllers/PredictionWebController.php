<?php

namespace App\Http\Controllers;

use App\Http\Requests\PredictionRequest;
use App\Models\Amenity;
use App\Models\City;
use App\Models\Locality;
use App\Models\MarketData;
use App\Models\MlModel;
use App\Models\Prediction;
use App\Services\PredictionService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class PredictionWebController extends Controller
{
    protected PredictionService $predictionService;

    public function __construct(PredictionService $predictionService)
    {
        $this->predictionService = $predictionService;
    }

    public function create(): View
    {
        $city = City::where('slug', 'bangalore')->first();
        $localities = Locality::where('is_active', true)->orderBy('name')->get();
        $amenities = Amenity::where('is_active', true)->get();
        $activeModel = MlModel::where('status', 'active')->first();

        $propertyTypes = ['Apartment', 'Villa', 'Independent House', 'Builder Floor', 'Penthouse', 'Studio'];
        $conditions = ['New', 'Excellent', 'Good', 'Average', 'Needs Renovation'];
        $furnishings = ['Unfurnished', 'Semi-Furnished', 'Fully Furnished'];

        return view('pages.predict', compact(
            'city',
            'localities',
            'amenities',
            'activeModel',
            'propertyTypes',
            'conditions',
            'furnishings'
        ));
    }

    public function store(PredictionRequest $request): RedirectResponse
    {
        $result = $this->predictionService->predict($request->validated(), Auth::id());

        return redirect()->route('predictions.show', $result['prediction_id'])
            ->with('success', 'Property price prediction generated successfully!');
    }

    public function show(int $id): View
    {
        $prediction = Prediction::with(['locality.city', 'factors', 'user'])->findOrFail($id);

        // Fetch locality historical monthly market trend for Chart.js
        $marketTrends = [];
        if ($prediction->locality_id) {
            $marketTrends = MarketData::where('locality_id', $prediction->locality_id)
                ->where('property_type', 'Apartment')
                ->orderBy('year')
                ->orderBy('month')
                ->get();
        }

        // City average price per sqft for comparison
        $cityAvgRate = Locality::avg('avg_price_per_sqft') ?: 8950;
        $localityAvgRate = $prediction->locality ? $prediction->locality->avg_price_per_sqft : $cityAvgRate;

        return view('predictions.show', compact('prediction', 'marketTrends', 'cityAvgRate', 'localityAvgRate'));
    }

    public function index(Request $request): View
    {
        $user = Auth::user();
        $query = Prediction::with(['locality', 'city'])
            ->where('user_id', $user->id);

        if ($request->filled('locality_id')) {
            $query->where('locality_id', $request->locality_id);
        }

        if ($request->filled('property_type')) {
            $query->where('property_type', $request->property_type);
        }

        if ($request->filled('min_price')) {
            $query->where('predicted_price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('predicted_price', '<=', $request->max_price);
        }

        $predictions = $query->latest()->paginate(10);
        $localities = Locality::where('is_active', true)->orderBy('name')->get();

        return view('predictions.index', compact('predictions', 'localities'));
    }

    public function destroy(int $id): RedirectResponse
    {
        $prediction = Prediction::where('user_id', Auth::id())->findOrFail($id);
        $prediction->delete();

        return redirect()->route('predictions.index')->with('success', 'Valuation record deleted from your history.');
    }

    public function downloadPdf(int $id): Response
    {
        $prediction = Prediction::with(['locality.city', 'factors', 'user', 'model'])->findOrFail($id);

        $cityAvgRate = Locality::avg('avg_price_per_sqft') ?: 8950;
        $localityAvgRate = $prediction->locality ? $prediction->locality->avg_price_per_sqft : $cityAvgRate;

        $pdf = Pdf::loadView('reports.valuation-pdf', compact('prediction', 'cityAvgRate', 'localityAvgRate'))
            ->setPaper('a4', 'portrait');

        $fileName = 'Valuation_Report_' . str_replace(' ', '_', $prediction->property_type) . '_' . $prediction->id . '.pdf';

        return $pdf->download($fileName);
    }
}
