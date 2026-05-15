<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoomTypeController extends Controller
{
    public function index()
    {
        $roomTypes = DB::table('room_types')
            ->orderBy('name')
            ->get();

        return view('room-types.index', compact('roomTypes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'base_price' => 'required|string|max:50',
            'currency' => 'required|in:KSH,TSH,UGX,RAND,USD,EUR,GBP',
            'capacity' => 'required|integer|min:1',
            'amenities' => 'nullable|array',
            'amenities.*' => 'string',
        ]);

        $data['amenities'] = isset($data['amenities']) ? json_encode($data['amenities']) : json_encode([]);
        $data['created_at'] = now();
        $data['updated_at'] = now();

        DB::table('room_types')->insert($data);

        return redirect()->route('room-types.index')->with('success', 'Room type added successfully');
    }

    public function update(Request $request, $id)
    {
        $roomType = DB::table('room_types')->find($id);
        if (!$roomType) {
            abort(404);
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'base_price' => 'required|string|max:50',
            'currency' => 'required|in:KSH,TSH,UGX,RAND,USD,EUR,GBP',
            'capacity' => 'required|integer|min:1',
            'amenities' => 'nullable|array',
            'amenities.*' => 'string',
        ]);

        $data['amenities'] = isset($data['amenities']) ? json_encode($data['amenities']) : json_encode([]);
        $data['updated_at'] = now();

        DB::table('room_types')->where('id', $id)->update($data);

        return redirect()->route('room-types.index')->with('success', 'Room type updated successfully');
    }

    public function destroy($id)
    {
        $roomType = DB::table('room_types')->find($id);
        if (!$roomType) {
            abort(404);
        }

        // Check if any rooms use this type
        $roomsCount = DB::table('rooms')->where('room_type_id', $id)->count();
        if ($roomsCount > 0) {
            return redirect()->route('room-types.index')->with('error', 'Cannot delete room type that is in use by ' . $roomsCount . ' room(s)');
        }

        DB::table('room_types')->where('id', $id)->delete();

        return redirect()->route('room-types.index')->with('success', 'Room type deleted successfully');
    }
}
