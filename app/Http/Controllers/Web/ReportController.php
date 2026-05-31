<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        // Room Status Report
        $rooms = DB::table('rooms')
            ->leftJoin('bookings', function ($join) {
                $join->on('rooms.id', '=', 'bookings.room_id')
                    ->whereIn('bookings.status', ['confirmed', 'checked_in'])
                    ->whereDate('bookings.check_in_date', '<=', now())
                    ->whereDate('bookings.check_out_date', '>=', now());
            })
            ->leftJoin('room_types', 'rooms.room_type_id', '=', 'room_types.id')
            ->select(
                'rooms.id',
                'rooms.room_number',
                'rooms.status as room_status',
                'room_types.name as room_type',
                'room_types.base_price',
                'bookings.guest_name',
                'bookings.check_in_date',
                'bookings.check_out_date',
                'bookings.status as booking_status',
                DB::raw('CASE WHEN bookings.id IS NOT NULL THEN "occupied" ELSE rooms.status END as current_status')
            )
            ->orderBy('rooms.room_number')
            ->get();

        $totalRooms = $rooms->count();
        $occupiedRooms = $rooms->where('current_status', 'occupied')->count();
        $vacantRooms = $totalRooms - $occupiedRooms;

        // Sales Reports by Period
        // Daily Sales (last 7 days)
        $dailySales = DB::table('payments')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(amount) as total'))
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date', 'desc')
            ->get();

        // Weekly Sales (last 4 weeks)
        $weeklySales = DB::table('payments')
            ->select(
                DB::raw('YEARWEEK(created_at) as yearweek'),
                DB::raw('MIN(DATE(created_at)) as week_start'),
                DB::raw('MAX(DATE(created_at)) as week_end'),
                DB::raw('SUM(amount) as total')
            )
            ->where('created_at', '>=', Carbon::now()->subWeeks(4))
            ->groupBy(DB::raw('YEARWEEK(created_at)'))
            ->orderBy('yearweek', 'desc')
            ->get();

        // Monthly Sales (last 12 months)
        $monthlySales = DB::table('payments')
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('SUM(amount) as total')
            )
            ->where('created_at', '>=', Carbon::now()->subMonths(12))
            ->groupBy(DB::raw('DATE_FORMAT(created_at, "%Y-%m")'))
            ->orderBy('month', 'desc')
            ->get();

        // Yearly Sales (all years)
        $yearlySales = DB::table('payments')
            ->select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy(DB::raw('YEAR(created_at)'))
            ->orderBy('year', 'desc')
            ->get();

        // Outstanding Debt Report
        $outstandingDebt = DB::table('bookings')
            ->join('rooms', 'bookings.room_id', '=', 'rooms.id')
            ->select(
                'bookings.id',
                'bookings.booking_ref',
                'bookings.guest_name',
                'bookings.status',
                'rooms.room_number',
                'bookings.total_amount',
                'bookings.retainer_paid',
                'bookings.balance_due',
                'bookings.check_in_date',
                'bookings.check_out_date',
                'bookings.created_at'
            )
            ->where('bookings.balance_due', '>', 0)
            ->whereIn('bookings.status', ['pending', 'confirmed', 'checked_in'])
            ->orderBy('bookings.balance_due', 'desc')
            ->get();

        $totalOutstanding = $outstandingDebt->sum('balance_due');
        $totalPendingInvoices = $outstandingDebt->where('status', 'pending')->sum('balance_due');
        $totalConfirmedInvoices = $outstandingDebt->whereIn('status', ['confirmed', 'checked_in'])->sum('balance_due');

        return view('reports.index', compact(
            'rooms', 'totalRooms', 'occupiedRooms', 'vacantRooms',
            'dailySales', 'weeklySales', 'monthlySales', 'yearlySales',
            'outstandingDebt', 'totalOutstanding', 'totalPendingInvoices', 'totalConfirmedInvoices'
        ));
    }

    public function occupancy(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        // Daily occupancy stats
        $occupancy = DB::table('bookings')
            ->select(
                DB::raw('DATE(check_in_date) as date'),
                DB::raw('COUNT(*) as bookings'),
                DB::raw('SUM(total_amount) as revenue')
            )
            ->whereBetween('check_in_date', [$startDate, $endDate])
            ->groupBy('date')
            ->get();

        $totalRooms = DB::table('rooms')->count();
        $totalBookings = $occupancy->sum('bookings');
        $totalRevenue = $occupancy->sum('revenue');

        return view('reports.occupancy', compact('occupancy', 'totalRooms', 'totalBookings', 'totalRevenue', 'startDate', 'endDate'));
    }

    public function revenue(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        // Revenue by payment method
        $revenueByMethod = DB::table('payments')
            ->select('payment_method', DB::raw('SUM(amount) as total'))
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->groupBy('payment_method')
            ->get();

        // Revenue by date
        $dailyRevenue = DB::table('payments')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(amount) as total'))
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->groupBy('date')
            ->get();

        $totalRevenue = $revenueByMethod->sum('total');

        return view('reports.revenue', compact('revenueByMethod', 'dailyRevenue', 'totalRevenue', 'startDate', 'endDate'));
    }

    public function costs(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        $costs = DB::table('operational_costs')
            ->select('category', DB::raw('SUM(amount) as total'))
            ->whereBetween('date', [$startDate, $endDate])
            ->groupBy('category')
            ->get();

        $totalCosts = $costs->sum('total');

        return view('reports.costs', compact('costs', 'totalCosts', 'startDate', 'endDate'));
    }

    public function staffPerformance()
    {
        // Tasks completed by staff
        $taskStats = DB::table('tasks')
            ->select(
                'assigned_to',
                DB::raw('COUNT(*) as total_tasks'),
                DB::raw('SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed_tasks')
            )
            ->whereNotNull('assigned_to')
            ->groupBy('assigned_to')
            ->get();

        // Join with staff names
        $staff = DB::table('staff')
            ->select('id', 'full_name', 'role')
            ->where('is_active', true)
            ->get()
            ->map(function ($s) use ($taskStats) {
                $stats = $taskStats->firstWhere('assigned_to', $s->id);
                $s->total_tasks = $stats ? $stats->total_tasks : 0;
                $s->completed_tasks = $stats ? $stats->completed_tasks : 0;
                $s->completion_rate = $s->total_tasks > 0 ? round(($s->completed_tasks / $s->total_tasks) * 100, 1) : 0;
                return $s;
            });

        return view('reports.staff', compact('staff'));
    }
}
