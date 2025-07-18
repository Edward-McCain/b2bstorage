<template>
  <div class="bg-white rounded-lg shadow-sm">
    <!-- Method Header -->
    <div class="p-4 lg:p-6">
      <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-4 gap-2">
        <div class="flex flex-col lg:flex-row lg:items-center space-y-2 lg:space-y-0 lg:space-x-4">
          <span class="px-2 lg:px-3 py-1 rounded-full text-xs lg:text-sm font-medium bg-red-100 text-red-800">
            DELETE
          </span>
          <code class="text-sm lg:text-lg font-mono text-gray-900 break-all max-w-full">/api/products/{id}</code>
        </div>
        <div class="flex items-center space-x-2">
          <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full">
            Требует авторизацию
          </span>
        </div>
      </div>
      <p class="text-sm lg:text-base text-gray-600">Удаление товара</p>
    </div>

    <!-- Method Content -->
    <div class="p-4 lg:p-6">
      <!-- Parameters -->
      <div class="mb-6 lg:mb-8">
        <h3 class="text-base lg:text-lg font-semibold text-gray-900 mb-3 lg:mb-4 flex items-center">
          <Database class="h-4 w-4 lg:h-5 lg:w-5 mr-2" />
          Параметры пути
        </h3>
        <div class="bg-gray-50 rounded-lg p-3 lg:p-4">
          <div class="space-y-3">
            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-2">
              <div class="flex-1">
                <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                  <code class="text-xs lg:text-sm font-mono text-blue-600">id</code>
                  <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">
                    Обязательный
                  </span>
                </div>
                <p class="text-xs lg:text-sm text-gray-600 mt-1">ID товара для удаления</p>
                <p class="text-xs text-gray-500 mt-1">Тип: integer</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Request Example -->
      <div class="mb-6 lg:mb-8">
        <h3 class="text-base lg:text-lg font-semibold text-gray-900 mb-3 lg:mb-4 flex items-center">
          <Send class="h-4 w-4 lg:h-5 lg:w-5 mr-2" />
          Пример запроса
        </h3>
        <div class="bg-gray-900 rounded-lg p-3 lg:p-4">
          <div class="flex items-center justify-between mb-2">
            <span class="text-xs lg:text-sm text-gray-400">cURL</span>
            <button
              @click="copyToClipboard(requestExample)"
              class="text-gray-400 hover:text-white transition-colors"
            >
              <Copy class="h-3 w-3 lg:h-4 lg:w-4" />
            </button>
          </div>
          <pre class="text-xs lg:text-sm text-gray-300 overflow-x-auto break-words whitespace-pre-wrap"><code>{{ requestExample }}</code></pre>
        </div>
      </div>

      <!-- Success Response -->
      <div class="mb-6 lg:mb-8">
        <h3 class="text-base lg:text-lg font-semibold text-gray-900 mb-3 lg:mb-4 flex items-center">
          <CheckCircle class="h-4 w-4 lg:h-5 lg:w-5 mr-2 text-green-600" />
          Успешный ответ
        </h3>
        <div class="bg-green-50 rounded-lg p-3 lg:p-4 border border-green-200">
          <div class="flex items-center justify-between mb-2">
            <span class="text-xs lg:text-sm text-green-700 font-medium">200 OK</span>
            <button
              @click="copyToClipboard(successResponse)"
              class="text-green-600 hover:text-green-800 transition-colors"
            >
              <Copy class="h-3 w-3 lg:h-4 lg:w-4" />
            </button>
          </div>
          <pre class="text-xs lg:text-sm text-green-800 overflow-x-auto break-words whitespace-pre-wrap"><code>{{ successResponse }}</code></pre>
        </div>
      </div>

      <!-- Error Responses -->
      <div>
        <h3 class="text-base lg:text-lg font-semibold text-gray-900 mb-3 lg:mb-4 flex items-center">
          <AlertCircle class="h-4 w-4 lg:h-5 lg:w-5 mr-2 text-red-600" />
          Возможные ошибки
        </h3>
        <div class="space-y-3 lg:space-y-4">
          <div class="bg-red-50 rounded-lg p-3 lg:p-4 border border-red-200">
            <div class="flex items-center justify-between mb-2">
              <span class="text-xs lg:text-sm text-red-700 font-medium">401 Unauthorized</span>
              <button
                @click="copyToClipboard(errorResponses[0].response)"
                class="text-red-600 hover:text-red-800 transition-colors"
              >
                <Copy class="h-3 w-3 lg:h-4 lg:w-4" />
              </button>
            </div>
            <p class="text-xs lg:text-sm text-red-700 mb-2">Пользователь не авторизован</p>
            <pre class="text-xs lg:text-sm text-red-800 overflow-x-auto break-words whitespace-pre-wrap"><code>{{ errorResponses[0].response }}</code></pre>
          </div>
          <div class="bg-red-50 rounded-lg p-3 lg:p-4 border border-red-200">
            <div class="flex items-center justify-between mb-2">
              <span class="text-xs lg:text-sm text-red-700 font-medium">404 Not Found</span>
              <button
                @click="copyToClipboard(errorResponses[1].response)"
                class="text-red-600 hover:text-red-800 transition-colors"
              >
                <Copy class="h-3 w-3 lg:h-4 lg:w-4" />
              </button>
            </div>
            <p class="text-xs lg:text-sm text-red-700 mb-2">Товар не найден</p>
            <pre class="text-xs lg:text-sm text-red-800 overflow-x-auto break-words whitespace-pre-wrap"><code>{{ errorResponses[1].response }}</code></pre>
          </div>
          <div class="bg-red-50 rounded-lg p-3 lg:p-4 border border-red-200">
            <div class="flex items-center justify-between mb-2">
              <span class="text-xs lg:text-sm text-red-700 font-medium">403 Forbidden</span>
              <button
                @click="copyToClipboard(errorResponses[2].response)"
                class="text-red-600 hover:text-red-800 transition-colors"
              >
                <Copy class="h-3 w-3 lg:h-4 lg:w-4" />
              </button>
            </div>
            <p class="text-xs lg:text-sm text-red-700 mb-2">Нет прав для удаления товара</p>
            <pre class="text-xs lg:text-sm text-red-800 overflow-x-auto break-words whitespace-pre-wrap"><code>{{ errorResponses[2].response }}</code></pre>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import {
  Database,
  Send,
  CheckCircle,
  AlertCircle,
  Copy
} from 'lucide-vue-next'

export default {
  name: 'ProductDeleteMethod',
  components: {
    Database,
    Send,
    CheckCircle,
    AlertCircle,
    Copy
  },
  data() {
    return {
      requestExample: `curl -X DELETE http://localhost:8000/api/products/1 \\
  -H "Authorization: Bearer 1|abc123def456..."`,
      successResponse: `{
  "success": true,
  "message": "Product deleted successfully"
}`,
      errorResponses: [
        {
          code: 401,
          status: 'Unauthorized',
          description: 'Пользователь не авторизован',
          response: `{
  "success": false,
  "message": "User not authenticated"
}`
        },
        {
          code: 404,
          status: 'Not Found',
          description: 'Товар не найден',
          response: `{
  "success": false,
  "message": "Product not found"
}`
        },
        {
          code: 403,
          status: 'Forbidden',
          description: 'Нет прав для удаления товара',
          response: `{
  "success": false,
  "message": "You don't have permission to delete this product"
}`
        }
      ]
    }
  },
  methods: {
    async copyToClipboard(text) {
      try {
        await navigator.clipboard.writeText(text)
        // Можно добавить уведомление об успешном копировании
      } catch (err) {
        console.error('Failed to copy text: ', err)
      }
    }
  }
}
</script> 