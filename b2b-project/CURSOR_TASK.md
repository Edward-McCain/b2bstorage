# 🎯 Задача для Cursor: Интеграция WebRTC звонков

## 📋 Что нужно сделать

Интегрировать WebRTC звонки в проект, используя внешний SDK. Убрать существующие сложные скрипты и заменить их простыми вызовами функций.

## 🚀 Пошаговый план

### 1. Подключение SDK

**Файл:** `index.html` или `app.html` (главный HTML файл)

**Добавить в `<head>`:**
```html
<script src="https://45.92.173.142:8443/sdk/webrtc-integration-v2.js"></script>
```

### 2. Удалить существующие файлы

**Удалить эти файлы:**
- `useWebRTC.js` (если есть)
- `useWebRTC-optimized.js` (если есть)
- Любые другие файлы с WebRTC логикой

### 3. Упростить компонент списка пользователей

**Файл:** `UsersListPage.vue` (или аналогичный)

**Заменить весь код на:**

```vue
<template>
  <div class="users-list-page">
    <!-- Статус WebRTC -->
    <div class="webrtc-status">
      <div class="status-indicator" :class="{ 
        'connected': isConnected, 
        'disconnected': !isConnected,
        'loading': !isWebRTCLoaded 
      }">
        <span class="status-dot"></span>
        {{ getStatusText() }}
      </div>
      
      <div class="status-actions">
        <button @click="refreshUsers" :disabled="!isWebRTCLoaded" class="btn btn-secondary">
          🔄 Обновить
        </button>
        <button @click="endCall" :disabled="!isConnected" class="btn btn-danger">
          📞 Завершить звонок
        </button>
      </div>
    </div>

    <!-- Список пользователей -->
    <div class="users-grid">
      <div v-for="user in onlineUsers" :key="user.userId" class="user-card">
        <div class="user-info">
          <img :src="user.avatar" :alt="user.name" class="user-avatar" />
          <div class="user-details">
            <h3 class="user-name">{{ user.name }}</h3>
            <p class="user-id">{{ user.userId }}</p>
            <span class="online-status">● онлайн</span>
          </div>
        </div>
        
        <div class="user-actions">
          <button 
            @click="startVideoCall(user.userId)" 
            :disabled="!isReady"
            class="btn btn-video"
            title="Видео звонок"
          >
            📹
          </button>
          <button 
            @click="startAudioCall(user.userId)" 
            :disabled="!isReady"
            class="btn btn-audio"
            title="Аудио звонок"
          >
            📞
          </button>
        </div>
      </div>
    </div>

    <!-- Сообщение если нет пользователей -->
    <div v-if="onlineUsers.length === 0" class="no-users">
      <p>Нет пользователей онлайн</p>
      <button @click="refreshUsers" class="btn btn-primary">
        🔄 Обновить список
      </button>
    </div>

    <!-- Индикатор загрузки -->
    <div v-if="!isWebRTCLoaded" class="loading">
      <p>Загрузка WebRTC...</p>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'

// Состояние
const isWebRTCLoaded = ref(false)
const isConnected = ref(false)
const onlineUsers = ref([])

// Вычисляемые свойства
const isReady = computed(() => isWebRTCLoaded.value && isConnected.value)

// Функции
const getStatusText = () => {
  if (!isWebRTCLoaded.value) return 'Загрузка WebRTC...'
  if (isConnected.value) return 'Подключен к WebRTC'
  return 'Не подключен к WebRTC'
}

const refreshUsers = async () => {
  try {
    const users = await window.WebRTC.getOnlineUsers()
    onlineUsers.value = users
    console.log('👥 Пользователи обновлены:', users.length)
  } catch (error) {
    console.error('Ошибка обновления пользователей:', error)
  }
}

const startVideoCall = async (userId) => {
  try {
    await window.WebRTC.videoCall(userId)
    console.log('📹 Видео звонок начат')
  } catch (error) {
    console.error('Ошибка видео звонка:', error)
    alert(`Ошибка: ${error.message}`)
  }
}

const startAudioCall = async (userId) => {
  try {
    await window.WebRTC.audioCall(userId)
    console.log('📞 Аудио звонок начат')
  } catch (error) {
    console.error('Ошибка аудио звонка:', error)
    alert(`Ошибка: ${error.message}`)
  }
}

const endCall = () => {
  try {
    window.WebRTC.endCall()
    console.log('📞 Звонок завершен')
  } catch (error) {
    console.error('Ошибка завершения звонка:', error)
  }
}

// Инициализация
const initializeWebRTC = () => {
  const checkWebRTC = () => {
    if (window.WebRTC) {
      console.log('✅ WebRTC SDK загружен')
      isWebRTCLoaded.value = true
      setupEventHandlers()
      refreshUsers()
    } else {
      console.log('⏳ Ожидание загрузки WebRTC SDK...')
      setTimeout(checkWebRTC, 100)
    }
  }
  checkWebRTC()
}

// Настройка обработчиков событий
const setupEventHandlers = () => {
  if (!window.WebRTC) return

  window.WebRTC.on('registration_success', (userData) => {
    console.log('✅ Пользователь зарегистрирован:', userData)
    isConnected.value = true
  })

  window.WebRTC.on('registration_error', (error) => {
    console.error('❌ Ошибка регистрации:', error)
    isConnected.value = false
  })

  window.WebRTC.on('call_started', (callData) => {
    console.log('📞 Звонок начат:', callData)
  })

  window.WebRTC.on('call_ended', (callData) => {
    console.log('📞 Звонок завершен:', callData)
  })
}

// Автообновление пользователей
let refreshInterval = null

const startAutoRefresh = () => {
  refreshInterval = setInterval(refreshUsers, 30000) // каждые 30 секунд
}

const stopAutoRefresh = () => {
  if (refreshInterval) {
    clearInterval(refreshInterval)
    refreshInterval = null
  }
}

onMounted(() => {
  initializeWebRTC()
  startAutoRefresh()
})

onUnmounted(() => {
  stopAutoRefresh()
  if (window.WebRTC) {
    window.WebRTC.endCall()
  }
})
</script>

<style scoped>
.users-list-page {
  padding: 20px;
  max-width: 1200px;
  margin: 0 auto;
}

.webrtc-status {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  padding: 15px;
  background: #f8f9fa;
  border-radius: 8px;
  border: 1px solid #e9ecef;
}

.status-indicator {
  display: flex;
  align-items: center;
  gap: 10px;
  font-weight: bold;
}

.status-dot {
  width: 12px;
  height: 12px;
  border-radius: 50%;
  background: #6c757d;
}

.status-indicator.connected .status-dot {
  background: #28a745;
}

.status-indicator.disconnected .status-dot {
  background: #dc3545;
}

.status-indicator.loading .status-dot {
  background: #ffc107;
  animation: pulse 1.5s infinite;
}

@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.5; }
}

.status-actions {
  display: flex;
  gap: 10px;
}

.users-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 20px;
  margin-bottom: 20px;
}

.user-card {
  background: white;
  border: 1px solid #e9ecef;
  border-radius: 8px;
  padding: 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  transition: box-shadow 0.3s ease;
}

.user-card:hover {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.user-info {
  display: flex;
  align-items: center;
  gap: 15px;
}

.user-avatar {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  object-fit: cover;
}

.user-details {
  flex: 1;
}

.user-name {
  margin: 0 0 5px 0;
  font-size: 16px;
  font-weight: bold;
  color: #495057;
}

.user-id {
  margin: 0 0 5px 0;
  font-size: 12px;
  color: #6c757d;
  font-family: monospace;
}

.online-status {
  color: #28a745;
  font-size: 12px;
  font-weight: bold;
}

.user-actions {
  display: flex;
  gap: 10px;
}

.btn {
  padding: 8px 16px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 14px;
  font-weight: bold;
  transition: all 0.3s ease;
  min-width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.btn-primary {
  background: #007bff;
  color: white;
}

.btn-primary:hover:not(:disabled) {
  background: #0056b3;
}

.btn-secondary {
  background: #6c757d;
  color: white;
}

.btn-secondary:hover:not(:disabled) {
  background: #545b62;
}

.btn-danger {
  background: #dc3545;
  color: white;
}

.btn-danger:hover:not(:disabled) {
  background: #c82333;
}

.btn-video {
  background: #ff9800;
  color: white;
}

.btn-video:hover:not(:disabled) {
  background: #f57c00;
}

.btn-audio {
  background: #9c27b0;
  color: white;
}

.btn-audio:hover:not(:disabled) {
  background: #7b1fa2;
}

.no-users {
  text-align: center;
  padding: 40px;
  background: #f8f9fa;
  border-radius: 8px;
  border: 1px solid #e9ecef;
}

.no-users p {
  margin: 0 0 20px 0;
  color: #6c757d;
  font-size: 16px;
}

.loading {
  text-align: center;
  padding: 40px;
  color: #6c757d;
}
</style>

### 4. Удалить импорты из main.js

**Файл:** `main.js` (или `app.js`)

**Удалить строки:**
```javascript
// Удалить эти строки если есть:
import './useWebRTC.js'
// или
import './useWebRTC-optimized.js'
```

### 5. Добавить кнопки звонков в другие компоненты

**Для любого компонента, где нужны кнопки звонков:**

```vue
<template>
  <div>
    <!-- Кнопки звонка -->
    <button @click="startVideoCall(userId)" :disabled="!isUserOnline">
      📹 Видео звонок
    </button>
    <button @click="startAudioCall(userId)" :disabled="!isUserOnline">
      📞 Аудио звонок
    </button>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const props = defineProps(['userId'])
