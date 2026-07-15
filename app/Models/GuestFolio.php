<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuestFolio extends Model
{
    use HasFactory;

    protected $table = 'guest_folios';

    protected $fillable = [
        'booking_id',
        'guest_id',
        'tenant_id',
        'folio_number',
        'status',
        'room_charges',
        'food_charges',
        'drink_charges',
        'laundry_charges',
        'amenity_charges',
        'service_charges',
        'other_charges',
        'tax_amount',
        'discount_amount',
        'service_charge',
        'subtotal',
        'total_amount',
        'amount_paid',
        'balance_due',
        'payment_status',
        'closed_at',
        'closed_by',
        'notes',
    ];

    protected $casts = [
        'room_charges' => 'decimal:2',
        'food_charges' => 'decimal:2',
        'drink_charges' => 'decimal:2',
        'laundry_charges' => 'decimal:2',
        'amenity_charges' => 'decimal:2',
        'service_charges' => 'decimal:2',
        'other_charges' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'service_charge' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'balance_due' => 'decimal:2',
        'closed_at' => 'datetime',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    public function guest()
    {
        return $this->belongsTo(Guest::class, 'guest_id');
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function closer()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function charges()
    {
        return $this->hasMany(BookingCharge::class, 'folio_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'folio_id');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'folio_id');
    }

    public function isClosed(): bool
    {
        return in_array($this->status, ['closed', 'void'], true);
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }
}
