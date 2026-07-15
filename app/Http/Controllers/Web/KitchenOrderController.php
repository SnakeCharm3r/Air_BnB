<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\BookingCharge;
use App\Models\GuestFolio;
use App\Models\KitchenOrder;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KitchenOrderController extends Controller
{
    public function index()
    {
        $orders = KitchenOrder::with(['room', 'menuItem', 'booking'])
            ->orderByDesc('created_at')
            ->get();

        $activeItems = MenuItem::where('available', true)
            ->orderBy('name')
            ->get();

        $statuses = ['pending', 'preparing', 'ready', 'delivered', 'cancelled'];

        return view('kitchen-orders.index', compact('orders', 'activeItems', 'statuses'));
    }

    public function create()
    {
        $activeItems = MenuItem::where('available', true)
            ->orderBy('name')
            ->get();

        $bookings = DB::table('bookings')
            ->join('rooms', 'rooms.id', '=', 'bookings.room_id')
            ->leftJoin('guest_folios', function ($join) {
                $join->on('guest_folios.booking_id', '=', 'bookings.id')
                     ->where('guest_folios.status', 'open');
            })
            ->where('bookings.status', 'checked_in')
            ->select(
                'bookings.id',
                'bookings.guest_name',
                'bookings.room_id',
                'rooms.room_number',
                'guest_folios.id as folio_id'
            )
            ->orderBy('rooms.room_number')
            ->get();

        $menuItemPrices = $activeItems->pluck('price', 'id');

        return view('kitchen-orders.create', compact('activeItems', 'bookings', 'menuItemPrices'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'room_id'     => 'required|exists:rooms,id',
            'booking_id'  => 'nullable|exists:bookings,id',
            'guest_name'  => 'nullable|string',
            'menu_item_id' => 'required|exists:menu_items,id',
            'quantity'    => 'required|integer|min:1',
            'notes'       => 'nullable|string',
        ]);

        $menuItem = MenuItem::findOrFail($data['menu_item_id']);

        if (!$menuItem->available) {
            return back()->with('error', 'Selected menu item is not currently active.');
        }

        $data['unit_price']  = $menuItem->price;
        $data['total_price'] = $menuItem->price * $data['quantity'];
        $data['created_by']  = auth()->id();
        $data['status']      = 'pending';

        $order = KitchenOrder::create($data);

        // Post charge to guest folio if a booking is linked
        if (!empty($data['booking_id'])) {
            $folio = GuestFolio::where('booking_id', $data['booking_id'])
                ->where('status', 'open')
                ->first();

            if ($folio) {
                $charge = BookingCharge::create([
                    'booking_id'     => $data['booking_id'],
                    'folio_id'       => $folio->id,
                    'description'    => $menuItem->name . ' x' . $data['quantity'],
                    'charge_type'    => 'food',
                    'category'       => 'item',
                    'quantity'       => $data['quantity'],
                    'unit_price'     => $menuItem->price,
                    'amount'         => $order->total_price,
                    'total_amount'   => $order->total_price,
                    'posting_date'   => now()->toDateString(),
                    'status'         => 'posted',
                    'posted_by'      => auth()->id(),
                    'created_by'     => auth()->id(),
                    'reference_type' => 'kitchen_order',
                    'reference_id'   => $order->id,
                ]);

                // Update folio food_charges and running totals
                $folio->increment('food_charges', $order->total_price);
                $folio->increment('subtotal',     $order->total_price);
                $folio->increment('total_amount', $order->total_price);
                $folio->increment('balance_due',  $order->total_price);

                // Link the charge back to the order
                $order->update(['folio_charge_id' => $charge->id]);
            }
        }

        // Deduct inventory if linked
        $this->deductInventory($order, $menuItem);

        $message = !empty($data['booking_id'])
            ? 'Kitchen order placed and charged to room folio.'
            : 'Kitchen order placed successfully.';

        return redirect()->route('kitchen-orders.index')->with('success', $message);
    }

    public function show($id)
    {
        $order = KitchenOrder::with(['room', 'menuItem', 'booking'])->findOrFail($id);
        return view('kitchen-orders.show', compact('order'));
    }

    public function updateStatus(Request $request, $id)
    {
        $order = KitchenOrder::findOrFail($id);

        $data = $request->validate([
            'status' => 'required|in:pending,preparing,ready,delivered,cancelled',
        ]);

        $now = now();

        if ($data['status'] === 'preparing' && $order->status === 'pending') {
            $order->prepared_at = $now;
        }

        if ($data['status'] === 'delivered' && in_array($order->status, ['pending', 'preparing', 'ready'])) {
            $order->delivered_at = $now;
        }

        $order->status = $data['status'];
        $order->save();

        return redirect()->route('kitchen-orders.index')->with('success', 'Order status updated to ' . ucfirst($data['status']));
    }

    public function destroy($id)
    {
        $order = KitchenOrder::findOrFail($id);
        $order->delete();

        return redirect()->route('kitchen-orders.index')->with('success', 'Order removed.');
    }

    private function deductInventory(KitchenOrder $order, MenuItem $menuItem)
    {
        if (!$menuItem->inventory_item_id) {
            return;
        }

        $inventoryItem = $menuItem->inventoryItem;
        if ($inventoryItem && $inventoryItem->quantity >= $order->quantity) {
            $inventoryItem->quantity -= $order->quantity;
            $inventoryItem->save();

            DB::table('inventory_usage')->insert([
                'item_id' => $inventoryItem->id,
                'type' => 'sale',
                'quantity' => $order->quantity,
                'notes' => 'Kitchen order #' . $order->id . ' - ' . $menuItem->name,
                'created_by' => auth()->id(),
                'date' => now(),
                'created_at' => now(),
            ]);
        }
    }
}
