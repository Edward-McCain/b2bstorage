#!/bin/bash

# Подготовка директорий на сервере для SFTP загрузки

echo "📁 Подготовка директорий на сервере..."

# Создание директории для скриптов настройки
echo "📦 Создание директории для скриптов настройки..."
mkdir -p /root/server-setup
chmod 755 /root/server-setup

# Создание директории для Laravel приложения
echo "🚀 Создание директории для Laravel приложения..."
mkdir -p /var/www/b2bstorage.ru
chmod -R 755 /var/www/b2bstorage.ru

# Создание директории для логов
echo "📋 Создание директории для логов..."
mkdir -p /var/log/b2bstorage
chmod -R 755 /var/log/b2bstorage

# Создание временной директории для загрузок
echo "📥 Создание временной директории для загрузок..."
mkdir -p /tmp/b2b-uploads
chmod 777 /tmp/b2b-uploads

echo "✅ Директории подготовлены!"
echo ""
echo "📋 Доступные директории для SFTP:"
echo "- Скрипты настройки: /root/server-setup"
echo "- Laravel приложение: /var/www/b2bstorage.ru"
echo "- Логи: /var/log/b2bstorage"
echo "- Временные файлы: /tmp/b2b-uploads"
echo ""
echo "🔧 Следующие шаги:"
echo "1. Загрузите скрипты в /root/server-setup"
echo "2. Запустите setup-server.sh"
echo "3. Загрузите Laravel код в /var/www/b2bstorage.ru"
