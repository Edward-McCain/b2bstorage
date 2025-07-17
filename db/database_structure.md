# Структура базы данных B2B Storage

## Обзор таблиц

### Основные таблицы

#### users
Основная таблица пользователей системы
- `id` - уникальный идентификатор
- `user_id` - UUID пользователя
- `glink` - глобальная ссылка
- `role` - роль пользователя (0 - пользователь, 1 - администратор)
- `acc_type` - тип аккаунта (individual/company)
- `status_subscription` - статус подписки
- `first_name`, `last_name`, `user_name` - данные пользователя
- `position` - должность
- `email` - email
- `phone_number` - номер телефона
- `verified_email` - подтвержден ли email
- `phone_ok` - подтвержден ли телефон
- `password` - хеш пароля
- `fcm_token` - токен для push уведомлений
- `timezone` - часовой пояс
- `last_logged_in` - последний вход
- `is_online` - онлайн статус
- `language` - язык интерфейса
- `messages_language` - язык сообщений
- `country`, `city` - страна и город
- `avatar_url` - URL аватара
- `banned` - заблокирован ли пользователь
- `currency` - валюта по умолчанию
- `balance`, `ref_balance`, `demo_balance`, `bonus_balance` - балансы
- `inn` - ИНН
- `comp_pinfl` - ПИНФЛ компании
- `comp_state` - статус компании
- `company_type` - тип компании
- `company_name` - название компании
- `company_description` - описание компании
- `company_rating` - рейтинг компании
- `com_address` - адрес компании
- `com_leader` - руководитель
- `comp_logo_url` - логотип компании
- `comp_phone` - телефон компании
- `comp_mail` - email компании
- `comp_website_url` - сайт компании
- `company_link` - ссылка на компанию
- `company_statuses` - статусы компании
- `comp_verified` - верифицирована ли компания
- `comp_tariff` - тариф компании
- `deal_seen` - просмотренные сделки
- `notification_*` - настройки уведомлений
- `is_active` - активен ли аккаунт
- `catch` - дополнительная информация
- `reg_date` - дата регистрации
- `moderated` - прошел ли модерацию
- `gen_key` - генерационный ключ
- `referer` - реферер
- `invite_link` - пригласительная ссылка
- `deleted` - удален ли аккаунт
- `created_at`, `updated_at` - даты создания и обновления

#### categories
Категории товаров
- `id` - уникальный идентификатор
- `name` - название категории
- `description` - описание
- `parent_id` - ID родительской категории
- `created_at`, `updated_at` - даты создания и обновления

#### subcategories
Подкатегории товаров
- `id` - уникальный идентификатор
- `name` - название подкатегории
- `description` - описание
- `category_id` - ID родительской категории
- `created_at`, `updated_at` - даты создания и обновления

#### products
Товары
- `id` - уникальный идентификатор
- `name` - название товара
- `description` - описание
- `category_id` - ID категории
- `subcategory_id` - ID подкатегории
- `sku` - артикул
- `barcode` - штрихкод
- `price` - цена
- `currency` - валюта
- `created_at`, `updated_at` - даты создания и обновления

#### products_sklad
Товары на складах
- `id` - уникальный идентификатор
- `product_id` - ID товара
- `warehouse_id` - ID склада
- `quantity` - количество
- `price` - цена
- `created_at`, `updated_at` - даты создания и обновления

#### warehouses
Склады
- `id` - уникальный идентификатор
- `name` - название склада
- `address` - адрес
- `description` - описание
- `user_id` - ID владельца
- `created_at`, `updated_at` - даты создания и обновления

#### receipts
Поступления товаров
- `id` - уникальный идентификатор
- `number` - номер поступления
- `date` - дата поступления
- `warehouse_id` - ID склада
- `supplier_id` - ID поставщика
- `total_amount` - общая сумма
- `currency` - валюта
- `status` - статус
- `user_id` - ID пользователя
- `created_at`, `updated_at` - даты создания и обновления

