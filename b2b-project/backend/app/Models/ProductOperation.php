<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ProductOperation extends Model
{
    use HasFactory;

    protected $table = 'product_operations';
    
    protected $fillable = [
        'product_id',
        'warehouse_id',
        'operation_type',
        'quantity',
        'reference_type',
        'reference_id',
        'notes',
        'created_by'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'reference_id' => 'integer',
        'created_by' => 'integer'
    ];

    /**
     * Типы операций
     */
    const TYPE_RECEIPT = 'receipt';
    const TYPE_WRITE_OFF = 'write_off';
    const TYPE_INVENTORY = 'inventory';
    const TYPE_TRANSFER_IN = 'transfer_in';
    const TYPE_TRANSFER_OUT = 'transfer_out';

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
     * Связь с пользователем
     */
    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Получить все типы операций
     */
    public static function getOperationTypes()
    {
        return [
            self::TYPE_RECEIPT => 'Оприходование',
            self::TYPE_WRITE_OFF => 'Списание',
            self::TYPE_INVENTORY => 'Инвентаризация',
            self::TYPE_TRANSFER_IN => 'Перемещение (входящее)',
            self::TYPE_TRANSFER_OUT => 'Перемещение (исходящее)'
        ];
    }

    /**
     * Получить название типа операции
     */
    public function getOperationTypeNameAttribute()
    {
        $types = self::getOperationTypes();
        return $types[$this->operation_type] ?? $this->operation_type;
    }

    /**
     * Создать операцию
     */
    public static function createOperation($data)
    {
        return static::create([
            'product_id' => $data['product_id'],
            'warehouse_id' => $data['warehouse_id'],
            'operation_type' => $data['operation_type'],
            'quantity' => $data['quantity'],
            'reference_type' => $data['reference_type'] ?? null,
            'reference_id' => $data['reference_id'] ?? null,
            'notes' => $data['notes'] ?? null,
            'created_by' => $data['created_by'] ?? Auth::id()
        ]);
    }
} 