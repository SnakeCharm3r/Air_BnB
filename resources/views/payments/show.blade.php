@extends('layouts.app')

@section('title', 'Payment Details')
@section('page-title', 'Payment Details')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <a href="{{ route('payments.index') }}" class="inline-flex items-center gap-2 text-slate-600 hover:text-slate-800 transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Back to Payments
    </a>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">{{ format_money($payment->amount) }}</h1>
                <p class="text-slate-500">{{ $payment->booking?->guest_name ?? 'Guest' }} | {{ $payment->folio?->folio_number ?? '-' }}</p>
                <div class="mt-2">
                    <span class="px-3 py-1 text-sm font-medium rounded-full {{ $payment->is_void ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-700' }}">
                        {{ $payment->is_void ? 'Void' : ucfirst($payment->payment_status) }}
                    </span>
                </div>
            </div>
            <div class="text-right">
                <p class="text-sm text-slate-500">Receipt</p>
                <p class="text-lg font-bold text-slate-800">{{ $payment->receipt_number ?? 'N/A' }}</p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 mt-6">
            <div class="p-3 bg-slate-50 rounded-lg">
                <p class="text-sm text-slate-500">Method</p>
                <p class="font-medium">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</p>
            </div>
            <div class="p-3 bg-slate-50 rounded-lg">
                <p class="text-sm text-slate-500">Date</p>
                <p class="font-medium">{{ date('M d, Y', strtotime($payment->payment_date ?? $payment->created_at)) }}</p>
            </div>
            <div class="p-3 bg-slate-50 rounded-lg">
                <p class="text-sm text-slate-500">Reference</p>
                <p class="font-medium">{{ $payment->reference ?? '-' }}</p>
            </div>
            <div class="p-3 bg-slate-50 rounded-lg">
                <p class="text-sm text-slate-500">Cashier</p>
                <p class="font-medium">{{ $payment->createdBy?->name ?? 'System' }}</p>
            </div>
        </div>

        @if($payment->notes)
            <div class="mt-4 p-3 bg-slate-50 rounded-lg">
                <p class="text-sm text-slate-500">Notes</p>
                <p>{{ $payment->notes }}</p>
            </div>
        @endif

        <div class="flex flex-wrap gap-3 mt-6">
            @if(!$payment->is_void && !$payment->isRefund)
                <a href="{{ route('receipts.show', $payment) }}" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg text-sm font-medium transition">View Receipt</a>
                <a href="{{ route('receipts.print', $payment) }}" target="_blank" class="px-4 py-2 bg-slate-600 hover:bg-slate-700 text-white rounded-lg text-sm font-medium transition">Print Receipt</a>

                @can('payments.refund')
                <form action="{{ route('payments.refund', $payment) }}" method="POST" onsubmit="return confirm('Refund this payment?');">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-sm font-medium transition">Refund</button>
                </form>
                @endcan

                @can('payments.void')
                <form action="{{ route('payments.void', $payment) }}" method="POST" onsubmit="return confirm('Void this payment?');">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-rose-500 hover:bg-rose-600 text-white rounded-lg text-sm font-medium transition">Void</button>
                </form>
                @endcan
            @endif
        </div>
    </div>
</div>
@endsection
