#!/bin/bash

# Настройка SSL сертификата для b2bstorage.ru

echo "🔒 Настройка SSL сертификата..."

# Проверка доступности домена
echo "🌐 Проверка доступности домена..."
if ! nslookup b2bstorage.ru > /dev/null 2>&1; then
    echo "❌ Ошибка: домен b2bstorage.ru недоступен"
    echo "📋 Убедитесь, что DNS записи настроены правильно:"
    echo "   A     b2bstorage.ru     -> 5.35.85.110"
    echo "   A     www.b2bstorage.ru -> 5.35.85.110"
    echo "   A     ws.b2bstorage.ru  -> 5.35.85.110"
    exit 1
fi

echo "✅ Домен доступен"

# Получение SSL сертификата для основного домена
echo "🔐 Получение SSL сертификата для b2bstorage.ru..."
certbot --nginx -d b2bstorage.ru -d www.b2bstorage.ru --non-interactive --agree-tos --email admin@b2bstorage.ru

# Получение SSL сертификата для WebSockets
echo "🔐 Получение SSL сертификата для ws.b2bstorage.ru..."
certbot --nginx -d ws.b2bstorage.ru --non-interactive --agree-tos --email admin@b2bstorage.ru

# Настройка автоматического обновления сертификатов
echo "🔄 Настройка автоматического обновления сертификатов..."

# Создание скрипта для обновления
cat > /usr/local/bin/renew-ssl.sh << 'EOF'
#!/bin/bash

# Скрипт обновления SSL сертификатов
echo "🔄 Обновление SSL сертификатов..."

# Обновление сертификатов
certbot renew --quiet

# Перезапуск Nginx
systemctl reload nginx

echo "✅ SSL сертификаты обновлены"
EOF

chmod +x /usr/local/bin/renew-ssl.sh

# Добавление cron задачи для автоматического обновления
(crontab -l 2>/dev/null; echo "0 12 * * * /usr/local/bin/renew-ssl.sh") | crontab -

# Проверка статуса сертификатов
echo "📋 Проверка статуса сертификатов..."
certbot certificates

# Настройка безопасности SSL
echo "🔒 Настройка безопасности SSL..."

# Создание конфигурации для SSL
cat > /etc/nginx/snippets/ssl-params.conf << 'EOF'
# SSL параметры
ssl_protocols TLSv1.2 TLSv1.3;
ssl_prefer_server_ciphers on;
ssl_ciphers ECDHE-RSA-AES256-GCM-SHA512:DHE-RSA-AES256-GCM-SHA512:ECDHE-RSA-AES256-GCM-SHA384:DHE-RSA-AES256-GCM-SHA384;
ssl_ecdh_curve secp384r1;
ssl_session_timeout 10m;
ssl_session_cache shared:SSL:10m;
ssl_session_tickets off;
ssl_stapling on;
ssl_stapling_verify on;
resolver 8.8.8.8 8.8.4.4 valid=300s;
resolver_timeout 5s;
add_header Strict-Transport-Security "max-age=63072000; includeSubDomains; preload";
add_header X-Frame-Options DENY;
add_header X-Content-Type-Options nosniff;
add_header X-XSS-Protection "1; mode=block";
EOF

# Обновление конфигурации Nginx для HTTPS
cat > /etc/nginx/sites-available/b2bstorage.ru << 'EOF'
# HTTP -> HTTPS редирект
server {
    listen 80;
    listen [::]:80;
    server_name b2bstorage.ru www.b2bstorage.ru;
    return 301 https://$server_name$request_uri;
}

# HTTPS конфигурация
server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name b2bstorage.ru www.b2bstorage.ru;
    
    # SSL сертификаты
    ssl_certificate /etc/letsencrypt/live/b2bstorage.ru/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/b2bstorage.ru/privkey.pem;
    include /etc/nginx/snippets/ssl-params.conf;
    
    # Логи
    access_log /var/log/b2bstorage/access.log;
    error_log /var/log/b2bstorage/error.log;
    
    # Корневая директория
    root /var/www/b2bstorage.ru/public;
    index index.php index.html index.htm;
    
    # Обработка Laravel
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    # Обработка PHP
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_read_timeout 300;
        fastcgi_buffer_size 128k;
        fastcgi_buffers 4 256k;
        fastcgi_busy_buffers_size 256k;
    }
    
    # Запрет доступа к скрытым файлам
    location ~ /\. {
        deny all;
    }
    
    # Запрет доступа к файлам конфигурации
    location ~ \.(env|log|sql|conf|ini)$ {
        deny all;
    }
    
    # Кэширование статических файлов
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|pdf|txt|woff|woff2|ttf|eot|svg)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        access_log off;
    }
    
    # Gzip сжатие
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_proxied expired no-cache no-store private must-revalidate auth;
    gzip_types
        text/plain
        text/css
        text/xml
        text/javascript
        application/x-javascript
        application/xml+rss
        application/javascript
        application/json;
    
    # Ограничение размера загружаемых файлов
    client_max_body_size 100M;
    
    # Таймауты
    client_body_timeout 60s;
    client_header_timeout 60s;
    send_timeout 60s;
}
EOF

# Обновление конфигурации WebSockets для HTTPS
cat > /etc/nginx/sites-available/b2bstorage-websockets.ru << 'EOF'
# HTTP -> HTTPS редирект
server {
    listen 80;
    listen [::]:80;
    server_name ws.b2bstorage.ru;
    return 301 https://$server_name$request_uri;
}

# HTTPS конфигурация для WebSockets
server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name ws.b2bstorage.ru;
    
    # SSL сертификаты
    ssl_certificate /etc/letsencrypt/live/ws.b2bstorage.ru/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/ws.b2bstorage.ru/privkey.pem;
    include /etc/nginx/snippets/ssl-params.conf;
    
    # Логи
    access_log /var/log/b2bstorage/websockets_access.log;
    error_log /var/log/b2bstorage/websockets_error.log;
    
    location / {
        proxy_pass http://127.0.0.1:6001;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "upgrade";
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_cache_bypass $http_upgrade;
        proxy_read_timeout 86400;
    }
}
EOF

# Проверка конфигурации Nginx
nginx -t

# Перезапуск Nginx
systemctl reload nginx

echo "✅ SSL сертификаты настроены!"
echo "📋 Информация о SSL:"
echo "- Основной домен: https://b2bstorage.ru"
echo "- WebSockets: https://ws.b2bstorage.ru"
echo "- Автообновление: настроено (cron)"
echo "- Безопасность: HSTS, современные шифры" 