<template>
  <div class="bg-white rounded-lg shadow-sm p-6 lg:p-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
      <div class="flex items-center space-x-3">
        <div class="bg-blue-100 p-2 rounded-lg">
          <Receipt class="h-5 w-5 text-blue-600" />
        </div>
        <div>
          <div class="flex items-center space-x-2">
            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-medium">POST</span>
            <span class="font-mono text-sm lg:text-base text-gray-900">/receipts</span>
          </div>
          <h1 class="text-lg lg:text-xl font-bold text-gray-900 mt-1">Создание оприходования</h1>
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
        Создает новое оприходование с указанными товарами и поставщиком. 
        Метод поддерживает создание черновика или сразу подтвержденного оприходования.
      </p>
    </div>

    <!-- Request Body -->
    <div class="mb-6">
      <h2 class="text-base font-semibold text-gray-900 mb-3">Тело запроса</h2>
      <div class="bg-gray-50 rounded-lg p-4">
        <div class="space-y-3">
          <div class="flex items-start space-x-3">
            <span class="text-sm font-medium text-gray-700 min-w-24">date</span>
            <div class="flex-1">
              <span class="text-sm text-gray-600">Дата оприходования (YYYY-MM-DD) (обязательно)</span>
            </div>
          </div>
          <div class="flex items-start space-x-3">
            <span class="text-sm font-medium text-gray-700 min-w-24">supplier_id</span>
            <div class="flex-1">
              <span class="text-sm text-gray-600">ID поставщика (обязательно)</span>
            </div>
          </div>
          <div class="flex items-start space-x-3">
            <span class="text-sm font-medium text-gray-700 min-w-24">warehouse_id</span>
            <div class="flex-1">
              <span class="text-sm text-gray-600">ID склада (обязательно)</span>
            </div>
          </div>
          <div class="flex items-start space-x-3">
            <span class="text-sm font-medium text-gray-700 min-w-24">currency</span>
            <div class="flex-1">
              <span class="text-sm text-gray-600">Валюта (UZS, USD, EUR) (обязательно)</span>
            </div>
          </div>
          <div class="flex items-start space-x-3">
            <span class="text-sm font-medium text-gray-700 min-w-24">positions</span>
            <div class="flex-1">
              <span class="text-sm text-gray-600">Массив позиций товаров (обязательно)</span>
            </div>
          </div>
          <div class="flex items-start space-x-3">
            <span class="text-sm font-medium text-gray-700 min-w-24">notes</span>
            <div class="flex-1">
              <span class="text-sm text-gray-600">Примечания к оприходованию</span>
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
            <span class="text-sm font-medium text-gray-700 min-w-24">success</span>
            <div class="flex-1">
              <span class="text-sm text-gray-600">Статус операции (true/false)</span>
            </div>
          </div>
          <div class="flex items-start space-x-3">
            <span class="text-sm font-medium text-gray-700 min-w-24">data</span>
            <div class="flex-1">
              <span class="text-sm text-gray-600">Созданное оприходование</span>
            </div>
          </div>
          <div class="flex items-start space-x-3">
            <span class="text-sm font-medium text-gray-700 min-w-24">id</span>
            <div class="flex-1">
              <span class="text-sm text-gray-600">Уникальный идентификатор</span>
            </div>
          </div>
          <div class="flex items-start space-x-3">
            <span class="text-sm font-medium text-gray-700 min-w-24">number</span>
            <div class="flex-1">
              <span class="text-sm text-gray-600">Автоматически сгенерированный номер</span>
            </div>
          </div>
          <div class="flex items-start space-x-3">
            <span class="text-sm font-medium text-gray-700 min-w-24">status</span>
            <div class="flex-1">
              <span class="text-sm text-gray-600">Статус: draft или pending</span>
            </div>
          </div>
          <div class="flex items-start space-x-3">
            <span class="text-sm font-medium text-gray-700 min-w-24">total_amount</span>
            <div class="flex-1">
              <span class="text-sm text-gray-600">Рассчитанная общая сумма</span>
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
        <div class="flex items-center space-x-3 p-3 bg-yellow-50 rounded-lg">
          <AlertCircle class="h-5 w-5 text-yellow-500 flex-shrink-0" />
          <div>
            <div class="text-sm font-medium text-yellow-800">422 Validation Error</div>
            <div class="text-sm text-yellow-600">Ошибка валидации данных</div>
          </div>
        </div>
        <div class="flex items-center space-x-3 p-3 bg-red-50 rounded-lg">
          <AlertCircle class="h-5 w-5 text-red-500 flex-shrink-0" />
          <div>
            <div class="text-sm font-medium text-red-800">400 Bad Request</div>
            <div class="text-sm text-red-600">Неверные данные запроса</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Receipt, Copy, AlertCircle } from 'lucide-vue-next'

export default {
  name: 'ReceiptCreateMethod',
  components: {
    Receipt,
    Copy,
    AlertCircle
  },
  data() {
    return {
      curlExample: `curl -X POST "https://api.example.com/api/receipts" \\
  -H "Authorization: Bearer YOUR_TOKEN" \\
  -H "Content-Type: application/json" \\
  -H "Accept: application/json" \\
  -d '{
    "date": "2024-01-15",
    "supplier_id": 1,
    "warehouse_id": 1,
    "currency": "UZS",
    "positions": [
      {
        "product_id": 1,
        "quantity": 10,
        "price": 15000.00
      }
    ],
    "notes": "Оприходование товаров от поставщика"
  }'`,
      responseExample: `{
  "success": true,
  "data": {
    "id": 1,
    "number": "R-2024-001",
    "date": "2024-01-15",
    "status": "draft",
    "supplier_id": 1,
    "warehouse_id": 1,
    "currency": "UZS",
    "total_amount": 150000.00,
    "positions": [
      {
        "id": 1,
        "product_id": 1,
        "product_name": "Товар 1",
        "quantity": 10,
        "price": 15000.00,
        "total": 150000.00
      }
    ],
    "notes": "Оприходование товаров от поставщика",
    "created_at": "2024-01-15T10:30:00Z",
    "updated_at": "2024-01-15T10:30:00Z"
  },
  "message": "Оприходование успешно создано"
}`
    }
  },
  methods: {
    async copyEndpoint() {
      const endpoint = 'POST /api/receipts'
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