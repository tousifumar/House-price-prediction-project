<div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 pb-6 border-b border-slate-800">
    <div>
        <div class="inline-flex items-center space-x-2 px-2.5 py-1 rounded-full bg-emerald-950 border border-emerald-800 text-emerald-400 text-xs font-semibold">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            <span>Admin Operations Hub</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-black font-display text-white mt-1.5">
            {{ $title ?? 'Operations & Model Registry' }}
        </h1>
        <p class="text-xs text-slate-400">
            {{ $subtitle ?? 'Enterprise administrative controls for real-estate valuation & machine learning pipeline.' }}
        </p>
    </div>

    <!-- Admin Top Nav Tabs -->
    <div class="flex flex-wrap items-center gap-1.5 p-1.5 rounded-2xl bg-slate-800/90 border border-slate-700/80 text-xs font-semibold">
        <a href="{{ route('admin.dashboard') }}" 
           class="px-3 py-1.5 rounded-xl transition {{ request()->routeIs('admin.dashboard') ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-300 hover:text-white hover:bg-slate-700/60' }}">
            Overview
        </a>
        <a href="{{ route('admin.users.index') }}" 
           class="px-3 py-1.5 rounded-xl transition {{ request()->routeIs('admin.users.*') ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-300 hover:text-white hover:bg-slate-700/60' }}">
            Users
        </a>
        <a href="{{ route('admin.properties.index') }}" 
           class="px-3 py-1.5 rounded-xl transition {{ request()->routeIs('admin.properties.*') ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-300 hover:text-white hover:bg-slate-700/60' }}">
            Properties
        </a>
        <a href="{{ route('admin.locations.index') }}" 
           class="px-3 py-1.5 rounded-xl transition {{ request()->routeIs('admin.locations.*') ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-300 hover:text-white hover:bg-slate-700/60' }}">
            Locations
        </a>
        <a href="{{ route('admin.amenities.index') }}" 
           class="px-3 py-1.5 rounded-xl transition {{ request()->routeIs('admin.amenities.*') ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-300 hover:text-white hover:bg-slate-700/60' }}">
            Amenities
        </a>
        <a href="{{ route('admin.datasets.index') }}" 
           class="px-3 py-1.5 rounded-xl transition {{ request()->routeIs('admin.datasets.*') ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-300 hover:text-white hover:bg-slate-700/60' }}">
            Datasets
        </a>
        <a href="{{ route('admin.models.index') }}" 
           class="px-3 py-1.5 rounded-xl transition {{ request()->routeIs('admin.models.*') ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-300 hover:text-white hover:bg-slate-700/60' }}">
            ML Models
        </a>
    </div>
</div>
