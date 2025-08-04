<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Helpers\CategoryHelper;

class CategoryService
{
    /**
     * Получить категории в зависимости от типа пользователя
     */
    public static function getCategoriesByType($userId, $categoryType = 'system', $userLanguage = 'ru')
    {
        if ($categoryType === 'user') {
            return DB::table('user_categories')
                ->select('id', 'category_id', 'name')
                ->where('user_id', $userId)
                ->orderBy('name')
                ->get();
        } else {
            $categories = DB::table('categories')
                ->select('id', 'category_id', 'name', 'name_ru', 'name_en', 'name_uz', 'name_china')
                ->orderBy('name_ru')
                ->get();
            
            // Применяем правильные названия в зависимости от языка
            foreach ($categories as $category) {
                $category->name = CategoryHelper::getCategoryName($category, $userLanguage);
            }
            
            return $categories;
        }
    }
    
    /**
     * Получить подкатегории в зависимости от типа пользователя
     */
    public static function getSubcategoriesByType($categoryId, $userId, $categoryType = 'system', $userLanguage = 'ru')
    {
        if ($categoryType === 'user') {
            return DB::table('user_subcategories')
                ->select('id', 'subcategory_id', 'name')
                ->where('category_id', $categoryId)
                ->where('user_id', $userId)
                ->orderBy('name')
                ->get();
        } else {
            $subcategories = DB::table('subcategories')
                ->select('id', 'subcategory_id', 'name', 'name_ru', 'name_en', 'name_uz', 'name_china')
                ->where('category_id', $categoryId)
                ->orderBy('name_ru')
                ->get();
            
            // Применяем правильные названия в зависимости от языка
            foreach ($subcategories as $subcategory) {
                $subcategory->name = CategoryHelper::getSubcategoryName($subcategory, $userLanguage);
            }
            
            return $subcategories;
        }
    }
    
    /**
     * Проверить существование категории
     */
    public static function categoryExists($categoryId, $userId, $categoryType = 'system')
    {
        if ($categoryType === 'user') {
            return DB::table('user_categories')
                ->where('category_id', $categoryId)
                ->where('user_id', $userId)
                ->exists();
        } else {
            return DB::table('categories')
                ->where('category_id', $categoryId)
                ->exists();
        }
    }
    
    /**
     * Проверить существование подкатегории
     */
    public static function subcategoryExists($subcategoryId, $userId, $categoryType = 'system')
    {
        if ($categoryType === 'user') {
            return DB::table('user_subcategories')
                ->where('subcategory_id', $subcategoryId)
                ->where('user_id', $userId)
                ->exists();
        } else {
            return DB::table('subcategories')
                ->where('subcategory_id', $subcategoryId)
                ->exists();
        }
    }
    
    /**
     * Получить название категории
     */
    public static function getCategoryName($categoryId, $userId, $categoryType = 'system', $userLanguage = 'ru')
    {
        if ($categoryType === 'user') {
            $category = DB::table('user_categories')
                ->select('name')
                ->where('category_id', $categoryId)
                ->where('user_id', $userId)
                ->first();
        } else {
            $category = DB::table('categories')
                ->select('name', 'name_ru', 'name_en', 'name_uz', 'name_china')
                ->where('category_id', $categoryId)
                ->first();
        }
        
        if (!$category) {
            return null;
        }
        
        if ($categoryType === 'user') {
            return $category->name;
        } else {
            return CategoryHelper::getCategoryName($category, $userLanguage);
        }
    }
    
    /**
     * Получить название подкатегории
     */
    public static function getSubcategoryName($subcategoryId, $userId, $categoryType = 'system', $userLanguage = 'ru')
    {
        if ($categoryType === 'user') {
            $subcategory = DB::table('user_subcategories')
                ->select('name')
                ->where('subcategory_id', $subcategoryId)
                ->where('user_id', $userId)
                ->first();
        } else {
            $subcategory = DB::table('subcategories')
                ->select('name', 'name_ru', 'name_en', 'name_uz', 'name_china')
                ->where('subcategory_id', $subcategoryId)
                ->first();
        }
        
        if (!$subcategory) {
            return null;
        }
        
        if ($categoryType === 'user') {
            return $subcategory->name;
        } else {
            return CategoryHelper::getSubcategoryName($subcategory, $userLanguage);
        }
    }
    
    /**
     * Получить полную информацию о категории и подкатегории товара
     */
    public static function getProductCategoryInfo($product, $userId, $categoryType = 'system')
    {
        $categoryName = null;
        $subcategoryName = null;
        
        if ($product->category) {
            $categoryName = self::getCategoryName($product->category, $userId, $categoryType);
        }
        
        if ($product->subcategory) {
            $subcategoryName = self::getSubcategoryName($product->subcategory, $userId, $categoryType);
        }
        
        return [
            'category_id' => $product->category,
            'category_name' => $categoryName,
            'subcategory_id' => $product->subcategory,
            'subcategory_name' => $subcategoryName
        ];
    }
    
    /**
     * Валидировать категории товара
     */
    public static function validateProductCategories($categoryId, $subcategoryId, $userId, $categoryType = 'system')
    {
        $errors = [];
        
        if ($categoryId) {
            if (!self::categoryExists($categoryId, $userId, $categoryType)) {
                $errors[] = "Категория '{$categoryId}' не существует";
            }
        }
        
        if ($subcategoryId) {
            if (!self::subcategoryExists($subcategoryId, $userId, $categoryType)) {
                $errors[] = "Подкатегория '{$subcategoryId}' не существует";
            }
        }
        
        return $errors;
    }
} 