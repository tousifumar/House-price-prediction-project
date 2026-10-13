@extends('layouts.app')

@section('title', 'Sign In — House Price Predictor')

@section('content')
<div class="min-h-[calc(100vh-160px)] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-slate-50">
    <div class="max-w-4xl w-full grid grid-cols-1 lg:grid-cols-12 bg-white rounded-3xl shadow-xl border border-slate-200/80 overflow-hidden">
        
        <!-- Left Side: Login Form -->
        <div class="lg:col-span-7 p-8 sm:p-12">
            <div class="space-y-2 mb-8">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-600">Access Portal</span>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-display">Welcome Back</h1>
                <p class="text-sm text-slate-500">Sign in to your account to access saved properties, predictions, and alerts.</p>
            </div>

            <!-- Demo 1-Click Login Helper Box -->
            <div class="mb-8 p-4 rounded-2xl bg-emerald-50/70 border border-emerald-200/80 space-y-3">
                <div class="flex items-center justify-between text-xs">
                    <span class="font-bold text-emerald-900 flex items-center">
                        <svg class="w-4 h-4 mr-1 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        1-Click Test Login (Demo Mode)
                    </span>
                    <span class="text-[10px] text-emerald-700 font-medium">Instant Evaluation</span>
                </div>
                <div class="grid grid-cols-2 gap-2.5">
                    <form method="POST" action="{{ route('auth.demo-login') }}">
                        @csrf
                        <input type="hidden" name="role" value="demo">
                        <button type="submit" class="w-full py-2 px-3 rounded-xl bg-white hover:bg-emerald-100/50 border border-emerald-300 text-xs font-bold text-emerald-800 transition shadow-sm">
                            👤 Demo User
                        </button>
                    </form>
                    <form method="POST" action="{{ route('auth.demo-login') }}">
                        @csrf
                        <input type="hidden" name="role" value="admin">
                        <button type="submit" class="w-full py-2 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition shadow-sm">
                            ⚡ System Admin
                        </button>
                    </form>
                </div>
            </div>

            <!-- Standard Credentials Form -->
            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Email Address</label>
                    <input id="email" type="email" name="email" value="{{ old('email', 'demo@houseprice.ai') }}" required autofocus
                           class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm font-medium transition @error('email') border-rose-500 @enderror">
                    @error('email')
                        <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Password</label>
                    </div>
                    <input id="password" type="password" name="password" value="Demo@12345" required
                           class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm font-medium transition @error('password') border-rose-500 @enderror">
                    @error('password')
                        <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between text-sm">
                    <label class="flex items-center text-xs text-slate-600 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                        <span class="ml-2 font-medium">Keep me signed in</span>
                    </label>
                </div>

                <button type="submit" class="w-full py-3.5 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-sm shadow-md transition transform hover:-translate-y-0.5">
                    Sign In to Account
                </button>
            </form>

            <div class="mt-8 text-center text-xs text-slate-500">
                Don't have an account yet? 
                <a href="{{ route('register') }}" class="font-bold text-emerald-600 hover:text-emerald-700 underline">Create one free</a>
            </div>
        </div>

        <!-- Right Side: Platform Perks Showcase -->
        <div class="hidden lg:flex lg:col-span-5 bg-gradient-to-br from-slate-900 via-slate-950 to-emerald-950 p-10 flex-col justify-between text-white relative overflow-hidden">
            <div class="space-y-6 relative z-10">
                <div class="w-12 h-12 rounded-2xl bg-emerald-500/20 border border-emerald-500/30 flex items-center justify-center text-emerald-400 font-bold">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <h3 class="text-2xl font-bold font-display leading-snug">
                    Commercial-Grade Real Estate Intelligence
                </h3>
                <p class="text-xs text-slate-300 leading-relaxed">
                    Access machine learning price estimates, historical locality charts, confidence bounds, and comparison workspaces.
                </p>

                <div class="space-y-3 pt-4 text-xs">
                    <div class="flex items-center space-x-2 text-slate-200">
                        <svg class="w-4 h-4 text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                        <span>XGBoost & Regression Pipeline</span>
                    </div>
                    <div class="flex items-center space-x-2 text-slate-200">
                        <svg class="w-4 h-4 text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                        <span>Locality Benchmark Deviations</span>
                    </div>
                    <div class="flex items-center space-x-2 text-slate-200">
                        <svg class="w-4 h-4 text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                        <span>Downloadable PDF Valuation Reports</span>
                    </div>
                </div>
            </div>

            <div class="pt-8 border-t border-slate-800 text-[11px] text-slate-400 relative z-10">
                Encrypted Session • Role-Based Access Control
            </div>
        </div>
    </div>
</div>
@endsection
