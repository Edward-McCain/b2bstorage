# Руководство по настройке продакшн сервера

## Этап 1: Настройка продакшн сервера

### 1.1 Подключение к серверу
```bash
ssh root@45.92.173.142
# Пароль: B2B_sklad
```

### 1.2 Загрузка и запуск скрипта настройки
```bash
# Скачиваем скрипт настройки
wget https://raw.githubusercontent.com/[ваш-репозиторий]/main/setup-production-server.sh

# Делаем исполняемым
chmod +x setup-production-server.sh

# Запускаем настройку
./setup-production-server.sh
```

### 1.3 Копирование SSH ключей
После выполнения скрипта скопируйте приватный ключ и добавьте его в GitHub Secrets.

## Этап 2: Настройка GitHub Secrets

### 2.1 Обновление существующих secrets
Переименуйте в GitHub (Settings → Secrets and variables → Actions):

- `SERVER_HOST` → `TEST_SERVER_HOST`
- `SERVER_USER` → `TEST_SERVER_USER`  
- `SERVER_SSH_KEY` → `TEST_SERVER_SSH_KEY`

### 2.2 Добавление новых secrets для продакшна
Добавьте следующие secrets:

- `PROD_SERVER_HOST`: `45.92.173.142`
- `PROD_SERVER_USER`: `root`
- `PROD_SERVER_SSH_KEY`: [приватный ключ с сервера]

## Этап 3: Создание ветки production

### 3.1 Создание ветки
```bash
git checkout -b production
git push origin production
```

### 3.2 Первый деплой на продакшн
```bash
git add .
git commit -m "Initial production deployment"
git push origin production
```

## Этап 4: Инициализация продакшн сервера

### 4.1 Подключение к серверу после деплоя
```bash
ssh root@45.92.173.142
```

### 4.2 Запуск инициализации
```bash
cd /var/www/b2bstorage-backend
chmod +x init-production-server.sh
./init-production-server.sh
```

## Этап 5: Проверка работы

### 5.1 Проверка frontend
Откройте в браузере: `http://45.92.173.142`

### 5.2 Проверка backend API
Откройте в браузере: `http://45.92.173.142/api`

### 5.3 Проверка логов
```bash
# Логи Nginx
tail -f /var/log/nginx/error.log

# Логи Laravel
tail -f /var/www/b2bstorage-backend/storage/logs/laravel.log

# Логи PHP-FPM
tail -f /var/log/php8.4-fpm.log
```

## Логика работы

### Тестовый сервер (main ветка)
- Деплой: `git push origin main`
- URL: [ваш текущий тестовый сервер]
- Назначение: разработка и тестирование

### Продакшн сервер (production ветка)
- Деплой: `git push origin production`
- URL: `http://45.92.173.142`
- Назначение: финальная версия для пользователей

## Команды для работы

### Деплой на тестовый сервер
```bash
git add .
git commit -m "Update for testing"
git push origin main
```

### Деплой на продакшн сервер
```bash
git add .
git commit -m "Update for production"
git push origin production
```

### Синхронизация изменений
```bash
# С тестового на продакшн
git checkout production
git merge main
git push origin production

# С продакшна на тестовый
git checkout main
git merge production
git push origin main
```

## Мониторинг и обслуживание

### Проверка статуса сервисов
```bash
systemctl status nginx
systemctl status php8.4-fpm
```

### Перезапуск сервисов
```bash
systemctl restart nginx
systemctl restart php8.4-fpm
```

### Очистка кешей
```bash
cd /var/www/b2bstorage-backend
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

## Безопасность

### Firewall
```bash
# Проверка статуса
ufw status

# Добавление правил
ufw allow 443/tcp  # для HTTPS в будущем
```

### SSL сертификат (опционально)
```bash
# Установка Certbot
apt install certbot python3-certbot-nginx

# Получение сертификата
certbot --nginx -d your-domain.com
``` 