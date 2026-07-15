<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\KitchenHour;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MenuController extends Controller
{
    public function index()
    {
        KitchenHour::seedDefaults();

        $menuItems = MenuItem::with('inventoryItem')
            ->orderBy('name')
            ->get();

        $kitchenHours = KitchenHour::getGlobalHours();

        return view('menu.index', compact('menuItems', 'kitchenHours'));
    }

    public function syncFromIptv()
    {
        KitchenHour::seedDefaults();

        $iptvItems = DB::connection('iptv')->table('menu_items')->get();
        $synced = 0;
        $skipped = 0;

        foreach ($iptvItems as $item) {
            $inventoryItem = null;
            if (!empty($item->bnb_inventory_id)) {
                $inventoryItem = InventoryItem::find($item->bnb_inventory_id);
            }
            if (!$inventoryItem && !empty($item->name)) {
                $inventoryItem = InventoryItem::where('name', $item->name)->first();
            }

            $categories = $this->normalizeCategories($item->category);
            MenuItem::updateOrCreate(
                ['iptv_menu_item_id' => $item->id],
                [
                    'inventory_item_id' => $inventoryItem?->id,
                    'name' => $item->name,
                    'categories' => $categories,
                    'description' => $item->description,
                    'price' => $item->price,
                    'unit' => $item->unit,
                    'image' => $item->image,
                    'available' => (bool) $item->available,
                ]
            );

            $synced++;
        }

        return redirect()->route('menu.index')->with('success', "Synced {$synced} menu items from IPTV.");
    }

    public function create()
    {
        $categories = $this->categoryOptions();
        $inventoryItems = InventoryItem::orderBy('name')->get();
        return view('menu.create', compact('categories', 'inventoryItems'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'categories' => 'required|array|min:1',
            'categories.*' => 'string|in:breakfast,lunch,dinner,beverages,other',
            'inventory_item_id' => 'nullable|exists:inventory_items,id',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'unit' => 'nullable|string|max:255',
            'image' => 'nullable|string|max:255',
            'available' => 'boolean',
            'requires_chef' => 'boolean',
        ]);

        $data['available'] = $request->has('available');
        $data['requires_chef'] = $request->has('requires_chef');

        MenuItem::create($data);

        return redirect()->route('menu.index')->with('success', 'Menu item added successfully');
    }

    public function edit($id)
    {
        $menuItem = MenuItem::findOrFail($id);
        $categories = $this->categoryOptions();
        $inventoryItems = InventoryItem::orderBy('name')->get();
        return view('menu.edit', compact('menuItem', 'categories', 'inventoryItems'));
    }

    public function update(Request $request, $id)
    {
        $menuItem = MenuItem::findOrFail($id);

        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'categories' => 'sometimes|array|min:1',
            'categories.*' => 'string|in:breakfast,lunch,dinner,beverages,other',
            'inventory_item_id' => 'nullable|exists:inventory_items,id',
            'description' => 'nullable|string',
            'price' => 'sometimes|numeric|min:0',
            'unit' => 'nullable|string|max:255',
            'image' => 'nullable|string|max:255',
            'available' => 'boolean',
            'requires_chef' => 'boolean',
        ]);

        $data['available'] = $request->has('available');
        $data['requires_chef'] = $request->has('requires_chef');

        $menuItem->update($data);

        return redirect()->route('menu.index')->with('success', 'Menu item updated successfully');
    }

    public function destroy($id)
    {
        MenuItem::destroy($id);
        return redirect()->route('menu.index')->with('success', 'Menu item removed successfully');
    }

    public function updateKitchenHours(Request $request)
    {
        $data = $request->validate([
            'open_time' => 'required|date_format:H:i',
            'close_time' => 'required|date_format:H:i',
            'is_closed' => 'boolean',
        ]);

        KitchenHour::updateOrCreate(
            ['is_global' => true, 'tenant_id' => null],
            [
                'day_of_week' => null,
                'open_time' => $data['open_time'] . ':00',
                'close_time' => $data['close_time'] . ':00',
                'is_closed' => $request->has('is_closed'),
            ]
        );

        return redirect()->route('menu.index')->with('success', 'Kitchen hours updated successfully');
    }

    private function normalizeCategories($category)
    {
        $category = strtolower(trim($category));
        $category = match ($category) {
            'breakfast' => 'breakfast',
            'lunch' => 'lunch',
            'dinner' => 'dinner',
            'beverages', 'drinks' => 'beverages',
            default => 'other',
        };
        return [$category];
    }

    private function categoryOptions()
    {
        return [
            'breakfast' => 'Breakfast',
            'lunch' => 'Lunch',
            'dinner' => 'Dinner',
            'beverages' => 'Beverages',
            'other' => 'Other',
        ];
    }
}
