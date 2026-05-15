@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-8">
    <!-- Page Header -->
    <div class="border-b border-slate-200 pb-4">
        <h1 class="text-2xl font-bold text-slate-900">Dashboard</h1>
        <p class="text-sm text-slate-500 mt-1">Welcome back! Here's what's happening today.</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Rooms -->
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500">Total Rooms</p>
                    <p class="text-2xl font-bold text-slate-800 mt-1">{{ $stats['totalRooms'] }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-2">
                <span class="text-xs px-2 py-1 bg-emerald-100 text-emerald-700 rounded-full">{{ $stats['availableRooms'] }} available</span>
                <span class="text-xs px-2 py-1 bg-rose-100 text-rose-700 rounded-full">{{ $stats['occupiedRooms'] }} occupied</span>
            </div>
        </div>

        <!-- Today's Check-ins -->
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500">Today's Check-ins</p>
                    <p class="text-2xl font-bold text-slate-800 mt-1">{{ $stats['todayCheckIns'] }}</p>
                </div>
                <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
            <p class="text-xs text-slate-400 mt-3">Arriving guests</p>
        </div>

        <!-- Today's Check-outs -->
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500">Today's Check-outs</p>
                    <p class="text-2xl font-bold text-slate-800 mt-1">{{ $stats['todayCheckOuts'] }}</p>
                </div>
                <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4 4m0 0l-4 4m4-4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </div>
            </div>
            <p class="text-xs text-slate-400 mt-3">Departing guests</p>
        </div>

        <!-- Month Revenue -->
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500">Month Revenue</p>
                    <p class="text-2xl font-bold text-slate-800 mt-1">${{ number_format($stats['monthRevenue'], 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-violet-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-xs text-slate-400 mt-3">This month</p>
        </div>
    </div>

    <!-- Occupancy Rate & Secondary Stats -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Occupancy Chart -->
        <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm lg:col-span-2">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-slate-800">Occupancy Rate</h3>
                <span class="text-2xl font-bold text-amber-600">{{ $stats['occupancyRate'] }}%</span>
            </div>
            <div class="relative h-4 bg-slate-100 rounded-full overflow-hidden">
                <div class="absolute left-0 top-0 h-full bg-gradient-to-r from-amber-500 to-amber-400 rounded-full transition-all duration-1000" style="width: {{ $stats['occupancyRate'] }}%"></div>
            </div>
            <p class="text-sm text-slate-500 mt-3">
                {{ $stats['occupiedRooms'] }} of {{ $stats['totalRooms'] }} rooms occupied
            </p>
        </div>

        <!-- Quick Stats -->
        <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm space-y-4">
            <h3 class="font-semibold text-slate-800">Quick Overview</h3>
            
            <div class="flex items-center justify-between py-2 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <span class="text-sm text-slate-600">Active Staff</span>
                </div>
                <span class="font-semibold text-slate-800">{{ $stats['activeStaff'] }}</span>
            </div>

            <div class="flex items-center justify-between py-2 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <span class="text-sm text-slate-600">Low Stock Items</span>
                </div>
                <span class="font-semibold text-slate-800">{{ $stats['lowStockItems'] }}</span>
            </div>

            <div class="flex items-center justify-between py-2">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-rose-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                    </div>
                    <span class="text-sm text-slate-600">Pending Tasks</span>
                </div>
                <span class="font-semibold text-slate-800">{{ $stats['pendingTasks'] }}</span>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
        <div class="p-6 border-b border-slate-100">
            <h3 class="font-semibold text-slate-800">Recent Bookings</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Guest</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Room</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Check In</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Check Out</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentBookings as $booking)
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
                            <td class="px-6 py-4">
                                <div class="font-medium text-slate-800">{{ $booking->guest_name }}</div>
                                <div class="text-xs text-slate-500">{{ $booking->booking_ref }}</div>
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
                            <td colspan="6" class="px-6 py-8 text-center text-slate-400">No recent bookings</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
