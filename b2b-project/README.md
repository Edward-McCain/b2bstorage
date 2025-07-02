# B2B Storage Project

Профессиональная система управления хранилищем для B2B компаний с Laravel backend и Vue.js frontend.

## 🏗️ Архитектура

- **Backend**: Laravel 12 + PostgreSQL + Redis
- **Frontend**: Vue.js 3 + DaisyUI + Tailwind CSS
- **WebSockets**: Laravel WebSockets
- **Сервер**: Ubuntu 22.04 + Nginx + SSL

## 📁 Структура проекта

```
b2b-project/
├── backend/                 # Laravel API
│   ├── app/
│   ├── routes/
│   ├── database/
│   └── ...
├── frontend/               # Vue.js SPA
│   ├── src/
│   ├── public/
│   ├── deploy.sh
│   └── ...
└── README.md
```

## 🚀 Быстрый старт

### Локальная разработка

1. **Backend (Laravel)**
   ```bash
   cd backend
   composer install
   cp .env.example .env
   php artisan key:generate
   php artisan migrate
   php artisan serve
   ```

2. **Frontend (Vue.js)**
   ```bash
   cd frontend
   npm install
   npm run dev
   ```

### Деплой на сервер

1. **Настройка Nginx**
   ```bash
   cd frontend
   ./setup-nginx.sh
   ```

2. **Деплой фронтенда**
   ```bash
   cd frontend
   ./deploy.sh
   ```

3. **Деплой бэкенда**
   ```bash
   cd backend
   ./deploy.sh
   ```

## 🔧 Конфигурация

### Переменные окружения

**Backend (.env)**
```env
APP_NAME="B2B Storage"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://api.b2bstorage.ru

DB_CONNECTION=pgsql
DB_HOST=localhost
DB_PORT=5432
DB_DATABASE=b2bstorage
DB_USERNAME=b2buser
DB_PASSWORD=your_password

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

**Frontend (.env)**
```env
VITE_API_URL=https://api.b2bstorage.ru
VITE_APP_NAME="B2B Storage"
```

## 📊 Мониторинг

### Проверка состояния сервисов
```bash
ssh root@5.35.85.110 "systemctl status nginx postgresql redis php8.2-fpm"
```

### Логи
- Nginx: `/var/log/nginx/`
- Laravel: `/var/www/b2bstorage-app/storage/logs/`
- PostgreSQL: `/var/log/postgresql/`
- Redis: `/var/log/redis/`

## 🔒 Безопасность

- SSL сертификаты настроены автоматически
- CORS настроен для API
- Security headers добавлены
- Доступ к скрытым файлам заблокирован

## 📈 Масштабирование

### Горизонтальное масштабирование
- Добавление новых серверов
- Настройка балансировщика нагрузки
- Репликация базы данных

### Вертикальное масштабирование
- Увеличение ресурсов сервера
- Оптимизация запросов
- Кэширование

## 🛠️ Разработка

### Добавление новых функций

1. **Backend API**
   ```bash
   cd backend
   php artisan make:controller NewController
   php artisan make:model NewModel -m
   ```

2. **Frontend компоненты**
   ```bash
   cd frontend
   # Создать новый компонент в src/components/
   ```

### Тестирование
```bash
# Backend
cd backend
php artisan test

# Frontend
cd frontend
npm run test
```

## 📞 Поддержка

- **Документация**: [Laravel Docs](https://laravel.com/docs)
- **Vue.js**: [Vue.js Docs](https://vuejs.org/guide/)
- **DaisyUI**: [DaisyUI Docs](https://daisyui.com/)

## 🔄 Обновления

### Обновление зависимостей
```bash
# Backend
cd backend
composer update

# Frontend
cd frontend
npm update
```

### Обновление сервера
```bash
ssh root@5.35.85.110 "apt update && apt upgrade -y"
```

## 📝 Лицензия

Copyright © 2025 B2B Global. All rights reserved. 