<?php

namespace App\Helpers;

class CategoryHelper
{
    /**
     * Маппинг языков к полям в базе данных
     */
    private static function getLanguageFieldMapping()
    {
        return [
            'ru' => 'name',
            'en' => 'name_en',
            'uz' => 'name_uz',
            'china' => 'name_china',
            'zh-CN' => 'name_china' // Добавляем поддержку zh-CN для совместимости
        ];
    }

    /**
     * Получить название категории в зависимости от языка пользователя
     */
    public static function getCategoryName($category, $userLanguage = 'ru')
    {
        $languageMapping = self::getLanguageFieldMapping();
        $languageField = $languageMapping[$userLanguage] ?? 'name';
        
        // Проверяем, есть ли поле для данного языка
        if (isset($category->$languageField) && !empty($category->$languageField)) {
            return $category->$languageField;
        }
        
        // Если нет перевода для выбранного языка, возвращаем базовое название
        return $category->name ?? '';
    }
    
    /**
     * Получить название подкатегории в зависимости от языка пользователя
     */
    public static function getSubcategoryName($subcategory, $userLanguage = 'ru')
    {
        $languageMapping = self::getLanguageFieldMapping();
        $languageField = $languageMapping[$userLanguage] ?? 'name';
        
        // Проверяем, есть ли поле для данного языка
        if (isset($subcategory->$languageField) && !empty($subcategory->$languageField)) {
            return $subcategory->$languageField;
        }
        
        // Если нет перевода для выбранного языка, возвращаем базовое название
        return $subcategory->name ?? '';
    }
    
    /**
     * Получить групповое название категории в зависимости от языка пользователя
     */
    public static function getCategoryGroupName($category, $userLanguage = 'ru')
    {
        $languageField = 'group_name_' . $userLanguage;
        
        // Проверяем, есть ли поле для данного языка
        if (isset($category->$languageField) && !empty($category->$languageField)) {
            return $category->$languageField;
        }
        
        // Если нет, возвращаем русское название как fallback
        if (isset($category->group_name_ru) && !empty($category->group_name_ru)) {
            return $category->group_name_ru;
        }
        
        // Если нет русского, возвращаем базовое название
        return $category->group_name ?? '';
    }
} 