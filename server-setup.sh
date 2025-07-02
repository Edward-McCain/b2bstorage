#!/bin/bash

# Скрипт настройки VPS сервера для B2B Storage
# Домен: b2bstorage.ru
# Сервер: 5.35.85.110

set -e

echo "🚀 Начинаем настройку сервера для B2B Storage..."

# Обновление системы
echo "📦 Обновление системы..."
apt update && apt upgrade -y

# Установка базовых пакетов
echo "📦 Установка базовых пакетов..."
apt install -y curl wget git unzip software-properties-common apt-transport-https ca-certificates gnupg lsb-release

# Установка Nginx
echo "🌐 Установка Nginx..."
apt install -y nginx
systemctl enable nginx
systemctl start nginx

# Установка PHP 8.2 и расширений
echo "🐘 Установка PHP 8.2..."
add-apt-repository ppa:ondrej/php -y
apt update
apt install -y php8.2-fpm php8.2-cli php8.2-common php8.2-mysql php8.2-zip php8.2-gd php8.2-mbstring php8.2-curl php8.2-xml php8.2-bcmath php8.2-redis php8.2-pgsql php8.2-soap php8.2-intl php8.2-sqlite3 php8.2-tidy php8.2-uuid php8.2-xdebug php8.2-xmlrpc php8.2-xsl php8.2-zip

# Установка Composer
echo "🎼 Установка Composer..."
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
chmod +x /usr/local/bin/composer

# Установка PostgreSQL 15
echo "🐘 Установка PostgreSQL 15..."
sh -c 'echo "deb http://apt.postgresql.org/pub/repos/apt $(lsb_release -cs)-pgdg main" > /etc/apt/sources.list.d/pgdg.list'
wget --quiet -O - https://www.postgresql.org/media/keys/ACCC4CF8.asc | apt-key add -
apt update
apt install -y postgresql-15 postgresql-contrib-15

# Установка Redis
echo "🔴 Установка Redis..."
apt install -y redis-server
systemctl enable redis-server
systemctl start redis-server

# Установка Node.js и npm
echo "🟢 Установка Node.js..."
curl -fsSL https://deb.nodesource.com/setup_18.x | bash -
apt install -y nodejs

# Установка PM2 для управления процессами
echo "⚡ Установка PM2..."
npm install -g pm2

# Установка Certbot для SSL
echo "🔒 Установка Certbot..."
apt install -y certbot python3-certbot-nginx

# Создание пользователя для приложения
echo "👤 Создание пользователя приложения..."
useradd -m -s /bin/bash b2buser
usermod -aG sudo b2buser

# Создание директорий
echo "📁 Создание директорий..."
mkdir -p /var/www/b2bstorage.ru
mkdir -p /var/log/b2bstorage
mkdir -p /etc/nginx/sites-available
mkdir -p /etc/nginx/sites-enabled

# Настройка прав доступа
chown -R b2buser:b2buser /var/www/b2bstorage.ru
chmod -R 755 /var/www/b2bstorage.ru

echo "✅ Базовая настройка сервера завершена!"
echo "📋 Следующие шаги:"
echo "1. Настройка Nginx конфигурации"
echo "2. Настройка PostgreSQL"
echo "3. Настройка SSL сертификата"
echo "4. Развертывание Laravel приложения"
echo "5. Настройка WebSockets"
echo "6. Настройка Redis очередей" 