<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Guest extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'guests';

    protected $fillable = [
        'tenant_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'id_number',
        'id_type',
        'nationality',
        'company',
        'tax_id',
        'is_vip',
        'vip_level',
        'preferences',
        'notes',
        'total_stays',
        'total_spent',
        'last_stay_date',
        'next_stay_date',
        'is_blacklisted',
        'blacklist_reason',
        'metadata',
    ];

    protected $casts = [
        'is_vip' => 'boolean',
        'is_blacklisted' => 'boolean',
        'total_spent' => 'decimal:2',
        'last_stay_date' => 'date',
        'next_stay_date' => 'date',
        'metadata' => 'array',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'guest_id');
    }

    public function folios()
    {
        return $this->hasMany(GuestFolio::class, 'guest_id');
    }

    public function payments()
    {
        return $this->hasManyThrough(Payment::class, Booking::class, 'guest_id', 'booking_id');
    }

    public function fullName(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }
}
