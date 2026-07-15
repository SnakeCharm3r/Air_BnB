@extends('layouts.app')

@section('title', 'Checkout - ' . $booking->booking_ref)
@section('page-title', 'Guest Checkout')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <a href="{{ route('bookings.show', $booking) }}" class="inline-flex items-center gap-2 text-slate-600 hover:text-slate-800 transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Back to Booking
    </a>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <h1 class="text-2xl font-bold text-slate-800 mb-1">Checkout</h1>
        <p class="text-slate-500">{{ $booking->guest_name }} | Room {{ $booking->room->room_number ?? '-' }} | Folio {{ $folio->folio_number }}</p>
    </div>

    <!-- Final Charges Summary -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
            <p class="text-sm text-slate-500">Subtotal</p>
            <p class="text-xl font-bold text-slate-800">{{ format_money($summary['subtotal']) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
            <p class="text-sm text-slate-500">Discounts</p>
            <p class="text-xl font-bold text-slate-800">{{ format_money($summary['discounts']) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
            <p class="text-sm text-slate-500">Total</p>
            <p class="text-xl font-bold text-slate-800">{{ format_money($summary['total']) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
            <p class="text-sm text-slate-500">Paid</p>
            <p class="text-xl font-bold text-emerald-600">{{ format_money($summary['paid']) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4 md:col-span-2">
            <p class="text-sm text-slate-500">Outstanding Balance</p>
            <p class="text-xl font-bold {{ $summary['balance'] > 0 ? 'text-rose-600' : 'text-emerald-600' }}">{{ format_money($summary['balance']) }}</p>
        </div>
    </div>

    <!-- Final Payment Form -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <h2 class="text-lg font-semibold text-slate-800 mb-4">Final Payment</h2>
        <form action="{{ route('checkout.store', $booking) }}" method="POST" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Amount to Pay</label>
                    <input type="number" name="amount" min="0" step="0.01" value="{{ $summary['balance'] }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Payment Method</label>
                    <select name="payment_method" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                        <option value="cash">Cash</option>
                        <option value="card">Card</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="mobile_money">Mobile Money</option>
                        <option value="credit_account">Credit Account</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Reference</label>
                <input type="text" name="reference" maxlength="255" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500" placeholder="Transaction reference">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Notes</label>
                <textarea name="notes" rows="2" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500" placeholder="Checkout notes"></textarea>
            </div>

            <button type="submit" class="w-full px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition" onclick="return confirm('Complete checkout?');">
                Complete Checkout
            </button>
        </form>
    </div>
</div>
@endsection
