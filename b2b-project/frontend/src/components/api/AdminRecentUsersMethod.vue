<template>
  <div class="bg-white rounded-lg shadow-sm p-6 lg:p-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
      <div class="flex items-center space-x-3">
        <div class="flex items-center space-x-2">
          <div class="px-3 py-1 bg-green-100 text-green-800 rounded-md text-sm font-medium">GET</div>
          <span class="font-mono text-lg text-gray-900">/admin/recent-users</span>
        </div>
      </div>
      <div class="flex items-center space-x-2">
        <div class="w-2 h-2 bg-red-500 rounded-full"></div>
        <span class="text-sm text-gray-600">Админ</span>
      </div>
    </div>

    <!-- Title -->
    <div class="mb-8">
      <h1 class="text-lg lg:text-xl font-semibold text-gray-900">Недавние пользователи</h1>
    </div>

    <!-- Description -->
    <div class="mb-8">
      <p class="text-gray-700 leading-relaxed">
        Получает список недавно зарегистрированных пользователей системы. Этот метод доступен только администраторам и используется для мониторинга активности регистраций.
      </p>
    </div>

    <!-- Query Parameters -->
    <div class="mb-8">
      <h3 class="text-lg font-semibold text-gray-900 mb-4">Параметры запроса</h3>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Параметр</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Тип</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Обязательный</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Описание</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">limit</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">integer</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">Нет</span>
              </td>
              <td class="px-6 py-4 text-sm text-gray-500">Количество записей (по умолчанию 10, максимум 50)</td>
            </tr>
            <tr>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">days</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">integer</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">Нет</span>
              </td>
              <td class="px-6 py-4 text-sm text-gray-500">Количество дней для поиска (по умолчанию 7)</td>
            </tr>
            <tr>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">include_stats</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">boolean</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">Нет</span>
              </td>
              <td class="px-6 py-4 text-sm text-gray-500">Включить статистику регистраций</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Response -->
    <div class="mb-8">
      <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
        <CheckCircle class="h-5 w-5 mr-2" />
        Ответ
      </h3>
      <div class="bg-gray-50 rounded-lg p-4">
        <div class="flex items-center justify-between mb-2">
          <span class="text-sm font-medium text-gray-700">JSON (200 OK)</span>
          <button
            @click="copyToClipboard(responseBody)"
            class="flex items-center space-x-1 text-blue-600 hover:text-blue-700 text-sm"
          >
            <Copy class="h-4 w-4" />
            <span>Копировать</span>
          </button>
        </div>
        <pre class="text-sm text-gray-800 overflow-x-auto"><code>{{ responseBody }}</code></pre>
      </div>
    </div>

    <!-- Errors -->
    <div class="mb-8">
      <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
        <AlertCircle class="h-5 w-5 mr-2" />
        Возможные ошибки
      </h3>
      <div class="space-y-4">
        <div class="border border-red-200 rounded-lg p-4 bg-red-50">
          <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium text-red-800">401 Unauthorized</span>
            <span class="text-xs text-red-600">Авторизация</span>
          </div>
          <p class="text-sm text-red-700">
            Не авторизован. Требуется токен доступа.
          </p>
        </div>
        <div class="border border-red-200 rounded-lg p-4 bg-red-50">
          <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium text-red-800">403 Forbidden</span>
            <span class="text-xs text-red-600">Доступ</span>
          </div>
          <p class="text-sm text-red-700">
            Доступ запрещен. Требуются права администратора.
          </p>
        </div>
        <div class="border border-red-200 rounded-lg p-4 bg-red-50">
          <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium text-red-800">400 Bad Request</span>
            <span class="text-xs text-red-600">Валидация</span>
          </div>
          <p class="text-sm text-red-700">
            Неверные параметры запроса. Проверьте типы данных и допустимые значения.
          </p>
        </div>
        <div class="border border-red-200 rounded-lg p-4 bg-red-50">
          <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium text-red-800">500 Internal Server Error</span>
            <span class="text-xs text-red-600">Сервер</span>
          </div>
          <p class="text-sm text-red-700">
            Внутренняя ошибка сервера. Попробуйте позже.
          </p>
        </div>
      </div>
    </div>

    <!-- Example -->
    <div class="mb-8">
      <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
        <Send class="h-5 w-5 mr-2" />
        Пример использования
      </h3>
      <div class="bg-gray-50 rounded-lg p-4">
        <div class="flex items-center justify-between mb-2">
          <span class="text-sm font-medium text-gray-700">cURL</span>
          <button
            @click="copyToClipboard(curlExample)"
            class="flex items-center space-x-1 text-blue-600 hover:text-blue-700 text-sm"
          >
            <Copy class="h-4 w-4" />
            <span>Копировать</span>
          </button>
        </div>
        <pre class="text-sm text-gray-800 overflow-x-auto"><code>{{ curlExample }}</code></pre>
      </div>
    </div>
  </div>
</template>

<script>
import { CheckCircle, AlertCircle, Copy, Send } from 'lucide-vue-next'

export default {
  name: 'AdminRecentUsersMethod',
  components: {
    CheckCircle,
    AlertCircle,
    Copy,
    Send
  },
  data() {
    return {
      responseBody: `{
  "success": true,
  "data": {
    "users": [
      {
        "id": 15,
        "name": "Анна Волкова",
        "email": "anna.volkova@example.com",
        "phone": "+7 (999) 456-78-90",
        "company": "ООО Инновации",
        "role": "user",
        "status": "active",
        "email_verified_at": "2024-01-20T09:15:00.000000Z",
        "created_at": "2024-01-20T09:15:00.000000Z",
        "last_login_at": "2024-01-20T09:15:00.000000Z",
        "profile": {
          "avatar": null,
          "position": "Аналитик",
          "department": "Аналитика"
        }
      },
      {
        "id": 14,
        "name": "Дмитрий Соколов",
        "email": "dmitry.sokolov@example.com",
        "phone": "+7 (999) 345-67-89",
        "company": "ИП Соколов",
        "role": "user",
        "status": "active",
        "email_verified_at": "2024-01-19T16:30:00.000000Z",
        "created_at": "2024-01-19T16:30:00.000000Z",
        "last_login_at": "2024-01-19T16:30:00.000000Z",
        "profile": {
          "avatar": "https://example.com/avatars/14.jpg",
          "position": "Директор",
          "department": "Руководство"
        }
      },
      {
        "id": 13,
        "name": "Елена Морозова",
        "email": "elena.morozova@example.com",
        "phone": "+7 (999) 234-56-78",
        "company": "ООО Мороз",
        "role": "user",
        "status": "active",
        "email_verified_at": "2024-01-18T11:45:00.000000Z",
        "created_at": "2024-01-18T11:45:00.000000Z",
        "last_login_at": "2024-01-18T11:45:00.000000Z",
        "profile": {
          "avatar": null,
          "position": "Менеджер",
          "department": "Продажи"
        }
      }
    ],
    "stats": {
      "total_recent": 3,
      "total_today": 1,
      "total_week": 3,
      "total_month": 8,
      "growth_rate": 12.5,
      "verification_rate": 100.0,
      "active_rate": 100.0
    },
    "period": {
      "start_date": "2024-01-14T00:00:00.000000Z",
      "end_date": "2024-01-20T23:59:59.000000Z",
      "days": 7
    }
  }
}`,
      curlExample: `curl -X GET "https://api.example.com/api/admin/recent-users?limit=10&days=7&include_stats=true" \\
  -H "Accept: application/json" \\
  -H "Authorization: Bearer YOUR_ADMIN_TOKEN"`
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