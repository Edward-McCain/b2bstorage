#!/bin/bash

# Скрипт переноса данных с тестового сервера на продакшн
# Тестовый сервер: 5.35.85.110
# Продакшн сервер: 45.92.173.142

echo "🔄 Начинаем перенос данных с тестового сервера на продакшн..."

# Создаем временную папку
TEMP_DIR="/tmp/db_migration_$(date +%Y%m%d_%H%M%S)"
mkdir -p $TEMP_DIR
cd $TEMP_DIR

echo "📦 Создание дампа базы данных с тестового сервера..."

# Создаем дамп с тестового сервера
PGPASSWORD="B2B_Storage_2024!" pg_dump -h 5.35.85.110 -U b2buser -d b2bstorage -F c -f test_server_dump.dump

if [ $? -eq 0 ]; then
    echo "✅ Дамп тестового сервера создан успешно"
else
    echo "❌ Ошибка при создании дампа с тестового сервера"
    exit 1
fi

echo "📊 Размер дампа: $(du -h test_server_dump.dump | cut -f1)"

echo "🗄️ Восстановление данных на продакшн сервере..."

# Очищаем существующую базу данных на продакшне
echo "🧹 Очистка существующей базы данных..."
PGPASSWORD="B2B_sklad2025" psql -h localhost -U b2buser -d b2bstorage -c "DROP SCHEMA public CASCADE; CREATE SCHEMA public;"

# Восстанавливаем данные на продакшн сервере
PGPASSWORD="B2B_sklad2025" pg_restore -h localhost -U b2buser -d b2bstorage -c -v test_server_dump.dump

if [ $? -eq 0 ]; then
    echo "✅ Данные успешно восстановлены на продакшн сервере"
else
    echo "❌ Ошибка при восстановлении данных"
    exit 1
fi

# Проверяем количество таблиц
echo "📋 Проверка восстановленных таблиц..."
PGPASSWORD="B2B_sklad2025" psql -h localhost -U b2buser -d b2bstorage -c "
SELECT 
    schemaname,
    tablename,
    tableowner
FROM pg_tables 
WHERE schemaname = 'public' 
ORDER BY tablename;
"

# Проверяем количество записей в основных таблицах
echo "📊 Статистика данных:"
PGPASSWORD="B2B_sklad2025" psql -h localhost -U b2buser -d b2bstorage -c "
SELECT 
    'users' as table_name, COUNT(*) as record_count FROM users
UNION ALL
SELECT 
    'products' as table_name, COUNT(*) as record_count FROM products
UNION ALL
SELECT 
    'categories' as table_name, COUNT(*) as record_count FROM categories
UNION ALL
SELECT 
    'inventories' as table_name, COUNT(*) as record_count FROM inventories
UNION ALL
SELECT 
    'receipts' as table_name, COUNT(*) as record_count FROM receipts
ORDER BY table_name;
"

# Очищаем временные файлы
echo "🧹 Очистка временных файлов..."
rm -rf $TEMP_DIR

echo "✅ Перенос данных завершен успешно!"
echo "📝 Данные перенесены с тестового сервера (5.35.85.110) на продакшн сервер (45.92.173.142)" 