@extends('layouts.app')

@section('title', 'Create Account — House Price Predictor')

@section('content')
<div class="min-h-[calc(100vh-160px)] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-slate-50">
    <div class="max-w-md w-full bg-white rounded-3xl shadow-xl border border-slate-200/80 p-8 sm:p-10">
        
        <div class="space-y-2 mb-8 text-center">
            <span class="text-xs font-bold uppercase tracking-wider text-emerald-600">Get Started</span>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-display">Create Your Account</h1>
            <p class="text-xs text-slate-500">Access full AI property valuation tools and historical price trends.</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            <div>
                <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Full Name</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm font-medium transition @error('name') border-rose-500 @enderror"
                       placeholder="e.g. Rahul Sharma">
                @error('name')
                    <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Email Address</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm font-medium transition @error('email') border-rose-500 @enderror"
                       placeholder="you@example.com">
                @error('email')
                    <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="phone" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Phone Number (Optional)</label>
                <input id="phone" type="text" name="phone" value="{{ old('phone') }}"
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm font-medium transition @error('phone') border-rose-500 @enderror"
                       placeholder="+91 98765 43210">
                @error('phone')
                    <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Password</label>
                <input id="password" type="password" name="password" required
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm font-medium transition @error('password') border-rose-500 @enderror"
                       placeholder="Min 8 characters">
                @error('password')
                    <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Confirm Password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm font-medium transition">
            </div>

            <button type="submit" class="w-full mt-2 py-3.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-sm shadow-md transition transform hover:-translate-y-0.5">
                Create Free Account
            </button>
        </form>

        <div class="mt-6 text-center text-xs text-slate-500">
            Already registered? 
            <a href="{{ route('login') }}" class="font-bold text-emerald-600 hover:text-emerald-700 underline">Sign in here</a>
        </div>
    </div>
</div>
@endsection
