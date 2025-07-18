<template>
  <div class="bg-white rounded-lg shadow-sm p-6 lg:p-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
      <div class="flex items-center space-x-3">
        <div class="flex items-center space-x-2">
          <div class="px-3 py-1 bg-blue-100 text-blue-800 rounded-md text-sm font-medium">POST</div>
          <span class="font-mono text-lg text-gray-900">/balances/summary</span>
        </div>
        <h1 class="text-xl lg:text-2xl font-bold text-gray-900">Создание сводки остатков</h1>
      </div>
      <div class="flex items-center space-x-2">
        <div class="w-2 h-2 bg-green-500 rounded-full"></div>
        <span class="text-sm text-gray-600">Активно</span>
      </div>
    </div>

    <!-- Description -->
    <div class="mb-8">
      <p class="text-gray-700 leading-relaxed">
        Создает сводную информацию по остаткам товаров с возможностью фильтрации по различным параметрам. Позволяет получить агрегированные данные с учетом заданных критериев.
      </p>
    </div>

    <!-- Request Body -->
    <div class="mb-8">
      <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
        <Database class="h-5 w-5 mr-2" />
        Тело запроса
      </h3>
      <div class="bg-gray-50 rounded-lg p-4">
        <div class="flex items-center justify-between mb-2">
          <span class="text-sm font-medium text-gray-700">JSON</span>
          <button
            @click="copyToClipboard(requestBody)"
            class="flex items-center space-x-1 text-blue-600 hover:text-blue-700 text-sm"
          >
            <Copy class="h-4 w-4" />
            <span>Копировать</span>
          </button>
        </div>
        <pre class="text-sm text-gray-800 overflow-x-auto"><code>{{ requestBody }}</code></pre>
      </div>
    </div>

    <!-- Parameters -->
    <div class="mb-8">
      <h3 class="text-lg font-semibold text-gray-900 mb-4">Параметры</h3>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Поле</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Тип</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Обязательный</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Описание</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">warehouse_ids</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">array</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">Нет</span>
              </td>
              <td class="px-6 py-4 text-sm text-gray-500">Массив ID складов для фильтрации</td>
            </tr>
            <tr>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">category_ids</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">array</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">Нет</span>
              </td>
              <td class="px-6 py-4 text-sm text-gray-500">Массив ID категорий для фильтрации</td>
            </tr>
            <tr>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">currency_id</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">integer</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">Нет</span>
              </td>
              <td class="px-6 py-4 text-sm text-gray-500">ID валюты для расчета стоимости</td>
            </tr>
            <tr>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">include_zero</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">boolean</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">Нет</span>
              </td>
              <td class="px-6 py-4 text-sm text-gray-500">Включить товары с нулевым остатком</td>
            </tr>
            <tr>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">group_by</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">string</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">Нет</span>
              </td>
              <td class="px-6 py-4 text-sm text-gray-500">Группировка: warehouse, category, product</td>
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
          <span class="text-sm font-medium text-gray-700">JSON (201 Created)</span>
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
            <span class="text-sm font-medium text-red-800">400 Bad Request</span>
            <span class="text-xs text-red-600">Валидация</span>
          </div>
          <p class="text-sm text-red-700">
            Неверные данные в запросе. Проверьте обязательные поля и типы данных.
          </p>
        </div>
        <div class="border border-red-200 rounded-lg p-4 bg-red-50">
          <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium text-red-800">404 Not Found</span>
            <span class="text-xs text-red-600">Не найдено</span>
          </div>
          <p class="text-sm text-red-700">
            Склад, категория или валюта с указанным ID не найдена.
          </p>
        </div>
        <div class="border border-red-200 rounded-lg p-4 bg-red-50">
          <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium text-red-800">422 Unprocessable Entity</span>
            <span class="text-xs text-red-600">Ошибка валидации</span>
          </div>
          <p class="text-sm text-red-700">
            Ошибка валидации данных. Проверьте формат и ограничения полей.
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
import { Database, CheckCircle, AlertCircle, Copy, Send } from 'lucide-vue-next'

export default {
  name: 'BalanceSummaryPostMethod',
  components: {
    Database,
    CheckCircle,
    AlertCircle,
    Copy,
    Send
  },
  data() {
    return {
      requestBody: `{
  "warehouse_ids": [1, 2, 3],
  "category_ids": [1, 5],
  "currency_id": 1,
  "include_zero": false,
  "group_by": "warehouse"
}`,
      responseBody: `{
  "success": true,
  "data": {
    "summary_id": "summary_20240115_001",
    "total_products": 150,
    "total_quantity": 2500.75,
    "total_value": 3750000.00,
    "currency": {
      "id": 1,
      "code": "USD",
      "name": "Доллар США"
    },
    "filters": {
      "warehouse_ids": [1, 2, 3],
      "category_ids": [1, 5],
      "include_zero": false,
      "group_by": "warehouse"
    },
    "grouped_data": [
      {
        "warehouse_id": 1,
        "warehouse_name": "Основной склад",
        "products_count": 120,
        "total_quantity": 1800.50,
        "total_value": 2700000.00
      },
      {
        "warehouse_id": 2,
        "warehouse_name": "Склад 2",
        "products_count": 30,
        "total_quantity": 700.25,
        "total_value": 1050000.00
      }
    ],
    "created_at": "2024-01-15T10:30:00.000000Z"
  },
  "message": "Сводка остатков успешно создана"
}`,
      curlExample: `curl -X POST "https://api.example.com/api/balances/summary" \\
  -H "Content-Type: application/json" \\
  -H "Authorization: Bearer YOUR_TOKEN" \\
  -d '{
    "warehouse_ids": [1, 2, 3],
    "category_ids": [1, 5],
    "currency_id": 1,
    "include_zero": false,
    "group_by": "warehouse"
  }'`
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