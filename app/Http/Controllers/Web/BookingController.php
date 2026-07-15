<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    private function getOverdueBookings()
    {
        $today = now()->format('Y-m-d');
        return DB::table('bookings')
            ->join('rooms', 'bookings.room_id', '=', 'rooms.id')
            ->leftJoin('room_types', 'rooms.room_type_id', '=', 'room_types.id')
            ->select('bookings.*', 'rooms.room_number', 'room_types.base_price as room_rate')
            ->where('bookings.status', 'checked_in')
            ->where('bookings.check_out_date', '<', $today)
            ->orderBy('bookings.check_out_date')
            ->get();
    }

    public function index()
    {
        $bookings = DB::table('bookings')
            ->join('rooms', 'bookings.room_id', '=', 'rooms.id')
            ->select('bookings.*', 'rooms.room_number')
            ->orderByDesc('bookings.created_at')
            ->get();

        $overdueBookings = $this->getOverdueBookings();

        // Statistics
        $today = now()->format('Y-m-d');
        $stats = [
            'total' => DB::table('bookings')->count(),
            'checkins' => DB::table('bookings')->where('check_in_date', $today)->count(),
            'checkouts' => DB::table('bookings')->where('check_out_date', $today)->count(),
            'checked_in' => DB::table('bookings')->where('status', 'checked_in')->count(),
            'pending' => DB::table('bookings')->where('status', 'pending')->count(),
            'confirmed' => DB::table('bookings')->where('status', 'confirmed')->count(),
        ];

        // Calculate occupancy
        $totalRooms = DB::table('rooms')->count();
        $stats['occupancy'] = $totalRooms > 0 ? round(($stats['checked_in'] / $totalRooms) * 100, 1) : 0;

        // Calculate revenue (total from all bookings)
        $stats['revenue'] = DB::table('bookings')->sum('total_amount');

        // Format bookings for FullCalendar - create individual daily events
        $calendarBookings = collect();
        foreach ($bookings as $booking) {
            $checkIn = new \DateTime($booking->check_in_date);
            $checkOut = new \DateTime($booking->check_out_date);
            $interval = new \DateInterval('P1D');
            $period = new \DatePeriod($checkIn, $interval, $checkOut);

            foreach ($period as $day) {
                $calendarBookings->push([
                    'id' => $booking->id,
                    'title' => $booking->guest_name . ' - Room ' . $booking->room_number,
                    'start' => $day->format('Y-m-d'),
                    'end' => $day->format('Y-m-d'),
                    'allDay' => true,
                    'backgroundColor' => $this->getEventColor($booking->status),
                    'borderColor' => $this->getEventColor($booking->status),
                ]);
            }
        }

        // Add today indicator
        $calendarBookings->push([
            'id' => 'today',
            'title' => 'Today',
            'start' => $today,
            'end' => $today,
            'allDay' => true,
            'backgroundColor' => '#6366f1',
            'borderColor' => '#6366f1',
            'display' => 'background',
        ]);

        // Get upcoming bookings (check-in date is today or future)
        $upcomingBookings = DB::table('bookings')
            ->join('rooms', 'bookings.room_id', '=', 'rooms.id')
            ->select('bookings.*', 'rooms.room_number')
            ->where('bookings.check_in_date', '>=', $today)
            ->where('bookings.status', '!=', 'cancelled')
            ->orderBy('bookings.check_in_date')
            ->limit(5)
            ->get();

        return view('bookings.index', compact('bookings', 'stats', 'calendarBookings', 'upcomingBookings', 'overdueBookings'));
    }

    private function getEventColor($status)
    {
        $colors = [
            'pending' => '#f59e0b',
            'confirmed' => '#3b82f6',
            'checked_in' => '#10b981',
            'checked_out' => '#6b7280',
            'cancelled' => '#ef4444',
        ];
        return $colors[$status] ?? '#6b7280';
    }

    public function create()
    {
        $rooms = DB::table('rooms')
            ->leftJoin('room_types', 'rooms.room_type_id', '=', 'room_types.id')
            ->where('rooms.status', 'available')
            ->select('rooms.*', 'room_types.name as room_type_name', 'room_types.base_price as price')
            ->get();
        return view('bookings.create', compact('rooms'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            // Guest information
            'guest_name' => 'required|string',
            'guest_email' => 'nullable|email',
            'guest_phone' => 'nullable|string',
            'guest_id' => 'nullable|exists:guests,id',
            
            // Room(s) - support single or multiple rooms
            'room_ids' => 'required|array|min:1',
            'room_ids.*' => 'exists:rooms,id',
            
            // Dates
            'check_in_date' => 'required|date',
            'check_in_time' => 'nullable|date_format:H:i',
            'check_out_date' => 'required|date|after:check_in_date',
            'check_out_time' => 'nullable|date_format:H:i',
            
            // Occupancy
            'adults' => 'required|integer|min:1',
            'children' => 'nullable|integer|min:0',
            
            // Reservation details
            'reservation_type' => 'required|in:walk_in,advance,group,corporate,vip',
            'expiry_date' => 'nullable|date|after:today',
            
            // Financials
            'total_amount' => 'nullable|numeric|min:0',
            'retainer_paid' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|in:cash,credit_card,bank_transfer,crdb,selcom,dpo,gepg,mobile_money,control_number',
            'payment_reference' => 'nullable|string',
            
            // Corporate fields
            'company_name' => 'nullable|string',
            'tax_id' => 'nullable|string',
            'credit_terms_days' => 'nullable|integer|in:7,14,30,60',
            
            // Other
            'special_requests' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        // Get current tenant
        $tenantId = $this->getCurrentTenantId();
        
        // Handle guest - create or link existing
        $guestId = $this->handleGuest($data, $tenantId);
        
        // Calculate total amount if not provided (sum of room rates)
        if (empty($data['total_amount'])) {
            $data['total_amount'] = $this->calculateTotalAmount($data['room_ids'], $data['check_in_date'], $data['check_out_date']);
        }

        // Create bookings for each room
        $bookingIds = [];
        $primaryBookingId = null;
        
        foreach ($data['room_ids'] as $index => $roomId) {
            $bookingData = [
                'booking_ref' => 'BK-' . strtoupper(Str::random(6)),
                'tenant_id' => $tenantId,
                'guest_id' => $guestId,
                'room_id' => $roomId,
                'guest_name' => $data['guest_name'],
                'guest_email' => $data['guest_email'] ?? null,
                'guest_phone' => $data['guest_phone'] ?? null,
                'check_in_date' => $data['check_in_date'],
                'check_in_time' => $data['check_in_time'] ?? '14:00',
                'check_out_date' => $data['check_out_date'],
                'check_out_time' => $data['check_out_time'] ?? '11:00',
                'adults' => $data['adults'],
                'children' => $data['children'] ?? 0,
                'reservation_type' => $data['reservation_type'],
                'expiry_date' => $data['expiry_date'] ?? null,
                'total_amount' => $data['total_amount'] / count($data['room_ids']), // Split amount evenly
                'retainer_paid' => $index === 0 ? ($data['retainer_paid'] ?? 0) : 0, // Payment on primary booking
                'payment_method' => $data['payment_method'] ?? null,
                'payment_reference' => $data['payment_reference'] ?? null,
                'company_name' => $data['company_name'] ?? null,
                'tax_id' => $data['tax_id'] ?? null,
                'credit_terms_days' => $data['credit_terms_days'] ?? null,
                'special_requests' => $data['special_requests'] ?? null,
                'notes' => $data['notes'] ?? null,
                'created_by' => auth()->id(),
            ];

            // Determine status based on payment and reservation type
            $hasPayment = ($data['retainer_paid'] ?? 0) > 0 || !empty($data['payment_reference']);
            $isAdvance = $data['reservation_type'] === 'advance';
            
            if ($hasPayment) {
                $bookingData['status'] = 'confirmed';
            } elseif ($isAdvance) {
                $bookingData['status'] = 'reserved';
            } else {
                $bookingData['status'] = 'pending';
            }
            
            $bookingData['balance_due'] = $bookingData['total_amount'] - $bookingData['retainer_paid'];

            $bookingId = DB::table('bookings')->insertGetId($bookingData);
            $bookingIds[] = $bookingId;
            
            if ($index === 0) {
                $primaryBookingId = $bookingId;
            }

            // Update room status
            $roomStatus = $bookingData['status'] === 'confirmed' ? 'booked' : 
                         ($bookingData['status'] === 'reserved' ? 'reserved' : 'available');
            DB::table('rooms')->where('id', $roomId)->update(['status' => $roomStatus]);

            // Create guest folio
            $this->createGuestFolio($bookingId, $bookingData, $tenantId);

            // Log audit
            $this->logAudit('created', 'booking', $bookingId, $bookingData);
        }

        // Create invoice only if confirmed (on primary booking)
        if ($hasPayment && $primaryBookingId) {
            $this->createInvoice($primaryBookingId, $data);
        }

        $message = count($bookingIds) > 1 
            ? count($bookingIds) . ' rooms booked successfully.' 
            : 'Booking created successfully.';
        
        if ($hasPayment) {
            $message .= ' Invoice generated.';
        } elseif ($isAdvance) {
            $message .= ' Reservation confirmed until ' . ($data['expiry_date'] ?? 'no expiry date');
        } else {
            $message .= ' Pending payment.';
        }

        return redirect()->route('bookings.show', $primaryBookingId)->with('success', $message);
    }

    private function getCurrentTenantId()
    {
        // For now, return null or get from authenticated user
        // In multi-tenant setup, this would come from auth()->user()->tenant_id
        return auth()->user()->tenant_id ?? null;
    }

    private function handleGuest($data, $tenantId)
    {
        // If guest_id provided, verify it belongs to current tenant
        if (!empty($data['guest_id'])) {
            $guest = DB::table('guests')->where('id', $data['guest_id'])->first();
            if ($guest && ($tenantId === null || $guest->tenant_id === $tenantId)) {
                // Update guest if needed
                DB::table('guests')->where('id', $guest->id)->update([
                    'email' => $data['guest_email'] ?? $guest->email,
                    'phone' => $data['guest_phone'] ?? $guest->phone,
                    'updated_at' => now(),
                ]);
                return $guest->id;
            }
        }

        // Create new guest
        $guestId = DB::table('guests')->insertGetId([
            'tenant_id' => $tenantId,
            'first_name' => explode(' ', $data['guest_name'])[0] ?? $data['guest_name'],
            'last_name' => implode(' ', array_slice(explode(' ', $data['guest_name']), 1)) ?? '',
            'email' => $data['guest_email'] ?? null,
            'phone' => $data['guest_phone'] ?? null,
            'vip_level' => $data['reservation_type'] === 'vip' ? 'gold' : 'bronze',
            'total_stays' => 0,
            'total_spent' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->logAudit('created', 'guest', $guestId, ['name' => $data['guest_name']]);

        return $guestId;
    }

    private function calculateTotalAmount($roomIds, $checkInDate, $checkOutDate)
    {
        $checkIn = strtotime($checkInDate);
        $checkOut = strtotime($checkOutDate);
        $nights = max(1, ($checkOut - $checkIn) / (60 * 60 * 24));

        $total = 0;
        foreach ($roomIds as $roomId) {
            $room = DB::table('rooms')->where('id', $roomId)->first();
            if ($room) {
                $roomType = DB::table('room_types')->where('id', $room->room_type_id)->first();
                $total += $roomType->base_price * $nights;
            }
        }

        return $total;
    }

    private function createGuestFolio($bookingId, $bookingData, $tenantId)
    {
        $folioNumber = 'FOL-' . date('Ymd') . '-' . strtoupper(Str::random(4));
        
        $total = $bookingData['total_amount'] ?? 0;
        $paid = $bookingData['retainer_paid'] ?? 0;
        $balance = ($bookingData['balance_due'] ?? ($total - $paid));

        DB::table('guest_folios')->insert([
            'booking_id' => $bookingId,
            'guest_id' => $bookingData['guest_id'],
            'tenant_id' => $tenantId,
            'folio_number' => $folioNumber,
            'status' => 'open',
            'room_charges' => $total,
            'subtotal' => $total,
            'total_amount' => $total,
            'amount_paid' => $paid,
            'balance_due' => $balance,
            'payment_status' => $this->derivePaymentStatus($total, $paid),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function derivePaymentStatus(float $total, float $paid): string
    {
        if ($total <= 0) {
            return $paid > 0 ? 'overpaid' : 'paid';
        }

        if ($paid <= 0) {
            return 'unpaid';
        }

        if ($paid >= $total) {
            return $paid > $total ? 'overpaid' : 'paid';
        }

        if ($paid < $total * 0.5) {
            return 'deposit_paid';
        }

        return 'partially_paid';
    }

    private function logAudit($action, $modelType, $modelId, $data)
    {
        $userId = null;
        $tenantId = null;
        
        try {
            if (function_exists('auth') && auth()->check()) {
                $userId = auth()->id();
                $user = auth()->user();
                $tenantId = $user->tenant_id ?? null;
            }
        } catch (\Exception $e) {
            // Auth not available, proceed without user tracking
        }
        
        DB::table('audit_logs')->insert([
            'tenant_id' => $tenantId,
            'user_id' => $userId,
            'action' => $action,
            'model_type' => 'App\\Models\\' . ucfirst($modelType),
            'model_id' => $modelId,
            'description' => ucfirst($action) . ' ' . $modelType . ' #' . $modelId,
            'new_values' => json_encode($data),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->url(),
            'method' => request()->method(),
            'created_at' => now(),
        ]);
    }

    private function createInvoice($bookingId, $bookingData)
    {
        $bookingRecord = DB::table('bookings')->where('id', $bookingId)->first();
        $room = DB::table('rooms')->where('id', $bookingRecord->room_id ?? $bookingData['room_id'] ?? null)->first();
        $roomType = DB::table('room_types')->where('id', $room->room_type_id ?? null)->first();
        
        $checkIn = strtotime($bookingData['check_in_date']);
        $checkOut = strtotime($bookingData['check_out_date']);
        $nights = max(1, ($checkOut - $checkIn) / (60 * 60 * 24));
        
        $roomRate = $roomType->base_price ?? 0;
        $subtotal = $bookingData['total_amount'];
        $taxAmount = 0;
        $totalAmount = $subtotal;
        $amountPaid = $bookingData['retainer_paid'] ?? 0;
        $balanceDue = $totalAmount - $amountPaid;

        $invoiceData = [
            'invoice_number' => 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(4)),
            'booking_id' => $bookingId,
            'guest_name' => $bookingData['guest_name'],
            'guest_email' => $bookingData['guest_email'] ?? null,
            'guest_phone' => $bookingData['guest_phone'] ?? null,
            'room_number' => $room->room_number ?? 'N/A',
            'check_in_date' => $bookingData['check_in_date'],
            'check_in_time' => $bookingData['check_in_time'] ?? '14:00',
            'check_out_date' => $bookingData['check_out_date'],
            'check_out_time' => $bookingData['check_out_time'] ?? '11:00',
            'nights' => $nights,
            'room_rate' => $roomRate,
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
            'amount_paid' => $amountPaid,
            'balance_due' => $balanceDue,
            'payment_type' => $bookingData['payment_type'] ?? null,
            'payment_reference' => $bookingData['payment_reference'] ?? null,
            'status' => $amountPaid >= $totalAmount ? 'paid' : ($amountPaid > 0 ? 'partial' : 'pending'),
            'created_at' => now(),
            'updated_at' => now(),
        ];

        DB::table('invoices')->insert($invoiceData);
    }

    public function invoice($bookingId)
    {
        $booking = DB::table('bookings')
            ->join('rooms', 'bookings.room_id', '=', 'rooms.id')
            ->leftJoin('room_types', 'rooms.room_type_id', '=', 'room_types.id')
            ->select('bookings.*', 'rooms.room_number', 'room_types.name as room_type_name')
            ->where('bookings.id', $bookingId)
            ->first();

        if (!$booking) {
            abort(404);
        }

        $invoice = DB::table('invoices')
            ->where('booking_id', $bookingId)
            ->first();

        if (!$invoice) {
            // Create invoice if missing
            $this->createInvoice($bookingId, (array)$booking);
            $invoice = DB::table('invoices')->where('booking_id', $bookingId)->first();
        }

        // Get LIVE payment data - sum of all payments for this booking plus the initial retainer
        $totalPaid = DB::table('payments')
            ->where('booking_id', $bookingId)
            ->sum('amount') + ($booking->retainer_paid ?? 0);

        // Calculate current balance from LIVE data
        $currentBalanceDue = max(0, $booking->total_amount - $totalPaid);
        
        // Determine current status based on payment
        $currentStatus = 'pending';
        if ($currentBalanceDue <= 0) {
            $currentStatus = 'paid';
        } elseif ($totalPaid > 0) {
            $currentStatus = 'partial';
        }

        // Build updated invoice data object with LIVE values
        $invoiceData = (object)[
            'id' => $invoice->id ?? null,
            'booking_id' => $bookingId,
            'invoice_number' => $invoice->invoice_number ?? ('INV-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4))),
            'guest_name' => $booking->guest_name,
            'room_number' => $booking->room_number,
            'room_type_name' => $booking->room_type_name,
            'check_in_date' => $booking->check_in_date,
            'check_out_date' => $booking->check_out_date,
            'nights' => $invoice->nights ?? 1,
            'room_rate' => $invoice->room_rate ?? $booking->total_amount,
            'subtotal' => $booking->total_amount,
            'tax_amount' => 0,
            'total_amount' => $booking->total_amount,
            'amount_paid' => $totalPaid,
            'balance_due' => $currentBalanceDue,
            'status' => $currentStatus,
            'payment_type' => $booking->payment_method,
            'payment_reference' => $booking->payment_reference,
            'created_at' => $invoice->created_at ?? now(),
            'updated_at' => now(),
            'printed_at' => $invoice->printed_at ?? null,
        ];

        // Update the invoices table with current data
        if ($invoice) {
            DB::table('invoices')->where('id', $invoice->id)->update([
                'amount_paid' => $totalPaid,
                'balance_due' => $currentBalanceDue,
                'status' => $currentStatus,
                'updated_at' => now(),
            ]);
        }

        // Get lodge settings
        $settingsRow = DB::table('settings')->first();
        $settings = $settingsRow ? (array)$settingsRow : [];

        return view('bookings.invoice', compact('booking', 'invoice'))
            ->with('invoice', $invoiceData)
            ->with('settings', $settings);
    }

    public function printInvoice($bookingId)
    {
        $booking = DB::table('bookings')
            ->join('rooms', 'bookings.room_id', '=', 'rooms.id')
            ->leftJoin('room_types', 'rooms.room_type_id', '=', 'room_types.id')
            ->select('bookings.*', 'rooms.room_number', 'room_types.name as room_type_name')
            ->where('bookings.id', $bookingId)
            ->first();

        if (!$booking) {
            abort(404);
        }

        $invoice = DB::table('invoices')->where('booking_id', $bookingId)->first();

        // Get LIVE payment data - sum of all payments for this booking plus the initial retainer
        $totalPaid = DB::table('payments')
            ->where('booking_id', $bookingId)
            ->sum('amount') + ($booking->retainer_paid ?? 0);

        // Calculate current balance from LIVE data
        $currentBalanceDue = max(0, $booking->total_amount - $totalPaid);
        
        // Determine current status based on payment
        $currentStatus = 'pending';
        if ($currentBalanceDue <= 0) {
            $currentStatus = 'paid';
        } elseif ($totalPaid > 0) {
            $currentStatus = 'partial';
        }

        // Build updated invoice data object with LIVE values
        $invoiceData = (object)[
            'id' => $invoice->id ?? null,
            'booking_id' => $bookingId,
            'invoice_number' => $invoice->invoice_number ?? ('INV-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4))),
            'guest_name' => $booking->guest_name,
            'room_number' => $booking->room_number,
            'room_type_name' => $booking->room_type_name,
            'check_in_date' => $booking->check_in_date,
            'check_out_date' => $booking->check_out_date,
            'nights' => $invoice->nights ?? 1,
            'room_rate' => $invoice->room_rate ?? $booking->total_amount,
            'subtotal' => $booking->total_amount,
            'tax_amount' => 0,
            'total_amount' => $booking->total_amount,
            'amount_paid' => $totalPaid,
            'balance_due' => $currentBalanceDue,
            'status' => $currentStatus,
            'payment_type' => $booking->payment_method,
            'payment_reference' => $booking->payment_reference,
            'created_at' => $invoice->created_at ?? now(),
            'printed_at' => now(),
        ];

        // Update invoices table and mark as printed
        if ($invoice) {
            DB::table('invoices')->where('id', $invoice->id)->update([
                'amount_paid' => $totalPaid,
                'balance_due' => $currentBalanceDue,
                'status' => $currentStatus,
                'printed_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $settingsRow = DB::table('settings')->first();
        $settings = $settingsRow ? (array)$settingsRow : [];

        return view('bookings.invoice-print', compact('booking'))
            ->with('invoice', $invoiceData)
            ->with('settings', $settings);
    }

    public function show($id)
    {
        $booking = DB::table('bookings')
            ->join('rooms', 'bookings.room_id', '=', 'rooms.id')
            ->select('bookings.*', 'rooms.room_number')
            ->where('bookings.id', $id)
            ->first();

        if (!$booking) {
            abort(404);
        }

        return view('bookings.show', compact('booking'));
    }

    public function edit($id)
    {
        $booking = DB::table('bookings')->find($id);
        if (!$booking) {
            abort(404);
        }

        // Show available rooms plus the currently booked room
        $rooms = DB::table('rooms')
            ->leftJoin('room_types', 'rooms.room_type_id', '=', 'room_types.id')
            ->where(function ($query) use ($booking) {
                $query->where('rooms.status', 'available')
                      ->orWhere('rooms.id', $booking->room_id);
            })
            ->select('rooms.*', 'room_types.name as room_type_name', 'room_types.base_price as price')
            ->get();

        return view('bookings.edit', compact('booking', 'rooms'));
    }

    public function update(Request $request, $id)
    {
        $booking = DB::table('bookings')->find($id);
        if (!$booking) {
            abort(404);
        }

        $data = $request->validate([
            // Guest information
            'guest_name' => 'sometimes|string',
            'guest_email' => 'nullable|email',
            'guest_phone' => 'nullable|string',
            'guest_id' => 'nullable|exists:guests,id',
            
            // Room
            'room_id' => 'sometimes|exists:rooms,id',
            
            // Dates
            'check_in_date' => 'sometimes|date',
            'check_in_time' => 'nullable|date_format:H:i',
            'check_out_date' => 'sometimes|date|after:check_in_date',
            'check_out_time' => 'nullable|date_format:H:i',
            
            // Occupancy
            'adults' => 'sometimes|integer|min:1',
            'children' => 'sometimes|integer|min:0',
            
            // Reservation details
            'reservation_type' => 'sometimes|in:walk_in,advance,group,corporate,vip',
            'expiry_date' => 'nullable|date|after:today',
            
            // Financials
            'total_amount' => 'sometimes|numeric|min:0',
            'retainer_paid' => 'sometimes|numeric|min:0',
            'payment_method' => 'nullable|in:cash,credit_card,bank_transfer,crdb,selcom,dpo,gepg,mobile_money,control_number',
            'payment_reference' => 'nullable|string',
            
            // Corporate fields
            'company_name' => 'nullable|string',
            'tax_id' => 'nullable|string',
            'credit_terms_days' => 'nullable|integer|in:7,14,30,60',
            
            // Other
            'special_requests' => 'nullable|string',
            'notes' => 'nullable|string',
            'status' => 'sometimes|in:pending,reserved,confirmed,cancelled,checked_in,checked_out,no_show',
        ]);

        // Store old values for audit
        $oldValues = (array)$booking;

        // Handle guest update
        $tenantId = $this->getCurrentTenantId();
        if (isset($data['guest_id']) || isset($data['guest_name'])) {
            $guestId = $this->handleGuest($data, $tenantId);
            $data['guest_id'] = $guestId;
        }

        // Recalculate balance
        if (isset($data['total_amount']) || isset($data['retainer_paid'])) {
            $total = $data['total_amount'] ?? $booking->total_amount;
            $retainer = $data['retainer_paid'] ?? $booking->retainer_paid;
            $data['balance_due'] = $total - $retainer;
        }

        // If updating from pending/reserved to confirmed, create invoice
        $wasPending = in_array($booking->status, ['pending', 'reserved']);
        $isConfirmed = isset($data['status']) && $data['status'] === 'confirmed';
        if ($wasPending && $isConfirmed) {
            $invoiceExists = DB::table('invoices')->where('booking_id', $id)->exists();
            if (!$invoiceExists) {
                $invoiceData = array_merge($oldValues, $data);
                $this->createInvoice($id, $invoiceData);
            }
        }

        // Handle room change
        if (isset($data['room_id']) && $data['room_id'] != $booking->room_id) {
            // Free up old room
            DB::table('rooms')->where('id', $booking->room_id)->update(['status' => 'available']);
            // Book new room
            DB::table('rooms')->where('id', $data['room_id'])->update(['status' => 'booked']);
        }

        $data['updated_at'] = now();

        DB::table('bookings')->where('id', $id)->update($data);

        // Update guest folio
        $folio = DB::table('guest_folios')->where('booking_id', $id)->first();
        if ($folio) {
            $folioTotal = $data['total_amount'] ?? $folio->total_amount;
            $folioPaid = $data['retainer_paid'] ?? $folio->amount_paid;
            $folioBalance = $data['balance_due'] ?? $folio->balance_due;

            DB::table('guest_folios')->where('id', $folio->id)->update([
                'room_charges' => $folioTotal,
                'subtotal' => $folioTotal,
                'total_amount' => $folioTotal,
                'amount_paid' => $folioPaid,
                'balance_due' => $folioBalance,
                'payment_status' => $this->derivePaymentStatus($folioTotal, $folioPaid),
                'updated_at' => now(),
            ]);
        }

        // Log audit
        $this->logAudit('updated', 'booking', $id, [
            'old_values' => $oldValues,
            'new_values' => $data
        ]);

        return redirect()->route('bookings.show', $id)->with('success', 'Booking updated successfully');
    }

    public function confirmBooking(Request $request, $id)
    {
        $booking = DB::table('bookings')->find($id);
        if (!$booking) {
            abort(404);
        }

        $confirmWithoutPayment = $request->has('confirm_without_payment');

        if ($confirmWithoutPayment) {
            $data = $request->validate([
                'confirm_without_payment' => 'sometimes',
            ]);
        } else {
            $data = $request->validate([
                'payment_method' => 'required|in:cash,credit_card,bank_transfer,crdb,selcom,dpo,gepg,mobile_money,control_number',
                'payment_reference' => 'required|string',
                'retainer_paid' => 'nullable|numeric|min:0',
            ]);
        }

        $retainer = $data['retainer_paid'] ?? $booking->retainer_paid ?? 0;
        $newBalance = $booking->total_amount - $retainer;

        $updateData = [
            'status' => 'confirmed',
            'retainer_paid' => $retainer,
            'balance_due' => $newBalance,
            'updated_at' => now(),
        ];

        if (!$confirmWithoutPayment) {
            $updateData['payment_method'] = $data['payment_method'];
            $updateData['payment_reference'] = $data['payment_reference'];
        }

        DB::table('bookings')->where('id', $id)->update($updateData);

        // Update room status
        DB::table('rooms')->where('id', $booking->room_id)->update(['status' => 'booked']);

        // Create invoice if not exists (only when payment is provided)
        $invoiceExists = DB::table('invoices')->where('booking_id', $id)->exists();
        if (!$invoiceExists && !$confirmWithoutPayment) {
            $invoiceData = [
                'guest_name' => $booking->guest_name,
                'guest_email' => $booking->guest_email,
                'guest_phone' => $booking->guest_phone,
                'room_id' => $booking->room_id,
                'check_in_date' => $booking->check_in_date,
                'check_out_date' => $booking->check_out_date,
                'total_amount' => $booking->total_amount,
                'retainer_paid' => $retainer,
                'payment_method' => $data['payment_method'],
                'payment_reference' => $data['payment_reference'],
            ];
            $this->createInvoice($id, $invoiceData);
        }

        // Update guest folio
        $folio = DB::table('guest_folios')->where('booking_id', $id)->first();
        if ($folio) {
            DB::table('guest_folios')->where('id', $folio->id)->update([
                'amount_paid' => $retainer,
                'balance_due' => $newBalance,
                'payment_status' => $this->derivePaymentStatus($folio->total_amount, $retainer),
                'updated_at' => now(),
            ]);
        }

        // Log audit
        $auditData = [
            'amount' => $retainer,
            'balance_due' => $newBalance,
        ];
        if (!$confirmWithoutPayment) {
            $auditData['payment_method'] = $data['payment_method'];
        }
        $this->logAudit('confirmed', 'booking', $id, $auditData);

        $message = $confirmWithoutPayment 
            ? 'Booking confirmed without payment. Balance due: $' . number_format($newBalance, 2)
            : 'Booking confirmed and invoice generated.';

        return redirect()->route('bookings.show', $id)->with('success', $message);
    }

    public function destroy($id)
    {
        $booking = DB::table('bookings')->find($id);
        if (!$booking) {
            abort(404);
        }

        // Free up the room if not already checked out
        if ($booking->status !== 'checked_out') {
            DB::table('rooms')->where('id', $booking->room_id)->update(['status' => 'available']);
        }

        // Close guest folio
        $folio = DB::table('guest_folios')->where('booking_id', $id)->first();
        if ($folio) {
            DB::table('guest_folios')->where('id', $folio->id)->update([
                'status' => 'void',
                'closed_at' => now(),
                'closed_by' => auth()->id(),
                'updated_at' => now(),
            ]);
        }

        // Log audit
        $this->logAudit('deleted', 'booking', $id, (array)$booking);

        DB::table('bookings')->where('id', $id)->delete();

        return redirect()->route('bookings')->with('success', 'Booking cancelled successfully');
    }

    public function checkIn($id)
    {
        $booking = DB::table('bookings')->find($id);
        if (!$booking) {
            abort(404);
        }

        if ($booking->status === 'checked_in') {
            return redirect()->route('bookings.show', $id)->with('error', 'Guest is already checked in.');
        }

        DB::table('bookings')->where('id', $id)->update([
            'status'       => 'checked_in',
            'updated_at'   => now(),
        ]);

        DB::table('rooms')->where('id', $booking->room_id)->update([
            'status' => 'occupied',
            'last_cleaned_at' => now(),
        ]);

        // Update guest stay history
        if ($booking->guest_id) {
            DB::table('guests')->where('id', $booking->guest_id)->increment('total_stays');
        }

        // Log audit
        $this->logAudit('checked_in', 'booking', $id, ['room_id' => $booking->room_id]);

        return redirect()->route('bookings.show', $id)->with('success', 'Guest checked in successfully');
    }

    public function checkOut($id)
    {
        $booking = DB::table('bookings')->find($id);
        if (!$booking) {
            abort(404);
        }

        DB::table('bookings')->where('id', $id)->update([
            'status'       => 'checked_out',
            'actual_checkout' => now(),
            'updated_at'   => now(),
        ]);

        // Room goes to awaiting cleaning instead of available
        DB::table('rooms')->where('id', $booking->room_id)->update(['status' => 'awaiting_cleaning']);

        // Create housekeeping assignment
        $housekeepingId = DB::table('housekeeping_assignments')->insertGetId([
            'tenant_id' => $booking->tenant_id,
            'room_id' => $booking->room_id,
            'task_type' => 'cleaning',
            'status' => 'pending',
            'priority' => 'medium',
            'scheduled_time' => now(),
            'created_by' => auth()->id(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Close guest folio
        $folio = DB::table('guest_folios')->where('booking_id', $id)->first();
        if ($folio) {
            DB::table('guest_folios')->where('id', $folio->id)->update([
                'status' => 'closed',
                'closed_at' => now(),
                'closed_by' => auth()->id(),
                'updated_at' => now(),
            ]);
        }

        // Update guest total spent
        if ($booking->guest_id) {
            DB::table('guests')->where('id', $booking->guest_id)->increment('total_spent', $booking->total_amount);
        }

        // Log audit
        $this->logAudit('checked_out', 'booking', $id, [
            'balance_due' => $booking->balance_due,
            'housekeeping_created' => $housekeepingId
        ]);

        // If there's a balance due, redirect to billing with notification
        if ($booking->balance_due > 0) {
            return redirect()->route('billing.show', $id)
                ->with('warning', 'Guest checked out successfully. Outstanding balance of ' . number_format($booking->balance_due, 2) . ' requires payment.');
        }

        return redirect()->route('bookings.show', $id)->with('success', 'Guest checked out successfully. No outstanding balance.');
    }

    public function extendStay(Request $request, $id)
    {
        $booking = DB::table('bookings')->find($id);
        if (!$booking) {
            abort(404);
        }

        $data = $request->validate([
            'check_out_date' => 'required|date|after:' . $booking->check_out_date,
        ]);

        $newCheckOut = $data['check_out_date'];
        $oldCheckOut = $booking->check_out_date;

        $extraNights = max(0, (strtotime($newCheckOut) - strtotime($oldCheckOut)) / (60 * 60 * 24));

        if ($extraNights <= 0) {
            return redirect()->route('bookings')->with('error', 'New checkout date must be after the current checkout date.');
        }

        $roomRate = DB::table('rooms')
            ->join('room_types', 'rooms.room_type_id', '=', 'room_types.id')
            ->where('rooms.id', $booking->room_id)
            ->value('room_types.base_price') ?? 0;

        $extraCharge = $extraNights * $roomRate;
        $newTotal = ($booking->total_amount ?? 0) + $extraCharge;
        $newBalance = $newTotal - ($booking->retainer_paid ?? 0);

        DB::table('bookings')->where('id', $id)->update([
            'check_out_date' => $newCheckOut,
            'total_amount' => $newTotal,
            'balance_due' => $newBalance,
            'updated_at' => now(),
        ]);

        // Update folio totals
        $folio = DB::table('guest_folios')->where('booking_id', $id)->first();
        if ($folio) {
            DB::table('guest_folios')->where('id', $folio->id)->update([
                'room_charges' => $newTotal,
                'subtotal' => $newTotal,
                'total_amount' => $newTotal,
                'balance_due' => $newBalance,
                'payment_status' => $this->derivePaymentStatus($newTotal, $folio->amount_paid),
                'updated_at' => now(),
            ]);
        }

        $this->logAudit('extended_stay', 'booking', $id, [
            'old_check_out' => $oldCheckOut,
            'new_check_out' => $newCheckOut,
            'extra_nights' => $extraNights,
            'extra_charge' => $extraCharge,
        ]);

        return redirect()->route('bookings')->with('success', 'Stay extended by ' . $extraNights . ' night(s). Extra charge: ' . number_format($extraCharge, 2) . '. New balance due: ' . number_format($newBalance, 2));
    }

    public function checkoutOverdue($id)
    {
        $booking = DB::table('bookings')->find($id);
        if (!$booking) {
            abort(404);
        }

        // Use the standard checkout flow then send to billing for receipt/payment
        $this->checkOut($id);

        return redirect()->route('billing.show', $id)
            ->with('success', 'Guest checked out. Please review the bill and provide a receipt.');
    }

    public function markNoShow(Request $request, $id)
    {
        $booking = DB::table('bookings')->find($id);
        if (!$booking) {
            abort(404);
        }

        $data = $request->validate([
            'no_show_reason' => 'required|string',
        ]);

        DB::table('bookings')->where('id', $id)->update([
            'status' => 'no_show',
            'is_no_show' => true,
            'no_show_reason' => $data['no_show_reason'],
            'updated_at' => now(),
        ]);

        // Free up the room
        DB::table('rooms')->where('id', $booking->room_id)->update(['status' => 'available']);

        // Close guest folio
        $folio = DB::table('guest_folios')->where('booking_id', $id)->first();
        if ($folio) {
            DB::table('guest_folios')->where('id', $folio->id)->update([
                'status' => 'void',
                'closed_at' => now(),
                'closed_by' => auth()->id(),
                'updated_at' => now(),
            ]);
        }

        // Log audit
        $this->logAudit('marked_no_show', 'booking', $id, [
            'reason' => $data['no_show_reason']
        ]);

        return redirect()->route('bookings.show', $id)->with('success', 'Booking marked as no-show.');
    }
}
