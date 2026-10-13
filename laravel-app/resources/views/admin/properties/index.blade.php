@extends('layouts.app')

@section('title', 'Property Catalog Management — Admin Command Center')

@section('content')
<div class="py-10 bg-slate-900 min-h-screen text-slate-100" x-data="{ createModalOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        @include('admin.partials.nav', [
            'title' => 'Property Catalog Management',
            'subtitle' => 'Index, curate, and maintain verified residential real estate listings across Bangalore micro-markets.'
        ])

        <!-- Top Controls & Create Button -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 bg-slate-800/80 border border-slate-700/80 rounded-2xl p-5 shadow-sm">
            <form method="GET" action="{{ route('admin.properties.index') }}" class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
                <select name="locality_id" onchange="this.form.submit()" class="bg-slate-900 border border-slate-700 text-slate-200 text-xs rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-emerald-500">
                    <option value="">All Micro-Markets</option>
                    @foreach($localities as $loc)
                        <option value="{{ $loc->id }}" {{ request('locality_id') == $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                    @endforeach
                </select>

                <select name="property_type" onchange="this.form.submit()" class="bg-slate-900 border border-slate-700 text-slate-200 text-xs rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-emerald-500">
                    <option value="">All Typologies</option>
                    @foreach(['Apartment', 'Villa', 'Independent House', 'Builder Floor', 'Penthouse'] as $type)
                        <option value="{{ $type }}" {{ request('property_type') === $type ? 'selected' : '' }}>{{ $type }}</option>
                    @endforeach
                </select>

                @if(request()->hasAny(['locality_id', 'property_type']))
                    <a href="{{ route('admin.properties.index') }}" class="text-xs text-rose-400 hover:underline">Clear</a>
                @endif
            </form>

            <button @click="createModalOpen = true" class="inline-flex items-center px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Add New Listing
            </button>
        </div>

        <!-- CREATE PROPERTY MODAL -->
        <div x-show="createModalOpen" 
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4"
             style="display: none;"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            
            <div @click.outside="createModalOpen = false" class="bg-slate-900 rounded-3xl max-w-2xl w-full p-6 sm:p-8 shadow-2xl border border-slate-700 space-y-6 text-slate-200">
                <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                    <h3 class="text-lg font-bold text-white font-display">Add Property to Catalog</h3>
                    <button @click="createModalOpen = false" class="text-slate-400 hover:text-white">✕</button>
                </div>

                <form method="POST" action="{{ route('admin.properties.store') }}" class="space-y-4 text-xs">
                    @csrf

                    <div>
                        <label class="block font-bold uppercase tracking-wider text-slate-400 mb-1">Title / Community Name</label>
                        <input type="text" name="title" required placeholder="e.g. Prestige Lakeside Habitat 3BHK"
                               class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3.5 py-2.5 text-white">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block font-bold uppercase tracking-wider text-slate-400 mb-1">Micro-Market Locality</label>
                            <select name="locality_id" required class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3.5 py-2.5 text-white">
                                @foreach($localities as $loc)
                                    <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold uppercase tracking-wider text-slate-400 mb-1">Property Type</label>
                            <select name="property_type" required class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3.5 py-2.5 text-white">
                                @foreach(['Apartment', 'Villa', 'Independent House', 'Builder Floor', 'Penthouse'] as $type)
                                    <option value="{{ $type }}">{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block font-bold uppercase tracking-wider text-slate-400 mb-1">Price (₹)</label>
                            <input type="number" name="price" required min="100000" step="50000" placeholder="15000000"
                                   class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3.5 py-2.5 text-white">
                        </div>
                        <div>
                            <label class="block font-bold uppercase tracking-wider text-slate-400 mb-1">Built-up Area (sqft)</label>
                            <input type="number" name="area" required min="100" placeholder="1650"
                                   class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3.5 py-2.5 text-white">
                        </div>
                        <div>
                            <label class="block font-bold uppercase tracking-wider text-slate-400 mb-1">Bedrooms (BHK)</label>
                            <input type="number" name="bedrooms" required min="1" max="10" value="3"
                                   class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3.5 py-2.5 text-white">
                        </div>
                    </div>

                    <div class="grid grid-cols-4 gap-4">
                        <div>
                            <label class="block font-bold uppercase tracking-wider text-slate-400 mb-1">Baths</label>
                            <input type="number" name="bathrooms" required min="1" max="10" value="2"
                                   class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3.5 py-2.5 text-white">
                        </div>
                        <div>
                            <label class="block font-bold uppercase tracking-wider text-slate-400 mb-1">Floor</label>
                            <input type="number" name="floor" required min="0" value="5"
                                   class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3.5 py-2.5 text-white">
                        </div>
                        <div>
                            <label class="block font-bold uppercase tracking-wider text-slate-400 mb-1">Total Floors</label>
                            <input type="number" name="total_floors" required min="1" value="14"
                                   class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3.5 py-2.5 text-white">
                        </div>
                        <div>
                            <label class="block font-bold uppercase tracking-wider text-slate-400 mb-1">Age (Yrs)</label>
                            <input type="number" name="property_age" required min="0" value="2"
                                   class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3.5 py-2.5 text-white">
                        </div>
                    </div>

                    <input type="hidden" name="transaction_type" value="Sale">
                    <input type="hidden" name="furnishing" value="Semi-Furnished">

                    <!-- Amenities Select -->
                    <div>
                        <label class="block font-bold uppercase tracking-wider text-slate-400 mb-1.5">Society Amenities</label>
                        <div class="grid grid-cols-3 gap-2 max-h-32 overflow-y-auto p-2 bg-slate-950 rounded-xl border border-slate-800">
                            @foreach($amenities as $amenity)
                                <label class="flex items-center space-x-2 text-[11px] text-slate-300">
                                    <input type="checkbox" name="amenities[]" value="{{ $amenity->id }}" class="rounded bg-slate-800 border-slate-700 text-emerald-500">
                                    <span>{{ $amenity->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="pt-4 flex justify-end space-x-3">
                        <button type="button" @click="createModalOpen = false" class="px-4 py-2 rounded-xl border border-slate-700 text-slate-400">Cancel</button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold">Publish Listing</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Property Catalog Table -->
        <div class="bg-slate-800/80 border border-slate-700/80 rounded-3xl overflow-hidden shadow-xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-900/60 text-slate-400 uppercase font-bold border-b border-slate-700">
                        <tr>
                            <th class="py-3.5 px-6">Property / Title</th>
                            <th class="py-3.5 px-6">Specifications</th>
                            <th class="py-3.5 px-6">Price</th>
                            <th class="py-3.5 px-6">Rate / Sqft</th>
                            <th class="py-3.5 px-6">Status</th>
                            <th class="py-3.5 px-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/60">
                        @foreach($properties as $prop)
                            <tr class="hover:bg-slate-700/30 transition">
                                <td class="py-4 px-6">
                                    <div class="font-bold text-white text-sm">{{ $prop->title }}</div>
                                    <div class="text-[11px] text-slate-400">{{ $prop->locality->name ?? 'Bangalore' }}</div>
                                </td>
                                <td class="py-4 px-6 text-slate-300">
                                    {{ $prop->bedrooms }} BHK • {{ number_format($prop->area) }} sq.ft • Floor {{ $prop->floor }}/{{ $prop->total_floors }}
                                </td>
                                <td class="py-4 px-6 font-black font-display text-emerald-400 text-sm">
                                    {{ \App\Models\Prediction::formatPriceInIndianSystem($prop->price) }}
                                </td>
                                <td class="py-4 px-6 text-slate-300 font-semibold">
                                    ₹{{ number_format($prop->price_per_sqft, 0) }}
                                </td>
                                <td class="py-4 px-6">
                                    <span class="px-2 py-0.5 rounded-full bg-emerald-950 text-emerald-400 border border-emerald-800 text-[10px] font-bold uppercase">
                                        {{ $prop->status }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <form method="POST" action="{{ route('admin.properties.destroy', $prop->id) }}" onsubmit="return confirm('Delete this property listing?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1 rounded-lg hover:bg-rose-950/60 text-slate-400 hover:text-rose-400 transition" title="Delete Listing">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($properties->hasPages())
                <div class="p-4 border-t border-slate-700/80">
                    {{ $properties->withQueryString()->links() }}
                </div>
            @endif
        </div>

    </div>
</div>
@endsection
