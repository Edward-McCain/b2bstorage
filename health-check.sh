#!/bin/bash

# Проверка здоровья сервера B2B Storage

echo "🏥 Проверка здоровья сервера B2B Storage..."
echo ""

# Проверка системных ресурсов
echo "📊 Системные ресурсы:"
echo "CPU: $(top -bn1 | grep "Cpu(s)" | awk '{print $2}' | cut -d'%' -f1)%"
echo "Память: $(free -m | awk 'NR==2{printf "%.1f%%", $3*100/$2}')"
echo "Диск: $(df -h / | awk 'NR==2{print $5}')"
echo ""

# Проверка сервисов
echo "🔧 Статус сервисов:"
services=("nginx" "postgresql" "redis-server" "php8.2-fpm" "laravel-websockets" "laravel-queue")
all_ok=true

for service in "${services[@]}"; do
    if systemctl is-active --quiet $service; then
        echo "✅ $service: активен"
    else
        echo "❌ $service: неактивен"
        all_ok=false
    fi
done
echo ""

# Проверка портов
echo "🔌 Проверка портов:"
ports=("80" "443" "5432" "6379" "6001")
for port in "${ports[@]}"; do
    if netstat -tuln | grep ":$port " > /dev/null; then
        echo "✅ Порт $port: открыт"
    else
        echo "❌ Порт $port: закрыт"
        all_ok=false
    fi
done
echo ""

# Проверка SSL сертификатов
echo "🔒 Проверка SSL сертификатов:"
if [ -f "/etc/letsencrypt/live/b2bstorage.ru/fullchain.pem" ]; then
    echo "✅ SSL сертификат для b2bstorage.ru: установлен"
else
    echo "❌ SSL сертификат для b2bstorage.ru: не найден"
    all_ok=false
fi

if [ -f "/etc/letsencrypt/live/ws.b2bstorage.ru/fullchain.pem" ]; then
    echo "✅ SSL сертификат для ws.b2bstorage.ru: установлен"
else
    echo "❌ SSL сертификат для ws.b2bstorage.ru: не найден"
    all_ok=false
fi
echo ""

# Проверка базы данных
echo "🗄️ Проверка базы данных:"
if sudo -u postgres psql -d b2bstorage -c "SELECT version();" > /dev/null 2>&1; then
    echo "✅ PostgreSQL: подключение успешно"
else
    echo "❌ PostgreSQL: ошибка подключения"
    all_ok=false
fi
echo ""

# Проверка Redis
echo "🔴 Проверка Redis:"
if redis-cli -a 'B2B_Redis_2024!' ping > /dev/null 2>&1; then
    echo "✅ Redis: подключение успешно"
else
    echo "❌ Redis: ошибка подключения"
    all_ok=false
fi
echo ""

# Проверка Laravel приложения
echo "🚀 Проверка Laravel приложения:"
if [ -f "/var/www/b2bstorage.ru/public/index.php" ]; then
    echo "✅ Laravel: файлы найдены"
    
    # Проверка .env файла
    if [ -f "/var/www/b2bstorage.ru/.env" ]; then
        echo "✅ .env файл: существует"
    else
        echo "❌ .env файл: не найден"
        all_ok=false
    fi
    
    # Проверка прав доступа
    if [ -w "/var/www/b2bstorage.ru/storage" ]; then
        echo "✅ Права доступа: настроены"
    else
        echo "❌ Права доступа: проблемы"
        all_ok=false
    fi
else
    echo "❌ Laravel: файлы не найдены"
    all_ok=false
fi
echo ""

# Проверка DNS
echo "🌐 Проверка DNS:"
if nslookup b2bstorage.ru > /dev/null 2>&1; then
    echo "✅ b2bstorage.ru: резолвится"
else
    echo "❌ b2bstorage.ru: не резолвится"
    all_ok=false
fi

if nslookup ws.b2bstorage.ru > /dev/null 2>&1; then
    echo "✅ ws.b2bstorage.ru: резолвится"
else
    echo "❌ ws.b2bstorage.ru: не резолвится"
    all_ok=false
fi
echo ""

# Итоговая оценка
echo "📋 Итоговая оценка:"
if $all_ok; then
    echo "🎉 Все системы работают корректно!"
    echo "✅ Сервер готов к работе"
else
    echo "⚠️  Обнаружены проблемы"
    echo "❌ Требуется дополнительная настройка"
fi
echo ""

echo "🛠️  Рекомендации:"
echo "1. Проверьте DNS записи в панели Beget"
echo "2. Убедитесь, что все сервисы запущены"
echo "3. Проверьте логи при возникновении ошибок"
echo "4. Настройте SSL сертификаты при необходимости" 