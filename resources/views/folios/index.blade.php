@extends('layouts.app')

@section('title', 'Folios')
@section('page-title', 'Guest Folios')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
        <form method="GET" action="{{ route('folios.index') }}" class="flex flex-wrap items-end gap-4">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <div class="flex-1 min-w-[160px]">
                <label for="date" class="block text-xs font-medium text-slate-500 uppercase mb-1">Date</label>
                <input type="date" id="date" name="date" value="{{ $filters['date'] ?? '' }}" class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div class="flex-1 min-w-[160px]">
                <label for="month" class="block text-xs font-medium text-slate-500 uppercase mb-1">Month</label>
                <input type="month" id="month" name="month" value="{{ $filters['month'] ?? '' }}" class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div class="flex-1 min-w-[160px]">
                <label for="search" class="block text-xs font-medium text-slate-500 uppercase mb-1">Search</label>
                <input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="Guest, ref, folio..." class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
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
                <a href="{{ route('folios.index') }}" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-lg text-sm font-medium transition">Clear</a>
            </div>
        </form>
    </div>

    <div class="flex flex-wrap gap-3">
        <a href="{{ request()->fullUrlWithQuery(['status' => 'open']) }}" class="px-4 py-2 bg-white border border-slate-200 rounded-lg text-sm hover:bg-slate-50">Open</a>
        <a href="{{ request()->fullUrlWithQuery(['status' => 'closed']) }}" class="px-4 py-2 bg-white border border-slate-200 rounded-lg text-sm hover:bg-slate-50">Closed</a>
        <a href="{{ request()->fullUrlWithQuery(['status' => 'void']) }}" class="px-4 py-2 bg-white border border-slate-200 rounded-lg text-sm hover:bg-slate-50">Void</a>
        <a href="{{ route('folios.index') }}" class="px-4 py-2 bg-white border border-slate-200 rounded-lg text-sm hover:bg-slate-50">All</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <p class="text-sm text-slate-500">Open Balance</p>
            <p class="text-2xl font-bold text-rose-600">{{ format_money($summary['open_balance']) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <p class="text-sm text-slate-500">Open Folios</p>
            <p class="text-2xl font-bold text-slate-800">{{ $summary['open_count'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <p class="text-sm text-slate-500">Closed Folios</p>
            <p class="text-2xl font-bold text-slate-800">{{ $summary['closed_count'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <p class="text-sm text-slate-500">Overdue</p>
            <p class="text-2xl font-bold text-amber-600">{{ $summary['overdue_count'] }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Folio #</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Guest</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-slate-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 uppercase">Total</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 uppercase">Paid</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 uppercase">Balance</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-slate-500 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($folios as $folio)
                        <tr>
                            <td class="px-4 py-3 text-sm font-medium">{{ $folio->folio_number }}</td>
                            <td class="px-4 py-3 text-sm">{{ $folio->booking?->guest_name ?? $folio->guest?->name }}</td>
                            <td class="px-4 py-3 text-center"><span class="px-2 py-1 text-xs font-medium rounded-full {{ $folio->status === 'open' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-700' }}">{{ ucfirst($folio->status) }}</span></td>
                            <td class="px-4 py-3 text-right text-sm">{{ format_money($folio->total_amount) }}</td>
                            <td class="px-4 py-3 text-right text-sm text-emerald-600">{{ format_money($folio->amount_paid) }}</td>
                            <td class="px-4 py-3 text-right text-sm font-bold {{ $folio->balance_due > 0 ? 'text-rose-600' : 'text-emerald-600' }}">{{ format_money($folio->balance_due) }}</td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('folios.dashboard', $folio) }}" class="px-3 py-1.5 bg-blue-500 hover:bg-blue-600 text-white rounded-lg text-sm">Open</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-8 text-center text-slate-400">No folios found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($folios->hasPages())
            <div class="p-4 border-t border-slate-200">{{ $folios->links() }}</div>
        @endif
    </div>
</div>
@endsection
