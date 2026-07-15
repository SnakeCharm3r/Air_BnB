@extends('layouts.app')

@section('title', 'Record Payment - ' . $folio->folio_number)
@section('page-title', 'Record Payment')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <a href="{{ route('folios.dashboard', $folio) }}" class="inline-flex items-center gap-2 text-slate-600 hover:text-slate-800 transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Back to Folio
    </a>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <h1 class="text-xl font-bold text-slate-800 mb-1">Record Payment</h1>
        <p class="text-sm text-slate-500 mb-6">Folio {{ $folio->folio_number }} | {{ $folio->booking->guest_name }} | Balance Due {{ format_money($folio->balance_due) }}</p>

        <form action="{{ route('payments.store') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="folio_id" value="{{ $folio->id }}">

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Amount</label>
                <input type="number" name="amount" min="0" step="0.01" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500" placeholder="0.00">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Payment Method</label>
                <select name="payment_method" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <option value="cash">Cash</option>
                    <option value="card">Card</option>
                    <option value="bank_transfer">Bank Transfer</option>
                    <option value="mobile_money">Mobile Money</option>
                    <option value="credit_account">Credit Account</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Payment Date</label>
                <input type="date" name="payment_date" value="{{ now()->toDateString() }}" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Reference</label>
                <input type="text" name="reference" maxlength="255" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500" placeholder="Transaction reference">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Notes</label>
                <textarea name="notes" rows="2" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500" placeholder="Optional notes"></textarea>
            </div>

            <button type="submit" class="w-full px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg text-sm font-medium transition">Record Payment</button>
        </form>
    </div>
</div>
@endsection
