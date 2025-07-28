<?php

// Тест для отладки API статистики

class ApiDebugTest {
    private $pdo;
    
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
    
    public function runDebug() {
        echo "\n🔍 ОТЛАДКА API СТАТИСТИКИ\n";
        echo "==========================\n\n";
        
        $userId = 52; // Тестовый пользователь
        
        // Проверяем данные в таблицах
        $this->checkTableData($userId);
        
        // Проверяем запросы контроллера
        $this->checkControllerQueries($userId);
        
        // Проверяем даты
        $this->checkDateRanges();
    }
    
    private function checkTableData($userId) {
        echo "📊 ПРОВЕРКА ДАННЫХ В ТАБЛИЦАХ\n";
        echo "-----------------------------\n";
        
        // Проверяем receipts
        $stmt = $this->pdo->prepare("SELECT id, user_id, date FROM receipts WHERE user_id = ?");
        $stmt->execute([$userId]);
        $receipts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "receipts для user_id = {$userId}:\n";
        foreach ($receipts as $receipt) {
            echo "  - ID: {$receipt['id']}, Date: {$receipt['date']}\n";
        }
        echo "\n";
        
        // Проверяем write_offs
        $stmt = $this->pdo->prepare("SELECT id, user_id, date FROM write_offs WHERE user_id = ?");
        $stmt->execute([$userId]);
        $writeOffs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "write_offs для user_id = {$userId}:\n";
        foreach ($writeOffs as $writeOff) {
            echo "  - ID: {$writeOff['id']}, Date: {$writeOff['date']}\n";
        }
        echo "\n";
        
        // Проверяем product_transfers
        $stmt = $this->pdo->prepare("SELECT id, created_by, created_at FROM product_transfers WHERE created_by = ?");
        $stmt->execute([$userId]);
        $transfers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "product_transfers для created_by = {$userId}:\n";
        foreach ($transfers as $transfer) {
            echo "  - ID: {$transfer['id']}, Created: {$transfer['created_at']}\n";
        }
        echo "\n";
    }
    
    private function checkControllerQueries($userId) {
        echo "🔧 ПРОВЕРКА ЗАПРОСОВ КОНТРОЛЛЕРА\n";
        echo "--------------------------------\n";
        
        $endDate = date('Y-m-d');
        $startDate = date('Y-m-d', strtotime('-1 year'));
        
        echo "Период: {$startDate} - {$endDate}\n\n";
        
        // Тестируем запрос receipts
        $query = "
            SELECT 
                DATE_TRUNC('month', date) as period,
                COUNT(*) as count
            FROM receipts
            WHERE user_id = ?
            AND date BETWEEN ? AND ?
            GROUP BY period
            ORDER BY period
        ";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$userId, $startDate, $endDate]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "Receipts query results:\n";
        foreach ($results as $result) {
            echo "  - Period: {$result['period']}, Count: {$result['count']}\n";
        }
        echo "\n";
        
        // Тестируем запрос write_offs
        $query = "
            SELECT 
                DATE_TRUNC('month', date) as period,
                COUNT(*) as count
            FROM write_offs
            WHERE user_id = ?
            AND date BETWEEN ? AND ?
            GROUP BY period
            ORDER BY period
        ";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$userId, $startDate, $endDate]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "Write_offs query results:\n";
        foreach ($results as $result) {
            echo "  - Period: {$result['period']}, Count: {$result['count']}\n";
        }
        echo "\n";
        
        // Тестируем запрос product_transfers
        $query = "
            SELECT 
                DATE_TRUNC('month', created_at) as period,
                COUNT(*) as count
            FROM product_transfers
            WHERE created_by = ?
            AND created_at BETWEEN ? AND ?
            GROUP BY period
            ORDER BY period
        ";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$userId, $startDate, $endDate]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "Product_transfers query results:\n";
        foreach ($results as $result) {
            echo "  - Period: {$result['period']}, Count: {$result['count']}\n";
        }
        echo "\n";
    }
    
    private function checkDateRanges() {
        echo "📅 ПРОВЕРКА ДИАПАЗОНОВ ДАТ\n";
        echo "----------------------------\n";
        
        echo "Текущая дата: " . date('Y-m-d') . "\n";
        echo "Дата год назад: " . date('Y-m-d', strtotime('-1 year')) . "\n";
        echo "Дата месяц назад: " . date('Y-m-d', strtotime('-1 month')) . "\n";
        echo "Дата неделя назад: " . date('Y-m-d', strtotime('-1 week')) . "\n";
        
        // Проверяем все записи в таблицах
        $tables = ['receipts', 'write_offs', 'product_transfers'];
        
        foreach ($tables as $table) {
            $dateField = $table === 'product_transfers' ? 'created_at' : 'date';
            $stmt = $this->pdo->query("SELECT MIN({$dateField}), MAX({$dateField}) FROM {$table}");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            echo "{$table} - Min: {$result[0]}, Max: {$result[1]}\n";
        }
        
        echo "\n";
    }
}

// Запуск отладки
$debug = new ApiDebugTest();
$debug->runDebug();

?> 