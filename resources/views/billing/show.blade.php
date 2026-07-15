@extends('layouts.app')

@section('title', 'Billing - ' . $booking->guest_name)
@section('page-title', 'Billing Details')

@section('content')
<div class="space-y-6">
    <!-- Back Button -->
    <a href="{{ route('billing.index') }}" class="inline-flex items-center gap-2 text-slate-600 hover:text-slate-800 transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Back to Billing
    </a>

    <!-- Header Card -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">{{ $booking->guest_name }}</h1>
                <p class="text-slate-500">Booking #{{ $booking->booking_ref }} | Room {{ $booking->room_number }}</p>
                <div class="flex items-center gap-2 mt-2">
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
                        {{ ucfirst($booking->status) }}
                    </span>
                    <span class="px-3 py-1 text-sm font-medium rounded-full {{ $folio->balance_due > 0 ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-700' }}">
                        {{ ucfirst(str_replace('_', ' ', $folio->payment_status)) }}
                    </span>
                </div>
            </div>
            <div class="text-right">
                <p class="text-sm text-slate-500">Folio Balance Due</p>
                <p class="text-3xl font-bold {{ $folio->balance_due > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                    {{ format_money($folio->balance_due) }}
                </p>
                <p class="text-sm text-slate-500 mt-1">Folio #{{ $folio->folio_number }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - Charges & Payments -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Charges Summary -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-4 border-b border-slate-200 bg-slate-50">
                    <h3 class="text-lg font-semibold text-slate-800">Charges Summary</h3>
                </div>
                <div class="p-0">
                    <table class="w-full">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Description</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-slate-500 uppercase">Type</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 uppercase">Qty</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 uppercase">Unit Price</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 uppercase">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($charges as $charge)
                                <tr class="{{ $charge->status === 'reversed' ? 'opacity-60 line-through' : '' }}">
                                    <td class="px-4 py-3">
                                        <p class="font-medium text-slate-800">{{ $charge->description }}</p>
                                        <p class="text-sm text-slate-500">{{ date('M d, Y', strtotime($charge->posting_date ?? $charge->created_at)) }}</p>
                                        @if($charge->status === 'reversed')
                                            <p class="text-xs text-rose-600 font-medium">Reversed</p>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-700">
                                            {{ ucfirst(str_replace('_', ' ', $charge->charge_type)) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right text-sm text-slate-600">{{ number_format($charge->quantity, 2) }}</td>
                                    <td class="px-4 py-3 text-right text-sm text-slate-600">{{ format_money($charge->unit_price) }}</td>
                                    <td class="px-4 py-3 text-right font-medium">{{ format_money($charge->total_amount) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-slate-50 font-semibold">
                            <tr>
                                <td colspan="4" class="px-4 py-3 text-right">Total Charges</td>
                                <td class="px-4 py-3 text-right">{{ format_money($folio->subtotal) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Payment History -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-4 border-b border-slate-200 bg-slate-50">
                    <h3 class="text-lg font-semibold text-slate-800">Payment History</h3>
                </div>
                @if(count($payments) > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Date</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Method</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Receipt #</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Reference</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-slate-500 uppercase">Status</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 uppercase">Amount</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($payments as $payment)
                                    <tr class="{{ $payment->is_void ? 'opacity-60 line-through' : '' }}">
                                        <td class="px-4 py-3 text-sm">{{ date('M d, Y', strtotime($payment->payment_date ?? $payment->created_at)) }}</td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-emerald-100 text-emerald-700">
                                                {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-slate-600">{{ $payment->receipt_number ?? 'N/A' }}</td>
                                        <td class="px-4 py-3 text-sm text-slate-600">{{ $payment->reference ?? 'N/A' }}</td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $payment->is_void ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-700' }}">
                                                {{ $payment->is_void ? 'Void' : ucfirst($payment->payment_status) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-right font-medium {{ $payment->amount < 0 ? 'text-rose-600' : 'text-emerald-600' }}">{{ format_money($payment->amount) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-slate-50 font-semibold">
                                <tr>
                                    <td colspan="5" class="px-4 py-3 text-right">Total Paid</td>
                                    <td class="px-4 py-3 text-right text-emerald-600">{{ format_money($folio->amount_paid) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <div class="p-8 text-center text-slate-400">
                        <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <p>No payments recorded yet</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Right Column - Actions -->
        <div class="space-y-6">
            <!-- Payment Summary Card -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                <h3 class="text-lg font-semibold text-slate-800 mb-4">Folio Summary</h3>
                <div class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-600">Subtotal</span>
                        <span class="font-medium">{{ format_money($folio->subtotal) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-600">Total Paid</span>
                        <span class="font-medium text-emerald-600">{{ format_money($folio->amount_paid) }}</span>
                    </div>
                    <div class="border-t border-slate-200 pt-3">
                        <div class="flex justify-between text-base font-bold">
                            <span class="{{ $folio->balance_due > 0 ? 'text-rose-600' : 'text-emerald-600' }}">Balance Due</span>
                            <span class="{{ $folio->balance_due > 0 ? 'text-rose-600' : 'text-emerald-600' }}">{{ format_money($folio->balance_due) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            @if($folio->balance_due > 0)
            @can('payments.create')
            <!-- Process Payment Form -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                <h3 class="text-lg font-semibold text-slate-800 mb-4">Record Payment</h3>
                <form action="{{ route('billing.payment', $booking->id) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="amount" class="block text-sm font-medium text-slate-700 mb-1">Amount *</label>
                        <input type="number" id="amount" name="amount" required min="0" step="0.01"
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500"
                            placeholder="0.00">
                    </div>
                    <div>
                        <label for="payment_method" class="block text-sm font-medium text-slate-700 mb-1">Payment Method *</label>
                        <select id="payment_method" name="payment_method" required
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                            <option value="">Select method</option>
                            <option value="cash">Cash</option>
                            <option value="card">Card</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="mobile_money">Mobile Money</option>
                        </select>
                    </div>
                    <div>
                        <label for="payment_for" class="block text-sm font-medium text-slate-700 mb-1">Payment For</label>
                        <select id="payment_for" name="payment_for"
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                            <option value="">General / Folio Balance</option>
                            @foreach($charges as $charge)
                                <option value="{{ $charge->description }}">{{ $charge->description }} ({{ format_money($charge->total_amount) }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="reference" class="block text-sm font-medium text-slate-700 mb-1">Reference</label>
                        <input type="text" id="reference" name="reference"
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500"
                            placeholder="Transaction reference">
                    </div>
                    <div>
                        <label for="notes" class="block text-sm font-medium text-slate-700 mb-1">Notes</label>
                        <textarea id="notes" name="notes" rows="2"
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500"
                            placeholder="Optional notes..."></textarea>
                    </div>
                    <button type="submit" class="w-full px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg text-sm font-medium transition">
                        Record Payment
                    </button>
                </form>
            </div>
            @endcan

            @can('charges.create')
            <!-- Add Charge Form -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                <h3 class="text-lg font-semibold text-slate-800 mb-4">Add Charge</h3>
                <form action="{{ route('billing.charge', $booking->id) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="description" class="block text-sm font-medium text-slate-700 mb-1">Description *</label>
                        <input type="text" id="description" name="description" required
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500"
                            placeholder="e.g. Room Service, Mini Bar">
                    </div>
                    <div>
                        <label for="charge_amount" class="block text-sm font-medium text-slate-700 mb-1">Amount *</label>
                        <input type="number" id="charge_amount" name="amount" required min="0" step="0.01"
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500"
                            placeholder="0.00">
                    </div>
                    <div>
                        <label for="charge_type" class="block text-sm font-medium text-slate-700 mb-1">Charge Type *</label>
                        <select id="charge_type" name="charge_type" required
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                            <option value="room">Room</option>
                            <option value="restaurant">Restaurant</option>
                            <option value="laundry">Laundry</option>
                            <option value="mini_bar">Mini Bar</option>
                            <option value="room_service">Room Service</option>
                            <option value="spa">Spa</option>
                            <option value="transport">Transport</option>
                            <option value="damage">Damage</option>
                            <option value="conference">Conference</option>
                            <option value="equipment_hire">Equipment Hire</option>
                            <option value="extra_bed">Extra Bed</option>
                            <option value="early_check_in">Early Check In</option>
                            <option value="late_check_out">Late Check Out</option>
                            <option value="miscellaneous">Miscellaneous</option>
                        </select>
                    </div>
                    <button type="submit" class="w-full px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-sm font-medium transition">
                        Add Charge
                    </button>
                </form>
            </div>
            @endcan
            @endif

            <!-- Quick Actions -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                <h3 class="text-lg font-semibold text-slate-800 mb-4">Quick Actions</h3>
                <div class="space-y-2">
                    <a href="{{ route('bookings.show', $booking->id) }}" class="block w-full px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-sm font-medium transition text-center">
                        View Booking
                    </a>
                    <a href="{{ route('bookings.invoice', $booking->id) }}" class="block w-full px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-sm font-medium transition text-center">
                        View Invoice
                    </a>
                    <a href="{{ route('bookings.invoice.print', $booking->id) }}" target="_blank" class="block w-full px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-sm font-medium transition text-center">
                        Print Invoice
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
