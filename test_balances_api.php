<?php

// Тест API эндпоинтов фильтра страницы "Остатки"
// Проверяет работу API запросов, которые используются на фронтенде

class BalancesApiTest {
    private $baseUrl = 'http://127.0.0.1:8000/api'; // Локальный сервер
    private $testResults = [];
    
    public function __construct() {
        // Получаем токен авторизации
        $this->getAuthToken();
    }
    
    private function getAuthToken() {
        // Здесь нужно получить токен авторизации
        // Для тестирования можно использовать существующий токен или создать новый
        $this->authToken = '99|l9YCW6cJfqTAGk2FoCpD0tg2pkel92xNPxPpNqhw7e3185e3'; // Замените на реальный токен
    }
    
    private function makeApiRequest($endpoint, $method = 'GET', $data = null) {
        $url = $this->baseUrl . $endpoint;
        
        $headers = [
            'Accept: application/json',
            'Content-Type: application/json'
        ];
        
        if ($this->authToken) {
            $headers[] = 'Authorization: Bearer ' . $this->authToken;
        }
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if ($data) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return [
            'status' => $httpCode,
            'data' => json_decode($response, true),
            'raw' => $response
        ];
    }
    
    public function runAllTests() {
        echo "\n🔍 НАЧАЛО ТЕСТИРОВАНИЯ API ФИЛЬТРА СТРАНИЦЫ 'ОСТАТКИ'\n";
        echo "========================================================\n\n";
        
        // Тест 1: Получение списка складов
        $this->testWarehousesApi();
        
        // Тест 2: Получение категорий
        $this->testCategoriesApi();
        
        // Тест 3: Получение подкатегорий
        $this->testSubcategoriesApi();
        
        // Тест 4: Получение настроек полей товара
        $this->testProductFieldsApi();
        
        // Тест 5: Базовый запрос остатков
        $this->testBalancesBasicApi();
        
        // Тест 6: Фильтр по складу
        $this->testBalancesWarehouseFilter();
        
        // Тест 7: Фильтр по поиску
        $this->testBalancesSearchFilter();
        
        // Тест 8: Фильтр по количеству
        $this->testBalancesQuantityFilter();
        
        // Тест 9: Фильтр по категории
        $this->testBalancesCategoryFilter();
        
        // Тест 10: Фильтр по дате создания
        $this->testBalancesDateFilter();
        
        // Тест 11: Комбинированный фильтр
        $this->testBalancesCombinedFilter();
        
        // Тест 12: Сводка по остаткам
        $this->testBalancesSummaryApi();
        
        // Вывод результатов
        $this->printResults();
    }
    
    private function testWarehousesApi() {
        echo "🏢 ТЕСТ 1: API получения складов\n";
        echo "----------------------------------------\n";
        
        $response = $this->makeApiRequest('/warehouses');
        
        if ($response['status'] === 200) {
            echo "✅ Статус: 200 OK\n";
            if (isset($response['data']['data'])) {
                $warehouses = $response['data']['data'];
                echo "📊 Найдено складов: " . count($warehouses) . "\n";
                
                if (count($warehouses) > 0) {
                    echo "📋 Примеры складов:\n";
                    foreach (array_slice($warehouses, 0, 3) as $warehouse) {
                        echo "  - ID: {$warehouse['id']}, Название: {$warehouse['name']}\n";
                    }
                }
            }
            $this->testResults['warehouses_api'] = 'PASS';
        } else {
            echo "❌ Статус: {$response['status']}\n";
            echo "❌ Ответ: " . $response['raw'] . "\n";
            $this->testResults['warehouses_api'] = 'FAIL';
        }
        echo "\n";
    }
    
