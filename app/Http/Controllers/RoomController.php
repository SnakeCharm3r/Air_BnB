<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoomController extends Controller
{
    public function index()
    {
        $rooms = DB::table('rooms')
            ->join('room_types', 'rooms.room_type_id', '=', 'room_types.id')
            ->select(
                'rooms.*',
                'room_types.name as room_type_name',
                'room_types.base_price as price',
                'room_types.capacity'
            )
            ->get();

        return response()->json($rooms);
    }

    public function available(Request $request)
    {
        $query = DB::table('rooms')
            ->join('room_types', 'rooms.room_type_id', '=', 'room_types.id')
            ->select('rooms.*', 'room_types.name as room_type_name', 'room_types.base_price as price', 'room_types.capacity')
            ->where('rooms.status', 'available');

        if ($request->check_in && $request->check_out) {
            $booked = DB::table('bookings')
                ->whereIn('status', ['confirmed', 'checked_in'])
                ->where('check_in_date', '<', $request->check_out)
                ->where('check_out_date', '>', $request->check_in)
                ->pluck('room_id');

            $query->whereNotIn('rooms.id', $booked);
        }

        return response()->json($query->get());
    }

    public function show($id)
    {
        $room = DB::table('rooms')
            ->join('room_types', 'rooms.room_type_id', '=', 'room_types.id')
            ->select('rooms.*', 'room_types.name as room_type_name', 'room_types.base_price as price', 'room_types.capacity')
            ->where('rooms.id', $id)
            ->first();

        if (! $room) {
            return response()->json(['message' => 'Room not found'], 404);
        }

        return response()->json($room);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'room_number'  => 'required|string|unique:rooms',
            'room_type_id' => 'required|exists:room_types,id',
            'floor'        => 'required|integer',
            'status'       => 'in:available,booked,occupied,awaiting_cleaning,maintenance',
            'notes'        => 'nullable|string',
        ]);

        $id = DB::table('rooms')->insertGetId(array_merge($data, [
            'created_at' => now(),
            'updated_at' => now(),
        ]));

        return response()->json(DB::table('rooms')->find($id), 201);
    }

    public function update(Request $request, $id)
    {
        $room = DB::table('rooms')->find($id);
        if (! $room) {
            return response()->json(['message' => 'Room not found'], 404);
        }

        $data = $request->validate([
            'room_number'  => 'sometimes|string',
            'room_type_id' => 'sometimes|exists:room_types,id',
            'floor'        => 'sometimes|integer',
            'status'       => 'sometimes|in:available,booked,occupied,awaiting_cleaning,maintenance',
            'notes'        => 'nullable|string',
        ]);

        DB::table('rooms')->where('id', $id)->update(array_merge($data, ['updated_at' => now()]));

        return response()->json(DB::table('rooms')->find($id));
    }

    public function destroy($id)
    {
        DB::table('rooms')->where('id', $id)->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
