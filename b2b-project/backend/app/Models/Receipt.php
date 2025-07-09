<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    use HasFactory;

    protected $fillable = [
        'number',
        'date',
        'status',
        'is_posted',
        'organization',
        'project',
        'warehouse',
        'comment',
        'overhead_costs',
        'total',
        'user_id',
        'created_by',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'date' => 'datetime',
        'is_posted' => 'boolean',
        'overhead_costs' => 'decimal:2',
        'total' => 'decimal:2'
    ];

    public function positions()
    {
        return $this->hasMany(ReceiptPosition::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse');
    }

    public function files()
    {
        return $this->hasMany(ReceiptFile::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 