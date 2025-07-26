# Система умных уведомлений

## Обзор

Система умных уведомлений предоставляет пользователям автоматические рекомендации и уведомления на основе анализа данных в системе управления складскими запасами. Система интегрирована с OpenAI API для генерации интеллектуальных рекомендаций.

## Функциональность

### Типы уведомлений

1. **Информация (info)** - общие информационные сообщения
2. **Предупреждение (warning)** - важные предупреждения
3. **Рекомендация (recommendation)** - AI-генерируемые рекомендации
4. **Низкие остатки (low_stock)** - уведомления о товарах с низкими остатками
5. **Просроченные документы (overdue)** - уведомления о просроченных документах

### AI функции

- **Анализ остатков** - анализ товаров с низкими остатками и рекомендации по закупкам
- **Анализ документов** - проверка просроченных оприходований, списаний и перемещений
- **Умный поиск** - улучшение результатов поиска товаров
- **Прогнозирование остатков** - прогноз остатков на основе исторических данных
- **Генерация рекомендаций** - общие рекомендации по управлению запасами

## Структура базы данных

### Таблица `notifications`

```sql
CREATE TABLE notifications (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    type VARCHAR(50) DEFAULT 'info',
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP WITHOUT TIME ZONE DEFAULT NOW(),
    updated_at TIMESTAMP WITHOUT TIME ZONE DEFAULT NOW()
);
```

## API Endpoints

### Уведомления

- `GET /api/notifications` - получение списка уведомлений
- `GET /api/notifications/unread` - получение непрочитанных уведомлений
- `GET /api/notifications/unread-count` - количество непрочитанных уведомлений
- `PUT /api/notifications/{id}/mark-read` - отметить как прочитанное
- `PUT /api/notifications/mark-all-read` - отметить все как прочитанные
- `DELETE /api/notifications/{id}` - удалить уведомление

### AI функции

- `POST /api/ai/analyze-stock` - анализ остатков
- `POST /api/ai/analyze-documents` - анализ документов
- `POST /api/ai/smart-search` - умный поиск
- `POST /api/ai/forecast-stock` - прогнозирование остатков
- `POST /api/ai/generate-recommendations` - генерация рекомендаций
- `POST /api/ai/comprehensive-analysis` - комплексный анализ

## Компоненты

### Backend

- **Notification Model** (`app/Models/Notification.php`) - модель для работы с уведомлениями
- **NotificationController** (`app/Http/Controllers/NotificationController.php`) - контроллер для API
- **AIController** (`app/Http/Controllers/AIController.php`) - контроллер для AI функций
- **NotificationService** (`app/Services/NotificationService.php`) - сервис для генерации уведомлений
- **AIService** (`app/Services/AIService.php`) - сервис для работы с OpenAI API

### Frontend

- **NotificationsPage** (`frontend/src/components/NotificationsPage.vue`) - главная страница уведомлений
- **NotificationItem** (`frontend/src/components/NotificationItem.vue`) - компонент отдельного уведомления
- **NotificationFilters** (`frontend/src/components/NotificationFilters.vue`) - компонент фильтров
- **useNotifications** (`frontend/src/composables/useNotifications.js`) - композабл для работы с уведомлениями
- **useAI** (`frontend/src/composables/useAI.js`) - композабл для AI функций

## Настройка

### OpenAI API

1. Добавьте API ключ в `.env` файл:
```
OPENAI_API_KEY=your_openai_api_key_here
```

2. Установите пакет OpenAI:
```bash
composer require openai-php/laravel
```

3. Опубликуйте конфигурацию:
```bash
php artisan vendor:publish --provider="OpenAI\Laravel\ServiceProvider"
```

### База данных

1. Создайте таблицу уведомлений:
```sql
CREATE TABLE notifications (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    type VARCHAR(50) DEFAULT 'info',
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP WITHOUT TIME ZONE DEFAULT NOW(),
    updated_at TIMESTAMP WITHOUT TIME ZONE DEFAULT NOW()
);
```

## Использование

### Создание уведомления

```php
use App\Models\Notification;

Notification::create([
    'user_id' => $userId,
    'type' => 'warning',
    'message' => 'У вас 3 необработанных оприходований старше 7 дней.',
    'is_read' => false
]);
```

### Использование AI сервиса

```php
use App\Services\AIService;

$aiService = new AIService();
$recommendations = $aiService->analyzeStockLevels($userId);
```

### Frontend использование

```javascript
import { useNotifications } from '@/composables/useNotifications'
import { useAI } from '@/composables/useAI'

const { notifications, loadNotifications, markAsRead } = useNotifications()
const { comprehensiveAnalysis, loading } = useAI()

// Загрузка уведомлений
await loadNotifications()

// Генерация AI рекомендаций
await comprehensiveAnalysis()
```

## Тестирование

Запустите тесты для проверки функциональности:

```bash
php test_notifications.php
```

## Безопасность

- Все API endpoints защищены аутентификацией
- Пользователи могут видеть только свои уведомления
- OpenAI API ключ хранится в защищенном `.env` файле
- Валидация всех входных данных

## Мониторинг

- Логирование всех AI запросов
- Отслеживание ошибок OpenAI API
- Мониторинг производительности запросов

## Будущие улучшения

- Push-уведомления
- Email уведомления
- Настройка предпочтений пользователя
- Более продвинутые AI модели
- Интеграция с другими системами 