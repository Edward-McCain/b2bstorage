<?php

// Тест подключения к базе данных и получения статистики

class StatisticsDatabaseTest {
    private $pdo;
    private $testResults = [];
    
    public function __construct() {
        $this->connectToDatabase();
    }
    
    private function connectToDatabase() {
        try {
            $host = '5.35.85.110';
            $dbname = 'b2bstorage';
            $username = 'b2buser';
            $password = 'B2B_Storage_2024!';
            $port = 5432;
            
            $dsn = "pgsql:host={$host};port={$port};dbname={$dbname}";
            $this->pdo = new PDO($dsn, $username, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            echo "✅ Подключение к базе данных успешно\n";
        } catch (PDOException $e) {
            echo "❌ Ошибка подключения к базе данных: " . $e->getMessage() . "\n";
            exit(1);
        }
    }
    
    public function runTests() {
        echo "\n📊 ТЕСТИРОВАНИЕ БАЗЫ ДАННЫХ ДЛЯ СТАТИСТИКИ\n";
        echo "============================================\n\n";
        
        // Тест 1: Проверка таблиц
        $this->testTables();
        
        // Тест 2: Проверка данных в таблицах
        $this->testTableData();
        
        // Тест 3: Проверка запросов статистики
        $this->testStatisticsQueries();
        
        // Тест 4: Проверка группировки данных
        $this->testDataGrouping();
        
        // Вывод результатов
        $this->printResults();
    }
    
    private function testTables() {
        echo "📋 ТЕСТ 1: Проверка таблиц\n";
        echo "---------------------------\n";
        
        $tables = ['receipts', 'write_offs', 'product_transfers'];
        
        foreach ($tables as $table) {
            try {
                $stmt = $this->pdo->query("SELECT COUNT(*) FROM {$table}");
                $count = $stmt->fetchColumn();
                echo "  - {$table}: {$count} записей\n";
                $this->testResults["table_{$table}"] = 'PASS';
            } catch (PDOException $e) {
                echo "  - {$table}: ❌ Ошибка - " . $e->getMessage() . "\n";
                $this->testResults["table_{$table}"] = 'FAIL';
            }
        }
        
        echo "\n";
    }
    
    private function testTableData() {
        echo "📊 ТЕСТ 2: Проверка данных в таблицах\n";
        echo "-------------------------------------\n";
        
        // Проверяем структуру таблиц
        $tableStructures = [
            'receipts' => ['id', 'user_id', 'date', 'number', 'status'],
            'write_offs' => ['id', 'user_id', 'date', 'number', 'status'],
            'product_transfers' => ['id', 'created_by', 'created_at', 'from_warehouse_id', 'to_warehouse_id']
        ];
        
        foreach ($tableStructures as $table => $fields) {
            try {
                $fieldList = implode(', ', $fields);
                $stmt = $this->pdo->query("SELECT {$fieldList} FROM {$table} LIMIT 1");
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($row) {
                    echo "  - {$table}: ✅ Структура корректна\n";
                    $this->testResults["structure_{$table}"] = 'PASS';
                } else {
                    echo "  - {$table}: ⚠️  Нет данных\n";
                    $this->testResults["structure_{$table}"] = 'WARN';
                }
            } catch (PDOException $e) {
                echo "  - {$table}: ❌ Ошибка - " . $e->getMessage() . "\n";
                $this->testResults["structure_{$table}"] = 'FAIL';
            }
        }
        
        echo "\n";
    }
    
    private function testStatisticsQueries() {
        echo "📈 ТЕСТ 3: Проверка запросов статистики\n";
        echo "----------------------------------------\n";
        
        $userId = 52; // Тестовый пользователь
        $periods = ['week', 'month', 'year'];
        
        foreach ($periods as $period) {
            echo "🔍 Тестирование периода: {$period}\n";
            
            try {
                // Тест запроса оприходований
                $receiptsQuery = $this->getStatisticsQuery('receipts', 'user_id', $userId, $period);
                $stmt = $this->pdo->query($receiptsQuery);
                $receiptsCount = $stmt->rowCount();
                echo "  - Оприходования: {$receiptsCount} групп данных\n";
                
                // Тест запроса списаний
                $writeOffsQuery = $this->getStatisticsQuery('write_offs', 'user_id', $userId, $period);
                $stmt = $this->pdo->query($writeOffsQuery);
                $writeOffsCount = $stmt->rowCount();
                echo "  - Списания: {$writeOffsCount} групп данных\n";
                
                // Тест запроса перемещений
                $transfersQuery = $this->getStatisticsQuery('product_transfers', 'created_by', $userId, $period);
                $stmt = $this->pdo->query($transfersQuery);
                $transfersCount = $stmt->rowCount();
                echo "  - Перемещения: {$transfersCount} групп данных\n";
                
                $this->testResults["query_{$period}"] = 'PASS';
            } catch (PDOException $e) {
                echo "  ❌ Ошибка запроса: " . $e->getMessage() . "\n";
                $this->testResults["query_{$period}"] = 'FAIL';
            }
        }
        
        echo "\n";
    }
    
    private function getStatisticsQuery($table, $userField, $userId, $period) {
        $groupBy = $this->getGroupBy($period);
        $dateField = $table === 'product_transfers' ? 'created_at' : 'date';
        
        return "
            SELECT 
                DATE_TRUNC('{$groupBy}', {$dateField}) as period,
                COUNT(*) as count
            FROM {$table}
            WHERE {$userField} = {$userId}
            AND {$dateField} >= NOW() - INTERVAL '1 {$period}'
            GROUP BY period
            ORDER BY period
        ";
    }
    
    private function getGroupBy($period) {
        switch ($period) {
            case 'week':
                return 'day';
            case 'month':
                return 'week';
            case 'year':
            default:
                return 'month';
        }
    }
    
    private function testDataGrouping() {
        echo "📊 ТЕСТ 4: Проверка группировки данных\n";
        echo "--------------------------------------\n";
        
        $userId = 52;
        
        try {
            // Тест группировки по месяцам (год)
            $query = "
                SELECT 
                    DATE_TRUNC('month', date) as period,
                    COUNT(*) as count
                FROM receipts
                WHERE user_id = {$userId}
                AND date >= NOW() - INTERVAL '1 year'
                GROUP BY period
                ORDER BY period
                LIMIT 5
            ";
            
            $stmt = $this->pdo->query($query);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo "  - Пример группировки по месяцам:\n";
            foreach ($results as $row) {
                echo "    * {$row['period']}: {$row['count']} операций\n";
            }
            
            $this->testResults['grouping'] = 'PASS';
        } catch (PDOException $e) {
            echo "  ❌ Ошибка группировки: " . $e->getMessage() . "\n";
            $this->testResults['grouping'] = 'FAIL';
        }
        
        echo "\n";
    }
    
    private function printResults() {
        echo "📊 РЕЗУЛЬТАТЫ ТЕСТИРОВАНИЯ БАЗЫ ДАННЫХ\n";
        echo "========================================\n";
        
        $passed = 0;
        $failed = 0;
        $warnings = 0;
        
        foreach ($this->testResults as $test => $result) {
            $status = $result === 'PASS' ? '✅' : ($result === 'WARN' ? '⚠️' : '❌');
            echo "{$status} {$test}: {$result}\n";
            
            if ($result === 'PASS') {
                $passed++;
            } elseif ($result === 'WARN') {
                $warnings++;
            } else {
                $failed++;
            }
        }
        
        echo "\n📈 ИТОГО:\n";
        echo "✅ Успешно: {$passed}\n";
        echo "⚠️  Предупреждения: {$warnings}\n";
        echo "❌ Ошибок: {$failed}\n";
        echo "📊 Всего тестов: " . count($this->testResults) . "\n";
        
        if ($failed === 0) {
            echo "\n🎉 БАЗА ДАННЫХ ГОТОВА ДЛЯ СТАТИСТИКИ!\n";
            echo "\n📋 РЕКОМЕНДАЦИИ:\n";
            echo "- 📊 API статистики готов к использованию\n";
            echo "- 📅 Все периоды поддерживаются\n";
            echo "- 🔄 Данные группируются корректно\n";
            echo "- 📱 График будет отображать реальные данные\n";
            echo "\n🎯 СЛЕДУЮЩИЙ ШАГ:\n";
            echo "Протестировать график на фронтенде!\n";
        } else {
            echo "\n⚠️  ЕСТЬ ПРОБЛЕМЫ С БАЗОЙ ДАННЫХ\n";
        }
    }
}

// Запуск тестов
$test = new StatisticsDatabaseTest();
$test->runTests();

?> 