<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today        = now()->toDateString();
        $monthStart   = now()->startOfMonth()->toDateString();
        $monthEnd     = now()->endOfMonth()->toDateString();
        $lastMonthStart = now()->subMonth()->startOfMonth()->toDateString();
        $lastMonthEnd   = now()->subMonth()->endOfMonth()->toDateString();

        // Month revenue = actual payments received this month (not voided, not refunds)
        $monthRevenue = (float) DB::table('payments')
            ->where('is_void', false)
            ->where('is_refund', false)
            ->whereBetween('payment_date', [$monthStart, $monthEnd])
            ->sum('amount');

        // Last month revenue for trend comparison
        $lastMonthRevenue = (float) DB::table('payments')
            ->where('is_void', false)
            ->where('is_refund', false)
            ->whereBetween('payment_date', [$lastMonthStart, $lastMonthEnd])
            ->sum('amount');

        $revenueChange = $lastMonthRevenue > 0
            ? round((($monthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1)
            : null;

        // Outstanding balance across all open folios
        $outstandingBalance = (float) DB::table('guest_folios')
            ->where('balance_due', '>', 0)
            ->sum('balance_due');

        // Room stats
        $totalRooms    = DB::table('rooms')->count();
        $availableRooms = DB::table('rooms')->where('status', 'available')->count();
        $occupiedRooms  = DB::table('rooms')->where('status', 'occupied')->count();

        // Today's arrivals (confirmed or just checked in today)
        $todayCheckIns = DB::table('bookings')
            ->where('check_in_date', $today)
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->count();

        // Today's departures (still checked_in, due out today)
        $todayCheckOuts = DB::table('bookings')
            ->where('check_out_date', $today)
            ->where('status', 'checked_in')
            ->count();

        // Currently in-house guests
        $currentGuests = DB::table('bookings')
            ->where('status', 'checked_in')
            ->count();

        // Pending / confirmed upcoming bookings
        $pendingBookings = DB::table('bookings')
            ->where('status', 'confirmed')
            ->count();

        // Active staff
        $activeStaff = DB::table('staff')->where('is_active', true)->count();

        // Low stock items
        $lowStockItems = DB::table('inventory_items')
            ->whereColumn('quantity', '<=', 'min_threshold')
            ->count();

        // Unpaid invoices (issued but not yet fully paid)
        $unpaidInvoices = DB::table('invoices')
            ->where('invoice_status', 'issued')
            ->where('balance_due', '>', 0)
            ->count();

        // New bookings this month
        $newBookingsThisMonth = DB::table('bookings')
            ->whereBetween('created_at', [$monthStart . ' 00:00:00', $monthEnd . ' 23:59:59'])
            ->count();

        $stats = [
            'totalRooms'          => $totalRooms,
            'availableRooms'      => $availableRooms,
            'occupiedRooms'       => $occupiedRooms,
            'occupancyRate'       => $totalRooms > 0
                                        ? round(($occupiedRooms / $totalRooms) * 100, 1)
                                        : 0,
            'todayCheckIns'       => $todayCheckIns,
            'todayCheckOuts'      => $todayCheckOuts,
            'currentGuests'       => $currentGuests,
            'pendingBookings'     => $pendingBookings,
            'monthRevenue'        => $monthRevenue,
            'lastMonthRevenue'    => $lastMonthRevenue,
            'revenueChange'       => $revenueChange,
            'outstandingBalance'  => $outstandingBalance,
            'activeStaff'         => $activeStaff,
            'lowStockItems'       => $lowStockItems,
            'unpaidInvoices'      => $unpaidInvoices,
            'newBookingsThisMonth' => $newBookingsThisMonth,
        ];

        // Recent bookings
        $recentBookings = DB::table('bookings')
            ->join('rooms', 'bookings.room_id', '=', 'rooms.id')
            ->select('bookings.*', 'rooms.room_number')
            ->orderByDesc('bookings.created_at')
            ->limit(8)
            ->get();

        return view('dashboard', compact('stats', 'recentBookings'));
    }
}