const isUserOnline = ref(false)

const startVideoCall = async () => {
  try {
    await window.WebRTC.videoCall(props.userId)
  } catch (error) {
    alert(`Ошибка: ${error.message}`)
  }
}

const startAudioCall = async () => {
  try {
    await window.WebRTC.audioCall(props.userId)
  } catch (error) {
    alert(`Ошибка: ${error.message}`)
  }
}

onMounted(async () => {
  // Проверяем онлайн статус
  isUserOnline.value = await window.WebRTC.checkUserOnline(props.userId)
})
</script>
```

### 6. Проверить localStorage

**Убедиться, что в localStorage есть:**

```javascript
// Проверить в консоли браузера:
console.log('Auth token:', localStorage.getItem('auth_token'))
console.log('User data:', localStorage.getItem('user'))

// Если нет, добавить:
localStorage.setItem('auth_token', 'user_id|token_part')
localStorage.setItem('user', JSON.stringify({
  user_id: '4442c7fb-d338-44a6-9321-bb4bcb5b76ec',
  name: 'Имя пользователя'
}))
```

### 7. Исправить проблемы загрузки SDK

**Проверить в index.html:**
```html
<!-- Должно быть в <head> или перед закрывающим </body> -->
<script src="https://45.92.173.142:8443/sdk/webrtc-integration-v2.js"></script>
```

**Заменить функцию initializeWebRTC в UsersListPage.vue:**
```javascript
// Инициализация
const initializeWebRTC = () => {
  let attempts = 0;
  const maxAttempts = 100; // 10 секунд максимум
  
  const checkWebRTC = () => {
    attempts++;
    
    // Проверяем доступность SDK
    if (window.WebRTC && typeof window.WebRTC.videoCall === 'function') {
      console.log('✅ WebRTC SDK загружен и готов к работе')
      isWebRTCLoaded.value = true
      setupEventHandlers()
      refreshUsers()
      return;
    }
    
    // Проверяем, не превышен ли лимит попыток
    if (attempts >= maxAttempts) {
      console.error('❌ WebRTC SDK не загрузился за 10 секунд')
      console.error('Проверьте подключение SDK в index.html')
      return;
    }
    
    // Логируем каждые 10 попыток
    if (attempts % 10 === 0) {
      console.log(`⏳ Ожидание загрузки WebRTC SDK... (${attempts}/${maxAttempts})`)
    }
    
    setTimeout(checkWebRTC, 100)
  }
  
  // Начинаем проверку
  console.log('🚀 Начинаем инициализацию WebRTC SDK...')
  checkWebRTC()
}
```

**Добавить диагностику в onMounted:**
```javascript
onMounted(() => {
  // Проверяем, подключен ли SDK
  console.log('🔍 Проверка подключения SDK...')
  console.log('window.WebRTC:', window.WebRTC)
  console.log('document.scripts:', Array.from(document.scripts).map(s => s.src))
  
  initializeWebRTC()
  startAutoRefresh()
})
```

### 8. Альтернативный способ подключения SDK

**Если SDK не загружается, попробовать динамическое подключение:**

```javascript
// Добавить в UsersListPage.vue перед initializeWebRTC
const loadWebRTCSDK = () => {
  return new Promise((resolve, reject) => {
    // Проверяем, не загружен ли уже SDK
    if (window.WebRTC) {
      resolve();
      return;
    }
    
    // Создаем script элемент
    const script = document.createElement('script');
    script.src = 'https://45.92.173.142:8443/sdk/webrtc-integration-v2.js';
    script.onload = () => {
      console.log('✅ WebRTC SDK загружен динамически')
      resolve();
    };
    script.onerror = () => {
      console.error('❌ Ошибка загрузки WebRTC SDK')
      reject(new Error('Не удалось загрузить WebRTC SDK'));
    };
    
    // Добавляем в head
    document.head.appendChild(script);
  });
}

