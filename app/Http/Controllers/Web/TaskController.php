<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = DB::table('tasks')
            ->leftJoin('staff', 'tasks.assigned_to', '=', 'staff.id')
            ->leftJoin('rooms', 'tasks.room_id', '=', 'rooms.id')
            ->select(
                'tasks.*',
                'staff.full_name as assigned_to_name',
                'rooms.room_number'
            )
            ->orderByDesc('tasks.created_at')
            ->get();

        // Get staff for dropdown
        $staff = DB::table('staff')->where('is_active', true)->get();
        $rooms = DB::table('rooms')->get();

        return view('tasks.index', compact('tasks', 'staff', 'rooms'));
    }

    public function show($id)
    {
        $task = DB::table('tasks')
            ->leftJoin('staff', 'tasks.assigned_to', '=', 'staff.id')
            ->leftJoin('rooms', 'tasks.room_id', '=', 'rooms.id')
            ->select('tasks.*', 'staff.full_name as assigned_to_name', 'rooms.room_number')
            ->where('tasks.id', $id)
            ->first();

        if (!$task) {
            abort(404);
        }

        return view('tasks.show', compact('task'));
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
        ]);

        $data['status']     = 'pending';
        $data['created_by'] = auth()->id();
        $data['created_at'] = now();
        $data['updated_at'] = now();

        $id = DB::table('tasks')->insertGetId($data);

        return redirect()->route('tasks.index')->with('success', 'Task created successfully');
    }

    public function update(Request $request, $id)
    {
        $task = DB::table('tasks')->find($id);
        if (!$task) {
            abort(404);
        }

        $data = $request->validate([
            'title'          => 'sometimes|string',
            'description'    => 'nullable|string',
            'room_id'        => 'nullable|exists:rooms,id',
            'priority'       => 'sometimes|in:low,medium,high,urgent',
            'status'         => 'sometimes|in:pending,in_progress,completed',
            'assigned_to'    => 'nullable|exists:staff,id',
            'scheduled_date' => 'nullable|date',
        ]);

        if (isset($data['status']) && $data['status'] === 'completed') {
            $data['completed_at'] = now();
        }

        $data['updated_at'] = now();

        DB::table('tasks')->where('id', $id)->update($data);

        return redirect()->route('tasks.index')->with('success', 'Task updated successfully');
    }

    public function destroy($id)
    {
        DB::table('tasks')->where('id', $id)->delete();
        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully');
    }
}
