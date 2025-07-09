<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceiptFile extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'receipt_id',
        'filename',
        'size_mb',
        'file_url',
        'uploaded_at',
        'employee'
    ];

    protected $casts = [
        'size_mb' => 'decimal:2',
        'uploaded_at' => 'datetime'
    ];

    public function receipt()
    {
        return $this->belongsTo(Receipt::class);
    }
} 