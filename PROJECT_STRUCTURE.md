# Структура проекта B2B SKLAD

## 🏗️ Архитектура проекта

**Backend:** Laravel 11 + Sanctum (API)
**Frontend:** Vue.js 3 + Vue Router + Tailwind CSS
**База данных:** PostgreSQL

---

## 🔧 BACKEND (Laravel)

### 📁 Структура директорий
```
backend/
├── app/
│   ├── Http/Controllers/     # API контроллеры
│   ├── Models/              # Eloquent модели
│   ├── Http/Middleware/     # Middleware
│   └── Providers/           # Сервис-провайдеры
├── routes/
│   └── api.php             # API маршруты
├── database/
│   ├── migrations/          # Миграции БД
│   └── seeders/            # Сидеры данных
└── config/                 # Конфигурация
```

### 🔐 Аутентификация и безопасность

#### Middleware
- **`auth:sanctum`** - защита API маршрутов
- **`InternationalSupport`** - поддержка международных пользователей

#### Rate Limiting
```php
// API: 120 запросов в минуту
RateLimiter::for('api', function (Request $request) {
    return Limit::perMinute(120)->by($request->user()?->id ?: $request->ip());
});

// Auth: 30 попыток входа в минуту
RateLimiter::for('auth', function (Request $request) {
    return Limit::perMinute(30)->by($request->ip());
});

// Register: 10 попыток регистрации в час
RateLimiter::for('register', function (Request $request) {
    return Limit::perHour(10)->by($request->ip());
});
```

### 🎯 API Контроллеры

#### 1. **AuthController** - Аутентификация
```php
// Маршруты:
POST /api/register         # Регистрация
POST /api/login            # Вход
POST /api/logout           # Выход
GET  /api/user             # Данные пользователя
PUT  /api/profile          # Обновление профиля
POST /api/user/avatar      # Загрузка аватара
GET  /api/user/settings    # Настройки пользователя
PUT  /api/user/personal    # Персональные данные
PUT  /api/user/company     # Данные компании
PUT  /api/user/password    # Смена пароля

// Валидация:
- email: required|email|unique
- password: required|min:8|confirmed
- user_name: required|string|max:255
```

#### 2. **ProductController** - Управление товарами
```php
// Маршруты:
GET    /api/products              # Список товаров
GET    /api/products/{id}         # Детали товара
POST   /api/products/draft        # Создание черновика
PUT    /api/products/{id}         # Обновление товара
DELETE /api/products/{id}         # Удаление товара
POST   /api/products/{id}/images  # Загрузка изображений
GET    /api/products/{id}/images  # Получение изображений
DELETE /api/products/images/{id}  # Удаление изображения

// Валидация:
- name: required|string|max:255
- description: nullable|string
- category_id: nullable|string
- article: nullable|string|max:255
- code: nullable|string|max:255
- unit: nullable|string|max:255
```

#### 3. **ReceiptController** - Оприходования
```php
// Маршруты:
GET    /api/receipts              # Список оприходований
GET    /api/receipts/{id}         # Детали оприходования
POST   /api/receipts              # Создание оприходования
PUT    /api/receipts/{id}         # Обновление оприходования
DELETE /api/receipts/{id}         # Удаление оприходования

// Валидация:
- number: required|string|max:50
- date: required|date
- organization: required|string|max:255
- warehouse: required|integer|exists:warehouses,id
- status: nullable|string|in:draft,posted
- positions: nullable|array
- positions.*.name: required|string|max:255
- positions.*.quantity: required|numeric|min:0
- positions.*.price: required|numeric|min:0
```

#### 4. **WriteOffController** - Списания
```php
// Маршруты:
GET    /api/write-offs            # Список списаний
GET    /api/write-offs/{id}       # Детали списания
POST   /api/write-offs            # Создание списания
PUT    /api/write-offs/{id}       # Обновление списания
DELETE /api/write-offs/{id}       # Удаление списания

// Валидация:
- number: required|string|max:50
- date: required|date
- organization: required|string|max:255
- warehouse: required|integer
- status: nullable|string|max:50
- positions: nullable|array
- positions.*.name: required|string
- positions.*.quantity: required|numeric
- positions.*.price: required|numeric
```