    private function testCategoriesApi() {
        echo "📂 ТЕСТ 2: API получения категорий\n";
        echo "----------------------------------------\n";
        
        $response = $this->makeApiRequest('/categories');
        
        if ($response['status'] === 200) {
            echo "✅ Статус: 200 OK\n";
            if (isset($response['data']['data'])) {
                $categories = $response['data']['data'];
                echo "📊 Найдено категорий: " . count($categories) . "\n";
                
                if (count($categories) > 0) {
                    echo "📋 Примеры категорий:\n";
                    foreach (array_slice($categories, 0, 3) as $category) {
                        echo "  - ID: {$category['id']}, Название: {$category['name']}\n";
                    }
                }
            }
            $this->testResults['categories_api'] = 'PASS';
        } else {
            echo "❌ Статус: {$response['status']}\n";
            echo "❌ Ответ: " . $response['raw'] . "\n";
            $this->testResults['categories_api'] = 'FAIL';
        }
        echo "\n";
    }
    
    private function testSubcategoriesApi() {
        echo "📁 ТЕСТ 3: API получения подкатегорий\n";
        echo "----------------------------------------\n";
        
        // Сначала получаем категории для тестирования
        $categoriesResponse = $this->makeApiRequest('/categories');
        if ($categoriesResponse['status'] === 200 && isset($categoriesResponse['data']['data'])) {
            $categories = $categoriesResponse['data']['data'];
            if (count($categories) > 0) {
                $firstCategory = $categories[0];
                $categoryId = $firstCategory['id'];
                
                $response = $this->makeApiRequest("/subcategories?category_id={$categoryId}");
                
                if ($response['status'] === 200) {
                    echo "✅ Статус: 200 OK\n";
                    if (isset($response['data']['data'])) {
                        $subcategories = $response['data']['data'];
                        echo "📊 Найдено подкатегорий для категории '{$firstCategory['name']}': " . count($subcategories) . "\n";
                        
                        if (count($subcategories) > 0) {
                            echo "📋 Примеры подкатегорий:\n";
                            foreach (array_slice($subcategories, 0, 3) as $subcategory) {
                                echo "  - ID: {$subcategory['id']}, Название: {$subcategory['name']}\n";
                            }
                        }
                    }
                    $this->testResults['subcategories_api'] = 'PASS';
                } else {
                    echo "❌ Статус: {$response['status']}\n";
                    echo "❌ Ответ: " . $response['raw'] . "\n";
                    $this->testResults['subcategories_api'] = 'FAIL';
                }
            } else {
                echo "⚠️  Нет категорий для тестирования подкатегорий\n";
                $this->testResults['subcategories_api'] = 'SKIP';
            }
        } else {
            echo "❌ Не удалось получить категории для тестирования подкатегорий\n";
            $this->testResults['subcategories_api'] = 'FAIL';
        }
        echo "\n";
    }
    
    private function testProductFieldsApi() {
        echo "🔧 ТЕСТ 4: API получения настроек полей товара\n";
        echo "----------------------------------------\n";
        
        $response = $this->makeApiRequest('/product-fields');
        
        if ($response['status'] === 200) {
            echo "✅ Статус: 200 OK\n";
            if (isset($response['data']['data'])) {
                $fields = $response['data']['data'];
                echo "📊 Найдено полей: " . count($fields) . "\n";
                
                if (count($fields) > 0) {
                    echo "📋 Примеры полей:\n";
                    foreach (array_slice($fields, 0, 3) as $field) {
                        echo "  - ID: {$field['id']}, Название: {$field['field_name']}\n";
                    }
                }
            }
            $this->testResults['product_fields_api'] = 'PASS';
        } else {
            echo "❌ Статус: {$response['status']}\n";
            echo "❌ Ответ: " . $response['raw'] . "\n";
            $this->testResults['product_fields_api'] = 'FAIL';
        }
        echo "\n";
    }
    
