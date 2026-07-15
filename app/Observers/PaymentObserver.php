<?php

namespace App\Observers;

use App\Events\PaymentReceived;
use App\Models\Payment;
use App\Services\FolioService;

class PaymentObserver
{
    public function __construct(protected FolioService $folioService)
    {
    }

    /**
     * Recalculate the folio balance and fire the PaymentReceived event.
     */
    public function created(Payment $payment): void
    {
        if ($payment->folio && ! $payment->is_void) {
            $this->folioService->recalculate($payment->folio);
        }

        PaymentReceived::dispatch($payment);
    }

    /**
     * When a payment is voided, recalculate the folio.
     */
    public function updated(Payment $payment): void
    {
        if ($payment->isDirty('is_void') && $payment->folio) {
            $this->folioService->recalculate($payment->folio);
        }
    }
}
