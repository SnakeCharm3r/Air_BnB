<?php

namespace App\Services;

use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

class ReceiptService
{
    /**
     * Generate a professional receipt view model from a payment.
     * No separate receipts table is used; the payment table already stores receipt_number.
     */
    public function generateForPayment(Payment $payment): object
    {
        $payment->load(['folio.charges', 'folio.payments', 'booking.room', 'booking.invoices']);

        $folio = $payment->folio;
        $booking = $payment->booking;

        $charges = $folio?->charges ?? collect();
        $payments = $folio?->payments ?? collect();

        $invoice = $folio?->invoices()->where('invoice_status', 'issued')->latest()->first();

        // Monetary calculations
        $totalCharges = $folio?->total_amount ?? $charges->sum('total_amount');
        $totalPaid = $folio?->amount_paid ?? $payments->where('payment_status', 'successful')->where('is_void', false)->sum('amount');
        $currentPayment = $payment->isSuccessful() ? (float) $payment->amount : 0;
        $previousPayments = max(0, $totalPaid - $currentPayment);
        $outstandingBefore = max(0, $totalCharges - $previousPayments);
        $remainingBalance = max(0, $totalCharges - $totalPaid);
        $changeReturned = $currentPayment > $outstandingBefore ? $currentPayment - $outstandingBefore : 0;
        $guestCredit = $changeReturned;

        // Receipt status badge
        if ($payment->is_refund) {
            $status = 'refunded';
        } elseif ($payment->is_void || $payment->payment_status === 'cancelled') {
            $status = 'cancelled';
        } elseif ($remainingBalance <= 0 && $totalCharges > 0) {
            $status = 'fully_paid';
        } elseif ($totalPaid > 0) {
            $status = 'partially_paid';
        } else {
            $status = 'pending';
        }

        $statusConfig = [
            'fully_paid' => ['label' => 'FULLY PAID', 'class' => 'bg-emerald-100 text-emerald-700'],
            'partially_paid' => ['label' => 'PARTIALLY PAID', 'class' => 'bg-amber-100 text-amber-700'],
            'refunded' => ['label' => 'REFUNDED', 'class' => 'bg-rose-100 text-rose-700'],
            'cancelled' => ['label' => 'CANCELLED', 'class' => 'bg-slate-100 text-slate-600'],
            'pending' => ['label' => 'PENDING', 'class' => 'bg-blue-100 text-blue-700'],
        ];

        // Itemized charges
        $chargeItems = $charges->map(function ($charge) {
            return (object) [
                'description' => $charge->description,
                'quantity' => $charge->quantity ?? 1,
                'unit_rate' => $charge->unit_price ?? $charge->total_amount,
                'amount' => $charge->total_amount,
                'status' => $charge->status,
                'posting_date' => $charge->posting_date ?? $charge->created_at,
            ];
        });

        // Payment history (all successful payments on the folio except the current one)
        $paymentHistory = $payments
            ->where('id', '!=', $payment->id)
            ->where('payment_status', 'successful')
            ->where('is_void', false)
            ->map(function ($p) {
                return (object) [
                    'receipt_number' => $p->receipt_number,
                    'payment_date' => $p->payment_date,
                    'payment_method' => $p->payment_method,
                    'reference' => $p->reference,
                    'amount' => $p->amount,
                ];
            });

        // Financial summary categories (use stored folio breakdown, no new tax calculations)
        $accommodationCharges = (float) ($folio?->room_charges ?? 0);
        $otherCharges = max(0, (float) ($folio?->subtotal ?? $totalCharges) - $accommodationCharges);
        $discounts = (float) ($folio?->discount_amount ?? 0);
        $serviceCharge = (float) ($folio?->service_charge ?? 0);
        $grandTotal = $accommodationCharges + $otherCharges - $discounts + $serviceCharge;

        // Cashier / print metadata
        $cashier = Auth::user()?->name ?? 'System';
        $printedAt = now();

        // QR verification URL
        $verificationUrl = URL::route('receipts.show', $payment);
        $qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' . urlencode($verificationUrl);

        $lengthOfStay = 0;
        if ($booking?->check_in_date && $booking?->check_out_date) {
            $lengthOfStay = max(1, now()->parse($booking->check_in_date)->diffInDays(now()->parse($booking->check_out_date)));
        }

        $receipt = (object) [
            'payment_id' => $payment->id,
            'booking_id' => $booking?->id,
            'folio_id' => $folio?->id,
            'receipt_number' => $payment->receipt_number ?? 'RCP-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4)),
            'invoice_number' => $invoice?->invoice_number,
            'folio_number' => $folio?->folio_number,
            'booking_number' => $booking?->booking_ref,
            'guest_name' => $booking?->guest_name ?? 'Guest',
            'room_number' => $booking?->room?->room_number,
            'check_in_date' => $booking?->check_in_date,
            'check_out_date' => $booking?->check_out_date,
            'length_of_stay' => $lengthOfStay,
            'receipt_date' => $payment->payment_date ?? now()->toDateString(),
            'payment_date' => $payment->payment_date,
            'payment_method' => $payment->payment_method,
            'reference' => $payment->reference,
            'payment_for' => $payment->payment_for ?: 'Accommodation',
            'receipt_type' => $payment->payment_for ? $payment->payment_for . ' Payment' : 'Accommodation Payment',
            'cashier' => $cashier,
            'notes' => $payment->notes,
            'status' => $status,
            'status_label' => $statusConfig[$status]['label'],
            'status_class' => $statusConfig[$status]['class'],
            'amount_received' => $currentPayment,
            'total_charges' => $totalCharges,
            'total_paid' => $totalPaid,
            'previous_payments' => $previousPayments,
            'outstanding_before' => $outstandingBefore,
            'remaining_balance' => $remainingBalance,
            'change_returned' => $changeReturned,
            'guest_credit' => $guestCredit,
            'charges' => $chargeItems,
            'payment_history' => $paymentHistory,
            'accommodation_charges' => $accommodationCharges,
            'other_charges' => $otherCharges,
            'discounts' => $discounts,
            'service_charge' => $serviceCharge,
            'grand_total' => $grandTotal,
            'payments_made' => $previousPayments,
            'current_payment' => $currentPayment,
            'outstanding_balance' => $remainingBalance,
            'printed_by' => $cashier,
            'printed_at' => $printedAt,
            'is_reprint' => ! empty($payment->receipt_number),
            'verification_url' => $verificationUrl,
            'qr_code_url' => $qrCodeUrl,
        ];

        if (empty($payment->receipt_number)) {
            $payment->update(['receipt_number' => $receipt->receipt_number]);
        }

        return $receipt;
    }

    public function forPayment(Payment $payment): object
    {
        return $this->generateForPayment($payment);
    }

    public function email(Payment $payment, string $email): bool
    {
        // Placeholder for email dispatch.
        return true;
    }
}
