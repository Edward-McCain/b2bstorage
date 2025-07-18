<template>
  <div class="bg-white rounded-lg shadow-sm p-6 lg:p-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
      <div class="flex items-center space-x-3">
        <div class="flex items-center space-x-2">
          <div class="px-3 py-1 bg-green-100 text-green-800 rounded-md text-sm font-medium">GET</div>
          <span class="font-mono text-lg text-gray-900">/admin/inventories</span>
        </div>
      </div>
      <div class="flex items-center space-x-2">
        <div class="w-2 h-2 bg-red-500 rounded-full"></div>
        <span class="text-sm text-gray-600">Админ</span>
      </div>
    </div>

    <!-- Title -->
    <div class="mb-8">
      <h1 class="text-lg lg:text-xl font-semibold text-gray-900">Список инвентаризаций (админ)</h1>
    </div>

    <!-- Description -->
    <div class="mb-8">
      <p class="text-gray-700 leading-relaxed">
        Получает список всех инвентаризаций в системе с расширенной информацией для администраторов. Включает данные о создателях, складах, товарах и статистику расхождений.
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
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">page</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">integer</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">Нет</span>
              </td>
              <td class="px-6 py-4 text-sm text-gray-500">Номер страницы (по умолчанию 1)</td>
            </tr>
            <tr>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">per_page</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">integer</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">Нет</span>
              </td>
              <td class="px-6 py-4 text-sm text-gray-500">Количество записей на странице (по умолчанию 15)</td>
            </tr>
            <tr>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">search</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">string</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">Нет</span>
              </td>
              <td class="px-6 py-4 text-sm text-gray-500">Поиск по номеру, складу или товарам</td>
            </tr>
            <tr>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">user_id</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">integer</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">Нет</span>
              </td>
              <td class="px-6 py-4 text-sm text-gray-500">Фильтр по создателю инвентаризации</td>
            </tr>
            <tr>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">warehouse_id</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">integer</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">Нет</span>
              </td>
              <td class="px-6 py-4 text-sm text-gray-500">Фильтр по складу</td>
            </tr>
            <tr>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">status</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">string</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">Нет</span>
              </td>
              <td class="px-6 py-4 text-sm text-gray-500">Фильтр по статусу: draft, in_progress, completed, cancelled</td>
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
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">sort</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">string</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">Нет</span>
              </td>
              <td class="px-6 py-4 text-sm text-gray-500">Сортировка: created_at, updated_at, completed_at, number</td>
            </tr>
            <tr>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">include_stats</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">boolean</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">Нет</span>
              </td>
              <td class="px-6 py-4 text-sm text-gray-500">Включить статистику по инвентаризациям</td>
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
  name: 'AdminInventoriesMethod',
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
  "data": [
    {
      "id": 1,
      "number": "INV-2024-001",
      "date": "2024-01-20",
      "warehouse_id": 1,
      "warehouse": {
        "id": 1,
        "name": "Основной склад",
        "address": "г. Москва, ул. Примерная, д. 1"
      },
      "user_id": 1,
      "user": {
        "id": 1,
        "name": "Иван Петров",
        "email": "ivan.petrov@example.com"
      },
      "status": "completed",
      "total_items": 1250,
      "checked_items": 1250,
      "discrepancies": 15,
      "notes": "Годовая инвентаризация основного склада",
      "started_at": "2024-01-20T08:00:00.000000Z",
      "completed_at": "2024-01-20T18:00:00.000000Z",
      "created_at": "2024-01-20T08:00:00.000000Z",
      "updated_at": "2024-01-20T18:00:00.000000Z",
      "items": [
        {
          "id": 1,
          "product_id": 1,
          "product": {
            "id": 1,
            "name": "Смартфон iPhone 15 Pro",
            "article": "IPHONE15PRO"
          },
          "expected_quantity": 50,
          "actual_quantity": 48,
          "difference": -2,
          "notes": "Найдено 2 поврежденных товара"
        },
        {
          "id": 2,
          "product_id": 2,
          "product": {
            "id": 2,
            "name": "Ноутбук MacBook Air M2",
            "article": "MBAIRM2"
          },
          "expected_quantity": 25,
          "actual_quantity": 25,
          "difference": 0,
          "notes": "Количество соответствует"
        }
      ],
      "files": [
        {
          "id": 1,
          "name": "inventory_report.pdf",
          "url": "https://example.com/files/inventory_report_1.pdf",
          "size": 2048000
        }
      ],
      "summary": {
        "total_expected_value": 12500000.00,
        "total_actual_value": 12450000.00,
        "total_difference_value": -500000.00,
        "accuracy_percentage": 96.0
      }
    },
    {
      "id": 2,
      "number": "INV-2024-002",
      "date": "2024-01-19",
      "warehouse_id": 2,
      "warehouse": {
        "id": 2,
        "name": "Склад №2",
        "address": "г. Санкт-Петербург, ул. Другая, д. 5"
      },
      "user_id": 2,
      "user": {
        "id": 2,
        "name": "Мария Сидорова",
        "email": "maria.sidorova@example.com"
      },
      "status": "in_progress",
      "total_items": 850,
      "checked_items": 450,
      "discrepancies": 8,
      "notes": "Квартальная инвентаризация склада №2",
      "started_at": "2024-01-19T09:00:00.000000Z",
      "completed_at": null,
      "created_at": "2024-01-19T09:00:00.000000Z",
      "updated_at": "2024-01-19T16:30:00.000000Z",
      "items": [
        {
          "id": 3,
          "product_id": 3,
          "product": {
            "id": 3,
            "name": "Бумага А4",
            "article": "PAPER_A4"
          },
          "expected_quantity": 1000,
          "actual_quantity": 995,
          "difference": -5,
          "notes": "Небольшая недостача"
        }
      ],
      "files": [],
      "summary": {
        "total_expected_value": 8500000.00,
        "total_actual_value": 8475000.00,
        "total_difference_value": -25000.00,
        "accuracy_percentage": 99.7
      }
    }
  ],
  "meta": {
    "total": 2,
    "per_page": 15,
    "current_page": 1,
    "last_page": 1,
    "from": 1,
    "to": 2
  },
  "stats": {
    "total_inventories": 45,
    "completed_inventories": 38,
    "in_progress_inventories": 5,
    "draft_inventories": 2,
    "total_items_checked": 56700,
    "total_discrepancies": 1250,
    "avg_accuracy_percentage": 97.8,
    "by_status": {
      "completed": 38,
      "in_progress": 5,
      "draft": 2,
      "cancelled": 0
    }
  }
}`,
      curlExample: `curl -X GET "https://api.example.com/api/admin/inventories?page=1&per_page=15&status=completed&date_from=2024-01-01&date_to=2024-01-31&include_stats=true" \\
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