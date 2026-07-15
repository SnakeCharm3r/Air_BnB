@extends('layouts.app')

@section('title', 'Folio Dashboard - ' . $folio->folio_number)
@section('page-title', 'Folio Dashboard')

@section('content')
<div class="space-y-6">
    <div class="flex items-start justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Folio {{ $folio->folio_number }}</h1>
            <p class="text-slate-500">{{ $folio->booking->guest_name }} | Room {{ $folio->booking->room->room_number ?? '-' }}</p>
            <div class="flex items-center gap-2 mt-2">
                <span class="px-3 py-1 text-sm font-medium rounded-full {{ $folio->status === 'open' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-700' }}">
                    {{ ucfirst($folio->status) }}
                </span>
                <span class="px-3 py-1 text-sm font-medium rounded-full {{ $folio->balance_due > 0 ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-700' }}">
                    {{ ucfirst(str_replace('_', ' ', $folio->payment_status)) }}
                </span>
            </div>
        </div>
        <div class="text-right">
            <p class="text-sm text-slate-500">Balance Due</p>
            <p class="text-3xl font-bold {{ $folio->balance_due > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                {{ format_money($folio->balance_due) }}
            </p>
        </div>
    </div>

    <!-- Balance Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
            <p class="text-sm text-slate-500">Subtotal</p>
            <p class="text-xl font-bold text-slate-800">{{ format_money($stats['subtotal']) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
            <p class="text-sm text-slate-500">Discounts</p>
            <p class="text-xl font-bold text-slate-800">{{ format_money($stats['discounts']) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
            <p class="text-sm text-slate-500">Tax</p>
            <p class="text-xl font-bold text-slate-800">{{ format_money($stats['tax']) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
            <p class="text-sm text-slate-500">Total</p>
            <p class="text-xl font-bold text-slate-800">{{ format_money($stats['total']) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
            <p class="text-sm text-slate-500">Paid</p>
            <p class="text-xl font-bold text-emerald-600">{{ format_money($stats['paid']) }}</p>
        </div>
    </div>

    <!-- Actions -->
    <div class="flex flex-wrap gap-3">
        <a href="{{ route('charges.create', ['folio_id' => $folio->id]) }}" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-sm font-medium transition">Post Charge</a>
        <a href="{{ route('payments.create', ['folio_id' => $folio->id]) }}" class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg text-sm font-medium transition">Record Payment</a>
        <a href="{{ route('invoices.generate') }}?folio_id={{ $folio->id }}" onclick="event.preventDefault(); document.getElementById('generate-invoice-form').submit();" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg text-sm font-medium transition">Generate Invoice</a>
        <form id="generate-invoice-form" action="{{ route('invoices.generate') }}" method="POST" class="hidden">
            @csrf
            <input type="hidden" name="folio_id" value="{{ $folio->id }}">
        </form>
        @if($folio->isOpen())
            <form action="{{ route('folios.close', $folio) }}" method="POST" onsubmit="return confirm('Close this folio?');">
                @csrf
                <button type="submit" class="px-4 py-2 bg-slate-600 hover:bg-slate-700 text-white rounded-lg text-sm font-medium transition">Close Folio</button>
            </form>
        @endif
    </div>

    <!-- Charges -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-200 bg-slate-50">
            <h3 class="text-lg font-semibold text-slate-800">Current Charges</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Description</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-slate-500 uppercase">Type</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 uppercase">Qty</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 uppercase">Unit</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 uppercase">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($charges as $charge)
                        <tr class="{{ $charge->status === 'reversed' ? 'opacity-60 line-through' : '' }}">
                            <td class="px-4 py-3">
                                <p class="font-medium text-slate-800">{{ $charge->description }}</p>
                                <p class="text-sm text-slate-500">{{ date('M d, Y', strtotime($charge->posting_date ?? $charge->created_at)) }}</p>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-700">{{ ucfirst(str_replace('_', ' ', $charge->charge_type)) }}</span>
                            </td>
                            <td class="px-4 py-3 text-right text-sm text-slate-600">{{ number_format($charge->quantity, 2) }}</td>
                            <td class="px-4 py-3 text-right text-sm text-slate-600">{{ format_money($charge->unit_price) }}</td>
                            <td class="px-4 py-3 text-right font-medium">{{ format_money($charge->total_amount) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-slate-400">No charges posted</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($charges->hasPages())
            <div class="p-4 border-t border-slate-200">{{ $charges->links() }}</div>
        @endif
    </div>

    <!-- Payments -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-200 bg-slate-50">
            <h3 class="text-lg font-semibold text-slate-800">Payments</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Method</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Receipt #</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-slate-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 uppercase">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($payments as $payment)
                        <tr class="{{ $payment->is_void ? 'opacity-60 line-through' : '' }}">
                            <td class="px-4 py-3 text-sm">{{ date('M d, Y', strtotime($payment->payment_date ?? $payment->created_at)) }}</td>
                            <td class="px-4 py-3"><span class="px-2 py-1 text-xs font-medium rounded-full bg-emerald-100 text-emerald-700">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</span></td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $payment->receipt_number ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-center"><span class="px-2 py-1 text-xs font-medium rounded-full {{ $payment->is_void ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-700' }}">{{ $payment->is_void ? 'Void' : ucfirst($payment->payment_status) }}</span></td>
                            <td class="px-4 py-3 text-right font-medium {{ $payment->amount < 0 ? 'text-rose-600' : 'text-emerald-600' }}">{{ format_money($payment->amount) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-slate-400">No payments recorded</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($payments->hasPages())
            <div class="p-4 border-t border-slate-200">{{ $payments->links() }}</div>
        @endif
    </div>

    <!-- Invoices -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-200 bg-slate-50">
            <h3 class="text-lg font-semibold text-slate-800">Invoice History</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Invoice #</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Date</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-slate-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 uppercase">Total</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 uppercase">Balance</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($invoices as $invoice)
                        <tr>
                            <td class="px-4 py-3 text-sm font-medium"><a href="{{ route('invoices.show', $invoice) }}" class="text-blue-600 hover:underline">{{ $invoice->invoice_number }}</a></td>
                            <td class="px-4 py-3 text-sm">{{ date('M d, Y', strtotime($invoice->invoice_date ?? $invoice->created_at)) }}</td>
                            <td class="px-4 py-3 text-center"><span class="px-2 py-1 text-xs font-medium rounded-full bg-slate-100 text-slate-700">{{ ucfirst($invoice->invoice_status) }}</span></td>
                            <td class="px-4 py-3 text-right font-medium">{{ format_money($invoice->grand_total) }}</td>
                            <td class="px-4 py-3 text-right font-medium">{{ format_money($invoice->balance_due) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-slate-400">No invoices generated</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($invoices->hasPages())
            <div class="p-4 border-t border-slate-200">{{ $invoices->links() }}</div>
        @endif
    </div>
</div>
@endsection
