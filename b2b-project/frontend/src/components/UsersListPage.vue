<template>
  <div class="users-page" data-version="1.0.0" style="margin-top: 100px;">

    <!-- Загрузка -->
    <div v-if="loading" class="loading-state">
      <div class="loading-spinner"></div>
      <p>Загрузка пользователей...</p>
    </div>

    <!-- Ошибка -->
    <div v-if="error && !loading" class="error-state">
      <AlertCircle class="w-12 h-12" />
      <p>{{ error }}</p>
      <div class="error-actions">
        <button @click="loadUsers" class="retry-btn">
          <RefreshCw class="w-4 h-4" />
          Повторить
        </button>
        <button 
          v-if="error.includes('авторизован')" 
          @click="router.push('/auth')" 
          class="login-btn"
        >
          <LogIn class="w-4 h-4" />
          Войти в систему
        </button>
      </div>
    </div>

    <!-- Таблица пользователей -->
    <div v-if="!loading && !error" class="users-table-container">
      <div class="table-wrapper">
        <table class="users-table">
          <thead>
            <tr>
              <th class="avatar-col">Фото</th>
              <th class="name-col">Имя</th>
              <th class="actions-col">Действия</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="user in users" :key="user.id" class="user-row">
              <td class="avatar-cell">
                <div class="user-avatar">
                  <img 
                    v-if="user.avatar_url" 
                    :src="getAvatarUrl(user.avatar_url)" 
                    :alt="user.user_name"
                    @error="onAvatarError"
                  >
                  <div v-else class="avatar-placeholder">
                    {{ getInitials(user.user_name || user.first_name) }}
                  </div>
                </div>
              </td>
              
              <td class="name-cell">
                <div class="user-info">
                  <div class="user-name">
                    {{ user.user_name || user.first_name || 'Без имени' }}
                    <span 
                      v-if="isUserOnline(user)" 
                      class="online-indicator" style="background: limegreen;width: 10px;height: 10px;display: block;border-radius: 50%;position: absolute;margin: -17px 0 0 -14px;"
                    >
                    </span>
                  </div>
                  <div v-if="user.phone_number" class="user-phone">{{ user.phone_number }}</div>
                </div>
              </td>
              
              <td class="actions-cell">
                <div class="action-buttons">
                  <button 
                    @click="makeAudioCall(user)"
                    class="action-btn audio-call-btn"
                    :title="'Аудиозвонок через API'"
                  >
                    <Phone class="w-4 h-4" />
                  </button>
                  <button 
                    @click="makeVideoCall(user)"
                    class="action-btn video-call-btn"
                    :title="'Видеозвонок через API'"
                  >
                    <Video class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      
      <!-- Пагинация (только если поддерживается) -->
      <div v-if="pagination && pagination.last_page > 1" class="pagination">
        <button 
          @click="changePage(pagination.current_page - 1)"
          :disabled="pagination.current_page <= 1"
          class="pagination-btn"
        >
          <ChevronLeft class="w-4 h-4" />
          Предыдущая
        </button>
        
        <div class="pagination-info">
          Страница {{ pagination.current_page }} из {{ pagination.last_page }}
          ({{ pagination.total }} пользователей)
        </div>
        
        <button 
          @click="changePage(pagination.current_page + 1)"
          :disabled="pagination.current_page >= pagination.last_page"
          class="pagination-btn"
        >
          Следующая
          <ChevronRight class="w-4 h-4" />
        </button>
      </div>
    </div>

    <!-- Пустое состояние -->
    <div v-if="!loading && !error && users.length === 0" class="empty-state">
      <Users class="w-12 h-12" />
      <h3>Пользователи не найдены</h3>
      <p>Попробуйте изменить параметры поиска или фильтры</p>
    </div>

    <!-- Модальное окно просмотра пользователя -->
    <div v-if="selectedUser" class="modal-overlay" @click="closeUserModal">
      <div class="user-modal" @click.stop>
        <div class="modal-header">
          <h3>Информация о пользователе</h3>
          <button @click="closeUserModal" class="modal-close-btn">
            <X class="w-4 h-4" />
          </button>
        </div>
        
        <div class="modal-content">
          <div class="user-details">
            <div class="user-avatar-large">
              <img 
                v-if="selectedUser.avatar_url" 
                :src="getAvatarUrl(selectedUser.avatar_url)" 
                :alt="selectedUser.user_name"
                @error="onAvatarError"
              >
              <div v-else class="avatar-placeholder-large">
                {{ getInitials(selectedUser.user_name || selectedUser.first_name) }}
              </div>
            </div>
            
            <div class="user-info-grid">
              <div class="info-item">
                <label>Имя пользователя:</label>
                <span>{{ selectedUser.user_name || 'Не указано' }}</span>
              </div>
              
              <div class="info-item">
                <label>Имя:</label>
                <span>{{ selectedUser.first_name || 'Не указано' }}</span>
              </div>
              
              <div class="info-item">
                <label>Фамилия:</label>
                <span>{{ selectedUser.last_name || 'Не указано' }}</span>
              </div>
              
              <div class="info-item">
                <label>Email:</label>
                <span>{{ selectedUser.email }}</span>
              </div>
              
              <div class="info-item">
                <label>Телефон:</label>
                <span>{{ selectedUser.phone_number || 'Не указан' }}</span>
              </div>
              
              <div class="info-item">
                <label>Позиция:</label>
                <span>{{ selectedUser.position || 'Не указана' }}</span>
              </div>
              
              <div class="info-item">
                <label>Компания:</label>
                <span>{{ selectedUser.company_name || 'Не указана' }}</span>
              </div>
              
              <div class="info-item">
                <label>ИНН:</label>
                <span>{{ selectedUser.inn || 'Не указан' }}</span>
              </div>
              
              <div class="info-item">
                <label>Страна:</label>
                <span>{{ selectedUser.country || 'Не указана' }}</span>
              </div>
              
              <div class="info-item">
                <label>Город:</label>
                <span>{{ selectedUser.city || 'Не указан' }}</span>
              </div>
              
              <div class="info-item">
                <label>Валюта:</label>
                <span>{{ selectedUser.currency || 'UZS' }}</span>
              </div>
              
              <div class="info-item">
                <label>Язык:</label>
                <span>{{ getLanguageName(selectedUser.language) }}</span>
              </div>
              
              <div class="info-item">
                <label>Статус:</label>
                <span class="status-badge" :class="getStatusClass(selectedUser)">
                  <i :class="getStatusIcon(selectedUser)"></i>
                  {{ getStatusText(selectedUser) }}
                </span>
              </div>
              
              <div class="info-item">
                <label>Роль:</label>
                <span class="role-badge" :class="getRoleClass(selectedUser.role)">
                  <i :class="getRoleIcon(selectedUser.role)"></i>
                  {{ getRoleText(selectedUser.role) }}
                </span>
              </div>
              
              <div class="info-item">
                <label>Дата регистрации:</label>
                <span>{{ formatDate(selectedUser.created_at) }}</span>
              </div>
              
              <div class="info-item">
                <label>Последний вход:</label>
                <span>{{ formatDate(selectedUser.last_logged_in) }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Индикатор статуса WebRTC -->
  <div 
    class="webrtc-status hidden" 
    :class="{ 'ready': webrtcReady, 'not-ready': !webrtcReady }"
    v-if="!webrtcReady"
  >
    {{ webrtcReady ? '✅ WebRTC готов' : '⏳ WebRTC загружается...' }}
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, onUnmounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { Users, AlertCircle, Phone, Video, RefreshCw, LogIn, ChevronLeft, ChevronRight, X } from 'lucide-vue-next'

// Роутер
const router = useRouter()

// Отладочная информация
//console.log('=== DEBUG INFO ===')
const debugToken = localStorage.getItem('auth_token')
const debugUser = localStorage.getItem('user')
//console.log('Auth token:', debugToken)
//console.log('Token length:', debugToken ? debugToken.length : 0)
//console.log('Token starts with:', debugToken ? debugToken.substring(0, 20) + '...' : 'null')
//console.log('User data:', debugUser)
try {
  const parsedUser = debugUser ? JSON.parse(debugUser) : null
  //console.log('Parsed user:', parsedUser)
} catch (e) {
  //console.log('Error parsing user data:', e)
}
//console.log('=================')

// Реактивные данные
const loading = ref(false)
const error = ref('')
const users = ref([])
const stats = ref(null)
const pagination = ref(null)
const selectedUser = ref(null)
const searchQuery = ref('')
const webrtcReady = ref(false)
const onlineUsers = ref([])
const onlineUsersMap = ref(new Map())

// Сортировка по умолчанию
const sortBy = ref('created_at')
const sortOrder = ref('desc')

// Debounced search
let searchTimeout = null
const debouncedSearch = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    loadUsers()
  }, 500)
}

