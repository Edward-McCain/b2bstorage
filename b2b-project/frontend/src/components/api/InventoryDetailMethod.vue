<template>
  <div class="bg-white rounded-lg shadow-sm p-6 lg:p-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
      <div class="flex items-center space-x-3">
        <div class="bg-green-100 p-2 rounded-lg">
          <ClipboardList class="h-5 w-5 text-green-600" />
        </div>
        <div>
          <div class="flex items-center space-x-2">
            <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-medium">GET</span>
            <span class="font-mono text-sm lg:text-base text-gray-900">/inventories/{id}</span>
          </div>
          <h1 class="text-lg lg:text-xl font-bold text-gray-900 mt-1">Получение инвентаризации по ID</h1>
        </div>
      </div>
      <button
        @click="copyEndpoint"
        class="flex items-center space-x-2 text-gray-500 hover:text-gray-700 transition-colors"
      >
        <Copy class="h-4 w-4" />
        <span class="text-sm">Копировать</span>
      </button>
    </div>

    <!-- Description -->
    <div class="mb-6">
      <h2 class="text-base font-semibold text-gray-900 mb-3">Описание</h2>
      <p class="text-sm lg:text-base text-gray-600 leading-relaxed">
        Возвращает детальную информацию об инвентаризации по ее уникальному идентификатору. 
        Включает все позиции товаров, файлы и статус обработки.
      </p>
    </div>

    <!-- Parameters -->
    <div class="mb-6">
      <h2 class="text-base font-semibold text-gray-900 mb-3">Параметры пути</h2>
      <div class="bg-gray-50 rounded-lg p-4">
        <div class="space-y-3">
          <div class="flex items-start space-x-3">
            <span class="text-sm font-medium text-gray-700 min-w-20">id</span>
            <div class="flex-1">
              <span class="text-sm text-gray-600">Уникальный идентификатор инвентаризации (обязательно)</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Example Request -->
    <div class="mb-6">
      <h2 class="text-base font-semibold text-gray-900 mb-3">Пример запроса</h2>
      <div class="bg-gray-900 rounded-lg p-4">
        <div class="flex items-center justify-between mb-2">
          <span class="text-sm text-gray-400">cURL</span>
          <button
            @click="copyCode('curl')"
            class="text-gray-400 hover:text-white transition-colors"
          >
            <Copy class="h-4 w-4" />
          </button>
        </div>
        <pre class="text-sm text-gray-300 overflow-x-auto"><code>{{ curlExample }}</code></pre>
      </div>
    </div>

    <!-- Example Response -->
    <div class="mb-6">
      <h2 class="text-base font-semibold text-gray-900 mb-3">Пример ответа</h2>
      <div class="bg-gray-900 rounded-lg p-4">
        <div class="flex items-center justify-between mb-2">
          <span class="text-sm text-gray-400">JSON</span>
          <button
            @click="copyCode('response')"
            class="text-gray-400 hover:text-white transition-colors"
          >
            <Copy class="h-4 w-4" />
          </button>
        </div>
        <pre class="text-sm text-gray-300 overflow-x-auto"><code>{{ responseExample }}</code></pre>
      </div>
    </div>

    <!-- Response Fields -->
    <div class="mb-6">
      <h2 class="text-base font-semibold text-gray-900 mb-3">Поля ответа</h2>
      <div class="bg-gray-50 rounded-lg p-4">
        <div class="space-y-3">
          <div class="flex items-start space-x-3">
            <span class="text-sm font-medium text-gray-700 min-w-24">id</span>
            <div class="flex-1">
              <span class="text-sm text-gray-600">Уникальный идентификатор инвентаризации</span>
            </div>
          </div>
          <div class="flex items-start space-x-3">
            <span class="text-sm font-medium text-gray-700 min-w-24">number</span>
            <div class="flex-1">
              <span class="text-sm text-gray-600">Номер инвентаризации</span>
            </div>
          </div>
          <div class="flex items-start space-x-3">
            <span class="text-sm font-medium text-gray-700 min-w-24">date</span>
            <div class="flex-1">
              <span class="text-sm text-gray-600">Дата инвентаризации</span>
            </div>
          </div>
          <div class="flex items-start space-x-3">
            <span class="text-sm font-medium text-gray-700 min-w-24">status</span>
            <div class="flex-1">
              <span class="text-sm text-gray-600">Статус: draft, in_progress, completed, cancelled</span>
            </div>
          </div>
          <div class="flex items-start space-x-3">
            <span class="text-sm font-medium text-gray-700 min-w-24">warehouse</span>
            <div class="flex-1">
              <span class="text-sm text-gray-600">Информация о складе</span>
            </div>
          </div>
          <div class="flex items-start space-x-3">
            <span class="text-sm font-medium text-gray-700 min-w-24">description</span>
            <div class="flex-1">
              <span class="text-sm text-gray-600">Описание инвентаризации</span>
            </div>
          </div>
          <div class="flex items-start space-x-3">
            <span class="text-sm font-medium text-gray-700 min-w-24">items</span>
            <div class="flex-1">
              <span class="text-sm text-gray-600">Массив позиций товаров</span>
            </div>
          </div>
          <div class="flex items-start space-x-3">
            <span class="text-sm font-medium text-gray-700 min-w-24">files</span>
            <div class="flex-1">
              <span class="text-sm text-gray-600">Прикрепленные файлы</span>
            </div>
          </div>
          <div class="flex items-start space-x-3">
            <span class="text-sm font-medium text-gray-700 min-w-24">statistics</span>
            <div class="flex-1">
              <span class="text-sm text-gray-600">Статистика инвентаризации</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Error Codes -->
    <div class="mb-6">
      <h2 class="text-base font-semibold text-gray-900 mb-3">Коды ошибок</h2>
      <div class="space-y-3">
        <div class="flex items-center space-x-3 p-3 bg-red-50 rounded-lg">
          <AlertCircle class="h-5 w-5 text-red-500 flex-shrink-0" />
          <div>
            <div class="text-sm font-medium text-red-800">401 Unauthorized</div>
            <div class="text-sm text-red-600">Не авторизован</div>
          </div>
        </div>
        <div class="flex items-center space-x-3 p-3 bg-red-50 rounded-lg">
          <AlertCircle class="h-5 w-5 text-red-500 flex-shrink-0" />
          <div>
            <div class="text-sm font-medium text-red-800">404 Not Found</div>
            <div class="text-sm text-red-600">Инвентаризация не найдена</div>
          </div>
        </div>
        <div class="flex items-center space-x-3 p-3 bg-red-50 rounded-lg">
          <AlertCircle class="h-5 w-5 text-red-500 flex-shrink-0" />
          <div>
            <div class="text-sm font-medium text-red-800">403 Forbidden</div>
            <div class="text-sm text-red-600">Нет доступа к данной инвентаризации</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ClipboardList, Copy, AlertCircle } from 'lucide-vue-next'

