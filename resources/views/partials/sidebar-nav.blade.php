@php
$user = auth()->user();
$navItems = [];

$navItems[] = ['route' => 'dashboard', 'icon' => 'dashboard', 'label' => 'Dashboard'];
$navItems[] = ['route' => 'web.rooms', 'icon' => 'rooms', 'label' => 'Rooms'];

if ($user->isReceptionist()) {
    $navItems[] = ['route' => 'bookings', 'icon' => 'bookings', 'label' => 'Bookings'];
}

if ($user->isReceptionist()) {
    $accountingChildren = [];
    $accountingChildren[] = ['route' => 'accounting.index', 'label' => 'Accounting Home'];
    $accountingChildren[] = ['route' => 'billing.index', 'label' => 'Billing'];
    $accountingChildren[] = ['route' => 'receipts.index', 'label' => 'Receipts'];
    $accountingChildren[] = ['route' => 'invoices.index', 'label' => 'Invoices'];
    $accountingChildren[] = ['route' => 'payments.index', 'label' => 'Payments'];
    $accountingChildren[] = ['route' => 'folios.index', 'label' => 'Folios'];

    $navItems[] = [
        'group' => 'accounting',
        'icon' => 'accounting',
        'label' => 'Accounting',
        'children' => $accountingChildren,
    ];
}

if ($user->isReceptionist() || $user->isChef()) {
    $navItems[] = ['route' => 'inventory.index', 'icon' => 'inventory', 'label' => 'Inventory'];
    $navItems[] = ['route' => 'menu.index', 'icon' => 'menu', 'label' => 'Menu'];
    $navItems[] = ['route' => 'kitchen-orders.index', 'icon' => 'menu', 'label' => 'Kitchen Orders'];
}

if ($user->isManager()) {
    $navItems[] = ['route' => 'staff.index', 'icon' => 'staff', 'label' => 'Staff'];
    $navItems[] = ['route' => 'costs.index', 'icon' => 'costs', 'label' => 'Costs'];
    $navItems[] = ['route' => 'reports.index', 'icon' => 'reports', 'label' => 'Reports'];
    $navItems[] = ['route' => 'maintenance.index', 'icon' => 'maintenance', 'label' => 'Maintenance'];
    $navItems[] = ['route' => 'infrastructure.index', 'icon' => 'infrastructure', 'label' => 'Infrastructure'];
    $navItems[] = ['route' => 'users.index', 'icon' => 'users', 'label' => 'User Management'];
}

if ($user->isAdmin()) {
    $navItems[] = ['route' => 'permissions.index', 'icon' => 'users', 'label' => 'Roles & Permissions'];
}

$navItems[] = ['route' => 'tasks.index', 'icon' => 'tasks', 'label' => 'My Tasks'];

$isAccountingActive = request()->routeIs('accounting.index', 'billing.*', 'receipts.*', 'invoices.*', 'payments.*', 'folios.*', 'charges.*');
@endphp

<nav class="flex-1 py-4 px-3 space-y-1 sidebar-collapsed:px-2 sidebar-collapsed:overflow-hidden overflow-y-auto">
    @foreach($navItems as $item)
        @if(isset($item['group']))
            <div class="sidebar-group" data-group="{{ $item['group'] }}">
                <button type="button"
                        class="sidebar-group-toggle group w-full flex items-center justify-between gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all {{ $isAccountingActive ? 'bg-amber-500 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }} sidebar-collapsed:justify-center sidebar-collapsed:px-2 relative z-10"
                        title="{{ $item['label'] }}">
                    <div class="flex items-center gap-3 sidebar-collapsed:gap-0">
                        @include('components.icons.' . $item['icon'], ['class' => 'w-5 h-5 flex-shrink-0'])
                        <span class="truncate sidebar-text transition-all duration-300">{{ $item['label'] }}</span>
                    </div>
                    <svg class="sidebar-chevron w-4 h-4 transition-transform duration-200 sidebar-collapsed:hidden {{ $isAccountingActive ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="sidebar-group-menu pl-4 mt-1 space-y-1 sidebar-collapsed:hidden {{ $isAccountingActive ? '' : 'hidden' }}">
                    @foreach($item['children'] as $child)
                        @php $isChildActive = request()->routeIs($child['route']); @endphp
                        <a href="{{ route($child['route']) }}"
                           class="group flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all {{ $isChildActive ? 'bg-amber-500/80 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                            <span class="truncate">{{ $child['label'] }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @else
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
        @endif
    @endforeach
</nav>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.sidebar-group-toggle').forEach(function (toggle) {
            toggle.addEventListener('click', function () {
                const group = this.closest('.sidebar-group');
                const menu = group.querySelector('.sidebar-group-menu');
                const chevron = this.querySelector('.sidebar-chevron');
                menu.classList.toggle('hidden');
                chevron.classList.toggle('rotate-180');
            });
        });
    });
</script>
