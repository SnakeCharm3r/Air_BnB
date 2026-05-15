<div class="border-t border-slate-800 p-4 sidebar-collapsed:p-2">
    <div class="flex items-center gap-3 sidebar-collapsed:justify-center">
        {{-- Avatar with tooltip --}}
        <div class="relative group z-50">
            <div class="w-9 h-9 rounded-full bg-amber-500 flex items-center justify-center flex-shrink-0">
                <span class="text-white text-sm font-bold">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</span>
            </div>
            {{-- Tooltip for collapsed state --}}
            <span class="absolute left-full ml-2 px-2 py-1 bg-slate-800 text-white text-xs rounded opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all whitespace-nowrap z-50 sidebar-collapsed:block lg:hidden">
                {{ auth()->user()->name ?? 'User' }}
            </span>
        </div>
        
        {{-- User info - hidden when collapsed --}}
        <div class="flex-1 min-w-0 sidebar-text transition-all duration-300">
            <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name ?? 'User' }}</p>
            <p class="text-xs text-slate-400 capitalize">{{ auth()->user()->role ?? 'user' }}</p>
        </div>
        
        {{-- Logout button with tooltip --}}
        <form method="POST" action="{{ route('logout') }}" class="inline relative group">
            @csrf
            <button type="submit" class="text-slate-400 hover:text-white transition-colors p-1 rounded hover:bg-slate-800">
                @include('components.icons.logout', ['class' => 'w-5 h-5'])
            </button>
            <span class="absolute left-full ml-2 px-2 py-1 bg-slate-800 text-white text-xs rounded opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all whitespace-nowrap z-50 sidebar-collapsed:block lg:hidden">
                Logout
            </span>
        </form>
    </div>
</div>