// Импортируем API конфигурацию
import { apiConfig, apiRequest } from '../config/api.js'

// API URL из централизованной конфигурации
const API_BASE_URL = apiConfig.baseURL

// Получение заголовков для авторизации
const getAuthHeaders = () => {
  const token = localStorage.getItem('auth_token')
  if (!token) {
    throw new Error('Токен авторизации не найден')
  }
  return {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
}

// Проверка авторизации
const checkAuth = () => {
  const token = localStorage.getItem('auth_token')
  const user = localStorage.getItem('user')
  
  //console.log('Проверка авторизации:')
  //console.log('- Токен:', token ? 'есть' : 'нет')
  //console.log('- Пользователь:', user ? 'есть' : 'нет')
  
  if (!token || !user) {
    //console.log('Пользователь не авторизован')
    error.value = 'Пользователь не авторизован. Необходимо войти в систему.'
    // Перенаправляем на правильный роут авторизации
    // router.push('/auth')
    return false
  }
  
  return true
}

// Загрузка онлайн пользователей из WebRTC API
const loadOnlineUsers = async () => {
  try {
    //console.log('🌐 Загружаем онлайн пользователей из WebRTC...')
    const response = await fetch('https://webrtc.b2bsklad.uz/api/online/online')
    const data = await response.json()
    
    if (data.success && data.users) {
      onlineUsers.value = data.users
      // Создаем Map для быстрого поиска по ID
      const userMap = new Map()
      data.users.forEach(user => {
        userMap.set(user.id, user)
      })
      onlineUsersMap.value = userMap
      //console.log(`✅ Загружено ${data.users.length} онлайн пользователей из WebRTC`)
    } else {
      //console.log('❌ Ошибка загрузки онлайн пользователей:', data.message)
    }
  } catch (error) {
    console.error('❌ Ошибка загрузки онлайн пользователей:', error)
  }
}

// Проверка онлайн статуса пользователя
const isUserOnline = (user) => {
  const userId = user.user_id || user.id
  return onlineUsersMap.value.has(userId.toString())
}

// Загрузка пользователей
const loadUsers = async (page = 1) => {
  // Проверяем авторизацию перед запросом
  if (!checkAuth()) {
    return
  }
  
  try {
    loading.value = true
    error.value = ''
    
    // Используем готовую функцию apiRequest из конфигурации
    //console.log('🔍 Проверяем авторизацию с /api/me...')
    try {
      const meResponse = await apiRequest('/me')
      //console.log('✅ /api/me ответ:', meResponse.status, meResponse.data)
    } catch (e) {
      //console.log('❌ /api/me ошибка:', e)
    }
    
    //console.log('📡 Отправляем запрос к /users...')
    const response = await apiRequest('/users')
    //console.log('✅ /users ответ:', response.status, response.data)
    
    const data = response.data
    
    if (!response.ok) {
      //console.log('=== ОШИБКА API ===')
      //console.log('Status:', response.status)
      //console.log('Response:', data)
      //console.log('================')
      
      // Если 401, очищаем токены и перенаправляем
      if (response.status === 401) {
        //console.log('❌ Получен 401 - токен недействителен или истек')
        //console.log('Текущий токен:', localStorage.getItem('auth_token'))
        
        // Временно отключаем автоматическое удаление для отладки
        // localStorage.removeItem('auth_token')
        // localStorage.removeItem('user')
        // router.push('/auth')
        // return
        
        throw new Error('Ошибка авторизации: токен недействителен или истек')
      }
      throw new Error(data.message || `HTTP error! status: ${response.status}`)
    }
    
    if (data.success) {
      // Поддерживаем два формата ответа:
      // 1. AdminController: data.data.users, data.data.pagination, data.data.stats
      // 2. AuthController: data.users (без pagination и stats)
      
      if (data.data && data.data.users) {
        // Формат AdminController
        users.value = data.data.users || []
        pagination.value = data.data.pagination || null
        stats.value = data.data.stats || null
      } else if (data.users) {
        // Формат AuthController
        users.value = data.users || []
        pagination.value = null // AuthController не поддерживает пагинацию
        stats.value = null // AuthController не поддерживает статистику
      } else {
        users.value = []
        pagination.value = null
        stats.value = null
      }
      
      //console.log('✅ Пользователи загружены:', users.value.length)
      
      // Загружаем онлайн статус пользователей параллельно
      loadOnlineUsers()
    } else {
      throw new Error(data.message || 'Ошибка загрузки пользователей')
    }
  } catch (err) {
    console.error('Ошибка загрузки пользователей:', err)
    error.value = err.message || 'Ошибка загрузки данных'
    
    // Если ошибка авторизации, перенаправляем на страницу входа
    if (err.message?.includes('401') || err.message?.includes('Unauthorized') || err.message?.includes('Unauthenticated')) {
      //console.log('🔍 Обнаружена ошибка авторизации в catch блоке')
      //console.log('Ошибка:', err.message)
      
      // Временно отключаем автоматическое удаление для отладки
      // localStorage.removeItem('auth_token')
      // localStorage.removeItem('user')
      // router.push('/auth')
    }
  } finally {
    loading.value = false
  }
}

// Изменение страницы
const changePage = (page) => {
  if (page >= 1 && pagination.value && page <= pagination.value.last_page) {
    loadUsers(page)
  }
}

// Очистка поиска
const clearSearch = () => {
  searchQuery.value = ''
  loadUsers()
}

// 🚀 ПРОСТЫЕ API-FIRST ФУНКЦИИ ЗВОНКОВ (БЕЗ ПРОВЕРОК ГОТОВНОСТИ)
const makeAudioCall = async (user) => {
  const userId = user.user_id || user.id
  const userName = user.user_name || user.first_name || 'Пользователь'
  
  //console.log(`newRTC:: Direct API audio call to: ${userName} (${userId})`)
  
  try {
    if (window.callUser) {
      const result = await window.callUser(userId, 'audio')
      if (result && result.success) {
        // window.toastr?.success(`📞 Звоним ${userName}...`)
      } else {
        window.toastr?.error('Ошибка инициации звонка')
      }
    } else {
      // Если window.callUser еще не готов, делаем прямой API запрос
      const response = await fetch('https://webrtc.b2bsklad.uz/api/webrtc/initiate-call', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          targetUserId: userId,
          callType: 'audio',
          callerId: JSON.parse(localStorage.getItem('user')).user_id,
          callerName: JSON.parse(localStorage.getItem('user')).name || JSON.parse(localStorage.getItem('user')).first_name
        })
      })
      const result = await response.json()
      if (result.success) {
        // window.toastr?.success(`📞 Звоним ${userName}...`)
      } else {
        window.toastr?.error('Ошибка API звонка')
      }
    }
  } catch (error) {
    console.error('newRTC:: Call error:', error)
    window.toastr?.error('Ошибка звонка')
  }
}

