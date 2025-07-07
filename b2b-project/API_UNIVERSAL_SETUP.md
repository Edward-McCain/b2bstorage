# Универсальная настройка API

## Обзор

Система настроена для автоматического определения окружения и использования соответствующих API URL.

## Конфигурация

### Файл: `frontend/src/config/api.js`

```javascript
const API_CONFIG = {
  // Локальная разработка
  development: {
    baseURL: 'http://127.0.0.1:8000/api',
    timeout: 10000
  },
  // Продакшн
  production: {
    baseURL: 'https://api.b2bstorage.ru/api',
    timeout: 10000
  }
}
```

## Автоматическое определение окружения

Система автоматически определяет окружение по следующим критериям:
- `import.meta.env.DEV` (Vite dev mode)
- `window.location.hostname === 'localhost'`
- `window.location.hostname === '127.0.0.1'`

## Использование

### Импорт
```javascript
import { apiRequest, getApiUrl } from '@/config/api'
```

### Выполнение запросов
```javascript
// GET запрос
const response = await apiRequest('/user/profile')

// POST запрос с данными
const response = await apiRequest('/user/avatar', {
  method: 'POST',
  body: JSON.stringify({ avatar: base64Image })
})
```

## Сохранение файлов

### Структура папок
```
storage/app/public/uploads/
├── avatars/          # Аватары пользователей
└── products/         # Изображения товаров
```

### Пути для доступа
- Локально: `http://localhost:8000/storage/uploads/avatars/filename.jpg`
- На сервере: `https://your-domain.com/storage/uploads/avatars/filename.jpg`

### Относительные пути в БД
Все пути сохраняются в формате: `/storage/uploads/avatars/filename.jpg`

## Деплой

### Скрипт деплоя загрузок
```bash
# Копирование папок с загрузками на сервер
./deploy-uploads.sh
```

### Настройка сервера
1. Убедитесь, что папка `storage/app/public/uploads/` существует на сервере
2. Установите правильные права доступа: `chmod -R 755 storage/app/public/uploads/`
3. Создайте символическую ссылку: `php artisan storage:link`

## Примеры использования

### Загрузка аватара
```javascript
const response = await apiRequest('/user/avatar', {
  method: 'POST',
  body: JSON.stringify({
    avatar: base64Image
  })
})

if (response.ok) {
  avatarUrl.value = response.data.data.avatar_url
}
```

### Загрузка изображения товара
```javascript
const formData = new FormData()
formData.append('image', file)
formData.append('alt_text', 'Описание изображения')

const response = await apiRequest('/products/123/images', {
  method: 'POST',
  body: formData
})
```

## Безопасность

- Все запросы автоматически включают токен авторизации из localStorage
- Content-Type автоматически устанавливается для JSON и FormData
- Таймаут запросов: 10 секунд
- Валидация на стороне сервера для всех загрузок файлов 