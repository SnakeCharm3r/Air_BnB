<?php

namespace App\Services;

use App\Enums\FolioStatus;
use App\Enums\PaymentStatus;
use App\Models\Booking;
use App\Models\GuestFolio;
use App\Models\User;
use Illuminate\Support\Str;

class FolioService
{
    /**
     * Open a folio for a booking. Does nothing if a folio already exists.
     */
    public function openFolio(Booking $booking): GuestFolio
    {
        $folio = GuestFolio::where('booking_id', $booking->id)->first();

        if ($folio) {
            return $folio;
        }

        $nights = max(1, now()->parse($booking->check_in_date)->diffInDays(now()->parse($booking->check_out_date)));
        $roomRate = $booking->total_amount / max(1, $nights);

        return GuestFolio::create([
            'booking_id' => $booking->id,
            'guest_id' => $booking->guest_id,
            'tenant_id' => $booking->tenant_id,
            'folio_number' => 'FOL-' . date('Ymd') . '-' . strtoupper(Str::random(4)),
            'status' => FolioStatus::Open->value,
            'room_charges' => $booking->total_amount,
            'subtotal' => $booking->total_amount,
            'total_amount' => $booking->total_amount,
            'amount_paid' => $booking->retainer_paid ?? 0,
            'balance_due' => ($booking->total_amount ?? 0) - ($booking->retainer_paid ?? 0),
            'payment_status' => $this->derivePaymentStatus($booking->total_amount ?? 0, $booking->retainer_paid ?? 0),
        ]);
    }

    /**
     * Recalculate folio totals from posted charges and successful payments.
     */
    public function recalculate(GuestFolio $folio): GuestFolio
    {
        $charges = $folio->charges()->where('status', 'posted')->get();

        $roomCharges = (float) $folio->room_charges;
        $otherCharges = $charges->sum('total_amount');
        $subtotal = $roomCharges + $otherCharges;
        $taxAmount = 0; // Tax was removed from invoices; folio mirrors that.
        $serviceCharge = 0;
        $discount = $charges->sum('discount_amount');
        $totalAmount = max(0, $subtotal + $taxAmount + $serviceCharge - $discount);

        $amountPaid = $folio->payments()
            ->where('is_void', false)
            ->where('payment_status', 'successful')
            ->sum('amount');

        $balanceDue = max(0, $totalAmount - $amountPaid);

        $folio->update([
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'service_charge' => $serviceCharge,
            'discount_amount' => $discount,
            'total_amount' => $totalAmount,
            'amount_paid' => $amountPaid,
            'balance_due' => $balanceDue,
            'payment_status' => $this->derivePaymentStatus($totalAmount, $amountPaid),
        ]);

        $this->syncBookingPaymentStatus($folio);

        return $folio->fresh();
    }

    /**
     * Close an open folio at checkout.
     */
    public function closeFolio(GuestFolio $folio, ?User $user = null): GuestFolio
    {
        if ($folio->isClosed()) {
            return $folio;
        }

        $this->recalculate($folio);

        $folio->update([
            'status' => FolioStatus::Closed->value,
            'closed_at' => now(),
            'closed_by' => $user?->id,
        ]);

        return $folio->fresh();
    }

    /**
     * Void a folio (e.g., cancelled booking). Folio becomes read-only.
     */
    public function voidFolio(GuestFolio $folio, ?User $user = null): GuestFolio
    {
        $folio->update([
            'status' => FolioStatus::Void->value,
            'closed_at' => now(),
            'closed_by' => $user?->id,
            'payment_status' => PaymentStatus::Refunded->value,
        ]);

        return $folio->fresh();
    }

    /**
     * Determine payment status from total and paid amounts.
     */
    public function derivePaymentStatus(float $total, float $paid): string
    {
        if ($total <= 0) {
            return $paid > 0 ? PaymentStatus::Overpaid->value : PaymentStatus::Paid->value;
        }

        if ($paid <= 0) {
            return PaymentStatus::Unpaid->value;
        }

        if ($paid >= $total) {
            return $paid > $total ? PaymentStatus::Overpaid->value : PaymentStatus::Paid->value;
        }

        if ($paid < $total * 0.5) {
            return PaymentStatus::DepositPaid->value;
        }

        return PaymentStatus::PartiallyPaid->value;
    }

    /**
     * Keep the booking's payment_status in sync with the folio.
     */
    protected function syncBookingPaymentStatus(GuestFolio $folio): void
    {
        if ($folio->booking_id && $folio->booking) {
            $folio->booking->update([
                'payment_status' => $folio->payment_status,
                'balance_due' => $folio->balance_due,
            ]);
        }
    }
}
