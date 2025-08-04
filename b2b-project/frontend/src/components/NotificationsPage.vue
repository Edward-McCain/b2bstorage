<template>
  <div class="min-h-screen bg-gray-50" style="padding-top: 66px;">
    <!-- Header -->
    <div class="bg-white shadow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
          <div>
            <!-- Уведомления -->
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">{{ t('NotificationsPage_1') }}</h1>
            <!-- Управление уведомлениями и рекомендациями -->
            <p class="mt-1 text-sm text-gray-500">{{ t('NotificationsPage_2') }}</p>
          </div>
          
          <!-- Actions -->
          <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
            <button
              @click="markAllAsRead"
              :disabled="loading || unreadCount === 0"
              class="inline-flex items-center justify-center px-3 sm:px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <!-- Отметить все как прочитанные / Все прочитано -->
              <span class="hidden sm:inline">{{ t('NotificationsPage_3') }}</span>
              <span class="sm:hidden">{{ t('NotificationsPage_4') }}</span>
            </button>
            
            <button
              @click="getAIRecommendations"
              :disabled="loading || aiLoading"
              class="inline-flex items-center justify-center px-3 sm:px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 cursor-pointer"
            >
              <Loader2 v-if="aiLoading" class="animate-spin h-4 w-4 mr-2" />
              <Sparkles v-else class="h-4 w-4 mr-2" />
              <!-- Ждем ответа AI / Получить AI рекомендации -->
              <span v-if="aiLoading" class="hidden sm:inline">{{ t('NotificationsPage_7') }}</span>
              <span v-else class="hidden sm:inline">{{ t('NotificationsPage_5') }}</span>
              <!-- Ждем ответа AI / AI рекомендации -->
              <span v-if="aiLoading" class="sm:hidden">{{ t('NotificationsPage_7') }}</span>
              <span v-else class="sm:hidden">{{ t('NotificationsPage_6') }}</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Filters -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-6" style="padding-bottom: 0 !important;">
      <div class="bg-white rounded-lg shadow p-4 mb-4 sm:mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center gap-4">
          <div class="flex-1 min-w-0">
            <!-- Тип уведомления -->
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ t('NotificationsPage_8') }}</label>
            <Multiselect
              v-model="filters.type"
              :options="[
                { label: t('NotificationsPage_9'), value: '' },
                { label: t('NotificationsPage_10'), value: 'info' },
                { label: t('NotificationsPage_11'), value: 'warning' },
                { label: t('NotificationsPage_12'), value: 'recommendation' },
                { label: t('NotificationsPage_13'), value: 'low_stock' },
                { label: t('NotificationsPage_14'), value: 'overdue' }
              ]"
              label="label"
              value="value"
              :object="true"
              :placeholder="t('NotificationsPage_9')"
              :max-height="400"
              class="w-full text-sm multiselect-custom"
              @input="onTypeChange"
              @change="onTypeChange"
              @select="onTypeChange"
            />
          </div>
          
          <div class="flex-1 min-w-0">
            <!-- Статус -->
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ t('NotificationsPage_15') }}</label>
            <Multiselect
              v-model="filters.isRead"
              :options="[
                { label: t('NotificationsPage_16'), value: '' },
                { label: t('NotificationsPage_17'), value: 'false' },
                { label: t('NotificationsPage_18'), value: 'true' }
              ]"
              label="label"
              value="value"
              :object="true"
              :placeholder="t('NotificationsPage_16')"
              :max-height="400"
              class="w-full text-sm multiselect-custom"
              @input="onReadStatusChange"
              @change="onReadStatusChange"
              @select="onReadStatusChange"
            />
          </div>
          
          <div class="flex items-center justify-between sm:justify-end hidden">
            <span v-if="totalCount > 0" class="text-sm text-gray-500 ml-4">
              <!-- Всего: / Непрочитанных: -->
              {{ t('NotificationsPage_19') }} {{ totalCount }} | {{ t('NotificationsPage_20') }} {{ unreadCount }}
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" style="padding-bottom: 16px;">
      <!-- Список уведомлений -->
      <div class="space-y-3 sm:space-y-4 mb-8">
        
        <div v-if="loading" class="flex items-center justify-center py-8">
          <Loader2 class="animate-spin h-6 w-6 text-blue-600 mr-2" />
          <!-- Загрузка уведомлений... -->
          <span class="text-sm text-gray-600">{{ t('NotificationsPage_21') }}</span>
        </div>
        
        <div v-else-if="notifications.length === 0" class="text-center py-8">
          <Bell class="h-12 w-12 text-gray-400 mx-auto mb-4" />
          <!-- Уведомлений пока нет -->
          <p class="text-gray-500">{{ t('NotificationsPage_22') }}</p>
        </div>
        
        <NotificationItem 
          v-else
          v-for="notification in notifications"
          :key="notification.id"
          :notification="notification"
          @updated="loadNotifications"
          @deleted="loadNotifications"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { Bell, Sparkles, Loader2, X } from 'lucide-vue-next'
