@extends('layouts.app')

@section('title', 'Admin Command Center — House Price Predictor')

@section('content')
<div class="py-10 bg-slate-900 min-h-screen text-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        @include('admin.partials.nav', [
            'title' => 'Machine Learning & Platform Control',
            'subtitle' => 'Monitor datasets, model training lifecycle, live inference traffic, and users.'
        ])

        <!-- Metric KPI Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <!-- Total Users -->
            <div class="bg-slate-800/80 border border-slate-700/80 rounded-2xl p-6 space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Registered Users</span>
                    <span class="text-emerald-400 text-sm">👥</span>
                </div>
                <div class="text-3xl font-black font-display text-white">{{ $stats['total_users'] }}</div>
                <div class="text-[11px] text-slate-400">Total accounts on platform</div>
            </div>

            <!-- Total Predictions -->
            <div class="bg-slate-800/80 border border-slate-700/80 rounded-2xl p-6 space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Valuations</span>
                    <span class="text-emerald-400 text-sm">⚡</span>
                </div>
                <div class="text-3xl font-black font-display text-emerald-400">{{ $stats['total_predictions'] }}</div>
                <div class="text-[11px] text-slate-400">Inference calls processed</div>
            </div>

            <!-- Total Properties -->
            <div class="bg-slate-800/80 border border-slate-700/80 rounded-2xl p-6 space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Monitored Units</span>
                    <span class="text-emerald-400 text-sm">🏢</span>
                </div>
                <div class="text-3xl font-black font-display text-white">{{ $stats['total_properties'] }}</div>
                <div class="text-[11px] text-slate-400">Indexed property records</div>
            </div>

            <!-- Monitored Hubs -->
            <div class="bg-slate-800/80 border border-slate-700/80 rounded-2xl p-6 space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Bangalore Localities</span>
                    <span class="text-emerald-400 text-sm">📍</span>
                </div>
                <div class="text-3xl font-black font-display text-white">{{ $stats['total_localities'] }}</div>
                <div class="text-[11px] text-slate-400">Micro-market benchmark zones</div>
            </div>
        </div>

        <!-- Model Registry & Metrics -->
        <div class="bg-slate-800/80 border border-slate-700/80 rounded-3xl p-7 space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-white font-display">Machine Learning Model Registry</h3>
                    <p class="text-xs text-slate-400">Models evaluated during training pipeline. Only one active in production.</p>
                </div>
                <span class="px-3 py-1 rounded-full bg-emerald-950 text-emerald-300 border border-emerald-800 text-xs font-bold">
                    FastAPI REST Integration Ready
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-900/60 text-slate-400 uppercase font-bold border-b border-slate-700">
                        <tr>
                            <th class="py-3 px-4">Version</th>
                            <th class="py-3 px-4">Algorithm</th>
                            <th class="py-3 px-4">R² Score</th>
                            <th class="py-3 px-4">MAE</th>
                            <th class="py-3 px-4">RMSE</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4">Trained Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/60">
                        @foreach($models as $m)
                            <tr class="hover:bg-slate-700/30 transition">
                                <td class="py-3.5 px-4 font-mono font-bold text-white">{{ $m->version }}</td>
                                <td class="py-3.5 px-4 font-semibold text-slate-200">{{ $m->algorithm }}</td>
                                <td class="py-3.5 px-4 font-bold text-emerald-400">{{ $m->r2_score ? round($m->r2_score * 100, 2) . '%' : 'N/A' }}</td>
                                <td class="py-3.5 px-4 text-slate-300">₹{{ number_format($m->mae) }}</td>
                                <td class="py-3.5 px-4 text-slate-300">₹{{ number_format($m->rmse) }}</td>
                                <td class="py-3.5 px-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $m->status === 'active' ? 'bg-emerald-900/80 text-emerald-300 border border-emerald-700' : 'bg-slate-700 text-slate-300' }}">
                                        {{ $m->status }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-slate-400">
                                    {{ $m->trained_at ? $m->trained_at->format('M d, Y') : 'Preloaded' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Predictions Live Stream -->
        <div class="bg-slate-800/80 border border-slate-700/80 rounded-3xl p-7 space-y-4">
            <h3 class="text-lg font-bold text-white font-display">Recent Valuation Requests</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-900/60 text-slate-400 uppercase font-bold border-b border-slate-700">
                        <tr>
                            <th class="py-3 px-4">User</th>
                            <th class="py-3 px-4">Locality</th>
                            <th class="py-3 px-4">Area & BHK</th>
                            <th class="py-3 px-4">Predicted Value</th>
                            <th class="py-3 px-4">Range</th>
                            <th class="py-3 px-4">Confidence</th>
                            <th class="py-3 px-4">Timestamp</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/60">
                        @foreach($recentPredictions as $rp)
                            <tr class="hover:bg-slate-700/30 transition">
                                <td class="py-3.5 px-4 font-medium text-white">{{ $rp->user ? $rp->user->name : 'Guest User' }}</td>
                                <td class="py-3.5 px-4 text-slate-300">{{ $rp->locality ? $rp->locality->name : 'Bangalore' }}</td>
                                <td class="py-3.5 px-4 text-slate-400">{{ number_format($rp->area) }} sq.ft • {{ $rp->bedrooms }} BHK</td>
                                <td class="py-3.5 px-4 font-bold text-emerald-400">{{ $rp->formatted_price }}</td>
                                <td class="py-3.5 px-4 text-slate-300">{{ $rp->formatted_min_price }} — {{ $rp->formatted_max_price }}</td>
                                <td class="py-3.5 px-4 text-emerald-300 font-semibold">{{ $rp->confidence_score }}%</td>
                                <td class="py-3.5 px-4 text-slate-400">{{ $rp->created_at->diffForHumans() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection
