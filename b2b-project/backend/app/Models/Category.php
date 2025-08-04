<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'icon_url',
        'product_count',
        'group_name',
        'name_en',
        'name_ru',
        'name_uz',
        'name_china',
        'group_name_en',
        'group_name_ru',
        'group_name_uz',
        'group_name_china'
    ];

    /**
     * Получить подкатегории для этой категории
     */
    public function subcategories()
    {
        return $this->hasMany(Subcategory::class, 'category_id', 'category_id');
    }

    /**
     * Получить товары для этой категории
     */
    public function products()
    {
        return $this->hasMany(ProductSklad::class, 'category', 'category_id');
    }
}
