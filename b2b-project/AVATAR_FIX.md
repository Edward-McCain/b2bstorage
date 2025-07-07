# Исправление отображения аватара

## Проблема
Аватар загружается в базу данных, но не отображается на сайте.

## Решение

### 1. Проверка файла
```bash
# Проверить, что файл существует
ls -la storage/app/public/uploads/avatars/avatar_39_1751881477.jpg

# Проверить доступность через веб
curl -I http://localhost:8000/storage/uploads/avatars/avatar_39_1751881477.jpg
```

### 2. Обновление Header.vue
Добавлено отображение реального аватара вместо инициала:

```vue
<!-- Аватар -->
<div v-if="user?.avatar_url" class="h-8 w-8 rounded-full overflow-hidden">
  <img 
    :src="user.avatar_url" 
    :alt="user?.user_name || 'Аватар'"
    class="h-full w-full object-cover"
  />
</div>
<div v-else class="h-8 w-8 rounded-full bg-blue-700 flex items-center justify-center text-white font-medium text-sm">
  {{ (user?.user_name || 'П').charAt(0).toUpperCase() }}
</div>
```

### 3. Добавление обработчика событий
В Header.vue добавлен обработчик события `avatar-updated`:

```javascript
// Обработчик обновления аватара
const handleAvatarUpdated = (newAvatarUrl) => {
  if (user.value) {
    user.value.avatar_url = newAvatarUrl
    // Обновляем данные в localStorage
    localStorage.setItem('user', JSON.stringify(user.value))
  }
}

// Слушаем событие обновления аватара
window.addEventListener('avatar-updated', (event) => {
  handleAvatarUpdated(event.detail)
})
```

### 4. Отправка события из AccountSettingsPage
В AccountSettingsPage.vue добавлена отправка глобального события:

```javascript
// Отправляем глобальное событие
window.dispatchEvent(new CustomEvent('avatar-updated', {
  detail: response.data.data.avatar_url
}))
```

## Проверка

### 1. Проверить URL в базе данных
```bash
php artisan tinker --execute="echo App\Models\User::find(39)->avatar_url;"
```

### 2. Проверить доступность файла
```bash
curl -s -o /dev/null -w "%{http_code}" http://localhost:8000/storage/uploads/avatars/avatar_39_1751881477.jpg
```

### 3. Проверить в браузере
- Откройте http://localhost:5173
- Зайдите в настройки аккаунта
- Загрузите новый аватар
- Проверьте, что аватар отображается в шапке

## Возможные проблемы

### 1. Файл не найден
- Проверить права доступа к папке storage
- Убедиться, что символическая ссылка создана: `php artisan storage:link`

### 2. Неправильный URL
- Проверить, что в базе данных сохранен правильный путь
- Убедиться, что путь начинается с `/storage/`

### 3. Кэширование браузера
- Очистить кэш браузера
- Добавить версию к URL: `?v=1`

### 4. CORS проблемы
- Проверить настройки CORS в Laravel
- Убедиться, что фронтенд и бэкенд на правильных портах

## Тестирование

Используйте файл `test-avatar.html` для проверки отображения аватара:

```bash
open test-avatar.html
```

## Результат

После исправления:
- ✅ Аватар загружается в базу данных
- ✅ Файл сохраняется в правильной папке
- ✅ Аватар отображается в шапке сайта
- ✅ Аватар обновляется в реальном времени 