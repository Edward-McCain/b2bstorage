# Универсальная настройка API

## Обзор

Система настроена для автоматического определения домена и использования соответствующего API URL.

## Как это работает

### Автоматическое определение домена

Система автоматически определяет окружение по следующим критериям:

1. **Продакшн домен**: `b2bsklad.uz` или `www.b2bsklad.uz`
   - API: `https://b2bsklad.uz/api`
   - Storage: `https://b2bsklad.uz`

2. **Тестовый домен**: `b2bstorage.ru`, `www.b2bstorage.ru` или `api.b2bstorage.ru`
   - API: `https://api.b2bstorage.ru/api`
   - Storage: `https://api.b2bstorage.ru`

3. **Локальная разработка**: `localhost` или `127.0.0.1`
   - API: `http://127.0.0.1:8000/api`
   - Storage: `http://127.0.0.1:8000`

4. **IP адрес сервера**: `45.92.173.142`
   - API: `https://b2bsklad.uz/api`
   - Storage: `https://b2bsklad.uz`

## Конфигурация

### Файл: `frontend/src/config/api.js`

```javascript
// Функция для определения API URL на основе текущего домена
function getApiBaseUrl() {
  const hostname = window.location.hostname;
  
  // Продакшн домен
  if (hostname === 'b2bsklad.uz' || hostname === 'www.b2bsklad.uz') {
    return 'https://b2bsklad.uz/api';
  }
  
  // Тестовый домен
  if (hostname === 'b2bstorage.ru' || hostname === 'www.b2bstorage.ru' || hostname === 'api.b2bstorage.ru') {
    return 'https://api.b2bstorage.ru/api';
  }
  
  // Локальная разработка
  if (hostname === 'localhost' || hostname === '127.0.0.1') {
    return 'http://127.0.0.1:8000/api';
  }
  
  // По умолчанию для IP адресов (продакшн)
  if (hostname === '45.92.173.142') {
    return 'https://b2bsklad.uz/api';
  }
  
  // Fallback на продакшн
  return 'https://b2bsklad.uz/api';
}
```

## Использование

### Импорт
```javascript
import { apiRequest, getApiUrl, getFileUrl } from '@/config/api'
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

// Получение URL файла
const avatarUrl = getFileUrl('/storage/uploads/avatars/filename.jpg')
```

## Отладка

В консоли браузера выводится информация о текущем домене и API URL:

```javascript
console.log('Current hostname:', window.location.hostname);
console.log('API Base URL:', apiConfig.baseURL);
console.log('Storage Base URL:', apiConfig.storageURL);
```

## Деплой

### Для тестового сервера
```bash
git push origin main
```

### Для продакшн сервера
```bash
git push origin production
```

## Структура доменов

### Тестовый сервер
- Frontend: `https://b2bstorage.ru`
- API: `https://api.b2bstorage.ru/api`

### Продакшн сервер
- Frontend: `https://b2bsklad.uz`
- API: `https://b2bsklad.uz/api`

### Локальная разработка
- Frontend: `http://localhost:3000`
- API: `http://127.0.0.1:8000/api`

## Преимущества

1. **Автоматическое определение** - не нужно менять код при деплое
2. **Универсальность** - один код работает везде
3. **Простота** - разработчики не думают о доменах
4. **Надежность** - fallback на продакшн при ошибках

## Обновленные email адреса

- Support: `support@b2bsklad.uz`
- Admin: `admin@b2bsklad.uz` 