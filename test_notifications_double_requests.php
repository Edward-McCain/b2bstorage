<?php

// Тест двойных запросов в фильтрах уведомлений

class NotificationsDoubleRequestsTest {
    
    public function runTests() {
        echo "\n🔍 ТЕСТ ДВОЙНЫХ ЗАПРОСОВ В ФИЛЬТРАХ УВЕДОМЛЕНИЙ\n";
        echo "================================================\n\n";
        
        // Анализ проблемы
        $this->analyzeProblem();
        
        // Проверка фронтенда
        $this->checkFrontendIssues();
        
        // Проверка контроллера
        $this->checkControllerIssues();
        
        // Решения
        $this->provideSolutions();
        
        echo "\n";
    }
    
    private function analyzeProblem() {
        echo "📋 АНАЛИЗ ПРОБЛЕМЫ\n";
        echo "-------------------\n";
        
        echo "🔍 Проблема: При смене фильтров вызывается 2 запроса\n";
        echo "  - 1й запрос: нормальный с фильтрами\n";
        echo "  - 2й запрос: общий без фильтров\n";
        echo "\n";
        
        echo "📋 Возможные причины:\n";
        echo "  1. Двойной вызов loadNotifications()\n";
        echo "  2. Проблема с Multiselect компонентом\n";
        echo "  3. Неправильная обработка событий\n";
        echo "  4. Проблема с реактивностью Vue\n";
        echo "\n";
    }
    
    private function checkFrontendIssues() {
        echo "📱 ПРОВЕРКА ФРОНТЕНДА\n";
        echo "---------------------\n";
        
        echo "📋 Проблемы в NotificationsPage.vue:\n";
        echo "  - updateFilters() вызывает loadNotifications()\n";
        echo "  - @change в Multiselect может срабатывать дважды\n";
        echo "  - Возможно, есть дополнительный вызов в другом месте\n";
        echo "\n";
        
        echo "📋 Код updateFilters:\n";
        echo "  const updateFilters = () => {\n";
        echo "    loadNotifications()\n";
        echo "  }\n";
        echo "\n";
        
        echo "📋 Проблемы с Multiselect:\n";
        echo "  - :object=\"true\" может вызывать проблемы\n";
        echo "  - @change может срабатывать при инициализации\n";
        echo "  - Возможно, есть дополнительный обработчик\n";
        echo "\n";
        
        echo "📋 Возможные решения:\n";
        echo "  1. Добавить debounce для updateFilters\n";
        echo "  2. Проверить, нет ли дублирующих обработчиков\n";
        echo "  3. Использовать watch вместо @change\n";
        echo "  4. Добавить проверку на изменение значений\n";
        echo "\n";
    }
    
    private function checkControllerIssues() {
        echo "🔧 ПРОВЕРКА КОНТРОЛЛЕРА\n";
        echo "------------------------\n";
        
        echo "📋 NotificationController.php:\n";
        echo "  - Метод index() обрабатывает фильтры\n";
        echo "  - Логирование показывает параметры\n";
        echo "  - Возможно, есть проблема с аутентификацией\n";
        echo "\n";
        
        echo "📋 Возможные проблемы:\n";
        echo "  1. Fallback аутентификация может вызывать проблемы\n";
        echo "  2. Возможно, есть middleware, который влияет на запросы\n";
        echo "  3. Проблема с CORS или другими заголовками\n";
        echo "\n";
        
        echo "📋 Проверка параметров:\n";
        echo "  - type: \$request->get('type')\n";
        echo "  - is_read: \$request->get('is_read')\n";
        echo "  - Логирование показывает правильные значения\n";
        echo "\n";
    }
    
    private function provideSolutions() {
        echo "🛠️ РЕШЕНИЯ ПРОБЛЕМЫ\n";
        echo "--------------------\n";
        
        echo "📋 Решение 1: Добавить debounce\n";
        echo "  - Использовать lodash debounce\n";
        echo "  - Задержка 300ms между запросами\n";
        echo "  - Предотвратить множественные вызовы\n";
        echo "\n";
        
        echo "📋 Решение 2: Использовать watch вместо @change\n";
        echo "  - watch(filters, () => loadNotifications())\n";
        echo "  - Более контролируемое поведение\n";
        echo "  - Предотвращение двойных вызовов\n";
        echo "\n";
        
        echo "📋 Решение 3: Проверить обработчики событий\n";
        echo "  - Убрать @change из Multiselect\n";
        echo "  - Использовать только watch\n";
        echo "  - Добавить проверку на реальные изменения\n";
        echo "\n";
        
        echo "📋 Решение 4: Добавить флаг загрузки\n";
        echo "  - Предотвратить новые запросы во время загрузки\n";
        echo "  - if (loading.value) return\n";
        echo "  - Защита от множественных запросов\n";
        echo "\n";
        
        echo "📋 Решение 5: Проверить API конфигурацию\n";
        echo "  - Проверить apiRequest функцию\n";
        echo "  - Возможно, есть автоматические запросы\n";
        echo "  - Проверить interceptors в axios\n";
        echo "\n";
        
        echo "🎯 РЕКОМЕНДУЕМОЕ РЕШЕНИЕ:\n";
        echo "1. Заменить @change на watch\n";
        echo "2. Добавить debounce\n";
        echo "3. Добавить проверку на изменения\n";
        echo "4. Добавить защиту от множественных запросов\n";
        echo "\n";
    }
}

// Запуск тестов
$test = new NotificationsDoubleRequestsTest();
$test->runTests();

?> 