export default {
  name: 'InventoryDetailMethod',
  components: {
    ClipboardList,
    Copy,
    AlertCircle
  },
  data() {
    return {
      curlExample: `curl -X GET "https://api.example.com/api/inventories/1" \\
  -H "Authorization: Bearer YOUR_TOKEN" \\
  -H "Accept: application/json"`,
      responseExample: `{
  "data": {
    "id": 1,
    "number": "INV-2024-001",
    "date": "2024-01-15",
    "status": "completed",
    "warehouse": {
      "id": 1,
      "name": "Основной склад",
      "address": "г. Ташкент, ул. Складская, 45"
    },
    "description": "Годовая инвентаризация",
    "items": [
      {
        "id": 1,
        "product_id": 1,
        "product_name": "Товар 1",
        "expected_quantity": 100,
        "actual_quantity": 98,
        "difference": -2,
        "unit": "шт",
        "price": 10000.00,
        "total_difference": -20000.00
      }
    ],
    "files": [
      {
        "id": 1,
        "name": "inventory_report.pdf",
        "url": "https://example.com/files/inventory_report.pdf",
        "size": 512000,
        "type": "application/pdf"
      }
    ],
    "statistics": {
      "total_items": 150,
      "counted_items": 148,
      "discrepancy_items": 2,
      "total_difference_amount": -50000.00
    },
    "created_at": "2024-01-15T10:30:00Z",
    "updated_at": "2024-01-15T14:20:00Z"
  }
}`
    }
  },
  methods: {
    async copyEndpoint() {
      const endpoint = 'GET /api/inventories/{id}'
      try {
        await navigator.clipboard.writeText(endpoint)
      } catch (err) {
        console.error('Failed to copy endpoint: ', err)
      }
    },
    async copyCode(type) {
      const text = type === 'curl' ? this.curlExample : this.responseExample
      try {
        await navigator.clipboard.writeText(text)
      } catch (err) {
        console.error('Failed to copy code: ', err)
      }
    }
  }
}
</script> 