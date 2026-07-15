<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\BookingCharge;
use App\Models\GuestFolio;
use App\Services\ChargePostingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChargeController extends Controller
{
    protected ChargePostingService $chargeService;

    public function __construct(ChargePostingService $chargeService)
    {
        $this->chargeService = $chargeService;
    }

    public function index(Request $request)
    {
        $query = BookingCharge::with(['booking', 'folio', 'poster'])
            ->latest();

        if ($request->filled('folio_id')) {
            $query->where('folio_id', $request->input('folio_id'));
        }

        if ($request->filled('charge_type')) {
            $query->where('charge_type', $request->input('charge_type'));
        }

        $charges = $query->paginate(25);

        return view('charges.index', compact('charges'));
    }

    public function create(Request $request)
    {
        $folio = GuestFolio::with('booking')->findOrFail($request->input('folio_id'));

        return view('charges.create', compact('folio'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'folio_id' => 'required|exists:guest_folios,id',
            'description' => 'required|string|max:255',
            'charge_type' => 'required|string|in:room,restaurant,laundry,mini_bar,room_service,spa,transport,damage,conference,equipment_hire,extra_bed,early_check_in,late_check_out,miscellaneous',
            'quantity' => 'nullable|numeric|min:0.01',
            'unit_price' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'posting_date' => 'nullable|date',
        ]);

        $folio = GuestFolio::with('booking')->findOrFail($data['folio_id']);

        $charge = $this->chargeService->postCharge([
            'booking_id' => $folio->booking_id,
            'folio_id' => $folio->id,
            'description' => $data['description'],
            'charge_type' => $data['charge_type'],
            'quantity' => $data['quantity'] ?? 1,
            'unit_price' => $data['unit_price'],
            'discount_amount' => $data['discount_amount'] ?? 0,
            'posting_date' => $data['posting_date'] ?? now()->toDateString(),
            'posted_by' => auth()->id(),
        ]);

        return redirect()->route('folios.dashboard', $folio)
            ->with('success', 'Charge posted successfully.');
    }

    public function reverse(Request $request, BookingCharge $charge)
    {
        $data = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $this->chargeService->reverseCharge($charge, auth()->user(), $data['reason']);

        return redirect()->route('folios.dashboard', $charge->folio_id)
            ->with('success', 'Charge reversed successfully.');
    }
}
