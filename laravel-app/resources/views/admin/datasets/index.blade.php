@extends('layouts.app')

@section('title', 'Dataset Management — Admin Command Center')

@section('content')
<div class="py-10 bg-slate-900 min-h-screen text-slate-100" x-data="datasetManager()">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        @include('admin.partials.nav', [
            'title' => 'Machine Learning Dataset Management',
            'subtitle' => 'Upload, validate, inspect, and register residential transaction datasets for model training & cross-validation.'
        ])

        <!-- Top Controls & Action -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 bg-slate-800/80 border border-slate-700/80 rounded-2xl p-5 shadow-sm">
            <div>
                <span class="text-xs text-slate-400">Registered training repositories: <strong class="text-white">{{ $datasets->total() }}</strong></span>
            </div>

            <button @click="uploadModalOpen = true" class="inline-flex items-center px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                Upload CSV Dataset
            </button>
        </div>

        <!-- UPLOAD DATASET MODAL -->
        <div x-show="uploadModalOpen" 
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4"
             style="display: none;">
            <div @click.outside="uploadModalOpen = false" class="bg-slate-900 rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-700 space-y-6 text-slate-200">
                <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                    <h3 class="text-lg font-bold text-white font-display">Upload Training Dataset</h3>
                    <button @click="uploadModalOpen = false" class="text-slate-400 hover:text-white">✕</button>
                </div>

                <form method="POST" action="{{ route('admin.datasets.store') }}" enctype="multipart/form-data" class="space-y-4 text-xs">
                    @csrf

                    <div>
                        <label class="block font-bold uppercase tracking-wider text-slate-400 mb-1">Dataset Name</label>
                        <input type="text" name="name" required placeholder="e.g. Bangalore Real Estate Transactions Q1 2026"
                               class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3.5 py-2.5 text-white">
                    </div>

                    <div>
                        <label class="block font-bold uppercase tracking-wider text-slate-400 mb-1">CSV File</label>
                        <input type="file" name="dataset_file" required accept=".csv,.txt"
                               class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3.5 py-2 text-slate-300 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-emerald-600 file:text-white hover:file:bg-emerald-500">
                        <p class="text-[10px] text-slate-400 mt-1">Expected columns: area, bedrooms, bathrooms, floor, property_age, locality, property_type, price.</p>
                    </div>

                    <div>
                        <label class="block font-bold uppercase tracking-wider text-slate-400 mb-1">Description (Optional)</label>
                        <textarea name="description" rows="3" placeholder="Source, collection date, notes..."
                                  class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3.5 py-2.5 text-white"></textarea>
                    </div>

                    <div class="pt-4 flex justify-end space-x-3">
                        <button type="button" @click="uploadModalOpen = false" class="px-4 py-2 rounded-xl border border-slate-700 text-slate-400">Cancel</button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold">Validate & Upload</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- PREVIEW SAMPLE MODAL -->
        <div x-show="previewModalOpen" 
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4"
             style="display: none;">
            <div @click.outside="previewModalOpen = false" class="bg-slate-900 rounded-3xl max-w-4xl w-full p-6 sm:p-8 shadow-2xl border border-slate-700 space-y-6 text-slate-200">
                <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                    <div>
                        <h3 class="text-lg font-bold text-white font-display">Dataset Preview: <span x-text="previewData.name" class="text-emerald-400"></span></h3>
                        <p class="text-xs text-slate-400">First 10 sample records (<span x-text="previewData.row_count"></span> total rows, <span x-text="previewData.column_count"></span> columns)</p>
                    </div>
                    <button @click="previewModalOpen = false" class="text-slate-400 hover:text-white">✕</button>
                </div>

                <div class="overflow-x-auto max-h-96 border border-slate-800 rounded-xl">
                    <table class="w-full text-left text-[11px]">
                        <thead class="bg-slate-950 text-slate-400 sticky top-0 uppercase font-bold border-b border-slate-800">
                            <tr>
                                <template x-for="(header, i) in previewData.headers" :key="i">
                                    <th class="py-2.5 px-3 whitespace-nowrap" x-text="header"></th>
                                </template>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800">
                            <template x-for="(row, r) in previewData.rows" :key="r">
                                <tr class="hover:bg-slate-800/50">
                                    <template x-for="(cell, c) in row" :key="c">
                                        <td class="py-2 px-3 whitespace-nowrap text-slate-300" x-text="cell"></td>
                                    </template>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-end">
                    <button @click="previewModalOpen = false" class="px-5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs">Close Preview</button>
                </div>
            </div>
        </div>

        <!-- Datasets Table -->
        <div class="bg-slate-800/80 border border-slate-700/80 rounded-3xl overflow-hidden shadow-xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-900/60 text-slate-400 uppercase font-bold border-b border-slate-700">
                        <tr>
                            <th class="py-3.5 px-6">Dataset Name / File</th>
                            <th class="py-3.5 px-6">Volume</th>
                            <th class="py-3.5 px-6">Status</th>
                            <th class="py-3.5 px-6">Trained Models</th>
                            <th class="py-3.5 px-6">Uploaded By</th>
                            <th class="py-3.5 px-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/60">
                        @forelse($datasets as $dataset)
                            <tr class="hover:bg-slate-700/30 transition">
                                <td class="py-4 px-6">
                                    <div class="font-bold text-white text-sm">{{ $dataset->name }}</div>
                                    <div class="text-[11px] text-slate-400 font-mono">{{ $dataset->filename }}</div>
                                </td>
                                <td class="py-4 px-6 text-slate-300">
                                    <span class="font-bold text-emerald-400">{{ number_format($dataset->row_count) }}</span> rows • 
                                    <span>{{ $dataset->column_count }} columns</span>
                                </td>
                                <td class="py-4 px-6">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-emerald-950 text-emerald-400 border border-emerald-800">
                                        {{ $dataset->status }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-slate-300">
                                    {{ $dataset->mlModels->count() }} models
                                </td>
                                <td class="py-4 px-6 text-slate-400 text-xs">
                                    {{ $dataset->uploader->name ?? 'System' }}
                                    <div class="text-[10px] text-slate-500">{{ $dataset->created_at->format('M d, Y') }}</div>
                                </td>
                                <td class="py-4 px-6 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center space-x-2">
                                        <!-- Preview Button -->
                                        <button @click="loadPreview({{ $dataset->id }})" 
                                                class="px-2.5 py-1 rounded-lg bg-slate-700 hover:bg-slate-600 text-white font-semibold transition" title="Preview CSV sample">
                                            Preview
                                        </button>

                                        <!-- Delete -->
                                        <form method="POST" action="{{ route('admin.datasets.destroy', $dataset->id) }}" onsubmit="return confirm('Permanently delete dataset file and record?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1 rounded-lg hover:bg-rose-950/60 text-slate-400 hover:text-rose-400 transition" title="Delete Dataset">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-500">No training datasets uploaded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($datasets->hasPages())
                <div class="p-4 border-t border-slate-700/80">
                    {{ $datasets->links() }}
                </div>
            @endif
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
function datasetManager() {
    return {
        uploadModalOpen: false,
        previewModalOpen: false,
        previewData: { name: '', headers: [], rows: [], row_count: 0, column_count: 0 },

        async loadPreview(datasetId) {
            try {
                const res = await fetch('/admin/datasets/' + datasetId + '/preview');
                if (!res.ok) throw new Error('Preview fetch failed');
                this.previewData = await res.json();
                this.previewModalOpen = true;
            } catch (e) {
                alert('Could not load dataset preview: ' + e.message);
            }
        }
    }
}
</script>
@endpush
