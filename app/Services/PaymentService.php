<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\GuestFolio;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class PaymentService
{
    public function __construct(protected FolioService $folioService)
    {
    }

    /**
     * Record a payment against a folio/booking.
     *
     * @param array{booking_id:int, folio_id:int, amount:float, payment_method:string, payment_date?:string, payment_status?:string, payment_gateway?:string, cashier_shift?:string, receipt_number?:string, currency?:string, exchange_rate?:float, bank_name?:string, mobile_provider?:string, approved_by?:int|null, reference?:string} $data
     */
    public function recordPayment(array $data): Payment
    {
        $booking = Booking::findOrFail($data['booking_id']);
        $folio = GuestFolio::findOrFail($data['folio_id']);

        $payment = Payment::create([
            'tenant_id' => $booking->tenant_id,
            'booking_id' => $booking->id,
            'folio_id' => $folio->id,
            'amount' => $data['amount'],
            'payment_method' => $data['payment_method'],
            'payment_type' => $data['payment_type'] ?? 'additional',
            'payment_date' => $data['payment_date'] ?? now()->toDateString(),
            'payment_status' => $data['payment_status'] ?? 'successful',
            'payment_gateway' => $data['payment_gateway'] ?? null,
            'cashier_shift' => $data['cashier_shift'] ?? null,
            'receipt_number' => $data['receipt_number'] ?? null,
            'reference' => $data['reference'] ?? null,
            'currency' => $data['currency'] ?? 'TZS',
            'exchange_rate' => $data['exchange_rate'] ?? 1,
            'bank_name' => $data['bank_name'] ?? null,
            'mobile_provider' => $data['mobile_provider'] ?? null,
            'approved_by' => $data['approved_by'] ?? null,
            'approved_at' => ($data['approved_by'] ?? null) ? now() : null,
            'is_void' => false,
            'notes' => $data['notes'] ?? null,
            'payment_for' => $data['payment_for'] ?? null,
            'created_by' => Auth::id(),
        ]);

        $this->folioService->recalculate($folio);

        return $payment;
    }

    /**
     * Refund a payment by creating a new negative payment entry.
     */
    public function refundPayment(Payment $originalPayment, float $amount, ?User $user = null, ?string $reason = null): Payment
    {
        if ($originalPayment->isRefund || $originalPayment->is_void) {
            throw new \RuntimeException('Cannot refund a refund or void payment.');
        }

        $refund = Payment::create([
            'tenant_id' => $originalPayment->tenant_id,
            'booking_id' => $originalPayment->booking_id,
            'folio_id' => $originalPayment->folio_id,
            'amount' => -abs($amount),
            'payment_method' => $originalPayment->payment_method,
            'payment_date' => now()->toDateString(),
            'payment_status' => 'refunded',
            'payment_gateway' => $originalPayment->payment_gateway,
            'currency' => $originalPayment->currency,
            'exchange_rate' => $originalPayment->exchange_rate,
            'is_refund' => true,
            'refunded_payment_id' => $originalPayment->id,
            'refund_reason' => $reason,
            'approved_by' => $user?->id ?? Auth::id(),
            'approved_at' => now(),
        ]);

        if ($originalPayment->folio) {
            $this->folioService->recalculate($originalPayment->folio);
        }

        return $refund;
    }

    /**
     * Void a payment. Payments are never deleted; they are marked void.
     */
    public function voidPayment(Payment $payment, ?string $reason = null, ?User $user = null): Payment
    {
        $payment->update([
            'is_void' => true,
            'void_reason' => $reason,
            'approved_by' => $user?->id ?? Auth::id(),
            'approved_at' => now(),
        ]);

        if ($payment->folio) {
            $this->folioService->recalculate($payment->folio);
        }

        return $payment->fresh();
    }
}
