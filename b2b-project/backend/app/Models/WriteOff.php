<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WriteOff extends Model
{
    protected $fillable = [
        'number',
        'date',
        'organization',
        'project',
        'warehouse',
        'status',
        'is_posted',
        'comment',
        'total',
        'overhead_costs',
        'created_by',
        'user_id'
    ];

    protected $casts = [
        'date' => 'datetime',
        'is_posted' => 'boolean',
        'total' => 'decimal:2',
        'overhead_costs' => 'decimal:2'
    ];

    /**
     * Связь с пользователем
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Связь с позициями списания
     */
    public function positions()
    {
        return $this->hasMany(WriteOffPosition::class);
    }

    /**
     * Связь с файлами списания
     */
    public function files()
    {
        return $this->hasMany(WriteOffFile::class);
    }

    /**
     * Связь со складом
     */
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse');
    }
} 