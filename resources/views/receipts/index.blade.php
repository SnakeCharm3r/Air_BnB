@extends('layouts.app')

@section('title', 'Receipts')
@section('page-title', 'Receipts')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
        <form method="GET" action="{{ route('receipts.index') }}" class="flex flex-wrap items-end gap-4">
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
                <a href="{{ route('receipts.index') }}" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-lg text-sm font-medium transition">Clear</a>
                <a href="{{ route('receipts.export', request()->only(['date', 'month'])) }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-medium transition">Export Excel</a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-200 bg-slate-50">
            <h3 class="text-lg font-semibold text-slate-800">Receipts</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Receipt #</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Guest</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-slate-500 uppercase">Method</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 uppercase">Amount</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-slate-500 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($receipts as $payment)
                        <tr>
                            <td class="px-4 py-3 text-sm font-medium">{{ $payment->receipt_number }}</td>
                            <td class="px-4 py-3 text-sm">{{ $payment->booking?->guest_name ?? '-' }}</td>
                            <td class="px-4 py-3 text-center"><span class="px-2 py-1 text-xs font-medium rounded-full bg-emerald-100 text-emerald-700">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</span></td>
                            <td class="px-4 py-3 text-right font-medium">{{ format_money($payment->amount) }}</td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('receipts.show', $payment) }}" class="inline-flex items-center justify-center w-8 h-8 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition" title="View Receipt">
                                    @include('components.icons.eye', ['class' => 'w-5 h-5'])
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-8 text-center text-slate-400">No receipts found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($receipts->hasPages())
            <div class="p-4 border-t border-slate-200">{{ $receipts->links() }}</div>
        @endif
    </div>
</div>
@endsection
