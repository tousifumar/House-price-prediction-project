@extends('layouts.app')

@section('title', 'Real-Time Price Alerts — House Price Predictor')

@section('content')
<div class="py-10 bg-slate-50 min-h-screen" x-data="{ createModalOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        <!-- Header Banner -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-blue-50 border border-blue-200 text-blue-700 text-xs font-semibold mb-2">
                    <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                    <span>Real-Time Market Monitoring</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-display">Property Price Alerts</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">Receive automated market notifications whenever new properties or valuation dips match your criteria.</p>
            </div>
            <div>
                <button @click="createModalOpen = true" class="inline-flex items-center px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-md transition group">
                    <svg class="w-4 h-4 mr-1.5 text-emerald-400 group-hover:rotate-90 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Create Price Alert
                </button>
            </div>
        </div>

        <!-- CREATE ALERT MODAL -->
        <div x-show="createModalOpen" 
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 backdrop-blur-sm flex items-center justify-center p-4"
             style="display: none;"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            
            <div @click.outside="createModalOpen = false" class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-100 space-y-6">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center space-x-3">
                        <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                            🔔
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900 font-display">Setup Market Trigger</h3>
                            <p class="text-xs text-slate-500">Configure parameters to monitor</p>
                        </div>
                    </div>
                    <button @click="createModalOpen = false" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('price-alerts.store') }}" class="space-y-4">
                    @csrf

                    <!-- Locality -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Target Locality</label>
                        <select name="locality_id" required class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-xs rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-emerald-500 font-medium">
                            @foreach($localities as $loc)
                                <option value="{{ $loc->id }}">{{ $loc->name }} (Avg ₹{{ number_format($loc->avg_price_per_sqft) }}/sqft)</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Property Type -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Property Type (Optional)</label>
                        <select name="property_type" class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-xs rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-emerald-500 font-medium">
                            <option value="">Any Typology</option>
                            @foreach($propertyTypes as $type)
                                <option value="{{ $type }}">{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Bedrooms -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Bedrooms (Optional)</label>
                        <select name="bedrooms" class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-xs rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-emerald-500 font-medium">
                            <option value="">Any Configuration</option>
                            @for($i=1; $i<=5; $i++)
                                <option value="{{ $i }}">{{ $i }} BHK</option>
                            @endfor
                        </select>
                    </div>

                    <!-- Trigger Condition & Price -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Trigger When Price Is</label>
                            <select name="condition" required class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-xs rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-emerald-500 font-medium">
                                <option value="below">Below Target (Bargain)</option>
                                <option value="above">Above Target (Premium)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Target Price (₹)</label>
                            <input type="number" name="target_price" required min="100000" step="50000" placeholder="e.g. 15000000"
                                   class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-xs rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-emerald-500 font-medium">
                        </div>
                    </div>

                    <div class="pt-4 flex items-center justify-end space-x-3">
                        <button type="button" @click="createModalOpen = false" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-xs font-semibold hover:bg-slate-50">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-md shadow-emerald-600/20">
                            Set Alert
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ALERTS LIST -->
        <div class="space-y-4">
            @if($alerts->isEmpty())
                <div class="bg-white rounded-3xl p-12 sm:p-16 text-center border border-slate-200 shadow-sm space-y-4">
                    <div class="w-16 h-16 rounded-2xl bg-blue-50 text-blue-600 mx-auto flex items-center justify-center text-3xl">
                        🔔
                    </div>
                    <div class="space-y-1">
                        <h3 class="text-lg font-bold text-slate-900">No active price alerts</h3>
                        <p class="text-xs text-slate-500 max-w-sm mx-auto">
                            Setup alerts for your target micro-markets to monitor when valuations or asking quotes fall below your budget.
                        </p>
                    </div>
                    <div>
                        <button @click="createModalOpen = true" class="inline-flex items-center px-4 py-2.5 rounded-xl bg-slate-900 text-white font-bold text-xs shadow-md">
                            Create First Alert
                        </button>
                    </div>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($alerts as $alert)
                        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 flex flex-col justify-between space-y-4 hover:shadow-md transition">
                            
                            <!-- Card Header -->
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold
                                        {{ $alert->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                        <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $alert->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                        {{ $alert->is_active ? 'Monitoring Active' : 'Paused' }}
                                    </span>

                                    <!-- Delete Trigger -->
                                    <form method="POST" action="{{ route('price-alerts.destroy', $alert->id) }}" onsubmit="return confirm('Delete this price alert?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-slate-400 hover:text-rose-500 transition p-1" title="Delete alert">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>

                                <div>
                                    <h3 class="text-base font-bold text-slate-900 font-display">
                                        {{ $alert->locality->name ?? 'Bangalore' }}
                                    </h3>
                                    <p class="text-xs text-slate-500">
                                        {{ $alert->property_type ?? 'Any Typology' }}
                                        @if($alert->bedrooms) • {{ $alert->bedrooms }} BHK @endif
                                    </p>
                                </div>
                            </div>

                            <!-- Target Trigger Box -->
                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-1">
                                <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block">
                                    Trigger Condition
                                </span>
                                <div class="text-sm font-bold text-slate-800">
                                    Price {{ $alert->condition === 'below' ? 'dips below' : 'exceeds' }}
                                    <span class="text-emerald-600 font-black font-display text-base">
                                        {{ \App\Models\Prediction::formatPriceInIndianSystem($alert->target_price) }}
                                    </span>
                                </div>
                                <div class="text-[10px] text-slate-400 pt-1">
                                    Created {{ $alert->created_at->format('M d, Y') }}
                                </div>
                            </div>

                            <!-- Card Footer: Pause/Resume Action -->
                            <div class="pt-2 flex items-center justify-between border-t border-slate-100">
                                <form method="POST" action="{{ route('price-alerts.toggle', $alert->id) }}" class="w-full">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="w-full text-center py-2 rounded-xl text-xs font-bold transition
                                        {{ $alert->is_active ? 'bg-slate-100 hover:bg-slate-200 text-slate-700' : 'bg-emerald-600 hover:bg-emerald-500 text-white shadow-sm' }}">
                                        {{ $alert->is_active ? 'Pause Monitoring' : 'Resume Monitoring' }}
                                    </button>
                                </form>
                            </div>

                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>
</div>
@endsection
