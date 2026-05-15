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
        $rooms = DB::table('rooms')->where('status', 'available')->get();
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
            'check_out_date' => 'required|date|after:check_in_date',
            'adults' => 'integer|min:1',
            'children' => 'integer|min:0',
            'total_amount' => 'numeric|min:0',
            'retainer_paid' => 'numeric|min:0',
            'special_requests' => 'nullable|string',
        ]);

        $data['booking_ref'] = 'BK-' . strtoupper(Str::random(6));
        $data['status'] = 'confirmed';
        $data['balance_due'] = $data['total_amount'] - ($data['retainer_paid'] ?? 0);
        $data['created_by'] = auth()->id();

        DB::table('bookings')->insert($data);

        // Update room status
        DB::table('rooms')->where('id', $data['room_id'])->update(['status' => 'booked']);

        return redirect()->route('bookings')->with('success', 'Booking created successfully');
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
}
