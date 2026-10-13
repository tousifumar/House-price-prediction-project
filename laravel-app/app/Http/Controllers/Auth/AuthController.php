<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();

        if ($user->isBlocked()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => 'Your account has been deactivated. Please contact support.',
            ]);
        }

        if ($user->isAdmin()) {
            return redirect()->intended(route('admin.dashboard', absolute: false))
                ->with('success', 'Welcome back to the Admin Command Center, ' . $user->name);
        }

        return redirect()->intended(route('dashboard', absolute: false))
            ->with('success', 'Welcome back, ' . $user->name);
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request): RedirectResponse
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'role' => 'user',
            'status' => 'active',
        ]);

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('success', 'Account created successfully! Welcome to House Price Predictor.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('info', 'You have been securely signed out.');
    }

    public function demoLogin(Request $request): RedirectResponse
    {
        $role = $request->input('role', 'demo');

        $email = $role === 'admin' ? 'admin@houseprice.ai' : 'demo@houseprice.ai';
        $user = User::where('email', $email)->first();

        if (! $user) {
            return redirect()->route('login')->with('error', 'Demo user not found. Please run seeders first.');
        }

        Auth::login($user);
        $request->session()->regenerate();

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard')->with('success', 'Logged in as Administrator (Demo Mode)');
        }

        return redirect()->route('dashboard')->with('success', 'Logged in as Demo Investor');
    }
}
