#!/bin/bash

# Настройка мониторинга для B2B Storage

echo "📊 Настройка мониторинга сервера..."

# Установка инструментов мониторинга
echo "📦 Установка инструментов мониторинга..."
apt install -y htop iotop nethogs nmon glances

# Установка logrotate для ротации логов
echo "📋 Настройка ротации логов..."
cat > /etc/logrotate.d/b2bstorage << 'EOF'
/var/log/b2bstorage/*.log {
    daily
    missingok
    rotate 30
    compress
    delaycompress
    notifempty
    create 644 b2buser www-data
    postrotate
        systemctl reload nginx
    endscript
}
EOF

# Создание скрипта для мониторинга
cat > /usr/local/bin/server-monitor.sh << 'EOF'
#!/bin/bash

# Скрипт мониторинга сервера

echo "=== Мониторинг сервера B2B Storage ==="
echo "Дата: $(date)"
echo ""

# Использование CPU и памяти
echo "=== Системные ресурсы ==="
echo "CPU: $(top -bn1 | grep "Cpu(s)" | awk '{print $2}' | cut -d'%' -f1)%"
echo "Память: $(free -m | awk 'NR==2{printf "%.1f%%", $3*100/$2}')"
echo "Диск: $(df -h / | awk 'NR==2{print $5}')"
echo ""

# Статус сервисов
echo "=== Статус сервисов ==="
services=("nginx" "postgresql" "redis-server" "php8.2-fpm" "laravel-websockets" "laravel-queue")
for service in "${services[@]}"; do
    if systemctl is-active --quiet $service; then
        echo "✅ $service: активен"
    else
        echo "❌ $service: неактивен"
    fi
done
echo ""

# Подключения к базе данных
echo "=== Подключения к PostgreSQL ==="
sudo -u postgres psql -d b2bstorage -c "SELECT count(*) as active_connections FROM pg_stat_activity WHERE state = 'active';" 2>/dev/null || echo "Ошибка подключения к БД"
echo ""

# Статистика Redis
echo "=== Статистика Redis ==="
redis-cli -a 'B2B_Redis_2024!' info memory | grep -E "(used_memory_human|maxmemory_human)" 2>/dev/null || echo "Ошибка подключения к Redis"
echo ""

# Размер логов
echo "=== Размер логов ==="
du -sh /var/log/b2bstorage/* 2>/dev/null | head -5
echo ""
EOF

chmod +x /usr/local/bin/server-monitor.sh

# Добавление cron задачи для мониторинга
(crontab -l 2>/dev/null; echo "*/5 * * * * /usr/local/bin/server-monitor.sh >> /var/log/b2bstorage/monitor.log 2>&1") | crontab -

echo "✅ Мониторинг настроен!"
echo "📋 Команды мониторинга:"
echo "- Общий мониторинг: /usr/local/bin/server-monitor.sh"
echo "- Системные ресурсы: htop"
echo "- Дисковые операции: iotop"
echo "- Сетевая активность: nethogs"
echo "- Логи мониторинга: tail -f /var/log/b2bstorage/monitor.log" 