    private function testBalancesBasicApi() {
        echo "📊 ТЕСТ 5: Базовый API получения остатков\n";
        echo "----------------------------------------\n";
        
        $response = $this->makeApiRequest('/balances');
        
        if ($response['status'] === 200) {
            echo "✅ Статус: 200 OK\n";
            if (isset($response['data']['data'])) {
                $balances = $response['data']['data'];
                echo "📊 Найдено остатков: " . count($balances) . "\n";
                
                if (count($balances) > 0) {
                    echo "📋 Примеры остатков:\n";
                    foreach (array_slice($balances, 0, 2) as $balance) {
                        $productName = $balance['product']['name'] ?? 'Неизвестный товар';
                        $quantity = $balance['quantity'] ?? 0;
                        echo "  - {$productName}: {$quantity} шт.\n";
                    }
                }
            }
            $this->testResults['balances_basic_api'] = 'PASS';
        } else {
            echo "❌ Статус: {$response['status']}\n";
            echo "❌ Ответ: " . $response['raw'] . "\n";
            $this->testResults['balances_basic_api'] = 'FAIL';
        }
        echo "\n";
    }
    
    private function testBalancesWarehouseFilter() {
        echo "🏢 ТЕСТ 6: Фильтр остатков по складу\n";
        echo "----------------------------------------\n";
        
        $filterData = [
            'warehouse_id' => 1, // Тестируем с первым складом
            'page' => 1
        ];
        
        $response = $this->makeApiRequest('/balances', 'POST', $filterData);
        
        if ($response['status'] === 200) {
            echo "✅ Статус: 200 OK\n";
            if (isset($response['data']['data'])) {
                $balances = $response['data']['data'];
                echo "📊 Найдено остатков для склада ID=1: " . count($balances) . "\n";
                
                if (count($balances) > 0) {
                    echo "📋 Примеры:\n";
                    foreach (array_slice($balances, 0, 2) as $balance) {
                        $productName = $balance['product']['name'] ?? 'Неизвестный товар';
                        $quantity = $balance['quantity'] ?? 0;
                        echo "  - {$productName}: {$quantity} шт.\n";
                    }
                }
            }
            $this->testResults['balances_warehouse_filter'] = 'PASS';
        } else {
            echo "❌ Статус: {$response['status']}\n";
            echo "❌ Ответ: " . $response['raw'] . "\n";
            $this->testResults['balances_warehouse_filter'] = 'FAIL';
        }
        echo "\n";
    }
    
    private function testBalancesSearchFilter() {
        echo "🔍 ТЕСТ 7: Фильтр остатков по поиску\n";
        echo "----------------------------------------\n";
        
        $filterData = [
            'search' => 'тест',
            'page' => 1
        ];
        
        $response = $this->makeApiRequest('/balances', 'POST', $filterData);
        
        if ($response['status'] === 200) {
            echo "✅ Статус: 200 OK\n";
            if (isset($response['data']['data'])) {
                $balances = $response['data']['data'];
                echo "📊 Найдено остатков по поиску 'тест': " . count($balances) . "\n";
                
                if (count($balances) > 0) {
                    echo "📋 Примеры:\n";
                    foreach (array_slice($balances, 0, 2) as $balance) {
                        $productName = $balance['product']['name'] ?? 'Неизвестный товар';
                        $quantity = $balance['quantity'] ?? 0;
                        echo "  - {$productName}: {$quantity} шт.\n";
                    }
                }
            }
            $this->testResults['balances_search_filter'] = 'PASS';
        } else {
            echo "❌ Статус: {$response['status']}\n";
            echo "❌ Ответ: " . $response['raw'] . "\n";
            $this->testResults['balances_search_filter'] = 'FAIL';
        }
        echo "\n";
    }
    