// Изменить initializeWebRTC
const initializeWebRTC = async () => {
  try {
    await loadWebRTCSDK();
    
    // Теперь SDK должен быть доступен
    if (window.WebRTC && typeof window.WebRTC.videoCall === 'function') {
      console.log('✅ WebRTC SDK готов к работе')
      isWebRTCLoaded.value = true
      setupEventHandlers()
      refreshUsers()
    } else {
      console.error('❌ WebRTC SDK загружен, но функции недоступны')
    }
  } catch (error) {
    console.error('❌ Ошибка инициализации WebRTC:', error)
  }
}
```

## 🧹 Что удалить

1. **Файлы:**
   - `useWebRTC.js`
   - `useWebRTC-optimized.js`
   - Любые другие файлы с WebRTC логикой

2. **Импорты:**
   - Удалить все импорты useWebRTC из компонентов
   - Удалить импорты из main.js

3. **Сложную логику:**
   - Удалить сложные composables
   - Удалить дублирующие функции

## ✅ Что проверить после изменений

1. **Загрузка SDK:**
   ```javascript
   console.log('WebRTC SDK:', window.WebRTC)
   ```

2. **Данные пользователя:**
   ```javascript
   console.log('Auth token:', localStorage.getItem('auth_token'))
   console.log('User data:', localStorage.getItem('user'))
   ```

3. **Функции звонков:**
   ```javascript
   // Должны работать:
   window.WebRTC.videoCall('user-id')
   window.WebRTC.audioCall('user-id')
   window.WebRTC.getOnlineUsers()
   ```

## 🎯 Результат

После выполнения всех изменений:
- ✅ Простые кнопки звонков работают
- ✅ Нет лишних файлов и кода
- ✅ Используется только внешний SDK
- ✅ Минимальная интеграция

## 📞 Доступные функции

```javascript
// Основные функции звонков
WebRTC.videoCall(userId)
WebRTC.audioCall(userId)
WebRTC.endCall()

// Проверка статуса
WebRTC.checkUserOnline(userId)
WebRTC.getOnlineUsers()

// События
WebRTC.on('registration_success', callback)
WebRTC.on('call_started', callback)
WebRTC.on('call_ended', callback)
```

Всё готово! Просто выполните эти шаги и WebRTC звонки будут работать. 🎉
