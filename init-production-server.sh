#!/bin/bash

# Скрипт инициализации продакшн сервера после деплоя
# Запускается на сервере после первого деплоя

echo "🚀 Инициализация продакшн сервера..."

# Переходим в директорию backend
cd /var/www/b2bstorage-backend

# Создание .env файла для PostgreSQL
echo "⚙️ Создание .env файла..."
cat > .env << 'EOF'
APP_NAME="B2B Sklad"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=http://45.92.173.142

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=b2bstorage
DB_USERNAME=b2buser
DB_PASSWORD=B2B_sklad2025

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

VITE_APP_NAME="${APP_NAME}"
VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
EOF

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