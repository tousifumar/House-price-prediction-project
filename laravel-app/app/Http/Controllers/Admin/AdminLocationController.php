<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Locality;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminLocationController extends Controller
{
    public function index(Request $request): View
    {
        $city = City::where('slug', 'bangalore')->first();
        $query = Locality::with('city')->withCount('properties');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $localities = $query->orderBy('name')->paginate(15);

        return view('admin.locations.index', compact('city', 'localities'));
    }

    public function storeLocality(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'city_id' => 'required|exists:cities,id',
            'name' => 'required|string|max:100|unique:localities,name',
            'avg_price_per_sqft' => 'required|numeric|min:1000',
            'growth_rate' => 'required|numeric',
            'location_score' => 'required|numeric|min:1|max:10',
            'price_tier' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        Locality::create([
            'city_id' => $validated['city_id'],
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'avg_price_per_sqft' => $validated['avg_price_per_sqft'],
            'growth_rate' => $validated['growth_rate'],
            'location_score' => $validated['location_score'],
            'price_tier' => $validated['price_tier'],
            'latitude' => $validated['latitude'] ?? 12.9716,
            'longitude' => $validated['longitude'] ?? 77.5946,
            'is_active' => true,
        ]);

        return back()->with('success', "Locality {$validated['name']} registered successfully.");
    }

    public function updateLocality(Request $request, int $id): RedirectResponse
    {
        $locality = Locality::findOrFail($id);

        $validated = $request->validate([
            'avg_price_per_sqft' => 'required|numeric|min:1000',
            'growth_rate' => 'required|numeric',
            'location_score' => 'required|numeric|min:1|max:10',
            'price_tier' => 'required|string',
        ]);

        $locality->update($validated);

        return back()->with('success', "Locality {$locality->name} benchmarks updated.");
    }

    public function toggleLocality(int $id): RedirectResponse
    {
        $locality = Locality::findOrFail($id);
        $locality->is_active = ! $locality->is_active;
        $locality->save();

        $status = $locality->is_active ? 'enabled' : 'disabled';
        return back()->with('success', "Locality {$locality->name} has been {$status}.");
    }
}
