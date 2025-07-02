#!/bin/bash

# Настройка Redis для B2B Storage

echo "🔴 Настройка Redis..."

# Резервная копия конфигурации
cp /etc/redis/redis.conf /etc/redis/redis.conf.backup

# Создание новой конфигурации Redis
cat > /etc/redis/redis.conf << 'EOF'
# Основные настройки
bind 127.0.0.1
port 6379
timeout 0
tcp-keepalive 300
daemonize yes
supervised systemd
pidfile /var/run/redis/redis-server.pid
loglevel notice
logfile /var/log/redis/redis-server.log
databases 16

# Настройки безопасности
requirepass B2B_Redis_2024!

# Настройки памяти
maxmemory 512mb
maxmemory-policy allkeys-lru
maxmemory-samples 5

# Настройки персистентности
save 900 1
save 300 10
save 60 10000
stop-writes-on-bgsave-error yes
rdbcompression yes
rdbchecksum yes
dbfilename dump.rdb
dir /var/lib/redis

# Настройки AOF
appendonly yes
appendfilename "appendonly.aof"
appendfsync everysec
no-appendfsync-on-rewrite no
auto-aof-rewrite-percentage 100
auto-aof-rewrite-min-size 64mb

# Настройки репликации
replica-serve-stale-data yes
replica-read-only yes

# Настройки клиентов
maxclients 10000

# Настройки производительности
tcp-backlog 511
hz 10
EOF

# Создание директории для логов
mkdir -p /var/log/redis
chown redis:redis /var/log/redis

# Настройка systemd для Redis
cat > /etc/systemd/system/redis.service << 'EOF'
[Unit]
Description=Redis In-Memory Data Store
After=network.target

[Service]
Type=notify
ExecStart=/usr/bin/redis-server /etc/redis/redis.conf
ExecStop=/bin/kill -s QUIT $MAINPID
TimeoutStopSec=0
Restart=always
User=redis
Group=redis
RuntimeDirectory=redis
RuntimeDirectoryMode=0755

[Install]
WantedBy=multi-user.target
EOF

# Перезагрузка systemd и перезапуск Redis
systemctl daemon-reload
systemctl restart redis-server
systemctl enable redis-server

# Проверка статуса
systemctl status redis-server

# Тест подключения
echo "🧪 Тестирование подключения к Redis..."
redis-cli -a "B2B_Redis_2024!" ping

echo "✅ Redis настроен!"
echo "📋 Информация о Redis:"
echo "- Хост: 127.0.0.1"
echo "- Порт: 6379"
echo "- Пароль: B2B_Redis_2024!"
echo "- Максимальная память: 512MB"
echo "- Политика памяти: allkeys-lru" 