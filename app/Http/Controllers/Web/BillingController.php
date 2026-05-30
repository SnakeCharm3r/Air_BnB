<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BillingController extends Controller
{
    public function index()
    {
        // Get pending bills (bookings with balance due)
        $pendingBills = DB::table('bookings')
            ->join('rooms', 'bookings.room_id', '=', 'rooms.id')
            ->select(
                'bookings.id',
                'bookings.guest_name',
                'bookings.booking_ref',
                'bookings.total_amount',
                'bookings.retainer_paid',
                'bookings.balance_due',
                'bookings.check_in_date',
                'bookings.check_out_date',
                'rooms.room_number',
                'bookings.status'
            )
            ->where('bookings.balance_due', '>', 0)
            ->whereIn('bookings.status', ['confirmed', 'checked_in'])
            ->orderBy('bookings.check_in_date')
            ->get();

        // Get today's payments
        $todayPayments = DB::table('payments')
            ->whereDate('created_at', today())
            ->sum('amount');

        // Get total outstanding
        $totalOutstanding = DB::table('bookings')
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
            ->where('bookings.status', 'pending')
            ->orderBy('bookings.created_at')
            ->get();

        return view('billing.index', compact('pendingBills', 'todayPayments', 'totalOutstanding', 'pendingConfirmations'));
    }

    public function show($bookingId)
    {
        $booking = DB::table('bookings')
            ->join('rooms', 'bookings.room_id', '=', 'rooms.id')
            ->select('bookings.*', 'rooms.room_number', 'rooms.room_type_id')
            ->where('bookings.id', $bookingId)
            ->first();

        if (!$booking) {
            abort(404);
        }

        // Get additional services/charges
        $charges = DB::table('booking_charges')
            ->where('booking_id', $bookingId)
            ->get();

        // Get payment history
        $payments = DB::table('payments')
            ->where('booking_id', $bookingId)
            ->orderByDesc('created_at')
            ->get();

        return view('billing.show', compact('booking', 'charges', 'payments'));
    }

    public function processPayment(Request $request, $bookingId)
    {
        $booking = DB::table('bookings')->find($bookingId);
        if (!$booking) {
            abort(404);
        }

        $data = $request->validate([
            'amount'        => 'required|numeric|min:0',
            'payment_method'=> 'required|in:cash,card,bank_transfer,mobile_money',
            'reference'     => 'nullable|string',
            'notes'         => 'nullable|string',
        ]);

        // Record payment
        DB::table('payments')->insert([
            'booking_id'     => $bookingId,
            'amount'         => $data['amount'],
            'payment_method' => $data['payment_method'],
            'reference'      => $data['reference'],
            'notes'          => $data['notes'],
            'created_by'     => auth()->id(),
            'created_at'     => now(),
        ]);

        // Update booking balance
        $newBalance = $booking->balance_due - $data['amount'];
        $newRetainer = $booking->retainer_paid + $data['amount'];

        DB::table('bookings')->where('id', $bookingId)->update([
            'balance_due'   => max(0, $newBalance),
            'retainer_paid' => $newRetainer,
            'updated_at'    => now(),
        ]);

        return redirect()->route('billing.show', $bookingId)->with('success', 'Payment recorded successfully');
    }

    public function addCharge(Request $request, $bookingId)
    {
        $booking = DB::table('bookings')->find($bookingId);
        if (!$booking) {
            abort(404);
        }

        $data = $request->validate([
            'description' => 'required|string',
            'amount'      => 'required|numeric|min:0',
            'category'    => 'required|in:service,item,damage,other',
        ]);

        // Add charge
        DB::table('booking_charges')->insert([
            'booking_id'  => $bookingId,
            'description' => $data['description'],
            'amount'      => $data['amount'],
            'category'    => $data['category'],
            'created_by'  => auth()->id(),
            'created_at'  => now(),
        ]);

        // Update booking total
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
