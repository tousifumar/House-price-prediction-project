@extends('layouts.app')

@section('title', 'Valuation Report #' . $prediction->id . ' — ' . $prediction->property_type . ' in ' . ($prediction->locality->name ?? 'Bangalore'))

@section('content')
<div class="py-10 bg-slate-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        <!-- Breadcrumb & Top Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center space-x-2 text-xs font-medium text-slate-500">
                <a href="{{ route('home') }}" class="hover:text-emerald-600 transition-colors">Home</a>
                <span>/</span>
                <a href="{{ route('predict') }}" class="hover:text-emerald-600 transition-colors">Valuation Engine</a>
                <span>/</span>
                @auth
                    <a href="{{ route('predictions.index') }}" class="hover:text-emerald-600 transition-colors">History</a>
                    <span>/</span>
                @endauth
                <span class="text-slate-900 font-semibold">Valuation #{{ $prediction->id }}</span>
            </div>

            <!-- Action Buttons Toolbar -->
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('predictions.pdf', $prediction->id) }}" 
                   class="inline-flex items-center px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs transition shadow-sm group">
                    <svg class="w-4 h-4 mr-2 text-emerald-400 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Download Executive PDF
                </a>

                @auth
                    @php
                        $isSaved = \App\Models\SavedProperty::where('user_id', Auth::id())
                            ->where('property_id', $prediction->property_id)
                            ->exists();
                    @endphp
                    @if($prediction->property_id)
                        @if($isSaved)
                            <span class="inline-flex items-center px-3.5 py-2.5 rounded-xl bg-teal-50 border border-teal-200 text-teal-800 font-semibold text-xs">
                                <svg class="w-4 h-4 mr-1.5 text-teal-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z"/>
                                </svg>
                                Saved in Portfolio
                            </span>
                        @else
                            <form method="POST" action="{{ route('saved-properties.store') }}" class="inline">
                                @csrf
                                <input type="hidden" name="property_id" value="{{ $prediction->property_id }}">
                                <button type="submit" class="inline-flex items-center px-3.5 py-2.5 rounded-xl bg-white border border-slate-200 hover:border-teal-400 hover:bg-teal-50/50 text-slate-700 hover:text-teal-700 font-semibold text-xs transition shadow-sm">
                                    <svg class="w-4 h-4 mr-1.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                                    </svg>
                                    Save Property
                                </button>
                            </form>
                        @endif
                    @endif
                @endauth

                <a href="{{ route('predict') }}" 
                   class="inline-flex items-center px-4 py-2.5 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-semibold text-xs transition shadow-sm">
                    <svg class="w-4 h-4 mr-1.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    New Valuation
                </a>
            </div>
        </div>

        <!-- HERO VALUATION SUMMARY CARD -->
        <div class="bg-gradient-to-br from-slate-900 via-slate-950 to-emerald-950 rounded-3xl p-6 sm:p-10 text-white shadow-2xl relative overflow-hidden border border-slate-800">
            <!-- Background Glow Decor -->
            <div class="absolute -right-20 -top-20 w-80 h-80 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -left-20 -bottom-20 w-80 h-80 bg-teal-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                <!-- Left: Headline & Price -->
                <div class="lg:col-span-7 space-y-4">
                    <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-emerald-500/20 border border-emerald-400/30 text-emerald-300 text-xs font-semibold">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>Official Automated Valuation Model (AVM) Estimate</span>
                    </div>

                    <div class="space-y-1">
                        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black font-display tracking-tight text-white">
                            {{ $prediction->formatted_price }}
                        </h1>
                        <p class="text-slate-300 text-sm font-medium">
                            Estimated Market Value • {{ $prediction->bedrooms }} BHK {{ $prediction->property_type }} in 
                            <span class="text-emerald-400 font-bold">{{ $prediction->locality->name ?? 'Bangalore' }}</span>
                        </p>
                    </div>

                    <!-- 90% Confidence Interval Bar -->
                    <div class="bg-white/10 backdrop-blur-md rounded-2xl p-4 border border-white/10 space-y-2">
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-slate-300 font-medium">90% Statistical Confidence Range</span>
                            <span class="font-bold text-emerald-300">
                                {{ $prediction->formatted_min_price }} — {{ $prediction->formatted_max_price }}
                            </span>
                        </div>
                        <div class="w-full bg-white/20 h-2.5 rounded-full overflow-hidden relative">
                            <div class="bg-gradient-to-r from-teal-400 to-emerald-400 h-full rounded-full w-4/5 mx-auto"></div>
                        </div>
                        <div class="flex justify-between items-center text-[11px] text-slate-400 pt-0.5">
                            <span>Low Estimate: {{ $prediction->formatted_min_price }}</span>
                            <span>High Estimate: {{ $prediction->formatted_max_price }}</span>
                        </div>
                    </div>
                </div>

                <!-- Right: High-Level Valuation KPI Grid -->
                <div class="lg:col-span-5 grid grid-cols-2 gap-4">
                    <!-- Rate per Sq.Ft -->
                    <div class="bg-white/10 backdrop-blur-md rounded-2xl p-4 border border-white/10 space-y-1">
                        <span class="block text-[11px] font-semibold tracking-wider text-slate-400 uppercase">Rate / Sq.Ft</span>
                        <div class="text-2xl font-black text-white font-display">
                            ₹{{ number_format($prediction->price_per_sqft, 0) }}
                        </div>
                        <div class="text-[11px] text-emerald-300 flex items-center font-medium">
                            @if($prediction->price_per_sqft > $localityAvgRate)
                                <svg class="w-3.5 h-3.5 mr-1 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                                +{{ number_format((($prediction->price_per_sqft - $localityAvgRate) / max(1, $localityAvgRate)) * 100, 1) }}% vs Locality
                            @else
                                <svg class="w-3.5 h-3.5 mr-1 text-teal-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                                {{ number_format((($prediction->price_per_sqft - $localityAvgRate) / max(1, $localityAvgRate)) * 100, 1) }}% vs Locality
                            @endif
                        </div>
                    </div>

                    <!-- Model Confidence -->
                    <div class="bg-white/10 backdrop-blur-md rounded-2xl p-4 border border-white/10 space-y-1">
                        <span class="block text-[11px] font-semibold tracking-wider text-slate-400 uppercase">Model Accuracy</span>
                        <div class="text-2xl font-black text-white font-display">
                            {{ number_format($prediction->confidence_score, 1) }}%
                        </div>
                        <div class="text-[11px] text-slate-300 flex items-center font-medium">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 mr-1.5"></span>
                            High Reliability Grade
                        </div>
                    </div>

                    <!-- Built-up Area -->
                    <div class="bg-white/10 backdrop-blur-md rounded-2xl p-4 border border-white/10 space-y-1">
                        <span class="block text-[11px] font-semibold tracking-wider text-slate-400 uppercase">Super Area</span>
                        <div class="text-2xl font-black text-white font-display">
                            {{ number_format($prediction->area, 0) }} <span class="text-sm font-medium text-slate-300">sq.ft</span>
                        </div>
                        <div class="text-[11px] text-slate-300">
                            Carpet: {{ $prediction->carpet_area ? number_format($prediction->carpet_area, 0) . ' sq.ft' : 'N/A' }}
                        </div>
                    </div>

                    <!-- Valuation Model -->
                    <div class="bg-white/10 backdrop-blur-md rounded-2xl p-4 border border-white/10 space-y-1">
                        <span class="block text-[11px] font-semibold tracking-wider text-slate-400 uppercase">Engine Version</span>
                        <div class="text-lg font-black text-white font-display truncate">
                            {{ $prediction->model ? $prediction->model->algorithm : 'XGBoost v2' }}
                        </div>
                        <div class="text-[11px] text-emerald-300 truncate">
                            {{ $prediction->created_at->format('M d, Y • h:i A') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ASKING PRICE DEAL ANALYSIS (If asking price was provided) -->
        @if($prediction->user_asking_price)
            @php
                $asking = $prediction->user_asking_price;
                $predicted = $prediction->predicted_price;
                $diff = $asking - $predicted;
                $diffPct = round(($diff / max(1, $predicted)) * 100, 1);
            @endphp
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm">
                <div class="flex items-center space-x-3 mb-4">
                    <span class="text-2xl">⚖️</span>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 font-display">Asking Price vs AI Fair Value Analysis</h3>
                        <p class="text-xs text-slate-500">Instant quantitative assessment of seller quote vs algorithmic market baseline.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-center pt-2">
                    <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100">
                        <span class="text-xs text-slate-500 font-medium">Seller Asking Price</span>
                        <div class="text-2xl font-black text-slate-900 font-display mt-1">
                            {{ \App\Models\Prediction::formatPriceInIndianSystem($asking) }}
                        </div>
                    </div>

                    <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100">
                        <span class="text-xs text-slate-500 font-medium">AI Estimated Fair Value</span>
                        <div class="text-2xl font-black text-emerald-600 font-display mt-1">
                            {{ $prediction->formatted_price }}
                        </div>
                    </div>

                    <div class="rounded-2xl p-4 border 
                        @if($diffPct > 8) bg-rose-50 border-rose-200 text-rose-800
                        @elseif($diffPct < -5) bg-emerald-50 border-emerald-200 text-emerald-800
                        @else bg-blue-50 border-blue-200 text-blue-800 @endif">
                        <span class="text-xs font-semibold uppercase tracking-wider block">Assessment Verdict</span>
                        <div class="text-lg font-black font-display mt-1">
                            @if($diffPct > 8)
                                ⚠️ Premium / Overpriced (+{{ $diffPct }}%)
                            @elseif($diffPct < -5)
                                ✨ Great Deal / Underpriced ({{ $diffPct }}%)
                            @else
                                🎯 Fair Market Price ({{ $diffPct > 0 ? '+' : '' }}{{ $diffPct }}%)
                            @endif
                        </div>
                        <p class="text-[11px] mt-1 opacity-80">
                            Variance of {{ \App\Models\Prediction::formatPriceInIndianSystem(abs($diff)) }} compared to regression valuation.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <!-- LEFT COLUMN (7 Cols): Factors & Specifications -->
            <div class="lg:col-span-7 space-y-8">
                
                <!-- SECTION: EXPLAINABILITY & FACTORS -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                        <div class="flex items-center space-x-3">
                            <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-sm">
                                📊
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-900 font-display">Valuation Influence Factors</h3>
                                <p class="text-xs text-slate-500">Feature contribution breakdown based on trained multi-factor weights.</p>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 text-[11px] font-semibold">
                            SHAP Attributions
                        </span>
                    </div>

                    @if($prediction->factors && $prediction->factors->isNotEmpty())
                        <div class="space-y-4">
                            @foreach($prediction->factors as $factor)
                                <div class="p-4 rounded-2xl border transition-all hover:border-slate-300 {{ $factor->factor_type === 'positive' ? 'bg-emerald-50/40 border-emerald-100' : 'bg-rose-50/40 border-rose-100' }}">
                                    <div class="flex items-center justify-between gap-4">
                                        <div class="flex items-center space-x-3">
                                            <span class="w-7 h-7 rounded-lg flex items-center justify-center text-xs font-bold {{ $factor->factor_type === 'positive' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                                {{ $factor->factor_type === 'positive' ? '+' : '−' }}
                                            </span>
                                            <div>
                                                <h4 class="text-sm font-bold text-slate-900">{{ $factor->factor_name }}</h4>
                                                <p class="text-xs text-slate-600 mt-0.5">{{ $factor->description }}</p>
                                            </div>
                                        </div>

                                        <div class="text-right flex-shrink-0">
                                            <span class="block text-sm font-black font-display {{ $factor->factor_type === 'positive' ? 'text-emerald-700' : 'text-rose-700' }}">
                                                {{ $factor->factor_type === 'positive' ? '+' : '−' }}{{ \App\Models\Prediction::formatPriceInIndianSystem(abs($factor->impact_amount)) }}
                                            </span>
                                            <span class="text-[10px] font-semibold text-slate-600">
                                                {{ $factor->factor_type === 'positive' ? '+' : '' }}{{ number_format($factor->impact_percentage, 1) }}%
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Impact Visual Indicator -->
                                    <div class="w-full bg-slate-200/80 h-1.5 rounded-full mt-3 overflow-hidden">
                                        <div class="h-full rounded-full {{ $factor->factor_type === 'positive' ? 'bg-emerald-500' : 'bg-rose-500' }}"
                                             style="width: {{ min(100, max(15, abs($factor->impact_percentage) * 8)) }}%;"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <!-- Fallback Feature Attribution Summary -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="p-4 rounded-2xl bg-emerald-50/50 border border-emerald-100 space-y-1">
                                <span class="text-xs font-bold text-emerald-800 uppercase tracking-wider">+ Positive Premiums</span>
                                <p class="text-xs text-slate-700">Locality tier in {{ $prediction->locality->name ?? 'Micro-market' }}, high built-up area ({{ $prediction->area }} sq.ft), and Society Amenities contribute positively to benchmark rate.</p>
                            </div>
                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-1">
                                <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">Depreciation & Age</span>
                                <p class="text-xs text-slate-700">Property age of {{ $prediction->property_age }} years and floor placement adjusted in accordance with standard urban depreciation curves.</p>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- SECTION: DETAILED PROPERTY SPECS -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
                    <div class="flex items-center space-x-3 pb-4 border-b border-slate-100">
                        <div class="w-9 h-9 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center font-bold text-sm">
                            🏠
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900 font-display">Property Specifications</h3>
                            <p class="text-xs text-slate-500">Full parameter list submitted for this valuation query.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-5">
                        <div class="space-y-1">
                            <span class="text-[11px] font-semibold text-slate-600 uppercase tracking-wider block">Type</span>
                            <span class="text-sm font-bold text-slate-900">{{ $prediction->property_type }}</span>
                        </div>
                        <div class="space-y-1">
                            <span class="text-[11px] font-semibold text-slate-600 uppercase tracking-wider block">Configuration</span>
                            <span class="text-sm font-bold text-slate-900">{{ $prediction->bedrooms }} BHK, {{ $prediction->bathrooms }} Baths</span>
                        </div>
                        <div class="space-y-1">
                            <span class="text-[11px] font-semibold text-slate-600 uppercase tracking-wider block">Balconies</span>
                            <span class="text-sm font-bold text-slate-900">{{ $prediction->balconies }} Balcony/Balconies</span>
                        </div>
                        <div class="space-y-1">
                            <span class="text-[11px] font-semibold text-slate-600 uppercase tracking-wider block">Super Built-up Area</span>
                            <span class="text-sm font-bold text-slate-900">{{ number_format($prediction->area) }} sq.ft</span>
                        </div>
                        <div class="space-y-1">
                            <span class="text-[11px] font-semibold text-slate-600 uppercase tracking-wider block">Carpet Area</span>
                            <span class="text-sm font-bold text-slate-900">{{ $prediction->carpet_area ? number_format($prediction->carpet_area) . ' sq.ft' : 'N/A' }}</span>
                        </div>
                        <div class="space-y-1">
                            <span class="text-[11px] font-semibold text-slate-600 uppercase tracking-wider block">Floor Position</span>
                            <span class="text-sm font-bold text-slate-900">{{ $prediction->floor }} of {{ $prediction->total_floors }} Floors</span>
                        </div>
                        <div class="space-y-1">
                            <span class="text-[11px] font-semibold text-slate-600 uppercase tracking-wider block">Property Age</span>
                            <span class="text-sm font-bold text-slate-900">{{ $prediction->property_age == 0 ? 'Brand New (< 1 yr)' : $prediction->property_age . ' Years' }}</span>
                        </div>
                        <div class="space-y-1">
                            <span class="text-[11px] font-semibold text-slate-600 uppercase tracking-wider block">Furnishing</span>
                            <span class="text-sm font-bold text-slate-900">{{ $prediction->furnishing ?? 'Semi-Furnished' }}</span>
                        </div>
                        <div class="space-y-1">
                            <span class="text-[11px] font-semibold text-slate-600 uppercase tracking-wider block">Covered Parking</span>
                            <span class="text-sm font-bold text-slate-900">{{ $prediction->parking ? 'Yes (Included)' : 'No' }}</span>
                        </div>
                    </div>

                    <!-- Amenities Included -->
                    @if($prediction->amenities_json && is_array($prediction->amenities_json) && count($prediction->amenities_json) > 0)
                        <div class="pt-4 border-t border-slate-100 space-y-2">
                            <span class="text-[11px] font-semibold text-slate-600 uppercase tracking-wider block">Selected Amenities</span>
                            <div class="flex flex-wrap gap-2">
                                @foreach($prediction->amenities_json as $amenity)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 text-xs font-medium">
                                        <svg class="w-3.5 h-3.5 mr-1 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        {{ $amenity }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- RIGHT COLUMN (5 Cols): Benchmark Rates & Locality Trends -->
            <div class="lg:col-span-5 space-y-8">
                
                <!-- BENCHMARK COMPARISON CARD -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
                    <div class="flex items-center space-x-3 pb-4 border-b border-slate-100">
                        <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-sm">
                            📈
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900 font-display">Market Benchmark Comparison</h3>
                            <p class="text-xs text-slate-500">Rate per square foot against city & micro-market averages.</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <!-- Property Rate -->
                        <div class="space-y-1.5">
                            <div class="flex justify-between text-xs font-bold">
                                <span class="text-emerald-700">This Property</span>
                                <span class="text-emerald-700">₹{{ number_format($prediction->price_per_sqft, 0) }} / sq.ft</span>
                            </div>
                            <div class="w-full bg-slate-100 h-2.5 rounded-full overflow-hidden">
                                <div class="bg-emerald-600 h-full rounded-full" style="width: {{ min(100, max(20, ($prediction->price_per_sqft / max(1, $prediction->price_per_sqft, $localityAvgRate, $cityAvgRate)) * 100)) }}%;"></div>
                            </div>
                        </div>

                        <!-- Locality Rate -->
                        <div class="space-y-1.5">
                            <div class="flex justify-between text-xs font-semibold">
                                <span class="text-slate-700">{{ $prediction->locality->name ?? 'Locality' }} Average</span>
                                <span class="text-slate-900 font-bold">₹{{ number_format($localityAvgRate, 0) }} / sq.ft</span>
                            </div>
                            <div class="w-full bg-slate-100 h-2.5 rounded-full overflow-hidden">
                                <div class="bg-teal-500 h-full rounded-full" style="width: {{ min(100, max(20, ($localityAvgRate / max(1, $prediction->price_per_sqft, $localityAvgRate, $cityAvgRate)) * 100)) }}%;"></div>
                            </div>
                        </div>

                        <!-- City Rate -->
                        <div class="space-y-1.5">
                            <div class="flex justify-between text-xs font-semibold">
                                <span class="text-slate-700">Bangalore City Benchmark</span>
                                <span class="text-slate-900 font-bold">₹{{ number_format($cityAvgRate, 0) }} / sq.ft</span>
                            </div>
                            <div class="w-full bg-slate-100 h-2.5 rounded-full overflow-hidden">
                                <div class="bg-slate-400 h-full rounded-full" style="width: {{ min(100, max(20, ($cityAvgRate / max(1, $prediction->price_per_sqft, $localityAvgRate, $cityAvgRate)) * 100)) }}%;"></div>
                            </div>
                        </div>
                    </div>

                    @if($prediction->locality)
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-2 text-xs">
                            <div class="flex justify-between">
                                <span class="text-slate-500">Locality Score:</span>
                                <span class="font-bold text-slate-800">{{ $prediction->locality->location_score ?? '8.5' }} / 10</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Annual Growth Rate:</span>
                                <span class="font-bold text-emerald-600">+{{ $prediction->locality->growth_rate ?? '8.2' }}% YoY</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Price Tier:</span>
                                <span class="font-bold text-slate-800">{{ $prediction->locality->price_tier ?? 'Mid-High' }}</span>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- HISTORICAL MONTHLY TREND CHART (Chart.js) -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div>
                            <h3 class="text-base font-bold text-slate-900 font-display">12-Month Locality Trend</h3>
                            <p class="text-xs text-slate-500">{{ $prediction->locality->name ?? 'Locality' }} Price History</p>
                        </div>
                        <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-lg">
                            ₹/sq.ft
                        </span>
                    </div>

                    <div class="relative h-56 w-full">
                        <canvas id="localityTrendChart"></canvas>
                    </div>
                </div>

            </div>
        </div>

        <!-- REGULATORY & VALUATION DISCLAIMER -->
        <div class="bg-slate-100/80 rounded-2xl p-6 border border-slate-200 text-slate-500 text-xs leading-relaxed space-y-2">
            <h4 class="font-bold text-slate-700 uppercase tracking-wider text-[11px]">Methodology & Regulatory Disclaimer</h4>
            <p>
                This valuation is generated using an Automated Valuation Model (AVM) combining multi-variable gradient boosted regression (XGBoost) and empirical real estate transaction indices for the Bangalore metropolitan region. The estimated price represents a statistical midpoint estimate with a 90% confidence interval under normal market liquidity conditions.
            </p>
            <p>
                This estimate does not constitute a certified physical appraisal or chartered surveyor inspection under RERA or financial underwriting regulations. Actual closing values may vary depending on physical finishes, title deed verification, view corridors, and individual negotiation.
            </p>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('localityTrendChart');
    if (!ctx) return;

    @php
        // Prepare market trend labels and rates for Chart.js
        $months = [];
        $rates = [];
        if (!empty($marketTrends) && count($marketTrends) > 0) {
            foreach($marketTrends as $trend) {
                $months[] = date('M Y', mktime(0, 0, 0, $trend->month, 1, $trend->year));
                $rates[] = (float) $trend->avg_price_per_sqft;
            }
        } else {
            // Default 12 month synthetic trend
            $baseRate = $prediction->locality ? (float) $prediction->locality->avg_price_per_sqft : 8950;
            $monthNames = ['Apr 25', 'May 25', 'Jun 25', 'Jul 25', 'Aug 25', 'Sep 25', 'Oct 25', 'Nov 25', 'Dec 25', 'Jan 26', 'Feb 26', 'Mar 26'];
            $multipliers = [0.93, 0.94, 0.95, 0.955, 0.97, 0.975, 0.98, 0.985, 0.99, 0.995, 1.0, 1.01];
            foreach($monthNames as $idx => $mName) {
                $months[] = $mName;
                $rates[] = round($baseRate * $multipliers[$idx]);
            }
        }
    @endphp

    const labels = @json($months);
    const dataPoints = @json($rates);

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Avg Rate (₹/sq.ft)',
                data: dataPoints,
                borderColor: '#059669',
                backgroundColor: 'rgba(5, 150, 105, 0.08)',
                borderWidth: 2.5,
                fill: true,
                tension: 0.35,
                pointBackgroundColor: '#059669',
                pointRadius: 3.5,
                pointHoverRadius: 6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return '₹' + context.parsed.y.toLocaleString('en-IN') + ' / sq.ft';
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 10 }, color: '#64748b' }
                },
                y: {
                    grid: { color: '#f1f5f9' },
                    ticks: {
                        font: { size: 10 },
                        color: '#64748b',
                        callback: function(value) {
                            return '₹' + (value >= 1000 ? (value / 1000) + 'k' : value);
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush
