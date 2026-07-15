@extends('layouts.app')

@section('title', 'Infrastructure')
@section('page-title', 'Infrastructure')

@section('content')
<div class="space-y-6">
    <!-- Header & Actions -->
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-bold text-slate-800">Hotel Infrastructure</h2>
        <div class="flex items-center gap-2">
            <a href="{{ route('infrastructure.categories.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-sm font-medium transition">
                Manage Categories
            </a>
            <form action="{{ route('infrastructure.sync-iptv') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="px-4 py-2 bg-indigo-500 hover:bg-indigo-600 text-white rounded-lg text-sm font-medium transition">
                    Sync IPTV Devices
                    @if($iptvCount !== null)
                        <span class="ml-1 text-xs bg-indigo-700 px-2 py-0.5 rounded-full">{{ $iptvCount }}</span>
                    @endif
                </button>
            </form>
            <a href="{{ route('infrastructure.create') }}" class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg text-sm font-medium transition">
                Add Device
            </a>
        </div>
    </div>

    <!-- Categories Filter -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('infrastructure.index') }}" class="px-3 py-1.5 rounded-lg text-sm font-medium transition {{ $selectedCategory ? 'bg-slate-100 text-slate-600 hover:bg-slate-200' : 'bg-emerald-500 text-white' }}">
                All
            </a>
            @foreach($categories as $category)
                <a href="{{ route('infrastructure.index', ['category' => $category->slug]) }}" class="px-3 py-1.5 rounded-lg text-sm font-medium transition {{ $selectedCategory === $category->slug ? 'bg-emerald-500 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    {{ $category->name }}
                </a>
            @endforeach
        </div>
    </div>

    <!-- Allocation Alerts -->
    @if($tvCategory && ($unallocatedTvs->count() || $roomsWithoutTv->count()))
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @if($unallocatedTvs->count())
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.054 0 1.502-1.002.814-1.78l-6.93-8.006a1.036 1.036 0 00-1.628 0l-6.93 8.006c-.688.778-.24 1.78.814 1.78z"/></svg>
                        <h3 class="font-semibold text-amber-800">Unallocated TVs</h3>
                    </div>
                    <p class="text-sm text-amber-700 mb-3">{{ $unallocatedTvs->count() }} TV(s) are not linked to a room.</p>
                    <ul class="space-y-1 text-sm">
                        @foreach($unallocatedTvs as $tv)
                            <li class="flex items-center justify-between">
                                <span>{{ $tv->name }}</span>
                                <a href="{{ route('infrastructure.edit', $tv->id) }}" class="text-amber-700 hover:underline font-medium">Assign room</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if($roomsWithoutTv->count())
                <div class="bg-rose-50 border border-rose-200 rounded-xl p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.054 0 1.502-1.002.814-1.78l-6.93-8.006a1.036 1.036 0 00-1.628 0l-6.93 8.006c-.688.778-.24 1.78.814 1.78z"/></svg>
                        <h3 class="font-semibold text-rose-800">Rooms Without TV</h3>
                    </div>
                    <p class="text-sm text-rose-700 mb-3">{{ $roomsWithoutTv->count() }} room(s) need a TV.</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($roomsWithoutTv as $room)
                            <span class="px-2 py-1 bg-white border border-rose-200 rounded text-sm text-rose-700">Room {{ $room->room_number }}</span>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @endif

    <!-- Status Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        @php
            $statusColors = [
                'online' => 'bg-emerald-100 text-emerald-700',
                'offline' => 'bg-rose-100 text-rose-700',
                'maintenance' => 'bg-amber-100 text-amber-700',
                'error' => 'bg-red-100 text-red-700',
            ];
        @endphp
        @foreach($statusCounts as $count)
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                <p class="text-sm text-slate-500 capitalize">{{ str_replace('_', ' ', $count->status) }}</p>
                <p class="text-2xl font-bold {{ $statusColors[$count->status] ?? 'text-slate-700' }}">{{ $count->count }}</p>
            </div>
        @endforeach
    </div>

    <!-- Devices Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-slate-800">Devices</h3>
            <span class="text-sm text-slate-500">{{ $devices->count() }} total</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Location</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">IP / MAC</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Source</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($devices as $device)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4">
                                <div class="font-medium text-slate-800">{{ $device->name }}</div>
                                @if($device->serial_number)
                                    <div class="text-xs text-slate-500">S/N: {{ $device->serial_number }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded text-xs font-medium bg-slate-100 text-slate-700">
                                    {{ $device->category_name ?? 'Uncategorized' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">
                                {{ $device->location ?? '—' }}
                                @if($device->room_number)
                                    <div class="text-xs text-slate-400">Room {{ $device->room_number }}</div>
                                @elseif($device->category_slug === 'tv')
                                    <div class="text-xs text-rose-500 font-medium">No room assigned</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">
                                <div>{{ $device->ip_address ?? '—' }}</div>
                                <div class="text-xs text-slate-400">{{ $device->mac_address ?? '—' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusColors = [
                                        'online' => 'bg-emerald-100 text-emerald-700',
                                        'offline' => 'bg-rose-100 text-rose-700',
                                        'maintenance' => 'bg-amber-100 text-amber-700',
                                        'error' => 'bg-red-100 text-red-700',
                                    ];
                                @endphp
                                <span class="px-2 py-1 rounded-full text-xs font-medium {{ $statusColors[$device->status] ?? 'bg-slate-100 text-slate-700' }}">
                                    {{ str_replace('_', ' ', ucfirst($device->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">
                                @if($device->source === 'iptv')
                                    <span class="px-2 py-1 rounded text-xs font-medium bg-indigo-100 text-indigo-700">IPTV</span>
                                @else
                                    <span class="px-2 py-1 rounded text-xs font-medium bg-slate-100 text-slate-700">Manual</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('infrastructure.show', $device->id) }}" class="text-slate-500 hover:text-slate-700" title="View">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    <a href="{{ route('infrastructure.edit', $device->id) }}" class="text-blue-500 hover:text-blue-700" title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.433-4.333A2.001 2.001 0 0119 8a2.001 2.001 0 01-.433 1.333L11 17l-4 1 1-4 8.433-8.667z"/></svg>
                                    </a>
                                    <form action="{{ route('infrastructure.toggle', $device->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-amber-500 hover:text-amber-700" title="Toggle Status">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                        </button>
                                    </form>
                                    <form action="{{ route('infrastructure.destroy', $device->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this device?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-500 hover:text-rose-700" title="Delete">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                                No devices found. Add a device or sync from IPTV.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