    private function testBalancesQuantityFilter() {
        echo "📈 ТЕСТ 8: Фильтр остатков по количеству\n";
        echo "----------------------------------------\n";
        
        $filterData = [
            'min_quantity' => 5,
            'max_quantity' => 100,
            'page' => 1
        ];
        
        $response = $this->makeApiRequest('/balances', 'POST', $filterData);
        
        if ($response['status'] === 200) {
            echo "✅ Статус: 200 OK\n";
            if (isset($response['data']['data'])) {
                $balances = $response['data']['data'];
                echo "📊 Найдено остатков (5 <= количество <= 100): " . count($balances) . "\n";
                
                if (count($balances) > 0) {
                    echo "📋 Примеры:\n";
                    foreach (array_slice($balances, 0, 2) as $balance) {
                        $productName = $balance['product']['name'] ?? 'Неизвестный товар';
                        $quantity = $balance['quantity'] ?? 0;
                        echo "  - {$productName}: {$quantity} шт.\n";
                    }
                }
            }
            $this->testResults['balances_quantity_filter'] = 'PASS';
        } else {
            echo "❌ Статус: {$response['status']}\n";
            echo "❌ Ответ: " . $response['raw'] . "\n";
            $this->testResults['balances_quantity_filter'] = 'FAIL';
        }
        echo "\n";
    }
    
    private function testBalancesCategoryFilter() {
        echo "📂 ТЕСТ 9: Фильтр остатков по категории\n";
        echo "----------------------------------------\n";
        
        $filterData = [
            'category' => 'krasota-i-lichnaya-gigiena',
            'page' => 1
        ];
        
        $response = $this->makeApiRequest('/balances', 'POST', $filterData);
        
        if ($response['status'] === 200) {
            echo "✅ Статус: 200 OK\n";
            if (isset($response['data']['data'])) {
                $balances = $response['data']['data'];
                echo "📊 Найдено остатков для категории 'krasota-i-lichnaya-gigiena': " . count($balances) . "\n";
                
                if (count($balances) > 0) {
                    echo "📋 Примеры:\n";
                    foreach (array_slice($balances, 0, 2) as $balance) {
                        $productName = $balance['product']['name'] ?? 'Неизвестный товар';
                        $quantity = $balance['quantity'] ?? 0;
                        echo "  - {$productName}: {$quantity} шт.\n";
                    }
                }
            }
            $this->testResults['balances_category_filter'] = 'PASS';
        } else {
            echo "❌ Статус: {$response['status']}\n";
            echo "❌ Ответ: " . $response['raw'] . "\n";
            $this->testResults['balances_category_filter'] = 'FAIL';
        }
        echo "\n";
    }
    
    private function testBalancesDateFilter() {
        echo "📅 ТЕСТ 10: Фильтр остатков по дате создания\n";
        echo "----------------------------------------\n";
        
        $filterData = [
            'created_at' => '2025-07-28',
            'page' => 1
        ];
        
        $response = $this->makeApiRequest('/balances', 'POST', $filterData);
        
        if ($response['status'] === 200) {
            echo "✅ Статус: 200 OK\n";
            if (isset($response['data']['data'])) {
                $balances = $response['data']['data'];
                echo "📊 Найдено остатков для даты '2025-07-28': " . count($balances) . "\n";
                
                if (count($balances) > 0) {
                    echo "📋 Примеры:\n";
                    foreach (array_slice($balances, 0, 2) as $balance) {
                        $productName = $balance['product']['name'] ?? 'Неизвестный товар';
                        $quantity = $balance['quantity'] ?? 0;
                        echo "  - {$productName}: {$quantity} шт.\n";
                    }
                }
            }
            $this->testResults['balances_date_filter'] = 'PASS';
        } else {
            echo "❌ Статус: {$response['status']}\n";
            echo "❌ Ответ: " . $response['raw'] . "\n";
            $this->testResults['balances_date_filter'] = 'FAIL';
        }
        echo "\n";
    }
    
