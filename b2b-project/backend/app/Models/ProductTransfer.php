<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductTransfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'from_warehouse_id',
        'to_warehouse_id',
        'transfer_date',
        'status',
        'notes',
        'created_by',
        'completed_at',
        'completed_by'
    ];

    protected $casts = [
        'transfer_date' => 'date',
        'completed_at' => 'datetime',
    ];

    protected $appends = [
        'status_text',
        'total_items',
        'actual_total_items'
    ];

    // Статусы перемещений
    const STATUS_DRAFT = 'draft';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    public static function getStatuses(): array
    {
        return [
            self::STATUS_DRAFT => 'Черновик',
            self::STATUS_CONFIRMED => 'Подтвержден',
            self::STATUS_COMPLETED => 'Выполнен',
            self::STATUS_CANCELLED => 'Отменен'
        ];
    }

    public function getStatusTextAttribute(): string
    {
        $statuses = self::getStatuses();
        return $statuses[$this->status] ?? $this->status;
    }

    // Отношения
    public function fromWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'from_warehouse_id');
    }

    public function toWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'to_warehouse_id');
    }

    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function completedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by', 'id');
    }

    public function positions(): HasMany
    {
        return $this->hasMany(ProductTransferPosition::class, 'transfer_id');
    }

    // Скоупы
    public function scopeByWarehouse($query, $warehouseId)
    {
        return $query->where(function ($q) use ($warehouseId) {
            $q->where('from_warehouse_id', $warehouseId)
              ->orWhere('to_warehouse_id', $warehouseId);
        });
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('transfer_date', [$startDate, $endDate]);
    }

    // Методы
    public function canBeConfirmed(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function canBeCompleted(): bool
    {
        return $this->status === self::STATUS_CONFIRMED;
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_CONFIRMED]);
    }

    public function getTotalQuantityAttribute(): int
    {
        return $this->positions->sum('quantity');
    }

    public function getActualTotalQuantityAttribute(): int
    {
        return $this->positions->sum('actual_quantity') ?? 0;
    }

    public function getTotalItemsAttribute(): int
    {
        if (!$this->relationLoaded('positions')) {
            return 0;
        }
        return $this->positions->sum('quantity');
    }

    public function getActualTotalItemsAttribute(): int
    {
        if (!$this->relationLoaded('positions')) {
            return 0;
        }
        return $this->positions->sum('actual_quantity') ?? 0;
    }
} 