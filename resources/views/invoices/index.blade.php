@extends('layouts.app')

@section('title', 'Invoices')
@section('page-title', 'Invoices')

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <p class="text-sm text-slate-500">Draft</p>
            <p class="text-2xl font-bold text-slate-800">{{ $summary['draft'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <p class="text-sm text-slate-500">Issued</p>
            <p class="text-2xl font-bold text-blue-600">{{ $summary['issued'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <p class="text-sm text-slate-500">Paid</p>
            <p class="text-2xl font-bold text-emerald-600">{{ $summary['paid'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <p class="text-sm text-slate-500">Outstanding</p>
            <p class="text-2xl font-bold text-rose-600">{{ format_money($summary['outstanding']) }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
        <form method="GET" action="{{ route('invoices.index') }}" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[160px]">
                <label for="date" class="block text-xs font-medium text-slate-500 uppercase mb-1">Date</label>
                <input type="date" id="date" name="date" value="{{ $filters['date'] ?? '' }}" class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div class="flex-1 min-w-[160px]">
                <label for="month" class="block text-xs font-medium text-slate-500 uppercase mb-1">Month</label>
                <input type="month" id="month" name="month" value="{{ $filters['month'] ?? '' }}" class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div class="w-32">
                <label for="per_page" class="block text-xs font-medium text-slate-500 uppercase mb-1">Show</label>
                <select id="per_page" name="per_page" onchange="this.form.submit()" class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                    @foreach([10, 25, 50, 100] as $size)
                        <option value="{{ $size }}" {{ ($filters['perPage'] ?? 25) == $size ? 'selected' : '' }}>{{ $size }} entries</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition">Filter</button>
                <a href="{{ route('invoices.index') }}" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-lg text-sm font-medium transition">Clear</a>
                <a href="{{ route('invoices.export', request()->only(['date', 'month', 'status', 'folio_id'])) }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-medium transition">Export Excel</a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-200 bg-slate-50">
            <h3 class="text-lg font-semibold text-slate-800">Invoice List</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Invoice #</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Guest / Room</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Check-in</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Charges</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-slate-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 uppercase">Total</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 uppercase">Paid</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 uppercase">Pending</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-slate-500 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($invoices as $invoice)
                        <tr class="hover:bg-slate-50 align-top">
                            <td class="px-4 py-3 text-sm font-medium">{{ $invoice->invoice_number }}</td>
                            <td class="px-4 py-3 text-sm">
                                <div class="font-medium text-slate-800">{{ $invoice->guest_name ?? $invoice->booking?->guest_name ?? '-' }}</div>
                                <div class="text-xs text-slate-500">Room {{ $invoice->room_number ?? $invoice->booking?->room?->room_number ?? 'N/A' }}</div>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600">
                                {{ $invoice->check_in_date ? date('d M Y', strtotime($invoice->check_in_date)) : '-' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600">
                                @php
                                    $chargeCount = $invoice->folio?->charges?->count() ?? 0;
                                    $chargeList = $invoice->folio?->charges?->pluck('description')->filter()->implode(', ') ?? '';
                                @endphp
                                <div class="text-xs font-medium text-slate-700">{{ $chargeCount }} charge{{ $chargeCount === 1 ? '' : 's' }}</div>
                                @if($chargeList)
                                    <div class="text-xs text-slate-500 truncate max-w-[180px]" title="{{ $chargeList }}">{{ $chargeList }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center"><span class="px-2 py-1 text-xs font-medium rounded-full
                                {{ $invoice->invoice_status === 'paid' ? 'bg-emerald-100 text-emerald-700' : ($invoice->invoice_status === 'issued' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700') }}
                            ">{{ ucfirst($invoice->invoice_status) }}</span></td>
                            <td class="px-4 py-3 text-right font-medium">{{ format_money($invoice->grand_total) }}</td>
                            <td class="px-4 py-3 text-right font-medium text-emerald-600">{{ format_money($invoice->amount_paid) }}</td>
                            <td class="px-4 py-3 text-right font-medium {{ $invoice->balance_due > 0 ? 'text-rose-600' : 'text-emerald-600' }}">{{ format_money($invoice->balance_due) }}</td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('invoices.show', $invoice) }}" class="inline-flex items-center justify-center w-8 h-8 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition" title="View Invoice">
                                    @include('components.icons.eye', ['class' => 'w-5 h-5'])
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="px-4 py-8 text-center text-slate-400">No invoices found</td></tr>
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
