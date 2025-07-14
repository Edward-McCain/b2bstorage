# Настройка системы валют

## Описание

Система валют позволяет:
1. Получать курсы валют с внешнего API (https://b2bmarket.uz/api/api/currency)
2. Сохранять курсы валют в базе данных
3. Позволять пользователям выбирать предпочитаемую валюту
4. Конвертировать суммы между валютами

## Структура базы данных

### Таблица currencies
- `id` - первичный ключ
- `currency_id` - уникальный идентификатор валюты (UUID)
- `full_name` - полное название валюты
- `currency_type` - код валюты (USD, EUR, RUB, UZS и т.д.)
- `rate` - курс валюты к USD
- `date` - дата курса
- `created_at`, `updated_at` - временные метки

### Поле currency в таблице users
- `currency` - предпочитаемая валюта пользователя (по умолчанию USD)

## API Endpoints

### Получение курсов валют
```
GET /api/currencies
```
Возвращает все курсы валют из базы данных.

### Обновление курсов с внешнего API
```
GET /api/currencies/fetch
```
Получает курсы с https://b2bmarket.uz/api/api/currency и сохраняет в базу.

### Получение курса по типу валюты
```
GET /api/currencies/type/{currency_type}
```
Возвращает курс для конкретной валюты.

### Конвертация валют
```
POST /api/currencies/convert
{
    "amount": 100,
    "from_currency": "USD",
    "to_currency": "EUR"
}
```

### Получение валюты пользователя
```
GET /api/user/currency
```

### Обновление валюты пользователя
```
PUT /api/user/currency
{
    "currency": "EUR"
}
```

## Установка

### 1. Создание таблицы валют

Выполните SQL скрипт:
```sql
-- Создание таблицы валют для PostgreSQL
CREATE TABLE IF NOT EXISTS currencies (
    id BIGSERIAL PRIMARY KEY,
    currency_id CHAR(36) UNIQUE NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    currency_type VARCHAR(10) NOT NULL,
    rate DECIMAL(10,2) NOT NULL,
    date TIMESTAMP NOT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL
);

-- Создание индексов
CREATE INDEX IF NOT EXISTS idx_currencies_currency_type ON currencies(currency_type);
CREATE INDEX IF NOT EXISTS idx_currencies_date ON currencies(date);

-- Добавление поля currency в таблицу users (если его нет)
DO $$ 
BEGIN
    IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_name = 'users' AND column_name = 'currency') THEN
        ALTER TABLE users ADD COLUMN currency VARCHAR(10) DEFAULT 'USD';
    END IF;
END $$;

-- Создание индекса для поля currency в таблице users
CREATE INDEX IF NOT EXISTS idx_users_currency ON users(currency);
```

### 2. Запуск миграции Laravel

```bash
cd b2b-project/backend
php artisan migrate
```

### 3. Первоначальная загрузка курсов валют

```bash
cd b2b-project/backend
php artisan currency:update-rates
```

## Автоматическое обновление курсов

Для автоматического обновления курсов валют добавьте в cron:

```bash
# Обновление курсов валют каждый час
0 * * * * cd /path/to/b2b-project/backend && php artisan currency:update-rates
```

## Использование на фронтенде

### Компонент выбора валюты

Компонент `CurrencySelector.vue` автоматически:
1. Загружает доступные валюты
2. Показывает текущую валюту пользователя
3. Позволяет выбрать новую валюту
4. Сохраняет выбор в базе данных

### Интеграция в Header

Компонент уже интегрирован в `Header.vue` и доступен:
- В десктопном меню пользователя (над "Настройки аккаунта")
- В мобильном меню (над "Настройки аккаунта")

## Поддерживаемые валюты

Система поддерживает все валюты, которые возвращает внешний API:
- USD (United States Dollar)
- EUR (Euro)
- RUB (Russian Ruble)
- UZS (Uzbekistani Som)
- CNY (Chinese Yuan)
- HKD (Hong Kong Dollar)
- NZD (New Zealand Dollar)
- GBP (British Pound Sterling)
- AUD (Australian Dollar)
- CAD (Canadian Dollar)
- CHF (Swiss Franc)
- JPY (Japanese Yen)

## Обработка ошибок

- При недоступности внешнего API система использует последние сохраненные курсы
- При отсутствии курсов в базе показываются базовые валюты (USD, EUR, RUB, UZS)
- Все ошибки логируются в Laravel logs

## Тестирование

### Тест API endpoints

```bash
# Получение курсов
curl -H "Authorization: Bearer YOUR_TOKEN" http://localhost/api/currencies

# Обновление курсов
curl -H "Authorization: Bearer YOUR_TOKEN" http://localhost/api/currencies/fetch

# Конвертация
curl -X POST -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"amount": 100, "from_currency": "USD", "to_currency": "EUR"}' \
  http://localhost/api/currencies/convert
```

### Тест команды

```bash
cd b2b-project/backend
php artisan currency:update-rates
``` 