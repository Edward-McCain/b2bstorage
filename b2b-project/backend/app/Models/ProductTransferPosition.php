<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\ProductSklad;

class ProductTransferPosition extends Model
{
    use HasFactory;

    protected $fillable = [
        'transfer_id',
        'product_id',
        'quantity',
        'actual_quantity',
        'notes'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'actual_quantity' => 'integer',
    ];

    // Отношения
    public function transfer(): BelongsTo
    {
        return $this->belongsTo(ProductTransfer::class, 'transfer_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(ProductSklad::class, 'product_id');
    }

    // Методы
    public function getQuantityDifferenceAttribute(): int
    {
        if ($this->actual_quantity === null) {
            return 0;
        }
        return $this->actual_quantity - $this->quantity;
    }

    public function isCompleted(): bool
    {
        return $this->actual_quantity !== null;
    }

    public function getCompletionPercentageAttribute(): float
    {
        if ($this->quantity === 0) {
            return 0;
        }
        return ($this->actual_quantity ?? 0) / $this->quantity * 100;
    }
} 