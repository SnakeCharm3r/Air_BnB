<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();

        // Stats
        $stats = [
            'totalRooms' => DB::table('rooms')->count(),
            'availableRooms' => DB::table('rooms')->where('status', 'available')->count(),
            'occupiedRooms' => DB::table('rooms')->where('status', 'occupied')->count(),
            'todayCheckIns' => DB::table('bookings')
                ->where('check_in_date', $today)
                ->whereIn('status', ['confirmed', 'checked_in'])
                ->count(),
            'todayCheckOuts' => DB::table('bookings')
                ->where('check_out_date', $today)
                ->where('status', 'checked_in')
                ->count(),
            'monthRevenue' => (float) DB::table('bookings')
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->whereIn('status', ['checked_out', 'confirmed', 'checked_in'])
                ->sum('total_amount'),
            'activeStaff' => DB::table('staff')->where('is_active', true)->count(),
            'lowStockItems' => DB::table('inventory_items')->whereColumn('quantity', '<=', 'min_threshold')->count(),
            'pendingTasks' => DB::table('tasks')->where('status', 'pending')->count(),
        ];

        $stats['occupancyRate'] = $stats['totalRooms'] > 0
            ? round(($stats['occupiedRooms'] / $stats['totalRooms']) * 100, 1)
            : 0;

        // Recent bookings
        $recentBookings = DB::table('bookings')
            ->join('rooms', 'bookings.room_id', '=', 'rooms.id')
            ->select('bookings.*', 'rooms.room_number')
            ->orderByDesc('bookings.created_at')
            ->limit(5)
            ->get();

        return view('dashboard', compact('stats', 'recentBookings'));
    }
}
