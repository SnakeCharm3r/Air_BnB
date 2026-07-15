@extends('layouts.app')

@section('title', 'Receipt - ' . $receipt->receipt_number)
@section('page-title', 'Receipt')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <a href="{{ route('receipts.index') }}" class="inline-flex items-center gap-2 text-slate-600 hover:text-slate-800 transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Back to Receipts
    </a>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-8">
        <!-- Hotel Header -->
        <div class="text-center mb-8 border-b-2 border-slate-200 pb-6">
            <h1 class="text-3xl font-bold text-slate-900">{{ $appSettings->lodge_name ?? 'Hotel' }}</h1>
            @if(!empty($appSettings->contact_address))
                <p class="text-sm text-slate-600 mt-1">{{ $appSettings->contact_address }}</p>
            @endif
            <p class="text-sm text-slate-600 mt-1">
                @if(!empty($appSettings->contact_phone))Tel: {{ $appSettings->contact_phone }}@endif
                @if(!empty($appSettings->contact_phone) && !empty($appSettings->contact_email)) | @endif
                @if(!empty($appSettings->contact_email))Email: {{ $appSettings->contact_email }}@endif
            </p>
            @if(!empty($appSettings->website))
                <p class="text-sm text-slate-600 mt-1">Web: {{ $appSettings->website }}</p>
            @endif
        </div>

        <!-- Receipt Title & Status -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
            <div>
                <h2 class="text-2xl font-bold text-slate-800">Receipt</h2>
                <p class="text-slate-500 mt-1">{{ $receipt->receipt_number }}</p>
            </div>
            <div class="mt-4 md:mt-0">
                <span class="px-3 py-1 text-sm font-bold uppercase rounded-full {{ $receipt->status_class }}">
                    {{ $receipt->status_label }}
                </span>
                @if($receipt->is_reprint)
                    <span class="ml-2 px-2 py-1 text-xs font-bold uppercase rounded-full bg-amber-100 text-amber-700">Reprint</span>
                @endif
            </div>
        </div>

        <!-- Receipt Info & Guest Info -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-slate-50 rounded-lg p-4">
                <h3 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-3">Receipt Information</h3>
                <div class="space-y-1 text-sm">
                    <p><span class="text-slate-600">Receipt #:</span> <span class="font-semibold">{{ $receipt->receipt_number }}</span></p>
                    @if($receipt->invoice_number)
                        <p><span class="text-slate-600">Invoice #:</span> <span class="font-semibold">{{ $receipt->invoice_number }}</span></p>
                    @endif
                    @if($receipt->folio_number)
                        <p><span class="text-slate-600">Folio #:</span> <span class="font-semibold">{{ $receipt->folio_number }}</span></p>
                    @endif
                    @if($receipt->booking_number)
                        <p><span class="text-slate-600">Booking #:</span> <span class="font-semibold">{{ $receipt->booking_number }}</span></p>
                    @endif
                    <p><span class="text-slate-600">Receipt Date:</span> <span class="font-semibold">{{ $receipt->receipt_date ? date('M d, Y', strtotime($receipt->receipt_date)) : '-' }}</span></p>
                    <p><span class="text-slate-600">Payment Date:</span> <span class="font-semibold">{{ $receipt->payment_date ? date('M d, Y', strtotime($receipt->payment_date)) : '-' }}</span></p>
                    <p><span class="text-slate-600">Method:</span> <span class="font-semibold">{{ ucfirst(str_replace('_', ' ', $receipt->payment_method ?? 'N/A')) }}</span></p>
                    @if($receipt->reference)
                        <p><span class="text-slate-600">Reference:</span> <span class="font-semibold">{{ $receipt->reference }}</span></p>
                    @endif
                    <p><span class="text-slate-600">Type:</span> <span class="font-semibold">{{ $receipt->receipt_type }}</span></p>
                </div>
            </div>

            <div class="bg-slate-50 rounded-lg p-4">
                <h3 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-3">Guest Information</h3>
                <div class="space-y-1 text-sm">
                    <p><span class="text-slate-600">Guest:</span> <span class="font-semibold">{{ $receipt->guest_name }}</span></p>
                    @if($receipt->room_number)
                        <p><span class="text-slate-600">Room:</span> <span class="font-semibold">{{ $receipt->room_number }}</span></p>
                    @endif
                    @if($receipt->booking_number)
                        <p><span class="text-slate-600">Reservation #:</span> <span class="font-semibold">{{ $receipt->booking_number }}</span></p>
                    @endif
                    @if($receipt->check_in_date)
                        <p><span class="text-slate-600">Arrival:</span> <span class="font-semibold">{{ date('M d, Y', strtotime($receipt->check_in_date)) }}</span></p>
                    @endif
                    @if($receipt->check_out_date)
                        <p><span class="text-slate-600">Departure:</span> <span class="font-semibold">{{ date('M d, Y', strtotime($receipt->check_out_date)) }}</span></p>
                    @endif
                    @if($receipt->length_of_stay > 0)
                        <p><span class="text-slate-600">Length of Stay:</span> <span class="font-semibold">{{ $receipt->length_of_stay }} Night{{ $receipt->length_of_stay > 1 ? 's' : '' }}</span></p>
                    @endif
                </div>
            </div>
        </div>

        @if($receipt->invoice_number)
            <div class="mb-8 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <p class="text-sm text-blue-800">
                    <span class="font-bold">Invoice Reference:</span> This receipt settles invoice <span class="font-semibold">{{ $receipt->invoice_number }}</span>.
                </p>
            </div>
        @endif

        <!-- Itemized Charges -->
        <div class="mb-8">
            <h3 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-3">Itemized Charges</h3>
            @if(count($receipt->charges) > 0)
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-sm">
                        <thead>
                            <tr class="bg-slate-800 text-white">
                                <th class="px-4 py-2 text-left">Description</th>
                                <th class="px-4 py-2 text-center">Qty</th>
                                <th class="px-4 py-2 text-right">Unit Rate</th>
                                <th class="px-4 py-2 text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($receipt->charges as $charge)
                                <tr class="border-b border-slate-200 {{ $charge->status === 'reversed' ? 'opacity-60 line-through' : '' }}">
                                    <td class="px-4 py-2">{{ $charge->description }}</td>
                                    <td class="px-4 py-2 text-center">{{ number_format($charge->quantity, 2) }}</td>
                                    <td class="px-4 py-2 text-right">{{ format_money($charge->unit_rate) }}</td>
                                    <td class="px-4 py-2 text-right font-semibold">{{ format_money($charge->amount) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-slate-100 font-bold">
                                <td colspan="3" class="px-4 py-2 text-right">Total Charges</td>
                                <td class="px-4 py-2 text-right">{{ format_money($receipt->total_charges) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <p class="text-sm text-slate-500">No charges recorded.</p>
            @endif
        </div>

        <!-- Payment History -->
        @if(count($receipt->payment_history) > 0)
            <div class="mb-8">
                <h3 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-3">Payment History</h3>
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-sm">
                        <thead>
                            <tr class="bg-slate-800 text-white">
                                <th class="px-4 py-2 text-left">Receipt #</th>
                                <th class="px-4 py-2 text-left">Date</th>
                                <th class="px-4 py-2 text-left">Method</th>
                                <th class="px-4 py-2 text-left">Reference</th>
                                <th class="px-4 py-2 text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($receipt->payment_history as $p)
                                <tr class="border-b border-slate-200">
                                    <td class="px-4 py-2">{{ $p->receipt_number ?? 'N/A' }}</td>
                                    <td class="px-4 py-2">{{ $p->payment_date ? date('M d, Y', strtotime($p->payment_date)) : '-' }}</td>
                                    <td class="px-4 py-2">{{ ucfirst(str_replace('_', ' ', $p->payment_method ?? 'N/A')) }}</td>
                                    <td class="px-4 py-2">{{ $p->reference ?? '-' }}</td>
                                    <td class="px-4 py-2 text-right font-semibold">{{ format_money($p->amount) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-slate-100 font-bold">
                                <td colspan="4" class="px-4 py-2 text-right">Total Previous Payments</td>
                                <td class="px-4 py-2 text-right">{{ format_money($receipt->previous_payments) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        @endif

        <!-- Current Payment -->
        <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-6 mb-8">
            <h3 class="text-sm font-bold text-emerald-800 uppercase tracking-wider mb-4">Current Payment (This Receipt)</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <p><span class="text-slate-600">Amount Received:</span> <span class="font-bold text-emerald-700">{{ format_money($receipt->amount_received) }}</span></p>
                    <p class="mt-1"><span class="text-slate-600">Payment Method:</span> <span class="font-semibold">{{ ucfirst(str_replace('_', ' ', $receipt->payment_method ?? 'N/A')) }}</span></p>
                    @if($receipt->reference)
                        <p class="mt-1"><span class="text-slate-600">Reference:</span> <span class="font-semibold">{{ $receipt->reference }}</span></p>
                    @endif
                    <p class="mt-1"><span class="text-slate-600">Cashier:</span> <span class="font-semibold">{{ $receipt->cashier ?? 'System' }}</span></p>
                </div>
                <div>
                    <p><span class="text-slate-600">Date Received:</span> <span class="font-semibold">{{ $receipt->payment_date ? date('M d, Y', strtotime($receipt->payment_date)) : '-' }}</span></p>
                    @if($receipt->notes)
                        <p class="mt-1"><span class="text-slate-600">Notes:</span> <span class="font-semibold">{{ $receipt->notes }}</span></p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Payment Allocation Summary -->
        <div class="mb-8">
            <h3 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-3">Payment Allocation Summary</h3>
            <div class="bg-slate-50 rounded-lg p-4 text-sm space-y-1">
                <div class="flex justify-between">
                    <span class="text-slate-600">Total Charges</span>
                    <span class="font-semibold">{{ format_money($receipt->total_charges) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-600">Previous Payments</span>
                    <span class="font-semibold">{{ format_money($receipt->previous_payments) }}</span>
                </div>
                <div class="flex justify-between border-b border-slate-200 pb-1">
                    <span class="text-slate-600">Outstanding Before Payment</span>
                    <span class="font-semibold">{{ format_money($receipt->outstanding_before) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-600">Current Payment</span>
                    <span class="font-bold text-emerald-600">{{ format_money($receipt->amount_received) }}</span>
                </div>
                @if($receipt->change_returned > 0)
                    <div class="flex justify-between">
                        <span class="text-slate-600">Change Returned</span>
                        <span class="font-semibold text-amber-600">{{ format_money($receipt->change_returned) }}</span>
                    </div>
                @endif
                @if($receipt->guest_credit > 0 && $receipt->change_returned <= 0)
                    <div class="flex justify-between">
                        <span class="text-slate-600">Guest Credit</span>
                        <span class="font-semibold text-blue-600">{{ format_money($receipt->guest_credit) }}</span>
                    </div>
                @endif
                <div class="flex justify-between border-t border-slate-300 pt-2 mt-1">
                    <span class="font-bold text-slate-800">Remaining Balance</span>
                    <span class="font-bold {{ $receipt->remaining_balance > 0 ? 'text-rose-600' : 'text-emerald-600' }}">{{ format_money($receipt->remaining_balance) }}</span>
                </div>
            </div>
        </div>

        <!-- Financial Summary -->
        <div class="mb-8">
            <h3 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-3">Financial Summary</h3>
            <div class="bg-slate-50 rounded-lg p-4 text-sm space-y-1">
                <div class="flex justify-between">
                    <span class="text-slate-600">Accommodation Charges</span>
                    <span class="font-semibold">{{ format_money($receipt->accommodation_charges) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-600">Other Charges</span>
                    <span class="font-semibold">{{ format_money($receipt->other_charges) }}</span>
                </div>
                @if($receipt->discounts > 0)
                    <div class="flex justify-between">
                        <span class="text-slate-600">Discounts</span>
                        <span class="font-semibold text-rose-600">-{{ format_money($receipt->discounts) }}</span>
                    </div>
                @endif
                <div class="flex justify-between border-t border-slate-200 pt-1 mt-1">
                    <span class="font-bold text-slate-800">Grand Total</span>
                    <span class="font-bold text-slate-800">{{ format_money($receipt->grand_total) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-600">Payments Made</span>
                    <span class="font-semibold text-emerald-600">{{ format_money($receipt->payments_made) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-600">Current Payment</span>
                    <span class="font-semibold text-emerald-600">{{ format_money($receipt->current_payment) }}</span>
                </div>
                <div class="flex justify-between border-t border-slate-300 pt-2">
                    <span class="font-bold text-slate-800">Outstanding Balance</span>
                    <span class="font-bold {{ $receipt->outstanding_balance > 0 ? 'text-rose-600' : 'text-emerald-600' }}">{{ format_money($receipt->outstanding_balance) }}</span>
                </div>
            </div>
        </div>

        <!-- Cashier & QR -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8 border-t-2 border-slate-200 pt-6">
            <div>
                <h3 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-3">Cashier Information</h3>
                <div class="text-sm space-y-1">
                    <p><span class="text-slate-600">Processed By:</span> <span class="font-semibold">{{ $receipt->cashier ?? 'System' }}</span></p>
                    <p><span class="text-slate-600">Printed By:</span> <span class="font-semibold">{{ $receipt->printed_by ?? 'System' }}</span></p>
                    <p><span class="text-slate-600">Print Date:</span> <span class="font-semibold">{{ $receipt->printed_at ? $receipt->printed_at->format('M d, Y H:i') : now()->format('M d, Y H:i') }}</span></p>
                </div>
            </div>
            <div class="text-left md:text-right">
                <h3 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-3">Verification</h3>
                @if($receipt->qr_code_url)
                    <img src="{{ $receipt->qr_code_url }}" alt="Receipt QR Code" class="w-24 h-24 md:ml-auto mb-2">
                @endif
                @if($receipt->verification_url)
                    <p class="text-xs text-slate-500 break-all">{{ $receipt->verification_url }}</p>
                @endif
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center text-sm text-slate-500 border-t border-slate-200 pt-6">
            <p class="font-semibold">Thank you for choosing {{ $appSettings->lodge_name ?? 'our hotel' }}.</p>
            @if(!empty($appSettings->contact_email))
                <p class="mt-1">{{ $appSettings->contact_email }}</p>
            @endif
            @if(!empty($appSettings->website))
                <p class="mt-1">{{ $appSettings->website }}</p>
            @endif
            <p class="mt-3 text-xs">This is an official payment receipt. Please retain this document for your records.</p>
        </div>
    </div>

    <div class="text-center">
        <a href="{{ route('receipts.print', $payment) }}" target="_blank" class="inline-block px-4 py-2 bg-slate-600 hover:bg-slate-700 text-white rounded-lg text-sm font-medium transition">Print Receipt</a>
    </div>
</div>
@endsection
