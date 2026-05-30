@extends('layouts.app')

@section('title', 'Booking #' . $booking->booking_ref)
@section('page-title', 'Booking Details')

@section('content')
<div class="space-y-6">
    <!-- Back Button -->
    <a href="{{ route('bookings') }}" class="inline-flex items-center gap-2 text-slate-600 hover:text-slate-800 transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Back to Bookings
    </a>

    <!-- Booking Header -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <div class="flex items-start justify-between">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <h1 class="text-2xl font-bold text-slate-800">Booking #{{ $booking->booking_ref }}</h1>
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
                    <span class="px-3 py-1 text-sm font-medium rounded-full {{ $statusClass }}">
                        {{ str_replace('_', ' ', ucfirst($booking->status)) }}
                    </span>
                </div>
                <p class="text-slate-500">Created on {{ date('F d, Y', strtotime($booking->created_at)) }}</p>
            </div>
            <div class="flex items-center gap-2">
                @if($booking->status === 'pending')
                    <a href="#confirm" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-sm font-medium transition">
                        Confirm Payment
                    </a>
                @elseif($booking->status === 'confirmed')
                    <form action="{{ route('bookings.checkin', $booking->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg text-sm font-medium transition">
                            Check In
                        </button>
                    </form>
                @elseif($booking->status === 'checked_in')
                    <form action="{{ route('bookings.checkout', $booking->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg text-sm font-medium transition">
                            Check Out
                        </button>
                    </form>
                @endif
                @if($booking->status !== 'pending')
                    <a href="{{ route('bookings.invoice', $booking->id) }}" class="px-4 py-2 bg-slate-600 hover:bg-slate-700 text-white rounded-lg text-sm font-medium transition">
                        View Invoice
                    </a>
                @endif
                <a href="{{ route('bookings.edit', $booking->id) }}" class="px-4 py-2 bg-slate-500 hover:bg-slate-600 text-white rounded-lg text-sm font-medium transition">
                    Edit
                </a>
                <form action="{{ route('bookings.destroy', $booking->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to cancel this booking?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-rose-500 hover:bg-rose-600 text-white rounded-lg text-sm font-medium transition">
                        Cancel
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Guest Information -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <h3 class="text-lg font-semibold text-slate-800 mb-4">Guest Information</h3>
            <div class="space-y-3">
                <div>
                    <label class="text-xs text-slate-500 uppercase">Name</label>
                    <p class="text-sm font-medium text-slate-800">{{ $booking->guest_name }}</p>
                </div>
                <div>
                    <label class="text-xs text-slate-500 uppercase">Email</label>
                    <p class="text-sm text-slate-600">{{ $booking->guest_email ?? 'Not provided' }}</p>
                </div>
                <div>
                    <label class="text-xs text-slate-500 uppercase">Phone</label>
                    <p class="text-sm text-slate-600">{{ $booking->guest_phone ?? 'Not provided' }}</p>
                </div>
            </div>
        </div>

        <!-- Room Details -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <h3 class="text-lg font-semibold text-slate-800 mb-4">Room Details</h3>
            <div class="space-y-3">
                <div>
                    <label class="text-xs text-slate-500 uppercase">Room Number</label>
                    <p class="text-sm font-medium text-slate-800">Room {{ $booking->room_number }}</p>
                </div>
                <div>
                    <label class="text-xs text-slate-500 uppercase">Guests</label>
                    <p class="text-sm text-slate-600">{{ $booking->adults }} Adults, {{ $booking->children }} Children</p>
                </div>
                <div>
                    <label class="text-xs text-slate-500 uppercase">Special Requests</label>
                    <p class="text-sm text-slate-600">{{ $booking->special_requests ?? 'None' }}</p>
                </div>
            </div>
        </div>

        <!-- Payment Summary -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <h3 class="text-lg font-semibold text-slate-800 mb-4">Payment Summary</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm text-slate-600">Total Amount</span>
                    <span class="text-sm font-medium text-slate-800">${{ number_format($booking->total_amount, 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-slate-600">Retainer Paid</span>
                    <span class="text-sm font-medium text-emerald-600">${{ number_format($booking->retainer_paid ?? 0, 2) }}</span>
                </div>
                <div class="border-t border-slate-200 pt-3 flex justify-between">
                    <span class="text-sm font-medium text-slate-700">Balance Due</span>
                    <span class="text-sm font-bold {{ $booking->balance_due > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                        ${{ number_format($booking->balance_due ?? 0, 2) }}
                    </span>
                </div>
            </div>
            @if($booking->balance_due > 0)
                <a href="{{ route('billing.show', $booking->id) }}" class="mt-4 block w-full text-center px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-sm font-medium transition">
                    Process Payment
                </a>
            @endif
        </div>
    </div>

    <!-- Stay Dates -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <h3 class="text-lg font-semibold text-slate-800 mb-4">Stay Dates</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-slate-500">Check In</p>
                    <p class="text-lg font-medium text-slate-800">{{ date('F d, Y', strtotime($booking->check_in_date)) }}</p>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-rose-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-slate-500">Check Out</p>
                    <p class="text-lg font-medium text-slate-800">{{ date('F d, Y', strtotime($booking->check_out_date)) }}</p>
                </div>
            </div>
        </div>
        @php
            $nights = (strtotime($booking->check_out_date) - strtotime($booking->check_in_date)) / (60 * 60 * 24);
        @endphp
        <div class="mt-4 pt-4 border-t border-slate-200">
            <p class="text-sm text-slate-600">Total Nights: <span class="font-medium text-slate-800">{{ $nights }} night(s)</span></p>
        </div>
    </div>

    @if($booking->status === 'pending')
    <!-- Confirm Payment Section -->
    <div id="confirm" class="bg-amber-50 border border-amber-200 rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-amber-800 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Confirm Booking Payment
        </h3>
        <p class="text-sm text-amber-700 mb-4">This booking is pending payment confirmation. Enter the payment details below to confirm and generate an invoice.</p>
        
        <form action="{{ route('bookings.confirm', $booking->id) }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="payment_type" class="block text-sm font-medium text-slate-700 mb-1">Payment Method *</label>
                    <select id="payment_type" name="payment_type" required
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                        <option value="">Select method</option>
                        <option value="cash">Cash Payment</option>
                        <option value="crdb">CRDB Bank Transfer</option>
                    </select>
                </div>
                <div>
                    <label for="payment_reference" class="block text-sm font-medium text-slate-700 mb-1">Payment Reference *</label>
                    <input type="text" id="payment_reference" name="payment_reference" required
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                        placeholder="e.g. CRDB-123456 or CASH-001">
                </div>
                <div>
                    <label for="retainer_paid" class="block text-sm font-medium text-slate-700 mb-1">Amount Paid</label>
                    <input type="number" id="retainer_paid" name="retainer_paid" min="0" step="0.01"
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                        placeholder="Leave blank if not paid">
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="submit" class="px-6 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-sm font-medium transition shadow-sm">
                    Confirm & Generate Invoice
                </button>
            </div>
        </form>
    </div>
    @endif
</div>
@endsection
