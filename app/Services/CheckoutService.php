<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\GuestFolio;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Room;
use Illuminate\Support\Facades\DB;

class CheckoutService
{
    protected FolioService $folioService;
    protected InvoiceService $invoiceService;
    protected ReceiptService $receiptService;
    protected PaymentService $paymentService;

    public function __construct(
        FolioService $folioService,
        InvoiceService $invoiceService,
        ReceiptService $receiptService,
        PaymentService $paymentService
    ) {
        $this->folioService = $folioService;
        $this->invoiceService = $invoiceService;
        $this->receiptService = $receiptService;
        $this->paymentService = $paymentService;
    }

    /**
     * Complete the checkout workflow for a booking.
     */
    public function checkout(Booking $booking, ?array $paymentData = null): array
    {
        return DB::transaction(function () use ($booking, $paymentData) {
            $folio = $booking->folio ?? $this->folioService->openFolio($booking);

            $this->folioService->recalculate($folio);

            // Record any final payment provided during checkout.
            $payment = null;
            if ($paymentData && ($paymentData['amount'] ?? 0) > 0) {
                $payment = $this->paymentService->recordPayment([
                    'booking_id' => $booking->id,
                    'folio_id' => $folio->id,
                    'amount' => $paymentData['amount'],
                    'payment_method' => $paymentData['payment_method'] ?? 'cash',
                    'payment_date' => now()->toDateString(),
                    'payment_gateway' => 'manual',
                    'receipt_number' => 'RCP-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4)),
                    'payment_status' => 'successful',
                    'reference' => $paymentData['reference'] ?? null,
                    'notes' => $paymentData['notes'] ?? 'Final checkout payment',
                ]);
            }

            // Generate invoice from the folio.
            $invoice = $this->invoiceService->generateFromFolio($folio, auth()->user());
            $this->invoiceService->issueInvoice($invoice, auth()->user());

            if ($folio->balance_due <= 0) {
                $this->invoiceService->markPaid($invoice);
            }

            // Close the folio.
            $this->folioService->closeFolio($folio, auth()->user());

            // Update booking and room.
            $booking->update([
                'status' => 'checked_out',
                'actual_checkout' => now(),
                'balance_due' => 0,
            ]);

            Room::where('id', $booking->room_id)->update([
                'status' => 'available',
            ]);

            $receipt = $payment ? $this->receiptService->forPayment($payment) : null;

            return compact('folio', 'invoice', 'payment', 'receipt');
        });
    }

    public function calculateFinalCharges(GuestFolio $folio): array
    {
        $this->folioService->recalculate($folio);

        return [
            'subtotal' => $folio->subtotal,
            'discounts' => $folio->discount_amount,
            'tax' => $folio->tax_amount,
            'service_charge' => $folio->service_charge,
            'total' => $folio->total_amount,
            'paid' => $folio->amount_paid,
            'balance' => $folio->balance_due,
        ];
    }
}
