@extends('layouts.app')

@section('title', 'User Management — Admin Command Center')

@section('content')
<div class="py-10 bg-slate-900 min-h-screen text-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        @include('admin.partials.nav', [
            'title' => 'User & Access Management',
            'subtitle' => 'Audit registered investor and appraiser accounts, update administrative permissions, and manage suspensions.'
        ])

        <!-- Search & Filter Bar -->
        <div class="bg-slate-800/80 border border-slate-700/80 rounded-2xl p-5 shadow-sm">
            <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Search User</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Name or email address..."
                           class="w-full bg-slate-900 border border-slate-700 text-slate-200 text-xs rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Role</label>
                    <select name="role" class="w-full bg-slate-900 border border-slate-700 text-slate-200 text-xs rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-emerald-500">
                        <option value="">All Roles</option>
                        <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>User (Standard)</option>
                        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin (Superuser)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Account Status</label>
                    <select name="status" class="w-full bg-slate-900 border border-slate-700 text-slate-200 text-xs rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-emerald-500">
                        <option value="">All Statuses</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="blocked" {{ request('status') === 'blocked' ? 'selected' : '' }}>Suspended</option>
                    </select>
                </div>

                <div class="flex items-center space-x-2">
                    <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs py-2.5 px-4 rounded-xl transition">
                        Filter Accounts
                    </button>
                    @if(request()->hasAny(['search', 'role', 'status']))
                        <a href="{{ route('admin.users.index') }}" class="p-2.5 rounded-xl border border-slate-700 hover:bg-slate-700 text-slate-400" title="Reset">
                            ✕
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Users Table -->
        <div class="bg-slate-800/80 border border-slate-700/80 rounded-3xl overflow-hidden shadow-xl">
            <div class="p-6 border-b border-slate-700/80 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-white font-display">Registered Accounts</h3>
                    <p class="text-xs text-slate-400">Total of {{ $users->total() }} users on platform.</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-900/60 text-slate-400 uppercase font-bold border-b border-slate-700">
                        <tr>
                            <th class="py-3.5 px-6">User</th>
                            <th class="py-3.5 px-6">Role</th>
                            <th class="py-3.5 px-6">Status</th>
                            <th class="py-3.5 px-6">Activity</th>
                            <th class="py-3.5 px-6">Joined Date</th>
                            <th class="py-3.5 px-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/60">
                        @forelse($users as $user)
                            <tr class="hover:bg-slate-700/30 transition">
                                <!-- User details -->
                                <td class="py-4 px-6">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 rounded-full bg-emerald-950 border border-emerald-800 flex items-center justify-center font-bold text-emerald-400 text-xs">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-white text-sm">{{ $user->name }}</div>
                                            <div class="text-[11px] text-slate-400">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Role -->
                                <td class="py-4 px-6">
                                    <form method="POST" action="{{ route('admin.users.role', $user->id) }}">
                                        @csrf
                                        @method('PATCH')
                                        <select name="role" onchange="this.form.submit()" 
                                                class="bg-slate-900 border border-slate-700 text-xs rounded-lg px-2.5 py-1 text-slate-200 font-semibold focus:ring-1 focus:ring-emerald-500">
                                            <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>User</option>
                                            <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                        </select>
                                    </form>
                                </td>

                                <!-- Status -->
                                <td class="py-4 px-6">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase
                                        {{ $user->status === 'active' ? 'bg-emerald-950 text-emerald-400 border border-emerald-800' : 'bg-rose-950 text-rose-400 border border-rose-800' }}">
                                        <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $user->status === 'active' ? 'bg-emerald-400' : 'bg-rose-400' }}"></span>
                                        {{ $user->status }}
                                    </span>
                                </td>

                                <!-- Activity stats -->
                                <td class="py-4 px-6 text-slate-300">
                                    <div class="text-xs">
                                        <strong>{{ $user->predictions_count }}</strong> valuations • 
                                        <strong>{{ $user->saved_properties_count }}</strong> saved
                                    </div>
                                </td>

                                <!-- Joined -->
                                <td class="py-4 px-6 text-slate-400 text-xs">
                                    {{ $user->created_at->format('M d, Y') }}
                                </td>

                                <!-- Action Buttons -->
                                <td class="py-4 px-6 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center space-x-2">
                                        <!-- Toggle Status -->
                                        @if($user->id !== Auth::id())
                                            <form method="POST" action="{{ route('admin.users.status', $user->id) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="px-2.5 py-1 rounded-lg border border-slate-700 hover:bg-slate-700 text-[11px] font-semibold text-slate-300 transition"
                                                        title="{{ $user->status === 'active' ? 'Suspend Account' : 'Activate Account' }}">
                                                    {{ $user->status === 'active' ? 'Suspend' : 'Activate' }}
                                                </button>
                                            </form>

                                            <!-- Delete -->
                                            <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}" onsubmit="return confirm('Permanently remove user {{ $user->name }}?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1 rounded-lg hover:bg-rose-950/60 text-slate-400 hover:text-rose-400 transition" title="Delete User">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-[10px] text-slate-500 italic">Current Session</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-500">No users found matching query criteria.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($users->hasPages())
                <div class="p-4 border-t border-slate-700/80">
                    {{ $users->withQueryString()->links() }}
                </div>
            @endif
        </div>

    </div>
</div>
@endsection
