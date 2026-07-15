<?php

namespace App\Observers;

use App\Events\ChargePosted;
use App\Models\BookingCharge;
use App\Services\FolioService;

class BookingChargeObserver
{
    public function __construct(protected FolioService $folioService)
    {
    }

    /**
     * Recalculate the folio total and fire the ChargePosted event.
     */
    public function created(BookingCharge $charge): void
    {
        if ($charge->folio) {
            $this->folioService->recalculate($charge->folio);
        }

        ChargePosted::dispatch($charge);
    }

    /**
     * Ensure a charge is never physically deleted; use the service reversal instead.
     */
    public function deleting(BookingCharge $charge): void
    {
        throw new \RuntimeException(
            'Charges cannot be deleted. Use ChargePostingService::reverseCharge() to create a reversing entry.'
        );
    }
}
