#!/bin/bash

# Настройка Nginx для b2bstorage.ru

echo "🌐 Настройка Nginx конфигурации..."

# Создание конфигурации Nginx
cat > /etc/nginx/sites-available/b2bstorage.ru << 'EOF'
server {
    listen 80;
    listen [::]:80;
    server_name b2bstorage.ru www.b2bstorage.ru;
    
    # Логи
    access_log /var/log/b2bstorage/access.log;
    error_log /var/log/b2bstorage/error.log;
    
    # Корневая директория
    root /var/www/b2bstorage.ru/public;
    index index.php index.html index.htm;
    
    # Основные настройки безопасности
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self' http: https: data: blob: 'unsafe-inline'" always;
    
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
    gzip_proxied expired no-cache no-store private auth;
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

# Создание конфигурации для WebSockets
cat > /etc/nginx/sites-available/b2bstorage-websockets.ru << 'EOF'
server {
    listen 80;
    listen [::]:80;
    server_name ws.b2bstorage.ru;
    
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

# Активация сайтов
ln -sf /etc/nginx/sites-available/b2bstorage.ru /etc/nginx/sites-enabled/
ln -sf /etc/nginx/sites-available/b2bstorage-websockets.ru /etc/nginx/sites-enabled/

# Удаление дефолтного сайта
rm -f /etc/nginx/sites-enabled/default

# Проверка конфигурации
nginx -t

# Перезапуск Nginx
systemctl restart nginx

echo "✅ Nginx конфигурация настроена!"
echo "📋 Сайты:"
echo "- Основной: b2bstorage.ru"
echo "- WebSockets: ws.b2bstorage.ru" 