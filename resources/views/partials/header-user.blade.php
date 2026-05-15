<div class="relative" id="user-dropdown-container">
    <button onclick="toggleUserDropdown()" class="flex items-center gap-3 hover:bg-slate-100 rounded-lg p-1 pr-2 transition-colors">
        <div class="hidden md:block text-right">
            <p class="text-sm font-medium text-slate-800">{{ auth()->user()->name ?? 'User' }}</p>
            <p class="text-xs text-slate-500 capitalize">{{ auth()->user()->role ?? 'User' }}</p>
        </div>
        <div class="w-9 h-9 rounded-full bg-amber-500 flex items-center justify-center">
            <span class="text-white text-sm font-bold">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</span>
        </div>
        @include('components.icons.chevron-down', ['class' => 'w-4 h-4 text-slate-400'])
    </button>

    {{-- Dropdown Menu --}}
    <div id="user-dropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-slate-200 py-1 z-50">
        <a href="{{ route('profile') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 transition-colors">
            Your profile
        </a>
        <div class="border-t border-slate-100 my-1"></div>
        <form method="POST" action="{{ route('logout') }}" class="block">
            @csrf
            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-rose-600 hover:bg-rose-50 transition-colors">
                Sign out
            </button>
        </form>
    </div>
</div>

<script>
function toggleUserDropdown() {
    const dropdown = document.getElementById('user-dropdown');
    dropdown.classList.toggle('hidden');
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const container = document.getElementById('user-dropdown-container');
    const dropdown = document.getElementById('user-dropdown');
    if (container && !container.contains(event.target)) {
        dropdown.classList.add('hidden');
    }
});
</script>
