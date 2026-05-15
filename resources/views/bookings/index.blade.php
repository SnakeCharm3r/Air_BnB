@extends('layouts.app')

@section('title', 'Bookings')
@section('page-title', 'Bookings')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Booking Management</h2>
            <p class="text-sm text-slate-500">Manage guest reservations</p>
        </div>
        <a href="{{ route('bookings.create') }}" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-sm font-medium transition shadow-sm">
            + New Booking
        </a>
    </div>

    <!-- Bookings Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Booking Ref</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Guest</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Room</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Check In</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Check Out</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($bookings as $booking)
                        @php
                            $statusColors = [
                                'confirmed' => 'bg-blue-100 text-blue-700',
                                'checked_in' => 'bg-emerald-100 text-emerald-700',
                                'checked_out' => 'bg-slate-100 text-slate-700',
                                'cancelled' => 'bg-rose-100 text-rose-700'
                            ];
                            $statusClass = $statusColors[$booking->status] ?? 'bg-slate-100 text-slate-700';
                        @endphp
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 text-sm font-medium text-slate-800">{{ $booking->booking_ref }}</td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-slate-800">{{ $booking->guest_name }}</div>
                                <div class="text-xs text-slate-500">{{ $booking->guest_phone ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $booking->room_number }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ date('M d, Y', strtotime($booking->check_in_date)) }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ date('M d, Y', strtotime($booking->check_out_date)) }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $statusClass }}">
                                    {{ str_replace('_', ' ', $booking->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right text-sm font-medium text-slate-800">${{ number_format($booking->total_amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-slate-400">No bookings found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
