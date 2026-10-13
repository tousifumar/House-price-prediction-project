<?php

namespace App\Http\Controllers;

use App\Models\Locality;
use App\Models\Property;
use App\Models\SavedProperty;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SavedPropertyController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $query = SavedProperty::with(['property.locality.city', 'property.amenities'])
            ->where('user_id', $user->id);

        if ($request->filled('locality_id')) {
            $query->whereHas('property', function ($q) use ($request) {
                $q->where('locality_id', $request->locality_id);
            });
        }

        $savedProperties = $query->latest()->paginate(9);
        $localities = Locality::where('is_active', true)->orderBy('name')->get();

        return view('saved-properties.index', compact('savedProperties', 'localities'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'notes' => 'nullable|string|max:500',
        ]);

        SavedProperty::firstOrCreate(
            [
                'user_id' => Auth::id(),
                'property_id' => $request->property_id,
            ],
            [
                'notes' => $request->notes,
            ]
        );

        return back()->with('success', 'Property saved to your portfolio!');
    }

    public function destroy(int $id): RedirectResponse
    {
        $saved = SavedProperty::where('user_id', Auth::id())->findOrFail($id);
        $saved->delete();

        return back()->with('success', 'Property removed from saved properties.');
    }
}
