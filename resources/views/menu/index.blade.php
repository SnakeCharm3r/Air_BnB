@extends('layouts.app')

@section('title', 'Menu Management')
@section('page-title', 'Menu')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Menu Management</h2>
            <p class="text-sm text-slate-500">IPTV menu synced with inventory and availability rules</p>
        </div>
        @if(auth()->user()->isChef())
            <div class="flex gap-3">
                <form action="{{ route('menu.sync') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg text-sm font-medium transition shadow-sm">
                        <i class="fas fa-sync-alt mr-2"></i>Sync from IPTV
                    </button>
                </form>
                <a href="{{ route('menu.create') }}" class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg text-sm font-medium transition shadow-sm">
                    <i class="fas fa-plus mr-2"></i>Add Menu Item
                </a>
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-4 gap-6 items-start">
        {{-- Menu Items Table --}}
        <div class="xl:col-span-3 bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100">
                <h3 class="font-semibold text-slate-800">Menu Items</h3>
                <p class="text-sm text-slate-500">Current availability is calculated from item schedule, kitchen hours, and chef duty.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Item</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Category</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Inventory Link</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Available Window</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Status Now</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($menuItems as $item)
                            @php
                                $availableNow = $item->isAvailableNow();
                                $window = $item->derivedServingWindow();
                                $from = $window['from'] !== '00:00:00' ? date('H:i', strtotime($window['from'])) : '-';
                                $until = $window['until'] !== '00:00:00' ? date('H:i', strtotime($window['until'])) : '-';
                            @endphp
                            <tr class="hover:bg-slate-50">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-slate-800">{{ $item->name }}</div>
                                    <div class="text-xs text-slate-500">{{ Str::limit($item->description, 40) }}</div>
                                </td>
                                <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($item->categories ?? [] as $cat)
                                        <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-slate-100 text-slate-600 capitalize">{{ $cat }}</span>
                                    @endforeach
                                </div>
                            </td>
                                <td class="px-6 py-4 text-sm text-slate-600">{{ $item->inventoryItem?->name ?? 'Not linked' }}</td>
                                <td class="px-6 py-4 text-sm font-medium text-slate-800">{{ format_money($item->price) }}</td>
                                <td class="px-6 py-4 text-sm text-slate-600">{{ $from }} - {{ $until }}</td>
                                <td class="px-6 py-4">
                                    @if($availableNow)
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-emerald-100 text-emerald-700">Available</span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-rose-100 text-rose-700">Unavailable</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if(auth()->user()->isChef())
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="{{ route('menu.edit', $item->id) }}" class="p-1.5 text-slate-400 hover:text-emerald-600 transition" title="Edit">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </a>
                                            <form action="{{ route('menu.destroy', $item->id) }}" method="POST" class="inline" onsubmit="return confirm('Remove this menu item?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 transition" title="Remove">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-xs text-slate-400">View only</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-slate-400">No menu items found. Sync from IPTV or add one manually.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Kitchen Hours Card --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <h3 class="text-lg font-semibold text-slate-800 mb-4">Kitchen Hours</h3>
            <form action="{{ route('menu.kitchen-hours') }}" method="POST" class="space-y-4">
                @csrf
                @php
                    $open = $kitchenHours && $kitchenHours->open_time ? date('H:i', strtotime($kitchenHours->open_time)) : '07:00';
                    $close = $kitchenHours && $kitchenHours->close_time ? date('H:i', strtotime($kitchenHours->close_time)) : '22:30';
                    $closed = $kitchenHours && $kitchenHours->is_closed;
                @endphp
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Kitchen opens at</label>
                    <input type="time" name="open_time" value="{{ $open }}" required class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Kitchen closes at</label>
                    <input type="time" name="close_time" value="{{ $close }}" required class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-sm">
                </div>
                <label class="flex items-center gap-2 text-sm">
                    <input type="checkbox" name="is_closed" value="1" {{ $closed ? 'checked' : '' }} class="rounded text-amber-500 focus:ring-amber-500">
                    <span class="text-slate-700">Kitchen closed</span>
                </label>
                <button type="submit" class="w-full px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-sm font-medium transition">Save Kitchen Hours</button>
            </form>
        </div>
    </div>
</div>
@endsection
