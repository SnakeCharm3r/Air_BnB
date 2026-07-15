<?php

namespace App\Services;

use App\Enums\CustomerType;
use App\Enums\InvoiceStatus;
use App\Models\GuestFolio;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Support\Str;

class InvoiceService
{
    /**
     * Generate a new invoice snapshot from a closed folio.
     */
    public function generateFromFolio(GuestFolio $folio, ?User $user = null): Invoice
    {
        $booking = $folio->booking;

        if (! $booking) {
            throw new \RuntimeException('Folio is not linked to a booking.');
        }

        // If an active invoice already exists for this folio, void it so the new one replaces it.
        $existingInvoice = Invoice::where('folio_id', $folio->id)
            ->whereNotIn('invoice_status', ['voided', 'cancelled'])
            ->first();

        $invoiceNumber = 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(4));

        if ($existingInvoice) {
            $this->voidInvoice($existingInvoice, 'Replaced by new invoice ' . $invoiceNumber);
        }

        $invoice = Invoice::create([
            'invoice_number' => $invoiceNumber,
            'booking_id' => $booking->id,
            'folio_id' => $folio->id,
            'tenant_id' => $folio->tenant_id,
            'guest_name' => $booking->guest_name,
            'guest_email' => $booking->guest_email,
            'guest_phone' => $booking->guest_phone,
            'room_number' => $booking->room?->room_number ?? 'N/A',
            'check_in_date' => $booking->check_in_date,
            'check_in_time' => $booking->check_in_time,
            'check_out_date' => $booking->check_out_date,
            'check_out_time' => $booking->check_out_time,
            'nights' => max(1, now()->parse($booking->check_in_date)->diffInDays(now()->parse($booking->check_out_date))),
            'room_rate' => $booking->total_amount / max(1, now()->parse($booking->check_in_date)->diffInDays(now()->parse($booking->check_out_date))),
            'subtotal' => $folio->subtotal,
            'tax_amount' => $folio->tax_amount,
            'service_charge' => $folio->service_charge,
            'total_amount' => $folio->total_amount,
            'grand_total' => $folio->total_amount,
            'amount_paid' => $folio->amount_paid,
            'balance_due' => $folio->balance_due,
            'payment_type' => $booking->payment_method,
            'payment_reference' => $booking->payment_reference,
            'status' => $folio->balance_due <= 0 ? 'paid' : 'partial',
            'invoice_status' => InvoiceStatus::Draft->value,
            'invoice_type' => $this->deriveInvoiceType($booking->reservation_type),
            'invoice_date' => now()->toDateString(),
            'issued_by' => $user?->id,
            'due_date' => now()->addDays($booking->credit_terms_days ?? 0)->toDateString(),
            'payment_terms' => $booking->credit_terms_days ? "Net {$booking->credit_terms_days} days" : 'Due on receipt',
            'customer_type' => $this->deriveCustomerType($booking),
            'company_name' => $booking->company_name,
            'tax_id' => $booking->tax_id,
        ]);

        return $invoice;
    }

    /**
     * Issue a draft invoice. Once issued it becomes immutable.
     */
    public function issueInvoice(Invoice $invoice, ?User $user = null): Invoice
    {
        $invoice->update([
            'invoice_status' => InvoiceStatus::Issued->value,
            'issued_by' => $user?->id ?? $invoice->issued_by,
            'invoice_date' => $invoice->invoice_date ?? now()->toDateString(),
        ]);

        return $invoice->fresh();
    }

    /**
     * Mark an invoice as paid once the folio balance is zero.
     */
    public function markPaid(Invoice $invoice): Invoice
    {
        $invoice->update([
            'invoice_status' => InvoiceStatus::Paid->value,
            'status' => 'paid',
        ]);

        return $invoice->fresh();
    }

    /**
     * Void a previously issued invoice.
     */
    public function voidInvoice(Invoice $invoice, ?string $reason = null): Invoice
    {
        $invoice->update([
            'invoice_status' => InvoiceStatus::Voided->value,
            'status' => 'cancelled',
            'notes' => $reason ? ($invoice->notes . "\nVoid reason: {$reason}") : $invoice->notes,
        ]);

        return $invoice->fresh();
    }

    protected function deriveInvoiceType(?string $reservationType): string
    {
        return match ($reservationType) {
            'corporate'  => 'corporate',
            'government' => 'government',
            'ngo'        => 'ngo',
            'vip'        => 'vip',
            default      => 'standard',
        };
    }

    protected function deriveCustomerType($booking): string
    {
        if ($booking->reservation_type === 'corporate' || $booking->company_name) {
            return CustomerType::Corporate->value;
        }

        if ($booking->reservation_type === 'government') {
            return CustomerType::Government->value;
        }

        if ($booking->reservation_type === 'ngo') {
            return CustomerType::NGO->value;
        }

        if ($booking->reservation_type === 'vip' || ($booking->guest && $booking->guest->is_vip)) {
            return CustomerType::VIP->value;
        }

        return CustomerType::WalkIn->value;
    }
}
