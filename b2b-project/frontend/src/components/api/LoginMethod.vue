<template>
  <div class="bg-white rounded-lg shadow-sm">
    <!-- Method Header -->
    <div class="p-4 lg:p-6">
      <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-4 gap-2">
        <div class="flex flex-col lg:flex-row lg:items-center space-y-2 lg:space-y-0 lg:space-x-4">
          <span class="px-2 lg:px-3 py-1 rounded-full text-xs lg:text-sm font-medium bg-blue-100 text-blue-800">
            POST
          </span>
          <code class="text-sm lg:text-lg font-mono text-gray-900 break-all max-w-full">/api/login</code>
        </div>
        <div class="flex items-center space-x-2">
          <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">
            Публичный
          </span>
        </div>
      </div>
      <p class="text-sm lg:text-base text-gray-600">Вход пользователя в систему</p>
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
                  <code class="text-xs lg:text-sm font-mono text-blue-600">email</code>
                  <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">
                    Обязательный
                  </span>
                </div>
                <p class="text-xs lg:text-sm text-gray-600 mt-1">Email пользователя</p>
                <p class="text-xs text-gray-500 mt-1">Тип: string</p>
              </div>
            </div>
            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-2">
              <div class="flex-1">
                <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                  <code class="text-xs lg:text-sm font-mono text-blue-600">password</code>
                  <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">
                    Обязательный
                  </span>
                </div>
                <p class="text-xs lg:text-sm text-gray-600 mt-1">Пароль пользователя</p>
                <p class="text-xs text-gray-500 mt-1">Тип: string</p>
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
              <span class="text-xs lg:text-sm text-red-700 font-medium">422 Unprocessable Entity</span>
              <button
                @click="copyToClipboard(errorResponses[0].response)"
                class="text-red-600 hover:text-red-800 transition-colors"
              >
                <Copy class="h-3 w-3 lg:h-4 lg:w-4" />
              </button>
            </div>
            <p class="text-xs lg:text-sm text-red-700 mb-2">Ошибка валидации данных</p>
            <pre class="text-xs lg:text-sm text-red-800 overflow-x-auto break-words whitespace-pre-wrap"><code>{{ errorResponses[0].response }}</code></pre>
          </div>
          <div class="bg-red-50 rounded-lg p-3 lg:p-4 border border-red-200">
            <div class="flex items-center justify-between mb-2">
              <span class="text-xs lg:text-sm text-red-700 font-medium">401 Unauthorized</span>
              <button
                @click="copyToClipboard(errorResponses[1].response)"
                class="text-red-600 hover:text-red-800 transition-colors"
              >
                <Copy class="h-3 w-3 lg:h-4 lg:w-4" />
              </button>
            </div>
            <p class="text-xs lg:text-sm text-red-700 mb-2">Неверные учетные данные</p>
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
            <p class="text-xs lg:text-sm text-red-700 mb-2">Аккаунт заблокирован</p>
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
  name: 'LoginMethod',
  components: {
    Database,
    Send,
    CheckCircle,
    AlertCircle,
    Copy
  },
  data() {
    return {
      requestExample: `curl -X POST http://localhost:8000/api/login \\
  -H "Content-Type: application/json" \\
  -d '{
    "email": "user@example.com",
    "password": "password123"
  }'`,
      successResponse: `{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {
      "id": 1,
      "first_name": "Иван Иванов",
      "email": "user@example.com",
      "user_name": "Иван Иванов",
      "position": "Пользователь",
      "country": "Россия",
      "city": "Москва",
      "currency": "RUB",
      "language": "ru",
      "timezone": "UTC",
      "last_logged_in": "2024-01-15T10:30:00.000000Z",
      "is_online": true
    },
    "token": "1|abc123def456...",
    "token_type": "Bearer"
  }
}`,
      errorResponses: [
        {
          code: 422,
          status: 'Unprocessable Entity',
          description: 'Ошибка валидации данных',
          response: `{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "email": ["The email field is required."],
    "password": ["The password field is required."]
  }
}`
        },
        {
          code: 401,
          status: 'Unauthorized',
          description: 'Неверные учетные данные',
          response: `{
  "success": false,
  "message": "Invalid credentials"
}`
        },
        {
          code: 403,
          status: 'Forbidden',
          description: 'Аккаунт заблокирован',
          response: `{
  "success": false,
  "message": "Account is banned or inactive"
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