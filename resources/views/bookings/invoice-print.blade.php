<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $invoice->invoice_number ?? 'N/A' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none !important; }
        }
        @page { size: A4; margin: 1cm; }
    </style>
</head>
<body class="bg-white min-h-screen">
    <!-- Print Button -->
    <div class="no-print fixed top-4 right-4 z-50">
        <button onclick="window.print()" class="px-6 py-3 bg-amber-500 hover:bg-amber-600 text-white rounded-lg font-medium shadow-lg transition">
            Print Invoice
        </button>
    </div>

    <div class="max-w-4xl mx-auto p-8">
        <!-- Invoice Header -->
        <div class="flex items-start justify-between mb-8 border-b-2 border-slate-800 pb-6">
            <div class="flex items-center gap-4">
                <!-- Logo -->
                <div class="w-24 h-24 bg-amber-100 rounded-lg flex items-center justify-center border border-amber-200">
                    <svg class="w-12 h-12 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-slate-900">{{ $settings['lodge_name'] ?? 'Milano Lodge' }}</h1>
                    <p class="text-sm text-slate-600 mt-1">{{ $settings['contact_address'] ?? '123 Lodge Street, Arusha, Tanzania' }}</p>
                    <p class="text-sm text-slate-600">Tel: {{ $settings['contact_phone'] ?? '+255 123 456 789' }} | Email: {{ $settings['contact_email'] ?? 'info@milanlodge.com' }}</p>
                </div>
            </div>
            <div class="text-right">
                <h2 class="text-4xl font-bold text-slate-300">INVOICE</h2>
                <p class="text-xl font-semibold text-slate-700 mt-2">#{{ $invoice->invoice_number ?? 'N/A' }}</p>
                <p class="text-sm text-slate-500 mt-1">Date: {{ date('F d, Y', strtotime($invoice->created_at)) }}</p>
            </div>
        </div>

        <!-- Bill To & Invoice Details -->
        <div class="grid grid-cols-2 gap-8 mb-8">
            <div class="bg-slate-50 p-4 rounded-lg">
                <h3 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-2">Bill To:</h3>
                <p class="text-lg font-bold text-slate-800">{{ $booking->guest_name }}</p>
                @if($booking->guest_email)
                    <p class="text-sm text-slate-600">{{ $booking->guest_email }}</p>
                @endif
                @if($booking->guest_phone)
                    <p class="text-sm text-slate-600">{{ $booking->guest_phone }}</p>
                @endif
            </div>
            <div class="text-right">
                <div class="inline-block bg-slate-50 p-4 rounded-lg">
                    <p class="text-sm"><span class="text-slate-600">Booking Ref:</span> <span class="font-semibold">{{ $booking->booking_ref }}</span></p>
                    <p class="text-sm"><span class="text-slate-600">Room:</span> <span class="font-semibold">{{ $booking->room_number }}</span></p>
                    @php
                        $statusColors = [
                            'paid' => 'text-emerald-600',
                            'partial' => 'text-amber-600',
                            'pending' => 'text-rose-600',
                            'cancelled' => 'text-slate-600'
                        ];
                        $statusColor = $statusColors[$invoice->status ?? 'pending'] ?? 'text-slate-600';
                    @endphp
                    <p class="text-sm mt-2"><span class="text-slate-600">Status:</span> <span class="font-bold {{ $statusColor }}">{{ ucfirst($invoice->status ?? 'Pending') }}</span></p>
                </div>
            </div>
        </div>

        <!-- Stay Details -->
        <div class="bg-slate-100 p-4 rounded-lg mb-8">
            <h3 class="text-sm font-bold text-slate-700 mb-3">Stay Details</h3>
            <div class="grid grid-cols-4 gap-4">
                <div>
                    <p class="text-xs text-slate-500 uppercase">Check In</p>
                    <p class="font-semibold">{{ date('M d, Y', strtotime($booking->check_in_date)) }}</p>
                    <p class="text-sm text-slate-500">{{ $booking->check_in_time ?? '14:00' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500 uppercase">Check Out</p>
                    <p class="font-semibold">{{ date('M d, Y', strtotime($booking->check_out_date)) }}</p>
                    <p class="text-sm text-slate-500">{{ $booking->check_out_time ?? '11:00' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500 uppercase">Nights</p>
                    <p class="font-semibold">{{ $invoice->nights ?? 1 }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500 uppercase">Room Type</p>
                    <p class="font-semibold">{{ $booking->room_type_name ?? 'Standard' }}</p>
                </div>
            </div>
        </div>

        <!-- Invoice Items -->
        <table class="w-full mb-8 border-collapse">
            <thead>
                <tr class="bg-slate-800 text-white">
                    <th class="px-4 py-3 text-left text-sm font-bold">Description</th>
                    <th class="px-4 py-3 text-center text-sm font-bold">Qty</th>
                    <th class="px-4 py-3 text-right text-sm font-bold">Rate</th>
                    <th class="px-4 py-3 text-right text-sm font-bold">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr class="border-b border-slate-200">
                    <td class="px-4 py-4">
                        <p class="font-bold text-slate-800">Room {{ $booking->room_number }} - {{ $booking->room_type_name ?? 'Standard Room' }}</p>
                        <p class="text-sm text-slate-500">{{ $invoice->nights ?? 1 }} night(s) accommodation</p>
                    </td>
                    <td class="px-4 py-4 text-center">{{ $invoice->nights ?? 1 }}</td>
                    <td class="px-4 py-4 text-right">${{ number_format($invoice->room_rate ?? 0, 2) }}</td>
                    <td class="px-4 py-4 text-right font-bold">${{ number_format($invoice->subtotal ?? 0, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Totals -->
        <div class="flex justify-end mb-8">
            <div class="w-1/2">
                <div class="flex justify-between py-2 border-b border-slate-200">
                    <span class="text-slate-600">Subtotal</span>
                    <span class="font-semibold">${{ number_format($invoice->subtotal ?? 0, 2) }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-slate-200">
                    <span class="text-slate-600">Tax (12%)</span>
                    <span class="font-semibold">${{ number_format($invoice->tax_amount ?? 0, 2) }}</span>
                </div>
                <div class="flex justify-between py-3 border-b-2 border-slate-800">
                    <span class="text-lg font-bold">Total</span>
                    <span class="text-lg font-bold">${{ number_format($invoice->total_amount ?? 0, 2) }}</span>
                </div>
                <div class="flex justify-between py-2 text-emerald-600">
                    <span>Amount Paid</span>
                    <span class="font-semibold">${{ number_format($invoice->amount_paid ?? 0, 2) }}</span>
                </div>
                <div class="flex justify-between py-3 bg-slate-100 px-4 rounded-lg mt-2">
                    <span class="font-bold {{ ($invoice->balance_due ?? 0) > 0 ? 'text-rose-600' : 'text-emerald-600' }}">Balance Due</span>
                    <span class="font-bold {{ ($invoice->balance_due ?? 0) > 0 ? 'text-rose-600' : 'text-emerald-600' }}">${{ number_format($invoice->balance_due ?? 0, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Payment Information -->
        <div class="border-t-2 border-slate-200 pt-6 mb-8">
            <h3 class="text-sm font-bold text-slate-700 mb-3">Payment Information</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm"><span class="font-semibold">Payment Method:</span> {{ $invoice->payment_type === 'crdb' ? 'CRDB Bank Transfer' : 'Cash Payment' }}</p>
                    @if($invoice->payment_reference)
                        <p class="text-sm"><span class="font-semibold">Reference:</span> {{ $invoice->payment_reference }}</p>
                    @endif
                </div>
                <div class="text-right">
                    <p class="text-sm text-slate-500">Thank you for choosing</p>
                    <p class="text-lg font-bold text-slate-800">{{ $settings['lodge_name'] ?? 'Milano Lodge' }}</p>
                    @if($invoice->printed_at)
                        <p class="text-xs text-slate-400 mt-2">Printed: {{ date('M d, Y H:i', strtotime($invoice->printed_at)) }}</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center text-xs text-slate-400 mt-12 pt-4 border-t border-slate-200">
            <p>This is an official invoice from {{ $settings['lodge_name'] ?? 'LodgeOS' }}</p>
            <p>Please retain this document for your records.</p>
        </div>
    </div>

    <script>
        // Auto-print after page load
        window.addEventListener('load', function() {
            setTimeout(function() {
                // Uncomment the line below to auto-print
                // window.print();
            }, 500);
        });
    </script>
</body>
</html>
