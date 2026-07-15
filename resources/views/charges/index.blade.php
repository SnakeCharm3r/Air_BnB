@extends('layouts.app')

@section('title', 'Charges')
@section('page-title', 'Posted Charges')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-200 bg-slate-50">
            <h3 class="text-lg font-semibold text-slate-800">Posted Charges</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Description</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-slate-500 uppercase">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Folio</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 uppercase">Total</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-slate-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($charges as $charge)
                        <tr class="{{ $charge->status === 'reversed' ? 'opacity-60 line-through' : '' }}">
                            <td class="px-4 py-3 text-sm">{{ date('M d, Y', strtotime($charge->posting_date ?? $charge->created_at)) }}</td>
                            <td class="px-4 py-3 text-sm font-medium">{{ $charge->description }}</td>
                            <td class="px-4 py-3 text-center"><span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-700">{{ ucfirst(str_replace('_', ' ', $charge->charge_type)) }}</span></td>
                            <td class="px-4 py-3 text-sm">{{ $charge->folio?->folio_number ?? '-' }}</td>
                            <td class="px-4 py-3 text-right font-medium">{{ format_money($charge->total_amount) }}</td>
                            <td class="px-4 py-3 text-center"><span class="px-2 py-1 text-xs font-medium rounded-full {{ $charge->status === 'posted' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">{{ ucfirst($charge->status) }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-8 text-center text-slate-400">No charges found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($charges->hasPages())
            <div class="p-4 border-t border-slate-200">{{ $charges->links() }}</div>
        @endif
    </div>
</div>
@endsection
