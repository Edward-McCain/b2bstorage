<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'address'
    ];

    /**
     * Связь с пользователем
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Связь с оприходованиями
     */
    public function receipts()
    {
        return $this->hasMany(Receipt::class);
    }
}
