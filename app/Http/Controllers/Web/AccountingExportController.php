<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\GuestFolio;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountingExportController extends Controller
{
    public function billing(Request $request)
    {
        $perPage = in_array($request->input('per_page'), [10, 25, 50, 100]) ? (int) $request->input('per_page') : 25;
        $date = $request->input('date');
        $month = $request->input('month');

        $outstandingQuery = DB::table('guest_folios')
            ->join('bookings', 'guest_folios.booking_id', '=', 'bookings.id')
            ->join('rooms', 'bookings.room_id', '=', 'rooms.id')
            ->select(
                'bookings.booking_ref',
                'bookings.guest_name',
                'rooms.room_number',
                'bookings.status',
                'guest_folios.total_amount',
                'guest_folios.amount_paid as retainer_paid',
                'guest_folios.balance_due',
                'bookings.check_in_date',
                'bookings.check_out_date'
            )
            ->where('guest_folios.status', 'open')
            ->where('guest_folios.balance_due', '>', 0)
            ->whereIn('bookings.status', ['confirmed', 'checked_in']);

        if ($date) {
            $outstandingQuery->whereDate('bookings.check_in_date', $date);
        }

        if ($month) {
            $outstandingQuery->whereYear('bookings.check_in_date', substr($month, 0, 4))
                ->whereMonth('bookings.check_in_date', substr($month, 5, 2));
        }

        $outstandingBills = $outstandingQuery->orderBy('bookings.check_in_date')->get();

        $pendingQuery = DB::table('bookings')
            ->join('rooms', 'bookings.room_id', '=', 'rooms.id')
            ->select(
                'bookings.booking_ref',
                'bookings.guest_name',
                'rooms.room_number',
                'bookings.total_amount',
                'bookings.check_in_date',
                'bookings.check_out_date'
            )
            ->where('bookings.status', 'pending');

        if ($date) {
            $pendingQuery->whereDate('bookings.check_in_date', $date);
        }

        if ($month) {
            $pendingQuery->whereYear('bookings.check_in_date', substr($month, 0, 4))
                ->whereMonth('bookings.check_in_date', substr($month, 5, 2));
        }

        $pendingConfirmations = $pendingQuery->orderBy('bookings.created_at')->get();

        return $this->export('exports.billing', 'Billing & Reconciliation', [
            'outstandingBills' => $outstandingBills,
            'pendingConfirmations' => $pendingConfirmations,
        ]);
    }

    public function invoices(Request $request)
    {
        $date = $request->input('date');
        $month = $request->input('month');

        $query = Invoice::with(['booking', 'booking.room', 'folio', 'folio.charges', 'folio.payments', 'issuer']);

        if ($date) {
            $query->whereDate('created_at', $date);
        }

        if ($month) {
            $query->whereYear('created_at', substr($month, 0, 4))
                ->whereMonth('created_at', substr($month, 5, 2));
        }

        if ($request->filled('status')) {
            $query->where('invoice_status', $request->input('status'));
        }

        if ($request->filled('folio_id')) {
            $query->where('folio_id', $request->input('folio_id'));
        }

        $invoices = $query->latest()->get();

        foreach ($invoices as $invoice) {
            $folio = $invoice->folio;
            $chargesTotal = $folio ? $folio->charges->sum('total_amount') : 0;
            $paymentsTotal = $folio
                ? $folio->payments->where('payment_status', 'successful')->where('is_void', false)->sum('amount')
                : 0;

            $invoice->grand_total = $chargesTotal;
            $invoice->amount_paid = $paymentsTotal;
            $invoice->balance_due = max(0, $chargesTotal - $paymentsTotal);
        }

        return $this->export('exports.invoices', 'Invoices', compact('invoices'));
    }

    public function payments(Request $request)
    {
        $date = $request->input('date');
        $month = $request->input('month');

        $query = Payment::with(['booking', 'folio']);

        if ($request->filled('folio_id')) {
            $query->where('folio_id', $request->input('folio_id'));
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->input('payment_method'));
        }

        if ($date) {
            $query->whereDate('payment_date', $date);
        }

        if ($month) {
            $query->whereYear('payment_date', substr($month, 0, 4))
                ->whereMonth('payment_date', substr($month, 5, 2));
        }

        $payments = $query->latest()->get();

        return $this->export('exports.payments', 'Payments', compact('payments'));
    }

    public function receipts(Request $request)
    {
        $date = $request->input('date');
        $month = $request->input('month');

        $query = Payment::with(['booking', 'folio'])
            ->whereNotNull('receipt_number');

        if ($date) {
            $query->whereDate('payment_date', $date);
        }

        if ($month) {
            $query->whereYear('payment_date', substr($month, 0, 4))
                ->whereMonth('payment_date', substr($month, 5, 2));
        }

        $receipts = $query->latest()->get();

        return $this->export('exports.receipts', 'Receipts', compact('receipts'));
    }

    private function export(string $view, string $title, array $data)
    {
        $settings = Setting::getInstance();

        $hotelName = $settings->lodge_name ?? config('app.name', 'Hotel');
        $hotelAddress = $settings->contact_address ?? '';
        $hotelPhone = $settings->contact_phone ?? '';
        $hotelEmail = $settings->contact_email ?? '';

        $filename = preg_replace('/[^A-Za-z0-9\-_]/', '_', $title) . '-' . now()->format('Y-m-d') . '.xls';

        return response()->view($view, array_merge($data, [
            'hotelName' => $hotelName,
            'hotelAddress' => $hotelAddress,
            'hotelPhone' => $hotelPhone,
            'hotelEmail' => $hotelEmail,
            'title' => $title,
            'generatedAt' => now()->format('Y-m-d H:i:s'),
        ]), 200, [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
