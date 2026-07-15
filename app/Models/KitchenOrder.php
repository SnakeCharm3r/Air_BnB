<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KitchenOrder extends Model
{
    protected $fillable = [
        'room_id',
        'booking_id',
        'folio_charge_id',
        'guest_name',
        'menu_item_id',
        'quantity',
        'unit_price',
        'total_price',
        'notes',
        'status',
        'created_by',
        'prepared_at',
        'delivered_at',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'prepared_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function room()
    {
        return $this->belongsTo(\App\Models\Room::class);
    }

    public function booking()
    {
        return $this->belongsTo(\App\Models\Booking::class);
    }

    public function menuItem()
    {
        return $this->belongsTo(\App\Models\MenuItem::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['delivered', 'cancelled']);
    }
}
