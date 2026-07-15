@extends('layouts.app')

@section('title', 'New Kitchen Order')
@section('page-title', 'New Kitchen Order')

@section('content')
<div class="max-w-3xl mx-auto space-y-4">

    <a href="{{ route('kitchen-orders.index') }}" class="inline-flex items-center gap-2 text-slate-600 hover:text-slate-800 text-sm transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Back to Orders
    </a>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-slate-800">New Kitchen Order</h2>
            <p class="text-sm text-slate-500">Select a checked-in guest to automatically charge the order to their room folio.</p>
        </div>

        @if($bookings->isEmpty())
            {{-- Empty state: no checked-in guests --}}
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                </div>
                <p class="text-slate-700 font-semibold text-lg">No active guests in any room for ordering</p>
                <p class="text-slate-400 text-sm mt-1">There are currently no guests with a checked-in status. Check in a guest first before placing a kitchen order.</p>
                <a href="{{ route('bookings') }}" class="mt-5 inline-flex items-center gap-2 px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-sm font-medium transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    View Bookings
                </a>
            </div>
        @else
        <form action="{{ route('kitchen-orders.store') }}" method="POST" class="space-y-6">
            @csrf
            <input type="hidden" name="room_id" id="room_id_input">
            <input type="hidden" name="booking_id" id="booking_id_input">

            {{-- Step 1: Select Checked-In Guest --}}
            <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                <h3 class="text-sm font-semibold text-amber-800 mb-3">1. Select Checked-In Guest</h3>
                <div>
                    <label for="booking_select" class="block text-sm font-medium text-slate-700 mb-1">Active Guest</label>
                    <select id="booking_select" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                        <option value="">— Select a checked-in guest —</option>
                        @foreach($bookings as $b)
                            <option value="{{ $b->id }}"
                                data-room-id="{{ $b->room_id }}"
                                data-guest="{{ $b->guest_name }}"
                                data-folio="{{ $b->folio_id ?? '' }}"
                                data-room-number="{{ $b->room_number }}">
                                Room {{ $b->room_number }} — {{ $b->guest_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                {{-- Folio charge notice --}}
                <div id="folio-notice" class="mt-3 hidden text-xs px-3 py-2 rounded-lg"></div>
            </div>

            {{-- Step 2: Auto-filled Room & Guest info (read-only) --}}
            <div id="guest-info-panel" class="hidden bg-white border border-slate-200 rounded-lg p-4">
                <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-3">Guest & Room Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1">Room Number</label>
                        <div id="display-room" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm font-semibold text-slate-800">—</div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1">Guest Name</label>
                        <div id="display-guest" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-800">—</div>
                    </div>
                </div>
                {{-- Hidden actual guest_name for form submission --}}
                <input type="hidden" name="guest_name" id="guest_name_input">
            </div>

            {{-- Step 3: Menu Item + Quantity --}}
            <div class="bg-slate-50 border border-slate-200 rounded-lg p-4">
                <h3 class="text-sm font-semibold text-slate-700 mb-3">2. Select Order</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="menu_item_id" class="block text-sm font-medium text-slate-700 mb-1">Menu Item *</label>
                        <select name="menu_item_id" id="menu_item_id" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                            <option value="">Select item</option>
                            @foreach($activeItems as $item)
                                <option value="{{ $item->id }}" data-price="{{ $item->price }}">
                                    {{ $item->name }} — {{ format_money($item->price) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="quantity" class="block text-sm font-medium text-slate-700 mb-1">Quantity *</label>
                        <input type="number" name="quantity" id="quantity" min="1" value="1" required
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                    </div>
                </div>

                {{-- Live total --}}
                <div class="mt-4 flex items-center justify-between bg-white border border-slate-200 rounded-lg px-4 py-3">
                    <span class="text-sm text-slate-600">Order Total</span>
                    <span id="order-total" class="text-lg font-bold text-emerald-600">{{ format_money(0) }}</span>
                </div>
            </div>

            <div>
                <label for="notes" class="block text-sm font-medium text-slate-700 mb-1">Notes / Special Instructions</label>
                <textarea name="notes" id="notes" rows="2"
                    class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                    placeholder="Allergies, preferences..."></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                <a href="{{ route('kitchen-orders.index') }}" class="px-4 py-2 text-slate-600 hover:text-slate-800 text-sm font-medium">Cancel</a>
                <button type="submit" id="submit-btn" disabled
                    class="px-5 py-2 bg-emerald-500 hover:bg-emerald-600 disabled:opacity-40 disabled:cursor-not-allowed text-white rounded-lg text-sm font-semibold transition shadow-sm">
                    Place Order
                </button>
            </div>
        </form>
        @endif
    </div>
</div>

@if($bookings->isNotEmpty())
<script>
const bookingSelect  = document.getElementById('booking_select');
const roomIdInput    = document.getElementById('room_id_input');
const bookingIdInput = document.getElementById('booking_id_input');
const guestNameInput = document.getElementById('guest_name_input');
const guestInfoPanel = document.getElementById('guest-info-panel');
const displayRoom    = document.getElementById('display-room');
const displayGuest   = document.getElementById('display-guest');
const menuSelect     = document.getElementById('menu_item_id');
const qtyInput       = document.getElementById('quantity');
const totalDisplay   = document.getElementById('order-total');
const folioNotice    = document.getElementById('folio-notice');
const submitBtn      = document.getElementById('submit-btn');

function formatMoney(amount) {
    return new Intl.NumberFormat('en-TZ', { style: 'currency', currency: 'TZS', minimumFractionDigits: 0 }).format(amount);
}

function updateTotal() {
    const opt   = menuSelect.options[menuSelect.selectedIndex];
    const price = parseFloat(opt?.dataset?.price || 0);
    const qty   = parseInt(qtyInput.value) || 1;
    totalDisplay.textContent = formatMoney(price * qty);
}

bookingSelect.addEventListener('change', function () {
    const opt = this.options[this.selectedIndex];

    if (!opt.value) {
        guestInfoPanel.classList.add('hidden');
        roomIdInput.value    = '';
        bookingIdInput.value = '';
        guestNameInput.value = '';
        folioNotice.className = 'mt-3 hidden text-xs px-3 py-2 rounded-lg';
        submitBtn.disabled = true;
        return;
    }

    // Populate hidden fields
    roomIdInput.value    = opt.dataset.roomId;
    bookingIdInput.value = opt.value;
    guestNameInput.value = opt.dataset.guest;

    // Show read-only panel
    displayRoom.textContent  = 'Room ' + opt.dataset.roomNumber;
    displayGuest.textContent = opt.dataset.guest;
    guestInfoPanel.classList.remove('hidden');

    // Folio charge notice
    const hasFolio = opt.dataset.folio !== '';
    folioNotice.className = 'mt-3 text-xs px-3 py-2 rounded-lg ' +
        (hasFolio
            ? 'bg-emerald-50 text-emerald-700 border border-emerald-200'
            : 'bg-amber-50 text-amber-700 border border-amber-200');
    folioNotice.textContent = hasFolio
        ? '✓ Open folio found — this order will be charged to Room ' + opt.dataset.roomNumber + ' folio automatically.'
        : '⚠ No open folio for this booking. Order will be placed in the kitchen but not billed to a folio yet.';
    folioNotice.classList.remove('hidden');

    submitBtn.disabled = false;
});

menuSelect.addEventListener('change', updateTotal);
qtyInput.addEventListener('input', updateTotal);
</script>
@endif
@endsection