#### 5. **InventoryController** - Инвентаризации
```php
// Маршруты:
GET    /api/inventories                    # Список инвентаризаций
GET    /api/inventories/{id}               # Детали инвентаризации
POST   /api/inventories                    # Создание инвентаризации
PUT    /api/inventories/{id}               # Обновление инвентаризации
DELETE /api/inventories/{id}               # Удаление инвентаризации
GET    /api/inventories/{id}/export        # Экспорт инвентаризации
POST   /api/inventories/calculate-balances # Расчет остатков

// Валидация:
- name: required|string|max:255
- description: nullable|string
- warehouse: required|exists:warehouses,id
- status: required|in:draft,in_progress,completed,cancelled
- positions: array
- positions.*.product_id: required|exists:products_sklad,id
- positions.*.calculated_quantity: required|numeric|min:0
- positions.*.actual_quantity: required|numeric|min:0
```

#### 6. **ProductTransferController** - Перемещения товаров
```php
// Маршруты:
GET    /api/transfers                      # Список перемещений
POST   /api/transfers/filter               # Фильтрация перемещений
POST   /api/transfers/available-products   # Доступные товары склада
GET    /api/transfers/all-products         # Все товары
POST   /api/transfers                      # Создание перемещения
GET    /api/transfers/{id}                 # Детали перемещения
PUT    /api/transfers/{id}                 # Обновление перемещения
DELETE /api/transfers/{id}                 # Удаление перемещения
POST   /api/transfers/{id}/confirm         # Подтверждение
POST   /api/transfers/{id}/complete        # Завершение
POST   /api/transfers/{id}/cancel          # Отмена

// Валидация:
- from_warehouse_id: required|exists:warehouses,id
- to_warehouse_id: required|exists:warehouses,id|different:from_warehouse_id
- transfer_date: required|date
- positions: required|array|min:1
- positions.*.product_id: required|exists:products_sklad,id
- positions.*.quantity: required|integer|min:1
```

#### 7. **ProductBalanceController** - Остатки товаров
```php
// Маршруты:
GET    /api/balances                       # Список остатков
POST   /api/balances                       # Фильтрация остатков
GET    /api/balances/summary               # Сводка остатков
POST   /api/balances/summary               # Сводка с фильтрами
GET    /api/balances/by-warehouse          # Остатки по складам
GET    /api/balances/by-product            # Остатки по товарам
GET    /api/balances/low-stock             # Товары с низким остатком
GET    /api/balances/out-of-stock          # Товары без остатка
GET    /api/balances/movements             # Движения товаров
POST   /api/balances/movements             # Движения с фильтрами
```

#### 8. **WarehouseController** - Склады
```php
// Маршруты:
GET    /api/warehouses                     # Список складов
GET    /api/warehouses/{id}                # Детали склада
POST   /api/warehouses                     # Создание склада
PUT    /api/warehouses/{id}                # Обновление склада
DELETE /api/warehouses/{id}                # Удаление склада
```

#### 9. **CategoryController** - Категории и подкатегории
```php
// Маршруты:
GET    /api/categories                     # Список категорий
GET    /api/categories/{id}/subcategories  # Подкатегории категории
GET    /api/subcategories                  # Подкатегории с фильтром
```

#### 10. **CurrencyController** - Валюты
```php
// Маршруты:
GET    /api/currencies                     # Курсы валют
GET    /api/currencies/fetch               # Обновление курсов
GET    /api/currencies/type/{type}        # Курс по типу
POST   /api/currencies/convert             # Конвертация
GET    /api/user/currency                  # Валюта пользователя
PUT    /api/user/currency                  # Обновление валюты
```

### 📊 Модели данных

#### Основные модели:
- **User** - Пользователи системы
- **ProductSklad** - Товары
- **Warehouse** - Склады
- **Receipt** - Оприходования
- **WriteOff** - Списания
- **Inventory** - Инвентаризации
- **ProductTransfer** - Перемещения товаров
- **ProductBalance** - Остатки товаров
- **Category** - Категории товаров
- **Subcategory** - Подкатегории товаров

### 🔒 Безопасность

#### Авторизация:
- **Laravel Sanctum** для API токенов
- Автоматическая генерация `user_id` при создании пользователя
- Проверка прав доступа к данным пользователя

#### Валидация:
- Все входные данные валидируются
- Проверка существования связанных записей
- Валидация бизнес-логики (например, достаточность товаров)

#### Изоляция данных:
- Пользователи видят только свои данные
- Все запросы фильтруются по `user_id` или `created_by`

---

## 🎨 FRONTEND (Vue.js 3)

