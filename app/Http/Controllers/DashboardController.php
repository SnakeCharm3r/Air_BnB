<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function stats(Request $request)
    {
        $today = now()->toDateString();
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();

        $totalRooms     = DB::table('rooms')->count();
        $availableRooms = DB::table('rooms')->where('status', 'available')->count();
        $occupiedRooms  = DB::table('rooms')->where('status', 'occupied')->count();

        $todayCheckIns  = DB::table('bookings')
            ->where('check_in_date', $today)
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->count();

        $todayCheckOuts = DB::table('bookings')
            ->where('check_out_date', $today)
            ->where('status', 'checked_in')
            ->count();

        $monthRevenue = (float) DB::table('bookings')
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->whereIn('status', ['checked_out', 'confirmed', 'checked_in'])
            ->sum('total_amount');

        $totalRevenue = (float) DB::table('bookings')
            ->whereIn('status', ['checked_out', 'confirmed', 'checked_in'])
            ->sum('total_amount');

        $totalBookings = DB::table('bookings')->count();

        $totalCosts = (float) DB::table('operational_costs')
            ->whereBetween('date', [$monthStart, $monthEnd])
            ->sum('amount');

        $totalPayroll = (float) DB::table('staff')
            ->where('is_active', true)
            ->sum('salary');

        $staffCount    = DB::table('staff')->where('is_active', true)->count();
        $activeStaff   = $staffCount;
        $lowStockItems = DB::table('inventory_items')->whereColumn('quantity', '<=', 'min_threshold')->count();
        $pendingTasks  = DB::table('tasks')->where('status', 'pending')->count();

        $occupancyRate = $totalRooms > 0
            ? round(($occupiedRooms / $totalRooms) * 100, 1)
            : 0;

        $avgRevenuePerRoom = $totalRooms > 0
            ? round($totalRevenue / $totalRooms, 2)
            : 0;

        // Daily revenue for current month
        $dailyRevenue = DB::table('bookings')
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->whereIn('status', ['checked_out', 'confirmed', 'checked_in'])
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as revenue')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Revenue per room
        $roomRevenue = DB::table('bookings')
            ->join('rooms', 'bookings.room_id', '=', 'rooms.id')
            ->whereIn('bookings.status', ['checked_out', 'confirmed', 'checked_in'])
            ->selectRaw('rooms.room_number as room, SUM(bookings.total_amount) as revenue')
            ->groupBy('rooms.id', 'rooms.room_number')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get();

        return response()->json([
            // snake_case (legacy)
            'total_rooms'        => $totalRooms,
            'available_rooms'    => $availableRooms,
            'occupied_rooms'     => $occupiedRooms,
            'today_checkins'     => $todayCheckIns,
            'today_checkouts'    => $todayCheckOuts,
            'month_revenue'      => $monthRevenue,
            'active_staff'       => $activeStaff,
            'low_stock_items'    => $lowStockItems,
            'pending_tasks'      => $pendingTasks,
            'occupancy_rate'     => $occupancyRate,

            // camelCase (used by reports/dashboard components)
            'totalRooms'         => $totalRooms,
            'availableRooms'     => $availableRooms,
            'occupiedRooms'      => $occupiedRooms,
            'occupied'           => $occupiedRooms,
            'available'          => $availableRooms,
            'todayCheckIns'      => $todayCheckIns,
            'todayCheckOuts'     => $todayCheckOuts,
            'todayCheckins'      => $todayCheckIns,
            'todayCheckouts'     => $todayCheckOuts,
            'monthRevenue'       => (float) ($monthRevenue ?? 0),
            'totalRevenue'       => (float) ($totalRevenue ?? 0),
            'totalBookings'      => (int) ($totalBookings ?? 0),
            'totalCosts'         => (float) ($totalCosts ?? 0),
            'totalPayroll'       => (float) ($totalPayroll ?? 0),
            'staffCount'         => $staffCount,
            'activeStaff'        => $activeStaff,
            'lowStockItems'      => $lowStockItems,
            'pendingTasks'       => $pendingTasks,
            'occupancyRate'      => $occupancyRate,
            'avgRevenuePerRoom'  => $avgRevenuePerRoom,
            'dailyRevenue'       => $dailyRevenue,
            'roomRevenue'        => $roomRevenue,
            'revenueByService'   => [],
        ]);
    }

    public function notifications(Request $request)
    {
        $notifications = [];

        $checkouts = DB::table('bookings')
            ->where('check_out_date', now()->toDateString())
            ->where('status', 'checked_in')
            ->select('booking_ref', 'guest_name', 'check_out_date')
            ->limit(5)
            ->get();

        foreach ($checkouts as $b) {
            $notifications[] = [
                'type'    => 'checkout',
                'message' => "Checkout today: {$b->guest_name} ({$b->booking_ref})",
            ];
        }

        $lowStock = DB::table('inventory_items')
            ->whereColumn('quantity', '<=', 'min_threshold')
            ->select('name', 'quantity', 'min_threshold')
            ->limit(5)
            ->get();

        foreach ($lowStock as $item) {
            $notifications[] = [
                'type'    => 'low_stock',
                'message' => "Low stock: {$item->name} ({$item->quantity} remaining)",
            ];
        }

        $pendingTasks = DB::table('tasks')
            ->where('status', 'pending')
            ->where('priority', 'urgent')
            ->select('title')
            ->limit(5)
            ->get();

        foreach ($pendingTasks as $task) {
            $notifications[] = [
                'type'    => 'task',
                'message' => "Urgent task: {$task->title}",
            ];
        }

        return response()->json($notifications);
    }
}
