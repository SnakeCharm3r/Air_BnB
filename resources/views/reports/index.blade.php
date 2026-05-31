@extends('layouts.app')

@section('title', 'Reports Dashboard')
@section('page-title', 'Reports Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-slate-500">Total Rooms</p>
                    <p class="text-2xl font-bold text-slate-800">{{ $totalRooms ?? 0 }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-rose-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-slate-500">Occupied</p>
                    <p class="text-2xl font-bold text-slate-800">{{ $occupiedRooms ?? 0 }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-slate-500">Vacant</p>
                    <p class="text-2xl font-bold text-slate-800">{{ $vacantRooms ?? 0 }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-slate-500">Outstanding Debt</p>
                    <p class="text-2xl font-bold text-rose-600">${{ number_format($totalOutstanding ?? 0, 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Room Status Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-200 bg-slate-50">
            <h3 class="text-lg font-semibold text-slate-800">Room Status</h3>
            <p class="text-sm text-slate-500">Current bookings and vacant rooms</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Room #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Type</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Guest</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Check In</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Check Out</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">Rate</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($rooms ?? [] as $room)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 text-sm font-medium text-slate-800">{{ $room->room_number }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $room->room_type ?? 'Standard' }}</td>
                            <td class="px-6 py-4 text-center">
                                @if($room->current_status === 'occupied')
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-rose-100 text-rose-700">Occupied</span>
                                @elseif($room->current_status === 'available')
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-emerald-100 text-emerald-700">Available</span>
                                @elseif($room->current_status === 'maintenance')
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-amber-100 text-amber-700">Maintenance</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-slate-100 text-slate-600">{{ ucfirst($room->current_status) }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $room->guest_name ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600">
                                {{ $room->check_in_date ? date('M d, Y', strtotime($room->check_in_date)) : '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">
                                {{ $room->check_out_date ? date('M d, Y', strtotime($room->check_out_date)) : '-' }}
                            </td>
                            <td class="px-6 py-4 text-right text-sm font-medium">${{ number_format($room->base_price ?? 0, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-slate-400">No rooms data available</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Sales Reports -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Daily Sales -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-slate-200 bg-slate-50">
                <h3 class="text-lg font-semibold text-slate-800">Daily Sales (Last 7 Days)</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Date</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 uppercase">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($dailySales ?? [] as $sale)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 text-sm">{{ date('M d, Y', strtotime($sale->date)) }}</td>
                                <td class="px-4 py-3 text-right text-sm font-medium text-emerald-600">${{ number_format($sale->total, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-4 py-8 text-center text-slate-400">No sales data available</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Weekly Sales -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-slate-200 bg-slate-50">
                <h3 class="text-lg font-semibold text-slate-800">Weekly Sales (Last 4 Weeks)</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Week</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 uppercase">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($weeklySales ?? [] as $sale)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 text-sm">{{ date('M d', strtotime($sale->week_start)) }} - {{ date('M d, Y', strtotime($sale->week_end)) }}</td>
                                <td class="px-4 py-3 text-right text-sm font-medium text-emerald-600">${{ number_format($sale->total, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-4 py-8 text-center text-slate-400">No sales data available</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Monthly Sales -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-slate-200 bg-slate-50">
                <h3 class="text-lg font-semibold text-slate-800">Monthly Sales (Last 12 Months)</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Month</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 uppercase">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($monthlySales ?? [] as $sale)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 text-sm">{{ date('F Y', strtotime($sale->month . '-01')) }}</td>
                                <td class="px-4 py-3 text-right text-sm font-medium text-emerald-600">${{ number_format($sale->total, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-4 py-8 text-center text-slate-400">No sales data available</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Yearly Sales -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-slate-200 bg-slate-50">
                <h3 class="text-lg font-semibold text-slate-800">Yearly Sales</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Year</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 uppercase">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($yearlySales ?? [] as $sale)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 text-sm">{{ $sale->year }}</td>
                                <td class="px-4 py-3 text-right text-sm font-medium text-emerald-600">${{ number_format($sale->total, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-4 py-8 text-center text-slate-400">No sales data available</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Outstanding Debt -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-200 bg-slate-50">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-slate-800">Outstanding Debt</h3>
                    <p class="text-sm text-slate-500">Pending, confirmed, and checked-in bookings with balance due</p>
                </div>
                <div class="flex items-center gap-4">
                    <div class="text-right">
                        <p class="text-xs text-slate-500">Pending: <span class="font-medium text-amber-600">${{ number_format($totalPendingInvoices ?? 0, 2) }}</span></p>
                        <p class="text-xs text-slate-500">Confirmed: <span class="font-medium text-blue-600">${{ number_format($totalConfirmedInvoices ?? 0, 2) }}</span></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Booking Ref</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Guest</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Room</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">Total</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">Paid</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">Balance Due</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($outstandingDebt ?? [] as $debt)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 text-sm font-medium text-slate-800">{{ $debt->booking_ref }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $debt->guest_name }}</td>
                            <td class="px-6 py-4 text-center">
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-amber-100 text-amber-700',
                                        'confirmed' => 'bg-blue-100 text-blue-700',
                                        'checked_in' => 'bg-emerald-100 text-emerald-700'
                                    ];
                                    $statusClass = $statusColors[$debt->status] ?? 'bg-slate-100 text-slate-600';
                                @endphp
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $statusClass }}">
                                    {{ ucfirst($debt->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $debt->room_number }}</td>
                            <td class="px-6 py-4 text-right text-sm font-medium">${{ number_format($debt->total_amount, 2) }}</td>
                            <td class="px-6 py-4 text-right text-sm text-emerald-600">${{ number_format($debt->retainer_paid, 2) }}</td>
                            <td class="px-6 py-4 text-right text-sm font-bold text-rose-600">${{ number_format($debt->balance_due, 2) }}</td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('billing.show', $debt->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-500 hover:bg-blue-600 text-white rounded-lg text-sm font-medium transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a1 1 0 11-2 0 1 1 0 012 0z"/>
                                    </svg>
                                    Pay
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
                                <p>No outstanding debt</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-slate-50 font-semibold">
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-right">Total Outstanding:</td>
                        <td class="px-6 py-4 text-right text-rose-600">${{ number_format($totalOutstanding ?? 0, 2) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
