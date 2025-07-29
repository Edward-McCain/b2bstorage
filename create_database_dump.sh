#!/bin/bash

# Скрипт для создания дампа базы данных с сервера
# Использование: ./create_database_dump.sh [SERVER_IP] [DB_NAME] [DB_USER]

SERVER_IP=${1:-"5.35.85.110"}
DB_NAME=${2:-"b2bstorage"}
DB_USER=${3:-"b2buser"}

echo "Создание дампа базы данных с сервера..."
echo "Сервер: $SERVER_IP"
echo "База данных: $DB_NAME"
echo "Пользователь: $DB_USER"

# Создаем дамп базы данных
pg_dump -h $SERVER_IP -U $DB_USER -d $DB_NAME --no-owner --no-privileges > database_dump.sql

if [ $? -eq 0 ]; then
    echo "✅ Дамп базы данных успешно создан: database_dump.sql"
    echo "Размер файла: $(du -h database_dump.sql | cut -f1)"
else
    echo "❌ Ошибка при создании дампа базы данных"
    exit 1
fi 