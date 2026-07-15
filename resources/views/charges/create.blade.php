@extends('layouts.app')

@section('title', 'Post Charge - ' . $folio->folio_number)
@section('page-title', 'Post Charge')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <a href="{{ route('folios.dashboard', $folio) }}" class="inline-flex items-center gap-2 text-slate-600 hover:text-slate-800 transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Back to Folio
    </a>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <h1 class="text-xl font-bold text-slate-800 mb-1">Post Charge</h1>
        <p class="text-sm text-slate-500 mb-6">Folio {{ $folio->folio_number }} | {{ $folio->booking->guest_name }}</p>

        <form action="{{ route('charges.store') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="folio_id" value="{{ $folio->id }}">

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Charge Type</label>
                <select name="charge_type" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <option value="room">Room</option>
                    <option value="restaurant">Restaurant</option>
                    <option value="laundry">Laundry</option>
                    <option value="mini_bar">Mini Bar</option>
                    <option value="room_service">Room Service</option>
                    <option value="spa">Spa</option>
                    <option value="transport">Transport</option>
                    <option value="damage">Damage</option>
                    <option value="conference">Conference</option>
                    <option value="equipment_hire">Equipment Hire</option>
                    <option value="extra_bed">Extra Bed</option>
                    <option value="early_check_in">Early Check In</option>
                    <option value="late_check_out">Late Check Out</option>
                    <option value="miscellaneous">Miscellaneous</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Description</label>
                <input type="text" name="description" required maxlength="255" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500" placeholder="e.g. Room service dinner">
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Quantity</label>
                    <input type="number" name="quantity" min="0.01" step="0.01" value="1" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Unit Price</label>
                    <input type="number" name="unit_price" min="0" step="0.01" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500" placeholder="0.00">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Discount</label>
                    <input type="number" name="discount_amount" min="0" step="0.01" value="0" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500" placeholder="0.00">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Posting Date</label>
                <input type="date" name="posting_date" value="{{ now()->toDateString() }}" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
            </div>

            <button type="submit" class="w-full px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-sm font-medium transition">Post Charge</button>
        </form>
    </div>
</div>
@endsection
