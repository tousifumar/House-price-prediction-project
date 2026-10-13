<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Amenity;
use App\Models\Locality;
use App\Models\Property;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminPropertyController extends Controller
{
    public function index(Request $request): View
    {
        $query = Property::with(['locality.city', 'amenities', 'user']);

        if ($request->filled('locality_id')) {
            $query->where('locality_id', $request->locality_id);
        }

        if ($request->filled('property_type')) {
            $query->where('property_type', $request->property_type);
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        $properties = $query->latest()->paginate(12);
        $localities = Locality::where('is_active', true)->orderBy('name')->get();
        $amenities = Amenity::where('is_active', true)->get();

        return view('admin.properties.index', compact('properties', 'localities', 'amenities'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'locality_id' => 'required|exists:localities,id',
            'property_type' => 'required|string',
            'transaction_type' => 'required|string|in:Sale,Rent',
            'price' => 'required|numeric|min:100000',
            'area' => 'required|numeric|min:100',
            'carpet_area' => 'nullable|numeric|min:50',
            'bedrooms' => 'required|integer|min:1|max:10',
            'bathrooms' => 'required|integer|min:1|max:10',
            'balconies' => 'nullable|integer|min:0|max:10',
            'floor' => 'required|integer|min:0',
            'total_floors' => 'required|integer|min:1',
            'property_age' => 'required|integer|min:0',
            'furnishing' => 'required|string',
            'condition' => 'nullable|string',
            'parking' => 'nullable|boolean',
            'amenities' => 'nullable|array',
            'amenities.*' => 'exists:amenities,id',
        ]);

        $locality = Locality::findOrFail($validated['locality_id']);

        $property = Property::create([
            'user_id' => Auth::id(),
            'locality_id' => $validated['locality_id'],
            'title' => $validated['title'],
            'property_type' => $validated['property_type'],
            'transaction_type' => $validated['transaction_type'],
            'price' => $validated['price'],
            'area' => $validated['area'],
            'carpet_area' => $validated['carpet_area'] ?? ($validated['area'] * 0.82),
            'bedrooms' => $validated['bedrooms'],
            'bathrooms' => $validated['bathrooms'],
            'balconies' => $validated['balconies'] ?? 1,
            'floor' => $validated['floor'],
            'total_floors' => $validated['total_floors'],
            'property_age' => $validated['property_age'],
            'construction_year' => date('Y') - $validated['property_age'],
            'furnishing' => $validated['furnishing'],
            'condition' => $validated['condition'] ?? 'Good',
            'parking' => $request->has('parking'),
            'latitude' => $locality->latitude,
            'longitude' => $locality->longitude,
            'status' => 'available',
        ]);

        if (!empty($validated['amenities'])) {
            $property->amenities()->sync($validated['amenities']);
        }

        return back()->with('success', 'Property listing created successfully.');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $property = Property::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'locality_id' => 'required|exists:localities,id',
            'property_type' => 'required|string',
            'price' => 'required|numeric|min:100000',
            'area' => 'required|numeric|min:100',
            'bedrooms' => 'required|integer|min:1|max:10',
            'bathrooms' => 'required|integer|min:1|max:10',
            'floor' => 'required|integer|min:0',
            'total_floors' => 'required|integer|min:1',
            'property_age' => 'required|integer|min:0',
            'furnishing' => 'required|string',
            'amenities' => 'nullable|array',
            'amenities.*' => 'exists:amenities,id',
        ]);

        $property->update([
            'title' => $validated['title'],
            'locality_id' => $validated['locality_id'],
            'property_type' => $validated['property_type'],
            'price' => $validated['price'],
            'area' => $validated['area'],
            'bedrooms' => $validated['bedrooms'],
            'bathrooms' => $validated['bathrooms'],
            'floor' => $validated['floor'],
            'total_floors' => $validated['total_floors'],
            'property_age' => $validated['property_age'],
            'furnishing' => $validated['furnishing'],
            'parking' => $request->has('parking'),
        ]);

        if (isset($validated['amenities'])) {
            $property->amenities()->sync($validated['amenities']);
        }

        return back()->with('success', 'Property details updated successfully.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $property = Property::findOrFail($id);
        $property->delete();

        return back()->with('success', 'Property removed from platform.');
    }
}
