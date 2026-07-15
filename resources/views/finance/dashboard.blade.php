@extends('layouts.app')

@section('title', 'Finance Dashboard')
@section('page-title', 'Finance Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Date Filter -->
    <form action="{{ route('finance.dashboard') }}" method="GET" class="flex flex-wrap items-end gap-3 bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Start Date</label>
            <input type="date" name="start_date" value="{{ $startDate }}" class="px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">End Date</label>
            <input type="date" name="end_date" value="{{ $endDate }}" class="px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
        </div>
        <button type="submit" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg text-sm font-medium transition">Filter</button>
    </form>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <p class="text-sm text-slate-500">Today's Revenue</p>
            <p class="text-2xl font-bold text-slate-800">{{ format_money($todaysRevenue) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <p class="text-sm text-slate-500">Today's Payments</p>
            <p class="text-2xl font-bold text-emerald-600">{{ format_money($todaysPayments) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <p class="text-sm text-slate-500">Outstanding Balance</p>
            <p class="text-2xl font-bold text-rose-600">{{ format_money($outstandingBalance) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <p class="text-sm text-slate-500">Pending Invoices</p>
            <p class="text-2xl font-bold text-amber-600">{{ $pendingInvoices }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <p class="text-sm text-slate-500">Deposits</p>
            <p class="text-2xl font-bold text-slate-800">{{ format_money($deposits) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <p class="text-sm text-slate-500">Refunds</p>
            <p class="text-2xl font-bold text-rose-600">{{ format_money($refunds) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <p class="text-sm text-slate-500">Invoices Paid Today</p>
            <p class="text-2xl font-bold text-emerald-600">{{ $paidInvoices }}</p>
        </div>
    </div>

    <!-- Revenue by Charge Type -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <h3 class="text-lg font-semibold text-slate-800 mb-4">Revenue by Department</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @forelse($summary['revenue_by_charge_type'] as $type => $amount)
                <div class="p-4 bg-slate-50 rounded-lg">
                    <p class="text-sm text-slate-500">{{ ucfirst(str_replace('_', ' ', $type)) }}</p>
                    <p class="text-lg font-bold text-slate-800">{{ format_money($amount) }}</p>
                </div>
            @empty
                <p class="text-slate-400 col-span-4">No revenue data</p>
            @endforelse
        </div>
    </div>

    <!-- Recent Payments -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-200 bg-slate-50">
            <h3 class="text-lg font-semibold text-slate-800">Recent Payments</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Guest</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Method</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 uppercase">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentPayments as $payment)
                        <tr>
                            <td class="px-4 py-3 text-sm">{{ date('M d, Y', strtotime($payment->payment_date ?? $payment->created_at)) }}</td>
                            <td class="px-4 py-3 text-sm">{{ $payment->guest_name }}</td>
                            <td class="px-4 py-3 text-sm"><span class="px-2 py-1 text-xs font-medium rounded-full bg-emerald-100 text-emerald-700">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</span></td>
                            <td class="px-4 py-3 text-right font-medium">{{ format_money($payment->amount) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-8 text-center text-slate-400">No recent payments</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Invoices -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-200 bg-slate-50">
            <h3 class="text-lg font-semibold text-slate-800">Recent Invoices</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Invoice #</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Guest</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-slate-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 uppercase">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentInvoices as $invoice)
                        <tr>
                            <td class="px-4 py-3 text-sm font-medium"><a href="{{ route('invoices.show', $invoice->id) }}" class="text-blue-600 hover:underline">{{ $invoice->invoice_number }}</a></td>
                            <td class="px-4 py-3 text-sm">{{ $invoice->guest_name }}</td>
                            <td class="px-4 py-3 text-center"><span class="px-2 py-1 text-xs font-medium rounded-full bg-slate-100 text-slate-700">{{ ucfirst($invoice->invoice_status) }}</span></td>
                            <td class="px-4 py-3 text-right font-medium">{{ format_money($invoice->grand_total) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-8 text-center text-slate-400">No recent invoices</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Top Spending Guests -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-200 bg-slate-50">
            <h3 class="text-lg font-semibold text-slate-800">Top Spending Guests</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Guest</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 uppercase">Bookings</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 uppercase">Total Spent</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($topGuests as $guest)
                        <tr>
                            <td class="px-4 py-3 text-sm">{{ $guest->guest_name }}</td>
                            <td class="px-4 py-3 text-right text-sm">{{ $guest->bookings_count }}</td>
                            <td class="px-4 py-3 text-right font-medium">{{ format_money($guest->total_spent) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="px-4 py-8 text-center text-slate-400">No guest data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
