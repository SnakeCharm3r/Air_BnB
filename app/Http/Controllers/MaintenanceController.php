<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MaintenanceController extends Controller
{
    public function index()
    {
        $tasks = DB::table('maintenance_tasks')
            ->leftJoin('rooms', 'maintenance_tasks.room_id', '=', 'rooms.id')
            ->leftJoin('staff', 'maintenance_tasks.assigned_to', '=', 'staff.id')
            ->select(
                'maintenance_tasks.*',
                'rooms.room_number',
                'staff.full_name as assigned_to_name'
            )
            ->orderByDesc('maintenance_tasks.created_at')
            ->get();

        return response()->json($tasks);
    }

    public function show($id)
    {
        $task = DB::table('maintenance_tasks')->find($id);
        if (! $task) return response()->json(['message' => 'Not found'], 404);
        return response()->json($task);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'          => 'required|string',
            'description'    => 'nullable|string',
            'room_id'        => 'nullable|exists:rooms,id',
            'priority'       => 'in:low,medium,high,urgent',
            'assigned_to'    => 'nullable|exists:staff,id',
            'scheduled_date' => 'nullable|date',
            'cost'           => 'nullable|numeric',
        ]);

        $data['status']     = 'pending';
        $data['created_by'] = $request->user()->id;
        $data['created_at'] = now();
        $data['updated_at'] = now();

        $id = DB::table('maintenance_tasks')->insertGetId($data);
        return response()->json(DB::table('maintenance_tasks')->find($id), 201);
    }

    public function update(Request $request, $id)
    {
        $task = DB::table('maintenance_tasks')->find($id);
        if (! $task) return response()->json(['message' => 'Not found'], 404);

        $data = $request->validate([
            'title'          => 'sometimes|string',
            'description'    => 'nullable|string',
            'room_id'        => 'nullable|exists:rooms,id',
            'priority'       => 'sometimes|in:low,medium,high,urgent',
            'status'         => 'sometimes|in:pending,in_progress,completed',
            'assigned_to'    => 'nullable|exists:staff,id',
            'scheduled_date' => 'nullable|date',
            'cost'           => 'nullable|numeric',
        ]);

        if (isset($data['status']) && $data['status'] === 'completed') {
            $data['completed_at'] = now();
        }

        DB::table('maintenance_tasks')->where('id', $id)->update(array_merge($data, ['updated_at' => now()]));
        return response()->json(DB::table('maintenance_tasks')->find($id));
    }

    public function destroy($id)
    {
        DB::table('maintenance_tasks')->where('id', $id)->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
