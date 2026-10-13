<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Amenity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminAmenityController extends Controller
{
    public function index(): View
    {
        $amenities = Amenity::withCount('properties')->orderBy('name')->get();
        return view('admin.amenities.index', compact('amenities'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:amenities,name',
            'category' => 'nullable|string|max:50',
            'icon' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:255',
        ]);

        Amenity::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'category' => $validated['category'] ?? 'General',
            'icon' => $validated['icon'] ?? 'sparkles',
            'description' => $validated['description'] ?? 'Society feature',
            'is_active' => true,
        ]);

        return back()->with('success', "Amenity {$validated['name']} registered.");
    }

    public function toggle(int $id): RedirectResponse
    {
        $amenity = Amenity::findOrFail($id);
        $amenity->is_active = ! $amenity->is_active;
        $amenity->save();

        $status = $amenity->is_active ? 'enabled' : 'disabled';
        return back()->with('success', "Amenity {$amenity->name} {$status}.");
    }
}
