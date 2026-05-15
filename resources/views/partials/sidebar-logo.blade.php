@php $lodgeName = $appSettings->lodge_name ?? 'LodgeOS'; @endphp
<div class="group flex items-center gap-3 px-4 py-5 border-b border-slate-800 sidebar-collapsed:justify-center sidebar-collapsed:px-2 relative">
    <div class="w-10 h-10 bg-amber-500 rounded-lg flex items-center justify-center flex-shrink-0 overflow-hidden">
        @if(!empty($appSettings->lodge_logo))
            <img src="{{ asset('storage/' . $appSettings->lodge_logo) }}" alt="{{ $lodgeName }}" class="w-full h-full object-cover">
        @else
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
        @endif
    </div>
    <div class="sidebar-text transition-all duration-300">
        <h1 class="font-bold text-lg tracking-tight">{{ $lodgeName }}</h1>
        <p class="text-xs text-slate-400">Management System</p>
    </div>
    
    {{-- Tooltip for collapsed state --}}
    <span class="absolute left-full ml-2 px-2 py-1 bg-slate-800 text-white text-xs rounded opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all whitespace-nowrap z-50 sidebar-collapsed:block lg:hidden">
        {{ $lodgeName }}
    </span>
</div>
