@extends('layouts.app')

@section('title', 'Predict House Price — AI Valuation Engine')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<style>
    #map { height: 260px; border-radius: 1rem; }
</style>
@endpush

@section('content')
<div class="py-12 bg-slate-50 min-h-screen" x-data="predictionForm()">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        <!-- Header Banner -->
        <div class="bg-gradient-to-r from-slate-900 via-slate-950 to-emerald-950 rounded-3xl p-8 sm:p-10 text-white shadow-xl relative overflow-hidden">
            <div class="space-y-3 relative z-10 max-w-2xl">
                <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-emerald-500/20 border border-emerald-500/30 text-emerald-300 text-xs font-semibold">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>AI Valuation Engine • Multi-Variable Regression</span>
                </div>
                <h1 class="text-3xl sm:text-4xl font-black font-display tracking-tight">
                    Evaluate Property Market Worth
                </h1>
                <p class="text-xs sm:text-sm text-slate-300 leading-relaxed">
                    Provide property physical specifications, locality, society amenities, and floor position to generate an algorithmic price estimate and confidence bounds.
                </p>
            </div>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xl overflow-hidden p-6 sm:p-10">
            <form method="POST" action="{{ route('predict.store') }}" @submit="isSubmitting = true" class="space-y-10">
                @csrf

                <!-- SECTION 1: PROPERTY SPECIFICATIONS -->
                <div class="space-y-6">
                    <div class="flex items-center space-x-3 pb-3 border-b border-slate-100">
                        <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 font-bold flex items-center justify-center text-sm">1</div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900 font-display">Property Specifications</h3>
                            <p class="text-xs text-slate-500">Physical configuration and layout</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Property Type -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Property Type</label>
                            <select name="property_type" x-model="propertyType" class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-xl px-3.5 py-3 focus:ring-2 focus:ring-emerald-500 font-medium transition">
                                @foreach($propertyTypes as $pt)
                                    <option value="{{ $pt }}">{{ $pt }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Transaction Type -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Transaction Type</label>
                            <select name="transaction_type" class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-xl px-3.5 py-3 focus:ring-2 focus:ring-emerald-500 font-medium transition">
                                <option value="Sale" selected>Resale / New Launch Sale</option>
                                <option value="Rent">Rental (Coming Soon)</option>
                            </select>
                        </div>

                        <!-- Built-up Area -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Super Built-up Area (sq.ft)</label>
                            <input type="number" name="area" x-model.number="area" @input="updateCarpet()" required min="100" max="25000" step="10"
                                   class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-xl px-3.5 py-3 focus:ring-2 focus:ring-emerald-500 font-medium transition @error('area') border-rose-500 @enderror">
                            @error('area') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Carpet Area -->
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Carpet Area (sq.ft)</label>
                                <span class="text-[10px] text-emerald-600 font-semibold cursor-pointer" @click="updateCarpet()">Auto 82%</span>
                            </div>
                            <input type="number" name="carpet_area" x-model.number="carpetArea" min="50" max="25000" step="10"
                                   class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-xl px-3.5 py-3 focus:ring-2 focus:ring-emerald-500 font-medium transition">
                        </div>

                        <!-- Bedrooms -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Bedrooms (BHK)</label>
                            <select name="bedrooms" x-model.number="bedrooms" class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-xl px-3.5 py-3 focus:ring-2 focus:ring-emerald-500 font-medium transition">
                                @for($i=1; $i<=6; $i++)
                                    <option value="{{ $i }}" {{ $i === 3 ? 'selected' : '' }}>{{ $i }} BHK</option>
                                @endfor
                            </select>
                        </div>

                        <!-- Bathrooms -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Bathrooms</label>
                            <select name="bathrooms" class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-xl px-3.5 py-3 focus:ring-2 focus:ring-emerald-500 font-medium transition">
                                @for($i=1; $i<=6; $i++)
                                    <option value="{{ $i }}" {{ $i === 2 ? 'selected' : '' }}>{{ $i }} {{ $i === 1 ? 'Bath' : 'Baths' }}</option>
                                @endfor
                            </select>
                        </div>

                        <!-- Balconies -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Balconies</label>
                            <select name="balconies" class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-xl px-3.5 py-3 focus:ring-2 focus:ring-emerald-500 font-medium transition">
                                @for($i=0; $i<=5; $i++)
                                    <option value="{{ $i }}" {{ $i === 2 ? 'selected' : '' }}>{{ $i }} {{ $i === 1 ? 'Balcony' : 'Balconies' }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                </div>

                <!-- SECTION 2: BUILDING & UNIT DETAILS -->
                <div class="space-y-6">
                    <div class="flex items-center space-x-3 pb-3 border-b border-slate-100">
                        <div class="w-8 h-8 rounded-lg bg-teal-100 text-teal-700 font-bold flex items-center justify-center text-sm">2</div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900 font-display">Building & Floor Details</h3>
                            <p class="text-xs text-slate-500">Elevation, age, and maintenance condition</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        <!-- Floor -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Unit Floor</label>
                            <input type="number" name="floor" value="{{ old('floor', 5) }}" min="0" max="60"
                                   class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-xl px-3.5 py-3 focus:ring-2 focus:ring-emerald-500 font-medium transition">
                        </div>

                        <!-- Total Floors -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Total Floors</label>
                            <input type="number" name="total_floors" value="{{ old('total_floors', 14) }}" min="1" max="60"
                                   class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-xl px-3.5 py-3 focus:ring-2 focus:ring-emerald-500 font-medium transition">
                        </div>

                        <!-- Property Age -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Age (Years)</label>
                            <input type="number" name="property_age" value="{{ old('property_age', 3) }}" min="0" max="50"
                                   class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-xl px-3.5 py-3 focus:ring-2 focus:ring-emerald-500 font-medium transition">
                        </div>

                        <!-- Condition -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Condition</label>
                            <select name="condition" class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-xl px-3.5 py-3 focus:ring-2 focus:ring-emerald-500 font-medium transition">
                                @foreach($conditions as $c)
                                    <option value="{{ $c }}" {{ $c === 'Good' ? 'selected' : '' }}>{{ $c }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- SECTION 3: FURNISHING & PARKING -->
                <div class="space-y-6">
                    <div class="flex items-center space-x-3 pb-3 border-b border-slate-100">
                        <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-700 font-bold flex items-center justify-center text-sm">3</div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900 font-display">Furnishing & Parking</h3>
                            <p class="text-xs text-slate-500">Interior status and parking provision</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Furnishing Status</label>
                            <div class="grid grid-cols-3 gap-3">
                                @foreach($furnishings as $f)
                                    <label class="flex items-center justify-center p-3 rounded-xl border border-slate-200 bg-slate-50 text-xs font-semibold text-slate-700 cursor-pointer hover:bg-emerald-50 transition has-[:checked]:bg-emerald-600 has-[:checked]:text-white has-[:checked]:border-emerald-600 shadow-sm">
                                        <input type="radio" name="furnishing" value="{{ $f }}" {{ $f === 'Semi-Furnished' ? 'checked' : '' }} class="sr-only">
                                        <span>{{ $f }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Parking Space</label>
                            <label class="flex items-center space-x-3 p-3 rounded-xl border border-slate-200 bg-slate-50 text-sm font-medium text-slate-700 cursor-pointer hover:bg-slate-100 transition">
                                <input type="checkbox" name="parking" value="1" checked class="w-5 h-5 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                <span class="font-semibold text-slate-800">Dedicated Covered/Open Car Parking</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- SECTION 4: LOCATION & LOCALITY -->
                <div class="space-y-6">
                    <div class="flex items-center space-x-3 pb-3 border-b border-slate-100">
                        <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-700 font-bold flex items-center justify-center text-sm">4</div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900 font-display">Location & Geographic Pin</h3>
                            <p class="text-xs text-slate-500">Select Bangalore micro-market and coordinates</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">City</label>
                                <input type="text" name="city" value="Bangalore" readonly
                                       class="w-full bg-slate-100 border border-slate-200 text-slate-600 text-sm rounded-xl px-3.5 py-3 font-semibold cursor-not-allowed">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Locality / Neighborhood</label>
                                <select name="locality" x-model="selectedLocality" @change="onLocalityChange($event)" required
                                        class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-xl px-3.5 py-3 focus:ring-2 focus:ring-emerald-500 font-semibold transition">
                                    @foreach($localities as $loc)
                                        <option value="{{ $loc->name }}" 
                                                data-lat="{{ $loc->latitude }}" 
                                                data-lng="{{ $loc->longitude }}" 
                                                data-rate="{{ $loc->avg_price_per_sqft }}"
                                                {{ $loc->slug === 'whitefield' ? 'selected' : '' }}>
                                            {{ $loc->name }} • Benchmark ₹{{ number_format($loc->avg_price_per_sqft) }}/sqft
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Latitude</label>
                                    <input type="text" name="latitude" x-model="lat" readonly class="w-full bg-slate-100 border border-slate-200 text-slate-600 text-xs rounded-xl px-3 py-2.5 font-mono">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Longitude</label>
                                    <input type="text" name="longitude" x-model="lng" readonly class="w-full bg-slate-100 border border-slate-200 text-slate-600 text-xs rounded-xl px-3 py-2.5 font-mono">
                                </div>
                            </div>
                        </div>

                        <!-- Map Selector -->
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">Interactive Map Location</span>
                                <span class="text-[10px] text-slate-500">Drag pin to adjust</span>
                            </div>
                            <div id="map" class="border border-slate-200 shadow-inner"></div>
                        </div>
                    </div>
                </div>

                <!-- SECTION 5: SOCIETY AMENITIES -->
                <div class="space-y-6">
                    <div class="flex items-center space-x-3 pb-3 border-b border-slate-100">
                        <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 font-bold flex items-center justify-center text-sm">5</div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900 font-display">Society & Society Amenities</h3>
                            <p class="text-xs text-slate-500">Select all gated amenities available</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3">
                        @foreach($amenities as $amenity)
                            <label class="flex items-center space-x-2.5 p-3 rounded-xl border border-slate-200/80 bg-slate-50/50 hover:bg-emerald-50 text-xs font-semibold text-slate-700 cursor-pointer transition has-[:checked]:bg-emerald-50 has-[:checked]:border-emerald-400 has-[:checked]:text-emerald-900">
                                <input type="checkbox" name="amenities[]" value="{{ $amenity->name }}" 
                                       {{ in_array($amenity->slug, ['parking', 'lift', 'security', 'gym', 'power-backup']) ? 'checked' : '' }}
                                       class="w-4 h-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                <span class="truncate">{{ $amenity->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- SECTION 6: USER ASKING PRICE (OPTIONAL) -->
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-800">Your Target / Asking Price (Optional)</h4>
                            <p class="text-xs text-slate-500">Compare your quote directly against the ML model's fair market valuation.</p>
                        </div>
                    </div>
                    <div class="max-w-md">
                        <input type="number" name="user_asking_price" placeholder="e.g. 14500000 for ₹1.45 Cr" step="10000"
                               class="w-full bg-white border border-slate-200 text-slate-800 text-sm rounded-xl px-4 py-3 focus:ring-2 focus:ring-emerald-500 font-semibold transition">
                    </div>
                </div>

                <!-- SUBMIT BUTTON -->
                <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="text-xs text-slate-500">
                        Inference Model: <strong class="text-slate-800">{{ $activeModel ? $activeModel->algorithm : 'Gradient Boosting Regressor' }}</strong> (Accuracy ~98.8%)
                    </div>

                    <button type="submit" :disabled="isSubmitting"
                            class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 rounded-2xl bg-emerald-600 hover:bg-emerald-500 disabled:opacity-50 text-white font-bold text-base shadow-glow transition transform hover:-translate-y-0.5">
                        <span x-show="!isSubmitting">Predict House Price &rarr;</span>
                        <span x-show="isSubmitting" class="flex items-center" style="display: none;">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                            Analyzing 20+ Features...
                        </span>
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
function predictionForm() {
    return {
        area: 1500,
        carpetArea: 1230,
        bedrooms: 3,
        propertyType: 'Apartment',
        selectedLocality: 'Whitefield',
        lat: 12.9698,
        lng: 77.7499,
        isSubmitting: false,
        map: null,
        marker: null,

        init() {
            this.updateCarpet();
            this.$nextTick(() => {
                this.initMap();
            });
        },

        updateCarpet() {
            if (this.area) {
                this.carpetArea = Math.round(this.area * 0.82);
            }
        },

        initMap() {
            const mapContainer = document.getElementById('map');
            if (!mapContainer || typeof L === 'undefined') return;

            this.map = L.map('map').setView([this.lat, this.lng], 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 18,
                attribution: '© OpenStreetMap contributors'
            }).addTo(this.map);

            this.marker = L.marker([this.lat, this.lng], { draggable: true }).addTo(this.map);
            this.marker.on('dragend', (e) => {
                const pos = e.target.getLatLng();
                this.lat = pos.lat.toFixed(6);
                this.lng = pos.lng.toFixed(6);
            });
        },

        onLocalityChange(event) {
            const opt = event.target.options[event.target.selectedIndex];
            const newLat = parseFloat(opt.getAttribute('data-lat') || 12.9716);
            const newLng = parseFloat(opt.getAttribute('data-lng') || 77.5946);

            this.lat = newLat;
            this.lng = newLng;

            if (this.map && this.marker) {
                this.map.setView([newLat, newLng], 13);
                this.marker.setLatLng([newLat, newLng]);
            }
        }
    }
}
</script>
@endpush
