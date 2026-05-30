@php
$navItems = [
    ['route' => 'dashboard', 'icon' => 'dashboard', 'label' => 'Dashboard'],
    ['route' => 'web.rooms', 'icon' => 'rooms', 'label' => 'Rooms'],
    ['route' => 'bookings', 'icon' => 'bookings', 'label' => 'Bookings'],
    ['route' => 'billing.index', 'icon' => 'billing', 'label' => 'Billing'],
    ['route' => 'staff.index', 'icon' => 'staff', 'label' => 'Staff'],
    ['route' => 'tasks.index', 'icon' => 'tasks', 'label' => 'My Tasks'],
    ['route' => 'inventory.index', 'icon' => 'inventory', 'label' => 'Inventory'],
    ['route' => 'costs.index', 'icon' => 'costs', 'label' => 'Costs'],
    ['route' => 'reports.index', 'icon' => 'reports', 'label' => 'Reports'],
    ['route' => 'maintenance.index', 'icon' => 'maintenance', 'label' => 'Maintenance'],
    ['route' => 'infrastructure.index', 'icon' => 'infrastructure', 'label' => 'Infrastructure'],
    ['route' => 'users.index', 'icon' => 'users', 'label' => 'User Management'],
];
@endphp

<nav class="flex-1 py-4 px-3 space-y-1 sidebar-collapsed:px-2 sidebar-collapsed:overflow-hidden overflow-y-auto">
    @foreach($navItems as $item)
        @php $isActive = request()->routeIs($item['route']); @endphp
        <a href="{{ route($item['route']) }}" 
           class="group flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all {{ $isActive ? 'bg-amber-500 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }} sidebar-collapsed:justify-center sidebar-collapsed:px-2 relative z-10"
           title="{{ $item['label'] }}">
            @include('components.icons.' . $item['icon'], ['class' => 'w-5 h-5 flex-shrink-0'])
            <span class="truncate sidebar-text transition-all duration-300">{{ $item['label'] }}</span>
            
            {{-- Tooltip for collapsed state --}}
            <span class="absolute left-full ml-2 px-2 py-1 bg-slate-800 text-white text-xs rounded opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all whitespace-nowrap z-50 sidebar-collapsed:block lg:hidden pointer-events-none">
                {{ $item['label'] }}
            </span>
        </a>
    @endforeach
</nav>
