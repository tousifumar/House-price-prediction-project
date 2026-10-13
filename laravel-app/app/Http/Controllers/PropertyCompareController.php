<?php

namespace App\Http\Controllers;

use App\Models\Locality;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PropertyCompareController extends Controller
{
    public function index(Request $request): View
    {
        $propertyIds = $request->input('ids', []);

        if (empty($propertyIds)) {
            // Default to first 2 properties for comparison preview
            $propertyIds = Property::take(2)->pluck('id')->toArray();
        }

        $properties = Property::with(['locality.city', 'amenities'])
            ->whereIn('id', $propertyIds)
            ->take(4)
            ->get();

        $availableProperties = Property::with('locality')->take(20)->get();

        return view('compare.index', compact('properties', 'availableProperties', 'propertyIds'));
    }
}
