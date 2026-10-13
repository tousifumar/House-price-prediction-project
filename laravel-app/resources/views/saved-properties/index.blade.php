@extends('layouts.app')

@section('title', 'Saved Properties Portfolio — House Price Predictor')

@section('content')
<div class="py-10 bg-slate-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        <!-- Header Banner -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-teal-50 border border-teal-200 text-teal-700 text-xs font-semibold mb-2">
                    <span>Portfolio Tracker</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-display">Saved Properties Portfolio</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">Bookmark high-yield prospective investments, add private notes, and benchmark valuations.</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('compare') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-semibold text-xs shadow-sm transition">
                    <svg class="w-4 h-4 mr-1.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Compare Properties
                </a>
                <a href="{{ route('predict') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md shadow-emerald-600/20 transition">
                    <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    New Valuation
                </a>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
            <form method="GET" action="{{ route('saved-properties.index') }}" class="flex items-center space-x-3 w-full sm:w-auto">
                <select name="locality_id" onchange="this.form.submit()" class="bg-slate-50 border border-slate-200 text-slate-800 text-xs rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-emerald-500 font-medium">
                    <option value="">Filter by Micro-Market (All)</option>
                    @foreach($localities as $loc)
                        <option value="{{ $loc->id }}" {{ request('locality_id') == $loc->id ? 'selected' : '' }}>
                            {{ $loc->name }}
                        </option>
                    @endforeach
                </select>
                @if(request('locality_id'))
                    <a href="{{ route('saved-properties.index') }}" class="text-xs text-rose-600 hover:underline font-semibold">Clear Filter</a>
                @endif
            </form>

            <span class="text-xs text-slate-500">
                Showing <strong class="text-slate-800">{{ $savedProperties->total() }}</strong> saved properties
            </span>
        </div>

        <!-- Saved Properties Grid -->
        @if($savedProperties->isEmpty())
            <div class="bg-white rounded-3xl p-12 sm:p-16 text-center border border-slate-200 shadow-sm space-y-4">
                <div class="w-16 h-16 rounded-2xl bg-teal-50 text-teal-600 mx-auto flex items-center justify-center text-3xl">
                    ⭐
                </div>
                <div class="space-y-1">
                    <h3 class="text-lg font-bold text-slate-900">Your saved portfolio is empty</h3>
                    <p class="text-xs text-slate-500 max-w-sm mx-auto">
                        Save properties from valuation result pages or marketplace searches to monitor their market worth over time.
                    </p>
                </div>
                <div>
                    <a href="{{ route('predict') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl bg-emerald-600 text-white font-bold text-xs shadow-md">
                        Evaluate & Save Property
                    </a>
                </div>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($savedProperties as $saved)
                    @php $property = $saved->property; @endphp
                    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm hover:shadow-md transition-shadow overflow-hidden flex flex-col justify-between">
                        <div>
                            <!-- Card Header -->
                            <div class="p-6 border-b border-slate-100 bg-gradient-to-br from-slate-50 to-white space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 text-xs font-bold">
                                        {{ $property->bedrooms }} BHK {{ $property->property_type }}
                                    </span>
                                    <span class="text-[11px] text-slate-400 font-medium">
                                        Saved {{ $saved->created_at->diffForHumans() }}
                                    </span>
                                </div>
                                <h3 class="text-base font-bold text-slate-900 line-clamp-1">
                                    {{ $property->title ?? ($property->bedrooms . ' BHK ' . $property->property_type . ' in ' . ($property->locality->name ?? 'Bangalore')) }}
                                </h3>
                                <div class="text-xs text-slate-500 flex items-center space-x-1">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                    <span>{{ $property->locality->name ?? 'Bangalore' }}, {{ $property->locality->city->name ?? 'Bangalore' }}</span>
                                </div>
                            </div>

                            <!-- Specs & Price Body -->
                            <div class="p-6 space-y-4">
                                <div class="flex items-baseline justify-between">
                                    <div>
                                        <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block">Price / Quote</span>
                                        <div class="text-2xl font-black font-display text-slate-900">
                                            {{ \App\Models\Prediction::formatPriceInIndianSystem($property->price) }}
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block">Rate / sq.ft</span>
                                        <div class="text-sm font-bold text-emerald-600">
                                            ₹{{ number_format($property->price_per_sqft, 0) }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Key Metrics Grid -->
                                <div class="grid grid-cols-3 gap-2 text-center py-2 px-3 rounded-xl bg-slate-50 border border-slate-100 text-xs">
                                    <div>
                                        <span class="text-[10px] text-slate-400 block font-semibold">Area</span>
                                        <span class="font-bold text-slate-800">{{ number_format($property->area) }} sq.ft</span>
                                    </div>
                                    <div>
                                        <span class="text-[10px] text-slate-400 block font-semibold">Baths</span>
                                        <span class="font-bold text-slate-800">{{ $property->bathrooms }}</span>
                                    </div>
                                    <div>
                                        <span class="text-[10px] text-slate-400 block font-semibold">Floor</span>
                                        <span class="font-bold text-slate-800">{{ $property->floor }}/{{ $property->total_floors }}</span>
                                    </div>
                                </div>

                                <!-- Notes Section -->
                                @if($saved->notes)
                                    <div class="p-3 rounded-xl bg-amber-50/60 border border-amber-200/60 text-xs text-amber-900 space-y-1">
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-amber-800 block">Personal Notes:</span>
                                        <p class="leading-relaxed">{{ $saved->notes }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Card Footer / Actions -->
                        <div class="p-6 pt-0 flex items-center justify-between border-t border-slate-100 gap-2 mt-2">
                            <a href="{{ route('predict', ['area' => $property->area, 'bedrooms' => $property->bedrooms, 'property_type' => $property->property_type, 'locality' => $property->locality->name ?? '']) }}" 
                               class="inline-flex items-center text-xs font-bold text-emerald-600 hover:text-emerald-700 py-2">
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2m0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                Re-value with AI
                            </a>

                            <div class="flex items-center space-x-2">
                                <a href="{{ route('compare', ['ids' => [$property->id]]) }}" class="p-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition" title="Compare this property">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                </a>

                                <form method="POST" action="{{ route('saved-properties.destroy', $saved->id) }}" onsubmit="return confirm('Remove property from saved portfolio?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 rounded-lg bg-white border border-slate-200 hover:bg-rose-50 hover:text-rose-600 text-slate-400 transition" title="Remove from portfolio">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($savedProperties->hasPages())
                <div class="pt-4">
                    {{ $savedProperties->withQueryString()->links() }}
                </div>
            @endif
        @endif

    </div>
</div>
@endsection
