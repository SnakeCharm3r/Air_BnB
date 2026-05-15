<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CostController extends Controller
{
    public function index()
    {
        return response()->json(
            DB::table('operational_costs')->orderByDesc('date')->get()
        );
    }

    public function show($id)
    {
        $cost = DB::table('operational_costs')->find($id);
        if (! $cost) return response()->json(['message' => 'Cost not found'], 404);
        return response()->json($cost);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category'    => 'required|string',
            'description' => 'required|string',
            'amount'      => 'required|numeric',
            'date'        => 'required|date',
            'department'  => 'nullable|string',
            'reference'   => 'nullable|string',
        ]);

        $data['created_by'] = $request->user()->id;
        $data['created_at'] = now();
        $data['updated_at'] = now();

        $id = DB::table('operational_costs')->insertGetId($data);
        return response()->json(DB::table('operational_costs')->find($id), 201);
    }

    public function update(Request $request, $id)
    {
        $cost = DB::table('operational_costs')->find($id);
        if (! $cost) return response()->json(['message' => 'Cost not found'], 404);

        $data = $request->validate([
            'category'    => 'sometimes|string',
            'description' => 'sometimes|string',
            'amount'      => 'sometimes|numeric',
            'date'        => 'sometimes|date',
            'department'  => 'nullable|string',
            'reference'   => 'nullable|string',
        ]);

        DB::table('operational_costs')->where('id', $id)->update(array_merge($data, ['updated_at' => now()]));
        return response()->json(DB::table('operational_costs')->find($id));
    }

    public function destroy($id)
    {
        DB::table('operational_costs')->where('id', $id)->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
