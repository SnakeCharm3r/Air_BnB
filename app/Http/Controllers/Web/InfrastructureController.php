<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InfrastructureController extends Controller
{
    private function tvCategory()
    {
        return DB::table('infrastructure_categories')->where('slug', 'tv')->first();
    }

    private function roomsList($excludeDeviceId = null)
    {
        $tvCategory = $this->tvCategory();
        $query = DB::table('rooms')
            ->select('id', 'room_number')
            ->orderBy('room_number');

        if ($tvCategory) {
            $occupiedRooms = DB::table('infrastructure_devices')
                ->where('category_id', $tvCategory->id)
                ->whereNotNull('room_id')
                ->when($excludeDeviceId, function ($q) use ($excludeDeviceId) {
                    $q->where('id', '!=', $excludeDeviceId);
                })
                ->pluck('room_id')
                ->toArray();

            $query->whereNotIn('id', $occupiedRooms);
        }

        return $query->get();
    }

    /* --------------------------------------------------
     * Devices
     * -------------------------------------------------- */

    public function index()
    {
        $categories = DB::table('infrastructure_categories')
            ->orderBy('name')
            ->get();

        $selectedCategory = request('category', null);

        $tvCategory = $this->tvCategory();

        $devicesQuery = DB::table('infrastructure_devices')
            ->leftJoin('infrastructure_categories', 'infrastructure_devices.category_id', '=', 'infrastructure_categories.id')
            ->leftJoin('rooms', 'infrastructure_devices.room_id', '=', 'rooms.id')
            ->select(
                'infrastructure_devices.*',
                'infrastructure_categories.name as category_name',
                'infrastructure_categories.slug as category_slug',
                'rooms.room_number'
            );

        if ($selectedCategory) {
            $devicesQuery->where('infrastructure_categories.slug', $selectedCategory);
        }

        $devices = $devicesQuery->orderBy('infrastructure_devices.name')->get();

        // Count by status
        $statusCounts = DB::table('infrastructure_devices')
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();

        // IPTV devices summary
        $iptvCount = 0;
        try {
            $iptvCount = DB::connection('iptv')->table('devices')->count();
        } catch (\Exception $e) {
            $iptvCount = null;
        }

        $validRoomIds = DB::table('rooms')->pluck('id')->toArray();

        // TVs that are not allocated to a valid room
        $unallocatedTvs = collect();
        if ($tvCategory) {
            $unallocatedTvs = DB::table('infrastructure_devices')
                ->where('category_id', $tvCategory->id)
                ->where(function ($query) use ($validRoomIds) {
                    $query->whereNull('room_id')
                          ->orWhereNotIn('room_id', $validRoomIds);
                })
                ->orderBy('name')
                ->get();
        }

        // Rooms that do not have a TV allocated
        $roomsWithoutTv = collect();
        if ($tvCategory) {
            $roomsWithTv = DB::table('infrastructure_devices')
                ->where('category_id', $tvCategory->id)
                ->whereIn('room_id', $validRoomIds)
                ->pluck('room_id')
                ->toArray();

            $roomsWithoutTv = DB::table('rooms')
                ->whereNotIn('id', $roomsWithTv)
                ->orderBy('room_number')
                ->get();
        }

        return view('infrastructure.index', compact(
            'categories',
            'devices',
            'statusCounts',
            'iptvCount',
            'selectedCategory',
            'tvCategory',
            'unallocatedTvs',
            'roomsWithoutTv'
        ));
    }

    public function show($id)
    {
        $device = DB::table('infrastructure_devices')
            ->leftJoin('infrastructure_categories', 'infrastructure_devices.category_id', '=', 'infrastructure_categories.id')
            ->leftJoin('rooms', 'infrastructure_devices.room_id', '=', 'rooms.id')
            ->select('infrastructure_devices.*', 'infrastructure_categories.name as category_name', 'infrastructure_categories.slug as category_slug', 'rooms.room_number')
            ->where('infrastructure_devices.id', $id)
            ->first();

        if (!$device) {
            abort(404);
        }

        // Get device logs if table exists
        $logs = [];
        try {
            $logs = DB::table('infrastructure_logs')
                ->where('device_id', $id)
                ->orderByDesc('created_at')
                ->limit(50)
                ->get();
        } catch (\Exception $e) {
            // Table might not exist
        }

        return view('infrastructure.show', compact('device', 'logs'));
    }

    public function create()
    {
        $categories = DB::table('infrastructure_categories')->orderBy('name')->get();
        $rooms = $this->roomsList();
        $tvCategory = $this->tvCategory();
        return view('infrastructure.create', compact('categories', 'rooms', 'tvCategory'));
    }

    public function store(Request $request)
    {
        $category = DB::table('infrastructure_categories')->where('id', $request->input('category_id'))->first();
        $tvCategory = $this->tvCategory();
        $isTv = $category && $tvCategory && $category->id === $tvCategory->id;

        $rules = [
            'name'          => 'required|string|max:255',
            'category_id'   => 'required|exists:infrastructure_categories,id',
            'device_type'   => 'nullable|string|max:255',
            'location'      => 'nullable|string|max:255',
            'room_id'       => $isTv ? 'required|integer|min:1|exists:rooms,id' : 'nullable|integer|min:1',
            'status'        => 'in:online,offline,maintenance,error',
            'ip_address'    => 'nullable|ip',
            'mac_address'   => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'config'        => 'nullable|string',
        ];

        $data = $request->validate($rules);

        // Validate TV room allocation and IP uniqueness
        if ($isTv && !empty($data['room_id'])) {
            $existingTv = DB::table('infrastructure_devices')
                ->where('category_id', $tvCategory->id)
                ->where('room_id', $data['room_id'])
                ->first();
            if ($existingTv) {
                return back()->withInput()->withErrors(['room_id' => 'This room is already allocated to TV device: ' . $existingTv->name]);
            }
        }

        if (!empty($data['ip_address'])) {
            $existingIp = DB::table('infrastructure_devices')
                ->where('ip_address', $data['ip_address'])
                ->first();
            if ($existingIp) {
                return back()->withInput()->withErrors(['ip_address' => 'This IP address is already assigned to: ' . $existingIp->name]);
            }
        }

        $data['device_type'] = $data['device_type'] ?? ($category ? $category->slug : 'other');
        $data['source'] = 'manual';
        $data['created_at'] = now();
        $data['updated_at'] = now();

        DB::table('infrastructure_devices')->insert($data);

        return redirect()->route('infrastructure.index')->with('success', 'Device added successfully');
    }

    public function edit($id)
    {
        $device = DB::table('infrastructure_devices')->find($id);
        if (!$device) {
            abort(404);
        }

        $categories = DB::table('infrastructure_categories')->orderBy('name')->get();
        $rooms = $this->roomsList($device->id);
        $tvCategory = $this->tvCategory();
        return view('infrastructure.edit', compact('device', 'categories', 'rooms', 'tvCategory'));
    }

    public function update(Request $request, $id)
    {
        $device = DB::table('infrastructure_devices')->find($id);
        if (!$device) {
            abort(404);
        }

        $category = DB::table('infrastructure_categories')->where('id', $request->input('category_id', $device->category_id))->first();
        $tvCategory = $this->tvCategory();
        $isTv = $category && $tvCategory && $category->id === $tvCategory->id;

        $rules = [
            'name'          => 'sometimes|string|max:255',
            'category_id'   => 'sometimes|exists:infrastructure_categories,id',
            'device_type'   => 'nullable|string|max:255',
            'location'      => 'nullable|string|max:255',
            'room_id'       => $isTv ? 'required|integer|min:1|exists:rooms,id' : 'nullable|integer|min:1',
            'status'        => 'sometimes|in:online,offline,maintenance,error',
            'ip_address'    => 'nullable|ip',
            'mac_address'   => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'config'        => 'nullable|string',
            'last_checked'  => 'nullable|date',
        ];

        $data = $request->validate($rules);

        // Validate TV room allocation and IP uniqueness, excluding the current device
        if ($isTv && !empty($data['room_id'])) {
            $existingTv = DB::table('infrastructure_devices')
                ->where('category_id', $tvCategory->id)
                ->where('room_id', $data['room_id'])
                ->where('id', '!=', $id)
                ->first();
            if ($existingTv) {
                return back()->withInput()->withErrors(['room_id' => 'This room is already allocated to TV device: ' . $existingTv->name]);
            }
        }

        if (!empty($data['ip_address'])) {
            $existingIp = DB::table('infrastructure_devices')
                ->where('ip_address', $data['ip_address'])
                ->where('id', '!=', $id)
                ->first();
            if ($existingIp) {
                return back()->withInput()->withErrors(['ip_address' => 'This IP address is already assigned to: ' . $existingIp->name]);
            }
        }

        if (isset($data['category_id']) && empty($data['device_type'])) {
            $data['device_type'] = $category ? $category->slug : 'other';
        }

        $data['updated_at'] = now();

        DB::table('infrastructure_devices')->where('id', $id)->update($data);

        return redirect()->route('infrastructure.index')->with('success', 'Device updated successfully');
    }

    public function destroy($id)
    {
        DB::table('infrastructure_devices')->where('id', $id)->delete();
        return redirect()->route('infrastructure.index')->with('success', 'Device deleted successfully');
    }

    public function toggleStatus($id)
    {
        $device = DB::table('infrastructure_devices')->find($id);
        if (!$device) {
            abort(404);
        }

        $newStatus = $device->status === 'online' ? 'offline' : 'online';

        DB::table('infrastructure_devices')->where('id', $id)->update([
            'status'       => $newStatus,
            'last_checked' => now(),
            'updated_at'   => now(),
        ]);

        return redirect()->route('infrastructure.index')->with('success', 'Device status updated');
    }

    /* --------------------------------------------------
     * IPTV Sync
     * -------------------------------------------------- */

    public function syncIptv()
    {
        try {
            $iptvDevices = DB::connection('iptv')->table('devices')->get();
        } catch (\Exception $e) {
            return redirect()->route('infrastructure.index')->with('error', 'Could not connect to IPTV database: ' . $e->getMessage());
        }

        $tvCategory = DB::table('infrastructure_categories')->where('slug', 'tv')->first();
        if (!$tvCategory) {
            return redirect()->route('infrastructure.index')->with('error', 'TV category not found. Please create it first.');
        }

        $synced = 0;
        foreach ($iptvDevices as $iptvDevice) {
            $existing = DB::table('infrastructure_devices')
                ->where('iptv_device_id', $iptvDevice->id)
                ->first();

            $data = [
                'name'          => $iptvDevice->device_name ?? ('IPTV Device ' . $iptvDevice->id),
                'category_id'   => $tvCategory->id,
                'device_type'   => 'tv',
                'location'      => $iptvDevice->room_id ? 'Room ' . $iptvDevice->room_id : null,
                'room_id'       => $iptvDevice->room_id,
                'status'        => $iptvDevice->status === 'active' ? 'online' : 'offline',
                'ip_address'    => $iptvDevice->ip_address,
                'mac_address'   => $iptvDevice->mac_address,
                'serial_number' => $iptvDevice->serial_number,
                'source'        => 'iptv',
                'iptv_last_seen' => $iptvDevice->last_seen,
                'updated_at'    => now(),
            ];

            if ($existing) {
                DB::table('infrastructure_devices')->where('id', $existing->id)->update($data);
            } else {
                $data['iptv_device_id'] = $iptvDevice->id;
                $data['created_at'] = now();
                DB::table('infrastructure_devices')->insert($data);
                $synced++;
            }
        }

        $total = $iptvDevices->count();
        return redirect()->route('infrastructure.index')->with('success', "IPTV sync completed. {$total} devices found, {$synced} new imported.");
    }

    /* --------------------------------------------------
     * Categories
     * -------------------------------------------------- */

    public function categoriesIndex()
    {
        $categories = DB::table('infrastructure_categories')->orderBy('name')->get();
        return view('infrastructure.categories.index', compact('categories'));
    }

    public function categoriesCreate()
    {
        return view('infrastructure.categories.create');
    }

    public function categoriesStore(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'icon'        => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active'   => 'nullable|boolean',
        ]);

        $data['slug'] = Str::slug($data['name']);
        $data['is_active'] = $request->boolean('is_active', true);
        $data['created_at'] = now();
        $data['updated_at'] = now();

        // Ensure unique slug
        $baseSlug = $data['slug'];
        $counter = 1;
        while (DB::table('infrastructure_categories')->where('slug', $data['slug'])->exists()) {
            $data['slug'] = $baseSlug . '-' . $counter++;
        }

        DB::table('infrastructure_categories')->insert($data);

        return redirect()->route('infrastructure.categories.index')->with('success', 'Category added successfully');
    }

    public function categoriesEdit($id)
    {
        $category = DB::table('infrastructure_categories')->find($id);
        if (!$category) {
            abort(404);
        }
        return view('infrastructure.categories.edit', compact('category'));
    }

    public function categoriesUpdate(Request $request, $id)
    {
        $category = DB::table('infrastructure_categories')->find($id);
        if (!$category) {
            abort(404);
        }

        $data = $request->validate([
            'name'        => 'sometimes|string|max:255',
            'icon'        => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active'   => 'nullable|boolean',
        ]);

        if (isset($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        $data['is_active'] = $request->boolean('is_active', $category->is_active);
        $data['updated_at'] = now();

        DB::table('infrastructure_categories')->where('id', $id)->update($data);

        return redirect()->route('infrastructure.categories.index')->with('success', 'Category updated successfully');
    }

    public function categoriesDestroy($id)
    {
        DB::table('infrastructure_categories')->where('id', $id)->delete();
        return redirect()->route('infrastructure.categories.index')->with('success', 'Category deleted successfully');
    }
}
