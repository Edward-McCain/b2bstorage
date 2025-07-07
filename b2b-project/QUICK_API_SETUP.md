# Быстрая настройка универсального API

## ✅ Что уже настроено

1. **Универсальная конфигурация API** - автоматически определяет локальную разработку или продакшн
2. **Правильные пути сохранения файлов** - `/storage/uploads/avatars/` и `/storage/uploads/products/`
3. **Относительные URL в БД** - работают и на локалке, и на сервере
4. **Автоматическое добавление токенов** - все запросы включают авторизацию

## 🚀 Как использовать

### В компонентах Vue:
```javascript
import { apiRequest } from '@/config/api'

// Загрузка аватара
const response = await apiRequest('/user/avatar', {
  method: 'POST',
  body: JSON.stringify({ avatar: base64Image })
})
```

### Токен авторизации:
- Ключ в localStorage: `auth_token`
- Автоматически добавляется ко всем запросам

## 📁 Структура папок

```
storage/app/public/uploads/
├── avatars/     # Аватары пользователей
└── products/    # Изображения товаров
```

## 🌐 URL для доступа к файлам

- **Локально**: `http://localhost:8000/storage/uploads/avatars/filename.jpg`
- **Сервер**: `https://your-domain.com/storage/uploads/avatars/filename.jpg`

## 🔧 Настройка сервера

1. Создайте папки:
```bash
mkdir -p storage/app/public/uploads/avatars
mkdir -p storage/app/public/uploads/products
```

2. Установите права:
```bash
chmod -R 755 storage/app/public/uploads
```

3. Создайте символическую ссылку:
```bash
php artisan storage:link
```

## 📤 Деплой на сервер

Используйте скрипт `deploy-uploads.sh` для копирования папок с загрузками на сервер.

## ✅ Готово!

Теперь система работает универсально:
- Автоматически определяет окружение
- Использует правильные API URL
- Сохраняет файлы в правильных папках
- Работает и на локалке, и на сервере 