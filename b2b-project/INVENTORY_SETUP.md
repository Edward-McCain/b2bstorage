# Система инвентаризаций B2B SKLAD

## Описание

Система инвентаризаций позволяет проводить инвентаризацию товаров на складах с учетом расчетных и фактических остатков, выявления избытков и недостач.

## Структура базы данных

### Таблица `inventories`
Основная таблица инвентаризаций:
- `id` - уникальный идентификатор
- `name` - название инвентаризации
- `description` - описание
- `warehouse_id` - ID склада
- `status` - статус (draft, in_progress, completed, cancelled)
- `created_by` - ID пользователя, создавшего инвентаризацию
- `created_at` - дата создания
- `updated_at` - дата обновления
- `completed_at` - дата завершения
- `notes` - примечания

### Таблица `inventory_items`
Товары в инвентаризации:
- `id` - уникальный идентификатор
- `inventory_id` - ID инвентаризации
- `product_id` - ID товара
- `calculated_quantity` - расчетный остаток
- `actual_quantity` - фактический остаток
- `difference_quantity` - разница (вычисляемое поле)
- `excess_shortage` - тип расхождения (normal, excess, shortage)
- `notes` - примечания
- `created_at` - дата создания
- `updated_at` - дата обновления

### Таблица `inventory_files`
Файлы инвентаризации:
- `id` - уникальный идентификатор
- `inventory_id` - ID инвентаризации
- `filename` - имя файла в системе
- `original_filename` - оригинальное имя файла
- `file_path` - путь к файлу
- `file_size` - размер файла
- `mime_type` - тип файла
- `uploaded_by` - ID пользователя, загрузившего файл
- `created_at` - дата загрузки

## Установка

### 1. Создание таблиц в базе данных

Выполните SQL скрипт для создания таблиц:

```bash
psql -d your_database_name -f create_inventory_tables.sql
```

### 2. Настройка API

Добавьте следующие маршруты в Laravel API:

```php
// routes/api.php
Route::prefix('inventories')->group(function () {
    Route::get('/', [InventoryController::class, 'index']);
    Route::post('/', [InventoryController::class, 'store']);
    Route::get('/{id}', [InventoryController::class, 'show']);
    Route::put('/{id}', [InventoryController::class, 'update']);
    Route::delete('/{id}', [InventoryController::class, 'destroy']);
    Route::get('/{id}/export', [InventoryController::class, 'export']);
});

Route::prefix('inventory-files')->group(function () {
    Route::post('/upload', [InventoryFileController::class, 'upload']);
    Route::delete('/{id}', [InventoryFileController::class, 'destroy']);
});
```

### 3. Создание моделей Laravel

#### Модель Inventory
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inventory extends Model
{
    protected $fillable = [
        'name',
        'description',
        'warehouse_id',
        'status',
        'created_by',
        'completed_at',
        'notes'
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(InventoryItem::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(InventoryFile::class);
    }
}
```

#### Модель InventoryItem
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryItem extends Model
{
    protected $fillable = [
        'inventory_id',
        'product_id',
        'calculated_quantity',
        'actual_quantity',
        'notes'
    ];

    protected $casts = [
        'calculated_quantity' => 'decimal:3',
        'actual_quantity' => 'decimal:3',
    ];

    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
```

#### Модель InventoryFile
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryFile extends Model
{
    protected $fillable = [
        'inventory_id',
        'filename',
        'original_filename',
        'file_path',
        'file_size',
        'mime_type',
        'uploaded_by'
    ];

    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class);
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
```

## Функциональность

### Создание инвентаризации
1. Перейдите на страницу "Инвентаризации"
2. Нажмите кнопку "Добавить"
3. Заполните основные поля:
   - Название инвентаризации
   - Выберите склад
   - Укажите статус
   - Добавьте описание (опционально)
4. Добавьте товары для инвентаризации
5. Загрузите файлы (опционально)
6. Сохраните инвентаризацию

### Редактирование инвентаризации
1. В списке инвентаризаций нажмите кнопку редактирования
2. Измените необходимые поля
3. Добавьте или удалите товары
4. Сохраните изменения

### Просмотр инвентаризации
1. В списке инвентаризаций нажмите кнопку просмотра
2. Просмотрите детальную информацию
3. Изучите статистику по расхождениям
4. Экспортируйте данные в Excel

### Удаление инвентаризации
1. В списке инвентаризаций нажмите кнопку удаления
2. Подтвердите удаление в модальном окне

## Статусы инвентаризации

- **Черновик** - инвентаризация создана, но не начата
- **В процессе** - инвентаризация проводится
- **Завершена** - инвентаризация завершена
- **Отменена** - инвентаризация отменена

## Типы расхождений

- **Норма** - фактический остаток равен расчетному
- **Избыток** - фактический остаток больше расчетного
- **Недостача** - фактический остаток меньше расчетного

## API Endpoints

### Получение списка инвентаризаций
```
GET /api/inventories
```

Параметры:
- `name` - поиск по названию
- `date_from` - дата от
- `date_to` - дата до
- `warehouse` - ID склада
- `status` - статус

### Создание инвентаризации
```
POST /api/inventories
```

### Получение инвентаризации
```
GET /api/inventories/{id}
```

### Обновление инвентаризации
```
PUT /api/inventories/{id}
```

### Удаление инвентаризации
```
DELETE /api/inventories/{id}
```

### Экспорт в Excel
```
GET /api/inventories/{id}/export
```

### Загрузка файла
```
POST /api/inventory-files/upload
```

## Особенности реализации

1. **Вычисляемые поля** - разница и тип расхождения вычисляются автоматически в базе данных
2. **Валидация** - проверка корректности данных на фронтенде и бэкенде
3. **Файлы** - поддержка загрузки и управления файлами
4. **Экспорт** - возможность экспорта данных в Excel
5. **Статистика** - автоматический подсчет расхождений

## Безопасность

- Все операции требуют авторизации
- Валидация данных на сервере
- Проверка прав доступа к складам
- Логирование операций

## Поддержка

При возникновении проблем:
1. Проверьте логи Laravel (`storage/logs/laravel.log`)
2. Убедитесь в корректности настроек базы данных
3. Проверьте права доступа к файлам
4. Обратитесь к документации Laravel и Vue.js 