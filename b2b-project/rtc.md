# 🚀 Простая интеграция WebRTC звонков

## 📋 Быстрый старт

### 1. Подключение SDK

Добавьте 
<script src="https://45.92.173.142:8443/sdk/webrtc-integration-v2.js"></script>

### 2. Настройка данных пользователя

Убедитесь, что в `localStorage` есть данные пользователя:

```javascript
// Токен аутентификации
localStorage.setItem('auth_token', 'user_id|token_part');

// Данные пользователя (JSON)
localStorage.setItem('user', JSON.stringify({
  user_id: '4442c7fb-d338-44a6-9321-bb4bcb5b76ec',
  name: 'Иван Иванов',
  // другие данные...
}));
```

## 📞 Функции для звонков

### Простые функции звонка

```javascript
// Видео звонок
WebRTC.videoCall('target-user-id');

// Аудио звонок
WebRTC.audioCall('target-user-id');
```

### Функции с проверкой онлайн статуса

```javascript
// Звонок только если пользователь онлайн
WebRTC.callUserIfOnline('target-user-id', 'video');
WebRTC.callUserIfOnline('target-user-id', 'audio');
```

### Проверка онлайн статуса

```javascript
// Проверить, онлайн ли пользователь
const isOnline = await WebRTC.checkUserOnline('target-user-id');
console.log('Пользователь онлайн:', isOnline);
```

### Получение списка пользователей онлайн

```javascript
// Получить всех пользователей онлайн
const onlineUsers = await WebRTC.getOnlineUsers();
console.log('Пользователи онлайн:', onlineUsers);
```

## 💡 Примеры использования

### Простая кнопка звонка

```html
<button onclick="startVideoCall('user-id')">📹 Видео звонок</button>
<button onclick="startAudioCall('user-id')">📞 Аудио звонок</button>

<script>
function startVideoCall(userId) {
    WebRTC.videoCall(userId)
        .then(() => console.log('Звонок начат'))
        .catch(error => alert('Ошибка: ' + error.message));
}

function startAudioCall(userId) {
    WebRTC.audioCall(userId)
        .then(() => console.log('Звонок начат'))
        .catch(error => alert('Ошибка: ' + error.message));
}
</script>
```

### Кнопка с проверкой онлайн статуса

```html
<button onclick="callIfOnline('user-id')">📹 Позвонить</button>

<script>
async function callIfOnline(userId) {
    try {
        await WebRTC.callUserIfOnline(userId, 'video');
        console.log('Звонок начат');
    } catch (error) {
        if (error.message === 'Пользователь не в сети') {
            alert('Пользователь не в сети');
        } else {
            alert('Ошибка: ' + error.message);
        }
    }
}
</script>
```

### Список пользователей с кнопками звонков

```html
<div id="users-list"></div>

<script>
async function loadUsers() {
    const users = await WebRTC.getOnlineUsers();
    const container = document.getElementById('users-list');
    
    container.innerHTML = users.map(user => `
        <div class="user-item">
            <span>${user.name}</span>
            <button onclick="WebRTC.videoCall('${user.userId}')">📹</button>
            <button onclick="WebRTC.audioCall('${user.userId}')">📞</button>
        </div>
    `).join('');
}

// Загружаем пользователей при загрузке страницы
loadUsers();
</script>
```

## 🔧 Интеграция в Vue.js

### В компоненте Vue

```vue
<template>
  <div>
    <button @click="startVideoCall(userId)" :disabled="!isUserOnline">
      📹 Видео звонок
    </button>
    <button @click="startAudioCall(userId)" :disabled="!isUserOnline">
      📞 Аудио звонок
    </button>
  </div>
</template>

<script>
export default {
  props: ['userId'],
  data() {
    return {
      isUserOnline: false
    }
  },
  async mounted() {
    // Проверяем онлайн статус
    this.isUserOnline = await window.WebRTC.checkUserOnline(this.userId);
  },
  methods: {
    async startVideoCall() {
      try {
        await window.WebRTC.videoCall(this.userId);
      } catch (error) {
        alert('Ошибка: ' + error.message);
      }
    },
    async startAudioCall() {
      try {
        await window.WebRTC.audioCall(this.userId);
      } catch (error) {
        alert('Ошибка: ' + error.message);
      }
    }
  }
}
</script>
```

