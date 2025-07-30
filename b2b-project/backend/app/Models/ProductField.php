<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductField extends Model
{
    use HasFactory;

    protected $table = 'product_fields';

    protected $fillable = [
        'user_id',
        'field_name',
        'field_type',
        'list_options',
    ];

    public $timestamps = false;
} 