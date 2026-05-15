<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoomController extends Controller
{
    public function index()
    {
        $rooms = DB::table('rooms')
            ->join('room_types', 'rooms.room_type_id', '=', 'room_types.id')
            ->select('rooms.*', 'room_types.name as type_name')
            ->orderBy('rooms.room_number')
            ->get();

        $roomTypes = DB::table('room_types')->get();

        return view('rooms.index', compact('rooms', 'roomTypes'));
    }

    public function show($id)
    {
        $room = DB::table('rooms')
            ->join('room_types', 'rooms.room_type_id', '=', 'room_types.id')
            ->select('rooms.*', 'room_types.name as type_name', 'room_types.description as type_description', 'room_types.base_price', 'room_types.currency', 'room_types.capacity', 'room_types.amenities')
            ->where('rooms.id', $id)
            ->first();

        if (!$room) {
            abort(404);
        }

        // Get current booking if room is occupied or booked
        $currentBooking = null;
        if (in_array($room->status, ['occupied', 'booked'])) {
            $currentBooking = DB::table('bookings')
                ->where('room_id', $id)
                ->whereIn('status', ['confirmed', 'checked_in'])
                ->first();
        }

        return view('rooms.show', compact('room', 'currentBooking'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'room_number'  => 'required|string|unique:rooms',
            'room_type_id' => 'required|exists:room_types,id',
            'floor'        => 'required|integer|min:1',
            'status'       => 'in:available,booked,occupied,awaiting_cleaning,maintenance',
            'notes'        => 'nullable|string',
        ]);

        $data['created_at'] = now();
        $data['updated_at'] = now();

        DB::table('rooms')->insert($data);

        return redirect()->route('web.rooms')->with('success', 'Room added successfully');
    }

    public function update(Request $request, $id)
    {
        $room = DB::table('rooms')->find($id);
        if (!$room) {
            abort(404);
        }

        $data = $request->validate([
            'room_number'  => 'sometimes|string|unique:rooms,room_number,' . $id,
            'room_type_id' => 'sometimes|exists:room_types,id',
            'floor'        => 'sometimes|integer|min:1',
            'status'       => 'sometimes|in:available,booked,occupied,awaiting_cleaning,maintenance',
            'notes'        => 'nullable|string',
        ]);

        $data['updated_at'] = now();

        DB::table('rooms')->where('id', $id)->update($data);

        return redirect()->route('web.rooms.show', $id)->with('success', 'Room updated successfully');
    }
}
