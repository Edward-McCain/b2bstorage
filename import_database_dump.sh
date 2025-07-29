#!/bin/bash

# Скрипт для импорта дампа базы данных в локальную базу
# Использование: ./import_database_dump.sh [DB_NAME] [DB_USER] [DUMP_FILE]

DB_NAME=${1:-"b2bs_local"}
DB_USER=${2:-"b2bs_user"}
DUMP_FILE=${3:-"database_dump.sql"}

echo "Импорт дампа базы данных в локальную базу..."
echo "База данных: $DB_NAME"
echo "Пользователь: $DB_USER"
echo "Файл дампа: $DUMP_FILE"

# Проверяем существование файла дампа
if [ ! -f "$DUMP_FILE" ]; then
    echo "❌ Файл дампа $DUMP_FILE не найден!"
    echo "Сначала создайте дамп с сервера:"
    echo "./create_database_dump.sh [SERVER_IP] [DB_NAME] [DB_USER]"
    exit 1
fi

# Проверяем подключение к базе данных
if ! psql -h localhost -U $DB_USER -d $DB_NAME -c "SELECT 1;" &> /dev/null; then
    echo "❌ Не удается подключиться к базе данных!"
    echo "Убедитесь, что локальная база данных настроена:"
    echo "./setup_local_database.sh [DB_NAME] [DB_USER] [DB_PASSWORD]"
    exit 1
fi

echo "Начинаем импорт дампа..."
psql -h localhost -U $DB_USER -d $DB_NAME < "$DUMP_FILE"

if [ $? -eq 0 ]; then
    echo "✅ Дамп базы данных успешно импортирован!"
    echo ""
    echo "Проверка импорта..."
    psql -h localhost -U $DB_USER -d $DB_NAME -c "\dt" | head -20
else
    echo "❌ Ошибка при импорте дампа базы данных"
    exit 1
fi 