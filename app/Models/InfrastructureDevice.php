<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InfrastructureDevice extends Model
{
    use HasFactory;

    protected $table = 'infrastructure_devices';

    protected $fillable = [
        'name',
        'category_id',
        'device_type',
        'location',
        'room_id',
        'status',
        'config',
        'last_checked',
        'ip_address',
        'mac_address',
        'serial_number',
        'source',
        'iptv_device_id',
        'iptv_last_seen',
    ];

    protected $casts = [
        'last_checked' => 'datetime',
        'iptv_last_seen' => 'datetime',
    ];

    public function category()
    {
        return $this->belongsTo(InfrastructureCategory::class, 'category_id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function getCategoryNameAttribute()
    {
        return $this->category ? $this->category->name : 'Uncategorized';
    }

    public function getCategorySlugAttribute()
    {
        return $this->category ? $this->category->slug : null;
    }

    public function getRoomNumberAttribute()
    {
        return $this->room ? $this->room->room_number : null;
    }
}