#### receipt_positions
Позиции поступлений
- `id` - уникальный идентификатор
- `receipt_id` - ID поступления
- `product_id` - ID товара
- `quantity` - количество
- `price` - цена
- `total` - общая сумма
- `created_at`, `updated_at` - даты создания и обновления

#### write_offs
Списания товаров
- `id` - уникальный идентификатор
- `number` - номер списания
- `date` - дата списания
- `warehouse_id` - ID склада
- `reason` - причина списания
- `total_amount` - общая сумма
- `currency` - валюта
- `status` - статус
- `user_id` - ID пользователя
- `created_at`, `updated_at` - даты создания и обновления

#### write_off_positions
Позиции списаний
- `id` - уникальный идентификатор
- `write_off_id` - ID списания
- `product_id` - ID товара
- `quantity` - количество
- `price` - цена
- `total` - общая сумма
- `created_at`, `updated_at` - даты создания и обновления

#### inventories
Инвентаризации
- `id` - уникальный идентификатор
- `number` - номер инвентаризации
- `date` - дата инвентаризации
- `warehouse_id` - ID склада
- `status` - статус
- `user_id` - ID пользователя
- `created_at`, `updated_at` - даты создания и обновления

#### inventory_items
Позиции инвентаризации
- `id` - уникальный идентификатор
- `inventory_id` - ID инвентаризации
- `product_id` - ID товара
- `expected_quantity` - ожидаемое количество
- `actual_quantity` - фактическое количество
- `difference` - разница
- `created_at`, `updated_at` - даты создания и обновления

#### product_transfers
Перемещения товаров
- `id` - уникальный идентификатор
- `number` - номер перемещения
- `date` - дата перемещения
- `from_warehouse_id` - ID склада откуда
- `to_warehouse_id` - ID склада куда
- `status` - статус
- `user_id` - ID пользователя
- `created_at`, `updated_at` - даты создания и обновления

#### product_transfer_positions
Позиции перемещений
- `id` - уникальный идентификатор
- `transfer_id` - ID перемещения
- `product_id` - ID товара
- `quantity` - количество
- `created_at`, `updated_at` - даты создания и обновления

#### product_operations
Операции с товарами
- `id` - уникальный идентификатор
- `product_id` - ID товара
- `warehouse_id` - ID склада
- `operation_type` - тип операции (receipt, write_off, transfer_in, transfer_out)
- `quantity` - количество
- `price` - цена
- `document_id` - ID документа
- `document_type` - тип документа
- `user_id` - ID пользователя
- `created_at` - дата создания

#### product_balances
Остатки товаров
- `id` - уникальный идентификатор
- `product_id` - ID товара
- `warehouse_id` - ID склада
- `quantity` - количество
- `average_price` - средняя цена
- `total_value` - общая стоимость
- `created_at`, `updated_at` - даты создания и обновления

#### product_images
Изображения товаров
- `id` - уникальный идентификатор
- `product_id` - ID товара
- `image_url` - URL изображения
- `is_main` - является ли главным
- `created_at`, `updated_at` - даты создания и обновления

### Системные таблицы

#### sessions
Сессии пользователей
- `id` - уникальный идентификатор
- `user_id` - ID пользователя
- `token` - токен сессии
- `expires_at` - время истечения
- `created_at`, `updated_at` - даты создания и обновления

#### personal_access_tokens
Токены доступа
- `id` - уникальный идентификатор
- `user_id` - ID пользователя
- `name` - название токена
- `token` - токен
- `abilities` - возможности
- `last_used_at` - последнее использование
- `expires_at` - время истечения
- `created_at`, `updated_at` - даты создания и обновления

#### migrations
Миграции базы данных
- `id` - уникальный идентификатор
- `migration` - название миграции
- `batch` - номер батча
- `created_at` - дата создания

#### failed_jobs
Неудачные задачи
- `id` - уникальный идентификатор
- `uuid` - UUID задачи
- `connection` - соединение
- `queue` - очередь
- `payload` - данные
- `exception` - исключение
- `failed_at` - дата неудачи

