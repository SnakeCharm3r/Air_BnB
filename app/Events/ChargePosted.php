<?php

namespace App\Events;

use App\Models\BookingCharge;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChargePosted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public BookingCharge $charge)
    {
    }
}
