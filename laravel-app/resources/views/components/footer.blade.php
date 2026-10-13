<footer class="bg-slate-950 text-slate-400 pt-16 pb-12 border-t border-slate-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-10 pb-12 border-b border-slate-900">
            <!-- Brand Column -->
            <div class="lg:col-span-2 space-y-4">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-500 to-teal-400 flex items-center justify-center text-white font-bold shadow-md shadow-emerald-500/20">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                    </div>
                    <span class="text-xl font-bold tracking-tight text-white font-display">HousePrice<span class="text-emerald-500">AI</span></span>
                </div>
                <p class="text-sm text-slate-400 leading-relaxed max-w-sm">
                    AI-powered property valuation and real-estate market intelligence platform. Accurate algorithmic price predictions backed by machine learning models and hyper-local transaction patterns.
                </p>
                <div class="pt-2 flex items-center space-x-2 text-xs text-emerald-400 font-medium">
                    <span class="inline-block w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Active ML Model: XGBoost v1.0 • 98.4% Accuracy</span>
                </div>
            </div>

            <!-- Platform Links -->
            <div>
                <h4 class="text-sm font-semibold text-white tracking-wider uppercase mb-4">Platform</h4>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="{{ route('home') }}" class="hover:text-white transition-colors">Instant Valuation</a></li>
                    <li><a href="{{ route('home') }}#features" class="hover:text-white transition-colors">Predictive Analytics</a></li>
                    <li><a href="{{ route('home') }}#market-stats" class="hover:text-white transition-colors">Market Benchmarks</a></li>
                    <li><a href="{{ route('login') }}" class="hover:text-white transition-colors">Valuation History</a></li>
                    <li><a href="{{ route('login') }}" class="hover:text-white transition-colors">Compare Properties</a></li>
                </ul>
            </div>

            <!-- Bangalore Hubs -->
            <div>
                <h4 class="text-sm font-semibold text-white tracking-wider uppercase mb-4">Supported Hubs</h4>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="{{ route('home') }}#locations" class="hover:text-white transition-colors">Whitefield Tech Corridor</a></li>
                    <li><a href="{{ route('home') }}#locations" class="hover:text-white transition-colors">Electronic City Phase 1 & 2</a></li>
                    <li><a href="{{ route('home') }}#locations" class="hover:text-white transition-colors">HSR Layout & Koramangala</a></li>
                    <li><a href="{{ route('home') }}#locations" class="hover:text-white transition-colors">Indiranagar & CBD</a></li>
                    <li><a href="{{ route('home') }}#locations" class="hover:text-white transition-colors">Hebbal & North Bangalore</a></li>
                </ul>
            </div>

            <!-- Resources & Legal -->
            <div>
                <h4 class="text-sm font-semibold text-white tracking-wider uppercase mb-4">Developer & Legal</h4>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="/api/health" target="_blank" class="hover:text-white transition-colors">System Health API</a></li>
                    <li><a href="#faq" class="hover:text-white transition-colors">Valuation Methodology</a></li>
                    <li><a href="#faq" class="hover:text-white transition-colors">Terms of Service</a></li>
                    <li><a href="#faq" class="hover:text-white transition-colors">Privacy Policy</a></li>
                    <li>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-950 text-emerald-300 border border-emerald-800">
                            FastAPI ML v1.0
                        </span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Disclaimer & Copyright -->
        <div class="pt-8 space-y-4 text-xs text-slate-500 leading-relaxed">
            <div class="p-4 rounded-xl bg-slate-900/60 border border-slate-800/80">
                <span class="font-semibold text-slate-400 block mb-1">Regulatory & Valuation Disclaimer:</span>
                House Price Predictor provides an automated mathematical estimation based on algorithmic regression models and sample market data available to the platform. Actual real-estate transaction prices vary based on bespoke unit features, interior finishes, exact floor rise, title diligence, bank valuations, and bilateral negotiations. This estimate is not an appraisal, certified valuation, or financial/legal advice.
            </div>

            <div class="flex flex-col sm:flex-row items-center justify-between text-slate-500 pt-2">
                <p>&copy; {{ date('Y') }} House Price Predictor SaaS. All rights reserved.</p>
                <div class="flex items-center space-x-6 mt-3 sm:mt-0">
                    <span class="text-slate-400">Bangalore Real Estate Data Engine</span>
                    <span class="text-emerald-500">99.9% Uptime</span>
                </div>
            </div>
        </div>
    </div>
</footer>
