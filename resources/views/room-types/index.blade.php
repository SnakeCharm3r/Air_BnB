@extends('layouts.app')

@section('title', 'Room Types')
@section('page-title', 'Room Types')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Room Type Management</h2>
            <p class="text-sm text-slate-500">Manage room types, pricing, and capacity</p>
        </div>
        <button onclick="toggleAddRoomTypeModal()" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-sm font-medium transition shadow-sm">
            + Add Room Type
        </button>
    </div>

    <!-- Room Types Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <table class="w-full">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Description</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Price</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Capacity</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Amenities</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-slate-600 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse($roomTypes as $type)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-medium text-slate-800">{{ $type->name }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-slate-600">{{ $type->description ?? '-' }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-semibold text-emerald-600">{{ format_money($type->base_price) }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-slate-600">{{ $type->capacity }} person(s)</span>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $amenities = json_decode($type->amenities, true) ?? [];
                            @endphp
                            @if(count($amenities) > 0)
                                <div class="flex flex-wrap gap-1">
                                    @foreach($amenities as $amenity)
                                        <span class="px-2 py-1 bg-slate-100 text-slate-600 text-xs rounded">{{ $amenity }}</span>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-sm text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <button onclick="editRoomType({{ $type->id }}, '{{ $type->name }}', '{{ $type->description ?? '' }}', '{{ $type->base_price }}', '{{ $type->currency ?? 'KSH' }}', {{ $type->capacity }}, @if($type->amenities) '{{ json_encode(json_decode($type->amenities, true)) }}' @else '[]' @endif)" class="text-slate-400 hover:text-blue-600 mr-3 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>
                            <form action="{{ route('room-types.destroy', $type->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this room type?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-slate-400 hover:text-rose-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                            </div>
                            <p class="text-sm font-medium text-slate-500">No room types found</p>
                            <p class="text-xs text-slate-400 mt-1">Click "Add Room Type" to create your first room type</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Add/Edit Room Type Modal -->
<div id="room-type-modal" class="fixed inset-0 z-50 hidden">
    <!-- Backdrop -->
    <div onclick="toggleAddRoomTypeModal()" class="absolute inset-0 bg-black/50 backdrop-blur-sm transition-opacity"></div>
    
    <!-- Modal Content -->
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-lg overflow-hidden transform transition-all">
            <!-- Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 bg-slate-50">
                <h2 id="room-type-modal-title" class="text-lg font-semibold text-slate-800">Add New Room Type</h2>
                <button onclick="toggleAddRoomTypeModal()" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-200 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <!-- Form -->
            <form id="room-type-form" action="{{ route('room-types.store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                @method('POST')
                
                <input type="hidden" id="room-type-id" name="id" value="">
                <input type="hidden" id="room-type-method" name="_method" value="POST">
                
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Room Type Name</label>
                    <input type="text" id="name" name="name" required 
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                        placeholder="e.g. Standard Single">
                </div>
                
                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-slate-700 mb-1">Description (optional)</label>
                    <textarea id="description" name="description" rows="2"
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                        placeholder="Brief description of the room type..."></textarea>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Price -->
                    <div>
                        <label for="base_price" class="block text-sm font-medium text-slate-700 mb-1">Base Price</label>
                        <input type="text" id="base_price" name="base_price" required 
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                            placeholder="e.g. 15000">
                    </div>
                    
                    <!-- Currency -->
                    <div>
                        <label for="currency" class="block text-sm font-medium text-slate-700 mb-1">Currency</label>
                        <select id="currency" name="currency" required
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                            <option value="TSH" selected>TSH (Tanzanian Shilling)</option>
                            <option value="KSH">KSH (Kenyan Shilling)</option>
                            <option value="UGX">UGX (Ugandan Shilling)</option>
                            <option value="RAND">RAND (South African Rand)</option>
                            <option value="USD">USD (US Dollar)</option>
                            <option value="EUR">EUR (Euro)</option>
                            <option value="GBP">GBP (British Pound)</option>
                        </select>
                    </div>
                    
                    <!-- Capacity -->
                    <div>
                        <label for="capacity" class="block text-sm font-medium text-slate-700 mb-1">Capacity</label>
                        <input type="number" id="capacity" name="capacity" required min="1"
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                            placeholder="e.g. 2">
                    </div>
                </div>
                
                <!-- Amenities -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Amenities</label>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="amenities[]" value="WiFi" class="w-4 h-4 text-amber-500 border-slate-300 rounded focus:ring-amber-500">
                            <span class="text-sm text-slate-600">WiFi</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="amenities[]" value="TV" class="w-4 h-4 text-amber-500 border-slate-300 rounded focus:ring-amber-500">
                            <span class="text-sm text-slate-600">TV</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="amenities[]" value="AC" class="w-4 h-4 text-amber-500 border-slate-300 rounded focus:ring-amber-500">
                            <span class="text-sm text-slate-600">AC</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="amenities[]" value="Mini Bar" class="w-4 h-4 text-amber-500 border-slate-300 rounded focus:ring-amber-500">
                            <span class="text-sm text-slate-600">Mini Bar</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="amenities[]" value="Balcony" class="w-4 h-4 text-amber-500 border-slate-300 rounded focus:ring-amber-500">
                            <span class="text-sm text-slate-600">Balcony</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="amenities[]" value="Parking" class="w-4 h-4 text-amber-500 border-slate-300 rounded focus:ring-amber-500">
                            <span class="text-sm text-slate-600">Parking</span>
                        </label>
                    </div>
                </div>
                
                <!-- Actions -->
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" onclick="toggleRoomTypeModal()" class="px-4 py-2 border border-slate-300 text-slate-600 rounded-lg text-sm font-medium hover:bg-slate-50 transition">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-sm font-medium transition shadow-sm">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleAddRoomTypeModal() {
    const modal = document.getElementById('room-type-modal');
    if (modal.classList.contains('hidden')) {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        // Reset form for add mode
        document.getElementById('room-type-form').reset();
        document.getElementById('room-type-form').action = '{{ route('room-types.store') }}';
        document.getElementById('room-type-modal-title').textContent = 'Add New Room Type';
        document.getElementById('room-type-method').value = 'POST';
    } else {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }
}

function editRoomType(id, name, description, price, currency, capacity, amenities) {
    const modal = document.getElementById('room-type-modal');
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    document.getElementById('room-type-form').action = '{{ route('room-types.update', ':id') }}'.replace(':id', id);
    document.getElementById('room-type-modal-title').textContent = 'Edit Room Type';
    document.getElementById('room-type-method').value = 'PUT';
    
    document.getElementById('name').value = name;
    document.getElementById('description').value = description;
    document.getElementById('base_price').value = price;
    document.getElementById('currency').value = currency;
    document.getElementById('capacity').value = capacity;
    
    // Clear and set amenities
    const amenityCheckboxes = document.querySelectorAll('input[name="amenities[]"]');
    amenityCheckboxes.forEach(cb => cb.checked = false);
    if (amenities && Array.isArray(amenities)) {
        amenities.forEach(amenity => {
            const checkbox = document.querySelector(`input[name="amenities[]"][value="${amenity}"]`);
            if (checkbox) checkbox.checked = true;
        });
    }
}

// Close modal on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modal = document.getElementById('room-type-modal');
        if (!modal.classList.contains('hidden')) {
            toggleAddRoomTypeModal();
        }
    }
});
</script>
@endsection
