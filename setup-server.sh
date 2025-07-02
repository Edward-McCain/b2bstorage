#!/bin/bash

# Полная настройка VPS сервера для B2B Storage
# Домен: b2bstorage.ru
# Сервер: 5.35.85.110

set -e

echo "🚀 Начинаем полную настройку сервера для B2B Storage..."
echo "📋 План настройки:"
echo "1. Базовая настройка системы"
echo "2. Настройка Nginx"
echo "3. Настройка PostgreSQL"
echo "4. Настройка Redis"
echo "5. Настройка SSL сертификатов"
echo "6. Развертывание Laravel приложения"
echo ""

# Проверка прав root
if [[ $EUID -ne 0 ]]; then
   echo "❌ Этот скрипт должен быть запущен с правами root"
   exit 1
fi

# Этап 1: Базовая настройка
echo "📦 Этап 1: Базовая настройка системы..."
chmod +x server-setup.sh
./server-setup.sh

echo ""
echo "✅ Этап 1 завершен!"
echo ""

# Этап 2: Настройка Nginx
echo "🌐 Этап 2: Настройка Nginx..."
chmod +x nginx-config.sh
./nginx-config.sh

echo ""
echo "✅ Этап 2 завершен!"
echo ""

# Этап 3: Настройка PostgreSQL
echo "🐘 Этап 3: Настройка PostgreSQL..."
chmod +x postgresql-setup.sh
./postgresql-setup.sh

echo ""
echo "✅ Этап 3 завершен!"
echo ""

# Этап 4: Настройка Redis
echo "🔴 Этап 4: Настройка Redis..."
chmod +x redis-setup.sh
./redis-setup.sh

echo ""
echo "✅ Этап 4 завершен!"
echo ""

# Этап 5: Настройка SSL (только если домен доступен)
echo "🔒 Этап 5: Настройка SSL сертификатов..."
echo "⚠️  Внимание: Убедитесь, что DNS записи настроены правильно!"
echo "   A     b2bstorage.ru     -> 5.35.85.110"
echo "   A     www.b2bstorage.ru -> 5.35.85.110"
echo "   A     ws.b2bstorage.ru  -> 5.35.85.110"
echo ""
read -p "Продолжить настройку SSL? (y/n): " -n 1 -r
echo ""
if [[ $REPLY =~ ^[Yy]$ ]]; then
    chmod +x ssl-setup.sh
    ./ssl-setup.sh
    echo ""
    echo "✅ Этап 5 завершен!"
else
    echo "⏭️  Этап 5 пропущен"
fi
echo ""

# Этап 6: Развертывание Laravel
echo "🚀 Этап 6: Развертывание Laravel приложения..."
echo "⚠️  Внимание: Этот этап создаст новый Laravel проект!"
echo "   Если у вас есть готовый репозиторий, отредактируйте laravel-deploy.sh"
echo ""
read -p "Продолжить развертывание Laravel? (y/n): " -n 1 -r
echo ""
if [[ $REPLY =~ ^[Yy]$ ]]; then
    chmod +x laravel-deploy.sh
    ./laravel-deploy.sh
    echo ""
    echo "✅ Этап 6 завершен!"
else
    echo "⏭️  Этап 6 пропущен"
fi
echo ""

# Финальная проверка
echo "🔍 Финальная проверка сервисов..."
echo ""

echo "📋 Статус основных сервисов:"
systemctl is-active --quiet nginx && echo "✅ Nginx: активен" || echo "❌ Nginx: неактивен"
systemctl is-active --quiet postgresql && echo "✅ PostgreSQL: активен" || echo "❌ PostgreSQL: неактивен"
systemctl is-active --quiet redis-server && echo "✅ Redis: активен" || echo "❌ Redis: неактивен"
systemctl is-active --quiet php8.2-fpm && echo "✅ PHP-FPM: активен" || echo "❌ PHP-FPM: неактивен"

echo ""
echo "🎉 Настройка сервера завершена!"
echo ""
echo "📋 Информация о сервере:"
echo "🌐 Основной сайт: https://b2bstorage.ru"
echo "🔌 WebSockets: https://ws.b2bstorage.ru"
echo "🗄️ База данных: PostgreSQL (b2bstorage)"
echo "🔴 Кэш/Очереди: Redis"
echo "📁 Приложение: /var/www/b2bstorage.ru"
echo "👤 Пользователь: b2buser"
echo ""
echo "🔑 Пароли:"
echo "- PostgreSQL: B2B_Storage_2024!"
echo "- Redis: B2B_Redis_2024!"
echo ""
echo "📝 Следующие шаги:"
echo "1. Настройте DNS записи в панели Beget"
echo "2. Запустите SSL настройку (если не сделано)"
echo "3. Загрузите ваш код в /var/www/b2bstorage.ru"
echo "4. Настройте .env файл под ваши нужды"
echo "5. Выполните миграции: php artisan migrate"
echo "6. Настройте очереди и WebSockets"
echo ""
echo "🛠️  Полезные команды:"
echo "- Проверка статуса: systemctl status [сервис]"
echo "- Просмотр логов: journalctl -u [сервис]"
echo "- Перезапуск сервиса: systemctl restart [сервис]"
echo "- Проверка Nginx: nginx -t"
echo "- Подключение к БД: sudo -u postgres psql -d b2bstorage"
echo "- Подключение к Redis: redis-cli -a 'B2B_Redis_2024!'" 