<?php

namespace App\Observers;

use App\Enums\FolioStatus;
use App\Events\FolioClosed;
use App\Models\GuestFolio;

class GuestFolioObserver
{
    /**
     * Prevent editing closed or void folios.
     */
    public function updating(GuestFolio $folio): void
    {
        if ($folio->isDirty() && $folio->isClosed()) {
            throw new \RuntimeException('Closed or void folios are read-only.');
        }
    }

    /**
     * Fire the FolioClosed event when a folio transitions to closed/void.
     */
    public function updated(GuestFolio $folio): void
    {
        if ($folio->isDirty('status') && $folio->isClosed()) {
            FolioClosed::dispatch($folio);
        }
    }
}
