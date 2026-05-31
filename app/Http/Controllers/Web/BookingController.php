<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = DB::table('bookings')
            ->join('rooms', 'bookings.room_id', '=', 'rooms.id')
            ->select('bookings.*', 'rooms.room_number')
            ->orderByDesc('bookings.created_at')
            ->get();

        return view('bookings.index', compact('bookings'));
    }

    public function create()
    {
        $rooms = DB::table('rooms')
            ->leftJoin('room_types', 'rooms.room_type_id', '=', 'room_types.id')
            ->where('rooms.status', 'available')
            ->select('rooms.*', 'room_types.name as room_type_name', 'room_types.base_price as price')
            ->get();
        return view('bookings.create', compact('rooms'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'guest_name' => 'required|string',
            'guest_email' => 'nullable|email',
            'guest_phone' => 'nullable|string',
            'room_id' => 'required|exists:rooms,id',
            'check_in_date' => 'required|date',
            'check_in_time' => 'nullable|date_format:H:i',
            'check_out_date' => 'required|date|after:check_in_date',
            'check_out_time' => 'nullable|date_format:H:i',
            'adults' => 'integer|min:1',
            'children' => 'integer|min:0',
            'total_amount' => 'required|numeric|min:0',
            'retainer_paid' => 'nullable|numeric|min:0',
            'payment_type' => 'nullable|in:cash,crdb',
            'payment_reference' => 'nullable|string',
            'special_requests' => 'nullable|string',
        ]);

        $data['booking_ref'] = 'BK-' . strtoupper(Str::random(6));
        $data['retainer_paid'] = $data['retainer_paid'] ?? 0;
        
        // Determine status based on payment
        $hasPayment = $data['retainer_paid'] > 0 || !empty($data['payment_reference']);
        $data['status'] = $hasPayment ? 'confirmed' : 'pending';
        
        $data['balance_due'] = $data['total_amount'] - $data['retainer_paid'];
        $data['created_by'] = auth()->id();

        $bookingId = DB::table('bookings')->insertGetId($data);

        // Update room status
        DB::table('rooms')->where('id', $data['room_id'])->update(['status' => 'booked']);

        // Create invoice only if confirmed
        if ($data['status'] === 'confirmed') {
            $this->createInvoice($bookingId, $data);
            return redirect()->route('bookings.show', $bookingId)->with('success', 'Booking confirmed and invoice generated.');
        }

        return redirect()->route('bookings.show', $bookingId)->with('success', 'Booking created with pending payment status.');
    }

    private function createInvoice($bookingId, $bookingData)
    {
        $room = DB::table('rooms')->where('id', $bookingData['room_id'])->first();
        $roomType = DB::table('room_types')->where('id', $room->room_type_id ?? null)->first();
        
        $checkIn = strtotime($bookingData['check_in_date']);
        $checkOut = strtotime($bookingData['check_out_date']);
        $nights = max(1, ($checkOut - $checkIn) / (60 * 60 * 24));
        
        $roomRate = $roomType->base_price ?? 0;
        $subtotal = $bookingData['total_amount'];
        $taxRate = 0.12; // 12% tax
        $taxAmount = $subtotal * $taxRate;
        $totalAmount = $subtotal + $taxAmount;
        $amountPaid = $bookingData['retainer_paid'] ?? 0;
        $balanceDue = $totalAmount - $amountPaid;

        $invoiceData = [
            'invoice_number' => 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(4)),
            'booking_id' => $bookingId,
            'guest_name' => $bookingData['guest_name'],
            'guest_email' => $bookingData['guest_email'] ?? null,
            'guest_phone' => $bookingData['guest_phone'] ?? null,
            'room_number' => $room->room_number ?? 'N/A',
            'check_in_date' => $bookingData['check_in_date'],
            'check_in_time' => $bookingData['check_in_time'] ?? '14:00',
            'check_out_date' => $bookingData['check_out_date'],
            'check_out_time' => $bookingData['check_out_time'] ?? '11:00',
            'nights' => $nights,
            'room_rate' => $roomRate,
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
            'amount_paid' => $amountPaid,
            'balance_due' => $balanceDue,
            'payment_type' => $bookingData['payment_type'] ?? null,
            'payment_reference' => $bookingData['payment_reference'] ?? null,
            'status' => $amountPaid >= $totalAmount ? 'paid' : ($amountPaid > 0 ? 'partial' : 'pending'),
            'created_at' => now(),
            'updated_at' => now(),
        ];

        DB::table('invoices')->insert($invoiceData);
    }

    public function invoice($bookingId)
    {
        $booking = DB::table('bookings')
            ->join('rooms', 'bookings.room_id', '=', 'rooms.id')
            ->leftJoin('room_types', 'rooms.room_type_id', '=', 'room_types.id')
            ->select('bookings.*', 'rooms.room_number', 'room_types.name as room_type_name')
            ->where('bookings.id', $bookingId)
            ->first();

        if (!$booking) {
            abort(404);
        }

        $invoice = DB::table('invoices')
            ->where('booking_id', $bookingId)
            ->first();

        if (!$invoice) {
            // Create invoice if missing
            $this->createInvoice($bookingId, (array)$booking);
            $invoice = DB::table('invoices')->where('booking_id', $bookingId)->first();
        }

        // Get LIVE payment data - sum of all payments for this booking
        $totalPaid = DB::table('payments')
            ->where('booking_id', $bookingId)
            ->sum('amount');

        // Calculate current balance from LIVE data
        $currentBalanceDue = max(0, $booking->total_amount - $totalPaid);
        
        // Determine current status based on payment
        $currentStatus = 'pending';
        if ($currentBalanceDue <= 0) {
            $currentStatus = 'paid';
        } elseif ($totalPaid > 0) {
            $currentStatus = 'partial';
        }

        // Build updated invoice data object with LIVE values
        $invoiceData = (object)[
            'id' => $invoice->id ?? null,
            'booking_id' => $bookingId,
            'invoice_number' => $invoice->invoice_number ?? ('INV-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4))),
            'guest_name' => $booking->guest_name,
            'room_number' => $booking->room_number,
            'room_type_name' => $booking->room_type_name,
            'check_in_date' => $booking->check_in_date,
            'check_out_date' => $booking->check_out_date,
            'nights' => $invoice->nights ?? 1,
            'room_rate' => $invoice->room_rate ?? $booking->total_amount,
            'subtotal' => $booking->total_amount / 1.12, // Remove tax
            'tax_amount' => $booking->total_amount - ($booking->total_amount / 1.12),
            'total_amount' => $booking->total_amount,
            'amount_paid' => $totalPaid,
            'balance_due' => $currentBalanceDue,
            'status' => $currentStatus,
            'payment_type' => $booking->payment_type,
            'payment_reference' => $booking->payment_reference,
            'created_at' => $invoice->created_at ?? now(),
            'updated_at' => now(),
            'printed_at' => $invoice->printed_at ?? null,
        ];

        // Update the invoices table with current data
        if ($invoice) {
            DB::table('invoices')->where('id', $invoice->id)->update([
                'amount_paid' => $totalPaid,
                'balance_due' => $currentBalanceDue,
                'status' => $currentStatus,
                'updated_at' => now(),
            ]);
        }

        // Get lodge settings
        $settingsRow = DB::table('settings')->first();
        $settings = $settingsRow ? (array)$settingsRow : [];

        return view('bookings.invoice', compact('booking', 'invoice'))
            ->with('invoice', $invoiceData)
            ->with('settings', $settings);
    }

    public function printInvoice($bookingId)
    {
        $booking = DB::table('bookings')
            ->join('rooms', 'bookings.room_id', '=', 'rooms.id')
            ->leftJoin('room_types', 'rooms.room_type_id', '=', 'room_types.id')
            ->select('bookings.*', 'rooms.room_number', 'room_types.name as room_type_name')
            ->where('bookings.id', $bookingId)
            ->first();

        if (!$booking) {
            abort(404);
        }

        $invoice = DB::table('invoices')->where('booking_id', $bookingId)->first();

        // Get LIVE payment data
        $totalPaid = DB::table('payments')
            ->where('booking_id', $bookingId)
            ->sum('amount');

        // Calculate current balance from LIVE data
        $currentBalanceDue = max(0, $booking->total_amount - $totalPaid);
        
        // Determine current status based on payment
        $currentStatus = 'pending';
        if ($currentBalanceDue <= 0) {
            $currentStatus = 'paid';
        } elseif ($totalPaid > 0) {
            $currentStatus = 'partial';
        }

        // Build updated invoice data object with LIVE values
        $invoiceData = (object)[
            'id' => $invoice->id ?? null,
            'booking_id' => $bookingId,
            'invoice_number' => $invoice->invoice_number ?? ('INV-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4))),
            'guest_name' => $booking->guest_name,
            'room_number' => $booking->room_number,
            'room_type_name' => $booking->room_type_name,
            'check_in_date' => $booking->check_in_date,
            'check_out_date' => $booking->check_out_date,
            'nights' => $invoice->nights ?? 1,
            'room_rate' => $invoice->room_rate ?? $booking->total_amount,
            'subtotal' => $booking->total_amount / 1.12,
            'tax_amount' => $booking->total_amount - ($booking->total_amount / 1.12),
            'total_amount' => $booking->total_amount,
            'amount_paid' => $totalPaid,
            'balance_due' => $currentBalanceDue,
            'status' => $currentStatus,
            'payment_type' => $booking->payment_type,
            'payment_reference' => $booking->payment_reference,
            'created_at' => $invoice->created_at ?? now(),
            'printed_at' => now(),
        ];

        // Update invoices table and mark as printed
        if ($invoice) {
            DB::table('invoices')->where('id', $invoice->id)->update([
                'amount_paid' => $totalPaid,
                'balance_due' => $currentBalanceDue,
                'status' => $currentStatus,
                'printed_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $settingsRow = DB::table('settings')->first();
        $settings = $settingsRow ? (array)$settingsRow : [];

        return view('bookings.invoice-print', compact('booking'))
            ->with('invoice', $invoiceData)
            ->with('settings', $settings);
    }

    public function show($id)
    {
        $booking = DB::table('bookings')
            ->join('rooms', 'bookings.room_id', '=', 'rooms.id')
            ->select('bookings.*', 'rooms.room_number')
            ->where('bookings.id', $id)
            ->first();

        if (!$booking) {
            abort(404);
        }

        return view('bookings.show', compact('booking'));
    }

    public function edit($id)
    {
        $booking = DB::table('bookings')->find($id);
        if (!$booking) {
            abort(404);
        }

        // Show available rooms plus the currently booked room
        $rooms = DB::table('rooms')
            ->leftJoin('room_types', 'rooms.room_type_id', '=', 'room_types.id')
            ->where(function ($query) use ($booking) {
                $query->where('rooms.status', 'available')
                      ->orWhere('rooms.id', $booking->room_id);
            })
            ->select('rooms.*', 'room_types.name as room_type_name', 'room_types.base_price as price')
            ->get();

        return view('bookings.edit', compact('booking', 'rooms'));
    }

    public function update(Request $request, $id)
    {
        $booking = DB::table('bookings')->find($id);
        if (!$booking) {
            abort(404);
        }

        $data = $request->validate([
            'guest_name'       => 'sometimes|string',
            'guest_email'      => 'nullable|email',
            'guest_phone'      => 'nullable|string',
            'room_id'          => 'sometimes|exists:rooms,id',
            'check_in_date'    => 'sometimes|date',
            'check_in_time'    => 'nullable|date_format:H:i',
            'check_out_date'   => 'sometimes|date|after:check_in_date',
            'check_out_time'   => 'nullable|date_format:H:i',
            'adults'           => 'integer|min:1',
            'children'         => 'integer|min:0',
            'total_amount'     => 'numeric|min:0',
            'retainer_paid'    => 'nullable|numeric|min:0',
            'payment_type'     => 'nullable|in:cash,crdb',
            'payment_reference'=> 'nullable|string',
            'special_requests' => 'nullable|string',
            'status'           => 'sometimes|in:pending,confirmed,cancelled,checked_in,checked_out',
        ]);

        // Recalculate balance
        if (isset($data['total_amount']) || isset($data['retainer_paid'])) {
            $total = $data['total_amount'] ?? $booking->total_amount;
            $retainer = $data['retainer_paid'] ?? $booking->retainer_paid;
            $data['balance_due'] = $total - $retainer;
        }

        // If updating from pending to confirmed, create invoice
        $wasPending = $booking->status === 'pending';
        $isConfirmed = isset($data['status']) && $data['status'] === 'confirmed';
        if ($wasPending && $isConfirmed) {
            $invoiceExists = DB::table('invoices')->where('booking_id', $id)->exists();
            if (!$invoiceExists) {
                $invoiceData = [
                    'guest_name' => $data['guest_name'] ?? $booking->guest_name,
                    'guest_email' => $data['guest_email'] ?? $booking->guest_email,
                    'guest_phone' => $data['guest_phone'] ?? $booking->guest_phone,
                    'room_id' => $data['room_id'] ?? $booking->room_id,
                    'check_in_date' => $data['check_in_date'] ?? $booking->check_in_date,
                    'check_out_date' => $data['check_out_date'] ?? $booking->check_out_date,
                    'total_amount' => $data['total_amount'] ?? $booking->total_amount,
                    'retainer_paid' => $data['retainer_paid'] ?? $booking->retainer_paid,
                    'payment_type' => $data['payment_type'] ?? $booking->payment_type,
                    'payment_reference' => $data['payment_reference'] ?? $booking->payment_reference,
                ];
                $this->createInvoice($id, $invoiceData);
            }
        }

        $data['updated_at'] = now();

        DB::table('bookings')->where('id', $id)->update($data);

        return redirect()->route('bookings.show', $id)->with('success', 'Booking updated successfully');
    }

    public function confirmBooking(Request $request, $id)
    {
        $booking = DB::table('bookings')->find($id);
        if (!$booking) {
            abort(404);
        }

        $data = $request->validate([
            'payment_type' => 'required|in:cash,crdb',
            'payment_reference' => 'required|string',
            'retainer_paid' => 'nullable|numeric|min:0',
        ]);

        $retainer = $data['retainer_paid'] ?? $booking->retainer_paid ?? 0;
        $newBalance = $booking->total_amount - $retainer;

        DB::table('bookings')->where('id', $id)->update([
            'status' => 'confirmed',
            'payment_type' => $data['payment_type'],
            'payment_reference' => $data['payment_reference'],
            'retainer_paid' => $retainer,
            'balance_due' => $newBalance,
            'updated_at' => now(),
        ]);

        // Create invoice if not exists
        $invoiceExists = DB::table('invoices')->where('booking_id', $id)->exists();
        if (!$invoiceExists) {
            $invoiceData = [
                'guest_name' => $booking->guest_name,
                'guest_email' => $booking->guest_email,
                'guest_phone' => $booking->guest_phone,
                'room_id' => $booking->room_id,
                'check_in_date' => $booking->check_in_date,
                'check_out_date' => $booking->check_out_date,
                'total_amount' => $booking->total_amount,
                'retainer_paid' => $retainer,
                'payment_type' => $data['payment_type'],
                'payment_reference' => $data['payment_reference'],
            ];
            $this->createInvoice($id, $invoiceData);
        }

        return redirect()->route('bookings.show', $id)->with('success', 'Booking confirmed and invoice generated.');
    }

    public function destroy($id)
    {
        $booking = DB::table('bookings')->find($id);
        if (!$booking) {
            abort(404);
        }

        // Free up the room if not already checked out
        if ($booking->status !== 'checked_out') {
            DB::table('rooms')->where('id', $booking->room_id)->update(['status' => 'available']);
        }

        DB::table('bookings')->where('id', $id)->delete();

        return redirect()->route('bookings')->with('success', 'Booking cancelled successfully');
    }

    public function checkIn($id)
    {
        $booking = DB::table('bookings')->find($id);
        if (!$booking) {
            abort(404);
        }

        DB::table('bookings')->where('id', $id)->update([
            'status'       => 'checked_in',
            'updated_at'   => now(),
        ]);

        DB::table('rooms')->where('id', $booking->room_id)->update(['status' => 'occupied']);

        return redirect()->route('bookings.show', $id)->with('success', 'Guest checked in successfully');
    }

    public function checkOut($id)
    {
        $booking = DB::table('bookings')->find($id);
        if (!$booking) {
            abort(404);
        }

        DB::table('bookings')->where('id', $id)->update([
            'status'       => 'checked_out',
            'actual_checkout' => now(),
            'updated_at'   => now(),
        ]);

        // Room goes to awaiting cleaning instead of available
        DB::table('rooms')->where('id', $booking->room_id)->update(['status' => 'awaiting_cleaning']);

        // If there's a balance due, redirect to billing with notification
        if ($booking->balance_due > 0) {
            return redirect()->route('billing.show', $id)
                ->with('warning', 'Guest checked out successfully. Outstanding balance of $' . number_format($booking->balance_due, 2) . ' requires payment.');
        }

        return redirect()->route('bookings.show', $id)->with('success', 'Guest checked out successfully. No outstanding balance.');
    }
}
