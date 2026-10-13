@extends('layouts.app')

@section('title', 'ML Model Management & Retraining — Admin Command Center')

@section('content')
<div class="py-10 bg-slate-900 min-h-screen text-slate-100" x-data="{ trainModalOpen: false, isTraining: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        @include('admin.partials.nav', [
            'title' => 'Machine Learning Model Lifecycle & Retraining',
            'subtitle' => 'Evaluate multi-algorithm regression candidates, monitor test validation metrics, trigger automated retraining, and activate production models.'
        ])

        <!-- Active Production Model Banner -->
        <div class="bg-gradient-to-br from-slate-900 via-slate-950 to-emerald-950 rounded-3xl p-6 sm:p-8 border border-emerald-900/60 shadow-2xl relative overflow-hidden">
            <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                <div class="space-y-2">
                    <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-emerald-950 border border-emerald-800 text-emerald-400 text-xs font-semibold">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>Active Production Engine</span>
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-black font-display text-white">
                        {{ $activeModel ? $activeModel->algorithm : 'No Active Model Configured' }}
                    </h2>
                    <p class="text-xs text-slate-300">
                        Version: <span class="font-mono text-emerald-400 font-bold">{{ $activeModel ? $activeModel->version : 'N/A' }}</span> • 
                        Trained: <span>{{ $activeModel && $activeModel->trained_at ? $activeModel->trained_at->format('M d, Y • h:i A') : 'N/A' }}</span>
                    </p>
                </div>

                <!-- Active Model Metric Badges -->
                @if($activeModel)
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-3.5 text-center">
                            <span class="block text-[10px] font-bold text-slate-400 uppercase">R² Accuracy</span>
                            <span class="text-xl font-black text-emerald-400 font-display">{{ round($activeModel->r2_score * 100, 1) }}%</span>
                        </div>
                        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-3.5 text-center">
                            <span class="block text-[10px] font-bold text-slate-400 uppercase">MAE Error</span>
                            <span class="text-sm font-bold text-white font-display">₹{{ number_format($activeModel->mae / 100000, 2) }} L</span>
                        </div>
                        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-3.5 text-center">
                            <span class="block text-[10px] font-bold text-slate-400 uppercase">RMSE</span>
                            <span class="text-sm font-bold text-white font-display">₹{{ number_format($activeModel->rmse / 100000, 2) }} L</span>
                        </div>
                        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-3.5 text-center">
                            <span class="block text-[10px] font-bold text-slate-400 uppercase">MAPE</span>
                            <span class="text-sm font-bold text-emerald-400 font-display">{{ $activeModel->mape }}%</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Action Bar: Trigger Retraining -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 bg-slate-800/80 border border-slate-700/80 rounded-2xl p-5 shadow-sm">
            <div>
                <h3 class="text-sm font-bold text-white">Automated Retraining Pipeline</h3>
                <p class="text-xs text-slate-400">Trigger multi-algorithm cross-validation on an updated transaction dataset.</p>
            </div>

            <button @click="trainModalOpen = true" class="inline-flex items-center px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                Launch Model Retraining
            </button>
        </div>

        <!-- RETRAINING MODAL -->
        <div x-show="trainModalOpen" 
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4"
             style="display: none;">
            <div @click.outside="trainModalOpen = false" class="bg-slate-900 rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-700 space-y-6 text-slate-200">
                <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                    <h3 class="text-lg font-bold text-white font-display">Launch ML Training Pipeline</h3>
                    <button @click="trainModalOpen = false" class="text-slate-400 hover:text-white">✕</button>
                </div>

                <form method="POST" action="{{ route('admin.models.train') }}" @submit="isTraining = true" class="space-y-4 text-xs">
                    @csrf

                    <div>
                        <label class="block font-bold uppercase tracking-wider text-slate-400 mb-1">Select Training Dataset</label>
                        <select name="dataset_id" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3.5 py-2.5 text-white">
                            <option value="">Default: ml-service/datasets/bangalore_house_prices.csv (3,000 rows)</option>
                            @foreach($datasets as $ds)
                                <option value="{{ $ds->id }}">{{ $ds->name }} ({{ number_format($ds->row_count) }} rows)</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="p-3.5 rounded-xl bg-slate-950 border border-slate-800 space-y-1.5 text-slate-300">
                        <span class="font-bold text-emerald-400 block uppercase tracking-wider text-[10px]">Training Pipeline Execution</span>
                        <p class="text-[11px] leading-relaxed">
                            The script evaluates 5 regression models (Linear Regression, Decision Tree, Random Forest, XGBoost, and Gradient Boosting) on an 80/20 train-test split with 5-fold cross-validation. The best candidate will be registered automatically in the model catalog.
                        </p>
                    </div>

                    <div class="pt-4 flex justify-end space-x-3">
                        <button type="button" @click="trainModalOpen = false" class="px-4 py-2 rounded-xl border border-slate-700 text-slate-400">Cancel</button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold flex items-center space-x-2">
                            <span x-show="!isTraining">Start Training Process</span>
                            <span x-show="isTraining" style="display: none;">Training in Progress...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Model Registry Table -->
        <div class="bg-slate-800/80 border border-slate-700/80 rounded-3xl overflow-hidden shadow-xl">
            <div class="p-6 border-b border-slate-700/80">
                <h3 class="text-base font-bold text-white font-display">Model Candidates Registry</h3>
                <p class="text-xs text-slate-400">All registered model candidates. Click "Activate" to promote a candidate to production.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-900/60 text-slate-400 uppercase font-bold border-b border-slate-700">
                        <tr>
                            <th class="py-3.5 px-6">Model / Version</th>
                            <th class="py-3.5 px-6">Algorithm</th>
                            <th class="py-3.5 px-6">R² Score</th>
                            <th class="py-3.5 px-6">MAE</th>
                            <th class="py-3.5 px-6">RMSE</th>
                            <th class="py-3.5 px-6">Status</th>
                            <th class="py-3.5 px-6">Trained Date</th>
                            <th class="py-3.5 px-6 text-right">Production Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/60">
                        @foreach($models as $model)
                            <tr class="hover:bg-slate-700/30 transition {{ $model->status === 'active' ? 'bg-emerald-950/20' : '' }}">
                                <td class="py-4 px-6 font-mono font-bold text-white">
                                    {{ $model->version }}
                                </td>
                                <td class="py-4 px-6 font-semibold text-slate-200">
                                    {{ $model->algorithm }}
                                </td>
                                <td class="py-4 px-6 font-black font-display text-emerald-400 text-sm">
                                    {{ round($model->r2_score * 100, 2) }}%
                                </td>
                                <td class="py-4 px-6 text-slate-300">
                                    ₹{{ number_format($model->mae / 100000, 2) }} L
                                </td>
                                <td class="py-4 px-6 text-slate-300">
                                    ₹{{ number_format($model->rmse / 100000, 2) }} L
                                </td>
                                <td class="py-4 px-6">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase
                                        {{ $model->status === 'active' ? 'bg-emerald-950 text-emerald-400 border border-emerald-800' : 'bg-slate-900 text-slate-400 border border-slate-700' }}">
                                        {{ $model->status }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-slate-400 text-xs">
                                    {{ $model->trained_at ? $model->trained_at->format('M d, Y') : 'N/A' }}
                                </td>
                                <td class="py-4 px-6 text-right">
                                    @if($model->status === 'active')
                                        <span class="text-xs font-bold text-emerald-400">Current Production</span>
                                    @else
                                        <form method="POST" action="{{ route('admin.models.activate', $model->id) }}" onsubmit="return confirm('Promote model {{ $model->version }} to active production?');" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs transition shadow-sm">
                                                Activate
                                            </button>
                                        </form>
                                    @endif
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
