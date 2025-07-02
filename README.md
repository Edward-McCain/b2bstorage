# 🚀 Настройка VPS сервера для B2B Storage

Полная настройка VPS сервера для развертывания B2B Storage приложения с Laravel, WebSockets, PostgreSQL и Redis.

## 📋 Технический стек

### Backend
- **Laravel** - PHP фреймворк
- **Laravel WebSockets** - WebSocket сервер
- **Eloquent ORM** - ORM для работы с базой данных
- **PostgreSQL 15** - основная база данных

### Frontend
- **Vue.js 3** - JavaScript фреймворк
- **daisyUI** - компоненты для Tailwind CSS
- **Tremor** - библиотека для дашбордов

### Инфраструктура
- **Nginx** - веб-сервер
- **Redis** - кэш и очереди
- **PHP 8.2** - интерпретатор PHP
- **Node.js 18** - JavaScript runtime

## 🖥️ Требования к серверу

- **ОС**: Ubuntu 20.04+ / Debian 11+
- **RAM**: минимум 2GB (рекомендуется 4GB+)
- **CPU**: 2 ядра (рекомендуется 4+)
- **Диск**: минимум 20GB свободного места
- **Домен**: настроенный DNS (b2bstorage.ru)

## 🚀 Быстрый старт

### 1. Подготовка сервера

Подключитесь к серверу:
```bash
ssh root@5.35.85.110
```

### 2. Загрузка скриптов

#### Вариант A: Через SFTP (рекомендуется)
1. Установите расширение SFTP в VS Code
2. Используйте готовую конфигурацию `.vscode/sftp.json`
3. Загрузите все файлы в `/root/server-setup`

#### Вариант B: Ручная загрузка
```bash
# Создайте директорию для скриптов
mkdir -p /root/server-setup
cd /root/server-setup

# Загрузите скрипты (скопируйте содержимое файлов)
```

### 3. Запуск автоматической настройки

```bash
chmod +x setup-server.sh
./setup-server.sh
```

## 📁 Структура скриптов

- `setup-server.sh` - основной скрипт настройки
- `server-setup.sh` - базовая настройка системы
- `nginx-config.sh` - настройка Nginx
- `postgresql-setup.sh` - настройка PostgreSQL
- `redis-setup.sh` - настройка Redis
- `ssl-setup.sh` - настройка SSL сертификатов
- `laravel-deploy.sh` - развертывание Laravel

## 🌐 Настройка DNS

В панели управления Beget настройте DNS записи:

```
A     b2bstorage.ru     -> 5.35.85.110
A     www.b2bstorage.ru -> 5.35.85.110
A     ws.b2bstorage.ru  -> 5.35.85.110
```

## 🔧 Ручная настройка (по этапам)

### Этап 1: Базовая настройка
```bash
chmod +x server-setup.sh
./server-setup.sh
```

### Этап 2: Nginx
```bash
chmod +x nginx-config.sh
./nginx-config.sh
```

### Этап 3: PostgreSQL
```bash
chmod +x postgresql-setup.sh
./postgresql-setup.sh
```

### Этап 4: Redis
```bash
chmod +x redis-setup.sh
./redis-setup.sh
```

### Этап 5: SSL (после настройки DNS)
```bash
chmod +x ssl-setup.sh
./ssl-setup.sh
```

### Этап 6: Laravel
```bash
chmod +x laravel-deploy.sh
./laravel-deploy.sh
```

## 📊 Информация о сервере

### Доступы
- **Основной сайт**: https://b2bstorage.ru
- **WebSockets**: https://ws.b2bstorage.ru
- **Приложение**: `/var/www/b2bstorage.ru`
- **Пользователь**: `b2buser`

### Пароли
- **PostgreSQL**: `B2B_Storage_2024!`
- **Redis**: `B2B_Redis_2024!`

### База данных
- **Хост**: localhost
- **Порт**: 5432
- **База**: b2bstorage
- **Пользователь**: b2buser

## 🔌 Настройка очередей

### Redis очереди
Система настроена для работы с Redis очередями:

```bash
# Проверка статуса очередей
systemctl status laravel-queue

# Просмотр логов очередей
journalctl -u laravel-queue -f

# Перезапуск очередей
systemctl restart laravel-queue
```

### Типы очередей
- **Синхронизация остатков** - обновление данных с внешних API
- **Формирование PDF** - генерация накладных
- **Прогрев кэша** - обновление кэша при изменении остатков
- **Уведомления** - отправка уведомлений сотрудникам/клиентам
- **Webhooks** - отправка данных внешним сервисам

