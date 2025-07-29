#!/bin/bash

# Скрипт для настройки локальной базы данных PostgreSQL
# Использование: ./setup_local_database.sh [DB_NAME] [DB_USER] [DB_PASSWORD]

DB_NAME=${1:-"b2bs_local"}
DB_USER=${2:-"b2bs_user"}
DB_PASSWORD=${3:-"b2bs_password"}

echo "Настройка локальной базы данных PostgreSQL..."
echo "База данных: $DB_NAME"
echo "Пользователь: $DB_USER"

# Проверяем, установлен ли PostgreSQL
if ! command -v psql &> /dev/null; then
    echo "❌ PostgreSQL не установлен. Установите PostgreSQL сначала."
    echo "Для macOS: brew install postgresql"
    echo "Для Ubuntu: sudo apt-get install postgresql postgresql-contrib"
    exit 1
fi

# Проверяем, запущен ли PostgreSQL
if ! pg_isready &> /dev/null; then
    echo "⚠️  PostgreSQL не запущен. Запускаем..."
    if [[ "$OSTYPE" == "darwin"* ]]; then
        brew services start postgresql
    else
        sudo systemctl start postgresql
    fi
fi

# Создаем пользователя базы данных
echo "Создание пользователя базы данных..."
psql -d postgres -c "CREATE USER $DB_USER WITH PASSWORD '$DB_PASSWORD';" 2>/dev/null || echo "Пользователь уже существует"

# Создаем базу данных
echo "Создание базы данных..."
psql -d postgres -c "CREATE DATABASE $DB_NAME OWNER $DB_USER;" 2>/dev/null || echo "База данных уже существует"

# Предоставляем права пользователю
echo "Настройка прав доступа..."
psql -d postgres -c "GRANT ALL PRIVILEGES ON DATABASE $DB_NAME TO $DB_USER;"
psql -d postgres -c "ALTER USER $DB_USER CREATEDB;"

echo "✅ Локальная база данных настроена успешно!"
echo ""
echo "Данные для подключения:"
echo "Host: localhost"
echo "Port: 5432"
echo "Database: $DB_NAME"
echo "Username: $DB_USER"
echo "Password: $DB_PASSWORD"
echo ""
echo "Для импорта дампа выполните:"
echo "psql -h localhost -U $DB_USER -d $DB_NAME < database_dump.sql" 