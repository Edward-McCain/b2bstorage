<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ReceiptPosition;
use App\Models\UserCategory;
use App\Models\UserSubcategory;

class ProductSklad extends Model
{
    use HasFactory;
    protected $table = 'products_sklad';
    protected $fillable = [
        'user_id', 'name', 'description', 'category', 'subcategory', 'country', 'supplier', 'article', 'code', 'external_code', 'unit', 'warehouse_id', 'weight', 'volume', 'vat', 'min_stock', 'stock_type', 'packing', 'accounting_type', 'traceable', 'marking', 'product_type', 'barcode_type', 'barcode', 'cash_register_tax', 'cash_register_type', 'start_count', 'price', 'fields'
    ];

    /**
     * Атрибуты, которые должны быть приведены к определенным типам
     */
    protected $casts = [
        'start_count' => 'integer',
        'price' => 'decimal:2',
        'weight' => 'decimal:3',
        'volume' => 'decimal:3',
        'min_stock' => 'decimal:3',
        'traceable' => 'boolean',
        'warehouse_id' => 'integer',
        'user_id' => 'integer',
        'fields' => 'array',
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
        if (!$this->category) {
            return null;
        }
        
        // Проверяем тип категорий пользователя
        $user = $this->user;
        if (!$user) {
            return $this->categoryRelation ? $this->categoryRelation->name_ru : $this->category;
        }
        
        $catsType = $user->cats_type ?? 'system';
        
        if ($catsType === 'user') {
            // Для пользовательских категорий
            $userCategory = \App\Models\UserCategory::where('category_id', $this->category)
                ->where('user_id', $user->id)
                ->first();
            return $userCategory ? $userCategory->name : $this->category;
        } else {
            // Для системных категорий
            return $this->categoryRelation ? $this->categoryRelation->name_ru : $this->category;
        }
    }

    /**
     * Получить название подкатегории
     */
    public function getSubcategoryNameAttribute()
    {
        if (!$this->subcategory) {
            return null;
        }
        
        // Проверяем тип категорий пользователя
        $user = $this->user;
        if (!$user) {
            return $this->subcategoryRelation ? $this->subcategoryRelation->name_ru : $this->subcategory;
        }
        
        $catsType = $user->cats_type ?? 'system';
        
        if ($catsType === 'user') {
            // Для пользовательских подкатегорий
            $userSubcategory = \App\Models\UserSubcategory::where('subcategory_id', $this->subcategory)
                ->where('user_id', $user->id)
                ->first();
            return $userSubcategory ? $userSubcategory->name : $this->subcategory;
        } else {
            // Для системных подкатегорий
            return $this->subcategoryRelation ? $this->subcategoryRelation->name_ru : $this->subcategory;
        }
    }

    /**
     * Связь с пользователем
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Связь со складом
     */
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    /**
     * Связь с категорией (для админ-панели)
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category', 'category_id');
    }

    /**
     * Связь с подкатегорией (для админ-панели)
     */
    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class, 'subcategory', 'subcategory_id');
    }

    /**
     * Связь с позициями оприходования
     */
    public function receiptPositions()
    {
        return $this->hasMany(ReceiptPosition::class, 'product_id');
    }

    /**
     * Связь с остатками на складах
     */
    public function balances()
    {
        return $this->hasMany(ProductBalance::class, 'product_id');
    }
} 