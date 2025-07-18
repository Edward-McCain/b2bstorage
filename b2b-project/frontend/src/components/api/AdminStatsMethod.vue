<template>
  <div class="bg-white rounded-lg shadow-sm p-6 lg:p-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
      <div class="flex items-center space-x-3">
        <div class="flex items-center space-x-2">
          <div class="px-3 py-1 bg-green-100 text-green-800 rounded-md text-sm font-medium">GET</div>
          <span class="font-mono text-lg text-gray-900">/admin/stats</span>
        </div>
      </div>
      <div class="flex items-center space-x-2">
        <div class="w-2 h-2 bg-red-500 rounded-full"></div>
        <span class="text-sm text-gray-600">Админ</span>
      </div>
    </div>

    <!-- Title -->
    <div class="mb-8">
      <h1 class="text-lg lg:text-xl font-semibold text-gray-900">Статистика системы</h1>
    </div>

    <!-- Description -->
    <div class="mb-8">
      <p class="text-gray-700 leading-relaxed">
        Получает общую статистику системы для администраторов. Включает данные о пользователях, товарах, операциях и финансовые показатели.
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
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">period</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">string</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">Нет</span>
              </td>
              <td class="px-6 py-4 text-sm text-gray-500">Период: today, week, month, quarter, year (по умолчанию month)</td>
            </tr>
            <tr>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">date_from</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">date</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">Нет</span>
              </td>
              <td class="px-6 py-4 text-sm text-gray-500">Дата начала периода (YYYY-MM-DD)</td>
            </tr>
            <tr>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">date_to</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">date</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">Нет</span>
              </td>
              <td class="px-6 py-4 text-sm text-gray-500">Дата окончания периода (YYYY-MM-DD)</td>
            </tr>
            <tr>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">include_charts</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">boolean</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">Нет</span>
              </td>
              <td class="px-6 py-4 text-sm text-gray-500">Включить данные для графиков</td>
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
  name: 'AdminStatsMethod',
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
    "period": {
      "start": "2024-01-01",
      "end": "2024-01-31",
      "name": "Январь 2024"
    },
    "users": {
      "total": 1250,
      "active": 1180,
      "new_this_period": 45,
      "growth_rate": 3.7,
      "by_status": {
        "active": 1180,
        "inactive": 45,
        "suspended": 25
      },
      "by_role": {
        "admin": 5,
        "manager": 25,
        "user": 1220
      }
    },
    "products": {
      "total": 5670,
      "active": 5450,
      "new_this_period": 180,
      "avg_price": 125431.20,
      "total_value": 711695904.00,
      "by_category": {
        "electronics": 2500,
        "clothing": 1500,
        "books": 800,
        "other": 870
      }
    },
    "operations": {
      "receipts": {
        "total": 1250,
        "this_period": 180,
        "total_amount": 156789000.00,
        "avg_amount": 125431.20
      },
      "write_offs": {
        "total": 450,
        "this_period": 65,
        "total_amount": 56789000.00,
        "avg_amount": 126197.78
      },
      "transfers": {
        "total": 180,
        "this_period": 25,
        "total_items": 4500,
        "avg_items": 25.0
      },
      "inventories": {
        "total": 45,
        "this_period": 8,
        "completed": 38,
        "in_progress": 5
      }
    },
    "warehouses": {
      "total": 5,
      "total_capacity": 50000,
      "used_capacity": 35000,
      "utilization_rate": 70.0,
      "by_warehouse": {
        "Основной склад": {
          "capacity": 20000,
          "used": 15000,
          "utilization": 75.0
        },
        "Склад №2": {
          "capacity": 15000,
          "used": 10000,
          "utilization": 66.7
        }
      }
    },
    "financial": {
      "total_revenue": 156789000.00,
      "total_expenses": 125431200.00,
      "profit": 31357800.00,
      "profit_margin": 20.0,
      "avg_order_value": 125431.20,
      "top_products": [
        {
          "id": 1,
          "name": "Смартфон iPhone 15 Pro",
          "revenue": 4049550.00,
          "quantity": 45
        },
        {
          "id": 2,
          "name": "Ноутбук MacBook Air M2",
          "revenue": 3639720.00,
          "quantity": 28
        }
      ]
    },
    "performance": {
      "avg_response_time": 150,
      "uptime": 99.9,
      "active_sessions": 85,
      "peak_concurrent_users": 120,
      "api_calls": {
        "total": 125000,
        "this_period": 18000,
        "success_rate": 99.5
      }
    },
    "charts": {
      "user_growth": [
        { "date": "2024-01-01", "users": 1205 },
        { "date": "2024-01-15", "users": 1225 },
        { "date": "2024-01-31", "users": 1250 }
      ],
      "revenue_trend": [
        { "date": "2024-01-01", "revenue": 5000000 },
        { "date": "2024-01-15", "revenue": 7500000 },
        { "date": "2024-01-31", "revenue": 15678900 }
      ],
      "operations_by_type": [
        { "type": "receipts", "count": 180 },
        { "type": "write_offs", "count": 65 },
        { "type": "transfers", "count": 25 },
        { "type": "inventories", "count": 8 }
      ]
    }
  }
}`,
      curlExample: `curl -X GET "https://api.example.com/api/admin/stats?period=month&include_charts=true" \\
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