const makeVideoCall = async (user) => {
  const userId = user.user_id || user.id
  const userName = user.user_name || user.first_name || 'Пользователь'
  
  //console.log(`newRTC:: Direct API video call to: ${userName} (${userId})`)
  
  try {
    if (window.callUser) {
      const result = await window.callUser(userId, 'video')
      if (result && result.success) {
        // window.toastr?.success(`📹 Видео звоним ${userName}...`)
      } else {
        window.toastr?.error('Ошибка инициации видео звонка')
      }
    } else {
      // Прямой API запрос если embed не готов
      const response = await fetch('https://webrtc.b2bsklad.uz/api/webrtc/initiate-call', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          targetUserId: userId,
          callType: 'video',
          callerId: JSON.parse(localStorage.getItem('user')).user_id,
          callerName: JSON.parse(localStorage.getItem('user')).name || JSON.parse(localStorage.getItem('user')).first_name
        })
      })
      const result = await response.json()
      if (result.success) {
        // window.toastr?.success(`📹 Видео звоним ${userName}...`)
      } else {
        window.toastr?.error('Ошибка API видео звонка')
      }
    }
  } catch (error) {
    console.error('newRTC:: Video call error:', error)
    window.toastr?.error('Ошибка видео звонка')
  }
}

// Простая проверка готовности API
const checkWebRTCStatus = () => {
  webrtcReady.value = window.isWebRTCAvailable ? window.isWebRTCAvailable() : false
}

