<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductBalance extends Model
{
    use HasFactory;

    protected $table = 'product_balances';
    
    protected $fillable = [
        'product_id',
        'warehouse_id',
        'quantity'
    ];

    protected $casts = [
        'quantity' => 'integer'
    ];

    /**
     * Связь с товаром
     */
    public function product()
    {
        return $this->belongsTo(ProductSklad::class, 'product_id');
    }

    /**
     * Связь со складом
     */
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    /**
     * Получить остаток товара на конкретном складе
     */
    public static function getBalance($productId, $warehouseId)
    {
        return static::where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->first();
    }

    /**
     * Обновить остаток товара на складе
     */
    public static function updateBalance($productId, $warehouseId, $quantity)
    {
        return static::updateOrCreate(
            [
                'product_id' => $productId,
                'warehouse_id' => $warehouseId
            ],
            [
                'quantity' => $quantity
            ]
        );
    }

    /**
     * Увеличить остаток товара на складе
     */
    public static function incrementBalance($productId, $warehouseId, $quantity)
    {
        $balance = static::firstOrCreate(
            [
                'product_id' => $productId,
                'warehouse_id' => $warehouseId
            ],
            [
                'quantity' => 0
            ]
        );

        $balance->increment('quantity', $quantity);
        return $balance;
    }

    /**
     * Уменьшить остаток товара на складе
     */
    public static function decrementBalance($productId, $warehouseId, $quantity)
    {
        $balance = static::firstOrCreate(
            [
                'product_id' => $productId,
                'warehouse_id' => $warehouseId
            ],
            [
                'quantity' => 0
            ]
        );

        $balance->decrement('quantity', $quantity);
        return $balance;
    }
} 