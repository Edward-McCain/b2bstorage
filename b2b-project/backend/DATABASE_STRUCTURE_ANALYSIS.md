# Анализ структуры базы данных

## Выполните SQL запрос для получения структуры:

```sql
-- Выполните содержимое файла get_all_tables_structure.sql
```

## Ожидаемые таблицы и их структура:

### Основные таблицы товаров и складов:
1. **products_sklad** - товары
2. **warehouses** - склады
3. **categories** - категории
4. **subcategories** - подкатегории

### Таблицы документов:
5. **receipts** - оприходования
6. **receipt_positions** - позиции оприходований
7. **receipt_files** - файлы оприходований
8. **write_offs** - списания
9. **write_off_positions** - позиции списаний
10. **write_off_files** - файлы списаний
11. **inventories** - инвентаризации
12. **inventory_items** - позиции инвентаризации
13. **inventory_files** - файлы инвентаризации

### Новые таблицы для остатков:
14. **product_balances** - остатки товаров на складах
15. **product_operations** - операции с товарами
16. **product_transfers** - перемещения товаров
17. **product_transfer_positions** - позиции перемещений

### Системные таблицы:
18. **users** - пользователи
19. **personal_access_tokens** - токены доступа
20. **sessions** - сессии
21. **cache** - кэш
22. **jobs** - задачи
23. **migrations** - миграции

## Проверка связей:

### Связи товаров:
- products_sklad.category -> categories.category_id
- products_sklad.subcategory -> subcategories.subcategory_id
- product_balances.product_id -> products_sklad.id
- product_operations.product_id -> products_sklad.id
- receipt_positions.product_id -> products_sklad.id
- write_off_positions.product_id -> products_sklad.id
- inventory_items.product_id -> products_sklad.id
- product_transfer_positions.product_id -> products_sklad.id

### Связи складов:
- product_balances.warehouse_id -> warehouses.id
- product_operations.warehouse_id -> warehouses.id
- receipts.warehouse -> warehouses.id
- write_offs.warehouse -> warehouses.id
- inventories.warehouse -> warehouses.id
- product_transfers.from_warehouse_id -> warehouses.id
- product_transfers.to_warehouse_id -> warehouses.id

### Связи документов:
- receipt_positions.receipt_id -> receipts.id
- receipt_files.receipt_id -> receipts.id
- write_off_positions.write_off_id -> write_offs.id
- write_off_files.write_off_id -> write_offs.id
- inventory_items.inventory_id -> inventories.id
- inventory_files.inventory_id -> inventories.id
- product_transfer_positions.transfer_id -> product_transfers.id

### Связи пользователей:
- receipts.user_id -> users.id
- write_offs.user_id -> users.id
- inventories.user_id -> users.id
- product_transfers.created_by -> users.id
- product_operations.created_by -> users.id

## Потенциальные проблемы для проверки:

### 1. Несоответствие типов данных:
- Проверить, что все ID имеют тип BIGINT
- Проверить, что количества имеют тип INTEGER
- Проверить, что даты имеют тип TIMESTAMP

### 2. Отсутствующие внешние ключи:
- receipts.warehouse -> warehouses.id
- write_offs.warehouse -> warehouses.id
- inventories.warehouse -> warehouses.id
- receipts.user_id -> users.id
- write_offs.user_id -> users.id
- inventories.user_id -> users.id

### 3. Отсутствующие индексы:
- product_balances(product_id, warehouse_id)
- product_operations(product_id, warehouse_id)
- product_operations(operation_type)
- product_operations(reference_type, reference_id)

### 4. Проблемы с уникальностью:
- product_balances(product_id, warehouse_id) должен быть уникальным
- receipts.number должен быть уникальным для пользователя
- write_offs.number должен быть уникальным для пользователя

## SQL запросы для исправления проблем:

### 1. Добавление недостающих внешних ключей:
```sql
-- Добавить внешние ключи для складов
ALTER TABLE receipts 
ADD CONSTRAINT fk_receipts_warehouse 
FOREIGN KEY (warehouse) REFERENCES warehouses(id) ON DELETE CASCADE;

ALTER TABLE write_offs 
ADD CONSTRAINT fk_write_offs_warehouse 
FOREIGN KEY (warehouse) REFERENCES warehouses(id) ON DELETE CASCADE;

ALTER TABLE inventories 
ADD CONSTRAINT fk_inventories_warehouse 
FOREIGN KEY (warehouse) REFERENCES warehouses(id) ON DELETE CASCADE;

-- Добавить внешние ключи для пользователей
ALTER TABLE receipts 
ADD CONSTRAINT fk_receipts_user_id 
FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;

ALTER TABLE write_offs 
ADD CONSTRAINT fk_write_offs_user_id 
FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;

ALTER TABLE inventories 
ADD CONSTRAINT fk_inventories_user_id 
FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;
```

### 2. Добавление недостающих индексов:
```sql
-- Индексы для быстрого поиска
CREATE INDEX IF NOT EXISTS idx_receipts_user_id ON receipts(user_id);
CREATE INDEX IF NOT EXISTS idx_receipts_warehouse ON receipts(warehouse);
CREATE INDEX IF NOT EXISTS idx_write_offs_user_id ON write_offs(user_id);
CREATE INDEX IF NOT EXISTS idx_write_offs_warehouse ON write_offs(warehouse);
CREATE INDEX IF NOT EXISTS idx_inventories_user_id ON inventories(user_id);
CREATE INDEX IF NOT EXISTS idx_inventories_warehouse ON inventories(warehouse);
```

### 3. Добавление уникальных ограничений:
```sql
-- Уникальные номера документов для пользователя
ALTER TABLE receipts 
ADD CONSTRAINT unique_receipt_number_per_user 
UNIQUE (user_id, number);

ALTER TABLE write_offs 
ADD CONSTRAINT unique_write_off_number_per_user 
UNIQUE (user_id, number);
```

## После получения структуры:

1. Сравните полученную структуру с ожидаемой
2. Проверьте наличие всех необходимых таблиц
3. Проверьте правильность типов данных
4. Проверьте наличие внешних ключей
5. Проверьте наличие индексов
6. Выполните необходимые SQL запросы для исправления проблем 