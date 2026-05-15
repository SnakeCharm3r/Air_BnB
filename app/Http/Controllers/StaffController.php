<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StaffController extends Controller
{
    public function index()
    {
        $staff = DB::table('staff')
            ->select('id', 'full_name as name', 'role', 'department', 'phone', 'email', 'hire_date', 'is_active as status')
            ->get()
            ->map(function ($s) {
                $s->status = $s->status ? 'active' : 'inactive';
                return $s;
            });

        return response()->json($staff);
    }

    public function show($id)
    {
        $staff = DB::table('staff')->find($id);
        if (! $staff) {
            return response()->json(['message' => 'Staff not found'], 404);
        }
        return response()->json($staff);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'full_name'  => 'required|string',
            'role'       => 'required|string',
            'department' => 'nullable|string',
            'phone'      => 'nullable|string',
            'email'      => 'nullable|email',
            'hire_date'  => 'nullable|date',
            'salary'     => 'nullable|numeric',
        ]);

        $data['is_active'] = true;
        $data['created_at'] = now();
        $data['updated_at'] = now();

        $id = DB::table('staff')->insertGetId($data);

        return response()->json(DB::table('staff')->find($id), 201);
    }

    public function update(Request $request, $id)
    {
        $staff = DB::table('staff')->find($id);
        if (! $staff) {
            return response()->json(['message' => 'Staff not found'], 404);
        }

        $data = $request->validate([
            'full_name'  => 'sometimes|string',
            'role'       => 'sometimes|string',
            'department' => 'nullable|string',
            'phone'      => 'nullable|string',
            'email'      => 'nullable|email',
            'hire_date'  => 'nullable|date',
            'salary'     => 'nullable|numeric',
            'is_active'  => 'sometimes|boolean',
        ]);

        DB::table('staff')->where('id', $id)->update(array_merge($data, ['updated_at' => now()]));

        return response()->json(DB::table('staff')->find($id));
    }

    public function destroy($id)
    {
        DB::table('staff')->where('id', $id)->delete();
        return response()->json(['message' => 'Deleted']);
    }

    public function attendance($id)
    {
        $records = DB::table('attendance')
            ->where('staff_id', $id)
            ->orderByDesc('date')
            ->limit(30)
            ->get();

        return response()->json($records);
    }
}
