#!/bin/bash

# Скрипт настройки продакшн сервера для B2B Sklad
# IP: 45.92.173.142
# Login: root
# Password: B2B_sklad

echo "🚀 Начинаем настройку продакшн сервера..."

# Обновление системы
echo "📦 Обновление системы..."
apt update && apt upgrade -y

# Установка необходимых пакетов
echo "📦 Установка необходимых пакетов..."
apt install -y nginx php8.4-fpm php8.4-mysql php8.4-xml php8.4-mbstring php8.4-curl php8.4-zip php8.4-gd php8.4-intl php8.4-bcmath php8.4-sqlite3 composer nodejs npm git unzip curl

# Установка Node.js 20
echo "📦 Установка Node.js 20..."
curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
apt-get install -y nodejs

# Создание пользователя для веб-сервера
echo "👤 Создание пользователя www-data..."
useradd -m -s /bin/bash www-data || true

# Создание директорий для проекта
echo "📁 Создание директорий проекта..."
if [ -d "/var/www/b2bstorage-frontend" ]; then
    echo "⚠️  Директория /var/www/b2bstorage-frontend уже существует"
    read -p "Удалить существующую директорию? (y/N): " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        rm -rf /var/www/b2bstorage-frontend
        mkdir -p /var/www/b2bstorage-frontend
    fi
else
    mkdir -p /var/www/b2bstorage-frontend
fi

if [ -d "/var/www/b2bstorage-backend" ]; then
    echo "⚠️  Директория /var/www/b2bstorage-backend уже существует"
    read -p "Удалить существующую директорию? (y/N): " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        rm -rf /var/www/b2bstorage-backend
        mkdir -p /var/www/b2bstorage-backend
    fi
else
    mkdir -p /var/www/b2bstorage-backend
fi

mkdir -p /var/log/b2bstorage

# Настройка прав доступа
echo "🔐 Настройка прав доступа..."
chown -R www-data:www-data /var/www/b2bstorage-frontend
chown -R www-data:www-data /var/www/b2bstorage-backend
chmod -R 755 /var/www/b2bstorage-frontend
chmod -R 755 /var/www/b2bstorage-backend

# Настройка Nginx
echo "🌐 Настройка Nginx..."
cat > /etc/nginx/sites-available/b2bstorage << 'EOF'
server {
    listen 80;
    server_name 45.92.173.142;
    
    # Frontend
    location / {
        root /var/www/b2bstorage-frontend;
        try_files $uri $uri/ /index.html;
        add_header Cache-Control "no-cache, no-store, must-revalidate";
    }
    
    # Backend API
    location /api {
        root /var/www/b2bstorage-backend/public;
        try_files $uri $uri/ /index.php?$query_string;
        
        location ~ \.php$ {
            fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
            fastcgi_index index.php;
            fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
            include fastcgi_params;
        }
    }
    
    # Laravel storage files
    location /storage {
        alias /var/www/b2bstorage-backend/storage/app/public;
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
    
    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self' http: https: data: blob: 'unsafe-inline'" always;
}
EOF

# Активация сайта
ln -sf /etc/nginx/sites-available/b2bstorage /etc/nginx/sites-enabled/
rm -f /etc/nginx/sites-enabled/default

# Настройка PHP-FPM
echo "🔧 Настройка PHP-FPM..."
sed -i 's/upload_max_filesize = 2M/upload_max_filesize = 100M/' /etc/php/8.4/fpm/php.ini
sed -i 's/post_max_size = 8M/post_max_size = 100M/' /etc/php/8.4/fpm/php.ini
sed -i 's/memory_limit = 128M/memory_limit = 512M/' /etc/php/8.4/fpm/php.ini
sed -i 's/max_execution_time = 30/max_execution_time = 300/' /etc/php/8.4/fpm/php.ini

# Настройка прав для storage
echo "📁 Настройка storage директорий..."
mkdir -p /var/www/b2bstorage-backend/storage/framework/cache
mkdir -p /var/www/b2bstorage-backend/storage/framework/sessions
mkdir -p /var/www/b2bstorage-backend/storage/framework/views
mkdir -p /var/www/b2bstorage-backend/storage/logs
mkdir -p /var/www/b2bstorage-backend/storage/app/public

chown -R www-data:www-data /var/www/b2bstorage-backend/storage
chmod -R 775 /var/www/b2bstorage-backend/storage

# Создание .env файла
echo "⚙️ Создание .env файла..."
cat > /var/www/b2bstorage-backend/.env << 'EOF'
APP_NAME="B2B Sklad"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=http://45.92.173.142

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=sqlite
DB_DATABASE=/var/www/b2bstorage-backend/database/database.sqlite

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

# Создание базы данных
echo "🗄️ Создание базы данных..."
touch /var/www/b2bstorage-backend/database/database.sqlite
chown www-data:www-data /var/www/b2bstorage-backend/database/database.sqlite
chmod 664 /var/www/b2bstorage-backend/database/database.sqlite

# Настройка firewall
echo "🔥 Настройка firewall..."
ufw allow 22/tcp
ufw allow 80/tcp
ufw allow 443/tcp
ufw --force enable

# Перезапуск сервисов
echo "🔄 Перезапуск сервисов..."
systemctl restart nginx
systemctl restart php8.4-fpm
systemctl enable nginx
systemctl enable php8.4-fpm

# Создание SSH ключа для GitHub Actions
echo "🔑 Создание SSH ключа для GitHub Actions..."
ssh-keygen -t rsa -b 4096 -f /root/.ssh/id_rsa -N ""
cat /root/.ssh/id_rsa.pub >> /root/.ssh/authorized_keys
chmod 600 /root/.ssh/authorized_keys

echo "🔑 Публичный ключ для GitHub Actions:"
cat /root/.ssh/id_rsa.pub

echo "🔑 Приватный ключ для GitHub Actions:"
cat /root/.ssh/id_rsa

echo "✅ Настройка продакшн сервера завершена!"
echo "📝 Следующие шаги:"
echo "1. Добавьте SSH ключи в GitHub Secrets"
echo "2. Создайте ветку production: git checkout -b production"
echo "3. Запустите миграции на сервере"
echo "4. Сгенерируйте APP_KEY: php artisan key:generate" 