// Минимальная инициализация - только проверка готовности
const initializeWebRTCEvents = () => {
  //console.log('newRTC:: API-First approach - all logic in embed.js')
  
  // Проверяем готовность API каждые 2 секунды
  const checkInterval = setInterval(() => {
    checkWebRTCStatus()
    if (webrtcReady.value) {
      //console.log('newRTC:: WebRTC API ready!')
      clearInterval(checkInterval)
    }
  }, 2000)
}

// Просмотр пользователя
const viewUser = (user) => {
  selectedUser.value = user
}

// Закрытие модального окна
const closeUserModal = () => {
  selectedUser.value = null
}

// Получение URL аватара
const getAvatarUrl = (avatarUrl) => {
  if (!avatarUrl) return ''
  
  // Если URL уже абсолютный, возвращаем как есть
  if (avatarUrl.startsWith('http')) {
    return avatarUrl
  }
  
  // Если URL начинается с /storage, добавляем базовый URL
  if (avatarUrl.startsWith('/storage')) {
    return `https://b2bstorage.ru${avatarUrl}`
  }
  
  // Иначе добавляем полный путь
  return `https://b2bstorage.ru/storage/${avatarUrl}`
}

// Обработка ошибки загрузки аватара
const onAvatarError = (event) => {
  event.target.style.display = 'none'
}

