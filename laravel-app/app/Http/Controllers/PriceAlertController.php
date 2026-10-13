<?php

namespace App\Http\Controllers;

use App\Models\Locality;
use App\Models\PriceAlert;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PriceAlertController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $alerts = PriceAlert::with(['locality.city'])
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        $localities = Locality::where('is_active', true)->orderBy('name')->get();
        $propertyTypes = ['Apartment', 'Villa', 'Independent House', 'Builder Floor', 'Penthouse'];

        return view('alerts.index', compact('alerts', 'localities', 'propertyTypes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'locality_id' => 'required|exists:localities,id',
            'property_type' => 'nullable|string',
            'bedrooms' => 'nullable|integer|min:1|max:6',
            'target_price' => 'required|numeric|min:100000',
            'condition' => 'required|string|in:below,above',
        ]);

        $locality = Locality::findOrFail($validated['locality_id']);

        PriceAlert::create([
            'user_id' => Auth::id(),
            'city_id' => $locality->city_id,
            'locality_id' => $locality->id,
            'property_type' => $validated['property_type'],
            'bedrooms' => $validated['bedrooms'],
            'target_price' => $validated['target_price'],
            'condition' => $validated['condition'],
            'is_active' => true,
        ]);

        return redirect()->route('price-alerts.index')->with('success', 'Price alert created! You will be notified when matching properties or market valuations trigger.');
    }

    public function toggle(int $id): RedirectResponse
    {
        $alert = PriceAlert::where('user_id', Auth::id())->findOrFail($id);
        $alert->is_active = ! $alert->is_active;
        $alert->save();

        $statusText = $alert->is_active ? 'activated' : 'paused';
        return back()->with('info', "Price alert has been {$statusText}.");
    }

    public function destroy(int $id): RedirectResponse
    {
        $alert = PriceAlert::where('user_id', Auth::id())->findOrFail($id);
        $alert->delete();

        return back()->with('success', 'Price alert deleted.');
    }
}
