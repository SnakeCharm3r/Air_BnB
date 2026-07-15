@extends('layouts.app')

@section('title', 'Edit Booking #' . $booking->booking_ref)
@section('page-title', 'Edit Booking')

@section('content')
<div class="space-y-6">
    <!-- Back Button -->
    <a href="{{ route('bookings.show', $booking->id) }}" class="inline-flex items-center gap-2 text-slate-600 hover:text-slate-800 transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Back to Booking
    </a>

    <!-- Edit Booking Form -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
        <div class="p-6">
            <h2 class="text-lg font-semibold text-slate-800 mb-6">Edit Booking #{{ $booking->booking_ref }}</h2>
            
            <form action="{{ route('bookings.update', $booking->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Guest Information -->
                    <div class="space-y-4">
                        <h3 class="text-sm font-semibold text-slate-700 uppercase tracking-wider border-b border-slate-200 pb-2">Guest Information</h3>
                        
                        <div>
                            <label for="guest_name" class="block text-sm font-medium text-slate-700 mb-1">Guest Name *</label>
                            <input type="text" id="guest_name" name="guest_name" required value="{{ $booking->guest_name }}"
                                class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                        </div>
                        
                        <div>
                            <label for="guest_email" class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                            <input type="email" id="guest_email" name="guest_email" value="{{ $booking->guest_email }}"
                                class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                        </div>
                        
                        <div>
                            <label for="guest_phone" class="block text-sm font-medium text-slate-700 mb-1">Phone Number</label>
                            <input type="tel" id="guest_phone" name="guest_phone" value="{{ $booking->guest_phone }}"
                                class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                        </div>
                    </div>
                    
                    <!-- Booking Details -->
                    <div class="space-y-4">
                        <h3 class="text-sm font-semibold text-slate-700 uppercase tracking-wider border-b border-slate-200 pb-2">Booking Details</h3>
                        
                        <div>
                            <label for="room_id" class="block text-sm font-medium text-slate-700 mb-1">Room *</label>
                            <select id="room_id" name="room_id" required
                                class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                                @foreach($rooms as $room)
                                    <option value="{{ $room->id }}" {{ $booking->room_id == $room->id ? 'selected' : '' }}>
                                        Room {{ $room->room_number }} - {{ $room->room_type_name ?? 'Standard' }} 
                                        ({{ format_money($room->price ?? 0) }}/night)
                                        @if($booking->room_id == $room->id)
                                            [CURRENT]
                                        @elseif($room->status === 'available')
                                            [AVAILABLE]
                                        @else
                                            [{{ strtoupper($room->status) }}]
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-slate-500">Current room marked with [CURRENT]. Available rooms show [AVAILABLE].</p>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="check_in_date" class="block text-sm font-medium text-slate-700 mb-1">Check In Date *</label>
                                <input type="date" id="check_in_date" name="check_in_date" required 
                                    value="{{ $booking->check_in_date }}"
                                    class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                            </div>
                            <div>
                                <label for="check_in_time" class="block text-sm font-medium text-slate-700 mb-1">Check In Time</label>
                                <input type="time" id="check_in_time" name="check_in_time" 
                                    value="{{ $booking->check_in_time ?? '14:00' }}"
                                    class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="check_out_date" class="block text-sm font-medium text-slate-700 mb-1">Check Out Date *</label>
                                <input type="date" id="check_out_date" name="check_out_date" required 
                                    value="{{ $booking->check_out_date }}"
                                    class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                            </div>
                            <div>
                                <label for="check_out_time" class="block text-sm font-medium text-slate-700 mb-1">Check Out Time</label>
                                <input type="time" id="check_out_time" name="check_out_time" 
                                    value="{{ $booking->check_out_time ?? '11:00' }}"
                                    class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="adults" class="block text-sm font-medium text-slate-700 mb-1">Adults</label>
                                <input type="number" id="adults" name="adults" min="1" value="{{ $booking->adults }}"
                                    class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                            </div>
                            <div>
                                <label for="children" class="block text-sm font-medium text-slate-700 mb-1">Children</label>
                                <input type="number" id="children" name="children" min="0" value="{{ $booking->children }}"
                                    class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Payment Information -->
                <div class="space-y-4 pt-4 border-t border-slate-200">
                    <h3 class="text-sm font-semibold text-slate-700 uppercase tracking-wider">Payment Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="payment_type" class="block text-sm font-medium text-slate-700 mb-1">Payment Method</label>
                            <select id="payment_type" name="payment_type"
                                class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                                <option value="cash" {{ ($booking->payment_type ?? '') === 'cash' ? 'selected' : '' }}>Cash Payment</option>
                                <option value="crdb" {{ ($booking->payment_type ?? '') === 'crdb' ? 'selected' : '' }}>CRDB Bank Transfer</option>
                            </select>
                        </div>
                        <div>
                            <label for="payment_reference" class="block text-sm font-medium text-slate-700 mb-1">Payment Reference</label>
                            <input type="text" id="payment_reference" name="payment_reference" 
                                value="{{ $booking->payment_reference ?? '' }}"
                                class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                                placeholder="e.g. CRDB-123456 or CASH-001">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="total_amount" class="block text-sm font-medium text-slate-700 mb-1">Total Amount *</label>
                            <input type="number" id="total_amount" name="total_amount" required min="0" step="0.01"
                                value="{{ $booking->total_amount }}"
                                class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                        </div>
                        <div>
                            <label for="retainer_paid" class="block text-sm font-medium text-slate-700 mb-1">Retainer/Deposit</label>
                            <input type="number" id="retainer_paid" name="retainer_paid" min="0" step="0.01"
                                value="{{ $booking->retainer_paid ?? 0 }}"
                                class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-500 mb-1">Current Balance</label>
                            <div class="px-3 py-2 bg-slate-100 rounded-lg text-sm font-medium {{ $booking->balance_due > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                                {{ format_money($booking->balance_due ?? 0) }}
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Status -->
                <div class="pt-4 border-t border-slate-200">
                    <label for="status" class="block text-sm font-medium text-slate-700 mb-1">Booking Status</label>
                    <select id="status" name="status"
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                        <option value="confirmed" {{ $booking->status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="checked_in" {{ $booking->status === 'checked_in' ? 'selected' : '' }}>Checked In</option>
                        <option value="checked_out" {{ $booking->status === 'checked_out' ? 'selected' : '' }}>Checked Out</option>
                        <option value="cancelled" {{ $booking->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                
                <!-- Special Requests -->
                <div class="pt-4 border-t border-slate-200">
                    <label for="special_requests" class="block text-sm font-medium text-slate-700 mb-1">Special Requests</label>
                    <textarea id="special_requests" name="special_requests" rows="3"
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                        placeholder="Any special requests or notes...">{{ $booking->special_requests }}</textarea>
                </div>
                
                <!-- Actions -->
                <div class="flex items-center justify-end gap-3 pt-4">
                    <a href="{{ route('bookings.show', $booking->id) }}" class="px-4 py-2 border border-slate-300 text-slate-600 rounded-lg text-sm font-medium hover:bg-slate-50 transition">
                        Cancel
                    </a>
                    <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-sm font-medium transition shadow-sm">
                        Update Booking
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
