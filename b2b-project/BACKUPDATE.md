
Работай только с /var/www/b2bstorage-backend на VPS

после переключения базы данных и перед деплоем переключать на серверную
cd b2b-project/backend && php artisan config:clear



#!/bin/bash
# Быстрое переключение на серверную базу данных
sed -i '' 's/LOCAL_DB=true/LOCAL_DB=false/g' b2b-project/backend/.env && cd b2b-project/backend && php artisan config:clear && echo "✅ Переключено на серверную БД" 


#!/bin/bash
# Быстрое переключение на локальную базу данных
sed -i '' 's/LOCAL_DB=false/LOCAL_DB=true/g' b2b-project/backend/.env && cd b2b-project/backend && php artisan config:clear && echo "✅ Переключено на локальную БД" 


# запуск на локалке фронт и бэк:

cd b2b-project/backend
php artisan serve --host=127.0.0.1 --port=8000


cd b2b-project/frontend
npm run dev




# 📋 Инструкция по деплою Laravel + Vue.js проекта

## 🏠 **1. Разработка на локалке**
```bash
# Работаете в папках:
# - b2b-project/backend/ (Laravel)
# - b2b-project/frontend/ (Vue.js)
```

## 📤 **2. Подготовка к деплою**
```bash
# 1. Сохраните все изменения в Git
cd b2b-project/backend
git add .
git commit -m "Обновление API: добавлены методы для товаров"
git push origin main

cd ../frontend
git add .
git commit -m "Обновление фронтенда: добавлена страница товаров"
git push origin main
```

## 🚀 **3. Деплой на сервер**

### **Backend (Laravel):**
```bash
# Подключитесь к серверу
ssh root@5.35.85.110

# Перейдите в папку проекта
cd /var/www/b2bstorage-backend

# Получите изменения из Git
git pull origin main

# Установите зависимости (если есть новые)
composer install --no-dev --optimize-autoloader

# Выполните миграции (если есть новые)
php artisan migrate --force

# Очистите все кэши
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Пересоздайте кэш для продакшена
php artisan config:cache
php artisan route:cache

# Обновите автозагрузчик
composer dump-autoload

# Проверьте права доступа
chmod -R 755 storage
chmod -R 755 bootstrap/cache

# Создайте символическую ссылку для storage (если не существует)
php artisan storage:link
```

### **Frontend (Vue.js):**
```bash
# Перейдите в папку фронтенда
cd /var/www/b2bstorage-app

# Получите изменения из Git
git pull origin main

# Установите зависимости (если есть новые)
npm install

# Соберите проект для продакшена
npm run build

# Проверьте права доступа
chown -R www-data:www-data .
```

## ⚙️ **4. Проверка конфигурации**

### **Проверьте nginx конфигурации:**
```bash
# Проверьте пути в конфигурации
cat /etc/nginx/sites-available/b2bstorage-api
cat /etc/nginx/sites-available/b2bstorage.ru

# Убедитесь, что пути правильные:
# - Backend: /var/www/b2bstorage-backend/public
# - Frontend: /var/www/b2bstorage-app/dist (или public)

# Проверьте конфигурацию nginx
nginx -t

# Перезагрузите nginx
systemctl reload nginx
```

### **Проверьте .env файлы:**
```bash
# Backend .env
cd /var/www/b2bstorage-backend
grep -E "APP_ENV|APP_DEBUG|APP_URL" .env

# Должно быть:
# APP_ENV=production
# APP_DEBUG=false
# APP_URL=https://api.b2bstorage.ru
```

## 🧪 **5. Тестирование**

### **Проверьте API:**
```bash
# Проверьте роуты
php artisan route:list --path=api

# Протестируйте API
curl -X GET https://api.b2bstorage.ru/api/categories
curl -X POST https://api.b2bstorage.ru/api/login -H "Content-Type: application/json" -d '{"email":"test@example.com","password":"password123"}'
```

### **Проверьте фронтенд:**
```bash
# Откройте в браузере:
# https://b2bstorage.ru
# Проверьте, что все страницы загружаются
```

## 🔧 **6. Если что-то не работает**

### **Проверьте логи:**
```bash
# Laravel логи
tail -f /var/www/b2bstorage-backend/storage/logs/laravel.log

# Nginx логи
tail -f /var/log/nginx/error.log
tail -f /var/log/nginx/access.log

# PHP-FPM логи
tail -f /var/log/php8.2-fpm.log
```

### **Частые проблемы:**
```bash
# 1. Неправильные пути в nginx
# 2. Не обновлены кэши Laravel
# 3. Неправильные права доступа
# 4. Не выполнены миграции
# 5. Не установлены зависимости
```

## 📝 **7. Чек-лист после деплоя**

- [ ] Git pull выполнен для backend и frontend
- [ ] Composer install выполнен (если есть новые зависимости)
- [ ] NPM install и build выполнен для frontend
- [ ] Миграции выполнены
- [ ] Кэши очищены и пересозданы
- [ ] Права доступа установлены
- [ ] Nginx перезагружен
- [ ] API протестирован
- [ ] Фронтенд протестирован

## 🚨 **8. Экстренные команды**

```bash
# Полная очистка и пересоздание кэша
php artisan optimize:clear
php artisan optimize

# Перезапуск всех сервисов
systemctl restart nginx
systemctl restart php8.2-fpm

# Проверка статуса сервисов
systemctl status nginx
systemctl status php8.2-fpm
```

## 🔄 **9. Быстрый деплой (если только код, без новых зависимостей)**

```bash
# Backend
cd /var/www/b2bstorage-backend
git pull origin main
php artisan cache:clear
php artisan route:clear
php artisan route:cache
composer dump-autoload

# Frontend
cd /var/www/b2bstorage-app
git pull origin main
npm run build
```

## 📞 **10. Контакты для поддержки**

- **Сервер:** 5.35.85.110
- **Домены:** 
  - Frontend: https://b2bstorage.ru
  - API: https://api.b2bstorage.ru
- **Пути на сервере:**
  - Backend: `/var/www/b2bstorage-backend/`
  - Frontend: `/var/www/b2bstorage-app/`

---

**Примечание:** Эта инструкция поможет вам быстро и правильно деплоить изменения на сервер! 🎉