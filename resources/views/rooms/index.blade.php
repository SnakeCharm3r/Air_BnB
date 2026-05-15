@extends('layouts.app')

@section('title', 'Rooms')
@section('page-title', 'Rooms')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Room Management</h2>
            <p class="text-sm text-slate-500">Manage rooms and their status</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('room-types.index') }}" class="px-4 py-2 border border-slate-300 text-slate-600 rounded-lg text-sm font-medium hover:bg-slate-50 transition">
                Manage Room Types
            </a>
            <button onclick="toggleAddRoomModal()" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-sm font-medium transition shadow-sm">
                + Add Room
            </button>
        </div>
    </div>

    <!-- Room Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
        @forelse($rooms as $room)
            @php
                $statusColors = [
                    'available' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-600', 'badge' => 'bg-emerald-100 text-emerald-700'],
                    'occupied' => ['bg' => 'bg-rose-100', 'text' => 'text-rose-600', 'badge' => 'bg-rose-100 text-rose-700'],
                    'booked' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-600', 'badge' => 'bg-blue-100 text-blue-700'],
                    'awaiting_cleaning' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-600', 'badge' => 'bg-amber-100 text-amber-700'],
                    'maintenance' => ['bg' => 'bg-slate-100', 'text' => 'text-slate-600', 'badge' => 'bg-slate-100 text-slate-700']
                ];
                $colors = $statusColors[$room->status] ?? $statusColors['available'];
            @endphp
            <div onclick="showRoomDetails({{ $room->id }}, '{{ $room->room_number }}', '{{ $room->type_name ?? 'Standard' }}', '{{ $room->status }}', '{{ $room->floor }}', '{{ $room->notes ?? '' }}')" class="bg-white rounded-xl border border-slate-200 p-4 text-center cursor-pointer hover:shadow-md transition">
                <div class="w-full h-20 {{ $colors['bg'] }} rounded-lg flex items-center justify-center mb-3">
                    <span class="{{ $colors['text'] }} font-bold text-lg">{{ $room->room_number }}</span>
                </div>
                <p class="text-xs text-slate-500">{{ $room->type_name ?? 'Standard' }}</p>
                <span class="inline-block mt-2 px-2 py-1 {{ $colors['badge'] }} text-xs rounded-full capitalize">{{ str_replace('_', ' ', $room->status) }}</span>
            </div>
        @empty
            <div class="col-span-full text-center py-12 text-slate-400">
                <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <p class="text-sm font-medium text-slate-500">No rooms found</p>
                <p class="text-xs text-slate-400 mt-1">Click "Add Room" to create your first room</p>
            </div>
        @endforelse
    </div>
</div>

<!-- Add Room Modal -->
<div id="add-room-modal" class="fixed inset-0 z-50 hidden">
    <!-- Backdrop -->
    <div onclick="toggleAddRoomModal()" class="absolute inset-0 bg-black/50 backdrop-blur-sm transition-opacity"></div>
    
    <!-- Modal Content -->
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-lg overflow-hidden transform transition-all">
            <!-- Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 bg-slate-50">
                <h2 class="text-lg font-semibold text-slate-800">Add New Room</h2>
                <button onclick="toggleAddRoomModal()" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-200 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <!-- Form -->
            <form action="{{ route('web.rooms.store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Room Number -->
                    <div>
                        <label for="room_number" class="block text-sm font-medium text-slate-700 mb-1">Room Number</label>
                        <input type="text" id="room_number" name="room_number" required 
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                            placeholder="e.g. 101">
                    </div>
                    
                    <!-- Floor -->
                    <div>
                        <label for="floor" class="block text-sm font-medium text-slate-700 mb-1">Floor</label>
                        <input type="number" id="floor" name="floor" required min="1"
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                            placeholder="e.g. 1">
                    </div>
                </div>
                
                <!-- Room Type -->
                <div>
                    <label for="room_type_id" class="block text-sm font-medium text-slate-700 mb-1">Room Type</label>
                    <select id="room_type_id" name="room_type_id" required
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                        <option value="">Select room type</option>
                        @foreach($roomTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->name }} - {{ $type->currency ?? 'KSH' }} {{ number_format($type->base_price) }} / {{ $type->capacity }} person(s)</option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-slate-700 mb-1">Status</label>
                    <select id="status" name="status" required
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                        <option value="available">Available</option>
                        <option value="booked">Booked</option>
                        <option value="occupied">Occupied</option>
                        <option value="awaiting_cleaning">Awaiting Cleaning</option>
                        <option value="maintenance">Under Maintenance</option>
                    </select>
                </div>
                
                <!-- Notes -->
                <div>
                    <label for="notes" class="block text-sm font-medium text-slate-700 mb-1">Notes (optional)</label>
                    <textarea id="notes" name="notes" rows="2"
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                        placeholder="Any special notes about this room..."></textarea>
                </div>
                
                <!-- Actions -->
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" onclick="toggleAddRoomModal()" class="px-4 py-2 border border-slate-300 text-slate-600 rounded-lg text-sm font-medium hover:bg-slate-50 transition">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-sm font-medium transition shadow-sm">
                        Add Room
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Room Details Modal -->
<div id="room-details-modal" class="fixed inset-0 z-50 hidden">
    <!-- Backdrop -->
    <div onclick="toggleRoomDetailsModal()" class="absolute inset-0 bg-black/50 backdrop-blur-sm transition-opacity"></div>
    
    <!-- Modal Content -->
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-lg overflow-hidden transform transition-all">
            <!-- Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 bg-slate-50">
                <h2 id="room-details-title" class="text-lg font-semibold text-slate-800">Room Details</h2>
                <button onclick="toggleRoomDetailsModal()" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-200 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <!-- Content -->
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1">Room Number</label>
                        <p id="detail-room-number" class="text-sm font-semibold text-slate-800"></p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1">Floor</label>
                        <p id="detail-floor" class="text-sm text-slate-800"></p>
                    </div>
                </div>
                
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">Room Type</label>
                    <p id="detail-type" class="text-sm text-slate-800"></p>
                </div>
                
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">Status</label>
                    <span id="detail-status" class="inline-block px-2 py-1 text-xs rounded-full"></span>
                </div>
                
                <!-- Status-specific information -->
                <div id="status-specific-info" class="bg-slate-50 rounded-lg p-4">
                    <!-- Dynamic content based on status -->
                </div>
                
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">Notes</label>
                    <p id="detail-notes" class="text-sm text-slate-600"></p>
                </div>
                
                <!-- More Info Button -->
                <a id="more-info-link" href="#" class="block w-full text-center px-4 py-2 border border-slate-300 text-slate-600 rounded-lg text-sm font-medium hover:bg-slate-50 transition">
                    More Info
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function toggleAddRoomModal() {
    const modal = document.getElementById('add-room-modal');
    if (modal.classList.contains('hidden')) {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    } else {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }
}