## 🔧 Интеграция в React

### React компонент

```jsx
import React, { useState, useEffect } from 'react';

function CallButton({ userId }) {
  const [isOnline, setIsOnline] = useState(false);

  useEffect(() => {
    // Проверяем онлайн статус при загрузке
    const checkOnline = async () => {
      const online = await window.WebRTC.checkUserOnline(userId);
      setIsOnline(online);
    };
    checkOnline();
  }, [userId]);

  const startVideoCall = async () => {
    try {
      await window.WebRTC.videoCall(userId);
    } catch (error) {
      alert('Ошибка: ' + error.message);
    }
  };

  const startAudioCall = async () => {
    try {
      await window.WebRTC.audioCall(userId);
    } catch (error) {
      alert('Ошибка: ' + error.message);
    }
  };

  return (
    <div>
      <button onClick={startVideoCall} disabled={!isOnline}>
        📹 Видео
      </button>
      <button onClick={startAudioCall} disabled={!isOnline}>
        📞 Аудио
      </button>
    </div>
  );
}
```

## 🛠️ Дополнительные функции

### Завершение звонка

```javascript
WebRTC.endCall();
```

### Проверка статуса регистрации

```javascript
if (WebRTC.isRegistered) {
    console.log('Пользователь зарегистрирован');
}
```

### Обработка событий

```javascript
// Настройка обработчиков событий
WebRTC.on('registration_success', (userData) => {
    console.log('✅ Пользователь зарегистрирован:', userData);
});

WebRTC.on('call_started', (callData) => {
    console.log('📞 Звонок начат:', callData);
});

WebRTC.on('call_ended', (callData) => {
    console.log('📞 Звонок завершен:', callData);
});

WebRTC.on('registration_error', (error) => {
    console.error('❌ Ошибка регистрации:', error);
});
```

## ⚠️ Требования

1. **HTTPS соединение** - Для доступа к камере/микрофону
2. **Данные в localStorage**:
   - `auth_token` - токен аутентификации
   - `user` - данные пользователя (JSON)
3. **Разрешения браузера** - Камера и микрофон
4. **Современный браузер** - Поддержка WebRTC

## 🔍 Отладка

### Проверка в консоли браузера

```javascript
// Проверить загрузку SDK
console.log('WebRTC SDK:', window.WebRTC);

// Проверить данные пользователя
console.log('Auth token:', localStorage.getItem('auth_token'));
console.log('User data:', localStorage.getItem('user'));

// Проверить онлайн пользователей
WebRTC.getOnlineUsers().then(users => {
    console.log('Пользователи онлайн:', users);
});
```

### Частые ошибки

1. **"Токен или данные пользователя не найдены"**
   - Проверьте `localStorage.getItem('auth_token')` и `localStorage.getItem('user')`

2. **"Пользователь не в сети"**
   - Пользователь не зарегистрирован в системе WebRTC

3. **"Iframe not ready"**
   - Проблема с загрузкой iframe, попробуйте перезагрузить страницу

## 📱 Готовые примеры

### Простая кнопка звонка
```html
<button onclick="WebRTC.videoCall('user-id')">📹 Позвонить</button>
```

### Кнопка с обработкой ошибок
```html
<button onclick="safeCall('user-id')">📹 Позвонить</button>
<script>
async function safeCall(userId) {
    try {
        await WebRTC.videoCall(userId);
    } catch (error) {
        alert('Ошибка: ' + error.message);
    }
}
</script>
```

Всё готово! Просто подключите SDK и используйте функции `WebRTC.videoCall()` и `WebRTC.audioCall()` на кнопках звонков. 🎉
