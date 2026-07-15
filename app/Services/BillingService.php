<?php

namespace App\Services;

use App\Models\BookingCharge;
use App\Models\GuestFolio;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class BillingService
{
    /**
     * Total outstanding balance across all open folios.
     */
    public function getOutstandingBalances(): float
    {
        return (float) GuestFolio::where('status', 'open')
            ->sum('balance_due');
    }

    /**
     * Revenue grouped by charge type for a date range.
     */
    public function getRevenueByChargeType(string $startDate, string $endDate): array
    {
        return BookingCharge::whereBetween('posting_date', [$startDate, $endDate])
            ->where('status', 'posted')
            ->selectRaw('charge_type, SUM(total_amount) as revenue')
            ->groupBy('charge_type')
            ->pluck('revenue', 'charge_type')
            ->toArray();
    }

    /**
     * Payments grouped by method for a date range.
     */
    public function getPaymentsByMethod(string $startDate, string $endDate): array
    {
        return Payment::whereBetween('payment_date', [$startDate, $endDate])
            ->where('is_void', false)
            ->where('payment_status', 'successful')
            ->selectRaw('payment_method, SUM(amount) as total')
            ->groupBy('payment_method')
            ->pluck('total', 'payment_method')
            ->toArray();
    }

    /**
     * Deposits received (payments recorded before checkout).
     */
    public function getDepositsReceived(string $startDate, string $endDate): float
    {
        return (float) Payment::whereBetween('payment_date', [$startDate, $endDate])
            ->where('is_void', false)
            ->where('payment_status', 'successful')
            ->whereHas('folio', function ($query) {
                $query->where('status', 'open');
            })
            ->sum('amount');
    }

    /**
     * Refunds recorded in a date range.
     */
    public function getRefunds(string $startDate, string $endDate): float
    {
        return (float) Payment::whereBetween('payment_date', [$startDate, $endDate])
            ->where('is_refund', true)
            ->where('is_void', false)
            ->sum('amount');
    }

    /**
     * Revenue by day for a date range.
     */
    public function getRevenueByDay(string $startDate, string $endDate): array
    {
        return BookingCharge::whereBetween('posting_date', [$startDate, $endDate])
            ->where('status', 'posted')
            ->selectRaw('posting_date, SUM(total_amount) as revenue')
            ->groupBy('posting_date')
            ->orderBy('posting_date')
            ->pluck('revenue', 'posting_date')
            ->toArray();
    }

    /**
     * Revenue by room type for a date range.
     */
    public function getRevenueByRoomType(string $startDate, string $endDate): array
    {
        return BookingCharge::whereBetween('posting_date', [$startDate, $endDate])
            ->where('status', 'posted')
            ->where('charge_type', 'room')
            ->join('bookings', 'booking_charges.booking_id', '=', 'bookings.id')
            ->join('rooms', 'bookings.room_id', '=', 'rooms.id')
            ->join('room_types', 'rooms.room_type_id', '=', 'room_types.id')
            ->selectRaw('room_types.name as room_type, SUM(booking_charges.total_amount) as revenue')
            ->groupBy('room_types.name')
            ->pluck('revenue', 'room_type')
            ->toArray();
    }

    /**
     * Total revenue (posted charges) for a date range.
     */
    public function getRevenueReport(string $startDate, string $endDate): array
    {
        $total = (float) BookingCharge::whereBetween('posting_date', [$startDate, $endDate])
            ->where('status', 'posted')
            ->sum('total_amount');

        return ['total' => $total];
    }

    /**
     * Total successful payments for a date range.
     */
    public function getPaymentsReport(string $startDate, string $endDate): array
    {
        $total = (float) Payment::whereBetween('payment_date', [$startDate, $endDate])
            ->where('is_void', false)
            ->where('is_refund', false)
            ->where('payment_status', 'successful')
            ->sum('amount');

        return ['total' => $total];
    }

    /**
     * Deposits report wrapper.
     */
    public function getDepositsReport(string $startDate, string $endDate): array
    {
        return ['total' => $this->getDepositsReceived($startDate, $endDate)];
    }

    /**
     * Refunds report wrapper.
     */
    public function getRefundsReport(string $startDate, string $endDate): array
    {
        return ['total' => abs($this->getRefunds($startDate, $endDate))];
    }

    /**
     * One-call dashboard summary.
     */
    public function getDashboardSummary(string $startDate, string $endDate): array
    {
        $today = now()->toDateString();

        return [
            'todays_revenue' => (float) BookingCharge::where('posting_date', $today)
                ->where('status', 'posted')
                ->sum('total_amount'),
            'outstanding_balances' => $this->getOutstandingBalances(),
            'deposits_received' => $this->getDepositsReceived($startDate, $endDate),
            'refunds' => abs($this->getRefunds($startDate, $endDate)),
            'payments_by_method' => $this->getPaymentsByMethod($startDate, $endDate),
            'revenue_by_charge_type' => $this->getRevenueByChargeType($startDate, $endDate),
            'revenue_by_day' => $this->getRevenueByDay($startDate, $endDate),
            'revenue_by_room_type' => $this->getRevenueByRoomType($startDate, $endDate),
        ];
    }
}
