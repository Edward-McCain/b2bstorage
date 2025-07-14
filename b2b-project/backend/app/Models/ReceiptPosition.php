<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceiptPosition extends Model
{
    use HasFactory;

    protected $fillable = [
        'receipt_id',
        'product_id',
        'name',
        'code',
        'barcode',
        'article',
        'quantity',
        'balance',
        'price',
        'amount',
        'reason',
        'gtd',
        'rnpt',
        'country',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'balance' => 'decimal:3',
        'price' => 'decimal:2',
        'amount' => 'decimal:2'
    ];

    public function receipt()
    {
        return $this->belongsTo(Receipt::class);
    }

    public function product()
    {
        return $this->belongsTo(ProductSklad::class, 'product_id');
    }
} 