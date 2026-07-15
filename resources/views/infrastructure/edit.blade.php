@extends('layouts.app')

@section('title', 'Edit Device')
@section('page-title', 'Edit Infrastructure Device')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">
    <div class="flex items-center gap-2">
        <a href="{{ route('infrastructure.index') }}" class="text-slate-500 hover:text-slate-700">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <h2 class="text-xl font-bold text-slate-800">Edit Device</h2>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <form action="{{ route('infrastructure.update', $device->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Name</label>
                    <input type="text" name="name" value="{{ $device->name }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Category</label>
                    <select name="category_id" id="category_id" data-tv-category="{{ $tvCategory?->id }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">Select category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ $device->category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Location</label>
                    <input type="text" name="location" value="{{ $device->location }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <div id="room-field">
                    <label class="block text-sm font-medium text-slate-700 mb-1">
                        Room <span id="room-required" class="text-rose-500 hidden">*</span>
                    </label>
                    <select name="room_id" id="room_id" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">Select room</option>
                        @foreach($rooms as $room)
                            <option value="{{ $room->id }}" {{ $device->room_id == $room->id ? 'selected' : '' }}>Room {{ $room->room_number }}</option>
                        @endforeach
                    </select>
                    <p id="room-help" class="text-xs text-slate-500 mt-1 hidden">A TV must be allocated to a room.</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="online" {{ $device->status === 'online' ? 'selected' : '' }}>Online</option>
                        <option value="offline" {{ $device->status === 'offline' ? 'selected' : '' }}>Offline</option>
                        <option value="maintenance" {{ $device->status === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        <option value="error" {{ $device->status === 'error' ? 'selected' : '' }}>Error</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">IP Address</label>
                    <input type="text" name="ip_address" value="{{ $device->ip_address }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">MAC Address</label>
                    <input type="text" name="mac_address" value="{{ $device->mac_address }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Serial Number</label>
                    <input type="text" name="serial_number" value="{{ $device->serial_number }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Configuration / Notes</label>
                <textarea name="config" rows="4" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">{{ $device->config }}</textarea>
            </div>
            <div class="flex justify-end gap-3">
                <a href="{{ route('infrastructure.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-sm font-medium transition">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg text-sm font-medium transition">Update Device</button>
            </div>
        </form>
    </div>
</div>

<script>
    (function () {
        const category = document.getElementById('category_id');
        const room = document.getElementById('room_id');
        const required = document.getElementById('room-required');
        const help = document.getElementById('room-help');
        const tvCategory = category.dataset.tvCategory;

        function toggleRoom() {
            const isTv = tvCategory && category.value === tvCategory;
            if (isTv) {
                room.required = true;
                required.classList.remove('hidden');
                help.classList.remove('hidden');
            } else {
                room.required = false;
                required.classList.add('hidden');
                help.classList.add('hidden');
            }
        }

        category.addEventListener('change', toggleRoom);
        toggleRoom();
    })();
</script>
@endsection
