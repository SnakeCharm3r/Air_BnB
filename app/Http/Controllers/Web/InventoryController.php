<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function index()
    {
        $items = DB::table('inventory_items')
            ->orderBy('name')
            ->get()
            ->map(function ($item) {
                $item->is_low_stock = $item->quantity <= $item->min_threshold;
                return $item;
            });

        return view('inventory.index', compact('items'));
    }

    public function show($id)
    {
        $item = DB::table('inventory_items')->find($id);
        if (!$item) {
            abort(404);
        }

        // Get usage history
        $usage = DB::table('inventory_usage')
            ->where('item_id', $id)
            ->orderByDesc('date')
            ->limit(30)
            ->get();

        return view('inventory.show', compact('item', 'usage'));
    }

    public function create()
    {
        return view('inventory.create');
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

        DB::table('inventory_items')->insert($data);

        return redirect()->route('inventory.index')->with('success', 'Inventory item added successfully');
    }

    public function edit($id)
    {
        $item = DB::table('inventory_items')->find($id);
        if (!$item) {
            abort(404);
        }

        return view('inventory.edit', compact('item'));
    }

    public function update(Request $request, $id)
    {
        $item = DB::table('inventory_items')->find($id);
        if (!$item) {
            abort(404);
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

        $data['updated_at'] = now();

        DB::table('inventory_items')->where('id', $id)->update($data);

        return redirect()->route('inventory.index')->with('success', 'Inventory item updated successfully');
    }

    public function destroy($id)
    {
        DB::table('inventory_items')->where('id', $id)->delete();
        return redirect()->route('inventory.index')->with('success', 'Inventory item deleted successfully');
    }

    public function restock(Request $request, $id)
    {
        $item = DB::table('inventory_items')->find($id);
        if (!$item) {
            abort(404);
        }

        $data = $request->validate([
            'quantity' => 'required|integer|min:1',
            'notes'    => 'nullable|string',
        ]);

        // Update inventory quantity
        DB::table('inventory_items')->where('id', $id)->increment('quantity', $data['quantity']);

        // Log the restock
        DB::table('inventory_usage')->insert([
            'item_id'     => $id,
            'type'        => 'restock',
            'quantity'    => $data['quantity'],
            'notes'       => $data['notes'],
            'created_by'  => auth()->id(),
            'date'        => now(),
            'created_at'  => now(),
        ]);

        return redirect()->route('inventory.index')->with('success', 'Inventory restocked successfully');
    }
}
