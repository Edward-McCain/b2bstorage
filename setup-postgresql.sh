#!/bin/bash

# Скрипт настройки PostgreSQL для B2B Sklad
# Запускается на продакшн сервере

echo "🗄️ Начинаем настройку PostgreSQL..."

# Установка PostgreSQL
echo "📦 Установка PostgreSQL..."
apt update
apt install -y postgresql postgresql-contrib

# Запуск и включение сервиса
echo "🔄 Запуск PostgreSQL..."
systemctl start postgresql
systemctl enable postgresql

# Переключение на пользователя postgres
echo "👤 Настройка пользователя и базы данных..."
sudo -u postgres psql << EOF
-- Создание пользователя
CREATE USER b2buser WITH PASSWORD 'B2B_sklad2025';

-- Создание базы данных
CREATE DATABASE b2bstorage OWNER b2buser;

-- Предоставление прав пользователю
GRANT ALL PRIVILEGES ON DATABASE b2bstorage TO b2buser;

-- Подключение к базе данных и предоставление прав на схемы
\c b2bstorage;
GRANT ALL ON SCHEMA public TO b2buser;
GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA public TO b2buser;
GRANT ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA public TO b2buser;
ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT ALL ON TABLES TO b2buser;
ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT ALL ON SEQUENCES TO b2buser;

-- Выход
\q
EOF

# Настройка аутентификации
echo "🔐 Настройка аутентификации..."
cat > /tmp/pg_hba.conf << 'EOF'
# Database administrative login by Unix domain socket
local   all             postgres                                peer

# TYPE  DATABASE        USER            ADDRESS                 METHOD

# "local" is for Unix domain socket connections only
local   all             all                                     md5
# IPv4 local connections:
host    all             all             127.0.0.1/32            md5
# IPv6 local connections:
host    all             all             ::1/128                 md5
# Allow replication connections from localhost, by a user with the
# replication privilege.
local   replication     all                                     peer
host    replication     all             127.0.0.1/32            md5
host    replication     all             ::1/128                 md5
EOF

# Копирование конфигурации
cp /tmp/pg_hba.conf /etc/postgresql/*/main/pg_hba.conf

# Перезапуск PostgreSQL
echo "🔄 Перезапуск PostgreSQL..."
systemctl restart postgresql

# Установка PHP PostgreSQL расширения
echo "📦 Установка PHP PostgreSQL расширения..."
apt install -y php8.4-pgsql

# Перезапуск PHP-FPM
echo "🔄 Перезапуск PHP-FPM..."
systemctl restart php8.4-fpm

# Проверка подключения
echo "✅ Проверка подключения к базе данных..."
PGPASSWORD=B2B_sklad2025 psql -h localhost -U b2buser -d b2bstorage -c "\l"

echo "✅ Настройка PostgreSQL завершена!"
echo "📝 Данные базы данных:"
echo "   База данных: b2bstorage"
echo "   Пользователь: b2buser"
echo "   Пароль: B2B_sklad2025"
echo "   Хост: localhost"
echo "   Порт: 5432" 