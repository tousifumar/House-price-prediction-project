@extends('layouts.app')

@section('title', 'User Dashboard — House Price Predictor')

@section('content')
<div class="py-10 bg-slate-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        <!-- Welcome Banner -->
        <div class="bg-gradient-to-r from-slate-900 via-slate-950 to-emerald-950 rounded-3xl p-8 text-white shadow-xl flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
            <div class="space-y-2">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-400">Investor Dashboard</span>
                <h1 class="text-2xl sm:text-3xl font-extrabold font-display">Welcome Back, {{ $user->name }}</h1>
                <p class="text-xs sm:text-sm text-slate-300">Track your property valuations, market alerts, and benchmark indices.</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('predict') }}" class="px-5 py-3 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-xs shadow-glow transition flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    New Valuation
                </a>
            </div>
        </div>

        <!-- Metric Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <!-- Total Predictions -->
            <a href="{{ route('predictions.index') }}" class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition space-y-2 group block">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500 group-hover:text-emerald-600 transition">Total Predictions</span>
                    <span class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs">📈</span>
                </div>
                <div class="text-3xl font-extrabold text-slate-900 font-display">{{ $stats['total_predictions'] }}</div>
                <div class="text-[11px] text-slate-500">Valuation queries saved →</div>
            </a>

            <!-- Saved Properties -->
            <a href="{{ route('saved-properties.index') }}" class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition space-y-2 group block">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500 group-hover:text-teal-600 transition">Saved Properties</span>
                    <span class="w-8 h-8 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center text-xs">⭐</span>
                </div>
                <div class="text-3xl font-extrabold text-slate-900 font-display">{{ $stats['saved_properties_count'] }}</div>
                <div class="text-[11px] text-slate-500">Monitored units →</div>
            </a>

            <!-- Price Alerts -->
            <a href="{{ route('price-alerts.index') }}" class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition space-y-2 group block">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500 group-hover:text-blue-600 transition">Price Alerts</span>
                    <span class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xs">🔔</span>
                </div>
                <div class="text-3xl font-extrabold text-slate-900 font-display">{{ $stats['price_alerts_count'] }}</div>
                <div class="text-[11px] text-slate-500">Active market triggers →</div>
            </a>

            <!-- Avg Estimated Value -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Average Valuation</span>
                    <span class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xs">💰</span>
                </div>
                <div class="text-3xl font-extrabold text-emerald-600 font-display">
                    {{ \App\Models\Prediction::formatPriceInIndianSystem($stats['avg_estimated_value']) }}
                </div>
                <div class="text-[11px] text-slate-500">Across your portfolio</div>
            </div>
        </div>

        <!-- Recent Predictions Table -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-900 font-display">Recent Valuations</h3>
                    <p class="text-xs text-slate-500">Historical property estimates processed through active ML model.</p>
                </div>
                <a href="{{ route('predictions.index') }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700">
                    View All Valuations →
                </a>
            </div>

            @if($predictions->isEmpty())
                <div class="p-12 text-center space-y-3">
                    <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center">
                        📋
                    </div>
                    <h4 class="text-sm font-bold text-slate-700">No predictions recorded yet</h4>
                    <p class="text-xs text-slate-500 max-w-sm mx-auto">Run your first house valuation to see detailed regression estimates and factor breakdowns here.</p>
                    <a href="{{ route('predict') }}" class="inline-block mt-2 px-4 py-2 rounded-xl bg-emerald-600 text-white font-bold text-xs">
                        Predict Now
                    </a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 text-slate-500 text-xs uppercase font-semibold border-b border-slate-100">
                            <tr>
                                <th class="py-3.5 px-6">Property / Location</th>
                                <th class="py-3.5 px-6">Specifications</th>
                                <th class="py-3.5 px-6">Estimated Value</th>
                                <th class="py-3.5 px-6">Confidence</th>
                                <th class="py-3.5 px-6">Date</th>
                                <th class="py-3.5 px-6 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($predictions as $pred)
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="py-4 px-6">
                                        <div class="font-bold text-slate-900">{{ $pred->property_type }}</div>
                                        <div class="text-xs text-slate-500">{{ $pred->locality ? $pred->locality->name : 'Bangalore' }}</div>
                                    </td>
                                    <td class="py-4 px-6 text-xs text-slate-600">
                                        {{ number_format($pred->area) }} sq.ft • {{ $pred->bedrooms }} BHK • {{ $pred->furnishing }}
                                    </td>
                                    <td class="py-4 px-6 font-bold text-emerald-600">
                                        {{ $pred->formatted_price }}
                                        <span class="block text-[10px] text-slate-400 font-normal">₹{{ number_format($pred->price_per_sqft) }}/sqft</span>
                                    </td>
                                    <td class="py-4 px-6">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            {{ $pred->confidence_score }}%
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-xs text-slate-500">
                                        {{ $pred->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="py-4 px-6 text-right">
                                        <div class="inline-flex items-center space-x-2">
                                            <a href="{{ route('predictions.show', $pred->id) }}" class="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700" title="View Report">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            </a>
                                            <a href="{{ route('predictions.pdf', $pred->id) }}" class="p-1.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-white" title="Download PDF">
                                                <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

    </div>
</div>
@endsection
