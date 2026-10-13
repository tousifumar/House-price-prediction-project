<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Amenity;
use App\Models\City;
use App\Models\Locality;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LocationApiController extends Controller
{
    public function getLocations(): JsonResponse
    {
        $cities = City::where('is_active', true)
            ->withCount(['localities' => function ($q) {
                $q->where('is_active', true);
            }])
            ->get();

        return response()->json([
            'success' => true,
            'data' => $cities,
        ]);
    }

    public function getLocalities(Request $request): JsonResponse
    {
        $cityId = $request->query('city_id');
        $query = Locality::where('is_active', true);

        if ($cityId) {
            $query->where('city_id', $cityId);
        }

        $localities = $query->orderBy('name')->get();

        return response()->json([
            'success' => true,
            'count' => $localities->count(),
            'data' => $localities,
        ]);
    }

    public function getAmenities(): JsonResponse
    {
        $amenities = Amenity::where('is_active', true)->get();

        return response()->json([
            'success' => true,
            'count' => $amenities->count(),
            'data' => $amenities,
        ]);
    }
}
