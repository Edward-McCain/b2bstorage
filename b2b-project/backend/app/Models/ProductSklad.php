<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSklad extends Model
{
    use HasFactory;
    protected $table = 'products_sklad';
    protected $fillable = [
        'user_id', 'name', 'description', 'category', 'subcategory', 'country', 'supplier', 'article', 'code', 'external_code', 'unit', 'warehouse_id', 'weight', 'volume', 'vat', 'min_stock', 'stock_type', 'packing', 'accounting_type', 'traceable', 'marking', 'product_type', 'barcode_type', 'barcode', 'cash_register_tax', 'cash_register_type'
    ];

    /**
     * Связь с изображениями товара
     */
    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_id');
    }

    /**
     * Связь с категорией
     */
    public function categoryRelation()
    {
        return $this->belongsTo(Category::class, 'category', 'category_id');
    }

    /**
     * Связь с подкатегорией
     */
    public function subcategoryRelation()
    {
        return $this->belongsTo(Subcategory::class, 'subcategory', 'subcategory_id');
    }

    /**
     * Получить название категории
     */
    public function getCategoryNameAttribute()
    {
        return $this->categoryRelation ? $this->categoryRelation->name_ru : $this->category;
    }

    /**
     * Получить название подкатегории
     */
    public function getSubcategoryNameAttribute()
    {
        return $this->subcategoryRelation ? $this->subcategoryRelation->name_ru : $this->subcategory;
    }
} 