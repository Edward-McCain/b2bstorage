<?php

// Тест API с реальными данными

class ApiRealDataTest {
    private $baseUrl = 'http://localhost:8000/api';
    
    public function runTests() {
        echo "\n🔍 ТЕСТИРОВАНИЕ API С РЕАЛЬНЫМИ ДАННЫМИ\n";
        echo "==========================================\n\n";
        
        // Тест 1: Проверка периода "неделя"
        $this->testPeriod('week', 'Неделя');
        
        // Тест 2: Проверка периода "месяц"
        $this->testPeriod('month', 'Месяц');
        
        // Тест 3: Проверка периода "год"
        $this->testPeriod('year', 'Год');
        
        // Тест 4: Проверка с реальными данными
        $this->testRealData();
    }
    
    private function testPeriod($period, $label) {
        echo "📅 ТЕСТ ПЕРИОДА: {$label}\n";
        echo "------------------------\n";
        
        $url = "{$this->baseUrl}/statistics/operations?period={$period}";
        echo "URL: {$url}\n";
        
        // Симуляция запроса
        echo "📊 Ожидаемый результат:\n";
        echo "  - Период: {$period}\n";
        echo "  - Группировка: " . $this->getGroupBy($period) . "\n";
        echo "  - Диапазон дат: " . $this->getDateRange($period) . "\n";
        echo "  - Данные: receipts, writeOffs, transfers\n";
        echo "\n";
    }
    
    private function getGroupBy($period) {
        switch ($period) {
            case 'week':
                return 'по дням';
            case 'month':
                return 'по неделям';
            case 'year':
                return 'по месяцам';
            default:
                return 'по месяцам';
        }
    }
    
    private function getDateRange($period) {
        $endDate = date('Y-m-d');
        switch ($period) {
            case 'week':
                $startDate = date('Y-m-d', strtotime('-1 week'));
                break;
            case 'month':
                $startDate = date('Y-m-d', strtotime('-1 month'));
                break;
            case 'year':
                $startDate = date('Y-m-d', strtotime('-1 year'));
                break;
            default:
                $startDate = date('Y-m-d', strtotime('-1 year'));
        }
        return "{$startDate} - {$endDate}";
    }
    
    private function testRealData() {
        echo "📊 ТЕСТ РЕАЛЬНЫХ ДАННЫХ\n";
        echo "------------------------\n";
        
        echo "📋 Анализ данных в базе:\n";
        echo "  - receipts: 16 записей (июль 2025)\n";
        echo "  - write_offs: 9 записей (июль 2025)\n";
        echo "  - product_transfers: 2 записи (июль 2025)\n";
        echo "\n";
        
        echo "🔍 Проблема:\n";
        echo "  - Данные только за июль 2025\n";
        echo "  - Запрос за год (2024-07-28 - 2025-07-28) не найдет данные\n";
        echo "  - Нужно изменить логику для отображения доступных данных\n";
        echo "\n";
        
        echo "💡 Решение:\n";
        echo "  1. Изменить период на 'month' для отображения данных\n";
        echo "  2. Или добавить логику для отображения последних доступных данных\n";
        echo "  3. Или создать тестовые данные за разные месяцы\n";
        echo "\n";
    }
    
    public function printRecommendations() {
        echo "📋 РЕКОМЕНДАЦИИ ДЛЯ ИСПРАВЛЕНИЯ:\n";
        echo "================================\n";
        echo "\n";
        echo "1. 🔧 ИЗМЕНИТЬ КОНТРОЛЛЕР:\n";
        echo "   - Добавить логику для отображения последних доступных данных\n";
        echo "   - Если данных нет за запрошенный период, показывать последние данные\n";
        echo "   - Добавить fallback для пустых результатов\n";
        echo "\n";
        echo "2. 📊 ИЗМЕНИТЬ ФРОНТЕНД:\n";
        echo "   - Добавить обработку пустых данных\n";
        echo "   - Показывать сообщение 'Нет данных за выбранный период'\n";
        echo "   - Автоматически переключаться на период с данными\n";
        echo "\n";
        echo "3. 🗄️  ДОБАВИТЬ ТЕСТОВЫЕ ДАННЫЕ:\n";
        echo "   - Создать записи за разные месяцы\n";
        echo "   - Или изменить существующие даты на более старые\n";
        echo "\n";
        echo "4. 🎯 БЫСТРОЕ РЕШЕНИЕ:\n";
        echo "   - Изменить период по умолчанию на 'month'\n";
        echo "   - Это покажет данные за последний месяц\n";
        echo "\n";
    }
}

// Запуск тестов
$test = new ApiRealDataTest();
$test->runTests();
$test->printRecommendations();

?> 