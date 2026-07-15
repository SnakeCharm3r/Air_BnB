<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\BillingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinanceController extends Controller
{
    protected BillingService $billingService;

    public function __construct(BillingService $billingService)
    {
        $this->billingService = $billingService;
    }

    public function dashboard(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfMonth()->toDateString());

        $summary = $this->billingService->getDashboardSummary($startDate, $endDate);

        $today = now()->toDateString();
        $todaysRevenue = $this->billingService->getRevenueReport($today, $today)['total'] ?? 0;
        $todaysPayments = $this->billingService->getPaymentsReport($today, $today)['total'] ?? 0;

        $outstandingBalance = DB::table('guest_folios')
            ->where('status', 'open')
            ->where('balance_due', '>', 0)
            ->sum('balance_due');

        $pendingInvoices = DB::table('invoices')
            ->where('invoice_status', 'issued')
            ->where('balance_due', '>', 0)
            ->count();

        $paidInvoices = DB::table('invoices')
            ->where('invoice_status', 'paid')
            ->whereDate('updated_at', $today)
            ->count();

        $deposits = $this->billingService->getDepositsReport($startDate, $endDate)['total'] ?? 0;
        $refunds = $this->billingService->getRefundsReport($startDate, $endDate)['total'] ?? 0;

        $recentPayments = DB::table('payments')
            ->join('bookings', 'payments.booking_id', '=', 'bookings.id')
            ->select('payments.*', 'bookings.guest_name', 'bookings.booking_ref')
            ->where('payments.is_void', false)
            ->orderByDesc('payments.created_at')
            ->limit(10)
            ->get();

        $recentInvoices = DB::table('invoices')
            ->join('bookings', 'invoices.booking_id', '=', 'bookings.id')
            ->select('invoices.*', 'bookings.guest_name', 'bookings.booking_ref')
            ->orderByDesc('invoices.created_at')
            ->limit(10)
            ->get();

        $topGuests = DB::table('bookings')
            ->select('guest_name', DB::raw('SUM(total_amount) as total_spent'), DB::raw('COUNT(*) as bookings_count'))
            ->whereBetween('check_in_date', [$startDate, $endDate])
            ->groupBy('guest_name')
            ->orderByDesc('total_spent')
            ->limit(10)
            ->get();

        return view('finance.dashboard', compact(
            'summary',
            'todaysRevenue',
            'todaysPayments',
            'outstandingBalance',
            'pendingInvoices',
            'paidInvoices',
            'deposits',
            'refunds',
            'recentPayments',
            'recentInvoices',
            'topGuests',
            'startDate',
            'endDate'
        ));
    }
}
