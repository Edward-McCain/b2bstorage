<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WriteOffPosition extends Model
{
    protected $fillable = [
        'write_off_id',
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
        'product_id'
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'balance' => 'decimal:3',
        'price' => 'decimal:2',
        'amount' => 'decimal:2'
    ];

    /**
     * Связь со списанием
     */
    public function writeOff()
    {
        return $this->belongsTo(WriteOff::class);
    }

    /**
     * Связь с товаром
     */
    public function product()
    {
        return $this->belongsTo(ProductSklad::class, 'product_id');
    }
} 