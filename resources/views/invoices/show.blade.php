@extends('layouts.app')

@section('title', 'Invoice - ' . $invoice->invoice_number)
@section('page-title', 'Invoice Details')

@section('content')
<div class="space-y-6">
    <a href="{{ route('invoices.index') }}" class="inline-flex items-center gap-2 text-slate-600 hover:text-slate-800 transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Back to Invoices
    </a>

    @if($invoice->invoice_status === 'issued')
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 flex items-start gap-3">
            <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <p class="text-sm font-medium text-blue-800">Issued invoices are immutable</p>
                <p class="text-sm text-blue-700 mt-0.5">To change this invoice, void it and create a new one from the folio.</p>
            </div>
        </div>
    @endif

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">{{ $invoice->invoice_number }}</h1>
                <p class="text-slate-500">{{ $invoice->booking->guest_name }} | Room {{ $invoice->booking->room->room_number ?? '-' }}</p>
                <div class="mt-2">
                    <span class="px-3 py-1 text-sm font-medium rounded-full
                        {{ $invoice->invoice_status === 'paid' ? 'bg-emerald-100 text-emerald-700' : ($invoice->invoice_status === 'issued' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700') }}">
                        {{ ucfirst($invoice->invoice_status) }}
                    </span>
                </div>
            </div>
            <div class="text-right">
                <p class="text-sm text-slate-500">Balance Due</p>
                <p class="text-3xl font-bold {{ $invoice->balance_due > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                    {{ format_money($invoice->balance_due) }}
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
            <div class="p-4 bg-slate-50 rounded-lg">
                <p class="text-sm text-slate-500">Total</p>
                <p class="text-lg font-bold text-slate-800">{{ format_money($invoice->grand_total) }}</p>
            </div>
            <div class="p-4 bg-slate-50 rounded-lg">
                <p class="text-sm text-slate-500">Paid</p>
                <p class="text-lg font-bold text-emerald-600">{{ format_money($invoice->amount_paid) }}</p>
            </div>
            <div class="p-4 bg-slate-50 rounded-lg">
                <p class="text-sm text-slate-500">Invoice Date</p>
                <p class="text-lg font-bold text-slate-800">{{ date('M d, Y', strtotime($invoice->invoice_date)) }}</p>
            </div>
        </div>

        <div class="flex flex-wrap gap-3 mt-6">
            @can('invoices.issue')
            @if($invoice->invoice_status === 'draft')
                <form action="{{ route('invoices.issue', $invoice) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg text-sm font-medium transition">Issue Invoice</button>
                </form>
            @endif
            @endcan

            @can('invoices.issue')
            @if($invoice->invoice_status !== 'paid' && $invoice->invoice_status !== 'voided' && $invoice->invoice_status !== 'cancelled')
                <form action="{{ route('invoices.paid', $invoice) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg text-sm font-medium transition">Mark Paid</button>
                </form>
            @endif
            @endcan

            <a href="{{ route('invoices.print', $invoice) }}" target="_blank" class="px-4 py-2 bg-slate-600 hover:bg-slate-700 text-white rounded-lg text-sm font-medium transition">Print</a>

            @can('invoices.cancel')
            @if($invoice->invoice_status !== 'voided' && $invoice->invoice_status !== 'cancelled')
                <form action="{{ route('invoices.cancel', $invoice) }}" method="POST" onsubmit="return confirm('Cancel this invoice?');">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-sm font-medium transition">Cancel</button>
                </form>
            @endif
            @endcan

            @can('invoices.void')
            @if($invoice->invoice_status !== 'voided' && $invoice->invoice_status !== 'cancelled')
                <form action="{{ route('invoices.void', $invoice) }}" method="POST" onsubmit="return confirm('Void this invoice? This cannot be undone.');">
                    @csrf
                    <div class="flex items-center gap-2">
                        <input type="text" name="reason" required
                            placeholder="Reason for voiding *"
                            class="px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-rose-500 w-64">
                        <button type="submit" class="px-4 py-2 bg-rose-500 hover:bg-rose-600 text-white rounded-lg text-sm font-medium transition">Void</button>
                    </div>
                    @error('reason')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </form>
            @endif
            @endcan
        </div>
    </div>
</div>
@endsection
