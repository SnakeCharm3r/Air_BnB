<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\GuestFolio;
use App\Models\Invoice;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    protected InvoiceService $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    public function index(Request $request)
    {
        $perPage = in_array($request->input('per_page'), [10, 25, 50, 100]) ? (int) $request->input('per_page') : 25;
        $date = $request->input('date');
        $month = $request->input('month');

        $baseQuery = Invoice::with(['booking', 'booking.room', 'folio', 'folio.charges', 'folio.payments', 'issuer']);

        if ($date) {
            $baseQuery->whereDate('created_at', $date);
        }

        if ($month) {
            $baseQuery->whereYear('created_at', substr($month, 0, 4))
                ->whereMonth('created_at', substr($month, 5, 2));
        }

        if ($request->filled('status')) {
            $baseQuery->where('invoice_status', $request->input('status'));
        }

        if ($request->filled('folio_id')) {
            $baseQuery->where('folio_id', $request->input('folio_id'));
        }

        // Recalculate live totals from folio charges/payments on the full filtered set.
        // Issued/paid/voided invoices are immutable, so we only persist updates for draft invoices.
        $allInvoices = (clone $baseQuery)->get();
        foreach ($allInvoices as $invoice) {
            // Legacy invoices were created without a folio_id; link them if a folio exists and the invoice is still draft.
            if (! $invoice->folio_id && $invoice->booking_id && $invoice->invoice_status === 'draft') {
                $folio = GuestFolio::where('booking_id', $invoice->booking_id)->first();
                if ($folio) {
                    $invoice->folio_id = $folio->id;
                    $invoice->save();
                    $invoice->setRelation('folio', $folio);
                }
            }

            $folio = $invoice->folio;
            $chargesTotal = $folio ? $folio->charges->sum('total_amount') : 0;
            $paymentsTotal = $folio
                ? $folio->payments->where('payment_status', 'successful')->where('is_void', false)->sum('amount')
                : 0;
            $balance = max(0, $chargesTotal - $paymentsTotal);

            $status = 'draft';
            if ($balance <= 0 && $paymentsTotal > 0) {
                $status = 'paid';
            } elseif ($paymentsTotal > 0) {
                $status = 'partial';
            } elseif ($invoice->invoice_status === 'issued') {
                $status = 'issued';
            }

            $invoice->grand_total = $chargesTotal;
            $invoice->amount_paid = $paymentsTotal;
            $invoice->balance_due = $balance;

            // Only draft invoices can be updated in place. Immutable invoices must be voided and recreated.
            if ($invoice->invoice_status === 'draft') {
                $invoice->invoice_status = $status;
                if ($invoice->isDirty()) {
                    $invoice->save();
                }
            }
        }

        $summary = [
            'draft' => $allInvoices->where('invoice_status', 'draft')->count(),
            'issued' => $allInvoices->where('invoice_status', 'issued')->count(),
            'paid' => $allInvoices->where('invoice_status', 'paid')->count(),
            'outstanding' => $allInvoices->whereNotIn('invoice_status', ['paid', 'voided', 'cancelled'])->sum('balance_due'),
        ];

        $invoices = (clone $baseQuery)->latest()->paginate($perPage)->withQueryString();

        $filters = compact('date', 'month', 'perPage');

        return view('invoices.index', compact('invoices', 'summary', 'filters'));
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['booking', 'booking.room', 'folio', 'issuer']);

        return view('invoices.show', compact('invoice'));
    }

    public function print(Invoice $invoice)
    {
        $invoice->load(['booking', 'booking.room', 'folio', 'issuer']);

        return view('invoices.print', compact('invoice'));
    }

    public function generate(Request $request)
    {
        $data = $request->validate([
            'folio_id' => 'required|exists:guest_folios,id',
        ]);

        $folio = GuestFolio::with('booking')->findOrFail($data['folio_id']);
        $invoice = $this->invoiceService->generateFromFolio($folio, Auth::user());

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Invoice generated successfully.');
    }

    public function issue(Request $request, Invoice $invoice)
    {
        $this->invoiceService->issueInvoice($invoice, Auth::user());

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Invoice issued successfully.');
    }

    public function markPaid(Request $request, Invoice $invoice)
    {
        $this->invoiceService->markPaid($invoice);

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Invoice marked as paid.');
    }

    public function cancel(Request $request, Invoice $invoice)
    {
        $data = $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $invoice->update([
            'invoice_status' => 'cancelled',
            'status' => 'cancelled',
            'notes' => ($invoice->notes ? $invoice->notes . "\n" : '') . 'Cancelled: ' . ($data['reason'] ?? 'No reason provided'),
        ]);

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Invoice cancelled.');
    }

    public function void(Request $request, Invoice $invoice)
    {
        $data = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $this->invoiceService->voidInvoice($invoice, $data['reason']);

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Invoice voided.');
    }
}
