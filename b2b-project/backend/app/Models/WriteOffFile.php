<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WriteOffFile extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'write_off_id',
        'filename',
        'size_mb',
        'uploaded_at',
        'employee',
        'file_url'
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
        'size_mb' => 'decimal:2'
    ];

    /**
     * Связь со списанием
     */
    public function writeOff()
    {
        return $this->belongsTo(WriteOff::class);
    }
} 