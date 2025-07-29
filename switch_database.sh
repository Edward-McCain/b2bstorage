#!/bin/bash

# Скрипт для переключения между локальной и серверной базой данных
# Использование: ./switch_database.sh [local|server]

ENV_FILE="b2b-project/backend/.env"
MODE=${1:-"local"}

if [ ! -f "$ENV_FILE" ]; then
    echo "❌ Файл .env не найден!"
    echo "Создайте файл .env из env_example.txt"
    exit 1
fi

case $MODE in
    "local")
        echo "🔄 Переключение на локальную базу данных..."
        sed -i '' 's/LOCAL_DB=false/LOCAL_DB=true/g' "$ENV_FILE"
        echo "✅ Переключено на локальную базу данных"
        ;;
    "server")
        echo "🔄 Переключение на серверную базу данных..."
        sed -i '' 's/LOCAL_DB=true/LOCAL_DB=false/g' "$ENV_FILE"
        echo "✅ Переключено на серверную базу данных"
        ;;
    *)
        echo "❌ Неверный режим!"
        echo "Использование: ./switch_database.sh [local|server]"
        exit 1
        ;;
esac

# Очищаем кэш Laravel
echo "🧹 Очистка кэша..."
cd b2b-project/backend
php artisan config:clear
php artisan cache:clear

echo ""
echo "📋 Текущие настройки:"
if grep -q "LOCAL_DB=true" "$ENV_FILE"; then
    echo "📍 Режим: Локальная база данных"
    echo "🏠 Host: 127.0.0.1"
    echo "🗄️  Database: b2bs_local"
else
    echo "📍 Режим: Серверная база данных"
    echo "🌐 Host: 5.35.85.110"
    echo "🗄️  Database: b2bstorage"
fi

echo ""
echo "🔍 Для проверки подключения выполните:"
echo "php check_database_connection.php" 