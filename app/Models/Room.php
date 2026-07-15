<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $table = 'rooms';

    protected $fillable = [
        'tenant_id',
        'room_number',
        'room_type_id',
        'floor',
        'status',
        'is_smoking',
        'is_accessible',
        'blocked_until',
        'blocked_reason',
        'last_cleaned_at',
        'cleaned_by',
        'notes',
    ];

    protected $casts = [
        'is_smoking' => 'boolean',
        'is_accessible' => 'boolean',
        'blocked_until' => 'datetime',
        'last_cleaned_at' => 'datetime',
    ];

    public function devices()
    {
        return $this->hasMany(InfrastructureDevice::class, 'room_id');
    }

    public function tvs()
    {
        return $this->devices()->whereHas('category', function ($query) {
            $query->where('slug', 'tv');
        });
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'room_id');
    }

    public function currentBooking()
    {
        return $this->hasOne(Booking::class, 'room_id')
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->latest('check_in_date');
    }

    public function folios()
    {
        return $this->hasManyThrough(GuestFolio::class, Booking::class, 'room_id', 'booking_id');
    }

    public function isAvailable(): bool
    {
        return $this->status === 'available';
    }
}
