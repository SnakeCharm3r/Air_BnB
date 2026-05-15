<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InfrastructureController extends Controller
{
    public function index()
    {
        return response()->json(DB::table('infrastructure_devices')->get());
    }

    public function show($id)
    {
        $device = DB::table('infrastructure_devices')->find($id);
        if (! $device) return response()->json(['message' => 'Device not found'], 404);
        return response()->json($device);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string',
            'device_type' => 'required|in:cctv,water_pump,generator,solar,ac,other',
            'location'    => 'nullable|string',
            'status'      => 'in:online,offline,maintenance,error',
            'config'      => 'nullable|string',
        ]);

        $data['created_at'] = now();
        $data['updated_at'] = now();

        $id = DB::table('infrastructure_devices')->insertGetId($data);
        return response()->json(DB::table('infrastructure_devices')->find($id), 201);
    }

    public function update(Request $request, $id)
    {
        $device = DB::table('infrastructure_devices')->find($id);
        if (! $device) return response()->json(['message' => 'Device not found'], 404);

        $data = $request->validate([
            'name'         => 'sometimes|string',
            'device_type'  => 'sometimes|in:cctv,water_pump,generator,solar,ac,other',
            'location'     => 'nullable|string',
            'status'       => 'sometimes|in:online,offline,maintenance,error',
            'config'       => 'nullable|string',
            'last_checked' => 'nullable|date',
        ]);

        DB::table('infrastructure_devices')->where('id', $id)->update(array_merge($data, ['updated_at' => now()]));
        return response()->json(DB::table('infrastructure_devices')->find($id));
    }

    public function destroy($id)
    {
        DB::table('infrastructure_devices')->where('id', $id)->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
