<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSubcategory extends Model
{
    use HasFactory;

    protected $table = 'user_subcategories';
    
    protected $fillable = [
        'user_id',
        'category_id',
        'subcategory_id',
        'name'
    ];

    protected $casts = [
        'user_id' => 'integer'
    ];

    /**
     * Связь с пользователем
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Связь с категорией
     */
    public function category()
    {
        return $this->belongsTo(UserCategory::class, 'category_id', 'category_id');
    }
} 