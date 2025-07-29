<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCategory extends Model
{
    use HasFactory;

    protected $table = 'user_categories';
    
    protected $fillable = [
        'user_id',
        'category_id',
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
     * Связь с подкатегориями
     */
    public function subcategories()
    {
        return $this->hasMany(UserSubcategory::class, 'category_id', 'category_id');
    }
} 