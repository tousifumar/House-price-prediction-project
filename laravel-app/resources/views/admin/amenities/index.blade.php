@extends('layouts.app')

@section('title', 'Amenity Catalog Management — Admin Command Center')

@section('content')
<div class="py-10 bg-slate-900 min-h-screen text-slate-100" x-data="{ createModalOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        @include('admin.partials.nav', [
            'title' => 'Society Amenities Catalog',
            'subtitle' => 'Manage premium and standard residential amenities used in feature-weight valuation modeling.'
        ])

        <!-- Top Bar -->
        <div class="flex items-center justify-between bg-slate-800/80 border border-slate-700/80 rounded-2xl p-5 shadow-sm">
            <div>
                <span class="text-xs text-slate-400">Total cataloged features: <strong class="text-white">{{ $amenities->count() }}</strong></span>
            </div>

            <button @click="createModalOpen = true" class="inline-flex items-center px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Add Amenity
            </button>
        </div>

        <!-- CREATE AMENITY MODAL -->
        <div x-show="createModalOpen" 
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4"
             style="display: none;">
            <div @click.outside="createModalOpen = false" class="bg-slate-900 rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl border border-slate-700 space-y-6 text-slate-200">
                <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                    <h3 class="text-lg font-bold text-white font-display">Add Amenity to Catalog</h3>
                    <button @click="createModalOpen = false" class="text-slate-400 hover:text-white">✕</button>
                </div>

                <form method="POST" action="{{ route('admin.amenities.store') }}" class="space-y-4 text-xs">
                    @csrf

                    <div>
                        <label class="block font-bold uppercase tracking-wider text-slate-400 mb-1">Amenity Name</label>
                        <input type="text" name="name" required placeholder="e.g. EV Charging Station"
                               class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3.5 py-2.5 text-white">
                    </div>

                    <div>
                        <label class="block font-bold uppercase tracking-wider text-slate-400 mb-1">Category</label>
                        <select name="category" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3.5 py-2.5 text-white">
                            <option value="Luxury">Luxury</option>
                            <option value="Sports & Fitness">Sports & Fitness</option>
                            <option value="Safety & Security">Safety & Security</option>
                            <option value="Convenience">Convenience</option>
                            <option value="Eco-Friendly">Eco-Friendly</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold uppercase tracking-wider text-slate-400 mb-1">Description</label>
                        <input type="text" name="description" placeholder="Brief feature description..."
                               class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3.5 py-2.5 text-white">
                    </div>

                    <div class="pt-4 flex justify-end space-x-3">
                        <button type="button" @click="createModalOpen = false" class="px-4 py-2 rounded-xl border border-slate-700 text-slate-400">Cancel</button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold">Save Amenity</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Amenities Grid / Table -->
        <div class="bg-slate-800/80 border border-slate-700/80 rounded-3xl overflow-hidden shadow-xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-900/60 text-slate-400 uppercase font-bold border-b border-slate-700">
                        <tr>
                            <th class="py-3.5 px-6">Amenity Name</th>
                            <th class="py-3.5 px-6">Category</th>
                            <th class="py-3.5 px-6">Properties Included</th>
                            <th class="py-3.5 px-6">Status</th>
                            <th class="py-3.5 px-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/60">
                        @foreach($amenities as $amenity)
                            <tr class="hover:bg-slate-700/30 transition">
                                <td class="py-4 px-6">
                                    <div class="font-bold text-white text-sm flex items-center space-x-2">
                                        <span>{{ $amenity->name }}</span>
                                    </div>
                                    <div class="text-[11px] text-slate-400">{{ $amenity->description }}</div>
                                </td>
                                <td class="py-4 px-6 text-slate-300">
                                    <span class="px-2.5 py-1 rounded-lg bg-slate-900 border border-slate-700 text-[11px] font-medium">
                                        {{ $amenity->category }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-slate-300 font-semibold">
                                    {{ $amenity->properties_count }} properties
                                </td>
                                <td class="py-4 px-6">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase
                                        {{ $amenity->is_active ? 'bg-emerald-950 text-emerald-400 border border-emerald-800' : 'bg-slate-800 text-slate-500' }}">
                                        {{ $amenity->is_active ? 'Active' : 'Disabled' }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <form method="POST" action="{{ route('admin.amenities.toggle', $amenity->id) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="px-2.5 py-1 rounded-lg border border-slate-700 hover:bg-slate-700 text-slate-300 transition">
                                            {{ $amenity->is_active ? 'Disable' : 'Enable' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection
