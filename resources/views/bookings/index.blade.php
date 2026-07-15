@extends('layouts.app')

@section('title', 'Bookings')
@section('page-title', 'Bookings')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">
                Booking Management
            </h2>
            <p class="text-sm text-slate-500">
                Manage guest reservations and payments
            </p>
        </div>

        @can('bookings.create')
        <a
            href="{{ route('bookings.create') }}"
            class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-sm font-medium transition shadow-sm"
        >
            <i class="fas fa-plus mr-2"></i>
            New Booking
        </a>
        @endcan
    </div>

    {{-- Overdue Checkouts Notifications --}}
    @if($overdueBookings->count())
        <div class="bg-rose-50 border border-rose-200 rounded-xl p-5 shadow-sm">
            <div class="flex items-center gap-2 mb-3">
                <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.054 0 1.502-1.002.814-1.78l-6.93-8.006a1.036 1.036 0 00-1.628 0l-6.93 8.006c-.688.778-.24 1.78.814 1.78z"/>
                </svg>
                <h3 class="font-semibold text-rose-800">Overdue Checkouts ({{ $overdueBookings->count() }})</h3>
            </div>
            <p class="text-sm text-rose-700 mb-4">
                The following guests are still checked in but their checkout date has passed. Choose to extend their stay or check them out.
            </p>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($overdueBookings as $overdue)
                    @php
                        $daysOverdue = (now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($overdue->check_out_date)->startOfDay(), false)) * -1;
                    @endphp
                    <div class="bg-white border border-rose-100 rounded-lg p-4">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <div class="font-semibold text-slate-800">{{ $overdue->guest_name }}</div>
                                <div class="text-sm text-slate-500">Room {{ $overdue->room_number }}</div>
                            </div>
                            <span class="px-2 py-1 bg-rose-100 text-rose-700 text-xs font-medium rounded-full">{{ $daysOverdue }} day(s) overdue</span>
                        </div>
                        <div class="text-sm text-slate-600 mb-3">
                            Checkout: <span class="font-medium">{{ date('M d, Y', strtotime($overdue->check_out_date)) }}</span>
                        </div>
                        <div class="flex gap-2">
                            <button type="button" onclick="openExtendModal({{ $overdue->id }}, '{{ $overdue->check_out_date }}', {{ $overdue->room_rate ?? 0 }})" class="flex-1 px-3 py-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg text-sm font-medium transition">
                                Extend
                            </button>
                            <form action="{{ route('bookings.checkout-overdue', $overdue->id) }}" method="POST" class="flex-1" onsubmit="return confirm('Check out {{ addslashes($overdue->guest_name) }}? A receipt will be sent to the cashier.');">
                                @csrf
                                <button type="submit" class="w-full px-3 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg text-sm font-medium transition">
                                    Checkout
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- View Toggle --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
        <div class="border-b border-slate-200">
            <nav class="flex gap-4 px-6">
                <button onclick="showView('calendar')" id="calendar-tab" class="py-4 px-2 text-sm font-medium text-amber-600 border-b-2 border-amber-600 transition">
                    <i class="fas fa-calendar-alt mr-2"></i>Calendar View
                </button>
                <button onclick="showView('list')" id="list-tab" class="py-4 px-2 text-sm font-medium text-slate-500 hover:text-slate-700 border-b-2 border-transparent transition">
                    <i class="fas fa-list mr-2"></i>List View
                </button>
            </nav>
        </div>

        {{-- Calendar View --}}
        <div id="calendar-view" class="p-6">

    {{-- Statistics --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-4">

        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-slate-500">Total Bookings</p>
                    <h3 class="text-2xl font-bold text-slate-800">
                        {{ $stats['total'] ?? 0 }}
                    </h3>
                </div>

                <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center">
                    <i class="fas fa-calendar-check text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-slate-500">Today's Check-ins</p>
                    <h3 class="text-2xl font-bold text-emerald-600">
                        {{ $stats['checkins'] ?? 0 }}
                    </h3>
                </div>

                <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center">
                    <i class="fas fa-door-open text-emerald-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-slate-500">Today's Check-outs</p>
                    <h3 class="text-2xl font-bold text-amber-600">
                        {{ $stats['checkouts'] ?? 0 }}
                    </h3>
                </div>

                <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center">
                    <i class="fas fa-sign-out-alt text-amber-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-slate-500">Occupancy</p>
                    <h3 class="text-2xl font-bold text-indigo-600">
                        {{ $stats['occupancy'] ?? 0 }}%
                    </h3>
                </div>

                <div class="w-12 h-12 rounded-xl bg-indigo-100 flex items-center justify-center">
                    <i class="fas fa-bed text-indigo-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-slate-500">Revenue</p>
                    <h3 class="text-2xl font-bold text-amber-600">
                        {{ format_money($stats['revenue'] ?? 0) }}
                    </h3>
                </div>

                <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center">
                    <i class="fas fa-dollar-sign text-amber-600 text-xl"></i>
                </div>
            </div>
        </div>

    </div>

    {{-- Calendar + Sidebar --}}
    <div class="grid grid-cols-1 xl:grid-cols-4 gap-6">

        {{-- Calendar --}}
        <div class="xl:col-span-3">

            <div class="bg-white rounded-xl border border-slate-200 shadow-sm">

                <div class="p-5 border-b border-slate-100 flex justify-between items-center">

                    <div class="flex gap-2 items-center">
                        <button class="px-3 py-2 border rounded-lg">
                            <i class="fas fa-chevron-left"></i>
                        </button>

                        <button class="px-3 py-2 border rounded-lg">
                            <i class="fas fa-chevron-right"></i>
                        </button>

                        <button class="px-4 py-2 border rounded-lg">
                            Today
                        </button>

                        <span class="font-semibold text-slate-700 ml-2">
                            <i class="fas fa-calendar-alt mr-2"></i>
                            {{ now()->format('F Y') }}
                        </span>
                    </div>

                    <div class="flex gap-2">
                        <button class="px-4 py-2 bg-amber-500 text-white rounded-lg">
                            Month
                        </button>

                        <button class="px-4 py-2 border rounded-lg">
                            Week
                        </button>

                        <button class="px-4 py-2 border rounded-lg">
                            Day
                        </button>
                    </div>

                </div>

                <div class="p-5">
                    <div id="booking-calendar"></div>
                </div>

            </div>

        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">

            {{-- Summary --}}
            <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">

                <h3 class="font-semibold text-slate-800 mb-5">
                    Booking Summary
                </h3>

                <div class="space-y-4">

                    <div class="flex justify-between">
                        <span class="text-slate-500">
                            <i class="fas fa-book text-blue-500 mr-2"></i>
                            Total
                        </span>

                        <span class="font-semibold">
                            {{ $stats['total'] ?? 0 }}
                        </span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-slate-500">
                            <i class="fas fa-check-circle text-emerald-500 mr-2"></i>
                            Checked In
                        </span>

                        <span class="font-semibold">
                            {{ $stats['checked_in'] ?? 0 }}
                        </span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-slate-500">
                            <i class="fas fa-clock text-amber-500 mr-2"></i>
                            Pending
                        </span>

                        <span class="font-semibold">
                            {{ $stats['pending'] ?? 0 }}
                        </span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-slate-500">
                            <i class="fas fa-check text-blue-500 mr-2"></i>
                            Confirmed
                        </span>

                        <span class="font-semibold">
                            {{ $stats['confirmed'] ?? 0 }}
                        </span>
                    </div>

                </div>

            </div>

            {{-- Upcoming --}}
            <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">

                <h3 class="font-semibold text-slate-800 mb-5">
                    Upcoming Bookings
                </h3>

                <div class="space-y-3">

                    @forelse($upcomingBookings as $booking)

                        <a
                            href="{{ route('bookings.show', $booking->id) }}"
                            class="block border border-slate-100 rounded-lg p-3 hover:bg-slate-50 transition"
                        >
                            <div class="font-medium text-slate-800">
                                {{ $booking->guest_name }}
                            </div>

                            <div class="text-sm text-slate-500">
                                Room {{ $booking->room_number }}
                            </div>

                            <div class="text-xs text-slate-400 mt-1">
                                {{ date('d M Y', strtotime($booking->check_in_date)) }}
                            </div>
                        </a>

                    @empty

                        <div class="text-sm text-slate-400">
                            No upcoming bookings.
                        </div>

                    @endforelse

                </div>

            </div>

        </div>

    </div>

        </div>

        {{-- List View --}}
        <div id="list-view" class="p-6 hidden">

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">

        <div class="p-5 border-b border-slate-100 flex justify-between items-center">

            <div>
                <h3 class="font-semibold text-slate-800">
                    Current Bookings
                </h3>

                <p class="text-sm text-slate-500">
                    All reservations and payment statuses
                </p>
            </div>

            <div class="flex gap-3">

                <input
                    type="text"
                    placeholder="Search bookings..."
                    class="border border-slate-200 rounded-lg px-4 py-2 text-sm"
                >

                <select
                    class="border border-slate-200 rounded-lg px-4 py-2 text-sm"
                >
                    <option>All Statuses</option>
                    <option>Pending</option>
                    <option>Confirmed</option>
                    <option>Checked In</option>
                    <option>Checked Out</option>
                </select>

            </div>

        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Booking Ref</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Guest</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Room</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Check In</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Check Out</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">Amount</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($bookings as $booking)
                        @php
                            $statusColors = [
                                'pending' => 'bg-amber-100 text-amber-700',
                                'confirmed' => 'bg-blue-100 text-blue-700',
                                'checked_in' => 'bg-emerald-100 text-emerald-700',
                                'checked_out' => 'bg-slate-100 text-slate-700',
                                'cancelled' => 'bg-rose-100 text-rose-700'
                            ];
                            $statusClass = $statusColors[$booking->status] ?? 'bg-slate-100 text-slate-700';
                        @endphp
                        <tr class="hover:bg-slate-50 {{ $booking->status === 'pending' ? 'bg-amber-50/50' : '' }}">
                            <td class="px-6 py-4 text-sm font-medium text-slate-800">
                                <a href="{{ route('bookings.show', $booking->id) }}" class="hover:text-amber-600 transition">{{ $booking->booking_ref }}</a>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-slate-800">{{ $booking->guest_name }}</div>
                                <div class="text-xs text-slate-500">{{ $booking->guest_phone ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $booking->room_number }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ date('M d, Y', strtotime($booking->check_in_date)) }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ date('M d, Y', strtotime($booking->check_out_date)) }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $statusClass }}">
                                    {{ ucfirst($booking->status) }}
                                </span>
                                @if($booking->status === 'pending')
                                    <div class="text-xs text-amber-600 mt-1">Awaiting Payment</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right text-sm font-medium text-slate-800">{{ format_money($booking->total_amount) }}</td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('bookings.show', $booking->id) }}" class="p-1.5 text-slate-400 hover:text-blue-600 transition" title="View">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    @if($booking->status === 'pending')
                                        <a href="{{ route('bookings.show', $booking->id) }}#confirm" class="p-1.5 text-amber-500 hover:text-amber-600 transition" title="Confirm Payment">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </a>
                                    @endif
                                    <a href="{{ route('bookings.edit', $booking->id) }}" class="p-1.5 text-slate-400 hover:text-amber-600 transition" title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-slate-400">No bookings found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

        </div>

    </div>

</div>

{{-- Extend Stay Modal --}}
<div id="extend-modal" class="fixed inset-0 z-50 hidden" aria-labelledby="extend-stay-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" onclick="closeExtendModal()"></div>
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-md transform overflow-hidden rounded-xl bg-white shadow-xl transition-all">
                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-emerald-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                            <h3 class="text-lg font-semibold text-slate-800" id="extend-stay-title">Extend Stay</h3>
                            <div class="mt-2">
                                <p class="text-sm text-slate-500 mb-4">Update the checkout date to extend the guest's stay. The total bill will be updated based on the room rate.</p>
                                <form id="extend-form" method="POST" action="" class="space-y-4">
                                    @csrf
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-1">Current Checkout</label>
                                        <input type="text" id="current-checkout" readonly class="w-full px-3 py-2 border border-slate-300 rounded-lg bg-slate-50 text-slate-600 text-sm">
                                    </div>
                                    <div>
                                        <label for="new-checkout" class="block text-sm font-medium text-slate-700 mb-1">New Checkout Date</label>
                                        <input type="date" id="new-checkout" name="check_out_date" required class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm">
                                    </div>
                                    <div class="bg-slate-50 rounded-lg p-3 text-sm space-y-1">
                                        <div class="flex justify-between">
                                            <span class="text-slate-600">Extra nights</span>
                                            <span id="extra-nights" class="font-medium text-slate-800">0</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-slate-600">Room rate</span>
                                            <span id="room-rate" class="font-medium text-slate-800">$0.00</span>
                                        </div>
                                        <div class="flex justify-between border-t border-slate-200 pt-2">
                                            <span class="text-slate-700 font-medium">Extra charge</span>
                                            <span id="extra-charge" class="font-bold text-emerald-600">$0.00</span>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-slate-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 gap-2">
                    <button type="button" onclick="submitExtendForm()" class="inline-flex w-full justify-center rounded-lg bg-emerald-500 px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-emerald-600 sm:w-auto">Confirm Extension</button>
                    <button type="button" onclick="closeExtendModal()" class="mt-3 inline-flex w-full justify-center rounded-lg bg-white px-3 py-2 text-sm font-medium text-slate-700 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50 sm:mt-0 sm:w-auto">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
{{-- FullCalendar v6 CSS is bundled in the JS; no separate CSS file needed --}}
@endpush

@push('scripts')
<script src="{{ asset('vendor/fullcalendar/index.global.min.js') }}"></script>

<script>
function showView(view) {
    const listView = document.getElementById('list-view');
    const calendarView = document.getElementById('calendar-view');
    const listTab = document.getElementById('list-tab');
    const calendarTab = document.getElementById('calendar-tab');

    if (view === 'list') {
        listView.classList.remove('hidden');
        calendarView.classList.add('hidden');
        listTab.classList.add('text-amber-600', 'border-amber-600');
        listTab.classList.remove('text-slate-500', 'border-transparent');
        calendarTab.classList.remove('text-amber-600', 'border-amber-600');
        calendarTab.classList.add('text-slate-500', 'border-transparent');
    } else {
        listView.classList.add('hidden');
        calendarView.classList.remove('hidden');
        calendarTab.classList.add('text-amber-600', 'border-amber-600');
        calendarTab.classList.remove('text-slate-500', 'border-transparent');
        listTab.classList.remove('text-amber-600', 'border-amber-600');
        listTab.classList.add('text-slate-500', 'border-transparent');
    }
}

document.addEventListener('DOMContentLoaded', function () {

    const calendarEl = document.getElementById('booking-calendar');

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        height: 700,
        headerToolbar: false,
        events: @json($calendarBookings ?? []),

        eventClick: function(info) {
            if (info.event.id === 'today') {
                return;
            }
            window.location =
                '/bookings/' + info.event.id;
        }
    });

    calendar.render();
});

