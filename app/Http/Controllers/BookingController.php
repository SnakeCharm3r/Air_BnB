<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = DB::table('bookings')
            ->join('rooms', 'bookings.room_id', '=', 'rooms.id')
            ->join('room_types', 'rooms.room_type_id', '=', 'room_types.id')
            ->select(
                'bookings.*',
                'rooms.room_number',
                'room_types.name as room_type_name'
            )
            ->orderByDesc('bookings.created_at')
            ->get();

        return response()->json($bookings);
    }

    public function show($id)
    {
        $booking = DB::table('bookings')
            ->join('rooms', 'bookings.room_id', '=', 'rooms.id')
            ->join('room_types', 'rooms.room_type_id', '=', 'room_types.id')
            ->select('bookings.*', 'rooms.room_number', 'room_types.name as room_type_name')
            ->where('bookings.id', $id)
            ->first();

        if (! $booking) {
            return response()->json(['message' => 'Booking not found'], 404);
        }

        return response()->json($booking);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'guest_name'       => 'required|string',
            'guest_email'      => 'nullable|email',
            'guest_phone'      => 'nullable|string',
            'room_id'          => 'required|exists:rooms,id',
            'check_in_date'    => 'required|date',
            'check_out_date'   => 'required|date|after:check_in_date',
            'adults'           => 'integer|min:1',
            'children'         => 'integer|min:0',
            'total_amount'     => 'numeric|min:0',
            'retainer_paid'    => 'numeric|min:0',
            'special_requests' => 'nullable|string',
        ]);

        $data['booking_ref'] = 'BK-' . strtoupper(Str::random(6));
        $data['status']      = 'confirmed';
        $data['balance_due'] = ($data['total_amount'] ?? 0) - ($data['retainer_paid'] ?? 0);
        $data['created_by']  = $request->user()->id;
        $data['created_at']  = now();
        $data['updated_at']  = now();

        DB::table('rooms')->where('id', $data['room_id'])->update(['status' => 'booked']);

        $id = DB::table('bookings')->insertGetId($data);

        return response()->json(DB::table('bookings')->find($id), 201);
    }

    public function update(Request $request, $id)
    {
        $booking = DB::table('bookings')->find($id);
        if (! $booking) {
            return response()->json(['message' => 'Booking not found'], 404);
        }

        $data = $request->validate([
            'guest_name'       => 'sometimes|string',
            'guest_email'      => 'nullable|email',
            'guest_phone'      => 'nullable|string',
            'check_in_date'    => 'sometimes|date',
            'check_out_date'   => 'sometimes|date',
            'adults'           => 'sometimes|integer',
            'children'         => 'sometimes|integer',
            'total_amount'     => 'sometimes|numeric',
            'retainer_paid'    => 'sometimes|numeric',
            'balance_due'      => 'sometimes|numeric',
            'status'           => 'sometimes|in:pending,confirmed,checked_in,checked_out,cancelled',
            'special_requests' => 'nullable|string',
        ]);

        DB::table('bookings')->where('id', $id)->update(array_merge($data, ['updated_at' => now()]));

        return response()->json(DB::table('bookings')->find($id));
    }

    public function destroy($id)
    {
        $booking = DB::table('bookings')->find($id);
        if ($booking) {
            DB::table('rooms')->where('id', $booking->room_id)->update(['status' => 'available']);
            DB::table('bookings')->where('id', $id)->delete();
        }
        return response()->json(['message' => 'Deleted']);
    }

    public function checkIn(Request $request, $id)
    {
        $booking = DB::table('bookings')->find($id);
        if (! $booking) {
            return response()->json(['message' => 'Booking not found'], 404);
        }

        DB::table('bookings')->where('id', $id)->update(['status' => 'checked_in', 'updated_at' => now()]);
        DB::table('rooms')->where('id', $booking->room_id)->update(['status' => 'occupied']);

        return response()->json(['message' => 'Checked in', 'booking' => DB::table('bookings')->find($id)]);
    }

    public function checkOut(Request $request, $id)
    {
        $booking = DB::table('bookings')->find($id);
        if (! $booking) {
            return response()->json(['message' => 'Booking not found'], 404);
        }

        DB::table('bookings')->where('id', $id)->update(['status' => 'checked_out', 'updated_at' => now()]);
        DB::table('rooms')->where('id', $booking->room_id)->update(['status' => 'awaiting_cleaning']);

        return response()->json(['message' => 'Checked out', 'booking' => DB::table('bookings')->find($id)]);
    }

    public function todayCheckIns()
    {
        $bookings = DB::table('bookings')
            ->join('rooms', 'bookings.room_id', '=', 'rooms.id')
            ->select('bookings.*', 'rooms.room_number')
            ->where('bookings.check_in_date', now()->toDateString())
            ->whereIn('bookings.status', ['confirmed', 'checked_in'])
            ->get();

        return response()->json($bookings);
    }

    public function todayCheckOuts()
    {
        $bookings = DB::table('bookings')
            ->join('rooms', 'bookings.room_id', '=', 'rooms.id')
            ->select('bookings.*', 'rooms.room_number')
            ->where('bookings.check_out_date', now()->toDateString())
            ->whereIn('bookings.status', ['checked_in', 'checked_out'])
            ->get();

        return response()->json($bookings);
    }
}
