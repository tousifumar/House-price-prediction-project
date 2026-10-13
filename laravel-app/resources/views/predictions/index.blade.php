@extends('layouts.app')

@section('title', 'Valuation History — House Price Predictor')

@section('content')
<div class="py-10 bg-slate-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        <!-- Header Banner -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-display">Your Valuation History</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">Review, filter, and export executive PDF reports of all past AI valuation queries.</p>
            </div>
            <a href="{{ route('predict') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md shadow-emerald-600/20 transition group">
                <svg class="w-4 h-4 mr-1.5 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                New Valuation
            </a>
        </div>

        <!-- Filter & Search Toolbar -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
            <form method="GET" action="{{ route('predictions.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
                <!-- Locality Filter -->
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Locality</label>
                    <select name="locality_id" class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-xs rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-emerald-500">
                        <option value="">All Localities</option>
                        @foreach($localities as $loc)
                            <option value="{{ $loc->id }}" {{ request('locality_id') == $loc->id ? 'selected' : '' }}>
                                {{ $loc->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Property Type Filter -->
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Property Type</label>
                    <select name="property_type" class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-xs rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-emerald-500">
                        <option value="">All Types</option>
                        @foreach(['Apartment', 'Villa', 'Independent House', 'Builder Floor', 'Penthouse', 'Studio'] as $type)
                            <option value="{{ $type }}" {{ request('property_type') == $type ? 'selected' : '' }}>
                                {{ $type }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Min Price -->
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Min Price (₹)</label>
                    <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="e.g. 5000000"
                           class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-xs rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-emerald-500">
                </div>

                <!-- Max Price -->
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Max Price (₹)</label>
                    <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="e.g. 25000000"
                           class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-xs rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-emerald-500">
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center space-x-2">
                    <button type="submit" class="w-full bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs py-2.5 px-4 rounded-xl transition">
                        Filter
                    </button>
                    @if(request()->hasAny(['locality_id', 'property_type', 'min_price', 'max_price']))
                        <a href="{{ route('predictions.index') }}" class="p-2.5 rounded-xl border border-slate-200 hover:bg-slate-100 text-slate-500" title="Reset Filters">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- History Records Table Card -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
            @if($predictions->isEmpty())
                <div class="p-12 sm:p-16 text-center space-y-4">
                    <div class="w-16 h-16 rounded-2xl bg-emerald-50 text-emerald-600 mx-auto flex items-center justify-center text-2xl">
                        📊
                    </div>
                    <div class="space-y-1">
                        <h3 class="text-base font-bold text-slate-900">No valuation records found</h3>
                        <p class="text-xs text-slate-500 max-w-sm mx-auto">
                            @if(request()->hasAny(['locality_id', 'property_type', 'min_price', 'max_price']))
                                Try adjusting or clearing your filters to see historical valuations.
                            @else
                                You haven't run any valuations yet. Use our multi-variable AI engine to evaluate properties.
                            @endif
                        </p>
                    </div>
                    <div>
                        <a href="{{ route('predict') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl bg-emerald-600 text-white font-bold text-xs shadow-md">
                            Run Valuation
                        </a>
                    </div>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 text-slate-500 text-xs uppercase font-semibold border-b border-slate-100">
                            <tr>
                                <th class="py-4 px-6">Property / Location</th>
                                <th class="py-4 px-6">Specifications</th>
                                <th class="py-4 px-6">Estimated Market Price</th>
                                <th class="py-4 px-6">Confidence Range</th>
                                <th class="py-4 px-6">Date</th>
                                <th class="py-4 px-6 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($predictions as $prediction)
                                <tr class="hover:bg-slate-50/70 transition-colors">
                                    <!-- Property & Location -->
                                    <td class="py-4 px-6">
                                        <div class="font-bold text-slate-900 flex items-center space-x-2">
                                            <span>{{ $prediction->property_type }}</span>
                                            <span class="text-[10px] px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 font-semibold uppercase">
                                                #{{ $prediction->id }}
                                            </span>
                                        </div>
                                        <div class="text-xs text-slate-500 flex items-center space-x-1 mt-0.5">
                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                            <span>{{ $prediction->locality->name ?? 'Bangalore' }}, {{ $prediction->city->name ?? 'Bangalore' }}</span>
                                        </div>
                                    </td>

                                    <!-- Specs -->
                                    <td class="py-4 px-6">
                                        <div class="text-xs font-semibold text-slate-800">
                                            {{ $prediction->bedrooms }} BHK • {{ number_format($prediction->area) }} sq.ft
                                        </div>
                                        <div class="text-[11px] text-slate-500">
                                            ₹{{ number_format($prediction->price_per_sqft, 0) }}/sq.ft
                                        </div>
                                    </td>

                                    <!-- Estimated Price -->
                                    <td class="py-4 px-6">
                                        <div class="text-base font-black font-display text-emerald-600">
                                            {{ $prediction->formatted_price }}
                                        </div>
                                    </td>

                                    <!-- Range & Confidence -->
                                    <td class="py-4 px-6">
                                        <div class="text-xs text-slate-700 font-medium">
                                            {{ $prediction->formatted_min_price }} – {{ $prediction->formatted_max_price }}
                                        </div>
                                        <div class="text-[10px] text-slate-400">
                                            Confidence: {{ number_format($prediction->confidence_score, 1) }}%
                                        </div>
                                    </td>

                                    <!-- Date -->
                                    <td class="py-4 px-6 text-xs text-slate-500 whitespace-nowrap">
                                        {{ $prediction->created_at->format('M d, Y') }}
                                        <div class="text-[10px] text-slate-400">{{ $prediction->created_at->format('h:i A') }}</div>
                                    </td>

                                    <!-- Actions -->
                                    <td class="py-4 px-6 text-right whitespace-nowrap">
                                        <div class="inline-flex items-center space-x-2">
                                            <a href="{{ route('predictions.show', $prediction->id) }}" 
                                               class="p-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 transition" 
                                               title="View Full Dashboard">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            </a>

                                            <a href="{{ route('predictions.pdf', $prediction->id) }}" 
                                               class="p-2 rounded-lg bg-slate-900 hover:bg-slate-800 text-white transition" 
                                               title="Download Executive PDF Report">
                                                <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            </a>

                                            <form method="POST" action="{{ route('predictions.destroy', $prediction->id) }}" onsubmit="return confirm('Are you sure you want to remove this prediction record from history?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-2 rounded-lg bg-white border border-slate-200 hover:bg-rose-50 hover:text-rose-600 text-slate-400 transition" title="Delete record">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($predictions->hasPages())
                    <div class="px-6 py-4 border-t border-slate-100">
                        {{ $predictions->withQueryString()->links() }}
                    </div>
                @endif
            @endif
        </div>

    </div>
</div>
@endsection