### 📁 Структура директорий
```
frontend/src/
├── components/           # Vue компоненты
│   ├── products/        # Компоненты товаров
│   ├── warehouses/      # Компоненты складов
│   ├── purchases/       # Компоненты закупок
│   ├── sales/          # Компоненты продаж
│   ├── analytics/      # Компоненты аналитики
│   ├── counterparties/ # Компоненты контрагентов
│   └── icons/          # Иконки
├── router/             # Маршрутизация
├── config/             # Конфигурация
├── data/               # Статические данные
├── services/           # Сервисы
└── assets/             # Ресурсы
```

### 🎯 Основные страницы

#### 1. **Аутентификация**
- **AuthPage.vue** - Страница входа/регистрации
- **LoginForm.vue** - Форма входа
- **RegisterForm.vue** - Форма регистрации

#### 2. **Главная страница**
- **HomePage.vue** - Главная страница
- **HeroSection.vue** - Героическая секция
- **Header.vue** - Шапка сайта
- **Footer.vue** - Подвал сайта

#### 3. **Товары** (`/products`)
- **ProductsPage.vue** - Список товаров (58KB, 1248 строк)
- **ProductCreatePage.vue** - Создание товара (37KB, 798 строк)
- **ProductEditPage.vue** - Редактирование товара (37KB, 835 строк)
- **ProductsMenu.vue** - Меню товаров

#### 4. **Оприходования** (`/products/receipts`)
- **ReceiptsPage.vue** - Список оприходований (14KB, 369 строк)
- **ReceiptCreatePage.vue** - Создание оприходования (27KB, 668 строк)
- **ReceiptViewPage.vue** - Просмотр оприходования (6.4KB, 149 строк)
- **ReceiptEditPage.vue** - Редактирование оприходования (25KB, 633 строк)

#### 5. **Списания** (`/products/write-offs`)
- **WriteOffsPage.vue** - Список списаний (13KB, 335 строк)
- **WriteOffCreatePage.vue** - Создание списания (27KB, 631 строка)
- **WriteOffViewPage.vue** - Просмотр списания (6.7KB, 160 строк)
- **WriteOffEditPage.vue** - Редактирование списания (32KB, 792 строки)

#### 6. **Инвентаризации** (`/products/inventory`)
- **InventoryPage.vue** - Список инвентаризаций (14KB, 392 строки)
- **InventoryCreatePage.vue** - Создание инвентаризации (23KB, 561 строка)
- **InventoryViewPage.vue** - Просмотр инвентаризации (10KB, 267 строк)
- **InventoryEditPage.vue** - Редактирование инвентаризации (24KB, 606 строк)

#### 7. **Перемещения** (`/products/transfers`)
- **TransfersPage.vue** - Список перемещений (14KB, 416 строк)
- **TransferCreatePage.vue** - Создание перемещения (22KB, 508 строк)
- **TransferViewModal.vue** - Просмотр перемещения (5KB, 136 строк)
- **TransferCompleteModal.vue** - Завершение перемещения (5.8KB, 158 строк)

#### 8. **Остатки** (`/products/balances`)
- **BalancesPage.vue** - Остатки товаров (18KB, 437 строк)
- **MovementsModal.vue** - Движения товаров (8.1KB, 250 строк)

#### 9. **Склады** (`/warehouses`)
- **WarehousesPage.vue** - Список складов
- **WarehouseCreatePage.vue** - Создание склада
- **WarehouseEditPage.vue** - Редактирование склада
- **WarehouseViewPage.vue** - Просмотр склада

#### 10. **Закупки** (`/purchases`)
- **PurchasesPage.vue** - Главная страница закупок
- **SupplierOrdersPage.vue** - Заказы поставщикам
- **SupplierInvoicesPage.vue** - Счета поставщиков
- **ReceivedInvoicesPage.vue** - Полученные счета
- **PurchaseReceiptsPage.vue** - Оприходования закупок
- **SupplierReturnsPage.vue** - Возвраты поставщикам
- **PurchaseManagementPage.vue** - Управление закупками

#### 11. **Продажи** (`/sales`)
- **SalesPage.vue** - Главная страница продаж
- **CustomerOrdersPage.vue** - Заказы клиентов
- **CustomerInvoicesPage.vue** - Счета клиентам
- **ShipmentsPage.vue** - Отгрузки
- **CommissionReportsPage.vue** - Комиссионные отчеты
- **CustomerReturnsPage.vue** - Возвраты клиентов
- **IssuedInvoicesPage.vue** - Выданные счета
- **ProfitabilityPage.vue** - Рентабельность
- **ConsignmentGoodsPage.vue** - Товары на комиссии
- **SalesFunnelPage.vue** - Воронка продаж
- **UnitEconomicsPage.vue** - Юнит-экономика

