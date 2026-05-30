<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CostController extends Controller
{
    public function index()
    {
        $costs = DB::table('operational_costs')
            ->orderByDesc('date')
            ->get();

        // Calculate totals by category
        $totals = DB::table('operational_costs')
            ->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->get();

        return view('costs.index', compact('costs', 'totals'));
    }

    public function show($id)
    {
        $cost = DB::table('operational_costs')->find($id);
        if (!$cost) {
            abort(404);
        }

        return view('costs.show', compact('cost'));
    }

    public function create()
    {
        return view('costs.create');
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

        $data['created_by'] = auth()->id();
        $data['created_at'] = now();
        $data['updated_at'] = now();

        DB::table('operational_costs')->insert($data);

        return redirect()->route('costs.index')->with('success', 'Cost recorded successfully');
    }

    public function edit($id)
    {
        $cost = DB::table('operational_costs')->find($id);
        if (!$cost) {
            abort(404);
        }

        return view('costs.edit', compact('cost'));
    }

    public function update(Request $request, $id)
    {
        $cost = DB::table('operational_costs')->find($id);
        if (!$cost) {
            abort(404);
        }

        $data = $request->validate([
            'category'    => 'sometimes|string',
            'description' => 'sometimes|string',
            'amount'      => 'sometimes|numeric',
            'date'        => 'sometimes|date',
            'department'  => 'nullable|string',
            'reference'   => 'nullable|string',
        ]);

        $data['updated_at'] = now();

        DB::table('operational_costs')->where('id', $id)->update($data);

        return redirect()->route('costs.index')->with('success', 'Cost updated successfully');
    }

    public function destroy($id)
    {
        DB::table('operational_costs')->where('id', $id)->delete();
        return redirect()->route('costs.index')->with('success', 'Cost deleted successfully');
    }
}
