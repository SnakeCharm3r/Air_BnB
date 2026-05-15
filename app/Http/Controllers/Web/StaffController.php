<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StaffController extends Controller
{
    public function index()
    {
        $staff = DB::table('staff')
            ->select('id', 'full_name as name', 'role', 'department', 'phone', 'email', 'hire_date', 'is_active as status')
            ->orderBy('full_name')
            ->get()
            ->map(function ($s) {
                $s->status = $s->status ? 'active' : 'inactive';
                return $s;
            });

        return view('staff.index', compact('staff'));
    }

    public function show($id)
    {
        $staff = DB::table('staff')->find($id);
        if (!$staff) {
            abort(404);
        }

        // Get attendance records
        $attendance = DB::table('attendance')
            ->where('staff_id', $id)
            ->orderByDesc('date')
            ->limit(30)
            ->get();

        return view('staff.show', compact('staff', 'attendance'));
    }

    public function create()
    {
        return view('staff.create');
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

        DB::table('staff')->insert($data);

        return redirect()->route('staff.index')->with('success', 'Staff member added successfully');
    }

    public function edit($id)
    {
        $staff = DB::table('staff')->find($id);
        if (!$staff) {
            abort(404);
        }

        return view('staff.edit', compact('staff'));
    }

    public function update(Request $request, $id)
    {
        $staff = DB::table('staff')->find($id);
        if (!$staff) {
            abort(404);
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

        return redirect()->route('staff.show', $id)->with('success', 'Staff member updated successfully');
    }

    public function destroy($id)
    {
        DB::table('staff')->where('id', $id)->delete();
        return redirect()->route('staff.index')->with('success', 'Staff member deleted successfully');
    }

    public function attendance($id)
    {
        $staff = DB::table('staff')->find($id);
        if (!$staff) {
            abort(404);
        }

        $records = DB::table('attendance')
            ->where('staff_id', $id)
            ->orderByDesc('date')
            ->limit(30)
            ->get();

        return view('staff.attendance', compact('staff', 'records'));
    }
}
