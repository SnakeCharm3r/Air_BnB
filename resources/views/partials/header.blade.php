<header class="bg-white border-b border-slate-200 sticky top-0 z-20">
    <div class="flex items-center justify-between px-4 lg:px-6 py-4 gap-4">
        <!-- Left: Toggle + Search -->
        <div class="flex items-center gap-4 flex-1">
            <!-- Desktop Sidebar Toggle -->
            <button onclick="toggleDesktopSidebar()" id="sidebar-toggle-btn" class="hidden lg:flex p-2 text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition-colors" title="Toggle Sidebar">
                <span id="icon-expanded">
                    @include('components.icons.panel-left-close', ['class' => 'w-5 h-5'])
                </span>
                <span id="icon-collapsed" class="hidden">
                    @include('components.icons.panel-left-open', ['class' => 'w-5 h-5'])
                </span>
            </button>
            
            <!-- Search Bar -->
            <div class="relative flex-1 max-w-md">
                @include('components.icons.search', ['class' => 'absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400'])
                <input type="text" placeholder="Search..." class="w-full pl-10 pr-4 py-2 bg-slate-100 border-0 rounded-lg text-sm text-slate-700 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:bg-white transition-all">
            </div>
        </div>

        <!-- Right Actions -->
        <div class="flex items-center gap-3">
            @include('partials.header-notifications')
            @include('partials.header-user')
        </div>
    </div>
</header>
