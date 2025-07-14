# API для системы инвентаризаций

## Установка и настройка

### 1. Выполнение миграций

```bash
php artisan migrate
```

### 2. Запуск сидера (опционально)

```bash
php artisan db:seed --class=InventorySeeder
```

### 3. Проверка маршрутов

Убедитесь, что маршруты добавлены в `routes/api.php`:

```php
// Inventory routes
Route::get('/inventories', [InventoryController::class, 'index']);
Route::get('/inventories/{id}', [InventoryController::class, 'show']);
Route::post('/inventories', [InventoryController::class, 'store']);
Route::put('/inventories/{id}', [InventoryController::class, 'update']);
Route::delete('/inventories/{id}', [InventoryController::class, 'destroy']);
Route::get('/inventories/{id}/export', [InventoryController::class, 'export']);

// Inventory file routes
Route::post('/inventory-files/upload', [InventoryFileController::class, 'upload']);
Route::get('/inventory-files/{id}', [InventoryFileController::class, 'show']);
Route::delete('/inventory-files/{id}', [InventoryFileController::class, 'destroy']);
```

## API Endpoints

### Инвентаризации

#### GET /api/inventories
Получить список инвентаризаций

**Параметры запроса:**
- `name` (string) - поиск по названию
- `date_from` (date) - дата от
- `date_to` (date) - дата до
- `warehouse` (integer) - ID склада
- `status` (string) - статус (draft, in_progress, completed, cancelled)

**Пример ответа:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Инвентаризация склада А",
      "description": "Описание инвентаризации",
      "warehouse_id": 1,
      "status": "completed",
      "created_by": 1,
      "created_at": "2025-01-20T10:00:00.000000Z",
      "updated_at": "2025-01-20T10:00:00.000000Z",
      "completed_at": "2025-01-20T12:00:00.000000Z",
      "notes": null,
      "items_count": 5,
      "discrepancies_count": 2,
      "warehouse_name": "Склад А",
      "warehouse_address": "ул. Примерная, 1",
      "created_by": "Test User",
      "warehouse": {
        "id": 1,
        "name": "Склад А",
        "address": "ул. Примерная, 1"
      },
      "items": [...],
      "files": [...]
    }
  ]
}
```

#### POST /api/inventories
Создать новую инвентаризацию

**Тело запроса:**
```json
{
  "name": "Новая инвентаризация",
  "description": "Описание",
  "warehouse_id": 1,
  "status": "draft",
  "positions": [
    {
      "product_id": 1,
      "calculated_quantity": 100.000,
      "actual_quantity": 95.000,
      "notes": "Примечание"
    }
  ],
  "files": [1, 2, 3]
}
```

#### GET /api/inventories/{id}
Получить инвентаризацию по ID

#### PUT /api/inventories/{id}
Обновить инвентаризацию

#### DELETE /api/inventories/{id}
Удалить инвентаризацию

#### GET /api/inventories/{id}/export
Экспорт инвентаризации в Excel

### Файлы инвентаризации

#### POST /api/inventory-files/upload
Загрузить файл

**Тело запроса:**
- `file` (file) - файл для загрузки (максимум 10MB)

**Пример ответа:**
```json
{
  "success": true,
  "message": "Файл загружен успешно",
  "data": {
    "id": 1,
    "filename": "abc123_1642680000.pdf",
    "original_filename": "document.pdf",
    "file_url": "/storage/inventory-files/abc123_1642680000.pdf",
    "file_size": 1024000,
    "uploaded_by": "Test User"
  }
}
```

#### GET /api/inventory-files/{id}
Получить информацию о файле

#### DELETE /api/inventory-files/{id}
Удалить файл

## Модели

### Inventory
```php
protected $fillable = [
    'name',
    'description', 
    'warehouse_id',
    'status',
    'created_by',
    'completed_at',
    'notes'
];
```

### InventoryItem
```php
protected $fillable = [
    'inventory_id',
    'product_id',
    'calculated_quantity',
    'actual_quantity',
    'notes'
];
```

### InventoryFile
```php
protected $fillable = [
    'inventory_id',
    'filename',
    'original_filename',
    'file_path',
    'file_size',
    'mime_type',
    'uploaded_by'
];
```

## Статусы инвентаризации

- `draft` - Черновик
- `in_progress` - В процессе
- `completed` - Завершена
- `cancelled` - Отменена

## Типы расхождений

- `normal` - Норма (фактический = расчетный)
- `excess` - Избыток (фактический > расчетный)
- `shortage` - Недостача (фактический < расчетный)

## Особенности реализации

1. **Вычисляемые поля** - разница и тип расхождения вычисляются автоматически в базе данных
2. **Валидация** - проверка данных на сервере
3. **Транзакции** - все операции с инвентаризациями выполняются в транзакциях
4. **Файлы** - поддержка загрузки и управления файлами
5. **Фильтрация** - гибкая система фильтров для поиска

## Обработка ошибок

Все API endpoints возвращают стандартизированные ответы:

**Успех:**
```json
{
  "success": true,
  "message": "Операция выполнена успешно",
  "data": {...}
}
```

**Ошибка:**
```json
{
  "success": false,
  "message": "Описание ошибки",
  "errors": {...} // для ошибок валидации
}
```

## Тестирование

Для тестирования API можно использовать:

1. **Postman** - для ручного тестирования
2. **Laravel Tinker** - для тестирования моделей
3. **Unit тесты** - для автоматизированного тестирования

### Пример тестирования через Tinker:

```bash
php artisan tinker
```

```php
// Создать инвентаризацию
$inventory = App\Models\Inventory::create([
    'name' => 'Тестовая инвентаризация',
    'warehouse_id' => 1,
    'status' => 'draft',
    'created_by' => 1
]);

// Добавить товар
App\Models\InventoryItem::create([
    'inventory_id' => $inventory->id,
    'product_id' => 1,
    'calculated_quantity' => 100,
    'actual_quantity' => 95
]);
```

## Безопасность

- Все маршруты защищены middleware `auth:sanctum`
- Валидация входных данных
- Проверка прав доступа
- Логирование операций 