    private function testBalancesCombinedFilter() {
        echo "🎯 ТЕСТ 11: Комбинированный фильтр остатков\n";
        echo "----------------------------------------\n";
        
        $filterData = [
            'warehouse_id' => 1,
            'search' => 'тест',
            'min_quantity' => 1,
            'page' => 1
        ];
        
        $response = $this->makeApiRequest('/balances', 'POST', $filterData);
        
        if ($response['status'] === 200) {
            echo "✅ Статус: 200 OK\n";
            if (isset($response['data']['data'])) {
                $balances = $response['data']['data'];
                echo "📊 Найдено остатков (склад=1, поиск='тест', мин.количество=1): " . count($balances) . "\n";
                
                if (count($balances) > 0) {
                    echo "📋 Примеры:\n";
                    foreach (array_slice($balances, 0, 2) as $balance) {
                        $productName = $balance['product']['name'] ?? 'Неизвестный товар';
                        $quantity = $balance['quantity'] ?? 0;
                        echo "  - {$productName}: {$quantity} шт.\n";
                    }
                }
            }
            $this->testResults['balances_combined_filter'] = 'PASS';
        } else {
            echo "❌ Статус: {$response['status']}\n";
            echo "❌ Ответ: " . $response['raw'] . "\n";
            $this->testResults['balances_combined_filter'] = 'FAIL';
        }
        echo "\n";
    }
    
    private function testBalancesSummaryApi() {
        echo "📊 ТЕСТ 12: API сводки по остаткам\n";
        echo "----------------------------------------\n";
        
        $response = $this->makeApiRequest('/balances/summary', 'POST', []);
        
        if ($response['status'] === 200) {
            echo "✅ Статус: 200 OK\n";
            if (isset($response['data']['summary'])) {
                $summary = $response['data']['summary'];
                echo "📊 Сводка:\n";
                echo "  - Всего товаров: {$summary['total_products']}\n";
                echo "  - Всего складов: {$summary['total_warehouses']}\n";
                echo "  - Общее количество: {$summary['total_quantity']}\n";
                echo "  - Общая стоимость: {$summary['total_value']}\n";
                echo "  - Товары с низким остатком: {$summary['low_stock_items']}\n";
                echo "  - Товары без остатка: {$summary['out_of_stock_items']}\n";
            }
            $this->testResults['balances_summary_api'] = 'PASS';
        } else {
            echo "❌ Статус: {$response['status']}\n";
            echo "❌ Ответ: " . $response['raw'] . "\n";
            $this->testResults['balances_summary_api'] = 'FAIL';
        }
        echo "\n";
    }
    
    private function printResults() {
        echo "📊 РЕЗУЛЬТАТЫ ТЕСТИРОВАНИЯ API\n";
        echo "================================================\n";
        
        $passed = 0;
        $failed = 0;
        $skipped = 0;
        
        foreach ($this->testResults as $test => $result) {
            $status = $result === 'PASS' ? '✅' : ($result === 'SKIP' ? '⚠️' : '❌');
            echo "{$status} {$test}: {$result}\n";
            
            if ($result === 'PASS') {
                $passed++;
            } elseif ($result === 'SKIP') {
                $skipped++;
            } else {
                $failed++;
            }
        }
        
        echo "\n📈 ИТОГО:\n";
        echo "✅ Успешно: {$passed}\n";
        echo "⚠️  Пропущено: {$skipped}\n";
        echo "❌ Ошибок: {$failed}\n";
        echo "📊 Всего тестов: " . count($this->testResults) . "\n";
        
        if ($failed === 0) {
            echo "\n🎉 ВСЕ API ТЕСТЫ ПРОЙДЕНЫ УСПЕШНО!\n";
        } else {
            echo "\n⚠️  ЕСТЬ ОШИБКИ В API ТЕСТАХ\n";
        }
    }
}

// Запуск тестов
echo "🔧 ТЕСТИРОВАНИЕ API ФИЛЬТРА СТРАНИЦЫ 'ОСТАТКИ'\n";
echo "================================================\n";
echo "⚠️  ВНИМАНИЕ: Для корректной работы тестов необходимо:\n";
echo "   1. Запустить Laravel сервер (php artisan serve)\n";
echo "   2. Установить правильный токен авторизации в тесте\n";
echo "   3. Убедиться, что API эндпоинты доступны\n\n";

$test = new BalancesApiTest();
$test->runAllTests();

?> 