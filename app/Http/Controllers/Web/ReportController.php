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

        // Sales Reports by Period (use payment_date, exclude voids and refunds)
        // Daily Sales (last 7 days)
        $dailySales = DB::table('payments')
            ->select(DB::raw('DATE(payment_date) as date'), DB::raw('SUM(amount) as total'))
            ->where('payment_date', '>=', Carbon::now()->subDays(7))
            ->where('is_void', false)
            ->where('is_refund', false)
            ->where('payment_status', 'successful')
            ->groupBy(DB::raw('DATE(payment_date)'))
            ->orderBy('date', 'desc')
            ->get();

        // Weekly Sales (last 4 weeks)
        $weeklySales = DB::table('payments')
            ->select(
                DB::raw('YEARWEEK(payment_date) as yearweek'),
                DB::raw('MIN(DATE(payment_date)) as week_start'),
                DB::raw('MAX(DATE(payment_date)) as week_end'),
                DB::raw('SUM(amount) as total')
            )
            ->where('payment_date', '>=', Carbon::now()->subWeeks(4))
            ->where('is_void', false)
            ->where('is_refund', false)
            ->where('payment_status', 'successful')
            ->groupBy(DB::raw('YEARWEEK(payment_date)'))
            ->orderBy('yearweek', 'desc')
            ->get();

        // Monthly Sales (last 12 months)
        $monthlySales = DB::table('payments')
            ->select(
                DB::raw('DATE_FORMAT(payment_date, "%Y-%m") as month'),
                DB::raw('SUM(amount) as total')
            )
            ->where('payment_date', '>=', Carbon::now()->subMonths(12))
            ->where('is_void', false)
            ->where('is_refund', false)
            ->where('payment_status', 'successful')
            ->groupBy(DB::raw('DATE_FORMAT(payment_date, "%Y-%m")'))
            ->orderBy('month', 'desc')
            ->get();

        // Yearly Sales (all years)
        $yearlySales = DB::table('payments')
            ->select(
                DB::raw('YEAR(payment_date) as year'),
                DB::raw('SUM(amount) as total')
            )
            ->where('is_void', false)
            ->where('is_refund', false)
            ->where('payment_status', 'successful')
            ->groupBy(DB::raw('YEAR(payment_date)'))
            ->orderBy('year', 'desc')
            ->get();

        // Outstanding Debt Report (use guest folios as the accounting source of truth)
        $outstandingDebt = DB::table('guest_folios')
            ->join('bookings', 'guest_folios.booking_id', '=', 'bookings.id')
            ->join('rooms', 'bookings.room_id', '=', 'rooms.id')
            ->select(
                'bookings.id',
                'bookings.booking_ref',
                'bookings.guest_name',
                'bookings.status',
                'rooms.room_number',
                'guest_folios.subtotal as total_amount',
                'guest_folios.amount_paid as retainer_paid',
                'guest_folios.balance_due',
                'guest_folios.payment_status',
                'guest_folios.folio_number',
                DB::raw('(select invoice_number from invoices where invoices.booking_id = bookings.id order by id desc limit 1) as last_invoice_number'),
                DB::raw('(select invoice_status from invoices where invoices.booking_id = bookings.id order by id desc limit 1) as last_invoice_status'),
                'bookings.check_in_date',
                'bookings.check_out_date',
                'bookings.created_at'
            )
            ->where('guest_folios.status', 'open')
            ->where('guest_folios.balance_due', '>', 0)
            ->whereIn('bookings.status', ['pending', 'confirmed', 'checked_in'])
            ->orderBy('guest_folios.balance_due', 'desc')
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

        // Revenue by payment method (use payment_date, exclude voids/refunds)
        $revenueByMethod = DB::table('payments')
            ->select('payment_method', DB::raw('SUM(amount) as total'))
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->where('is_void', false)
            ->where('is_refund', false)
            ->where('payment_status', 'successful')
            ->groupBy('payment_method')
            ->get();

        // Revenue by date
        $dailyRevenue = DB::table('payments')
            ->select(DB::raw('DATE(payment_date) as date'), DB::raw('SUM(amount) as total'))
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->where('is_void', false)
            ->where('is_refund', false)
            ->where('payment_status', 'successful')
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

    public function noShows(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        $noShows = DB::table('bookings')
            ->join('rooms', 'bookings.room_id', '=', 'rooms.id')
            ->select(
                'bookings.*',
                'rooms.room_number'
            )
            ->where('bookings.is_no_show', true)
            ->whereBetween('bookings.check_in_date', [$startDate, $endDate])
            ->orderByDesc('bookings.check_in_date')
            ->get();

        return view('reports.no-shows', compact('noShows', 'startDate', 'endDate'));
    }

    public function housekeeping(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        $assignments = DB::table('housekeeping_assignments')
            ->join('rooms', 'housekeeping_assignments.room_id', '=', 'rooms.id')
            ->leftJoin('staff', 'housekeeping_assignments.assigned_to', '=', 'staff.id')
            ->select(
                'housekeeping_assignments.*',
                'rooms.room_number',
                'staff.full_name as assigned_to_name'
            )
            ->whereBetween('housekeeping_assignments.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->orderByDesc('housekeeping_assignments.created_at')
            ->get();

        $totalAssignments = $assignments->count();
        $completedAssignments = $assignments->where('status', 'completed')->count();
        $pendingAssignments = $assignments->where('status', 'pending')->count();

        return view('reports.housekeeping', compact('assignments', 'totalAssignments', 'completedAssignments', 'pendingAssignments', 'startDate', 'endDate'));
    }

    public function maintenanceCosts(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        $maintenanceTasks = DB::table('maintenance_tasks')
            ->select('category', DB::raw('SUM(cost) as total_cost'), DB::raw('COUNT(*) as total_tasks'))
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->where('status', 'completed')
            ->groupBy('category')
            ->get();

        $totalCost = $maintenanceTasks->sum('total_cost');
        $totalTasks = $maintenanceTasks->sum('total_tasks');

        return view('reports.maintenance-costs', compact('maintenanceTasks', 'totalCost', 'totalTasks', 'startDate', 'endDate'));
    }

    public function bestCustomers(Request $request)
    {
        $limit = $request->input('limit', 20);

        $customers = DB::table('guests')
            ->leftJoin('bookings', 'guests.id', '=', 'bookings.guest_id')
            ->select(
                'guests.*',
                DB::raw('COUNT(bookings.id) as total_bookings'),
                DB::raw('SUM(bookings.total_amount) as total_spent')
            )
            ->whereNotNull('bookings.id')
            ->groupBy('guests.id')
            ->orderByDesc('total_spent')
            ->limit($limit)
            ->get();

        return view('reports.best-customers', compact('customers'));
    }

    public function roomPerformance(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->subDays(90)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        $rooms = DB::table('rooms')
            ->leftJoin('room_types', 'rooms.room_type_id', '=', 'room_types.id')
            ->leftJoin('bookings', 'rooms.id', '=', 'bookings.room_id')
            ->select(
                'rooms.id',
                'rooms.room_number',
                'room_types.name as room_type',
                'room_types.base_price',
                DB::raw('COUNT(bookings.id) as total_bookings'),
                DB::raw('SUM(bookings.total_amount) as total_revenue'),
                DB::raw('AVG(bookings.total_amount) as avg_revenue_per_booking')
            )
            ->whereBetween('bookings.check_in_date', [$startDate, $endDate])
            ->groupBy('rooms.id')
            ->orderByDesc('total_revenue')
            ->get();

        return view('reports.room-performance', compact('rooms', 'startDate', 'endDate'));
    }

    public function staffActivity(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        $activity = DB::table('audit_logs')
            ->join('users', 'audit_logs.user_id', '=', 'users.id')
            ->select(
                'users.name',
                'users.role',
                DB::raw('COUNT(audit_logs.id) as total_actions'),
                DB::raw('COUNT(DISTINCT DATE(audit_logs.created_at)) as active_days')
            )
            ->whereBetween('audit_logs.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->groupBy('users.id')
            ->orderByDesc('total_actions')
            ->get();

        return view('reports.staff-activity', compact('activity', 'startDate', 'endDate'));
    }
}
