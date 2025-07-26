<template>
  <div 
    class="bg-white rounded-lg shadow p-4 sm:p-6 hover:shadow-md transition-shadow"
    :class="{ 
      'border-l-4 border-yellow-400': !notification.is_read,
      'border-l-4 border-indigo-600': notification.type === 'recommendation' && !notification.is_read,
      'border-l-4 border-indigo-400': notification.type === 'recommendation' && notification.is_read
    }"
  >
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3 sm:gap-4">
      <div class="flex-1 min-w-0">
        <div class="flex flex-wrap items-center gap-2 mb-2 sm:mb-3">
          <!-- Бейджи -->
          <span 
            class="inline-flex items-center px-2 py-0.5 sm:px-2.5 sm:py-0.5 rounded-full text-xs font-medium"
            :class="{
              'bg-green-100 text-green-800': notification.type === 'recommendation',
              'bg-blue-100 text-blue-800': notification.type === 'info',
              'bg-yellow-100 text-yellow-800': notification.type === 'warning',
              'bg-red-100 text-red-800': notification.type === 'low_stock',
              'bg-orange-100 text-orange-800': notification.type === 'overdue'
            }"
          >
            {{ getTypeLabel(notification.type) }}
          </span>
          
          <!-- Лейбл "Новое" только для непрочитанных -->
          <span 
            v-if="!notification.is_read"
            class="inline-flex items-center px-2 py-0.5 sm:px-2.5 sm:py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
          >
            Новое
          </span>
        </div>
        
        <!-- Дата -->
        <div class="text-xs sm:text-sm text-gray-500 mb-2 sm:mb-3">
          {{ formatDate(notification.created_at) }}
        </div>
        
        <!-- Сообщение -->
        <div class="text-sm sm:text-base text-gray-700 whitespace-pre-line leading-relaxed">
          {{ notification.message }}
        </div>
      </div>
      
      <!-- Действия -->
      <div class="flex items-center justify-end sm:justify-start gap-2 sm:gap-3 sm:ml-4">
        <button
          @click="markAsRead"
          class="p-2 sm:p-2 text-gray-400 hover:text-green-600 transition-colors rounded-md hover:bg-gray-50"
          :title="notification.is_read ? 'Уже прочитано' : 'Отметить как прочитанное'"
        >
          <Check v-if="!notification.is_read" class="h-5 w-5" />
          <CheckCheck v-else class="h-5 w-5 text-green-600" />
        </button>
        
        <button
          @click="showDeleteModal = true"
          class="p-2 sm:p-2 text-gray-400 hover:text-red-600 transition-colors rounded-md hover:bg-gray-50"
          title="Удалить уведомление"
        >
          <Trash2 class="h-5 w-5" />
        </button>
      </div>
    </div>
  </div>

  <!-- Модалка подтверждения удаления -->
  <div 
    v-if="showDeleteModal"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
    @click="showDeleteModal = false"
  >
    <div 
      class="bg-white/90 backdrop-blur-sm rounded-lg p-4 sm:p-6 w-full max-w-md mx-auto"
      @click.stop
    >
      <h3 class="text-lg font-medium text-gray-900 mb-3 sm:mb-4">
        Удалить уведомление?
      </h3>
      <p class="text-sm sm:text-base text-gray-600 mb-4 sm:mb-6">
        Это действие нельзя отменить. Уведомление будет удалено навсегда.
      </p>
      <div class="flex flex-col sm:flex-row justify-end gap-2 sm:gap-3">
        <button
          @click="showDeleteModal = false"
          class="w-full sm:w-auto px-4 py-2 text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 transition-colors text-sm sm:text-base"
        >
          Отмена
        </button>
        <button
          @click="deleteNotification"
          class="w-full sm:w-auto px-4 py-2 text-white bg-red-600 rounded-md hover:bg-red-700 transition-colors text-sm sm:text-base"
        >
          Удалить
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Check, CheckCheck, Trash2 } from 'lucide-vue-next'
import { apiRequest } from '../config/api'

const props = defineProps({
  notification: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['updated', 'deleted'])

const showDeleteModal = ref(false)

const getTypeLabel = (type) => {
  const labels = {
    'info': 'Информация',
    'warning': 'Предупреждение',
    'recommendation': 'Рекомендация',
    'low_stock': 'Низкие остатки',
    'overdue': 'Просроченные документы'
  }
  return labels[type] || type
}

const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('ru-RU', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const markAsRead = async () => {
  if (props.notification.is_read) return
  
  try {
    const response = await apiRequest(`/notifications/${props.notification.id}/mark-read`, {
      method: 'PUT'
    })
    
    if (response.data && response.data.success) {
      emit('updated')
      // Отправляем событие об обновлении уведомлений
      window.dispatchEvent(new CustomEvent('notifications-updated'))
    }
  } catch (error) {
    console.error('Ошибка при отметке как прочитанное:', error)
  }
}

const deleteNotification = async () => {
  try {
    const response = await apiRequest(`/notifications/${props.notification.id}`, {
      method: 'DELETE'
    })
    
    if (response.data && response.data.success) {
      emit('deleted')
      showDeleteModal.value = false
      // Отправляем событие об обновлении уведомлений
      window.dispatchEvent(new CustomEvent('notifications-updated'))
    }
  } catch (error) {
    console.error('Ошибка при удалении уведомления:', error)
  }
}
</script> 