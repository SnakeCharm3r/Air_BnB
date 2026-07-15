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
                            <label for="guest_id" class="block text-sm font-medium text-slate-700 mb-1">Select Existing Guest</label>
                            <select id="guest_id" name="guest_id" 
                                class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                                onchange="toggleGuestFields()">
                                <option value="">Create new guest</option>
                                @php $guests = DB::table('guests')->orderBy('first_name')->get(); @endphp
                                @foreach($guests as $guest)
                                    <option value="{{ $guest->id }}">{{ $guest->first_name }} {{ $guest->last_name }} ({{ $guest->vip_level }})</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div id="new_guest_fields">
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
                    </div>
                    
                    <!-- Booking Details -->
                    <div class="space-y-4">
                        <h3 class="text-sm font-semibold text-slate-700 uppercase tracking-wider border-b border-slate-200 pb-2">Booking Details</h3>
                        
                        <div>
                            <label for="reservation_type" class="block text-sm font-medium text-slate-700 mb-1">Reservation Type *</label>
                            <select id="reservation_type" name="reservation_type" required
                                class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                                onchange="toggleCorporateFields()">
                                <option value="">Select reservation type</option>
                                <option value="walk_in">Walk-in</option>
                                <option value="advance">Advance Reservation</option>
                                <option value="group">Group Booking</option>
                                <option value="corporate">Corporate Booking</option>
                                <option value="vip">VIP Guest</option>
                            </select>
                        </div>

                        <div id="expiry_date_field" class="hidden">
                            <label for="expiry_date" class="block text-sm font-medium text-slate-700 mb-1">Reservation Expiry Date</label>
                            <input type="date" id="expiry_date" name="expiry_date" 
                                class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                            <p class="text-xs text-slate-400 mt-1">Required for advance reservations</p>
                        </div>

                        <div id="corporate_fields" class="hidden space-y-3">
                            <div>
                                <label for="company_name" class="block text-sm font-medium text-slate-700 mb-1">Company Name</label>
                                <input type="text" id="company_name" name="company_name" 
                                    class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                                    placeholder="e.g. Acme Corporation">
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label for="tax_id" class="block text-sm font-medium text-slate-700 mb-1">Tax ID</label>
                                    <input type="text" id="tax_id" name="tax_id" 
                                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                                        placeholder="TIN">
                                </div>
                                <div>
                                    <label for="credit_terms_days" class="block text-sm font-medium text-slate-700 mb-1">Credit Terms</label>
                                    <select id="credit_terms_days" name="credit_terms_days" 
                                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                                        <option value="">Select terms</option>
                                        <option value="7">7 Days</option>
                                        <option value="14">14 Days</option>
                                        <option value="30">30 Days</option>
                                        <option value="60">60 Days</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">
                                Select Rooms * 
                                <span class="text-xs text-emerald-600 font-normal">({{ count($rooms) }} available)</span>
                            </label>
                            <div class="space-y-2 max-h-48 overflow-y-auto border border-slate-200 rounded-lg p-3">
                                @forelse($rooms as $room)
                                    <label class="flex items-center gap-3 p-2 hover:bg-slate-50 rounded cursor-pointer">
                                        <input type="checkbox" name="room_ids[]" value="{{ $room->id }}" 
                                            class="w-4 h-4 text-amber-500 rounded focus:ring-amber-500">
                                        <span class="text-sm">
                                            Room {{ $room->room_number }} - {{ $room->room_type_name ?? 'Standard' }} 
                                            ({{ format_money($room->price ?? 0) }}/night)
                                        </span>
                                    </label>
                                @empty
                                    <p class="text-sm text-rose-600">No rooms currently available.</p>
                                @endforelse
                            </div>
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
                                <label for="adults" class="block text-sm font-medium text-slate-700 mb-1">Adults *</label>
                                <input type="number" id="adults" name="adults" min="1" value="1" required
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
                    <h3 class="text-sm font-semibold text-slate-700 uppercase tracking-wider">Payment Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="payment_method" class="block text-sm font-medium text-slate-700 mb-1">Payment Method</label>
                            <select id="payment_method" name="payment_method" 
                                class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                                <option value="">Select payment method</option>
                                <option value="cash">Cash</option>
                                <option value="credit_card">Credit Card</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="crdb">CRDB Bank</option>
                                <option value="selcom">Selcom</option>
                                <option value="dpo">DPO</option>
                                <option value="gepg">GePG</option>
                                <option value="mobile_money">Mobile Money</option>
                                <option value="control_number">Control Number</option>
                            </select>
                        </div>
                        <div>
                            <label for="payment_reference" class="block text-sm font-medium text-slate-700 mb-1">Payment Reference</label>
                            <input type="text" id="payment_reference" name="payment_reference" 
                                class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                                placeholder="e.g. Transaction ID or Reference">
                            <p class="text-xs text-slate-400 mt-1">Required for non-cash payments</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="total_amount" class="block text-sm font-medium text-slate-700 mb-1">Total Amount</label>
                            <input type="number" id="total_amount" name="total_amount" min="0" step="0.01"
                                class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                                placeholder="Auto-calculated">
                            <p class="text-xs text-slate-400 mt-1">Leave empty to auto-calculate from rooms</p>
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

                <!-- Notes -->
                <div class="pt-4 border-t border-slate-200">
                    <label for="notes" class="block text-sm font-medium text-slate-700 mb-1">Internal Notes</label>
                    <textarea id="notes" name="notes" rows="2"
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                        placeholder="Internal notes for staff..."></textarea>
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
    // Toggle guest fields based on selection
    function toggleGuestFields() {
        const guestId = document.getElementById('guest_id').value;
        const newGuestFields = document.getElementById('new_guest_fields');
        const guestName = document.getElementById('guest_name');
        
        if (guestId) {
            newGuestFields.classList.add('hidden');
            guestName.removeAttribute('required');
        } else {
            newGuestFields.classList.remove('hidden');
            guestName.setAttribute('required', 'required');
        }
    }

    // Toggle corporate fields based on reservation type
    function toggleCorporateFields() {
        const reservationType = document.getElementById('reservation_type').value;
        const corporateFields = document.getElementById('corporate_fields');
        const expiryDateField = document.getElementById('expiry_date_field');
        
        if (reservationType === 'corporate') {
            corporateFields.classList.remove('hidden');
        } else {
            corporateFields.classList.add('hidden');
        }
        
        if (reservationType === 'advance') {
            expiryDateField.classList.remove('hidden');
        } else {
            expiryDateField.classList.add('hidden');
        }
    }

    // Calculate balance automatically
    document.getElementById('total_amount').addEventListener('input', calculateBalance);
    document.getElementById('retainer_paid').addEventListener('input', calculateBalance);
    
    function calculateBalance() {
        const total = parseFloat(document.getElementById('total_amount').value) || 0;
        const retainer = parseFloat(document.getElementById('retainer_paid').value) || 0;
        const balance =Math.max(0, total - retainer);
        document.getElementById('balance_display').textContent = '$' + balance.toFixed(2);
    }

    // Set minimum dates
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('check_in_date').setAttribute('min', today);
    document.getElementById('check_out_date').setAttribute('min', today);
    document.getElementById('expiry_date').setAttribute('min', today);

    // Ensure check-out is after check-in
    document.getElementById('check_in_date').addEventListener('change', function() {
        document.getElementById('check_out_date').setAttribute('min', this.value);
    });
</script>
@endsection
