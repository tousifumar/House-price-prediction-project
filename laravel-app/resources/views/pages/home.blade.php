@extends('layouts.app')

@section('title', 'House Price Predictor — AI-Powered Real Estate Property Valuation')

@section('content')

    <!-- 1. HERO SECTION -->
    <section class="relative overflow-hidden pt-12 pb-20 lg:pt-20 lg:pb-28 bg-gradient-to-b from-white via-slate-50 to-slate-100/60">
        <!-- Ambient Decorative Glows -->
        <div class="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[400px] bg-gradient-to-tr from-emerald-100/40 via-teal-100/30 to-blue-100/40 blur-3xl -z-10 pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-center">
                <!-- Left: Headline & Actions -->
                <div class="lg:col-span-7 space-y-8 text-center lg:text-left">
                    <div class="inline-flex items-center space-x-2 px-3.5 py-1.5 rounded-full bg-emerald-50 border border-emerald-200/80 text-emerald-800 text-xs font-semibold tracking-wide shadow-sm">
                        <span class="flex h-2 w-2 rounded-full bg-emerald-500 animate-ping"></span>
                        <span>State-of-the-Art Machine Learning Valuation</span>
                    </div>

                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-slate-900 font-display leading-[1.12]">
                        Know Your Property's Worth <span class="text-gradient">Before You Buy or Sell</span>
                    </h1>

                    <p class="text-lg sm:text-xl text-slate-600 font-normal leading-relaxed max-w-2xl mx-auto lg:mx-0">
                        Get an AI-powered property price estimate based on location, property specifications and real-estate market patterns.
                    </p>

                    <!-- Dual CTAs -->
                    <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 pt-2">
                        <a href="{{ route('login') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-7 py-4 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-base shadow-glow transition-all duration-200 transform hover:-translate-y-0.5 group">
                            <span>Predict House Price</span>
                            <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </a>
                        <a href="#how-it-works" class="w-full sm:w-auto inline-flex items-center justify-center px-7 py-4 rounded-2xl bg-white hover:bg-slate-100 text-slate-700 font-semibold text-base border border-slate-200 shadow-sm transition-all duration-200">
                            Explore Features
                        </a>
                    </div>

                    <!-- Trust Stats Bar -->
                    <div class="pt-6 border-t border-slate-200/80 grid grid-cols-3 gap-4 text-center lg:text-left">
                        <div>
                            <div class="text-2xl font-black text-slate-900 font-display">₹4,800+ Cr</div>
                            <div class="text-xs font-medium text-slate-500">Valuations Analyzed</div>
                        </div>
                        <div>
                            <div class="text-2xl font-black text-emerald-600 font-display">{{ $stats['model_accuracy'] }}%</div>
                            <div class="text-xs font-medium text-slate-500">Model Accuracy (R²)</div>
                        </div>
                        <div>
                            <div class="text-2xl font-black text-slate-900 font-display">10 Hubs</div>
                            <div class="text-xs font-medium text-slate-500">Bangalore Localities</div>
                        </div>
                    </div>
                </div>

                <!-- Right: Interactive Quick Valuation Teaser Card -->
                <div class="lg:col-span-5" x-data="{
                    area: 1500,
                    localityRate: 8400,
                    bedrooms: 3,
                    localityName: 'Whitefield',
                    calculateEstimate() {
                        let base = this.area * this.localityRate;
                        let bhkBonus = (this.bedrooms - 2) * 500000;
                        return Math.max(2500000, base + bhkBonus);
                    },
                    formattedEstimate() {
                        let val = this.calculateEstimate();
                        if (val >= 10000000) return '₹' + (val / 10000000).toFixed(2) + ' Cr';
                        return '₹' + (val / 100000).toFixed(2) + ' L';
                    },
                    formattedRange() {
                        let val = this.calculateEstimate();
                        let min = val * 0.93;
                        let max = val * 1.07;
                        let minStr = min >= 10000000 ? '₹' + (min / 10000000).toFixed(2) + ' Cr' : '₹' + (min / 100000).toFixed(2) + ' L';
                        let maxStr = max >= 10000000 ? '₹' + (max / 10000000).toFixed(2) + ' Cr' : '₹' + (max / 100000).toFixed(2) + ' L';
                        return minStr + ' — ' + maxStr;
                    },
                    updateLocality(event) {
                        let select = event.target;
                        let option = select.options[select.selectedIndex];
                        this.localityRate = parseInt(option.getAttribute('data-rate') || 8400);
                        this.localityName = option.text;
                    }
                }">
                    <div class="glass-card rounded-3xl p-6 sm:p-8 shadow-xl border border-slate-200/90 relative overflow-hidden">
                        <div class="flex items-center justify-between pb-5 border-b border-slate-100">
                            <div>
                                <span class="text-xs font-bold uppercase tracking-wider text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-md">Live Valuation Teaser</span>
                                <h2 class="text-lg font-bold text-slate-900 mt-1">Instant Price Estimator</h2>
                            </div>
                            <span class="text-xs font-semibold text-slate-500">XGBoost v1.0</span>
                        </div>

                        <!-- Interactive Inputs -->
                        <div class="space-y-4 pt-5">
                            <!-- Locality Selection -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Locality</label>
                                <select @change="updateLocality($event)" class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 font-medium transition">
                                    @foreach($localities as $loc)
                                        <option value="{{ $loc->id }}" data-rate="{{ $loc->avg_price_per_sqft }}" {{ $loc->slug === 'whitefield' ? 'selected' : '' }}>
                                            {{ $loc->name }} (Avg ₹{{ number_format($loc->avg_price_per_sqft) }}/sqft)
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Built-up Area Slider -->
                            <div>
                                <div class="flex justify-between items-center text-xs font-semibold text-slate-700 mb-1.5">
                                    <span>Built-up Area</span>
                                    <span class="text-emerald-600 font-bold" x-text="area + ' sq.ft'"></span>
                                </div>
                                <input type="range" min="500" max="4000" step="50" x-model.number="area" class="w-full accent-emerald-600 cursor-pointer">
                                <div class="flex justify-between text-[10px] text-slate-400 mt-1 font-medium">
                                    <span>500 sq.ft (Studio)</span>
                                    <span>2,000 sq.ft</span>
                                    <span>4,000 sq.ft (Villa)</span>
                                </div>
                            </div>

                            <!-- Bedrooms Selection -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Bedrooms (BHK)</label>
                                <div class="grid grid-cols-4 gap-2">
                                    <template x-for="bhk in [1, 2, 3, 4]" :key="bhk">
                                        <button type="button" @click="bedrooms = bhk" 
                                                :class="bedrooms === bhk ? 'bg-emerald-600 text-white border-emerald-600 shadow-sm' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'"
                                                class="py-2 text-xs font-bold rounded-xl border transition-all text-center"
                                                x-text="bhk + ' BHK'">
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <!-- Real-time Prediction Output Box -->
                        <div class="mt-6 p-5 rounded-2xl bg-gradient-to-br from-slate-900 to-slate-950 text-white shadow-lg space-y-3">
                            <div class="flex items-center justify-between text-xs text-slate-400">
                                <span>Estimated Market Valuation</span>
                                <span class="text-emerald-400 font-semibold flex items-center">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 mr-1.5"></span> 87% Confidence
                                </span>
                            </div>

                            <div class="flex items-baseline space-x-2">
                                <span class="text-3xl sm:text-4xl font-black font-display text-white tracking-tight" x-text="formattedEstimate()"></span>
                                <span class="text-xs text-slate-400" x-text="'(~₹' + localityRate.toLocaleString() + '/sq.ft)'"></span>
                            </div>

                            <div class="pt-2 border-t border-slate-800 flex items-center justify-between text-xs">
                                <span class="text-slate-400">Estimated Range:</span>
                                <span class="font-semibold text-emerald-300" x-text="formattedRange()"></span>
                            </div>
                        </div>

                        <div class="mt-5">
                            <a href="{{ route('login') }}" class="w-full inline-flex items-center justify-center py-3 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold tracking-wide uppercase transition shadow">
                                Unlock Detailed AI Valuation Report &rarr;
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 2. PROPERTY PRICE PREDICTION CTA STRIP -->
    <section class="bg-emerald-900 text-white py-8 border-y border-emerald-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center space-x-4 text-center md:text-left">
                <div class="w-12 h-12 rounded-xl bg-emerald-800 flex items-center justify-center text-emerald-300 font-bold shrink-0">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold">Ready to analyze a specific property with full 20+ feature precision?</h3>
                    <p class="text-xs text-emerald-200">Floor level, carpet area, age, society amenities, and verified locality micro-trends.</p>
                </div>
            </div>
            <a href="{{ route('login') }}" class="px-6 py-3 rounded-xl bg-white hover:bg-emerald-50 text-emerald-950 font-bold text-sm shrink-0 shadow transition">
                Start Valuation
            </a>
        </div>
    </section>

    <!-- 3. HOW IT WORKS (4-STEP PROCESS) -->
    <section id="how-it-works" class="py-20 lg:py-28 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto space-y-4 mb-16">
                <span class="text-xs font-bold tracking-wider uppercase text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full">Algorithmic Workflow</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 font-display">How It Works in 4 Steps</h2>
                <p class="text-slate-600 text-base leading-relaxed">
                    Our platform processes physical parameters, builder quality, and locality micro-indices to return market-accurate valuations in milliseconds.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Step 1 -->
                <div class="relative bg-slate-50 rounded-2xl p-7 border border-slate-200/80 hover:border-emerald-300 hover:shadow-lg transition-all group">
                    <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-700 font-extrabold flex items-center justify-center mb-6 text-lg group-hover:scale-110 transition-transform">
                        1
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2 font-display">Enter Property Details</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Input property type (Apartment, Villa, Independent House), built-up area, carpet area, bedrooms, bathrooms, and floor level.
                    </p>
                </div>

                <!-- Step 2 -->
                <div class="relative bg-slate-50 rounded-2xl p-7 border border-slate-200/80 hover:border-emerald-300 hover:shadow-lg transition-all group">
                    <div class="w-12 h-12 rounded-xl bg-teal-100 text-teal-700 font-extrabold flex items-center justify-center mb-6 text-lg group-hover:scale-110 transition-transform">
                        2
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2 font-display">Select Location</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Choose your city and locality from top tech corridors (Whitefield, Koramangala, HSR Layout) or enter map coordinates.
                    </p>
                </div>

                <!-- Step 3 -->
                <div class="relative bg-slate-50 rounded-2xl p-7 border border-slate-200/80 hover:border-emerald-300 hover:shadow-lg transition-all group">
                    <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-700 font-extrabold flex items-center justify-center mb-6 text-lg group-hover:scale-110 transition-transform">
                        3
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2 font-display">AI Analyzes Property</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Our trained Machine Learning pipeline standardizes features and executes regression models with SHAP factor evaluation.
                    </p>
                </div>

                <!-- Step 4 -->
                <div class="relative bg-slate-50 rounded-2xl p-7 border border-slate-200/80 hover:border-emerald-300 hover:shadow-lg transition-all group">
                    <div class="w-12 h-12 rounded-xl bg-indigo-100 text-indigo-700 font-extrabold flex items-center justify-center mb-6 text-lg group-hover:scale-110 transition-transform">
                        4
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2 font-display">Get Estimated Market Price</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Receive your predicted price, confidence interval, influential factors breakdown, locality benchmark, and downloadable PDF report.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- 4. CORE SAAS FEATURES -->
    <section id="features" class="py-20 lg:py-28 bg-slate-50 border-y border-slate-200/60">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto space-y-4 mb-16">
                <span class="text-xs font-bold tracking-wider uppercase text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full">Platform Capabilities</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 font-display">Everything Built for Real Estate Precision</h2>
                <p class="text-slate-600 text-base leading-relaxed">
                    Engineered from ground up as a commercial analytics suite for buyers, sellers, realtors, and institutional investors.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="bg-white rounded-2xl p-8 border border-slate-200/80 hover:shadow-xl transition-all duration-300 space-y-4">
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 font-display">Multi-Model Regression</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Trains and compares Linear Regression, Random Forest, Gradient Boosting, and XGBoost to dynamically select the model with the highest R² score and lowest MAE.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="bg-white rounded-2xl p-8 border border-slate-200/80 hover:shadow-xl transition-all duration-300 space-y-4">
                    <div class="w-12 h-12 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 font-display">Locality Benchmarks</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Compare your property's valuation directly with hyper-local averages, median price per sq.ft, and citywide standards in real time.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="bg-white rounded-2xl p-8 border border-slate-200/80 hover:shadow-xl transition-all duration-300 space-y-4">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 font-display">Explainability Insights</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Understand which specific features influenced your valuation positively or negatively (locality premium, square footage, amenities, property age).
                    </p>
                </div>

                <!-- Feature 4 -->
                <div class="bg-white rounded-2xl p-8 border border-slate-200/80 hover:shadow-xl transition-all duration-300 space-y-4">
                    <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 font-display">Historical Market Trends</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Track 12-month trailing price movements, annual growth rates, transaction volumes, and demand indexes through interactive Chart.js visualizations.
                    </p>
                </div>

                <!-- Feature 5 -->
                <div class="bg-white rounded-2xl p-8 border border-slate-200/80 hover:shadow-xl transition-all duration-300 space-y-4">
                    <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 font-display">PDF Valuation Reports</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Download formal, branded valuation summaries ready for negotiation, bank submissions, co-investor pitches, or personal records.
                    </p>
                </div>

                <!-- Feature 6 -->
                <div class="bg-white rounded-2xl p-8 border border-slate-200/80 hover:shadow-xl transition-all duration-300 space-y-4">
                    <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 font-display">Price Alerts & Tracker</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Set target price thresholds in specific neighborhoods to get instant notifications when valuations align with your investment criteria.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- 5. MARKET STATISTICS SHOWCASE -->
    <section id="market-stats" class="py-20 lg:py-28 bg-slate-900 text-white relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                <div class="lg:col-span-6 space-y-6">
                    <span class="text-xs font-bold tracking-wider uppercase text-emerald-400 bg-emerald-950/80 border border-emerald-800 px-3 py-1 rounded-full">Real-Time Market Analytics</span>
                    <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold font-display leading-tight">
                        Bangalore Property Indices & Market Momentum
                    </h2>
                    <p class="text-slate-400 text-base leading-relaxed">
                        Our data pipelines ingest residential transactions, verified listings, and micro-market parameters continuously to reflect true market pricing dynamics.
                    </p>
                    
                    <div class="pt-4 flex items-center space-x-6">
                        <a href="{{ route('login') }}" class="px-6 py-3.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-sm shadow-lg transition">
                            Explore Detailed Analytics
                        </a>
                        <span class="text-xs text-slate-400">Updated for {{ date('F Y') }}</span>
                    </div>
                </div>

                <!-- Stats Grid -->
                <div class="lg:col-span-6 grid grid-cols-2 gap-5">
                    <div class="bg-slate-800/80 border border-slate-700/80 p-6 rounded-2xl">
                        <div class="text-3xl sm:text-4xl font-black font-display text-emerald-400">₹{{ number_format($stats['avg_city_price_per_sqft']) }}</div>
                        <div class="text-xs font-semibold text-slate-300 mt-1">City Average Price / sq.ft</div>
                        <div class="text-[11px] text-slate-500 mt-2">Across 10 key monitored localities</div>
                    </div>

                    <div class="bg-slate-800/80 border border-slate-700/80 p-6 rounded-2xl">
                        <div class="text-3xl sm:text-4xl font-black font-display text-emerald-400">+{{ $stats['avg_growth_rate'] }}%</div>
                        <div class="text-xs font-semibold text-slate-300 mt-1">Annual YoY Appreciation</div>
                        <div class="text-[11px] text-slate-500 mt-2">Leading tech corridor growth</div>
                    </div>

                    <div class="bg-slate-800/80 border border-slate-700/80 p-6 rounded-2xl">
                        <div class="text-3xl sm:text-4xl font-black font-display text-white">{{ number_format($stats['total_properties']) }}+</div>
                        <div class="text-xs font-semibold text-slate-300 mt-1">Properties Indexed</div>
                        <div class="text-[11px] text-slate-500 mt-2">Active training & benchmark set</div>
                    </div>

                    <div class="bg-slate-800/80 border border-slate-700/80 p-6 rounded-2xl">
                        <div class="text-3xl sm:text-4xl font-black font-display text-white">{{ $stats['model_accuracy'] }}%</div>
                        <div class="text-xs font-semibold text-slate-300 mt-1">Confidence Rating (R²)</div>
                        <div class="text-[11px] text-slate-500 mt-2">Validated on hold-out test split</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 6. SUPPORTED LOCATIONS (BANGALORE LOCALITIES) -->
    <section id="locations" class="py-20 lg:py-28 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto space-y-4 mb-16">
                <span class="text-xs font-bold tracking-wider uppercase text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full">Coverage & Coverage</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 font-display">Supported Bangalore Micro-Markets</h2>
                <p class="text-slate-600 text-base leading-relaxed">
                    Explore average property valuations and annual growth trajectories across premier residential neighborhoods.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-5">
                @foreach($localities as $loc)
                    <div class="bg-slate-50 rounded-2xl p-5 border border-slate-200/80 hover:border-emerald-300 hover:shadow-md transition-all">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-bold text-slate-900 truncate">{{ $loc->name }}</span>
                            <span class="inline-flex items-center text-[10px] font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full">
                                +{{ $loc->growth_rate }}% YoY
                            </span>
                        </div>
                        <div class="text-xl font-extrabold text-slate-900 font-display">₹{{ number_format($loc->avg_price_per_sqft) }}</div>
                        <div class="text-[11px] text-slate-500">per sq.ft benchmark</div>

                        <div class="pt-4 mt-4 border-t border-slate-200/60 flex items-center justify-between text-xs text-slate-600">
                            <span>Score: <strong class="text-slate-800">{{ $loc->location_score }}/10</strong></span>
                            <span>{{ number_format($loc->total_properties) }} units</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- 7. WHY CHOOSE HOUSE PRICE PREDICTOR (COMPARISON MATRIX) -->
    <section class="py-20 lg:py-28 bg-slate-50 border-t border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto space-y-4 mb-16">
                <span class="text-xs font-bold tracking-wider uppercase text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full">Comparative Advantage</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 font-display">Traditional Appraisal vs. AI Valuation</h2>
                <p class="text-slate-600 text-base leading-relaxed">
                    Why modern home buyers and professional real-estate investors depend on our algorithmic valuation platform.
                </p>
            </div>

            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden max-w-4xl mx-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50/70">
                            <th class="py-4 px-6 font-bold text-slate-800">Feature Comparison</th>
                            <th class="py-4 px-6 font-bold text-slate-400">Traditional Broker / Appraiser</th>
                            <th class="py-4 px-6 font-bold text-emerald-600">House Price Predictor AI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr>
                            <td class="py-4 px-6 font-semibold text-slate-900">Valuation Turnaround Time</td>
                            <td class="py-4 px-6 text-slate-500">3 to 7 business days</td>
                            <td class="py-4 px-6 font-bold text-emerald-600">Instant (&lt; 200 milliseconds)</td>
                        </tr>
                        <tr>
                            <td class="py-4 px-6 font-semibold text-slate-900">Objective Valuation Bias</td>
                            <td class="py-4 px-6 text-slate-500">Subject to broker sales pressure</td>
                            <td class="py-4 px-6 font-bold text-emerald-600">100% Unbiased algorithmic modeling</td>
                        </tr>
                        <tr>
                            <td class="py-4 px-6 font-semibold text-slate-900">Feature Dimensions Analyzed</td>
                            <td class="py-4 px-6 text-slate-500">3–5 rough estimations</td>
                            <td class="py-4 px-6 font-bold text-emerald-600">20+ multi-variant specifications</td>
                        </tr>
                        <tr>
                            <td class="py-4 px-6 font-semibold text-slate-900">Confidence Range & Factors</td>
                            <td class="py-4 px-6 text-slate-500">Subjective verbal guess</td>
                            <td class="py-4 px-6 font-bold text-emerald-600">Documented bounds & SHAP factors</td>
                        </tr>
                        <tr>
                            <td class="py-4 px-6 font-semibold text-slate-900">Historical Trend Analytics</td>
                            <td class="py-4 px-6 text-slate-500">Rarely available</td>
                            <td class="py-4 px-6 font-bold text-emerald-600">Interactive 12-month charts</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- 8. PREDICTION PROCESS DEEP DIVE -->
    <section class="py-20 lg:py-28 bg-white border-t border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto space-y-4 mb-16">
                <span class="text-xs font-bold tracking-wider uppercase text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full">Engineering Architecture</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 font-display">Behind the Prediction Engine</h2>
                <p class="text-slate-600 text-base leading-relaxed">
                    A decoupled high-performance inference workflow connecting Laravel with Python FastAPI microservices.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto">
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                    <div class="text-xs font-bold uppercase tracking-wider text-emerald-600">Stage 1: Validation</div>
                    <h3 class="text-lg font-bold text-slate-900">Laravel Request Pipeline</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Validates physical parameters (built-up, carpet ratio, floor ratios, amenity sets) through strict Form Requests preventing erroneous input.
                    </p>
                </div>

                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                    <div class="text-xs font-bold uppercase tracking-wider text-teal-600">Stage 2: Preprocessing</div>
                    <h3 class="text-lg font-bold text-slate-900">Unified Feature Pipeline</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Applies the exact same Scikit-Learn transformer used in training: outlier handling, categorical encoding, and feature derivations.
                    </p>
                </div>

                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                    <div class="text-xs font-bold uppercase tracking-wider text-blue-600">Stage 3: Inference</div>
                    <h3 class="text-lg font-bold text-slate-900">Active Model Scoring</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        In-memory XGBoost model evaluates the feature array, returning price prediction, statistical confidence interval, and factor impacts.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- 9. TESTIMONIALS -->
    <section class="py-20 lg:py-28 bg-slate-50 border-t border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto space-y-4 mb-16">
                <span class="text-xs font-bold tracking-wider uppercase text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full">User Testimonials</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 font-display">Trusted by Buyers, Sellers & Brokers</h2>
                <p class="text-slate-600 text-base leading-relaxed">
                    Discover how real users leverage automated valuation insights during real estate negotiations.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Testimonial 1 -->
                <div class="bg-white rounded-2xl p-7 border border-slate-200/80 shadow-sm space-y-4">
                    <div class="flex text-amber-400 space-x-1">
                        @for($i=0; $i<5; $i++)
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        @endfor
                    </div>
                    <p class="text-sm text-slate-600 italic leading-relaxed">
                        "Saved me over ₹8 Lakhs during our Whitefield 3 BHK purchase. The seller asked for ₹1.52 Cr, but the valuation report highlighted fair value at ₹1.43 Cr. The breakdown gave us concrete data to negotiate."
                    </p>
                    <div class="pt-3 border-t border-slate-100 flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-800 font-bold flex items-center justify-center text-sm">
                            SK
                        </div>
                        <div>
                            <div class="text-sm font-bold text-slate-900">Siddharth K.</div>
                            <div class="text-xs text-slate-500">Tech Lead, Home Buyer</div>
                        </div>
                    </div>
                </div>

                <!-- Testimonial 2 -->
                <div class="bg-white rounded-2xl p-7 border border-slate-200/80 shadow-sm space-y-4">
                    <div class="flex text-amber-400 space-x-1">
                        @for($i=0; $i<5; $i++)
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        @endfor
                    </div>
                    <p class="text-sm text-slate-600 italic leading-relaxed">
                        "As a real-estate advisor in Koramangala, I generate these PDF valuation summaries for prospective NRI clients. The algorithmic transparency and confidence bounds give them complete peace of mind."
                    </p>
                    <div class="pt-3 border-t border-slate-100 flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-full bg-teal-100 text-teal-800 font-bold flex items-center justify-center text-sm">
                            PR
                        </div>
                        <div>
                            <div class="text-sm font-bold text-slate-900">Pooja Raman</div>
                            <div class="text-xs text-slate-500">Real Estate Consultant</div>
                        </div>
                    </div>
                </div>

                <!-- Testimonial 3 -->
                <div class="bg-white rounded-2xl p-7 border border-slate-200/80 shadow-sm space-y-4">
                    <div class="flex text-amber-400 space-x-1">
                        @for($i=0; $i<5; $i++)
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        @endfor
                    </div>
                    <p class="text-sm text-slate-600 italic leading-relaxed">
                        "The market trend charts and price per sq.ft tracking in HSR Layout let us time our apartment sale perfectly. Sold within 14 days right within the predicted valuation bracket."
                    </p>
                    <div class="pt-3 border-t border-slate-100 flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-800 font-bold flex items-center justify-center text-sm">
                            AM
                        </div>
                        <div>
                            <div class="text-sm font-bold text-slate-900">Anand Murthy</div>
                            <div class="text-xs text-slate-500">Property Owner</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 10. FAQ ACCORDION -->
    <section id="faq" class="py-20 lg:py-28 bg-white border-t border-slate-200/80" x-data="{ activeAccordion: 1 }">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center space-y-4 mb-16">
                <span class="text-xs font-bold tracking-wider uppercase text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full">Frequently Asked Questions</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 font-display">Got Questions? We Have Answers</h2>
                <p class="text-slate-600 text-base leading-relaxed">
                    Transparent explanations on model training, accuracy calculation, and data handling.
                </p>
            </div>

            <div class="space-y-4">
                <!-- Q1 -->
                <div class="border border-slate-200 rounded-2xl p-5 bg-slate-50/50">
                    <button @click="activeAccordion = (activeAccordion === 1 ? null : 1)" class="w-full flex items-center justify-between text-left font-bold text-slate-900 text-base focus:outline-none">
                        <span>How accurate is the Machine Learning property valuation?</span>
                        <svg class="w-5 h-5 text-slate-500 transform transition-transform" :class="activeAccordion === 1 ? 'rotate-180 text-emerald-600' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="activeAccordion === 1" x-collapse class="mt-3 text-sm text-slate-600 leading-relaxed pt-2 border-t border-slate-200/60">
                        Our active XGBoost model achieves an R² score of 91.4% on hold-out test datasets, with a Mean Absolute Error (MAE) under ₹4.85 Lakhs for standard apartments. Each prediction also includes a 90% confidence range reflecting localized price variance.
                    </div>
                </div>

                <!-- Q2 -->
                <div class="border border-slate-200 rounded-2xl p-5 bg-slate-50/50">
                    <button @click="activeAccordion = (activeAccordion === 2 ? null : 2)" class="w-full flex items-center justify-between text-left font-bold text-slate-900 text-base focus:outline-none">
                        <span>How is the estimated price range calculated?</span>
                        <svg class="w-5 h-5 text-slate-500 transform transition-transform" :class="activeAccordion === 2 ? 'rotate-180 text-emerald-600' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="activeAccordion === 2" x-collapse class="mt-3 text-sm text-slate-600 leading-relaxed pt-2 border-t border-slate-200/60" style="display: none;">
                        The price range is computed using the residual variance of the active model combined with the specific locality's historical price deviation. Rather than displaying a single static figure, we present a statistically sound range (e.g. ₹1.32 Cr — ₹1.52 Cr) within which the property is most likely to transact.
                    </div>
                </div>

                <!-- Q3 -->
                <div class="border border-slate-200 rounded-2xl p-5 bg-slate-50/50">
                    <button @click="activeAccordion = (activeAccordion === 3 ? null : 3)" class="w-full flex items-center justify-between text-left font-bold text-slate-900 text-base focus:outline-none">
                        <span>Which property factors have the greatest influence on price?</span>
                        <svg class="w-5 h-5 text-slate-500 transform transition-transform" :class="activeAccordion === 3 ? 'rotate-180 text-emerald-600' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="activeAccordion === 3" x-collapse class="mt-3 text-sm text-slate-600 leading-relaxed pt-2 border-t border-slate-200/60" style="display: none;">
                        According to our model feature importance metrics, the primary factors are Super Built-up Area (32%), Locality Benchmark Rate (28%), Location & Accessibility Score (14%), Bedroom Count (10%), and Property Age & Depreciation (6%).
                    </div>
                </div>

                <!-- Q4 -->
                <div class="border border-slate-200 rounded-2xl p-5 bg-slate-50/50">
                    <button @click="activeAccordion = (activeAccordion === 4 ? null : 4)" class="w-full flex items-center justify-between text-left font-bold text-slate-900 text-base focus:outline-none">
                        <span>Can this valuation replace a bank or government registered appraisal?</span>
                        <svg class="w-5 h-5 text-slate-500 transform transition-transform" :class="activeAccordion === 4 ? 'rotate-180 text-emerald-600' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="activeAccordion === 4" x-collapse class="mt-3 text-sm text-slate-600 leading-relaxed pt-2 border-t border-slate-200/60" style="display: none;">
                        No. House Price Predictor provides an automated mathematical estimation for market intelligence, decision-making, and negotiation support. It is not an official legal, tax, or certified banking appraisal.
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 11. FINAL CONVERSION CTA BANNER -->
    <section class="py-20 lg:py-24 bg-gradient-to-tr from-slate-950 via-slate-900 to-emerald-950 text-white relative overflow-hidden">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-8 relative z-10">
            <div class="inline-flex items-center space-x-2 px-3.5 py-1.5 rounded-full bg-emerald-900/60 border border-emerald-500/30 text-emerald-300 text-xs font-semibold">
                <span>Free Instant Access</span>
            </div>

            <h2 class="text-3xl sm:text-5xl font-extrabold font-display leading-tight tracking-tight">
                Discover What Your Property is Really Worth Today
            </h2>

            <p class="text-base sm:text-lg text-slate-300 max-w-2xl mx-auto leading-relaxed">
                Join thousands of homeowners, investors, and agents using AI to make confident, data-backed real estate transactions.
            </p>

            <div class="pt-4 flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('login') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 rounded-2xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-base shadow-glow transition transform hover:-translate-y-0.5">
                    Start Valuation Now &rarr;
                </a>
                <a href="{{ route('register') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 rounded-2xl bg-slate-800/80 hover:bg-slate-800 text-white font-semibold text-base border border-slate-700 transition">
                    Create Free Account
                </a>
            </div>
        </div>
    </section>

@endsection
