<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subcategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'subcategory_id',
        'name',
        'category_id',
        'product_count',
        'name_en',
        'name_ru',
        'name_uz'
    ];

    /**
     * Получить категорию для этой подкатегории
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

    /**
     * Получить товары для этой подкатегории
     */
    public function products()
    {
        return $this->hasMany(ProductSklad::class, 'subcategory', 'subcategory_id');
    }
}
