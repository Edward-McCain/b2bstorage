<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryItem extends Model
{
    protected $fillable = [
        'inventory_id',
        'product_id',
        'calculated_quantity',
        'actual_quantity',
        'notes',
        'photo'
    ];

    protected $casts = [
        'calculated_quantity' => 'integer',
        'actual_quantity' => 'integer',
    ];

    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(ProductSklad::class);
    }
} 