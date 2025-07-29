#!/bin/bash

# Полный скрипт настройки локальной базы данных
echo "🚀 Настройка локальной базы данных для разработки"
echo "=================================================="

# Проверяем наличие PostgreSQL
if ! command -v psql &> /dev/null; then
    echo "❌ PostgreSQL не установлен!"
    echo "Установите PostgreSQL:"
    echo "macOS: brew install postgresql"
    echo "Ubuntu: sudo apt-get install postgresql postgresql-contrib"
    exit 1
fi

# Делаем скрипты исполняемыми
echo "📝 Настройка скриптов..."
chmod +x setup_local_database.sh
chmod +x create_database_dump.sh
chmod +x import_database_dump.sh

# Настраиваем локальную базу данных
echo "🗄️  Настройка локальной базы данных..."
./setup_local_database.sh b2bs_local b2bs_user b2bs_password

if [ $? -ne 0 ]; then
    echo "❌ Ошибка при настройке локальной базы данных"
    exit 1
fi

# Создаем дамп с сервера
echo "📥 Создание дампа с сервера..."
./create_database_dump.sh

if [ $? -ne 0 ]; then
    echo "❌ Ошибка при создании дампа"
    exit 1
fi

# Импортируем дамп
echo "📤 Импорт дампа в локальную базу..."
./import_database_dump.sh b2bs_local b2bs_user database_dump.sql

if [ $? -ne 0 ]; then
    echo "❌ Ошибка при импорте дампа"
    exit 1
fi

# Настраиваем Laravel
echo "⚙️  Настройка Laravel..."
if [ ! -f "b2b-project/backend/.env" ]; then
    cp env_example.txt b2b-project/backend/.env
    echo "✅ Файл .env создан"
else
    echo "⚠️  Файл .env уже существует"
fi

# Генерируем ключ приложения
cd b2b-project/backend
php artisan key:generate --force

# Очищаем кэш
php artisan config:clear
php artisan cache:clear

echo ""
echo "✅ Настройка завершена успешно!"
echo ""
echo "📋 Следующие шаги:"
echo "1. Запустите сервер разработки:"
echo "   cd b2b-project/backend && php artisan serve"
echo ""
echo "2. Для переключения между локальной и серверной БД:"
echo "   - Локальная: LOCAL_DB=true в .env"
echo "   - Серверная: LOCAL_DB=false в .env"
echo ""
echo "3. Для обновления локальной БД:"
echo "   ./create_database_dump.sh && ./import_database_dump.sh" 