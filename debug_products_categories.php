<?php
/**
 * Дополнительная диагностика API товаров с категориями
 * Запуск: php debug_products_categories.php
 */

// Конфигурация
$baseUrl = 'http://127.0.0.1:8000/api';
$token = '99|l9YCW6cJfqTAGk2FoCpD0tg2pkel92xNPxPpNqhw7e3185e3';

class ProductsCategoriesDebugger {
    private $baseUrl;
    private $token;
    
    public function __construct($baseUrl, $token) {
        $this->baseUrl = $baseUrl;
        $this->token = $token;
    }
    
    private function makeRequest($endpoint, $method = 'GET', $data = null) {
        $url = $this->baseUrl . $endpoint;
        $headers = [
            'Authorization: Bearer ' . $this->token,
            'Accept: application/json',
            'Content-Type: application/json'
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        
        if ($data && in_array($method, ['POST', 'PUT'])) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return [
            'code' => $httpCode,
            'data' => json_decode($response, true)
        ];
    }
    
    public function debugProductsCategories() {
        echo "🔍 ДОПОЛНИТЕЛЬНАЯ ДИАГНОСТИКА API ТОВАРОВ С КАТЕГОРИЯМИ\n";
        echo "========================================================\n\n";
        
        // 1. Проверяем настройки пользователя
        echo "📋 1. НАСТРОЙКИ ПОЛЬЗОВАТЕЛЯ\n";
        echo "-----------------------------\n";
        
        $settingsResponse = $this->makeRequest('/user/settings');
        if ($settingsResponse['code'] === 200) {
            $settings = $settingsResponse['data']['data']['personal'] ?? [];
            echo "cats_type: " . ($settings['cats_type'] ?? 'не указан') . "\n";
            echo "product_fields_visibility: " . ($settings['product_fields_visibility'] ?? 'не указан') . "\n";
            
            // Парсим product_fields_visibility
            if (isset($settings['product_fields_visibility'])) {
                $visibility = json_decode($settings['product_fields_visibility'], true);
                echo "categories enabled: " . ($visibility['categories'] ?? 'не указано') . "\n";
                echo "category enabled: " . ($visibility['category'] ?? 'не указано') . "\n";
                echo "subcategory enabled: " . ($visibility['subcategory'] ?? 'не указано') . "\n";
            }
        }
        echo "\n";
        
        // 2. Проверяем товары с разными параметрами
        echo "📋 2. ПРОВЕРКА API ТОВАРОВ\n";
        echo "---------------------------\n";
        
        // Без параметров
        echo "🔍 Товары без параметров:\n";
        $productsResponse = $this->makeRequest('/products');
        if ($productsResponse['code'] === 200) {
            $products = $productsResponse['data']['data']['data'] ?? [];
            echo "Найдено товаров: " . count($products) . "\n";
            
            foreach ($products as $product) {
                echo "  - {$product['name']}: category={$product['category']}, subcategory={$product['subcategory']}\n";
            }
        }
        echo "\n";
        
        // С параметром include_categories
        echo "🔍 Товары с include_categories=true:\n";
        $productsWithCategoriesResponse = $this->makeRequest('/products?include_categories=true');
        if ($productsWithCategoriesResponse['code'] === 200) {
            $productsWithCategories = $productsWithCategoriesResponse['data']['data']['data'] ?? [];
            echo "Найдено товаров: " . count($productsWithCategories) . "\n";
            
            foreach ($productsWithCategories as $product) {
                echo "  - {$product['name']}: category={$product['category']}, subcategory={$product['subcategory']}\n";
                echo "    category_name: {$product['category_name']}, subcategory_name: {$product['subcategory_name']}\n";
            }
        }
        echo "\n";
        
        // 3. Проверяем конкретный товар с подкатегорией
        echo "📋 3. АНАЛИЗ ТОВАРА С ПОДКАТЕГОРИЕЙ\n";
        echo "------------------------------------\n";
        
        $products = $productsResponse['data']['data']['data'] ?? [];
        foreach ($products as $product) {
            if ($product['subcategory'] && !$product['category']) {
                echo "🔍 Товар с подкатегорией без категории: {$product['name']}\n";
                echo "  subcategory: {$product['subcategory']}\n";
                echo "  subcategory_name: {$product['subcategory_name']}\n";
                echo "  subcategory_relation: " . ($product['subcategory_relation'] ? 'Есть' : 'NULL') . "\n";
                
                if ($product['subcategory_relation']) {
                    $relation = $product['subcategory_relation'];
                    echo "  Информация о подкатегории:\n";
                    echo "    ID: {$relation['subcategory_id']}\n";
                    echo "    Название: {$relation['name']}\n";
                    echo "    Категория: {$relation['category_id']}\n";
                    echo "    Количество товаров: {$relation['product_count']}\n";
                }
                echo "\n";
            }
        }
        
        // 4. Проверяем подкатегории для системных категорий
        echo "📋 4. ПРОВЕРКА ПОДКАТЕГОРИЙ\n";
        echo "----------------------------\n";
        
        $systemCategoriesResponse = $this->makeRequest('/categories');
        if ($systemCategoriesResponse['code'] === 200) {
            $systemCategories = $systemCategoriesResponse['data']['data'] ?? [];
            
            foreach ($systemCategories as $category) {
                $categoryId = $category['category_id'];
                $subcategoriesResponse = $this->makeRequest("/categories/{$categoryId}/subcategories");
                
                if ($subcategoriesResponse['code'] === 200) {
                    $subcategories = $subcategoriesResponse['data']['data'] ?? [];
                    if (!empty($subcategories)) {
                        echo "🔍 Категория '{$category['name']}' ({$categoryId}):\n";
                        foreach ($subcategories as $subcategory) {
                            echo "  - {$subcategory['name']} ({$subcategory['subcategory_id']})\n";
                        }
                        echo "\n";
                    }
                }
            }
        }
        
        // 5. Проверяем проблему с отображением
        echo "📋 5. АНАЛИЗ ПРОБЛЕМЫ ОТОБРАЖЕНИЯ\n";
        echo "-----------------------------------\n";
        
        $catsType = 'system';
        if ($settingsResponse['code'] === 200) {
            $catsType = $settingsResponse['data']['data']['personal']['cats_type'] ?? 'system';
        }
        
        echo "Тип категорий пользователя: {$catsType}\n";
        
        if ($catsType === 'user') {
            echo "⚠️  ПРОБЛЕМА: Пользователь использует пользовательские категории, но товары имеют системные подкатегории!\n";
            echo "Это объясняет, почему категории не отображаются.\n";
            echo "\nВозможные решения:\n";
            echo "1. Переключить пользователя на системные категории\n";
            echo "2. Создать пользовательские категории для существующих товаров\n";
            echo "3. Мигрировать данные товаров в пользовательские категории\n";
        } else {
            echo "✅ Пользователь использует системные категории\n";
        }
        
        // 6. Проверяем товары с системными подкатегориями
        echo "\n📋 6. ТОВАРЫ С СИСТЕМНЫМИ ПОДКАТЕГОРИЯМИ\n";
        echo "--------------------------------------------\n";
        
        $productsWithSystemSubcategories = [];
        foreach ($products as $product) {
            if ($product['subcategory'] && $product['subcategory_relation']) {
                $relation = $product['subcategory_relation'];
                $productsWithSystemSubcategories[] = [
                    'product' => $product,
                    'system_category_id' => $relation['category_id'],
                    'system_subcategory_id' => $relation['subcategory_id']
                ];
            }
        }
        
        echo "Найдено товаров с системными подкатегориями: " . count($productsWithSystemSubcategories) . "\n";
        
        foreach ($productsWithSystemSubcategories as $item) {
            $product = $item['product'];
            echo "🛍️ Товар: {$product['name']}\n";
            echo "  Системная категория: {$item['system_category_id']}\n";
            echo "  Системная подкатегория: {$item['system_subcategory_id']}\n";
            echo "  Название подкатегории: {$product['subcategory_name']}\n";
            echo "\n";
        }
        
        // 7. Рекомендации
        echo "📋 7. РЕКОМЕНДАЦИИ\n";
        echo "-------------------\n";
        
        if ($catsType === 'user' && !empty($productsWithSystemSubcategories)) {
            echo "🚨 КРИТИЧЕСКАЯ ПРОБЛЕМА ОБНАРУЖЕНА!\n";
            echo "Пользователь использует пользовательские категории, но товары имеют системные подкатегории.\n";
            echo "\nРекомендуемые действия:\n";
            echo "1. Переключить пользователя на системные категории\n";
            echo "2. Или создать пользовательские категории для существующих товаров\n";
            echo "3. Или мигрировать данные товаров\n";
        } else {
            echo "✅ Проблем не обнаружено\n";
        }
    }
}

// Запуск диагностики
$debugger = new ProductsCategoriesDebugger($baseUrl, $token);
$debugger->debugProductsCategories(); 