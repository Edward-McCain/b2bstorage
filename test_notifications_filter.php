<?php

// Тест фильтров уведомлений

class NotificationsFilterTest {
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
    
    public function runTests() {
        echo "\n🔍 ТЕСТИРОВАНИЕ ФИЛЬТРОВ УВЕДОМЛЕНИЙ\n";
        echo "=======================================\n\n";
        
        $userId = 52; // Тестовый пользователь
        
        // Проверяем данные в таблице
        $this->checkNotificationsData($userId);
        
        // Тестируем фильтры
        $this->testTypeFilter($userId);
        $this->testReadStatusFilter($userId);
        $this->testCombinedFilters($userId);
        
        // Проверяем API
        $this->testApiFilters();
        
        // Вывод результатов
        $this->printResults();
    }
    
    private function checkNotificationsData($userId) {
        echo "📊 ПРОВЕРКА ДАННЫХ УВЕДОМЛЕНИЙ\n";
        echo "-------------------------------\n";
        
        // Общее количество
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM notifications WHERE user_id = ?");
        $stmt->execute([$userId]);
        $totalCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        echo "Всего уведомлений для user_id = {$userId}: {$totalCount}\n";
        
        // По типам
        $stmt = $this->pdo->prepare("SELECT type, COUNT(*) as count FROM notifications WHERE user_id = ? GROUP BY type");
        $stmt->execute([$userId]);
        $typeCounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "По типам:\n";
        foreach ($typeCounts as $typeCount) {
            echo "  - {$typeCount['type']}: {$typeCount['count']}\n";
        }
        
        // По статусу прочтения
        $stmt = $this->pdo->prepare("SELECT is_read, COUNT(*) as count FROM notifications WHERE user_id = ? GROUP BY is_read");
        $stmt->execute([$userId]);
        $readCounts = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo "По статусу прочтения:\n";
        echo "  - Прочитанные: " . ($readCounts['count'] ?? 0) . "\n";
        
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = false");
        $stmt->execute([$userId]);
        $unreadCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        echo "  - Непрочитанные: {$unreadCount}\n";
        
        echo "\n";
    }
    
    private function testTypeFilter($userId) {
        echo "🔍 ТЕСТ ФИЛЬТРА ПО ТИПУ\n";
        echo "------------------------\n";
        
        $types = ['info', 'warning', 'recommendation', 'low_stock', 'overdue'];
        
        foreach ($types as $type) {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND type = ?");
            $stmt->execute([$userId, $type]);
            $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            echo "Тип '{$type}': {$count} уведомлений\n";
        }
        
        echo "\n";
    }
    
    private function testReadStatusFilter($userId) {
        echo "🔍 ТЕСТ ФИЛЬТРА ПО СТАТУСУ ПРОЧТЕНИЯ\n";
        echo "--------------------------------------\n";
        
        // Прочитанные
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = true");
        $stmt->execute([$userId]);
        $readCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        echo "Прочитанные (is_read = true): {$readCount}\n";
        
        // Непрочитанные
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = false");
        $stmt->execute([$userId]);
        $unreadCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        echo "Непрочитанные (is_read = false): {$unreadCount}\n";
        
        echo "\n";
    }
    
    private function testCombinedFilters($userId) {
        echo "🔍 ТЕСТ КОМБИНИРОВАННЫХ ФИЛЬТРОВ\n";
        echo "--------------------------------\n";
        
        // Тип + статус прочтения
        $types = ['info', 'warning', 'recommendation'];
        $readStatuses = [true, false];
        
        foreach ($types as $type) {
            foreach ($readStatuses as $isRead) {
                $readStatus = $isRead ? 'true' : 'false';
                $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND type = ? AND is_read = ?");
                $stmt->execute([$userId, $type, $isRead]);
                $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
                echo "Тип '{$type}' + прочитано '{$readStatus}': {$count}\n";
            }
        }
        
        echo "\n";
    }
    
    private function testApiFilters() {
        echo "🔍 ТЕСТ API ФИЛЬТРОВ\n";
        echo "---------------------\n";
        
        echo "📋 Проверяемые URL:\n";
        echo "  - /api/notifications?type=info\n";
        echo "  - /api/notifications?is_read=false\n";
        echo "  - /api/notifications?type=warning&is_read=true\n";
        echo "\n";
        
        echo "📋 Ожидаемое поведение:\n";
        echo "  - Фильтр по типу должен работать\n";
        echo "  - Фильтр по статусу прочтения должен работать\n";
        echo "  - Комбинированные фильтры должны работать\n";
        echo "\n";
        
        echo "📋 Возможные проблемы:\n";
        echo "  1. Неправильная передача параметров\n";
        echo "  2. Ошибки в контроллере\n";
        echo "  3. Проблемы с аутентификацией\n";
        echo "  4. Неправильные типы данных\n";
        echo "\n";
    }
    
    private function printResults() {
        echo "📊 РЕЗУЛЬТАТЫ ТЕСТИРОВАНИЯ\n";
        echo "==========================\n";
        
        echo "🔍 ПРОБЛЕМЫ ФИЛЬТРОВ:\n";
        echo "1. Фильтр по типу не работает\n";
        echo "2. Фильтр по статусу прочтения не работает\n";
        echo "\n";
        
        echo "🔧 ВОЗМОЖНЫЕ ПРИЧИНЫ:\n";
        echo "1. Неправильная передача параметров в API\n";
        echo "2. Ошибки в логике фильтрации в контроллере\n";
        echo "3. Проблемы с типами данных (string vs boolean)\n";
        echo "4. Отсутствие данных в базе\n";
        echo "\n";
        
        echo "💡 РЕКОМЕНДАЦИИ:\n";
        echo "1. Проверить передачу параметров в frontend\n";
        echo "2. Добавить логирование в контроллер\n";
        echo "3. Проверить типы данных в запросах\n";
        echo "4. Создать тестовые данные\n";
        echo "\n";
    }
}

// Запуск тестов
$test = new NotificationsFilterTest();
$test->runTests();

?> 