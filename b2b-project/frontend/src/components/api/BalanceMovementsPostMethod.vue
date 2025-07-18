<template>
  <div class="bg-white rounded-lg shadow-sm p-6 lg:p-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
      <div class="flex items-center space-x-3">
        <div class="flex items-center space-x-2">
          <div class="px-3 py-1 bg-blue-100 text-blue-800 rounded-md text-sm font-medium">POST</div>
          <span class="font-mono text-lg text-gray-900">/balances/movements</span>
        </div>
        <h1 class="text-xl lg:text-2xl font-bold text-gray-900">Создание движения остатков</h1>
      </div>
      <div class="flex items-center space-x-2">
        <div class="w-2 h-2 bg-green-500 rounded-full"></div>
        <span class="text-sm text-gray-600">Активно</span>
      </div>
    </div>

    <!-- Description -->
    <div class="mb-8">
      <p class="text-gray-700 leading-relaxed">
        Создает новое движение остатков товара. Позволяет зафиксировать изменение количества товара на складе с указанием типа операции и причины.
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
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">product_id</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">integer</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">Да</span>
              </td>
              <td class="px-6 py-4 text-sm text-gray-500">ID товара</td>
            </tr>
            <tr>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">warehouse_id</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">integer</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">Да</span>
              </td>
              <td class="px-6 py-4 text-sm text-gray-500">ID склада</td>
            </tr>
            <tr>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">quantity</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">decimal</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">Да</span>
              </td>
              <td class="px-6 py-4 text-sm text-gray-500">Количество (положительное для прихода, отрицательное для расхода)</td>
            </tr>
            <tr>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">operation_type</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">string</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">Да</span>
              </td>
              <td class="px-6 py-4 text-sm text-gray-500">Тип операции: receipt, write_off, transfer_in, transfer_out, adjustment</td>
            </tr>
            <tr>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">reference_id</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">integer</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">Нет</span>
              </td>
              <td class="px-6 py-4 text-sm text-gray-500">ID связанного документа (накладная, списание и т.д.)</td>
            </tr>
            <tr>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">reference_type</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">string</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">Нет</span>
              </td>
              <td class="px-6 py-4 text-sm text-gray-500">Тип связанного документа: receipt, write_off, transfer, inventory</td>
            </tr>
            <tr>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">unit_price</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">decimal</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">Нет</span>
              </td>
              <td class="px-6 py-4 text-sm text-gray-500">Цена за единицу</td>
            </tr>
            <tr>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">currency_id</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">integer</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">Нет</span>
              </td>
              <td class="px-6 py-4 text-sm text-gray-500">ID валюты</td>
            </tr>
            <tr>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">notes</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">string</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">Нет</span>
              </td>
              <td class="px-6 py-4 text-sm text-gray-500">Дополнительные заметки</td>
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
            Товар или склад с указанным ID не найден.
          </p>
        </div>
        <div class="border border-red-200 rounded-lg p-4 bg-red-50">
          <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium text-red-800">409 Conflict</span>
            <span class="text-xs text-red-600">Конфликт</span>
          </div>
          <p class="text-sm text-red-700">
            Недостаточно товара на складе для выполнения операции.
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
  name: 'BalanceMovementsPostMethod',
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
  "product_id": 1,
  "warehouse_id": 2,
  "quantity": 50.00,
  "operation_type": "receipt",
  "reference_id": 15,
  "reference_type": "receipt",
  "unit_price": 1500.00,
  "currency_id": 1,
  "notes": "Поступление товара по накладной №15"
}`,
      responseBody: `{
  "success": true,
  "data": {
    "id": 25,
    "product_id": 1,
    "warehouse_id": 2,
    "quantity": "50.00",
    "operation_type": "receipt",
    "reference_id": 15,
    "reference_type": "receipt",
    "unit_price": "1500.00",
    "currency_id": 1,
    "total_value": "75000.00",
    "balance_before": "100.50",
    "balance_after": "150.50",
    "notes": "Поступление товара по накладной №15",
    "created_at": "2024-01-15T10:30:00.000000Z",
    "updated_at": "2024-01-15T10:30:00.000000Z",
    "product": {
      "id": 1,
      "name": "Товар 1",
      "sku": "SKU001"
    },
    "warehouse": {
      "id": 2,
      "name": "Склад 2",
      "address": "ул. Примерная, 123"
    },
    "currency": {
      "id": 1,
      "code": "USD",
      "name": "Доллар США"
    }
  },
  "message": "Движение остатков успешно создано"
}`,
      curlExample: `curl -X POST "https://api.example.com/api/balances/movements" \\
  -H "Content-Type: application/json" \\
  -H "Authorization: Bearer YOUR_TOKEN" \\
  -d '{
    "product_id": 1,
    "warehouse_id": 2,
    "quantity": 50.00,
    "operation_type": "receipt",
    "reference_id": 15,
    "reference_type": "receipt",
    "unit_price": 1500.00,
    "currency_id": 1,
    "notes": "Поступление товара по накладной №15"
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