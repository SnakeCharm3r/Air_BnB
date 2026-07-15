<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $table = 'invoices';

    protected $fillable = [
        'invoice_number',
        'booking_id',
        'folio_id',
        'tenant_id',
        'guest_name',
        'guest_email',
        'guest_phone',
        'room_number',
        'check_in_date',
        'check_in_time',
        'check_out_date',
        'check_out_time',
        'nights',
        'room_rate',
        'subtotal',
        'tax_amount',
        'service_charge',
        'total_amount',
        'grand_total',
        'amount_paid',
        'balance_due',
        'payment_type',
        'payment_reference',
        'status',
        'invoice_status',
        'invoice_type',
        'invoice_date',
        'issued_by',
        'credit_terms_days',
        'due_date',
        'payment_terms',
        'customer_type',
        'company_name',
        'tax_id',
        'vat_number',
        'discount_rate',
        'service_charge_rate',
        'overdue_since',
        'reminder_sent_at',
        'final_notice_sent_at',
        'notes',
        'printed_at',
    ];

    protected $casts = [
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'invoice_date' => 'date',
        'due_date' => 'date',
        'room_rate' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'service_charge' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'balance_due' => 'decimal:2',
        'discount_rate' => 'decimal:4',
        'service_charge_rate' => 'decimal:4',
        'overdue_since' => 'datetime',
        'reminder_sent_at' => 'datetime',
        'final_notice_sent_at' => 'datetime',
        'printed_at' => 'datetime',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    public function folio()
    {
        return $this->belongsTo(GuestFolio::class, 'folio_id');
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function issuer()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function isIssued(): bool
    {
        return $this->invoice_status !== 'draft' && $this->invoice_status !== null;
    }

    public function isPaid(): bool
    {
        return $this->invoice_status === 'paid';
    }
}
