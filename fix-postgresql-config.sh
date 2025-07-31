#!/bin/bash

# Скрипт исправления конфигурации PostgreSQL
echo "🔧 Исправление конфигурации PostgreSQL..."

# Находим версию PostgreSQL
PG_VERSION=$(psql --version | grep -oE '[0-9]+\.[0-9]+' | head -1)
echo "📦 Версия PostgreSQL: $PG_VERSION"

# Путь к конфигурационному файлу
PG_CONF="/etc/postgresql/$PG_VERSION/main/postgresql.conf"

# Резервная копия
cp $PG_CONF ${PG_CONF}.backup

# Обновляем конфигурацию для TCP/IP подключений
echo "🌐 Настройка TCP/IP подключений..."

# Находим и комментируем старые настройки
sed -i 's/^#listen_addresses = .*/listen_addresses = '\''*'\''/' $PG_CONF
sed -i 's/^#port = .*/port = 5432/' $PG_CONF

# Добавляем новые настройки если их нет
if ! grep -q "listen_addresses" $PG_CONF; then
    echo "listen_addresses = '*'" >> $PG_CONF
fi

if ! grep -q "port = 5432" $PG_CONF; then
    echo "port = 5432" >> $PG_CONF
fi

# Настройка аутентификации
PG_HBA="/etc/postgresql/$PG_VERSION/main/pg_hba.conf"

# Создаем новый pg_hba.conf
cat > $PG_HBA << 'EOF'
# TYPE  DATABASE        USER            ADDRESS                 METHOD

# "local" is for Unix domain socket connections only
local   all             all                                     peer

# IPv4 local connections:
host    all             all             127.0.0.1/32            md5
host    all             all             0.0.0.0/0               md5

# IPv6 local connections:
host    all             all             ::1/128                 md5

# Allow replication connections from localhost, by a user with the
# replication privilege.
local   replication     all                                     peer
host    replication     all             127.0.0.1/32            md5
host    replication     all             ::1/128                 md5
EOF

# Перезапуск PostgreSQL
echo "🔄 Перезапуск PostgreSQL..."
systemctl restart postgresql

# Проверка статуса
echo "✅ Проверка статуса PostgreSQL..."
systemctl status postgresql

# Проверка прослушивания портов
echo "🔍 Проверка прослушивания портов..."
netstat -tlnp | grep 5432

# Тест подключения
echo "🧪 Тест подключения к базе данных..."
PGPASSWORD=B2B_sklad2025 psql -h localhost -U b2buser -d b2bstorage -c "SELECT version();"

echo "✅ Конфигурация PostgreSQL исправлена!" 