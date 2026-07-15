@extends('layouts.app')

@section('title', 'Print Invoice - ' . $invoice->invoice_number)
@section('page-title', 'Print Invoice')

@section('content')
<div class="max-w-3xl mx-auto bg-white rounded-xl border border-slate-200 shadow-sm p-8 print:shadow-none print:border-0">
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-slate-800">Invoice</h1>
        <p class="text-slate-500 mt-1">{{ $appSettings->lodge_name ?? 'Hotel' }}</p>
    </div>

    <div class="flex justify-between mb-8">
        <div>
            <p class="font-bold text-slate-800">Bill To:</p>
            <p class="text-slate-600">{{ $invoice->booking?->guest_name ?? 'Guest' }}</p>
            <p class="text-slate-600">Room {{ $invoice->booking?->room?->room_number ?? '-' }}</p>
        </div>
        <div class="text-right">
            <p class="font-bold text-slate-800">Invoice #</p>
            <p class="text-slate-600">{{ $invoice->invoice_number }}</p>
            <p class="font-bold text-slate-800 mt-2">Date</p>
            <p class="text-slate-600">{{ date('M d, Y', strtotime($invoice->invoice_date ?? $invoice->created_at)) }}</p>
        </div>
    </div>

    <table class="w-full mb-8">
        <thead class="bg-slate-50">
            <tr>
                <th class="px-4 py-2 text-left text-sm font-medium text-slate-500 uppercase">Description</th>
                <th class="px-4 py-2 text-right text-sm font-medium text-slate-500 uppercase">Amount</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            <tr>
                <td class="px-4 py-3">Room charges & services</td>
                <td class="px-4 py-3 text-right">{{ format_money($invoice->subtotal) }}</td>
            </tr>
            @if($invoice->tax_amount > 0)
            <tr>
                <td class="px-4 py-3">Tax</td>
                <td class="px-4 py-3 text-right">{{ format_money($invoice->tax_amount) }}</td>
            </tr>
            @endif
            @if($invoice->service_charge > 0)
            <tr>
                <td class="px-4 py-3">Service charge</td>
                <td class="px-4 py-3 text-right">{{ format_money($invoice->service_charge) }}</td>
            </tr>
            @endif
            @if($invoice->discount_amount > 0)
            <tr>
                <td class="px-4 py-3">Discount</td>
                <td class="px-4 py-3 text-right">-{{ format_money($invoice->discount_amount) }}</td>
            </tr>
            @endif
        </tbody>
        <tfoot class="bg-slate-50 font-bold">
            <tr>
                <td class="px-4 py-3 text-right">Grand Total</td>
                <td class="px-4 py-3 text-right">{{ format_money($invoice->grand_total) }}</td>
            </tr>
            <tr>
                <td class="px-4 py-3 text-right">Paid</td>
                <td class="px-4 py-3 text-right">{{ format_money($invoice->amount_paid) }}</td>
            </tr>
            <tr>
                <td class="px-4 py-3 text-right">Balance Due</td>
                <td class="px-4 py-3 text-right">{{ format_money($invoice->balance_due) }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="text-center mt-8">
        <p class="text-sm text-slate-500">Thank you for your business.</p>
        <button onclick="window.print()" class="mt-4 px-4 py-2 bg-slate-600 hover:bg-slate-700 text-white rounded-lg text-sm font-medium print:hidden">Print</button>
    </div>
</div>
@endsection
