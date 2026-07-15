<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    protected $table = 'inventory_items';

    protected $fillable = [
        'tenant_id',
        'name',
        'category',
        'quantity',
        'unit',
        'min_threshold',
        'location',
        'supplier',
        'unit_cost',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'min_threshold' => 'integer',
        'unit_cost' => 'decimal:2',
    ];
}
