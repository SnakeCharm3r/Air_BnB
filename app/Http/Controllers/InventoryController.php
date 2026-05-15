<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function index()
    {
        return response()->json(DB::table('inventory_items')->get());
    }

    public function show($id)
    {
        $item = DB::table('inventory_items')->find($id);
        if (! $item) {
            return response()->json(['message' => 'Item not found'], 404);
        }
        return response()->json($item);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string',
            'category'      => 'required|string',
            'quantity'      => 'required|integer|min:0',
            'unit'          => 'required|string',
            'min_threshold' => 'required|integer|min:0',
            'location'      => 'nullable|string',
            'supplier'      => 'nullable|string',
            'unit_cost'     => 'nullable|numeric',
        ]);

        $data['created_at'] = now();
        $data['updated_at'] = now();

        $id = DB::table('inventory_items')->insertGetId($data);

        return response()->json(DB::table('inventory_items')->find($id), 201);
    }

    public function update(Request $request, $id)
    {
        $item = DB::table('inventory_items')->find($id);
        if (! $item) {
            return response()->json(['message' => 'Item not found'], 404);
        }

        $data = $request->validate([
            'name'          => 'sometimes|string',
            'category'      => 'sometimes|string',
            'quantity'      => 'sometimes|integer|min:0',
            'unit'          => 'sometimes|string',
            'min_threshold' => 'sometimes|integer|min:0',
            'location'      => 'nullable|string',
            'supplier'      => 'nullable|string',
            'unit_cost'     => 'nullable|numeric',
        ]);

        DB::table('inventory_items')->where('id', $id)->update(array_merge($data, ['updated_at' => now()]));

        return response()->json(DB::table('inventory_items')->find($id));
    }

    public function destroy($id)
    {
        DB::table('inventory_items')->where('id', $id)->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
