#!/bin/bash

# Развертывание Laravel приложения для B2B Storage

echo "🚀 Развертывание Laravel приложения..."

# Переключение в директорию приложения
cd /var/www/b2bstorage.ru

# Клонирование репозитория (замените на ваш репозиторий)
echo "📥 Клонирование репозитория..."
# git clone https://github.com/your-username/b2bstorage.git .
# Или создание нового Laravel проекта
composer create-project laravel/laravel . --prefer-dist

# Установка зависимостей
echo "📦 Установка PHP зависимостей..."
composer install --no-dev --optimize-autoloader

# Установка Node.js зависимостей
echo "📦 Установка Node.js зависимостей..."
npm install
npm run build

# Настройка прав доступа
echo "🔐 Настройка прав доступа..."
chown -R b2buser:www-data /var/www/b2bstorage.ru
chmod -R 755 /var/www/b2bstorage.ru
chmod -R 775 /var/www/b2bstorage.ru/storage
chmod -R 775 /var/www/b2bstorage.ru/bootstrap/cache

# Создание .env файла
echo "⚙️ Создание .env файла..."
cp .env.example .env

# Настройка .env файла
cat > .env << 'EOF'
APP_NAME="B2B Storage"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://b2bstorage.ru
APP_TIMEZONE=Europe/Moscow
APP_LOCALE=ru

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=b2bstorage
DB_USERNAME=b2buser
DB_PASSWORD=B2B_Storage_2024!

BROADCAST_DRIVER=pusher
CACHE_DRIVER=redis
FILESYSTEM_DISK=local
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
SESSION_LIFETIME=120

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=B2B_Redis_2024!
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@b2bstorage.ru"
MAIL_FROM_NAME="${APP_NAME}"

PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_HOST=127.0.0.1
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

# Генерация APP_KEY
echo "🔑 Генерация APP_KEY..."
php artisan key:generate

# Выполнение миграций
echo "🗄️ Выполнение миграций..."
php artisan migrate --force

# Очистка кэша
echo "🧹 Очистка кэша..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Оптимизация для продакшена
echo "⚡ Оптимизация для продакшена..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Установка Laravel WebSockets
echo "🔌 Установка Laravel WebSockets..."
composer require beyondcode/laravel-websockets

# Публикация конфигурации WebSockets
php artisan vendor:publish --provider="BeyondCode\LaravelWebSockets\WebSocketsServiceProvider" --tag="migrations"
php artisan vendor:publish --provider="BeyondCode\LaravelWebSockets\WebSocketsServiceProvider" --tag="config"

# Выполнение миграций WebSockets
php artisan migrate

# Создание конфигурации WebSockets
cat > config/websockets.php << 'EOF'
<?php

return [
    'dashboard' => [
        'port' => env('LARAVEL_WEBSOCKETS_PORT', 6001),
    ],

    'apps' => [
        [
            'id' => env('PUSHER_APP_ID'),
            'name' => env('APP_NAME'),
            'key' => env('PUSHER_APP_KEY'),
            'secret' => env('PUSHER_APP_SECRET'),
            'path' => env('PUSHER_APP_PATH'),
            'capacity' => null,
            'enable_client_messages' => false,
            'enable_statistics' => true,
        ],
    ],

    'ssl' => [
        'local_cert' => env('LARAVEL_WEBSOCKETS_SSL_LOCAL_CERT', null),
        'local_pk' => env('LARAVEL_WEBSOCKETS_SSL_LOCAL_PK', null),
        'passphrase' => env('LARAVEL_WEBSOCKETS_SSL_PASSPHRASE', null),
    ],

    'statistics' => [
        'model' => \BeyondCode\LaravelWebSockets\Statistics\Models\WebSocketsStatisticsEntry::class,
        'interval_in_seconds' => 60,
        'delete_statistics_older_than_days' => 60,
    ],

    'max_request_size_in_kb' => 250,

    'path' => 'laravel-websockets',
    'middleware' => [
        'web',
        \BeyondCode\LaravelWebSockets\WebSockets\Channels\ChannelManagers\ArrayChannelManager::class,
    ],
];
EOF

# Создание systemd сервиса для WebSockets
cat > /etc/systemd/system/laravel-websockets.service << 'EOF'
[Unit]
Description=Laravel WebSockets Server
After=network.target

[Service]
Type=simple
User=b2buser
Group=b2buser
WorkingDirectory=/var/www/b2bstorage.ru
ExecStart=/usr/bin/php artisan websockets:serve
Restart=always
RestartSec=10

[Install]
WantedBy=multi-user.target
EOF

# Создание systemd сервиса для очередей
cat > /etc/systemd/system/laravel-queue.service << 'EOF'
[Unit]
Description=Laravel Queue Worker
After=network.target

[Service]
Type=simple
User=b2buser
Group=b2buser
WorkingDirectory=/var/www/b2bstorage.ru
ExecStart=/usr/bin/php artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
Restart=always
RestartSec=10

[Install]
WantedBy=multi-user.target
EOF

# Создание systemd сервиса для планировщика
cat > /etc/systemd/system/laravel-scheduler.service << 'EOF'
[Unit]
Description=Laravel Scheduler
After=network.target

[Service]
Type=oneshot
User=b2buser
Group=b2buser
WorkingDirectory=/var/www/b2bstorage.ru
ExecStart=/usr/bin/php artisan schedule:run

[Install]
WantedBy=multi-user.target
EOF

# Создание таймера для планировщика
cat > /etc/systemd/system/laravel-scheduler.timer << 'EOF'
[Unit]
Description=Run Laravel Scheduler every minute
Requires=laravel-scheduler.service

[Timer]
Unit=laravel-scheduler.service
OnCalendar=*:*:00

[Install]
WantedBy=timers.target
EOF

# Перезагрузка systemd
systemctl daemon-reload

# Запуск сервисов
systemctl enable laravel-websockets
systemctl start laravel-websockets

systemctl enable laravel-queue
systemctl start laravel-queue

systemctl enable laravel-scheduler.timer
systemctl start laravel-scheduler.timer

# Проверка статуса сервисов
echo "📋 Проверка статуса сервисов..."
systemctl status laravel-websockets
systemctl status laravel-queue
systemctl status laravel-scheduler.timer

echo "✅ Laravel приложение развернуто!"
echo "📋 Информация о развертывании:"
echo "- Приложение: /var/www/b2bstorage.ru"
echo "- WebSockets: порт 6001"
echo "- Очереди: Redis"
echo "- Планировщик: активен"
echo "- Пользователь: b2buser" 