## 🔌 WebSockets

### Настройка
WebSockets сервер работает на порту 6001:

```bash
# Проверка статуса
systemctl status laravel-websockets

# Просмотр логов
journalctl -u laravel-websockets -f

# Перезапуск
systemctl restart laravel-websockets
```

### Конфигурация
```php
// config/broadcasting.php
'pusher' => [
    'driver' => 'pusher',
    'key' => env('PUSHER_APP_KEY'),
    'secret' => env('PUSHER_APP_SECRET'),
    'app_id' => env('PUSHER_APP_ID'),
    'options' => [
        'cluster' => env('PUSHER_APP_CLUSTER'),
        'host' => env('PUSHER_HOST', '127.0.0.1'),
        'port' => env('PUSHER_PORT', 443),
        'scheme' => env('PUSHER_SCHEME', 'https'),
        'useTLS' => env('PUSHER_SCHEME', 'https') === 'https',
    ],
],
```

## 🛠️ Полезные команды

### Системные команды
```bash
# Проверка статуса сервисов
systemctl status nginx postgresql redis-server php8.2-fpm

# Просмотр логов
journalctl -u nginx -f
journalctl -u postgresql -f
journalctl -u redis-server -f

# Перезапуск сервисов
systemctl restart nginx
systemctl restart postgresql
systemctl restart redis-server
```

### Laravel команды
```bash
# Переключение в директорию приложения
cd /var/www/b2bstorage.ru

# Выполнение миграций
php artisan migrate

# Очистка кэша
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Оптимизация для продакшена
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### База данных
```bash
# Подключение к PostgreSQL
sudo -u postgres psql -d b2bstorage

# Создание резервной копии
pg_dump -h localhost -U b2buser -d b2bstorage > backup.sql

# Восстановление из резервной копии
psql -h localhost -U b2buser -d b2bstorage < backup.sql
```

### Redis
```bash
# Подключение к Redis
redis-cli -a 'B2B_Redis_2024!'

# Просмотр статистики
redis-cli -a 'B2B_Redis_2024!' info

# Очистка кэша
redis-cli -a 'B2B_Redis_2024!' flushall
```

## 🔒 Безопасность

### Firewall
```bash
# Установка UFW
apt install ufw

# Настройка правил
ufw allow ssh
ufw allow 'Nginx Full'
ufw allow 5432/tcp  # PostgreSQL (только для локальных подключений)
ufw enable
```

### SSL сертификаты
- Автоматическое обновление через cron
- HSTS заголовки
- Современные шифры
- OCSP Stapling

### Мониторинг
```bash
# Установка мониторинга
apt install htop iotop nethogs

# Просмотр использования ресурсов
htop
iotop
nethogs
```

## 📈 Мониторинг и логи

### Логи приложения
```bash
# Laravel логи
tail -f /var/www/b2bstorage.ru/storage/logs/laravel.log

# Nginx логи
tail -f /var/log/b2bstorage/access.log
tail -f /var/log/b2bstorage/error.log

# WebSockets логи
tail -f /var/log/b2bstorage/websockets_access.log
tail -f /var/log/b2bstorage/websockets_error.log
```

### Системные логи
```bash
# PostgreSQL логи
tail -f /var/log/postgresql/postgresql-*.log

# Redis логи
tail -f /var/log/redis/redis-server.log

# PHP-FPM логи
tail -f /var/log/php8.2-fpm.log
```

## 🔄 Обновление

### Обновление системы
```bash
apt update && apt upgrade -y
```

### Обновление Laravel
```bash
cd /var/www/b2bstorage.ru
composer update --no-dev --optimize-autoloader
php artisan migrate
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Обновление SSL сертификатов
```bash
certbot renew
systemctl reload nginx
```

## 🆘 Устранение неполадок

### Проблемы с Nginx
```bash
# Проверка конфигурации
nginx -t

# Перезапуск
systemctl restart nginx

# Просмотр ошибок
journalctl -u nginx -f
```

### Проблемы с PHP
```bash
# Проверка статуса PHP-FPM
systemctl status php8.2-fpm

# Перезапуск
systemctl restart php8.2-fpm

# Проверка конфигурации
php-fpm8.2 -t
```

### Проблемы с базой данных
```bash
# Проверка подключения
sudo -u postgres psql -d b2bstorage -c "SELECT version();"

# Проверка статуса
systemctl status postgresql

# Перезапуск
systemctl restart postgresql
```

### Проблемы с Redis
```bash
# Проверка подключения
redis-cli -a 'B2B_Redis_2024!' ping

# Проверка статуса
systemctl status redis-server

# Перезапуск
systemctl restart redis-server
```

