<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\GuestFolio;
use App\Services\CheckoutService;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    protected CheckoutService $checkoutService;

    public function __construct(CheckoutService $checkoutService)
    {
        $this->checkoutService = $checkoutService;
    }

    public function show(Booking $booking)
    {
        $folio = $booking->folio ?? app(\App\Services\FolioService::class)->openFolio($booking);
        $summary = $this->checkoutService->calculateFinalCharges($folio);

        return view('checkout.show', compact('booking', 'folio', 'summary'));
    }

    public function store(Request $request, Booking $booking)
    {
        $data = $request->validate([
            'amount' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|in:cash,card,bank_transfer,mobile_money,credit_account',
            'reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $paymentData = null;
        if (($data['amount'] ?? 0) > 0) {
            $paymentData = [
                'amount' => $data['amount'],
                'payment_method' => $data['payment_method'] ?? 'cash',
                'reference' => $data['reference'] ?? null,
                'notes' => $data['notes'] ?? null,
            ];
        }

        $result = $this->checkoutService->checkout($booking, $paymentData);

        return redirect()->route('invoices.show', $result['invoice'])
            ->with('success', 'Checkout completed successfully.');
    }
}
