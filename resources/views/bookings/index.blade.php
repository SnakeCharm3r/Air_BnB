@extends('layouts.app')

@section('title', 'Bookings')
@section('page-title', 'Bookings')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Booking Management</h2>
            <p class="text-sm text-slate-500">Manage guest reservations and payments</p>
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
                        <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($bookings as $booking)
                        @php
                            $statusColors = [
                                'pending' => 'bg-amber-100 text-amber-700',
                                'confirmed' => 'bg-blue-100 text-blue-700',
                                'checked_in' => 'bg-emerald-100 text-emerald-700',
                                'checked_out' => 'bg-slate-100 text-slate-700',
                                'cancelled' => 'bg-rose-100 text-rose-700'
                            ];
                            $statusClass = $statusColors[$booking->status] ?? 'bg-slate-100 text-slate-700';
                        @endphp
                        <tr class="hover:bg-slate-50 {{ $booking->status === 'pending' ? 'bg-amber-50/50' : '' }}">
                            <td class="px-6 py-4 text-sm font-medium text-slate-800">
                                <a href="{{ route('bookings.show', $booking->id) }}" class="hover:text-amber-600 transition">{{ $booking->booking_ref }}</a>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-slate-800">{{ $booking->guest_name }}</div>
                                <div class="text-xs text-slate-500">{{ $booking->guest_phone ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $booking->room_number }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ date('M d, Y', strtotime($booking->check_in_date)) }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ date('M d, Y', strtotime($booking->check_out_date)) }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $statusClass }}">
                                    {{ ucfirst($booking->status) }}
                                </span>
                                @if($booking->status === 'pending')
                                    <div class="text-xs text-amber-600 mt-1">Awaiting Payment</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right text-sm font-medium text-slate-800">${{ number_format($booking->total_amount, 2) }}</td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('bookings.show', $booking->id) }}" class="p-1.5 text-slate-400 hover:text-blue-600 transition" title="View">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    @if($booking->status === 'pending')
                                        <a href="{{ route('bookings.show', $booking->id) }}#confirm" class="p-1.5 text-amber-500 hover:text-amber-600 transition" title="Confirm Payment">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </a>
                                    @endif
                                    <a href="{{ route('bookings.edit', $booking->id) }}" class="p-1.5 text-slate-400 hover:text-amber-600 transition" title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-slate-400">No bookings found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
