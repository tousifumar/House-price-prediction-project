<header x-data="{ mobileMenuOpen: false, profileDropdownOpen: false }" class="sticky top-0 z-50 bg-white/90 backdrop-blur-md border-b border-slate-200/80 transition-all duration-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">
            <!-- Brand Logo -->
            <div class="flex items-center space-x-3">
                <a href="{{ route('home') }}" class="flex items-center space-x-3 group">
                    <div class="w-11 h-11 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-500 flex items-center justify-center text-white shadow-md shadow-emerald-500/20 group-hover:scale-105 transition-transform duration-200">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                    </div>
                    <div>
                        <span class="text-xl font-extrabold tracking-tight text-slate-900 font-display">HousePrice<span class="text-emerald-600">AI</span></span>
                        <span class="block text-[10px] font-semibold tracking-wider text-slate-600 uppercase">Valuation Platform</span>
                    </div>
                </a>
            </div>

            <!-- Desktop Nav Links -->
            <nav class="hidden md:flex items-center space-x-6 text-sm font-medium text-slate-600">
                <a href="{{ route('predict') }}" class="text-emerald-700 hover:text-emerald-800 font-bold flex items-center transition-colors">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 mr-1.5 animate-pulse"></span>
                    AI Valuation
                </a>
                <a href="{{ route('market-analytics') }}" class="hover:text-emerald-600 transition-colors">Market Analytics</a>
                <a href="{{ route('compare') }}" class="hover:text-emerald-600 transition-colors">Compare</a>
                <a href="{{ route('home') }}#how-it-works" class="hover:text-emerald-600 transition-colors">How It Works</a>
                <a href="{{ route('home') }}#faq" class="hover:text-emerald-600 transition-colors">FAQ</a>
            </nav>

            <!-- Action Buttons / Auth State -->
            <div class="hidden md:flex items-center space-x-4">
                @auth
                    <!-- Authenticated Dropdown -->
                    <div class="relative" @click.outside="profileDropdownOpen = false">
                        <button @click="profileDropdownOpen = !profileDropdownOpen" class="flex items-center space-x-3 p-1.5 rounded-full hover:bg-slate-100 transition-colors">
                            <div class="w-9 h-9 rounded-full bg-emerald-100 border border-emerald-300 flex items-center justify-center text-emerald-800 font-bold text-sm">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <div class="text-left pr-2">
                                <span class="block text-xs font-semibold text-slate-900">{{ Auth::user()->name }}</span>
                                <span class="block text-[10px] font-medium text-slate-500 uppercase">{{ Auth::user()->role }}</span>
                            </div>
                            <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="profileDropdownOpen" 
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-xl border border-slate-100 py-2 z-50"
                             style="display: none;">
                            
                            @if(Auth::user()->isAdmin())
                                <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-2.5 text-sm text-slate-700 hover:bg-emerald-50 hover:text-emerald-700">
                                    <svg class="w-4 h-4 mr-3 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                    </svg>
                                    Admin Dashboard
                                </a>
                            @endif

                            <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50">
                                <svg class="w-4 h-4 mr-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                </svg>
                                User Dashboard
                            </a>

                            <a href="{{ route('predictions.index') }}" class="flex items-center px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50">
                                <svg class="w-4 h-4 mr-3 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Valuation History
                            </a>

                            <a href="{{ route('saved-properties.index') }}" class="flex items-center px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50">
                                <svg class="w-4 h-4 mr-3 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                                </svg>
                                Saved Portfolio
                            </a>

                            <a href="{{ route('price-alerts.index') }}" class="flex items-center px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50">
                                <svg class="w-4 h-4 mr-3 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                Price Alerts
                            </a>

                            <div class="border-t border-slate-100 my-1"></div>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left flex items-center px-4 py-2.5 text-sm text-rose-600 hover:bg-rose-50">
                                    <svg class="w-4 h-4 mr-3 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    Sign Out
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-700 hover:text-emerald-600 transition-colors px-3 py-2">
                        Sign In
                    </a>
                    <a href="{{ route('predict') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-bold transition-all shadow-md shadow-emerald-600/20">
                        Predict Price
                    </a>
                @endauth
            </div>

            <!-- Mobile menu button -->
            <div class="flex items-center md:hidden">
                <button @click="mobileMenuOpen = !mobileMenuOpen" type="button" class="p-2 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path x-show="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" style="display: none;" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile menu -->
    <div x-show="mobileMenuOpen" class="md:hidden border-b border-slate-200 bg-white px-4 pt-2 pb-6 space-y-3" style="display: none;">
        <a href="{{ route('predict') }}" @click="mobileMenuOpen = false" class="block px-3 py-2 rounded-lg text-base font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100">AI Valuation Tool</a>
        <a href="{{ route('market-analytics') }}" @click="mobileMenuOpen = false" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 hover:bg-slate-50">Market Analytics</a>
        <a href="{{ route('compare') }}" @click="mobileMenuOpen = false" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 hover:bg-slate-50">Compare Properties</a>
        <a href="{{ route('home') }}#how-it-works" @click="mobileMenuOpen = false" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 hover:bg-slate-50">How It Works</a>
        <a href="{{ route('home') }}#faq" @click="mobileMenuOpen = false" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 hover:bg-slate-50">FAQ</a>

        <div class="pt-4 border-t border-slate-100 flex flex-col space-y-2">
            @auth
                @if(Auth::user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="w-full text-center py-2.5 rounded-xl bg-emerald-50 text-emerald-700 font-semibold text-sm">
                        Admin Dashboard
                    </a>
                @endif
                <a href="{{ route('dashboard') }}" class="w-full text-center py-2.5 rounded-xl bg-slate-100 text-slate-800 font-semibold text-sm">
                    My Dashboard
                </a>
                <a href="{{ route('predictions.index') }}" class="w-full text-center py-2.5 rounded-xl border border-slate-200 text-slate-700 font-semibold text-sm">
                    Valuation History
                </a>
                <a href="{{ route('saved-properties.index') }}" class="w-full text-center py-2.5 rounded-xl border border-slate-200 text-slate-700 font-semibold text-sm">
                    Saved Portfolio
                </a>
                <a href="{{ route('price-alerts.index') }}" class="w-full text-center py-2.5 rounded-xl border border-slate-200 text-slate-700 font-semibold text-sm">
                    Price Alerts
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full py-2 text-center text-sm font-semibold text-rose-600">
                        Sign Out
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="w-full text-center py-2.5 rounded-xl border border-slate-300 text-slate-700 font-semibold text-sm">
                    Sign In
                </a>
                <a href="{{ route('register') }}" class="w-full text-center py-2.5 rounded-xl bg-emerald-600 text-white font-semibold text-sm">
                    Create Free Account
                </a>
            @endauth
        </div>
    </div>
</header>
