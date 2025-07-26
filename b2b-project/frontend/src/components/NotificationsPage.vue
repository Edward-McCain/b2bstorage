<template>
  <div class="min-h-screen bg-gray-50" style="padding-top: 66px;">
    <!-- Header -->
    <div class="bg-white shadow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
          <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Уведомления</h1>
            <p class="mt-1 text-sm text-gray-500">Управление уведомлениями и рекомендациями</p>
          </div>
          
          <!-- Actions -->
          <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
            <button
              @click="markAllAsRead"
              :disabled="loading || unreadCount === 0"
              class="inline-flex items-center justify-center px-3 sm:px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span class="hidden sm:inline">Отметить все как прочитанные</span>
              <span class="sm:hidden">Все прочитано</span>
            </button>
            
            <button
              @click="getAIRecommendations"
              :disabled="loading || aiLoading"
              class="inline-flex items-center justify-center px-3 sm:px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 cursor-pointer"
            >
              <Loader2 v-if="aiLoading" class="animate-spin h-4 w-4 mr-2" />
              <Sparkles v-else class="h-4 w-4 mr-2" />
              <span v-if="aiLoading" class="hidden sm:inline">Ждем ответа AI</span>
              <span v-else class="hidden sm:inline">Получить AI рекомендации</span>
              <span v-if="aiLoading" class="sm:hidden">Ждем ответа AI</span>
              <span v-else class="sm:hidden">AI рекомендации</span>
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
            <label class="block text-sm font-medium text-gray-700 mb-1">Тип уведомления</label>
            <Multiselect
              v-model="filters.type"
              :options="[
                { label: 'Все типы', value: '' },
                { label: 'Информация', value: 'info' },
                { label: 'Предупреждение', value: 'warning' },
                { label: 'Рекомендация', value: 'recommendation' },
                { label: 'Низкие остатки', value: 'low_stock' },
                { label: 'Просроченные документы', value: 'overdue' }
              ]"
              label="label"
              value="value"
              :object="true"
              placeholder="Все типы"
              :max-height="400"
              class="w-full text-sm multiselect-custom"
              @change="updateFilters"
            />
          </div>
          
          <div class="flex-1 min-w-0">
            <label class="block text-sm font-medium text-gray-700 mb-1">Статус</label>
            <Multiselect
              v-model="filters.isRead"
              :options="[
                { label: 'Все', value: '' },
                { label: 'Непрочитанные', value: 'false' },
                { label: 'Прочитанные', value: 'true' }
              ]"
              label="label"
              value="value"
              :object="true"
              placeholder="Все"
              :max-height="400"
              class="w-full text-sm multiselect-custom"
              @change="updateFilters"
            />
          </div>
          
          <div class="flex items-center justify-between sm:justify-end">
            <span v-if="totalCount > 0" class="text-sm text-gray-500">
              Всего: {{ totalCount }} | Непрочитанных: {{ unreadCount }}
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Список уведомлений -->
      <div class="space-y-3 sm:space-y-4">
        
        <div v-if="loading" class="flex items-center justify-center py-8">
          <Loader2 class="animate-spin h-6 w-6 text-blue-600 mr-2" />
          <span class="text-sm text-gray-600">Загрузка уведомлений...</span>
        </div>
        
        <div v-else-if="notifications.length === 0" class="text-center py-8">
          <Bell class="h-12 w-12 text-gray-400 mx-auto mb-4" />
          <p class="text-gray-500">Уведомлений пока нет</p>
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
  loading.value = true
  try {
    const params = new URLSearchParams()
    if (filters.value.type) params.append('type', filters.value.type)
    if (filters.value.isRead !== '') params.append('is_read', filters.value.isRead)
    
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
  } finally {
    aiLoading.value = false
  }
}

const updateFilters = () => {
  loadNotifications()
}

const clearFilters = () => {
  filters.value = {
    type: '',
    isRead: ''
  }
  loadNotifications()
}

// Computed
const totalCount = computed(() => notifications.value.length)

// Lifecycle
onMounted(() => {
  loadNotifications()
})
</script> 