// Получение инициалов
const getInitials = (name) => {
  if (!name) return '?'
  return name.split(' ').map(word => word[0]).join('').toUpperCase().slice(0, 2)
}

// Форматирование даты (оставляем на случай, если понадобится)
const formatDate = (dateString) => {
  if (!dateString) return 'Не указана'
  
  try {
    const date = new Date(dateString)
    return date.toLocaleDateString('ru-RU', {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    })
  } catch (error) {
    return 'Неверная дата'
  }
}

// Инициализация
onMounted(() => {
  // Проверяем авторизацию при загрузке страницы
  if (checkAuth()) {
    loadUsers()
  }
  
  // Проверяем статус WebRTC периодически
  setInterval(checkWebRTCStatus, 2000)
  
  // Обновляем онлайн статус пользователей каждые 30 секунд
  setInterval(loadOnlineUsers, 30000)
  
  // Инициализируем обработчики событий WebRTC
  initializeWebRTCEvents()
})
</script>

<style scoped>
.users-page {
  padding: 20px;
  max-width: 1400px;
  margin: 0 auto;
  background: #f8fafc;
  min-height: 100vh;
}

/* Заголовок страницы */
.page-header {
  margin-bottom: 30px;
}

.page-title {
  display: flex;
  align-items: center;
  gap: 12px;
  font-size: 28px;
  font-weight: 700;
  color: #1e293b;
  margin: 0 0 8px 0;
}

