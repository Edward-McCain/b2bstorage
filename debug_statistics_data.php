<?php

// Отладка данных статистики

class StatisticsDataDebug {
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
    
    public function debugData() {
        echo "\n🔍 ОТЛАДКА ДАННЫХ СТАТИСТИКИ\n";
        echo "=============================\n\n";
        
        $userId = 52; // Тестовый пользователь
        
        // Проверяем данные в таблицах
        $this->checkAllData($userId);
        
        // Проверяем методы getLatest*Data
        $this->checkLatestDataMethods($userId);
        
        // Проверяем группировку
        $this->checkGrouping($userId);
    }
    
    private function checkAllData($userId) {
        echo "📊 ПРОВЕРКА ВСЕХ ДАННЫХ\n";
        echo "------------------------\n";
        
        // Проверяем receipts
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM receipts WHERE user_id = ?");
        $stmt->execute([$userId]);
        $receiptsCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        echo "receipts для user_id = {$userId}: {$receiptsCount} записей\n";
        
        // Проверяем write_offs
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM write_offs WHERE user_id = ?");
        $stmt->execute([$userId]);
        $writeOffsCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        echo "write_offs для user_id = {$userId}: {$writeOffsCount} записей\n";
        
        // Проверяем product_transfers
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM product_transfers WHERE created_by = ?");
        $stmt->execute([$userId]);
        $transfersCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        echo "product_transfers для created_by = {$userId}: {$transfersCount} записей\n";
        
        echo "\n";
    }
    
    private function checkLatestDataMethods($userId) {
        echo "🔧 ПРОВЕРКА МЕТОДОВ GETLATEST*DATA\n";
        echo "----------------------------------\n";
        
        // Тестируем getLatestReceiptsData
        echo "📋 getLatestReceiptsData:\n";
        $query = "
            SELECT 
                DATE_TRUNC('month', date) as period,
                COUNT(*) as count
            FROM receipts
            WHERE user_id = ?
            GROUP BY period
            ORDER BY period DESC
            LIMIT 12
        ";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$userId]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "Результаты:\n";
        foreach ($results as $result) {
            echo "  - Period: {$result['period']}, Count: {$result['count']}\n";
        }
        echo "\n";
        
        // Тестируем getLatestWriteOffsData
        echo "📋 getLatestWriteOffsData:\n";
        $query = "
            SELECT 
                DATE_TRUNC('month', date) as period,
                COUNT(*) as count
            FROM write_offs
            WHERE user_id = ?
            GROUP BY period
            ORDER BY period DESC
            LIMIT 12
        ";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$userId]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "Результаты:\n";
        foreach ($results as $result) {
            echo "  - Period: {$result['period']}, Count: {$result['count']}\n";
        }
        echo "\n";
        
        // Тестируем getLatestTransfersData
        echo "📋 getLatestTransfersData:\n";
        $query = "
            SELECT 
                DATE_TRUNC('month', created_at) as period,
                COUNT(*) as count
            FROM product_transfers
            WHERE created_by = ?
            GROUP BY period
            ORDER BY period DESC
            LIMIT 12
        ";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$userId]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "Результаты:\n";
        foreach ($results as $result) {
            echo "  - Period: {$result['period']}, Count: {$result['count']}\n";
        }
        echo "\n";
    }
    
    private function checkGrouping($userId) {
        echo "📊 ПРОВЕРКА ГРУППИРОВКИ\n";
        echo "------------------------\n";
        
        $periods = ['day', 'week', 'month'];
        
        foreach ($periods as $groupBy) {
            echo "📋 Группировка по {$groupBy}:\n";
            
            // Receipts
            $query = "
                SELECT 
                    DATE_TRUNC('{$groupBy}', date) as period,
                    COUNT(*) as count
                FROM receipts
                WHERE user_id = ?
                GROUP BY period
                ORDER BY period DESC
                LIMIT 5
            ";
            
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([$userId]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo "  receipts:\n";
            foreach ($results as $result) {
                echo "    - Period: {$result['period']}, Count: {$result['count']}\n";
            }
            
            // Write-offs
            $query = "
                SELECT 
                    DATE_TRUNC('{$groupBy}', date) as period,
                    COUNT(*) as count
                FROM write_offs
                WHERE user_id = ?
                GROUP BY period
                ORDER BY period DESC
                LIMIT 5
            ";
            
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([$userId]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo "  write_offs:\n";
            foreach ($results as $result) {
                echo "    - Period: {$result['period']}, Count: {$result['count']}\n";
            }
            
            // Transfers
            $query = "
                SELECT 
                    DATE_TRUNC('{$groupBy}', created_at) as period,
                    COUNT(*) as count
                FROM product_transfers
                WHERE created_by = ?
                GROUP BY period
                ORDER BY period DESC
                LIMIT 5
            ";
            
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([$userId]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo "  product_transfers:\n";
            foreach ($results as $result) {
                echo "    - Period: {$result['period']}, Count: {$result['count']}\n";
            }
            
            echo "\n";
        }
    }
}

// Запуск отладки
$debug = new StatisticsDataDebug();
$debug->debugData();

?> 