import { apiRequest } from '../config/api'
import { t } from '../locales/index.js'
import Multiselect from '@vueform/multiselect'
import NotificationItem from './NotificationItem.vue'

// Reactive data
const notifications = ref([])
const loading = ref(false)
const aiLoading = ref(false)
const unreadCount = ref(0)
const filters = ref({
  type: '',
  isRead: ''
})

// Methods
const loadNotifications = async () => {
  console.log('=== loadNotifications вызван ===')
  console.log('Текущие фильтры:', filters.value)
  console.log('loading.value:', loading.value)
  
  // Защита от множественных запросов
  if (loading.value) {
    console.log('Запрос уже выполняется, пропускаем')
    return
  }
  
  loading.value = true
  try {
    const params = new URLSearchParams()
    
    // Фильтр по типу
    if (filters.value.type && filters.value.type.value) {
      params.append('type', filters.value.type.value)
      console.log('Добавляем фильтр по типу:', filters.value.type.value)
    } else {
      console.log('Фильтр по типу не добавлен:', filters.value.type)
    }
    
    // Фильтр по статусу прочтения
    if (filters.value.isRead && filters.value.isRead.value !== '') {
      params.append('is_read', filters.value.isRead.value)
      console.log('Добавляем фильтр по статусу:', filters.value.isRead.value)
    } else {
      console.log('Фильтр по статусу не добавлен:', filters.value.isRead)
    }
    
    console.log('Загружаем уведомления с параметрами:', params.toString())
    const response = await apiRequest(`/notifications?${params.toString()}`)
    console.log('Ответ API:', response)
    
    if (response.data && response.data.success) {
      console.log('Данные уведомлений:', response.data.data)
      console.log('Количество уведомлений:', response.data.data.length)
      notifications.value = response.data.data
      unreadCount.value = response.data.unread_count || 0
      console.log('notifications.value после установки:', notifications.value)
    } else {
      console.error('API вернул ошибку:', response.data)
    }
  } catch (error) {
    console.error('Ошибка при загрузке уведомлений:', error)
  } finally {
    loading.value = false
  }
}

// Обработчики изменений фильтров
const onTypeChange = (value) => {
  console.log('=== onTypeChange вызван ===')
  console.log('Полученное значение:', value)
  console.log('Тип значения:', typeof value)
  console.log('Значение до изменения:', filters.value.type)
  
  filters.value.type = value
  console.log('Значение после изменения:', filters.value.type)
  
  console.log('Вызываем loadNotifications...')
  loadNotifications()
}

const onReadStatusChange = (value) => {
  console.log('=== onReadStatusChange вызван ===')
  console.log('Полученное значение:', value)
  console.log('Тип значения:', typeof value)
  console.log('Значение до изменения:', filters.value.isRead)
  
  filters.value.isRead = value
  console.log('Значение после изменения:', filters.value.isRead)
  
  console.log('Вызываем loadNotifications...')
  loadNotifications()
}

const markAllAsRead = async () => {
  try {
    const response = await apiRequest('/notifications/mark-all-read', {
      method: 'PUT'
    })
    
    if (response.data && response.data.success) {
      loadNotifications()
    }
  } catch (error) {
    console.error('Ошибка при отметке всех как прочитанные:', error)
  }
}

const getAIRecommendations = async () => {
  aiLoading.value = true
  try {
    const response = await apiRequest('/ai/generate-recommendations', {
      method: 'POST'
    })
    
    if (response.data && response.data.success) {
      loadNotifications()
    }
  } catch (error) {
    console.error('Ошибка при получении AI рекомендаций:', error)
    
    // Проверяем, является ли это ошибкой "Попробуйте позднее"
    if (error.response && error.response.data && error.response.data.message === 'Попробуйте позднее.') {
      alert('Попробуйте позднее.')
    } else {
      alert('Ошибка при получении AI рекомендаций. Попробуйте еще раз.')
    }
  } finally {
    aiLoading.value = false
  }
}

const clearFilters = () => {
  console.log('=== clearFilters вызван ===')
  console.log('Фильтры до очистки:', filters.value)
  
  filters.value = {
    type: '',
    isRead: ''
  }
  
  console.log('Фильтры после очистки:', filters.value)
  console.log('Вызываем loadNotifications...')
  loadNotifications()
}

// Computed
const totalCount = computed(() => notifications.value.length)

// Lifecycle
onMounted(() => {
  loadNotifications()
})
</script> 