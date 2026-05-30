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
        return view('reports.index');
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
