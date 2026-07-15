<?php

namespace App\Observers;

use App\Enums\InvoiceStatus;
use App\Events\InvoiceIssued;
use App\Models\Invoice;

class InvoiceObserver
{
    /**
     * Prevent changes to issued, paid, or voided invoices.
     */
    public function updating(Invoice $invoice): void
    {
        $original = (string) $invoice->getOriginal('invoice_status');
        $immutableStatuses = [
            InvoiceStatus::Issued->value,
            InvoiceStatus::Paid->value,
            InvoiceStatus::Voided->value,
        ];

        if (
            $invoice->isDirty()
            && $invoice->exists
            && in_array($original, $immutableStatuses, true)
        ) {
            throw new \RuntimeException('Issued invoices are immutable. Void the invoice and create a new one.');
        }
    }

    /**
     * Fire the InvoiceIssued event when an invoice transitions from draft.
     */
    public function updated(Invoice $invoice): void
    {
        if (
            $invoice->isDirty('invoice_status')
            && $invoice->invoice_status === InvoiceStatus::Issued->value
        ) {
            InvoiceIssued::dispatch($invoice);
        }
    }
}
