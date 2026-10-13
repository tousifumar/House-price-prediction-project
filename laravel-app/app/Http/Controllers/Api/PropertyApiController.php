<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PropertyApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Property::with(['locality.city', 'amenities'])
            ->where('status', 'available');

        // Filter by city
        if ($request->filled('city_id')) {
            $query->whereHas('locality', function ($q) use ($request) {
                $q->where('city_id', $request->city_id);
            });
        }

        // Filter by locality
        if ($request->filled('locality_id')) {
            $query->where('locality_id', $request->locality_id);
        }

        // Filter by property type
        if ($request->filled('property_type')) {
            $query->where('property_type', $request->property_type);
        }

        // Filter by bedrooms
        if ($request->filled('bedrooms')) {
            $query->where('bedrooms', $request->bedrooms);
        }

        // Filter by price range
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Filter by area range
        if ($request->filled('min_area')) {
            $query->where('area', '>=', $request->min_area);
        }
        if ($request->filled('max_area')) {
            $query->where('area', '<=', $request->max_area);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        if (in_array($sortBy, ['price', 'area', 'created_at'])) {
            $query->orderBy($sortBy, $sortOrder === 'asc' ? 'asc' : 'desc');
        }

        $perPage = min((int) $request->get('per_page', 15), 50);
        $properties = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $properties->items(),
            'pagination' => [
                'current_page' => $properties->currentPage(),
                'last_page' => $properties->lastPage(),
                'per_page' => $properties->perPage(),
                'total' => $properties->total(),
            ],
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $property = Property::with(['locality.city', 'amenities', 'user'])->find($id);

        if (! $property) {
            return response()->json([
                'success' => false,
                'message' => 'Property not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $property,
        ]);
    }
}
