<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\GuestFolio;
use App\Services\FolioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FolioController extends Controller
{
    protected FolioService $folioService;

    public function __construct(FolioService $folioService)
    {
        $this->folioService = $folioService;
    }

    public function index(Request $request)
    {
        $perPage = in_array($request->input('per_page'), [10, 25, 50, 100]) ? (int) $request->input('per_page') : 25;
        $date = $request->input('date');
        $month = $request->input('month');

        $query = GuestFolio::with(['booking', 'booking.room', 'guest']);

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('booking', function ($q) use ($search) {
                $q->where('guest_name', 'like', "%{$search}%")
                  ->orWhere('booking_ref', 'like', "%{$search}%");
            })->orWhere('folio_number', 'like', "%{$search}%");
        }

        if ($date) {
            $query->whereDate('created_at', $date);
        }

        if ($month) {
            $query->whereYear('created_at', substr($month, 0, 4))
                ->whereMonth('created_at', substr($month, 5, 2));
        }

        $folios = $query->latest()->paginate($perPage)->withQueryString();

        $filters = compact('date', 'month', 'perPage');

        $summary = [
            'open_balance' => GuestFolio::where('status', 'open')->sum('balance_due'),
            'open_count' => GuestFolio::where('status', 'open')->count(),
            'closed_count' => GuestFolio::where('status', 'closed')->count(),
            'overdue_count' => GuestFolio::where('status', 'open')
                ->where('balance_due', '>', 0)
                ->whereHas('booking', function ($q) {
                    $q->whereDate('check_out_date', '<', today());
                })
                ->count(),
        ];

        return view('folios.index', compact('folios', 'summary', 'filters'));
    }

    public function show(GuestFolio $folio)
    {
        $folio->load(['booking', 'booking.room', 'guest', 'charges.poster', 'payments', 'invoices']);

        return view('folios.show', compact('folio'));
    }

    public function dashboard(GuestFolio $folio)
    {
        $folio->load(['booking', 'booking.room', 'guest']);

        $charges = $folio->charges()->with('poster')->orderByDesc('posting_date')->paginate(10, ['*'], 'charges');
        $payments = $folio->payments()->orderByDesc('payment_date')->paginate(10, ['*'], 'payments');
        $invoices = $folio->invoices()->orderByDesc('created_at')->paginate(10, ['*'], 'invoices');

        $stats = [
            'subtotal' => $folio->subtotal,
            'discounts' => $folio->discount_amount,
            'tax' => $folio->tax_amount,
            'service_charge' => $folio->service_charge,
            'total' => $folio->total_amount,
            'paid' => $folio->amount_paid,
            'balance' => $folio->balance_due,
        ];

        return view('folios.dashboard', compact('folio', 'charges', 'payments', 'invoices', 'stats'));
    }

    public function close(Request $request, GuestFolio $folio)
    {
        $this->authorize('close', $folio);

        $this->folioService->closeFolio($folio, auth()->user());

        return redirect()->route('folios.show', $folio)
            ->with('success', 'Folio closed successfully.');
    }

    public function void(Request $request, GuestFolio $folio)
    {
        $this->authorize('void', $folio);

        $data = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        DB::transaction(function () use ($folio, $data) {
            $folio->update([
                'status' => 'void',
                'closed_at' => now(),
                'closed_by' => auth()->id(),
                'notes' => ($folio->notes ? $folio->notes . "\n" : '') . 'Voided: ' . $data['reason'],
            ]);
        });

        return redirect()->route('folios.show', $folio)
            ->with('success', 'Folio voided successfully.');
    }
}