.page-title :deep(svg) {
  color: #3b82f6;
}

.page-description {
  color: #64748b;
  font-size: 16px;
  margin: 0;
}

/* Статистические карточки */
.stats-cards {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 20px;
  margin-bottom: 30px;
}

.stats-card {
  background: white;
  border-radius: 12px;
  padding: 24px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  display: flex;
  align-items: center;
  gap: 16px;
  border: 1px solid #e2e8f0;
  transition: all 0.2s ease;
}

.stats-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.stats-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  background: #f1f5f9;
  display: flex;
  align-items: center;
  justify-content: center;
}

.stats-icon i {
  width: 24px;
  height: 24px;
}

.stats-number {
  font-size: 24px;
  font-weight: 700;
  color: #1e293b;
  line-height: 1;
}

.stats-label {
  font-size: 14px;
  color: #64748b;
  margin-top: 4px;
}

/* Секция поиска */
.search-section {
  background: white;
  border-radius: 12px;
  padding: 24px;
  margin-bottom: 24px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  border: 1px solid #e2e8f0;
}

.search-box {
  position: relative;
}

.search-box i {
  position: absolute;
  left: 16px;
  top: 50%;
  transform: translateY(-50%);
  color: #64748b;
  width: 20px;
  height: 20px;
}

.search-input {
  width: 100%;
  padding: 12px 16px 12px 48px;
  border: 2px solid #e2e8f0;
  border-radius: 8px;
  font-size: 16px;
  background: #f8fafc;
  transition: all 0.2s ease;
}

.search-input:focus {
  outline: none;
  border-color: #3b82f6;
  background: white;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.clear-search-btn {
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  color: #64748b;
  cursor: pointer;
  padding: 4px;
  border-radius: 4px;
  transition: all 0.2s ease;
}

.clear-search-btn:hover {
  color: #ef4444;
  background: #fef2f2;
}

/* Состояния загрузки и ошибок */
.loading-state, .error-state, .empty-state {
  text-align: center;
  padding: 60px 20px;
  background: white;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  border: 1px solid #e2e8f0;
}

.loading-spinner {
  width: 40px;
  height: 40px;
  border: 4px solid #e2e8f0;
  border-top: 4px solid #3b82f6;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 16px;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.error-state :deep(svg), .empty-state :deep(svg) {
  color: #64748b;
  margin-bottom: 16px;
}

.error-actions {
  display: flex;
  gap: 12px;
  justify-content: center;
  margin-top: 16px;
}

.retry-btn, .login-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 12px 24px;
  border: none;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
}

.retry-btn {
  background: #3b82f6;
  color: white;
}

.retry-btn:hover {
  background: #2563eb;
  transform: translateY(-1px);
}

.login-btn {
  background: #10b981;
  color: white;
}

.login-btn:hover {
  background: #059669;
  transform: translateY(-1px);
}

/* Таблица пользователей */
.users-table-container {
  background: white;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  border: 1px solid #e2e8f0;
  overflow: hidden;
}

.table-wrapper {
  overflow-x: auto;
}

.users-table {
  width: 100%;
  border-collapse: collapse;
}

.users-table th {
  background: #f8fafc;
  padding: 16px 12px;
  text-align: left;
  font-weight: 600;
  color: #374151;
  font-size: 14px;
  border-bottom: 1px solid #e2e8f0;
  white-space: nowrap;
}

.users-table td {
  padding: 16px 12px;
  border-bottom: 1px solid #f1f5f9;
  vertical-align: top;
}

.user-row:hover {
  background: #f8fafc;
}

.user-row:last-child td {
  border-bottom: none;
}

/* Колонки таблицы */
.avatar-col, .avatar-cell {
  width: 80px;
}

.name-col {
  min-width: 250px;
}

.actions-col {
  width: 120px;
}

/* Аватар пользователя */
.user-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  overflow: hidden;
  background: #e2e8f0;
  display: flex;
  align-items: center;
  justify-content: center;
}

