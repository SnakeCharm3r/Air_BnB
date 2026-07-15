<?php

namespace App\Events;

use App\Models\GuestFolio;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FolioClosed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public GuestFolio $folio)
    {
    }
}
