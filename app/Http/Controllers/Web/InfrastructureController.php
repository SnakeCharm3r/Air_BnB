<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InfrastructureController extends Controller
{
    public function index()
    {
        $devices = DB::table('infrastructure_devices')
            ->orderBy('name')
            ->get();

        // Count by status
        $statusCounts = DB::table('infrastructure_devices')
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();

        return view('infrastructure.index', compact('devices', 'statusCounts'));
    }

    public function show($id)
    {
        $device = DB::table('infrastructure_devices')->find($id);
        if (!$device) {
            abort(404);
        }

        // Get device logs if table exists
        $logs = [];
        try {
            $logs = DB::table('infrastructure_logs')
                ->where('device_id', $id)
                ->orderByDesc('created_at')
                ->limit(50)
                ->get();
        } catch (\Exception $e) {
            // Table might not exist
        }

        return view('infrastructure.show', compact('device', 'logs'));
    }

    public function create()
    {
        return view('infrastructure.create');
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

        DB::table('infrastructure_devices')->insert($data);

        return redirect()->route('infrastructure.index')->with('success', 'Device added successfully');
    }

    public function edit($id)
    {
        $device = DB::table('infrastructure_devices')->find($id);
        if (!$device) {
            abort(404);
        }

        return view('infrastructure.edit', compact('device'));
    }

    public function update(Request $request, $id)
    {
        $device = DB::table('infrastructure_devices')->find($id);
        if (!$device) {
            abort(404);
        }

        $data = $request->validate([
            'name'         => 'sometimes|string',
            'device_type'  => 'sometimes|in:cctv,water_pump,generator,solar,ac,other',
            'location'     => 'nullable|string',
            'status'       => 'sometimes|in:online,offline,maintenance,error',
            'config'       => 'nullable|string',
            'last_checked' => 'nullable|date',
        ]);

        $data['updated_at'] = now();

        DB::table('infrastructure_devices')->where('id', $id)->update($data);

        return redirect()->route('infrastructure.index')->with('success', 'Device updated successfully');
    }

    public function destroy($id)
    {
        DB::table('infrastructure_devices')->where('id', $id)->delete();
        return redirect()->route('infrastructure.index')->with('success', 'Device deleted successfully');
    }

    public function toggleStatus($id)
    {
        $device = DB::table('infrastructure_devices')->find($id);
        if (!$device) {
            abort(404);
        }

        $newStatus = $device->status === 'online' ? 'offline' : 'online';

        DB::table('infrastructure_devices')->where('id', $id)->update([
            'status'       => $newStatus,
            'last_checked' => now(),
            'updated_at'   => now(),
        ]);

        return redirect()->route('infrastructure.index')->with('success', 'Device status updated');
    }
}
