<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\GuestFolio;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function index(Request $request)
    {
        $perPage = in_array($request->input('per_page'), [10, 25, 50, 100]) ? (int) $request->input('per_page') : 25;
        $date = $request->input('date');
        $month = $request->input('month');

        $query = Payment::with(['booking', 'folio', 'approver']);

        if ($request->filled('folio_id')) {
            $query->where('folio_id', $request->input('folio_id'));
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->input('payment_method'));
        }

        if ($date) {
            $query->whereDate('payment_date', $date);
        }

        if ($month) {
            $query->whereYear('payment_date', substr($month, 0, 4))
                ->whereMonth('payment_date', substr($month, 5, 2));
        }

        $payments = $query->latest()->paginate($perPage)->withQueryString();

        $filters = compact('date', 'month', 'perPage');

        return view('payments.index', compact('payments', 'filters'));
    }

    public function create(Request $request)
    {
        $folio = GuestFolio::with('booking')->findOrFail($request->input('folio_id'));

        return view('payments.create', compact('folio'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'folio_id' => 'required|exists:guest_folios,id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,card,bank_transfer,mobile_money,credit_account',
            'payment_date' => 'nullable|date',
            'reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $folio = GuestFolio::with('booking')->findOrFail($data['folio_id']);

        $payment = $this->paymentService->recordPayment([
            'booking_id' => $folio->booking_id,
            'folio_id' => $folio->id,
            'amount' => $data['amount'],
            'payment_method' => $data['payment_method'],
            'payment_date' => $data['payment_date'] ?? now()->toDateString(),
            'payment_gateway' => 'manual',
            'receipt_number' => 'RCP-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4)),
            'payment_status' => 'successful',
            'reference' => $data['reference'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        return redirect()->route('folios.dashboard', $folio)
            ->with('success', 'Payment recorded successfully.');
    }

    public function show(Payment $payment)
    {
        $payment->load(['booking', 'folio', 'approver', 'refundedPayment']);

        return view('payments.show', compact('payment'));
    }

    public function refund(Request $request, Payment $payment)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:0|max:' . $payment->amount,
            'reason' => 'required|string|max:500',
        ]);

        $this->paymentService->refundPayment($payment, $data['amount'], auth()->user(), $data['reason']);

        return redirect()->route('payments.show', $payment)
            ->with('success', 'Refund processed successfully.');
    }

    public function void(Request $request, Payment $payment)
    {
        $data = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $this->paymentService->voidPayment($payment, $data['reason'], auth()->user());

        return redirect()->route('payments.show', $payment)
            ->with('success', 'Payment voided successfully.');
    }
}