#### 12. **Аналитика** (`/analytics`)
- **AnalyticsPage.vue** - Главная страница аналитики (12KB, 270 строк)
- **AnalyticsSalesPage.vue** - Аналитика продаж
- **AnalyticsMoneyPage.vue** - Аналитика денег
- **AnalyticsOverdueOrdersPage.vue** - Просроченные заказы
- **AnalyticsOverdueInvoicesPage.vue** - Просроченные счета

#### 13. **Контрагенты** (`/counterparties`)
- **CounterpartiesPage.vue** - Главная страница контрагентов (8.3KB, 158 строк)
- **CounterpartiesBuyersPage.vue** - Покупатели
- **CounterpartiesSuppliersPage.vue** - Поставщики
- **CounterpartiesGroupsPage.vue** - Группы контрагентов

#### 14. **Настройки аккаунта** (`/account-settings`)
- **AccountSettingsPage.vue** - Настройки аккаунта (43KB, 1031 строка)

### 🛠️ Технические компоненты

#### UI компоненты:
- **ImageDropzone.vue** - Загрузка изображений (2.3KB, 79 строк)
- **CurrencySelector.vue** - Выбор валюты (5.2KB, 162 строки)

#### Служебные страницы:
- **InternalOrdersPage.vue** - Внутренние заказы (1.6KB, 37 строк)
- **PriceListsPage.vue** - Прайс-листы (1.5KB, 37 строк)
- **TurnoversPage.vue** - Обороты (1.4KB, 37 строк)
- **SerialNumbersPage.vue** - Серийные номера (1.5KB, 37 строк)
- **MarkingCodesPage.vue** - Коды маркировки (1.6KB, 37 строк)
- **MarkingPage.vue** - Маркировка (1.6KB, 37 строк)

### 🔧 Конфигурация

#### API конфигурация (`src/config/api.js`):
```javascript
// Базовый URL API
const API_BASE_URL = 'http://localhost:8000/api'

// Функция для API запросов
export async function apiRequest(endpoint, options = {}) {
  // Логика запросов к API
}
```

#### Роутинг (`src/router/index.js`):
- 50+ маршрутов
- Защищенные маршруты
- Ленивая загрузка компонентов

### 🎨 Стилизация

#### CSS фреймворк:
- **Tailwind CSS** - Utility-first CSS фреймворк
- **Кастомные стили** в `src/assets/`

#### Компоненты:
- **Multiselect** - Кастомные селекты
- **Lucide Vue** - Иконки
- **Toastr** - Уведомления

### 📊 Функциональность

#### Основные возможности:
1. **CRUD операции** для всех сущностей
2. **Фильтрация и поиск** данных
3. **Загрузка файлов** с прогресс-барами
4. **Валидация форм** на клиенте и сервере
5. **Уведомления** о результатах операций
6. **Адаптивный дизайн** для мобильных устройств
7. **Многоязычная поддержка** (подготовлена структура)
8. **Аналитика и отчеты**
9. **Управление валютой**
10. **Система ролей и прав доступа**

---

## 🔄 Взаимодействие Frontend ↔ Backend

### API запросы:
- Все запросы через `apiRequest()` функцию
- Автоматическое добавление токенов авторизации
- Обработка ошибок и уведомления

### Валидация:
- **Frontend**: Проверка форм перед отправкой
- **Backend**: Серверная валидация всех данных
- **Синхронизация**: Ошибки валидации отображаются в формах

### Безопасность:
- **CORS** настроен для API
- **CSRF** защита для веб-форм
- **Rate limiting** для предотвращения атак
- **Изоляция данных** по пользователям

---

## 📈 Статистика проекта

### Backend:
- **15+ контроллеров**
- **50+ API endpoints**
- **10+ моделей данных**
- **Полная валидация** всех входных данных
- **Безопасность** на всех уровнях

### Frontend:
- **50+ Vue компонентов**
- **50+ маршрутов**
- **Адаптивный дизайн**
- **Современный UI/UX**

### Общие характеристики:
- **Модульная архитектура**
- **Масштабируемость**
- **Безопасность**
- **Производительность**
- **Удобство использования** 