## 📞 Поддержка

При возникновении проблем:

1. Проверьте логи сервисов
2. Убедитесь в правильности конфигурации
3. Проверьте статус всех сервисов
4. Обратитесь к документации Laravel/PostgreSQL/Redis

## 📝 Лицензия

Этот проект предназначен для внутреннего использования B2B Storage. 

# B2B Storage Project

Современная платформа для управления B2B данными с использованием Laravel и Vue.js

## Структура проекта

```
b2b-project/
├── backend/          # Laravel бэкенд
├── frontend/         # Vue.js фронтенд
└── README.md
```

## Настройка разработки

### Frontend (Vue.js 3 + DaisyUI)

1. Перейдите в папку frontend:
   ```bash
   cd frontend
   ```

2. Установите зависимости:
   ```bash
   npm install
   ```

3. Запустите сервер разработки:
   ```bash
   npm run dev
   ```

4. Соберите для продакшена:
   ```bash
   npm run build
   ```

### Backend (Laravel)

1. Перейдите в папку backend:
   ```bash
   cd backend
   ```

2. Установите Composer (если не установлен):
   ```bash
   # macOS
   brew install composer
   
   # Или скачайте с https://getcomposer.org/
   ```

3. Установите зависимости:
   ```bash
   composer install
   ```

4. Скопируйте .env.example в .env и настройте базу данных:
   ```bash
   cp .env.example .env
   ```

5. Настройте подключение к базе данных:
   ```env
   DB_CONNECTION=pgsql
   DB_HOST=5.35.85.110
   DB_PORT=5432
   DB_DATABASE=b2bstorage
   DB_USERNAME=b2buser
   DB_PASSWORD=B2B_Storage_2024!
   ```

6. Сгенерируйте ключ приложения:
   ```bash
   php artisan key:generate
   ```

7. Запустите миграции:
   ```bash
   php artisan migrate
   ```

## SFTP Настройка

### Frontend
- Конфигурация: `frontend/.vscode/sftp.json`
- Удаленный путь: `/var/www/b2bstorage.ru/public_html`
- Автозагрузка при сохранении включена

### Backend
- Конфигурация: `backend/.vscode/sftp.json`
- Удаленный путь: `/var/www/b2bstorage-app`
- Автозагрузка при сохранении включена

## Сервер

- **IP:** 5.35.85.110
- **Домен:** b2bstorage.ru
- **SSL:** Настроен (Let's Encrypt)
- **База данных:** PostgreSQL
- **Кэш:** Redis
- **WebSockets:** ws.b2bstorage.ru

## Технологии

### Frontend
- Vue.js 3
- DaisyUI (Tailwind CSS)
- Vite

### Backend
- Laravel 12
- PostgreSQL 15
- Redis
- Laravel WebSockets

## Развертывание

1. **Frontend:**
   ```bash
   cd frontend
   npm run build
   # Содержимое dist/ папки копируется в /var/www/b2bstorage.ru/public_html
   ```

2. **Backend:**
   ```bash
   cd backend
   composer install --optimize-autoloader --no-dev
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

## Полезные команды

### Frontend
- `npm run dev` - Запуск сервера разработки
- `npm run build` - Сборка для продакшена
- `npm run preview` - Предварительный просмотр сборки

### Backend
- `php artisan serve` - Запуск сервера разработки
- `php artisan migrate` - Запуск миграций
- `php artisan make:controller` - Создание контроллера
- `php artisan make:model` - Создание модели

## CI/CD

Проект настроен с автоматическим деплоем через GitHub Actions. При пуше в ветку `main` происходит автоматический деплой на сервер.

### Структура проекта

- `b2b-project/frontend/` - Vue.js фронтенд
- `b2b-project/backend/` - Laravel бэкенд

### Технологии

- **Frontend**: Vue.js 3, Tailwind CSS, Vite
- **Backend**: Laravel 11, SQLite, Sanctum
- **Deployment**: GitHub Actions, Nginx, PHP-FPM

### Быстрый старт

1. Клонируйте репозиторий
2. Настройте окружение для разработки
3. Запустите фронтенд: `cd b2b-project/frontend && npm run dev`
4. Запустите бэкенд: `cd b2b-project/backend && php artisan serve`

### Деплой

Автоматический деплой происходит при пуше в ветку `main`. GitHub Actions:
- Собирает фронтенд
- Устанавливает зависимости бэкенда
- Загружает файлы на сервер
- Настраивает права доступа
- Перезапускает сервисы 