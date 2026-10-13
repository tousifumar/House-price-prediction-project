@extends('layouts.app')

@section('title', 'Micro-Market & Benchmark Management — Admin Command Center')

@section('content')
<div class="py-10 bg-slate-900 min-h-screen text-slate-100" x-data="{ createModalOpen: false, editModalOpen: false, editLocality: {} }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        @include('admin.partials.nav', [
            'title' => 'Bangalore Micro-Market Benchmarks',
            'subtitle' => 'Curate sub-market rate indices, capital growth velocity, and location scores consumed by the AI valuation engine.'
        ])

        <!-- Top Controls & Action -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 bg-slate-800/80 border border-slate-700/80 rounded-2xl p-5 shadow-sm">
            <form method="GET" action="{{ route('admin.locations.index') }}" class="flex items-center space-x-3 w-full sm:w-auto">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search locality..."
                       class="bg-slate-900 border border-slate-700 text-slate-200 text-xs rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-emerald-500 w-64">
                <button type="submit" class="px-4 py-2.5 rounded-xl bg-slate-700 hover:bg-slate-600 text-white font-semibold text-xs transition">
                    Search
                </button>
            </form>

            <button @click="createModalOpen = true" class="inline-flex items-center px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Register Locality
            </button>
        </div>

        <!-- CREATE LOCALITY MODAL -->
        <div x-show="createModalOpen" 
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4"
             style="display: none;">
            <div @click.outside="createModalOpen = false" class="bg-slate-900 rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-700 space-y-6 text-slate-200">
                <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                    <h3 class="text-lg font-bold text-white font-display">Register Bangalore Micro-Market</h3>
                    <button @click="createModalOpen = false" class="text-slate-400 hover:text-white">✕</button>
                </div>

                <form method="POST" action="{{ route('admin.locations.localities.store') }}" class="space-y-4 text-xs">
                    @csrf
                    <input type="hidden" name="city_id" value="{{ $city->id ?? 1 }}">

                    <div>
                        <label class="block font-bold uppercase tracking-wider text-slate-400 mb-1">Locality Name</label>
                        <input type="text" name="name" required placeholder="e.g. Sarjapur Road"
                               class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3.5 py-2.5 text-white">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block font-bold uppercase tracking-wider text-slate-400 mb-1">Avg Rate (₹/sqft)</label>
                            <input type="number" name="avg_price_per_sqft" required min="1000" placeholder="8500"
                                   class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3.5 py-2.5 text-white">
                        </div>
                        <div>
                            <label class="block font-bold uppercase tracking-wider text-slate-400 mb-1">YoY Growth Rate (%)</label>
                            <input type="number" step="0.1" name="growth_rate" required placeholder="8.5"
                                   class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3.5 py-2.5 text-white">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block font-bold uppercase tracking-wider text-slate-400 mb-1">Location Score (1-10)</label>
                            <input type="number" step="0.1" name="location_score" required min="1" max="10" placeholder="8.4"
                                   class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3.5 py-2.5 text-white">
                        </div>
                        <div>
                            <label class="block font-bold uppercase tracking-wider text-slate-400 mb-1">Price Tier</label>
                            <select name="price_tier" required class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3.5 py-2.5 text-white">
                                <option value="Affordable">Affordable</option>
                                <option value="Mid">Mid</option>
                                <option value="High" selected>High</option>
                                <option value="Luxury">Luxury</option>
                                <option value="Ultra Luxury">Ultra Luxury</option>
                            </select>
                        </div>
                    </div>

                    <div class="pt-4 flex justify-end space-x-3">
                        <button type="button" @click="createModalOpen = false" class="px-4 py-2 rounded-xl border border-slate-700 text-slate-400">Cancel</button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold">Register Locality</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- EDIT LOCALITY BENCHMARKS MODAL -->
        <div x-show="editModalOpen" 
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4"
             style="display: none;">
            <div @click.outside="editModalOpen = false" class="bg-slate-900 rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-700 space-y-6 text-slate-200">
                <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                    <h3 class="text-lg font-bold text-white font-display">Update Benchmarks: <span x-text="editLocality.name" class="text-emerald-400"></span></h3>
                    <button @click="editModalOpen = false" class="text-slate-400 hover:text-white">✕</button>
                </div>

                <form :action="'/admin/locations/localities/' + editLocality.id" method="POST" class="space-y-4 text-xs">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block font-bold uppercase tracking-wider text-slate-400 mb-1">Avg Rate (₹/sqft)</label>
                            <input type="number" name="avg_price_per_sqft" x-model="editLocality.avg_price_per_sqft" required min="1000"
                                   class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3.5 py-2.5 text-white">
                        </div>
                        <div>
                            <label class="block font-bold uppercase tracking-wider text-slate-400 mb-1">YoY Growth Rate (%)</label>
                            <input type="number" step="0.1" name="growth_rate" x-model="editLocality.growth_rate" required
                                   class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3.5 py-2.5 text-white">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block font-bold uppercase tracking-wider text-slate-400 mb-1">Location Score (1-10)</label>
                            <input type="number" step="0.1" name="location_score" x-model="editLocality.location_score" required min="1" max="10"
                                   class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3.5 py-2.5 text-white">
                        </div>
                        <div>
                            <label class="block font-bold uppercase tracking-wider text-slate-400 mb-1">Price Tier</label>
                            <select name="price_tier" x-model="editLocality.price_tier" required class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3.5 py-2.5 text-white">
                                <option value="Affordable">Affordable</option>
                                <option value="Mid">Mid</option>
                                <option value="High">High</option>
                                <option value="Luxury">Luxury</option>
                                <option value="Ultra Luxury">Ultra Luxury</option>
                            </select>
                        </div>
                    </div>

                    <div class="pt-4 flex justify-end space-x-3">
                        <button type="button" @click="editModalOpen = false" class="px-4 py-2 rounded-xl border border-slate-700 text-slate-400">Cancel</button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Locality Benchmarks Table -->
        <div class="bg-slate-800/80 border border-slate-700/80 rounded-3xl overflow-hidden shadow-xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-900/60 text-slate-400 uppercase font-bold border-b border-slate-700">
                        <tr>
                            <th class="py-3.5 px-6">Micro-Market</th>
                            <th class="py-3.5 px-6">Benchmark Rate</th>
                            <th class="py-3.5 px-6">YoY Growth</th>
                            <th class="py-3.5 px-6">Score</th>
                            <th class="py-3.5 px-6">Tier</th>
                            <th class="py-3.5 px-6">Properties</th>
                            <th class="py-3.5 px-6">Status</th>
                            <th class="py-3.5 px-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/60">
                        @foreach($localities as $loc)
                            <tr class="hover:bg-slate-700/30 transition">
                                <td class="py-4 px-6 font-bold text-white text-sm">
                                    {{ $loc->name }}
                                </td>
                                <td class="py-4 px-6 font-black font-display text-emerald-400 text-sm">
                                    ₹{{ number_format($loc->avg_price_per_sqft, 0) }} <span class="text-xs text-slate-400 font-normal">/ sq.ft</span>
                                </td>
                                <td class="py-4 px-6 font-bold text-emerald-400">
                                    +{{ $loc->growth_rate }}%
                                </td>
                                <td class="py-4 px-6 text-slate-300">
                                    ⭐ {{ $loc->location_score }} / 10
                                </td>
                                <td class="py-4 px-6">
                                    <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold uppercase
                                        @if($loc->price_tier === 'Luxury' || $loc->price_tier === 'Ultra Luxury') bg-purple-950 text-purple-400 border border-purple-800
                                        @elseif($loc->price_tier === 'High') bg-indigo-950 text-indigo-400 border border-indigo-800
                                        @else bg-teal-950 text-teal-400 border border-teal-800 @endif">
                                        {{ $loc->price_tier }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-slate-300">
                                    {{ $loc->properties_count }} listings
                                </td>
                                <td class="py-4 px-6">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase
                                        {{ $loc->is_active ? 'bg-emerald-950 text-emerald-400 border border-emerald-800' : 'bg-slate-800 text-slate-500' }}">
                                        {{ $loc->is_active ? 'Active' : 'Disabled' }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center space-x-2">
                                        <!-- Edit Modal Trigger -->
                                        <button @click="editLocality = {{ json_encode($loc) }}; editModalOpen = true;"
                                                class="px-2.5 py-1 rounded-lg bg-slate-700 hover:bg-slate-600 text-white font-semibold transition" title="Edit Benchmarks">
                                            Edit
                                        </button>

                                        <!-- Toggle Status -->
                                        <form method="POST" action="{{ route('admin.locations.localities.toggle', $loc->id) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="px-2.5 py-1 rounded-lg border border-slate-700 hover:bg-slate-700 text-slate-300 transition" title="Toggle active status">
                                                {{ $loc->is_active ? 'Disable' : 'Enable' }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($localities->hasPages())
                <div class="p-4 border-t border-slate-700/80">
                    {{ $localities->withQueryString()->links() }}
                </div>
            @endif
        </div>

    </div>
</div>
@endsection
