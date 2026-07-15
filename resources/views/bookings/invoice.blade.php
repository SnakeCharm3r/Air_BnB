@extends('layouts.app')

@section('title', 'Receipt #' . ($invoice->invoice_number ?? 'N/A'))
@section('page-title', 'Receipt')

@section('content')
<div class="space-y-6">
    <!-- Back Button & Actions -->
    <div class="flex items-center justify-between">
        <a href="{{ route('bookings.show', $booking->id) }}" class="inline-flex items-center gap-2 text-slate-600 hover:text-slate-800 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Booking
        </a>
        <div class="flex items-center gap-2">
            <a href="{{ route('bookings.invoice.print', $booking->id) }}" target="_blank" 
                class="px-4 py-2 bg-slate-700 hover:bg-slate-800 text-white rounded-lg text-sm font-medium transition">
                Print Receipt
            </a>
            @if($invoice && $invoice->balance_due > 0)
                <a href="{{ route('billing.show', $booking->id) }}" 
                    class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-sm font-medium transition">
                    Process Payment
                </a>
            @endif
        </div>
    </div>

    <!-- Receipt Card -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-lg overflow-hidden max-w-4xl mx-auto">
        <div class="p-8">
            <!-- Receipt Header -->
            <div class="flex items-start justify-between mb-8">
                <!-- Company Info -->
                <div class="flex items-start gap-4">
                    <!-- Logo -->
                    <div class="w-24 h-24 bg-slate-100 rounded-lg flex items-center justify-center border border-slate-200 overflow-hidden">
                        @if(!empty($settings['lodge_logo']) && file_exists(storage_path('app/public/' . $settings['lodge_logo'])))
                            <img src="{{ asset('storage/' . $settings['lodge_logo']) }}" alt="{{ $settings['lodge_name'] ?? 'Lodge' }}" class="w-full h-full object-cover">
                        @elseif(!empty($settings['lodge_logo']) && file_exists(public_path($settings['lodge_logo'])))
                            <img src="{{ asset($settings['lodge_logo']) }}" alt="{{ $settings['lodge_name'] ?? 'Lodge' }}" class="w-full h-full object-cover">
                        @else
                            <div class="text-center">
                                <svg class="w-8 h-8 text-slate-400 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <p class="text-xs text-slate-400">No Logo</p>
                            </div>
                        @endif
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-slate-800">{{ $settings['lodge_name'] ?? 'Milano Lodge' }}</h1>
                        <p class="text-sm text-slate-500">{{ $settings['contact_address'] ?? '123 Lodge Street, Arusha, Tanzania' }}</p>
                        <p class="text-sm text-slate-500">Arusha, Tanzania</p>
                        <p class="text-sm text-slate-500">Tel: {{ $settings['contact_phone'] ?? '+255 123 456 789' }}</p>
                    </div>
                </div>
                <!-- RECEIPT Title -->
                <div class="text-right">
                    <h2 class="text-4xl font-light text-slate-800 tracking-wider">RECEIPT</h2>
                </div>
            </div>

            <!-- Receipt Info Row -->
            <div class="flex justify-between items-start mb-8">
                <!-- Bill To -->
                <div>
                    <p class="text-sm font-bold text-slate-800 mb-1">Bill To:</p>
                    <p class="text-base text-slate-700">{{ $booking->guest_name }}</p>
                    @if($booking->guest_email)
                        <p class="text-sm text-slate-500">{{ $booking->guest_email }}</p>
                    @endif
                    @if($booking->guest_phone)
                        <p class="text-sm text-slate-500">{{ $booking->guest_phone }}</p>
                    @endif
                </div>

                <!-- Receipt Details -->
                <div class="text-right">
                    <div class="mb-2">
                        <span class="text-sm font-medium text-slate-700">Receipt#</span>
                        <span class="text-sm text-slate-600 ml-2">{{ $invoice->invoice_number ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-slate-700">Receipt Date</span>
                        <span class="text-sm text-slate-600 ml-2 flex items-center justify-end gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            {{ date('F d, Y', strtotime($invoice->created_at ?? now())) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Receipt Line Items Table -->
            <table class="w-full mb-6 border-collapse">
                <thead>
                    <tr class="bg-slate-900 text-white">
                        <th class="px-4 py-3 text-left text-sm font-medium">Item Description</th>
                        <th class="px-4 py-3 text-center text-sm font-medium">Qty</th>
                        <th class="px-4 py-3 text-right text-sm font-medium">Rate</th>
                        <th class="px-4 py-3 text-right text-sm font-medium">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <tr>
                        <td class="px-4 py-4">
                            <p class="font-medium text-slate-800">Room {{ $booking->room_number }} - {{ $booking->room_type_name ?? 'Standard Room' }}</p>
                            <p class="text-sm text-slate-500">{{ $invoice->nights ?? 1 }} night(s) accommodation</p>
                            <p class="text-xs text-slate-400">{{ date('M d, Y', strtotime($booking->check_in_date)) }} to {{ date('M d, Y', strtotime($booking->check_out_date)) }}</p>
                        </td>
                        <td class="px-4 py-4 text-center">{{ $invoice->nights ?? 1 }}</td>
                        <td class="px-4 py-4 text-right">{{ format_money($invoice->room_rate ?? 0) }}</td>
                        <td class="px-4 py-4 text-right font-medium">{{ format_money($invoice->subtotal ?? 0) }}</td>
                    </tr>
                </tbody>
            </table>

            <!-- Totals Section -->
            <div class="flex justify-end">
                <div class="w-full md:w-1/2 lg:w-1/3">
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm py-1">
                            <span class="text-slate-600">Sub Total</span>
                            <span class="font-medium">{{ format_money($invoice->subtotal ?? 0) }}</span>
                        </div>
                        <div class="flex justify-between text-lg font-bold border-t-2 border-slate-800 pt-3 mt-2">
                            <span>TOTAL</span>
                            <span class="bg-slate-100 px-3 py-1 rounded">{{ format_money($invoice->total_amount ?? 0) }}</span>
                        </div>
                        @if(($invoice->amount_paid ?? 0) > 0)
                        <div class="flex justify-between text-sm text-emerald-600 py-1">
                            <span>Amount Paid</span>
                            <span class="font-medium">{{ format_money($invoice->amount_paid) }}</span>
                        </div>
                        @endif
                        @if(($invoice->balance_due ?? 0) > 0)
                        <div class="flex justify-between text-base font-bold text-rose-600 py-1">
                            <span>Balance Due</span>
                            <span>{{ format_money($invoice->balance_due) }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Payment Status Badge -->
            <div class="mt-8 flex justify-center">
                @php
                    $statusConfig = [
                        'paid' => ['bg-emerald-100 text-emerald-700 border-emerald-200', 'PAID'],
                        'partial' => ['bg-amber-100 text-amber-700 border-amber-200', 'PARTIAL PAYMENT'],
                        'pending' => ['bg-rose-100 text-rose-700 border-rose-200', 'PENDING PAYMENT'],
                        'cancelled' => ['bg-slate-100 text-slate-600 border-slate-200', 'CANCELLED']
                    ];
                    $status = $statusConfig[$invoice->status ?? 'pending'] ?? $statusConfig['pending'];
                @endphp
                <span class="px-6 py-2 text-sm font-bold rounded-lg border {{ $status[0] }}">
                    {{ $status[1] }}
                </span>
            </div>

            <!-- Payment Info -->
            <div class="mt-8 pt-6 border-t border-slate-200">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-slate-700">
                            <span class="font-medium">Payment Method:</span> 
                            {{ $invoice->payment_type === 'crdb' ? 'CRDB Bank Transfer' : ($invoice->payment_type === 'cash' ? 'Cash Payment' : 'N/A') }}
                        </p>
                        @if($invoice->payment_reference)
                            <p class="text-slate-700 mt-1">
                                <span class="font-medium">Reference:</span> {{ $invoice->payment_reference }}
                            </p>
                        @endif
                    </div>
                    <div class="md:text-right">
                        <p class="text-xs text-slate-400">Thank you for choosing {{ $settings['lodge_name'] ?? 'Milano Lodge' }}!</p>
                        @if($invoice->printed_at)
                            <p class="text-xs text-slate-400 mt-1">Printed: {{ date('M d, Y H:i', strtotime($invoice->printed_at)) }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Notes Section -->
            <div class="mt-6 pt-4 border-t border-slate-200">
                <p class="text-sm font-medium text-slate-700 mb-2">Notes</p>
                <p class="text-sm text-slate-500">{{ $booking->special_requests ?? 'No special requests' }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