#### cache
Кеш
- `key` - ключ
- `value` - значение
- `expiration` - время истечения

#### cache_locks
Блокировки кеша
- `key` - ключ
- `owner` - владелец
- `expiration` - время истечения

#### password_reset_tokens
Токены сброса пароля
- `email` - email
- `token` - токен
- `created_at` - дата создания

#### modifications
Модификации
- `id` - уникальный идентификатор
- `table` - таблица
- `record_id` - ID записи
- `action` - действие
- `old_values` - старые значения
- `new_values` - новые значения
- `user_id` - ID пользователя
- `created_at` - дата создания

### Файловые таблицы

#### receipt_files
Файлы поступлений
- `id` - уникальный идентификатор
- `receipt_id` - ID поступления
- `file_url` - URL файла
- `file_name` - название файла
- `file_size` - размер файла
- `created_at`, `updated_at` - даты создания и обновления

#### write_off_files
Файлы списаний
- `id` - уникальный идентификатор
- `write_off_id` - ID списания
- `file_url` - URL файла
- `file_name` - название файла
- `file_size` - размер файла
- `created_at`, `updated_at` - даты создания и обновления

#### inventory_files
Файлы инвентаризации
- `id` - уникальный идентификатор
- `inventory_id` - ID инвентаризации
- `file_url` - URL файла
- `file_name` - название файла
- `file_size` - размер файла
- `created_at`, `updated_at` - даты создания и обновления

## Связи между таблицами

### Основные связи

1. **users** → **warehouses** (один ко многим)
   - Пользователь может иметь несколько складов

2. **categories** → **subcategories** (один ко многим)
   - Категория может иметь несколько подкатегорий

3. **categories** → **products** (один ко многим)
   - Категория может содержать несколько товаров

4. **subcategories** → **products** (один ко многим)
   - Подкатегория может содержать несколько товаров

5. **products** → **products_sklad** (один ко многим)
   - Товар может быть на нескольких складах

6. **warehouses** → **products_sklad** (один ко многим)
   - Склад может содержать несколько товаров

7. **users** → **receipts** (один ко многим)
   - Пользователь может создавать несколько поступлений

8. **warehouses** → **receipts** (один ко многим)
   - Склад может иметь несколько поступлений

9. **receipts** → **receipt_positions** (один ко многим)
   - Поступление может содержать несколько позиций

10. **products** → **receipt_positions** (один ко многим)
    - Товар может быть в нескольких поступлениях

### Аналогичные связи для списаний, инвентаризации и перемещений

## Индексы и оптимизация

### Рекомендуемые индексы

1. `users.email` - для быстрого поиска по email
2. `users.role` - для фильтрации по роли
3. `products.sku` - для поиска по артикулу
4. `products.category_id` - для фильтрации по категории
5. `products_sklad.warehouse_id` - для поиска товаров на складе
6. `receipts.date` - для поиска по дате
7. `receipts.warehouse_id` - для фильтрации по складу
8. `product_operations.created_at` - для истории операций

## Статистика данных

### Количество записей (примерно)

- **users**: ~1,250 пользователей
- **categories**: ~156 категорий
- **subcategories**: ~5,882 подкатегории
- **products**: ~3,420 товаров
- **warehouses**: ~15 складов
- **receipts**: ~87 поступлений
- **write_offs**: ~19 списаний
- **inventories**: ~26 инвентаризаций
- **product_transfers**: ~15 перемещений

## Особенности системы

1. **Роли пользователей**: 0 - обычный пользователь, 1 - администратор
2. **Мультивалютность**: поддержка RUB, USD, EUR, UZS
3. **Мультиязычность**: поддержка русского языка
4. **Файловые вложения**: для поступлений, списаний и инвентаризации
5. **История операций**: все изменения товаров отслеживаются
6. **Автоматические остатки**: расчет остатков на основе операций
7. **Верификация**: система верификации компаний и пользователей
8. **Уведомления**: поддержка email, SMS и push уведомлений 