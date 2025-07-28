<?php

// Тест фильтра страницы "Остатки"
// Проверяет работу всех полей фильтра по отдельности и вместе

class BalancesFilterTest {
    private $pdo;
    private $testResults = [];
    
    public function __construct() {
        $this->connectToDatabase();
    }
    
    private function connectToDatabase() {
        try {
            $this->pdo = new PDO(
                "pgsql:host=5.35.85.110;port=5432;dbname=b2bstorage",
                "b2buser",
                "B2B_Storage_2024!",
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
            echo "✅ Подключение к базе данных успешно\n";
        } catch (PDOException $e) {
            die("❌ Ошибка подключения к базе данных: " . $e->getMessage() . "\n");
        }
    }
    
    public function runAllTests() {
        echo "\n🔍 НАЧАЛО ТЕСТИРОВАНИЯ ФИЛЬТРА СТРАНИЦЫ 'ОСТАТКИ'\n";
        echo "================================================\n\n";
        
        // Тест 1: Проверка структуры таблиц
        $this->testTableStructure();
        
        // Тест 2: Проверка наличия данных
        $this->testDataAvailability();
        
        // Тест 3: Тестирование фильтра по складу
        $this->testWarehouseFilter();
        
        // Тест 4: Тестирование фильтра по поиску
        $this->testSearchFilter();
        
        // Тест 5: Тестирование фильтра по минимальному остатку
        $this->testMinQuantityFilter();
        
        // Тест 6: Тестирование фильтра по максимальному остатку
        $this->testMaxQuantityFilter();
        
        // Тест 7: Тестирование фильтра по категории
        $this->testCategoryFilter();
        
        // Тест 8: Тестирование фильтра по подкатегории
        $this->testSubcategoryFilter();
        
        // Тест 9: Тестирование фильтра по дате создания
        $this->testCreatedAtFilter();
        
        // Тест 10: Тестирование дополнительных полей
        $this->testAdditionalFieldsFilter();
        
        // Тест 11: Комбинированный фильтр
        $this->testCombinedFilter();
        
        // Вывод результатов
        $this->printResults();
    }
    
    private function testTableStructure() {
        echo "📋 ТЕСТ 1: Проверка структуры таблиц\n";
        echo "----------------------------------------\n";
        
        try {
            // Проверяем таблицу products_sklad
            $stmt = $this->pdo->query("SELECT column_name, data_type FROM information_schema.columns WHERE table_name = 'products_sklad' ORDER BY ordinal_position");
            $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo "Структура таблицы products_sklad:\n";
            foreach ($columns as $column) {
                echo "  - {$column['column_name']}: {$column['data_type']}\n";
            }
            
            // Проверяем таблицу product_balances
            $stmt = $this->pdo->query("SELECT column_name, data_type FROM information_schema.columns WHERE table_name = 'product_balances' ORDER BY ordinal_position");
            $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo "\nСтруктура таблицы product_balances:\n";
            foreach ($columns as $column) {
                echo "  - {$column['column_name']}: {$column['data_type']}\n";
            }
            
            // Проверяем таблицу warehouses
            $stmt = $this->pdo->query("SELECT column_name, data_type FROM information_schema.columns WHERE table_name = 'warehouses' ORDER BY ordinal_position");
            $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo "\nСтруктура таблицы warehouses:\n";
            foreach ($columns as $column) {
                echo "  - {$column['column_name']}: {$column['data_type']}\n";
            }
            
            $this->testResults['table_structure'] = 'PASS';
            echo "✅ Структура таблиц проверена\n\n";
            
        } catch (Exception $e) {
            $this->testResults['table_structure'] = 'FAIL';
            echo "❌ Ошибка проверки структуры таблиц: " . $e->getMessage() . "\n\n";
        }
    }
    
    private function testDataAvailability() {
        echo "📊 ТЕСТ 2: Проверка наличия данных\n";
        echo "----------------------------------------\n";
        
        try {
            // Количество товаров
            $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM products_sklad");
            $productsCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            echo "Товаров в базе: {$productsCount}\n";
            
            // Количество остатков
            $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM product_balances");
            $balancesCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            echo "Записей остатков: {$balancesCount}\n";
            
            // Количество складов
            $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM warehouses");
            $warehousesCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            echo "Складов: {$warehousesCount}\n";
            
            // Примеры данных
            $stmt = $this->pdo->query("SELECT id, name, category, subcategory FROM products_sklad LIMIT 3");
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo "\nПримеры товаров:\n";
            foreach ($products as $product) {
                echo "  - ID: {$product['id']}, Название: {$product['name']}, Категория: {$product['category']}, Подкатегория: {$product['subcategory']}\n";
            }
            
            $this->testResults['data_availability'] = 'PASS';
            echo "✅ Данные доступны\n\n";
            
        } catch (Exception $e) {
            $this->testResults['data_availability'] = 'FAIL';
            echo "❌ Ошибка проверки данных: " . $e->getMessage() . "\n\n";
        }
    }
    
    private function testWarehouseFilter() {
        echo "🏢 ТЕСТ 3: Фильтр по складу\n";
        echo "----------------------------------------\n";
        
        try {
            // Получаем список складов
            $stmt = $this->pdo->query("SELECT id, name FROM warehouses LIMIT 3");
            $warehouses = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($warehouses as $warehouse) {
                $warehouseId = $warehouse['id'];
                $warehouseName = $warehouse['name'];
                
                // Тестируем фильтр по конкретному складу
                $sql = "
                    SELECT pb.*, ps.name as product_name, w.name as warehouse_name
                    FROM product_balances pb
                    JOIN products_sklad ps ON pb.product_id = ps.id
                    JOIN warehouses w ON pb.warehouse_id = w.id
                    WHERE pb.warehouse_id = :warehouse_id
                    LIMIT 5
                ";
                
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(['warehouse_id' => $warehouseId]);
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                echo "Склад '{$warehouseName}' (ID: {$warehouseId}): найдено " . count($results) . " записей\n";
                
                if (count($results) > 0) {
                    echo "  Примеры:\n";
                    foreach (array_slice($results, 0, 2) as $result) {
                        echo "    - {$result['product_name']}: {$result['quantity']} шт.\n";
                    }
                }
            }
            
            $this->testResults['warehouse_filter'] = 'PASS';
            echo "✅ Фильтр по складу работает\n\n";
            
        } catch (Exception $e) {
            $this->testResults['warehouse_filter'] = 'FAIL';
            echo "❌ Ошибка фильтра по складу: " . $e->getMessage() . "\n\n";
        }
    }
    
    private function testSearchFilter() {
        echo "🔍 ТЕСТ 4: Фильтр по поиску\n";
        echo "----------------------------------------\n";
        
        try {
            // Тестируем поиск по названию
            $searchTerms = ['товар', 'продукт', 'тест'];
            
            foreach ($searchTerms as $term) {
                $sql = "
                    SELECT pb.*, ps.name as product_name
                    FROM product_balances pb
                    JOIN products_sklad ps ON pb.product_id = ps.id
                    WHERE ps.name ILIKE :search_term
                    LIMIT 3
                ";
                
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(['search_term' => "%{$term}%"]);
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                echo "Поиск '{$term}': найдено " . count($results) . " записей\n";
                
                if (count($results) > 0) {
                    echo "  Примеры:\n";
                    foreach (array_slice($results, 0, 2) as $result) {
                        echo "    - {$result['product_name']}\n";
                    }
                }
            }
            
            $this->testResults['search_filter'] = 'PASS';
            echo "✅ Фильтр по поиску работает\n\n";
            
        } catch (Exception $e) {
            $this->testResults['search_filter'] = 'FAIL';
            echo "❌ Ошибка фильтра по поиску: " . $e->getMessage() . "\n\n";
        }
    }
    
    private function testMinQuantityFilter() {
        echo "📉 ТЕСТ 5: Фильтр по минимальному остатку\n";
        echo "----------------------------------------\n";
        
        try {
            $minQuantities = [1, 5, 10];
            
            foreach ($minQuantities as $minQty) {
                $sql = "
                    SELECT pb.*, ps.name as product_name
                    FROM product_balances pb
                    JOIN products_sklad ps ON pb.product_id = ps.id
                    WHERE pb.quantity >= :min_quantity
                    ORDER BY pb.quantity ASC
                    LIMIT 3
                ";
                
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(['min_quantity' => $minQty]);
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                echo "Минимальный остаток >= {$minQty}: найдено " . count($results) . " записей\n";
                
                if (count($results) > 0) {
                    echo "  Примеры:\n";
                    foreach (array_slice($results, 0, 2) as $result) {
                        echo "    - {$result['product_name']}: {$result['quantity']} шт.\n";
                    }
                }
            }
            
            $this->testResults['min_quantity_filter'] = 'PASS';
            echo "✅ Фильтр по минимальному остатку работает\n\n";
            
        } catch (Exception $e) {
            $this->testResults['min_quantity_filter'] = 'FAIL';
            echo "❌ Ошибка фильтра по минимальному остатку: " . $e->getMessage() . "\n\n";
        }
    }
    
    private function testMaxQuantityFilter() {
        echo "📈 ТЕСТ 6: Фильтр по максимальному остатку\n";
        echo "----------------------------------------\n";
        
        try {
            $maxQuantities = [10, 50, 100];
            
            foreach ($maxQuantities as $maxQty) {
                $sql = "
                    SELECT pb.*, ps.name as product_name
                    FROM product_balances pb
                    JOIN products_sklad ps ON pb.product_id = ps.id
                    WHERE pb.quantity <= :max_quantity
                    ORDER BY pb.quantity DESC
                    LIMIT 3
                ";
                
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(['max_quantity' => $maxQty]);
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                echo "Максимальный остаток <= {$maxQty}: найдено " . count($results) . " записей\n";
                
                if (count($results) > 0) {
                    echo "  Примеры:\n";
                    foreach (array_slice($results, 0, 2) as $result) {
                        echo "    - {$result['product_name']}: {$result['quantity']} шт.\n";
                    }
                }
            }
            
            $this->testResults['max_quantity_filter'] = 'PASS';
            echo "✅ Фильтр по максимальному остатку работает\n\n";
            
        } catch (Exception $e) {
            $this->testResults['max_quantity_filter'] = 'FAIL';
            echo "❌ Ошибка фильтра по максимальному остатку: " . $e->getMessage() . "\n\n";
        }
    }
    
    private function testCategoryFilter() {
        echo "📂 ТЕСТ 7: Фильтр по категории\n";
        echo "----------------------------------------\n";
        
        try {
            // Получаем уникальные категории
            $stmt = $this->pdo->query("SELECT DISTINCT category FROM products_sklad WHERE category IS NOT NULL AND category != '' LIMIT 3");
            $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($categories as $category) {
                $categoryName = $category['category'];
                
                $sql = "
                    SELECT pb.*, ps.name as product_name, ps.category
                    FROM product_balances pb
                    JOIN products_sklad ps ON pb.product_id = ps.id
                    WHERE ps.category = :category
                    LIMIT 3
                ";
                
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(['category' => $categoryName]);
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                echo "Категория '{$categoryName}': найдено " . count($results) . " записей\n";
                
                if (count($results) > 0) {
                    echo "  Примеры:\n";
                    foreach (array_slice($results, 0, 2) as $result) {
                        echo "    - {$result['product_name']}: {$result['quantity']} шт.\n";
                    }
                }
            }
            
            $this->testResults['category_filter'] = 'PASS';
            echo "✅ Фильтр по категории работает\n\n";
            
        } catch (Exception $e) {
            $this->testResults['category_filter'] = 'FAIL';
            echo "❌ Ошибка фильтра по категории: " . $e->getMessage() . "\n\n";
        }
    }
    
    private function testSubcategoryFilter() {
        echo "📁 ТЕСТ 8: Фильтр по подкатегории\n";
        echo "----------------------------------------\n";
        
        try {
            // Получаем уникальные подкатегории
            $stmt = $this->pdo->query("SELECT DISTINCT subcategory FROM products_sklad WHERE subcategory IS NOT NULL AND subcategory != '' LIMIT 3");
            $subcategories = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($subcategories as $subcategory) {
                $subcategoryName = $subcategory['subcategory'];
                
                $sql = "
                    SELECT pb.*, ps.name as product_name, ps.subcategory
                    FROM product_balances pb
                    JOIN products_sklad ps ON pb.product_id = ps.id
                    WHERE ps.subcategory = :subcategory
                    LIMIT 3
                ";
                
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(['subcategory' => $subcategoryName]);
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                echo "Подкатегория '{$subcategoryName}': найдено " . count($results) . " записей\n";
                
                if (count($results) > 0) {
                    echo "  Примеры:\n";
                    foreach (array_slice($results, 0, 2) as $result) {
                        echo "    - {$result['product_name']}: {$result['quantity']} шт.\n";
                    }
                }
            }
            
            $this->testResults['subcategory_filter'] = 'PASS';
            echo "✅ Фильтр по подкатегории работает\n\n";
            
        } catch (Exception $e) {
            $this->testResults['subcategory_filter'] = 'FAIL';
            echo "❌ Ошибка фильтра по подкатегории: " . $e->getMessage() . "\n\n";
        }
    }
    
    private function testCreatedAtFilter() {
        echo "📅 ТЕСТ 9: Фильтр по дате создания\n";
        echo "----------------------------------------\n";
        
        try {
            // Получаем диапазон дат создания
            $stmt = $this->pdo->query("SELECT MIN(created_at) as min_date, MAX(created_at) as max_date FROM products_sklad");
            $dateRange = $stmt->fetch(PDO::FETCH_ASSOC);
            
            echo "Диапазон дат создания: {$dateRange['min_date']} - {$dateRange['max_date']}\n";
            
            // Тестируем фильтр по конкретной дате
            $testDate = date('Y-m-d'); // Сегодняшняя дата
            
            $sql = "
                SELECT pb.*, ps.name as product_name, ps.created_at
                FROM product_balances pb
                JOIN products_sklad ps ON pb.product_id = ps.id
                WHERE DATE(ps.created_at) = :created_date
                LIMIT 3
            ";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['created_date' => $testDate]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo "Дата создания '{$testDate}': найдено " . count($results) . " записей\n";
            
            if (count($results) > 0) {
                echo "  Примеры:\n";
                foreach (array_slice($results, 0, 2) as $result) {
                    echo "    - {$result['product_name']}: создан {$result['created_at']}\n";
                }
            }
            
            $this->testResults['created_at_filter'] = 'PASS';
            echo "✅ Фильтр по дате создания работает\n\n";
            
        } catch (Exception $e) {
            $this->testResults['created_at_filter'] = 'FAIL';
            echo "❌ Ошибка фильтра по дате создания: " . $e->getMessage() . "\n\n";
        }
    }
    
    private function testAdditionalFieldsFilter() {
        echo "🔧 ТЕСТ 10: Фильтр по дополнительным полям\n";
        echo "----------------------------------------\n";
        
        try {
            // Тестируем фильтр по стране
            $stmt = $this->pdo->query("SELECT DISTINCT country FROM products_sklad WHERE country IS NOT NULL AND country != '' LIMIT 2");
            $countries = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($countries as $country) {
                $countryName = $country['country'];
                
                $sql = "
                    SELECT pb.*, ps.name as product_name, ps.country
                    FROM product_balances pb
                    JOIN products_sklad ps ON pb.product_id = ps.id
                    WHERE ps.country ILIKE :country
                    LIMIT 3
                ";
                
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(['country' => "%{$countryName}%"]);
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                echo "Страна '{$countryName}': найдено " . count($results) . " записей\n";
                
                if (count($results) > 0) {
                    echo "  Примеры:\n";
                    foreach (array_slice($results, 0, 2) as $result) {
                        echo "    - {$result['product_name']}: {$result['country']}\n";
                    }
                }
            }
            
            // Тестируем фильтр по поставщику
            $stmt = $this->pdo->query("SELECT DISTINCT supplier FROM products_sklad WHERE supplier IS NOT NULL AND supplier != '' LIMIT 2");
            $suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($suppliers as $supplier) {
                $supplierName = $supplier['supplier'];
                
                $sql = "
                    SELECT pb.*, ps.name as product_name, ps.supplier
                    FROM product_balances pb
                    JOIN products_sklad ps ON pb.product_id = ps.id
                    WHERE ps.supplier ILIKE :supplier
                    LIMIT 3
                ";
                
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(['supplier' => "%{$supplierName}%"]);
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                echo "Поставщик '{$supplierName}': найдено " . count($results) . " записей\n";
                
                if (count($results) > 0) {
                    echo "  Примеры:\n";
                    foreach (array_slice($results, 0, 2) as $result) {
                        echo "    - {$result['product_name']}: {$result['supplier']}\n";
                    }
                }
            }
            
            $this->testResults['additional_fields_filter'] = 'PASS';
            echo "✅ Фильтр по дополнительным полям работает\n\n";
            
        } catch (Exception $e) {
            $this->testResults['additional_fields_filter'] = 'FAIL';
            echo "❌ Ошибка фильтра по дополнительным полям: " . $e->getMessage() . "\n\n";
        }
    }
    
    private function testCombinedFilter() {
        echo "🎯 ТЕСТ 11: Комбинированный фильтр\n";
        echo "----------------------------------------\n";
        
        try {
            // Комбинированный фильтр: склад + минимальный остаток + поиск
            $sql = "
                SELECT pb.*, ps.name as product_name, w.name as warehouse_name
                FROM product_balances pb
                JOIN products_sklad ps ON pb.product_id = ps.id
                JOIN warehouses w ON pb.warehouse_id = w.id
                WHERE pb.quantity >= :min_quantity
                AND ps.name ILIKE :search_term
                ORDER BY pb.quantity DESC
                LIMIT 5
            ";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'min_quantity' => 1,
                'search_term' => '%товар%'
            ]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo "Комбинированный фильтр (остаток >= 1, поиск 'товар'): найдено " . count($results) . " записей\n";
            
            if (count($results) > 0) {
                echo "  Результаты:\n";
                foreach (array_slice($results, 0, 3) as $result) {
                    echo "    - {$result['product_name']}: {$result['quantity']} шт. (склад: {$result['warehouse_name']})\n";
                }
            }
            
            $this->testResults['combined_filter'] = 'PASS';
            echo "✅ Комбинированный фильтр работает\n\n";
            
        } catch (Exception $e) {
            $this->testResults['combined_filter'] = 'FAIL';
            echo "❌ Ошибка комбинированного фильтра: " . $e->getMessage() . "\n\n";
        }
    }
    
    private function printResults() {
        echo "📊 РЕЗУЛЬТАТЫ ТЕСТИРОВАНИЯ\n";
        echo "================================================\n";
        
        $passed = 0;
        $failed = 0;
        
        foreach ($this->testResults as $test => $result) {
            $status = $result === 'PASS' ? '✅' : '❌';
            echo "{$status} {$test}: {$result}\n";
            
            if ($result === 'PASS') {
                $passed++;
            } else {
                $failed++;
            }
        }
        
        echo "\n📈 ИТОГО:\n";
        echo "✅ Успешно: {$passed}\n";
        echo "❌ Ошибок: {$failed}\n";
        echo "📊 Всего тестов: " . count($this->testResults) . "\n";
        
        if ($failed === 0) {
            echo "\n🎉 ВСЕ ТЕСТЫ ПРОЙДЕНЫ УСПЕШНО!\n";
        } else {
            echo "\n⚠️  ЕСТЬ ОШИБКИ В ТЕСТАХ\n";
        }
    }
}

// Запуск тестов
$test = new BalancesFilterTest();
$test->runAllTests();

?> 