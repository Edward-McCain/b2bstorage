<template>
  <div class="bg-white rounded-lg shadow p-6">
    <div class="flex items-center justify-between mb-4">
      <div>
        <h3 class="text-lg font-medium text-gray-900">Получить уведомления</h3>
        <p class="text-sm text-gray-600">Получение списка уведомлений пользователя</p>
      </div>
      <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
        GET
      </span>
    </div>

    <div class="space-y-4">
      <!-- Параметры запроса -->
      <div>
        <h4 class="text-sm font-medium text-gray-900 mb-2">Параметры запроса</h4>
        <div class="bg-gray-50 rounded-md p-3 space-y-2">
          <div class="flex items-center justify-between">
            <span class="text-sm font-medium text-gray-700">type</span>
            <span class="text-sm text-gray-500">string (опционально)</span>
          </div>
          <div class="text-xs text-gray-600">
            Фильтр по типу уведомления: info, warning, recommendation, low_stock, overdue
          </div>
          
          <div class="flex items-center justify-between">
            <span class="text-sm font-medium text-gray-700">is_read</span>
            <span class="text-sm text-gray-500">boolean (опционально)</span>
          </div>
          <div class="text-xs text-gray-600">
            Фильтр по статусу прочтения: true/false
          </div>
          
          <div class="flex items-center justify-between">
            <span class="text-sm font-medium text-gray-700">limit</span>
            <span class="text-sm text-gray-500">integer (опционально, по умолчанию: 50)</span>
          </div>
          <div class="text-xs text-gray-600">
            Количество уведомлений для получения
          </div>
        </div>
      </div>

      <!-- Пример запроса -->
      <div>
        <h4 class="text-sm font-medium text-gray-900 mb-2">Пример запроса</h4>
        <div class="bg-gray-900 rounded-md p-3">
          <code class="text-sm text-green-400">
            GET /api/notifications?type=warning&is_read=false&limit=20
          </code>
        </div>
      </div>

      <!-- Ответ -->
      <div>
        <h4 class="text-sm font-medium text-gray-900 mb-2">Ответ</h4>
        <div class="bg-gray-900 rounded-md p-3">
          <pre class="text-sm text-gray-300 overflow-x-auto"><code>{
  "success": true,
  "data": [
    {
      "id": 1,
      "user_id": 1,
      "type": "warning",
      "message": "У вас 3 необработанных оприходований старше 7 дней.",
      "is_read": false,
      "created_at": "2024-01-15T10:30:00.000000Z",
      "updated_at": "2024-01-15T10:30:00.000000Z"
    }
  ],
  "unread_count": 5
}</code></pre>
        </div>
      </div>

      <!-- Тестирование -->
      <div>
        <h4 class="text-sm font-medium text-gray-900 mb-2">Тестирование</h4>
        <div class="space-y-3">
          <div class="flex items-center space-x-2">
            <select v-model="testParams.type" class="text-sm border border-gray-300 rounded px-2 py-1">
              <option value="">Все типы</option>
              <option value="info">Информация</option>
              <option value="warning">Предупреждение</option>
              <option value="recommendation">Рекомендация</option>
              <option value="low_stock">Низкие остатки</option>
              <option value="overdue">Просроченные документы</option>
            </select>
            <select v-model="testParams.isRead" class="text-sm border border-gray-300 rounded px-2 py-1">
              <option value="">Все</option>
              <option value="false">Непрочитанные</option>
              <option value="true">Прочитанные</option>
            </select>
            <input 
              v-model="testParams.limit" 
              type="number" 
              placeholder="Лимит" 
              class="text-sm border border-gray-300 rounded px-2 py-1 w-20"
            >
            <button 
              @click="testRequest" 
              :disabled="loading"
              class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 disabled:opacity-50"
            >
              {{ loading ? 'Загрузка...' : 'Тест' }}
            </button>
          </div>
          
          <div v-if="response" class="bg-gray-50 rounded-md p-3">
            <h5 class="text-sm font-medium text-gray-900 mb-2">Результат:</h5>
            <pre class="text-xs text-gray-700 overflow-x-auto">{{ JSON.stringify(response, null, 2) }}</pre>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { apiRequest } from '../../config/api'

const loading = ref(false)
const response = ref(null)
const testParams = ref({
  type: '',
  isRead: '',
  limit: 10
})

const testRequest = async () => {
  loading.value = true
  response.value = null
  
  try {
    const params = new URLSearchParams()
    if (testParams.value.type) params.append('type', testParams.value.type)
    if (testParams.value.isRead !== '') params.append('is_read', testParams.value.isRead)
    if (testParams.value.limit) params.append('limit', testParams.value.limit)
    
    const result = await apiRequest(`/notifications?${params.toString()}`)
    response.value = result
  } catch (error) {
    response.value = { error: error.message }
  } finally {
    loading.value = false
  }
}
</script> 