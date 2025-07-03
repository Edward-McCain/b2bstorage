#!/bin/bash

echo "🔍 Диагностика доступности сервера для пользователей из Узбекистана"
echo "================================================================"

# Проверка доступности основных доменов
echo "1. Проверка доступности доменов:"
echo "--------------------------------"

# Проверка DNS
echo "DNS записи:"
nslookup b2bstorage.ru
nslookup api.b2bstorage.ru
echo ""

# Проверка HTTP/HTTPS доступности
echo "HTTP доступность:"
curl -I -m 10 http://b2bstorage.ru
curl -I -m 10 http://api.b2bstorage.ru
echo ""

echo "HTTPS доступность:"
curl -I -m 10 https://b2bstorage.ru
curl -I -m 10 https://api.b2bstorage.ru
echo ""

# Проверка SSL сертификатов
echo "2. Проверка SSL сертификатов:"
echo "-----------------------------"
openssl s_client -connect b2bstorage.ru:443 -servername b2bstorage.ru < /dev/null 2>/dev/null | openssl x509 -noout -dates
openssl s_client -connect api.b2bstorage.ru:443 -servername api.b2bstorage.ru < /dev/null 2>/dev/null | openssl x509 -noout -dates
echo ""

# Проверка CORS заголовков
echo "3. Проверка CORS заголовков:"
echo "----------------------------"
curl -H "Origin: https://example.com" -H "Access-Control-Request-Method: POST" -H "Access-Control-Request-Headers: X-Requested-With" -X OPTIONS -I https://api.b2bstorage.ru/api/register
echo ""

# Проверка rate limiting
echo "4. Проверка rate limiting:"
echo "-------------------------"
for i in {1..5}; do
    echo "Попытка $i:"
    curl -X POST https://api.b2bstorage.ru/api/register \
        -H "Content-Type: application/json" \
        -H "Accept: application/json" \
        -d '{"email":"test@example.com","user_name":"Test User","password":"password123","password_confirmation":"password123"}' \
        -w "HTTP Status: %{http_code}, Time: %{time_total}s\n" \
        -o /dev/null \
        -s
done
echo ""

# Проверка логирования
echo "5. Проверка логов сервера:"
echo "-------------------------"
echo "Последние записи в логах Nginx:"
tail -n 20 /var/log/nginx/access.log | grep -E "(Uzbekistan|UZ|\.uz)" || echo "Записи из Узбекистана не найдены"
echo ""

echo "Последние ошибки Nginx:"
tail -n 20 /var/log/nginx/error.log
echo ""

# Проверка Laravel логов
echo "6. Проверка логов Laravel:"
echo "-------------------------"
if [ -f "/var/www/b2bstorage-app/storage/logs/laravel.log" ]; then
    echo "Последние записи в Laravel логах:"
    tail -n 20 /var/www/b2bstorage-app/storage/logs/laravel.log
else
    echo "Laravel лог файл не найден"
fi
echo ""

# Проверка настроек файрвола
echo "7. Проверка файрвола:"
echo "--------------------"
ufw status
echo ""

# Проверка доступности портов
echo "8. Проверка открытых портов:"
echo "----------------------------"
netstat -tlnp | grep -E ":80|:443"
echo ""

# Проверка геолокации IP
echo "9. Проверка геолокации:"
echo "----------------------"
echo "Текущий IP сервера:"
curl -s ipinfo.io/ip
echo ""

# Рекомендации
echo "10. Рекомендации для решения проблем:"
echo "====================================="
echo "1. Проверьте, что DNS записи правильно настроены для всех стран"
echo "2. Убедитесь, что SSL сертификаты действительны и поддерживают современные протоколы"
echo "3. Проверьте настройки CORS в Laravel и Nginx"
echo "4. Убедитесь, что rate limiting не слишком строгий"
echo "5. Проверьте логи на наличие ошибок 403, 429, 500"
echo "6. Рассмотрите возможность использования CDN для улучшения доступности"
echo "7. Проверьте, что провайдер не блокирует трафик из Узбекистана"
echo ""

echo "✅ Диагностика завершена!" 