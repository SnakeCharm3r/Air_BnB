@extends('layouts.app')

@section('title', $device->name)
@section('page-title', 'Device Details')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
            <a href="{{ route('infrastructure.index') }}" class="text-slate-500 hover:text-slate-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <h2 class="text-xl font-bold text-slate-800">{{ $device->name }}</h2>
        </div>
        <a href="{{ route('infrastructure.edit', $device->id) }}" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg text-sm font-medium transition">Edit Device</a>
    </div>

    @if($device->category_slug === 'tv' && !$device->room_number)
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 flex items-center gap-3">
            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.054 0 1.502-1.002.814-1.78l-6.93-8.006a1.036 1.036 0 00-1.628 0l-6.93 8.006c-.688.778-.24 1.78.814 1.78z"/></svg>
            <div>
                <p class="font-medium text-amber-800">This TV is not allocated to a room.</p>
                <p class="text-sm text-amber-700"><a href="{{ route('infrastructure.edit', $device->id) }}" class="underline font-medium">Edit the device</a> and assign a room.</p>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <h3 class="text-lg font-semibold text-slate-800 mb-4">General Information</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm text-slate-500">Category</span>
                    <span class="text-sm font-medium text-slate-800">{{ $device->category_name ?? 'Uncategorized' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-slate-500">Location</span>
                    <span class="text-sm font-medium text-slate-800">{{ $device->location ?? '—' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-slate-500">Room</span>
                    <span class="text-sm font-medium {{ $device->room_number ? 'text-slate-800' : 'text-rose-600' }}">
                        {{ $device->room_number ?? '—' }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-slate-500">Source</span>
                    <span class="text-sm font-medium text-slate-800 capitalize">{{ $device->source }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <h3 class="text-lg font-semibold text-slate-800 mb-4">Network & Status</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm text-slate-500">Status</span>
                    @php
                        $statusColors = [
                            'online' => 'text-emerald-600',
                            'offline' => 'text-rose-600',
                            'maintenance' => 'text-amber-600',
                            'error' => 'text-red-600',
                        ];
                    @endphp
                    <span class="text-sm font-medium {{ $statusColors[$device->status] ?? 'text-slate-600' }}">{{ str_replace('_', ' ', ucfirst($device->status)) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-slate-500">IP Address</span>
                    <span class="text-sm font-medium text-slate-800">{{ $device->ip_address ?? '—' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-slate-500">MAC Address</span>
                    <span class="text-sm font-medium text-slate-800">{{ $device->mac_address ?? '—' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-slate-500">Serial Number</span>
                    <span class="text-sm font-medium text-slate-800">{{ $device->serial_number ?? '—' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-slate-500">Last Seen</span>
                    <span class="text-sm font-medium text-slate-800">{{ $device->iptv_last_seen ?? '—' }}</span>
                </div>
            </div>
        </div>
    </div>

    @if($device->config)
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <h3 class="text-lg font-semibold text-slate-800 mb-4">Configuration / Notes</h3>
            <pre class="bg-slate-50 p-4 rounded-lg text-sm text-slate-700 overflow-x-auto">{{ $device->config }}</pre>
        </div>
    @endif

    @if(count($logs))
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <h3 class="text-lg font-semibold text-slate-800 mb-4">Recent Logs</h3>
            <div class="space-y-2">
                @foreach($logs as $log)
                    <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg">
                        <span class="text-sm text-slate-700">{{ $log->message ?? 'Log entry' }}</span>
                        <span class="text-xs text-slate-500">{{ $log->created_at }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
