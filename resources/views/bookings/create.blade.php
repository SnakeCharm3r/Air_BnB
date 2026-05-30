@extends('layouts.app')

@section('title', 'New Booking')
@section('page-title', 'Create Booking')

@section('content')
<div class="space-y-6">
    <!-- Back Button -->
    <a href="{{ route('bookings') }}" class="inline-flex items-center gap-2 text-slate-600 hover:text-slate-800 transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Back to Bookings
    </a>

    <!-- Create Booking Form -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
        <div class="p-6">
            <h2 class="text-lg font-semibold text-slate-800 mb-6">Create New Booking</h2>
            
            <form action="{{ route('bookings.store') }}" method="POST" class="space-y-6">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Guest Information -->
                    <div class="space-y-4">
                        <h3 class="text-sm font-semibold text-slate-700 uppercase tracking-wider border-b border-slate-200 pb-2">Guest Information</h3>
                        
                        <div>
                            <label for="guest_name" class="block text-sm font-medium text-slate-700 mb-1">Guest Name *</label>
                            <input type="text" id="guest_name" name="guest_name" required 
                                class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                                placeholder="e.g. John Doe">
                        </div>
                        
                        <div>
                            <label for="guest_email" class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                            <input type="email" id="guest_email" name="guest_email" 
                                class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                                placeholder="e.g. guest@example.com">
                        </div>
                        
                        <div>
                            <label for="guest_phone" class="block text-sm font-medium text-slate-700 mb-1">Phone Number</label>
                            <input type="tel" id="guest_phone" name="guest_phone" 
                                class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                                placeholder="e.g. +254 712 345 678">
                        </div>
                    </div>
                    
                    <!-- Booking Details -->
                    <div class="space-y-4">
                        <h3 class="text-sm font-semibold text-slate-700 uppercase tracking-wider border-b border-slate-200 pb-2">Booking Details</h3>
                        
                        <div>
                            <label for="room_id" class="block text-sm font-medium text-slate-700 mb-1">
                                Available Room * 
                                <span class="text-xs text-emerald-600 font-normal">({{ count($rooms) }} available)</span>
                            </label>
                            <select id="room_id" name="room_id" required
                                class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                                <option value="">Select an available room</option>
                                @forelse($rooms as $room)
                                    <option value="{{ $room->id }}">
                                        Room {{ $room->room_number }} - {{ $room->room_type_name ?? 'Standard' }} 
                                        (${{ number_format($room->price ?? 0, 2) }}/night)
                                    </option>
                                @empty
                                    <option value="" disabled>No available rooms</option>
                                @endforelse
                            </select>
                            @if(count($rooms) === 0)
                                <p class="mt-1 text-xs text-rose-600">No rooms currently available. Please check room availability or free up rooms first.</p>
                            @endif
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="check_in_date" class="block text-sm font-medium text-slate-700 mb-1">Check In Date *</label>
                                <input type="date" id="check_in_date" name="check_in_date" required 
                                    class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                            </div>
                            <div>
                                <label for="check_in_time" class="block text-sm font-medium text-slate-700 mb-1">Check In Time</label>
                                <input type="time" id="check_in_time" name="check_in_time" value="14:00"
                                    class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                                <p class="text-xs text-slate-400 mt-1">Default: 2:00 PM</p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="check_out_date" class="block text-sm font-medium text-slate-700 mb-1">Check Out Date *</label>
                                <input type="date" id="check_out_date" name="check_out_date" required 
                                    class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                            </div>
                            <div>
                                <label for="check_out_time" class="block text-sm font-medium text-slate-700 mb-1">Check Out Time</label>
                                <input type="time" id="check_out_time" name="check_out_time" value="11:00"
                                    class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                                <p class="text-xs text-slate-400 mt-1">Default: 11:00 AM</p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="adults" class="block text-sm font-medium text-slate-700 mb-1">Adults</label>
                                <input type="number" id="adults" name="adults" min="1" value="1"
                                    class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                            </div>
                            <div>
                                <label for="children" class="block text-sm font-medium text-slate-700 mb-1">Children</label>
                                <input type="number" id="children" name="children" min="0" value="0"
                                    class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Payment Information -->
                <div class="space-y-4 pt-4 border-t border-slate-200">
                    <h3 class="text-sm font-semibold text-slate-700 uppercase tracking-wider">Payment Information *</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="payment_type" class="block text-sm font-medium text-slate-700 mb-1">Payment Method *</label>
                            <select id="payment_type" name="payment_type" required
                                class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                                <option value="">Select payment method</option>
                                <option value="cash">Cash Payment</option>
                                <option value="crdb">CRDB Bank Transfer</option>
                            </select>
                        </div>
                        <div>
                            <label for="payment_reference" class="block text-sm font-medium text-slate-700 mb-1">Payment Reference</label>
                            <input type="text" id="payment_reference" name="payment_reference" 
                                class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                                placeholder="e.g. CRDB-123456 or CASH-001">
                            <p class="text-xs text-slate-400 mt-1">Required for CRDB payments. Optional for cash.</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="total_amount" class="block text-sm font-medium text-slate-700 mb-1">Total Amount *</label>
                            <input type="number" id="total_amount" name="total_amount" required min="0" step="0.01"
                                class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                                placeholder="0.00">
                        </div>
                        <div>
                            <label for="retainer_paid" class="block text-sm font-medium text-slate-700 mb-1">Amount Paid Now</label>
                            <input type="number" id="retainer_paid" name="retainer_paid" min="0" step="0.01" value="0"
                                class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                                placeholder="0.00">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-500 mb-1">Balance Due</label>
                            <div id="balance_display" class="px-3 py-2 bg-slate-100 rounded-lg text-sm font-medium text-slate-700">
                                Calculated automatically
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Special Requests -->
                <div class="pt-4 border-t border-slate-200">
                    <label for="special_requests" class="block text-sm font-medium text-slate-700 mb-1">Special Requests</label>
                    <textarea id="special_requests" name="special_requests" rows="3"
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                        placeholder="Any special requests or notes..."></textarea>
                </div>
                
                <!-- Actions -->
                <div class="flex items-center justify-end gap-3 pt-4">
                    <a href="{{ route('bookings') }}" class="px-4 py-2 border border-slate-300 text-slate-600 rounded-lg text-sm font-medium hover:bg-slate-50 transition">
                        Cancel
                    </a>
                    <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-sm font-medium transition shadow-sm">
                        Create Booking
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Calculate balance automatically
    document.getElementById('total_amount').addEventListener('input', calculateBalance);
    document.getElementById('retainer_paid').addEventListener('input', calculateBalance);
    
    function calculateBalance() {
        const total = parseFloat(document.getElementById('total_amount').value) || 0;
        const retainer = parseFloat(document.getElementById('retainer_paid').value) || 0;
        const balance = total - retainer;
        document.getElementById('balance_display').textContent = '$' + balance.toFixed(2);
    }
</script>
@endsection
