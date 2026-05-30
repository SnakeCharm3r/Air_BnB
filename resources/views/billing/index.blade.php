@extends('layouts.app')

@section('title', 'Billing & Reconciliation')
@section('page-title', 'Billing & Reconciliation')

@section('content')
<div class="space-y-6">
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-slate-500">Pending Confirmations</p>
                    <p class="text-2xl font-bold text-slate-800">{{ count($pendingConfirmations) }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-rose-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-slate-500">Outstanding Balance</p>
                    <p class="text-2xl font-bold text-slate-800">${{ number_format($totalOutstanding, 2) }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-slate-500">Today's Payments</p>
                    <p class="text-2xl font-bold text-slate-800">${{ number_format($todayPayments, 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Payment Confirmations -->
    @if(count($pendingConfirmations) > 0)
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-200 bg-amber-50">
            <h3 class="text-lg font-semibold text-amber-800 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Pending Payment Confirmations
            </h3>
            <p class="text-sm text-amber-600 mt-1">These bookings require payment confirmation before invoice generation</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Booking Ref</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Guest</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Room</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Check In</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">Amount</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($pendingConfirmations as $booking)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 text-sm font-medium text-slate-800">{{ $booking->booking_ref }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $booking->guest_name }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $booking->room_number }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ date('M d, Y', strtotime($booking->check_in_date)) }}</td>
                            <td class="px-6 py-4 text-right text-sm font-medium text-slate-800">${{ number_format($booking->total_amount, 2) }}</td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('bookings.show', $booking->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-sm font-medium transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Confirm
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Outstanding Bills -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-200">
            <h3 class="text-lg font-semibold text-slate-800">Outstanding Bills</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Booking Ref</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Guest</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Room</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">Total</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">Paid</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">Balance</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($pendingBills as $bill)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 text-sm font-medium text-slate-800">{{ $bill->booking_ref }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $bill->guest_name }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $bill->room_number }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $bill->status === 'checked_in' ? 'bg-emerald-100 text-emerald-700' : 'bg-blue-100 text-blue-700' }}">
                                    {{ ucfirst(str_replace('_', ' ', $bill->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right text-sm font-medium text-slate-800">${{ number_format($bill->total_amount, 2) }}</td>
                            <td class="px-6 py-4 text-right text-sm text-emerald-600">${{ number_format($bill->retainer_paid, 2) }}</td>
                            <td class="px-6 py-4 text-right text-sm font-bold text-rose-600">${{ number_format($bill->balance_due, 2) }}</td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('billing.show', $bill->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-500 hover:bg-blue-600 text-white rounded-lg text-sm font-medium transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    Process Payment
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-slate-400">
                                <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <p>No outstanding bills</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
