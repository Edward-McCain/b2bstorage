<template>
  <div class="bg-white rounded-lg shadow-sm">
    <!-- Method Header -->
    <div class="p-4 lg:p-6">
      <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-4 gap-2">
        <div class="flex flex-col lg:flex-row lg:items-center space-y-2 lg:space-y-0 lg:space-x-4">
          <span class="px-2 lg:px-3 py-1 rounded-full text-xs lg:text-sm font-medium bg-blue-100 text-blue-800">
            POST
          </span>
          <code class="text-sm lg:text-lg font-mono text-gray-900 break-all max-w-full">/api/products/draft</code>
        </div>
        <div class="flex items-center space-x-2">
          <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full">
            Требует авторизацию
          </span>
        </div>
      </div>
      <p class="text-sm lg:text-base text-gray-600">Создание черновика товара</p>
    </div>

    <!-- Method Content -->
    <div class="p-4 lg:p-6">
      <!-- Parameters -->
      <div class="mb-6 lg:mb-8">
        <h3 class="text-base lg:text-lg font-semibold text-gray-900 mb-3 lg:mb-4 flex items-center">
          <Database class="h-4 w-4 lg:h-5 lg:w-5 mr-2" />
          Параметры
        </h3>
        <div class="bg-gray-50 rounded-lg p-3 lg:p-4">
          <div class="space-y-3">
            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-2">
              <div class="flex-1">
                <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                  <code class="text-xs lg:text-sm font-mono text-blue-600">name</code>
                  <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">
                    Обязательный
                  </span>
                </div>
                <p class="text-xs lg:text-sm text-gray-600 mt-1">Название товара</p>
                <p class="text-xs text-gray-500 mt-1">Тип: string</p>
              </div>
            </div>
            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-2">
              <div class="flex-1">
                <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                  <code class="text-xs lg:text-sm font-mono text-blue-600">description</code>
                  <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">
                    Опциональный
                  </span>
                </div>
                <p class="text-xs lg:text-sm text-gray-600 mt-1">Описание товара</p>
                <p class="text-xs text-gray-500 mt-1">Тип: string</p>
              </div>
            </div>
            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-2">
              <div class="flex-1">
                <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                  <code class="text-xs lg:text-sm font-mono text-blue-600">sku</code>
                  <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">
                    Опциональный
                  </span>
                </div>
                <p class="text-xs lg:text-sm text-gray-600 mt-1">Артикул товара</p>
                <p class="text-xs text-gray-500 mt-1">Тип: string</p>
              </div>
            </div>
            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-2">
              <div class="flex-1">
                <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                  <code class="text-xs lg:text-sm font-mono text-blue-600">price</code>
                  <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">
                    Опциональный
                  </span>
                </div>
                <p class="text-xs lg:text-sm text-gray-600 mt-1">Цена товара</p>
                <p class="text-xs text-gray-500 mt-1">Тип: decimal</p>
              </div>
            </div>
            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-2">
              <div class="flex-1">
                <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                  <code class="text-xs lg:text-sm font-mono text-blue-600">category_id</code>
                  <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">
                    Опциональный
                  </span>
                </div>
                <p class="text-xs lg:text-sm text-gray-600 mt-1">ID категории</p>
                <p class="text-xs text-gray-500 mt-1">Тип: integer</p>
              </div>
            </div>
            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-2">
              <div class="flex-1">
                <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                  <code class="text-xs lg:text-sm font-mono text-blue-600">subcategory_id</code>
                  <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">
                    Опциональный
                  </span>
                </div>
                <p class="text-xs lg:text-sm text-gray-600 mt-1">ID подкатегории</p>
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
            <span class="text-xs lg:text-sm text-green-700 font-medium">201 Created</span>
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
              <span class="text-xs lg:text-sm text-red-700 font-medium">422 Unprocessable Entity</span>
              <button
                @click="copyToClipboard(errorResponses[1].response)"
                class="text-red-600 hover:text-red-800 transition-colors"
              >
                <Copy class="h-3 w-3 lg:h-4 lg:w-4" />
              </button>
            </div>
            <p class="text-xs lg:text-sm text-red-700 mb-2">Ошибка валидации данных</p>
            <pre class="text-xs lg:text-sm text-red-800 overflow-x-auto break-words whitespace-pre-wrap"><code>{{ errorResponses[1].response }}</code></pre>
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
  name: 'ProductDraftMethod',
  components: {
    Database,
    Send,
    CheckCircle,
    AlertCircle,
    Copy
  },
  data() {
    return {
      requestExample: `curl -X POST http://localhost:8000/api/products/draft \\
  -H "Authorization: Bearer 1|abc123def456..." \\
  -H "Content-Type: application/json" \\
  -d '{
    "name": "Новый товар",
    "description": "Описание нового товара",
    "sku": "NEW-PRODUCT-001",
    "price": 1000.00,
    "category_id": 1,
    "subcategory_id": 2
  }'`,
      successResponse: `{
  "success": true,
  "message": "Product draft created successfully",
  "data": {
    "product": {
      "id": 1,
      "name": "Новый товар",
      "description": "Описание нового товара",
      "sku": "NEW-PRODUCT-001",
      "price": 1000.00,
      "currency": "RUB",
      "category_id": 1,
      "category_name": "Электроника",
      "subcategory_id": 2,
      "subcategory_name": "Смартфоны",
      "status": "draft",
      "created_at": "2024-01-15T10:30:00.000000Z",
      "updated_at": "2024-01-15T10:30:00.000000Z"
    }
  }
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
          code: 422,
          status: 'Unprocessable Entity',
          description: 'Ошибка валидации данных',
          response: `{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "name": ["The name field is required."],
    "price": ["The price must be a number."],
    "category_id": ["The selected category id is invalid."]
  }
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