#!/bin/bash

# Настройка PostgreSQL для B2B Storage

echo "🐘 Настройка PostgreSQL..."

# Переключение на пользователя postgres
sudo -u postgres psql << 'EOF'

-- Создание базы данных
CREATE DATABASE b2bstorage;

-- Создание пользователя
CREATE USER b2buser WITH PASSWORD 'B2B_Storage_2024!';

-- Предоставление прав пользователю
GRANT ALL PRIVILEGES ON DATABASE b2bstorage TO b2buser;

-- Подключение к базе данных
\c b2bstorage

-- Предоставление прав на схему public
GRANT ALL ON SCHEMA public TO b2buser;
GRANT ALL ON ALL TABLES IN SCHEMA public TO b2buser;
GRANT ALL ON ALL SEQUENCES IN SCHEMA public TO b2buser;
GRANT ALL ON ALL FUNCTIONS IN SCHEMA public TO b2buser;

-- Настройка для будущих таблиц
ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT ALL ON TABLES TO b2buser;
ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT ALL ON SEQUENCES TO b2buser;
ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT ALL ON FUNCTIONS TO b2buser;

-- Выход
\q
EOF

# Настройка PostgreSQL для внешних подключений
echo "🔧 Настройка PostgreSQL для внешних подключений..."

# Резервная копия конфигурации
cp /etc/postgresql/15/main/postgresql.conf /etc/postgresql/15/main/postgresql.conf.backup
cp /etc/postgresql/15/main/pg_hba.conf /etc/postgresql/15/main/pg_hba.conf.backup

# Настройка postgresql.conf
cat >> /etc/postgresql/15/main/postgresql.conf << 'EOF'

# Настройки для производительности
shared_buffers = 256MB
effective_cache_size = 1GB
work_mem = 4MB
maintenance_work_mem = 64MB
checkpoint_completion_target = 0.9
wal_buffers = 16MB
default_statistics_target = 100
random_page_cost = 1.1
effective_io_concurrency = 200

# Настройки логирования
log_destination = 'stderr'
logging_collector = on
log_directory = 'log'
log_filename = 'postgresql-%Y-%m-%d_%H%M%S.log'
log_rotation_age = 1d
log_rotation_size = 100MB
log_min_duration_statement = 1000
log_checkpoints = on
log_connections = on
log_disconnections = on
log_lock_waits = on
log_temp_files = -1
log_autovacuum_min_duration = 0
log_error_verbosity = verbose

# Настройки подключений
max_connections = 100
EOF

# Настройка pg_hba.conf для подключений
cat >> /etc/postgresql/15/main/pg_hba.conf << 'EOF'

# Разрешить подключения с localhost
host    b2bstorage        b2buser          127.0.0.1/32            md5
host    b2bstorage        b2buser          ::1/128                 md5

# Разрешить подключения с сервера (для разработки)
host    b2bstorage        b2buser          5.35.85.110/32          md5
EOF

# Создание директории для логов
mkdir -p /var/log/postgresql
chown postgres:postgres /var/log/postgresql

# Перезапуск PostgreSQL
systemctl restart postgresql

# Проверка статуса
systemctl status postgresql

echo "✅ PostgreSQL настроен!"
echo "📋 Информация о базе данных:"
echo "- База данных: b2bstorage"
echo "- Пользователь: b2buser"
echo "- Пароль: B2B_Storage_2024!"
echo "- Хост: localhost"
echo "- Порт: 5432" 