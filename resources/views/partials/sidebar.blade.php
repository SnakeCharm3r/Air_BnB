<aside id="sidebar" class="fixed top-0 left-0 z-40 h-screen bg-slate-900 text-white transition-all duration-300 ease-in-out transform -translate-x-full lg:translate-x-0 w-64" data-collapsed="false">
    <div class="flex flex-col h-full">
        @include('partials.sidebar-logo')
        @include('partials.sidebar-nav')
        
        {{-- Settings - at bottom like old profile section --}}
        <div class="border-t border-slate-800 p-4 sidebar-collapsed:p-2">
            <a href="{{ route('settings') }}" 
               class="group flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('settings') ? 'bg-amber-500 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }} sidebar-collapsed:justify-center sidebar-collapsed:px-0 relative z-10"
               title="Settings">
                @include('components.icons.settings', ['class' => 'w-5 h-5 flex-shrink-0'])
                <span class="sidebar-text transition-all duration-300">Settings</span>
                
                {{-- Tooltip for collapsed state --}}
                <span class="absolute left-full ml-2 px-2 py-1 bg-slate-800 text-white text-xs rounded opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all whitespace-nowrap z-50 sidebar-collapsed:block lg:hidden pointer-events-none">
                    Settings
                </span>
            </a>
        </div>
    </div>
</aside>

@include('partials.sidebar-mobile')
