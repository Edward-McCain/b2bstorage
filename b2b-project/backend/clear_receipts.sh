#!/bin/bash

# Скрипт для очистки данных из таблиц оприходований
# Убедитесь, что у вас есть доступ к базе данных

echo "Очистка данных из таблиц оприходований..."

# Проверяем, что файл SQL существует
if [ ! -f "clear_receipts_data.sql" ]; then
    echo "Ошибка: файл clear_receipts_data.sql не найден"
    exit 1
fi

# Выполняем SQL запросы
# Замените параметры подключения к вашей базе данных
psql -h localhost -U postgres -d your_database_name -f clear_receipts_data.sql

echo "Очистка завершена!" 