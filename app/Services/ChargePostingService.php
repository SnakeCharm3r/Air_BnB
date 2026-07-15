<?php

namespace App\Services;

use App\Enums\ChargeStatus;
use App\Enums\ChargeType;
use App\Models\Booking;
use App\Models\BookingCharge;
use App\Models\GuestFolio;
use App\Models\User;

class ChargePostingService
{
    public function __construct(protected FolioService $folioService)
    {
    }

    /**
     * Post a charge to a booking folio.
     *
     * @param array{booking_id:int, folio_id:int, description:string, charge_type:string, quantity:float, unit_price:float, discount_amount?:float, posting_date?:string, reference_type?:string, reference_id?:int, posted_by?:int|null} $data
     */
    public function postCharge(array $data): BookingCharge
    {
        $booking = Booking::findOrFail($data['booking_id']);
        $folio = GuestFolio::findOrFail($data['folio_id']);

        $quantity = $data['quantity'] ?? 1;
        $unitPrice = $data['unit_price'] ?? 0;
        $discount = $data['discount_amount'] ?? 0;
        $total = max(0, ($quantity * $unitPrice) - $discount);

        $charge = BookingCharge::create([
            'booking_id' => $booking->id,
            'folio_id' => $folio->id,
            'description' => $data['description'],
            'amount' => $total, // Backward-compatible amount field.
            'category' => $this->mapCategory($data['charge_type'] ?? ChargeType::Miscellaneous->value),
            'charge_type' => $data['charge_type'] ?? ChargeType::Miscellaneous->value,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'discount_amount' => $discount,
            'total_amount' => $total,
            'posting_date' => $data['posting_date'] ?? now()->toDateString(),
            'status' => ChargeStatus::Posted->value,
            'created_by' => $data['posted_by'] ?? auth()->id(),
            'posted_by' => $data['posted_by'] ?? auth()->id(),
            'reference_type' => $data['reference_type'] ?? null,
            'reference_id' => $data['reference_id'] ?? null,
        ]);

        $this->folioService->recalculate($folio);

        return $charge;
    }

    /**
     * Reverse a posted charge by creating a negative reversing entry.
     * The original charge remains intact for audit purposes.
     */
    public function reverseCharge(BookingCharge $originalCharge, ?User $user = null, ?string $reason = null): BookingCharge
    {
        if (! $originalCharge->isPosted()) {
            throw new \RuntimeException('Only posted charges can be reversed.');
        }

        $originalCharge->update(['status' => ChargeStatus::Reversed->value]);

        $reversal = BookingCharge::create([
            'booking_id' => $originalCharge->booking_id,
            'folio_id' => $originalCharge->folio_id,
            'description' => 'Reversal: ' . $originalCharge->description . ($reason ? " ({$reason})" : ''),
            'amount' => -$originalCharge->total_amount,
            'category' => $originalCharge->category,
            'charge_type' => $originalCharge->charge_type,
            'quantity' => -$originalCharge->quantity,
            'unit_price' => $originalCharge->unit_price,
            'discount_amount' => -$originalCharge->discount_amount,
            'total_amount' => -$originalCharge->total_amount,
            'posting_date' => now()->toDateString(),
            'status' => ChargeStatus::Reversed->value,
            'created_by' => $user?->id ?? auth()->id(),
            'posted_by' => $user?->id ?? auth()->id(),
            'reference_type' => $originalCharge->reference_type,
            'reference_id' => $originalCharge->reference_id,
        ]);

        $this->folioService->recalculate($originalCharge->folio);

        return $reversal;
    }

    /**
     * Map a charge type to the legacy category enum for backward compatibility.
     */
    protected function mapCategory(string $chargeType): string
    {
        return match ($chargeType) {
            'room', 'extra_bed', 'early_check_in', 'late_check_out' => 'service',
            'restaurant', 'room_service', 'mini_bar', 'spa' => 'item',
            'damage' => 'damage',
            default => 'other',
        };
    }
}
