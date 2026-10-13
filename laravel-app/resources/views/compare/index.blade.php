@extends('layouts.app')

@section('title', 'Property Comparison Matrix — House Price Predictor')

@section('content')
<div class="py-10 bg-slate-50 min-h-screen" x-data="{ addDropdownOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        <!-- Header Banner -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-semibold mb-2">
                    <span>Multi-Property Decision Matrix</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-display">Property Comparison Matrix</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">Side-by-side evaluation of rates, physical specifications, amenities, and micro-market scores.</p>
            </div>

            <!-- Add Property Selector Form -->
            <div class="flex items-center space-x-3">
                <form method="GET" action="{{ route('compare') }}" class="flex items-center space-x-2">
                    @foreach($properties as $prop)
                        <input type="hidden" name="ids[]" value="{{ $prop->id }}">
                    @endforeach
                    <select name="ids[]" onchange="this.form.submit()" class="bg-white border border-slate-200 text-slate-800 text-xs rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-emerald-500 font-semibold shadow-sm">
                        <option value="">+ Add Property to Compare...</option>
                        @foreach($availableProperties as $avail)
                            @if(!in_array($avail->id, $properties->pluck('id')->toArray()))
                                <option value="{{ $avail->id }}">
                                    {{ $avail->bedrooms }} BHK {{ $avail->property_type }} — {{ $avail->locality->name ?? 'Bangalore' }} ({{ \App\Models\Prediction::formatPriceInIndianSystem($avail->price) }})
                                </option>
                            @endif
                        @endforeach
                    </select>
                </form>
            </div>
        </div>

        @if($properties->isEmpty())
            <div class="bg-white rounded-3xl p-12 sm:p-16 text-center border border-slate-200 shadow-sm space-y-4">
                <div class="w-16 h-16 rounded-2xl bg-indigo-50 text-indigo-600 mx-auto flex items-center justify-center text-3xl">
                    ⚖️
                </div>
                <div class="space-y-1">
                    <h3 class="text-lg font-bold text-slate-900">Select properties to compare</h3>
                    <p class="text-xs text-slate-500 max-w-sm mx-auto">Choose at least two properties from your portfolio or market listings to generate a side-by-side comparison matrix.</p>
                </div>
            </div>
        @else
            <!-- Responsive Comparison Table -->
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <!-- Head: Property Titles & Quick Actions -->
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50/70">
                            <th class="p-6 w-1/5 min-w-[200px] text-xs font-bold uppercase text-slate-400 tracking-wider align-bottom">
                                Parameters
                            </th>
                            @foreach($properties as $property)
                                <th class="p-6 min-w-[240px] align-top">
                                    <div class="space-y-3">
                                        <div class="flex items-center justify-between">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 text-xs font-bold">
                                                {{ $property->bedrooms }} BHK {{ $property->property_type }}
                                            </span>
                                            
                                            <!-- Remove from compare form -->
                                            <form method="GET" action="{{ route('compare') }}">
                                                @foreach($properties as $other)
                                                    @if($other->id !== $property->id)
                                                        <input type="hidden" name="ids[]" value="{{ $other->id }}">
                                                    @endif
                                                @endforeach
                                                <button type="submit" class="text-slate-400 hover:text-rose-500 transition p-1" title="Remove from compare">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                </button>
                                            </form>
                                        </div>
                                        <h3 class="text-base font-bold text-slate-900 font-display">
                                            {{ $property->title ?? ($property->bedrooms . ' BHK ' . $property->property_type) }}
                                        </h3>
                                        <div class="text-xs text-slate-500 flex items-center space-x-1">
                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                            <span>{{ $property->locality->name ?? 'Bangalore' }}</span>
                                        </div>
                                    </div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100 text-xs">
                        
                        <!-- Price Row -->
                        <tr class="hover:bg-slate-50/50">
                            <td class="p-6 font-bold text-slate-900 bg-slate-50/30">Asking / Listed Price</td>
                            @foreach($properties as $property)
                                <td class="p-6">
                                    <div class="text-xl font-black font-display text-emerald-600">
                                        {{ \App\Models\Prediction::formatPriceInIndianSystem($property->price) }}
                                    </div>
                                </td>
                            @endforeach
                        </tr>

                        <!-- Rate / sq.ft -->
                        <tr class="hover:bg-slate-50/50">
                            <td class="p-6 font-bold text-slate-900 bg-slate-50/30">Price per Sq.Ft</td>
                            @foreach($properties as $property)
                                <td class="p-6">
                                    <span class="font-bold text-slate-900 text-sm">₹{{ number_format($property->price_per_sqft, 0) }}</span> / sq.ft
                                </td>
                            @endforeach
                        </tr>

                        <!-- Super Built-up Area -->
                        <tr class="hover:bg-slate-50/50">
                            <td class="p-6 font-bold text-slate-900 bg-slate-50/30">Super Built-up Area</td>
                            @foreach($properties as $property)
                                <td class="p-6 font-semibold text-slate-800">
                                    {{ number_format($property->area) }} sq.ft
                                </td>
                            @endforeach
                        </tr>

                        <!-- Carpet Area -->
                        <tr class="hover:bg-slate-50/50">
                            <td class="p-6 font-bold text-slate-900 bg-slate-50/30">Carpet Area</td>
                            @foreach($properties as $property)
                                <td class="p-6 text-slate-700">
                                    {{ $property->carpet_area ? number_format($property->carpet_area) . ' sq.ft' : 'N/A' }}
                                </td>
                            @endforeach
                        </tr>

                        <!-- Bedrooms & Bathrooms -->
                        <tr class="hover:bg-slate-50/50">
                            <td class="p-6 font-bold text-slate-900 bg-slate-50/30">BHK / Bathrooms</td>
                            @foreach($properties as $property)
                                <td class="p-6 font-semibold text-slate-800">
                                    {{ $property->bedrooms }} BHK / {{ $property->bathrooms }} Baths
                                </td>
                            @endforeach
                        </tr>

                        <!-- Floor Position -->
                        <tr class="hover:bg-slate-50/50">
                            <td class="p-6 font-bold text-slate-900 bg-slate-50/30">Floor Level</td>
                            @foreach($properties as $property)
                                <td class="p-6 text-slate-700">
                                    Floor {{ $property->floor }} of {{ $property->total_floors }}
                                </td>
                            @endforeach
                        </tr>

                        <!-- Age / Condition -->
                        <tr class="hover:bg-slate-50/50">
                            <td class="p-6 font-bold text-slate-900 bg-slate-50/30">Property Age</td>
                            @foreach($properties as $property)
                                <td class="p-6 text-slate-700">
                                    {{ $property->property_age == 0 ? 'Brand New (< 1 yr)' : $property->property_age . ' Years' }}
                                </td>
                            @endforeach
                        </tr>

                        <!-- Furnishing -->
                        <tr class="hover:bg-slate-50/50">
                            <td class="p-6 font-bold text-slate-900 bg-slate-50/30">Furnishing</td>
                            @foreach($properties as $property)
                                <td class="p-6 text-slate-700">
                                    {{ $property->furnishing ?? 'Semi-Furnished' }}
                                </td>
                            @endforeach
                        </tr>

                        <!-- Locality Benchmark Score -->
                        <tr class="hover:bg-slate-50/50">
                            <td class="p-6 font-bold text-slate-900 bg-slate-50/30">Locality Score</td>
                            @foreach($properties as $property)
                                <td class="p-6">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-800 font-bold text-xs">
                                        ⭐ {{ $property->locality->location_score ?? '8.5' }}/10
                                    </span>
                                </td>
                            @endforeach
                        </tr>

                        <!-- Amenities List -->
                        <tr class="hover:bg-slate-50/50">
                            <td class="p-6 font-bold text-slate-900 bg-slate-50/30">Society Amenities</td>
                            @foreach($properties as $property)
                                <td class="p-6">
                                    <div class="flex flex-wrap gap-1.5">
                                        @forelse($property->amenities as $amenity)
                                            <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 text-[10px] font-medium">
                                                {{ $amenity->name }}
                                            </span>
                                        @empty
                                            <span class="text-slate-400 italic">Standard Amenities</span>
                                        @endforelse
                                    </div>
                                </td>
                            @endforeach
                        </tr>

                        <!-- Action Row -->
                        <tr class="bg-slate-50/70">
                            <td class="p-6 font-bold text-slate-900 bg-slate-50/30">Valuation Action</td>
                            @foreach($properties as $property)
                                <td class="p-6">
                                    <a href="{{ route('predict', ['area' => $property->area, 'bedrooms' => $property->bedrooms, 'property_type' => $property->property_type, 'locality' => $property->locality->name ?? '']) }}" 
                                       class="inline-flex items-center px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-sm transition">
                                        AI Valuation
                                    </a>
                                </td>
                            @endforeach
                        </tr>

                    </tbody>
                </table>
            </div>
        @endif

    </div>
</div>
@endsection