function toggleRoomDetailsModal() {
    const modal = document.getElementById('room-details-modal');
    if (modal.classList.contains('hidden')) {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    } else {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }
}

function showRoomDetails(id, roomNumber, type, status, floor, notes) {
    const modal = document.getElementById('room-details-modal');
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    document.getElementById('detail-room-number').textContent = roomNumber;
    document.getElementById('detail-floor').textContent = floor;
    document.getElementById('detail-type').textContent = type;
    document.getElementById('detail-notes').textContent = notes || 'No notes';
    
    const statusElement = document.getElementById('detail-status');
    const statusInfo = document.getElementById('status-specific-info');
    
    const statusColors = {
        'available': ['bg-emerald-100', 'text-emerald-700'],
        'occupied': ['bg-rose-100', 'text-rose-700'],
        'booked': ['bg-blue-100', 'text-blue-700'],
        'awaiting_cleaning': ['bg-amber-100', 'text-amber-700'],
        'maintenance': ['bg-slate-100', 'text-slate-700']
    };
    const colors = statusColors[status] || statusColors['available'];
    statusElement.className = `inline-block px-2 py-1 text-xs rounded-full ${colors[0]} ${colors[1]}`;
    statusElement.textContent = status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
    
    // Status-specific information
    let statusHtml = '';
    if (status === 'maintenance') {
        statusHtml = `
            <div class="space-y-2">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span class="text-sm text-slate-600">Expected completion: TBD</span>
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span class="text-sm text-slate-600">Assigned to: TBD</span>
                </div>
            </div>
        `;
    } else if (status === 'available') {
        statusHtml = `
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span class="text-sm text-emerald-600">Room is available for booking</span>
            </div>
        `;
    } else if (status === 'occupied') {
        statusHtml = `
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span class="text-sm text-rose-600">Currently occupied by a guest</span>
            </div>
        `;
    } else if (status === 'booked') {
        statusHtml = `
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span class="text-sm text-blue-600">Room is booked for future dates</span>
            </div>
        `;
    } else if (status === 'awaiting_cleaning') {
        statusHtml = `
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="text-sm text-amber-600">Awaiting cleaning before next use</span>
            </div>
        `;
    }
    statusInfo.innerHTML = statusHtml;
    
    // Update more info link
    document.getElementById('more-info-link').href = `/rooms/${id}`;
}

// Close modals on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const addModal = document.getElementById('add-room-modal');
        const detailsModal = document.getElementById('room-details-modal');
        
        if (!detailsModal.classList.contains('hidden')) {
            toggleRoomDetailsModal();
        } else if (!addModal.classList.contains('hidden')) {
            toggleAddRoomModal();
        }
    }
});
</script>
@endsection