.user-avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.avatar-placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #3b82f6, #8b5cf6);
  color: white;
  font-weight: 600;
  font-size: 14px;
}

/* Информация о пользователе */
.user-info {
  min-width: 0;
}

.user-name {
  font-weight: 600;
  color: #1e293b;
  margin-bottom: 4px;
}

.user-phone {
  font-size: 13px;
  color: #64748b;
}



/* Кнопки действий */
.action-buttons {
  display: flex;
  gap: 8px;
}

.action-btn {
  width: 36px;
  height: 36px;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
  font-size: 16px;
}



.action-btn:hover:not(:disabled) {
  transform: translateY(-1px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

.action-btn:disabled,
.action-btn.disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.audio-call-btn {
  background: #dcfce7;
  color: #166534;
}

.audio-call-btn:hover {
  background: #bbf7d0;
}

.video-call-btn {
  background: #dbeafe;
  color: #1d4ed8;
}

.video-call-btn:hover {
  background: #bfdbfe;
}

/* Пагинация */
.pagination {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 20px 24px;
  background: #f8fafc;
  border-top: 1px solid #e2e8f0;
}

.pagination-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 16px;
  background: white;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  color: #374151;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
}

.pagination-btn:hover:not(:disabled) {
  background: #f8fafc;
  border-color: #3b82f6;
  color: #3b82f6;
}

.pagination-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.pagination-info {
  font-size: 14px;
  color: #64748b;
}

/* Модальное окно */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 20px;
}

.user-modal {
  background: white;
  border-radius: 12px;
  max-width: 800px;
  width: 100%;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
}

.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 24px 24px 0;
  border-bottom: 1px solid #e2e8f0;
  margin-bottom: 24px;
}

.modal-header h3 {
  font-size: 20px;
  font-weight: 700;
  color: #1e293b;
  margin: 0;
}

.modal-close-btn {
  width: 32px;
  height: 32px;
  border: none;
  background: #f1f5f9;
  border-radius: 6px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #64748b;
  transition: all 0.2s ease;
}

.modal-close-btn:hover {
  background: #e2e8f0;
  color: #374151;
}

.modal-content {
  padding: 0 24px 24px;
}

.user-details {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.user-avatar-large {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  overflow: hidden;
  background: #e2e8f0;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto;
}

.user-avatar-large img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.avatar-placeholder-large {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #3b82f6, #8b5cf6);
  color: white;
  font-weight: 600;
  font-size: 24px;
}

.user-info-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 16px;
}

.info-item {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.info-item label {
  font-size: 12px;
  font-weight: 600;
  color: #64748b;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.info-item span {
  font-size: 14px;
  color: #1e293b;
  font-weight: 500;
}

/* Адаптивность */
@media (max-width: 768px) {
  .users-page {
    padding: 16px;
  }
  
  .stats-cards {
    grid-template-columns: 1fr;
  }
  
  .pagination {
    flex-direction: column;
    gap: 16px;
    text-align: center;
  }
  
  .user-info-grid {
    grid-template-columns: 1fr;
  }
  
  .modal-overlay {
    padding: 10px;
  }
}

/* Улучшенные стили для кнопок звонков */
.action-btn:disabled,
.action-btn.disabled {
  opacity: 0.4;
  cursor: not-allowed;
  transform: none !important;
  box-shadow: none !important;
}

.action-btn.loading {
  pointer-events: none;
  opacity: 0.7;
}

.action-btn.loading::after {
  content: '';
  position: absolute;
  width: 16px;
  height: 16px;
  border: 2px solid transparent;
  border-top: 2px solid currentColor;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Индикатор готовности WebRTC */
.webrtc-status {
  position: fixed;
  bottom: 20px;
  left: 20px;
  padding: 8px 12px;
  background: #10b981;
  color: white;
  border-radius: 6px;
  font-size: 12px;
  font-weight: 600;
  z-index: 1000;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.webrtc-status.ready {
  opacity: 1;
}

.webrtc-status.not-ready {
  background: #ef4444;
  opacity: 1;
}
</style>