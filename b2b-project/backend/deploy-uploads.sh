#!/bin/bash

# Скрипт для деплоя папок с загрузками на сервер
# Этот скрипт копирует папки с загруженными файлами на сервер

echo "🚀 Начинаем деплой загрузок на сервер..."

# Конфигурация
SERVER_HOST="your-server.com"
SERVER_USER="your-username"
SERVER_PATH="/var/www/b2b-project/backend"

# Папки для копирования
UPLOAD_DIRS=(
    "storage/app/public/uploads/avatars"
    "storage/app/public/uploads/products"
)

# Создаем папки на сервере если их нет
for dir in "${UPLOAD_DIRS[@]}"; do
    echo "📁 Создаем папку $dir на сервере..."
    ssh $SERVER_USER@$SERVER_HOST "mkdir -p $SERVER_PATH/$dir"
done

# Копируем файлы
for dir in "${UPLOAD_DIRS[@]}"; do
    if [ -d "$dir" ]; then
        echo "📤 Копируем $dir на сервер..."
        rsync -avz --delete "$dir/" "$SERVER_USER@$SERVER_HOST:$SERVER_PATH/$dir/"
    else
        echo "⚠️  Папка $dir не найдена, пропускаем..."
    fi
done

echo "✅ Деплой загрузок завершен!"

# Обновляем права доступа на сервере
echo "🔐 Обновляем права доступа..."
ssh $SERVER_USER@$SERVER_HOST "chmod -R 755 $SERVER_PATH/storage/app/public/uploads"

echo "🎉 Все готово! Загрузки успешно скопированы на сервер." 