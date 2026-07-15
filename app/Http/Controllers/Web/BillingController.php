<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\GuestFolio;
use App\Services\ChargePostingService;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BillingController extends Controller
{
    public function index(Request $request)
    {
        $perPage = in_array($request->input('per_page'), [10, 25, 50, 100]) ? (int) $request->input('per_page') : 25;
        $date = $request->input('date');
        $month = $request->input('month');

        $today = now()->format('Y-m-d');

        // Auto-cancel pending/reserved bookings whose check-in date has passed
        $expiredBookings = DB::table('bookings')
            ->whereIn('status', ['pending', 'reserved'])
            ->where('check_in_date', '<', $today)
            ->get();

        foreach ($expiredBookings as $booking) {
            DB::table('bookings')->where('id', $booking->id)->update([
                'status' => 'cancelled',
                'balance_due' => 0,
                'updated_at' => now(),
            ]);

            // Free up the room if it was held for this booking
            $room = DB::table('rooms')->where('id', $booking->room_id)->first();
            if ($room && in_array($room->status, ['booked', 'reserved'])) {
                DB::table('rooms')->where('id', $booking->room_id)->update([
                    'status' => 'available',
                    'updated_at' => now(),
                ]);
            }
        }

        // Get pending bills from open folios (the accounting source of truth)
        $pendingBillsQuery = DB::table('guest_folios')
            ->join('bookings', 'guest_folios.booking_id', '=', 'bookings.id')
            ->join('rooms', 'bookings.room_id', '=', 'rooms.id')
            ->select(
                'bookings.id',
                'bookings.guest_name',
                'bookings.booking_ref',
                'guest_folios.subtotal as total_amount',
                'guest_folios.amount_paid as retainer_paid',
                'guest_folios.balance_due',
                'bookings.check_in_date',
                'bookings.check_out_date',
                'rooms.room_number',
                'bookings.status'
            )
            ->where('guest_folios.status', 'open')
            ->where('guest_folios.balance_due', '>', 0)
            ->whereIn('bookings.status', ['confirmed', 'checked_in']);

        if ($date) {
            $pendingBillsQuery->whereDate('bookings.check_in_date', $date);
        }

        if ($month) {
            $pendingBillsQuery->whereYear('bookings.check_in_date', substr($month, 0, 4))
                ->whereMonth('bookings.check_in_date', substr($month, 5, 2));
        }

        $pendingBills = $pendingBillsQuery->orderBy('bookings.check_in_date')
            ->paginate($perPage)
            ->withQueryString();

        // Get today's payments
        $todayPayments = DB::table('payments')
            ->whereDate('payment_date', today())
            ->where('is_void', false)
            ->where('payment_status', 'successful')
            ->sum('amount');

        // Get total outstanding from open folios (the new accounting source of truth)
        $totalOutstanding = DB::table('guest_folios')
            ->where('status', 'open')
            ->where('balance_due', '>', 0)
            ->sum('balance_due');

        // Get pending payment confirmations (bookings awaiting payment confirmation)
        $pendingConfirmations = DB::table('bookings')
            ->join('rooms', 'bookings.room_id', '=', 'rooms.id')
            ->select(
                'bookings.id',
                'bookings.guest_name',
                'bookings.booking_ref',
                'bookings.total_amount',
                'bookings.check_in_date',
                'bookings.check_out_date',
                'rooms.room_number',
                'bookings.status'
            )
            ->where('bookings.status', 'pending');

        if ($date) {
            $pendingConfirmations->whereDate('bookings.check_in_date', $date);
        }

        if ($month) {
            $pendingConfirmations->whereYear('bookings.check_in_date', substr($month, 0, 4))
                ->whereMonth('bookings.check_in_date', substr($month, 5, 2));
        }

        $pendingConfirmations = $pendingConfirmations->orderBy('bookings.created_at')->get();

        $filters = compact('date', 'month', 'perPage');

        return view('billing.index', compact('pendingBills', 'todayPayments', 'totalOutstanding', 'pendingConfirmations', 'filters'));
    }

    public function show($bookingId)
    {
        $booking = Booking::with('room')->findOrFail($bookingId);

        // Ensure the guest folio exists for this booking
        $folio = $booking->folio;
        if (! $folio) {
            $folio = app(\App\Services\FolioService::class)->openFolio($booking);
        }

        $charges = $folio->charges()->with('poster')->orderByDesc('posting_date')->get();
        $payments = $folio->payments()->orderByDesc('payment_date')->get();

        return view('billing.show', compact('booking', 'folio', 'charges', 'payments'));
    }

    public function processPayment(Request $request, $bookingId, PaymentService $paymentService)
    {
        $booking = Booking::findOrFail($bookingId);
        $folio = $booking->folio ?? app(\App\Services\FolioService::class)->openFolio($booking);

        $data = $request->validate([
            'amount'         => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,card,bank_transfer,mobile_money',
            'reference'      => 'nullable|string',
            'notes'          => 'nullable|string',
            'payment_for'    => 'nullable|string',
        ]);

        $paymentService->recordPayment([
            'booking_id'      => $booking->id,
            'folio_id'        => $folio->id,
            'amount'          => $data['amount'],
            'payment_method'  => $data['payment_method'],
            'payment_date'    => now()->toDateString(),
            'payment_gateway' => 'manual',
            'receipt_number'  => 'RCP-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4)),
            'payment_status'  => 'successful',
            'reference'       => $data['reference'] ?? null,
            'notes'           => $data['notes'] ?? null,
            'payment_for'     => $data['payment_for'] ?? null,
        ]);

        // Keep the legacy booking fields in sync for existing views
        DB::table('bookings')->where('id', $bookingId)->update([
            'balance_due'   => max(0, $booking->balance_due - $data['amount']),
            'retainer_paid' => ($booking->retainer_paid ?? 0) + $data['amount'],
            'updated_at'    => now(),
        ]);

        return redirect()->route('billing.show', $bookingId)->with('success', 'Payment recorded successfully');
    }

    public function addCharge(Request $request, $bookingId, ChargePostingService $chargeService)
    {
        $booking = Booking::findOrFail($bookingId);
        $folio = $booking->folio ?? app(\App\Services\FolioService::class)->openFolio($booking);

        $data = $request->validate([
            'description'  => 'required|string',
            'amount'       => 'required|numeric|min:0',
            'charge_type'  => 'required|string|in:room,restaurant,laundry,mini_bar,room_service,spa,transport,damage,conference,equipment_hire,extra_bed,early_check_in,late_check_out,miscellaneous',
            'quantity'     => 'nullable|numeric|min:0',
        ]);

        $chargeService->postCharge([
            'booking_id'   => $booking->id,
            'folio_id'     => $folio->id,
            'description'  => $data['description'],
            'charge_type'  => $data['charge_type'],
            'quantity'     => $data['quantity'] ?? 1,
            'unit_price'   => $data['amount'],
            'posting_date' => now()->toDateString(),
            'posted_by'    => Auth::id(),
        ]);

        // Keep the legacy booking fields in sync for existing views
        DB::table('bookings')->where('id', $bookingId)->update([
            'total_amount' => $booking->total_amount + $data['amount'],
            'balance_due'  => $booking->balance_due + $data['amount'],
            'updated_at'   => now(),
        ]);

        return redirect()->route('billing.show', $bookingId)->with('success', 'Charge added successfully');
    }

    public function printInvoice($bookingId)
    {
        $booking = DB::table('bookings')
            ->join('rooms', 'bookings.room_id', '=', 'rooms.id')
            ->select('bookings.*', 'rooms.room_number')
            ->where('bookings.id', $bookingId)
            ->first();

        if (!$booking) {
            abort(404);
        }

        $charges = DB::table('booking_charges')
            ->where('booking_id', $bookingId)
            ->get();

        $payments = DB::table('payments')
            ->where('booking_id', $bookingId)
            ->get();

        return view('billing.invoice', compact('booking', 'charges', 'payments'));
    }
}
