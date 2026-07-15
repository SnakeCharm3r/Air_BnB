@extends('layouts.app')

@section('title', 'Room Details - ' . $room->room_number)
@section('page-title', 'Room Details')

@section('content')
<div class="space-y-6">
    <!-- Back Button -->
    <a href="{{ route('web.rooms') }}" class="inline-flex items-center gap-2 text-slate-600 hover:text-slate-800 transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Back to Rooms
    </a>

    <!-- Room Details Card -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
        <div class="p-6">
            <div class="flex items-start justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">Room {{ $room->room_number }}</h1>
                    <p class="text-slate-500 mt-1">{{ $room->type_name }} - Floor {{ $room->floor }}</p>
                </div>
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
                <span class="inline-block px-3 py-1 {{ $colors['badge'] }} text-sm font-medium rounded-full capitalize">
                    {{ str_replace('_', ' ', $room->status) }}
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Room Information -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-slate-800 border-b border-slate-200 pb-2">Room Information</h3>
                    
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-medium text-slate-500 mb-1">Room Number</label>
                            <p class="text-sm text-slate-800">{{ $room->room_number }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-xs font-medium text-slate-500 mb-1">Floor</label>
                            <p class="text-sm text-slate-800">{{ $room->floor }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-xs font-medium text-slate-500 mb-1">Room Type</label>
                            <p class="text-sm text-slate-800">{{ $room->type_name }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-xs font-medium text-slate-500 mb-1">Status</label>
                            <span class="inline-block px-2 py-1 {{ $colors['badge'] }} text-xs rounded-full capitalize">
                                {{ str_replace('_', ' ', $room->status) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Room Type Details -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-slate-800 border-b border-slate-200 pb-2">Room Type Details</h3>
                    
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-medium text-slate-500 mb-1">Base Price</label>
                            <p class="text-sm font-semibold text-emerald-600">{{ format_money($room->base_price) }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-xs font-medium text-slate-500 mb-1">Capacity</label>
                            <p class="text-sm text-slate-800">{{ $room->capacity }} person(s)</p>
                        </div>
                        
                        <div>
                            <label class="block text-xs font-medium text-slate-500 mb-1">Description</label>
                            <p class="text-sm text-slate-600">{{ $room->type_description ?? 'No description' }}</p>
                        </div>
                        
                        @if($room->amenities)
                        <div>
                            <label class="block text-xs font-medium text-slate-500 mb-1">Amenities</label>
                            @php
                                $amenities = json_decode($room->amenities, true) ?? [];
                            @endphp
                            <div class="flex flex-wrap gap-2">
                                @foreach($amenities as $amenity)
                                    <span class="px-2 py-1 bg-slate-100 text-slate-600 text-xs rounded">{{ $amenity }}</span>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div class="mt-6">
                <label class="block text-xs font-medium text-slate-500 mb-1">Notes</label>
                <p class="text-sm text-slate-600 bg-slate-50 p-3 rounded-lg">{{ $room->notes ?? 'No notes' }}</p>
            </div>

            <!-- Status-specific Information -->
            <div class="mt-6 bg-slate-50 rounded-lg p-4">
                <h3 class="text-lg font-semibold text-slate-800 mb-3">Status Information</h3>
                @if($room->status === 'maintenance')
                    <div class="space-y-2">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span class="text-sm text-slate-600">Expected completion: TBD</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <span class="text-sm text-slate-600">Assigned to: TBD</span>
                        </div>
                    </div>
                @elseif($room->status === 'available')
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span class="text-sm text-emerald-600">Room is available for booking</span>
                    </div>
                @elseif($room->status === 'occupied')
                    <div class="space-y-2">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <span class="text-sm text-rose-600">Currently occupied by a guest</span>
                        </div>
                        @if($currentBooking)
                            <div class="text-sm text-slate-600">
                                <p>Guest: {{ $currentBooking->guest_name ?? 'N/A' }}</p>
                                <p>Check-in: {{ $currentBooking->check_in_date ?? 'N/A' }}</p>
                                <p>Check-out: {{ $currentBooking->check_out_date ?? 'N/A' }}</p>
                            </div>
                        @endif
                    </div>
                @elseif($room->status === 'booked')
                    <div class="space-y-2">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span class="text-sm text-blue-600">Room is booked for future dates</span>
                        </div>
                        @if($currentBooking)
                            <div class="text-sm text-slate-600">
                                <p>Guest: {{ $currentBooking->guest_name ?? 'N/A' }}</p>
                                <p>Check-in: {{ $currentBooking->check_in_date ?? 'N/A' }}</p>
                                <p>Check-out: {{ $currentBooking->check_out_date ?? 'N/A' }}</p>
                            </div>
                        @endif
                    </div>
                @elseif($room->status === 'awaiting_cleaning')
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-sm text-amber-600">Awaiting cleaning before next use</span>
                    </div>
                @endif
            </div>

            <!-- Actions -->
            @canany(['rooms.edit'])
            <div class="mt-6 flex items-center gap-3 pt-6 border-t border-slate-200">
                @can('rooms.edit')
                <button onclick="toggleEditRoomModal()" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-sm font-medium transition shadow-sm">
                    Edit Room
                </button>
                <button onclick="toggleStatusModal()" class="px-4 py-2 border border-slate-300 text-slate-600 rounded-lg text-sm font-medium hover:bg-slate-50 transition">
                    Change Status
                </button>
                @endcan
            </div>
            @endcanany
        </div>
    </div>
</div>

<!-- Edit Room Modal -->
<div id="edit-room-modal" class="fixed inset-0 z-50 hidden">
    <!-- Backdrop -->
    <div onclick="toggleEditRoomModal()" class="absolute inset-0 bg-black/50 backdrop-blur-sm transition-opacity"></div>
    
    <!-- Modal Content -->
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-lg overflow-hidden transform transition-all">
            <!-- Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 bg-slate-50">
                <h2 class="text-lg font-semibold text-slate-800">Edit Room</h2>
                <button onclick="toggleEditRoomModal()" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-200 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <!-- Form -->
            <form action="{{ route('web.rooms.update', $room->id) }}" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Room Number -->
                    <div>
                        <label for="edit_room_number" class="block text-sm font-medium text-slate-700 mb-1">Room Number</label>
                        <input type="text" id="edit_room_number" name="room_number" required value="{{ $room->room_number }}"
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                    </div>
                    
                    <!-- Floor -->
                    <div>
                        <label for="edit_floor" class="block text-sm font-medium text-slate-700 mb-1">Floor</label>
                        <input type="number" id="edit_floor" name="floor" required min="1" value="{{ $room->floor }}"
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                    </div>
                </div>
                
                <!-- Room Type -->
                <div>
                    <label for="edit_room_type_id" class="block text-sm font-medium text-slate-700 mb-1">Room Type</label>
                    <select id="edit_room_type_id" name="room_type_id" required
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                        @php $roomTypes = DB::table('room_types')->get(); @endphp
                        @foreach($roomTypes as $type)
                            <option value="{{ $type->id }}" {{ $type->id == $room->room_type_id ? 'selected' : '' }}>{{ $type->name }} - {{ format_money($type->base_price) }}</option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Notes -->
                <div>
                    <label for="edit_notes" class="block text-sm font-medium text-slate-700 mb-1">Notes (optional)</label>
                    <textarea id="edit_notes" name="notes" rows="2"
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">{{ $room->notes ?? '' }}</textarea>
                </div>
                
                <!-- Actions -->
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" onclick="toggleEditRoomModal()" class="px-4 py-2 border border-slate-300 text-slate-600 rounded-lg text-sm font-medium hover:bg-slate-50 transition">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-sm font-medium transition shadow-sm">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Change Status Modal -->
<div id="status-modal" class="fixed inset-0 z-50 hidden">
    <!-- Backdrop -->
    <div onclick="toggleStatusModal()" class="absolute inset-0 bg-black/50 backdrop-blur-sm transition-opacity"></div>
    
    <!-- Modal Content -->
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md overflow-hidden transform transition-all">
            <!-- Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 bg-slate-50">
                <h2 class="text-lg font-semibold text-slate-800">Change Room Status</h2>
                <button onclick="toggleStatusModal()" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-200 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <!-- Form -->
            <form action="{{ route('web.rooms.update', $room->id) }}" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                
                <input type="hidden" name="room_number" value="{{ $room->room_number }}">
                <input type="hidden" name="room_type_id" value="{{ $room->room_type_id }}">
                <input type="hidden" name="floor" value="{{ $room->floor }}">
                <input type="hidden" name="notes" value="{{ $room->notes ?? '' }}">
                
                <!-- Status -->
                <div>
                    <label for="new_status" class="block text-sm font-medium text-slate-700 mb-1">New Status</label>
                    <select id="new_status" name="status" required
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                        <option value="available" {{ $room->status === 'available' ? 'selected' : '' }}>Available</option>
                        <option value="booked" {{ $room->status === 'booked' ? 'selected' : '' }}>Booked</option>
                        <option value="occupied" {{ $room->status === 'occupied' ? 'selected' : '' }}>Occupied</option>
                        <option value="awaiting_cleaning" {{ $room->status === 'awaiting_cleaning' ? 'selected' : '' }}>Awaiting Cleaning</option>
                        <option value="maintenance" {{ $room->status === 'maintenance' ? 'selected' : '' }}>Under Maintenance</option>
                    </select>
                </div>
                
                <!-- Actions -->
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" onclick="toggleStatusModal()" class="px-4 py-2 border border-slate-300 text-slate-600 rounded-lg text-sm font-medium hover:bg-slate-50 transition">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-sm font-medium transition shadow-sm">
                        Update Status
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleEditRoomModal() {
    const modal = document.getElementById('edit-room-modal');
    if (modal.classList.contains('hidden')) {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    } else {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }
}

function toggleStatusModal() {
    const modal = document.getElementById('status-modal');
    if (modal.classList.contains('hidden')) {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    } else {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }
}

// Close modals on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const editModal = document.getElementById('edit-room-modal');
        const statusModal = document.getElementById('status-modal');
        
        if (!statusModal.classList.contains('hidden')) {
            toggleStatusModal();
        } else if (!editModal.classList.contains('hidden')) {
            toggleEditRoomModal();
        }
    }
});
</script>
@endsection
