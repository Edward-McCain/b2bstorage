#!/bin/bash

# Скрипт инициализации продакшн сервера после деплоя
# Запускается на сервере после первого деплоя

echo "🚀 Инициализация продакшн сервера..."

# Переходим в директорию backend
cd /var/www/b2bstorage-backend

# Генерируем APP_KEY
echo "🔑 Генерация APP_KEY..."
php artisan key:generate

# Запускаем миграции
echo "🗄️ Запуск миграций..."
php artisan migrate --force

# Очищаем кеши
echo "🧹 Очистка кешей..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Кешируем конфигурации для продакшна
echo "⚡ Кеширование конфигураций..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Создаем символическую ссылку для storage
echo "🔗 Создание символической ссылки storage..."
php artisan storage:link

# Устанавливаем правильные права доступа
echo "🔐 Установка прав доступа..."
chown -R www-data:www-data /var/www/b2bstorage-backend
chown -R www-data:www-data /var/www/b2bstorage-frontend
chmod -R 755 /var/www/b2bstorage-backend
chmod -R 755 /var/www/b2bstorage-frontend
chmod -R 775 /var/www/b2bstorage-backend/storage
chmod -R 775 /var/www/b2bstorage-backend/bootstrap/cache

# Перезапускаем сервисы
echo "🔄 Перезапуск сервисов..."
systemctl reload nginx
systemctl reload php8.4-fpm

echo "✅ Инициализация продакшн сервера завершена!"
echo "🌐 Сайт доступен по адресу: http://45.92.173.142" 