let currentRoomRate = 0;
let currentCheckout = null;

function openExtendModal(bookingId, checkoutDate, roomRate) {
    currentRoomRate = roomRate || 0;
    currentCheckout = checkoutDate;

    document.getElementById('extend-form').action = '/bookings/' + bookingId + '/extend-stay';
    document.getElementById('current-checkout').value = new Date(checkoutDate).toLocaleDateString();

    const newCheckoutInput = document.getElementById('new-checkout');
    newCheckoutInput.value = '';
    newCheckoutInput.min = checkoutDate;

    document.getElementById('room-rate').textContent = '$' + currentRoomRate.toFixed(2);
    updateCharge();

    document.getElementById('extend-modal').classList.remove('hidden');
}

function closeExtendModal() {
    document.getElementById('extend-modal').classList.add('hidden');
}

function submitExtendForm() {
    const newCheckout = document.getElementById('new-checkout').value;
    if (!newCheckout || newCheckout <= currentCheckout) {
        alert('Please select a new checkout date after the current checkout date.');
        return;
    }
    document.getElementById('extend-form').submit();
}

function updateCharge() {
    const newCheckout = document.getElementById('new-checkout').value;
    if (!newCheckout || !currentCheckout) {
        document.getElementById('extra-nights').textContent = '0';
        document.getElementById('extra-charge').textContent = '$0.00';
        return;
    }

    const oneDay = 24 * 60 * 60 * 1000;
    const oldDate = new Date(currentCheckout);
    const newDate = new Date(newCheckout);
    const diffMs = newDate - oldDate;
    const extraNights = Math.max(0, Math.round(diffMs / oneDay));
    const extraCharge = extraNights * currentRoomRate;

    document.getElementById('extra-nights').textContent = extraNights;
    document.getElementById('extra-charge').textContent = '$' + extraCharge.toFixed(2);
}

document.getElementById('new-checkout').addEventListener('change', updateCharge);
</script>
@endpush