#!/bin/bash

echo "🌍 Применение улучшений для поддержки пользователей из Узбекистана"
echo "=================================================================="

# Проверка прав root
if [[ $EUID -ne 0 ]]; then
   echo "❌ Этот скрипт должен быть запущен с правами root"
   exit 1
fi

# 1. Очистка кэша Laravel
echo "1. Очистка кэша Laravel..."
cd /var/www/b2bstorage-app
php artisan config:clear
php artisan route:clear
php artisan cache:clear
php artisan view:clear

# 2. Применение новых конфигураций
echo "2. Применение новых конфигураций..."
php artisan config:cache
php artisan route:cache

# 3. Обновление конфигурации Nginx
echo "3. Обновление конфигурации Nginx..."
cp /var/www/b2bstorage-app/frontend/nginx-api.conf /etc/nginx/sites-available/b2bstorage-api

# 4. Проверка конфигурации Nginx
echo "4. Проверка конфигурации Nginx..."
nginx -t

if [ $? -eq 0 ]; then
    echo "✅ Конфигурация Nginx корректна"
    
    # 5. Перезапуск Nginx
    echo "5. Перезапуск Nginx..."
    systemctl reload nginx
    
    # 6. Перезапуск PHP-FPM
    echo "6. Перезапуск PHP-FPM..."
    systemctl restart php8.2-fpm
    
    # 7. Проверка статуса сервисов
    echo "7. Проверка статуса сервисов..."
    systemctl status nginx --no-pager -l
    systemctl status php8.2-fpm --no-pager -l
    
    # 8. Проверка доступности API
    echo "8. Проверка доступности API..."
    curl -I https://api.b2bstorage.ru/api/register
    
    # 9. Проверка CORS заголовков
    echo "9. Проверка CORS заголовков..."
    curl -H "Origin: https://example.com" \
         -H "Access-Control-Request-Method: POST" \
         -H "Access-Control-Request-Headers: X-Requested-With" \
         -X OPTIONS -I https://api.b2bstorage.ru/api/register
    
    echo ""
    echo "✅ Все улучшения применены успешно!"
    echo ""
    echo "📋 Что было улучшено:"
    echo "1. Улучшена CORS конфигурация для международных пользователей"
    echo "2. Смягчены rate limiting ограничения"
    echo "3. Добавлены дополнительные SSL шифры"
    echo "4. Улучшены таймауты для медленных соединений"
    echo "5. Добавлена поддержка credentials в CORS"
    echo "6. Улучшена обработка FastCGI"
    echo ""
    echo "🌍 Теперь сайт должен лучше работать для пользователей из Узбекистана"
    echo ""
    echo "🔍 Для диагностики используйте: ./diagnostic-uzbekistan.sh"
    
else
    echo "❌ Ошибка в конфигурации Nginx"
    exit 1
fi 