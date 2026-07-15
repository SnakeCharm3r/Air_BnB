<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\ReceiptService;
use Illuminate\Http\Request;

class ReceiptController extends Controller
{
    protected ReceiptService $receiptService;

    public function __construct(ReceiptService $receiptService)
    {
        $this->receiptService = $receiptService;
    }

    public function index(Request $request)
    {
        $perPage = in_array($request->input('per_page'), [10, 25, 50, 100]) ? (int) $request->input('per_page') : 25;
        $date = $request->input('date');
        $month = $request->input('month');

        $query = Payment::with(['booking', 'folio'])
            ->whereNotNull('receipt_number');

        if ($date) {
            $query->whereDate('payment_date', $date);
        }

        if ($month) {
            $query->whereYear('payment_date', substr($month, 0, 4))
                ->whereMonth('payment_date', substr($month, 5, 2));
        }

        $query->latest();

        $receipts = $query->paginate($perPage)->withQueryString();

        $filters = compact('date', 'month', 'perPage');

        return view('receipts.index', compact('receipts', 'filters'));
    }

    public function show(Payment $payment)
    {
        $receipt = $this->receiptService->forPayment($payment);

        return view('receipts.show', compact('receipt', 'payment'));
    }

    public function print(Payment $payment)
    {
        $receipt = $this->receiptService->forPayment($payment);

        return view('receipts.print', compact('receipt', 'payment'));
    }

    public function generate(Payment $payment)
    {
        $receipt = $this->receiptService->forPayment($payment);

        return redirect()->route('receipts.show', $payment)
            ->with('success', 'Receipt generated successfully.');
    }
}
