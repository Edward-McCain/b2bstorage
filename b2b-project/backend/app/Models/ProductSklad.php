<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSklad extends Model
{
    use HasFactory;
    protected $table = 'products_sklad';
    protected $fillable = [
        'user_id', 'name', 'description', 'category', 'subcategory', 'country', 'supplier', 'article', 'code', 'external_code', 'unit', 'weight', 'volume', 'vat', 'min_stock', 'stock_type', 'packing', 'accounting_type', 'traceable', 'marking', 'product_type', 'barcode_type', 'barcode', 'cash_register_tax', 'cash_register_type'
    ];

    /**
     * Связь с изображениями товара
     */
    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_id');
    }
} 