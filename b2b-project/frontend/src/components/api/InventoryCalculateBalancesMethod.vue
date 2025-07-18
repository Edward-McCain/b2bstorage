<template>
  <div class="bg-white rounded-lg shadow-sm p-6 lg:p-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
      <div class="flex items-center space-x-3">
        <div class="bg-blue-100 p-2 rounded-lg">
          <ClipboardList class="h-5 w-5 text-blue-600" />
        </div>
        <div>
          <div class="flex items-center space-x-2">
            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-medium">POST</span>
            <span class="font-mono text-sm lg:text-base text-gray-900">/inventories/calculate-balances</span>
          </div>
          <h1 class="text-lg lg:text-xl font-bold text-gray-900 mt-1">Расчет балансов инвентаризации</h1>
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
        Выполняет расчет балансов для инвентаризации на основе данных о товарах и их остатках. 
        Метод автоматически рассчитывает ожидаемые и фактические количества, а также расхождения.
      </p>
    </div>

    <!-- Request Body -->
    <div class="mb-6">
      <h2 class="text-base font-semibold text-gray-900 mb-3">Тело запроса</h2>
      <div class="bg-gray-50 rounded-lg p-4">
        <div class="space-y-3">
          <div class="flex items-start space-x-3">
            <span class="text-sm font-medium text-gray-700 min-w-24">inventory_id</span>
            <div class="flex-1">
              <span class="text-sm text-gray-600">ID инвентаризации (обязательно)</span>
            </div>
          </div>
          <div class="flex items-start space-x-3">
            <span class="text-sm font-medium text-gray-700 min-w-24">warehouse_id</span>
            <div class="flex-1">
              <span class="text-sm text-gray-600">ID склада для расчета (обязательно)</span>
            </div>
          </div>
          <div class="flex items-start space-x-3">
            <span class="text-sm font-medium text-gray-700 min-w-24">date</span>
            <div class="flex-1">
              <span class="text-sm text-gray-600">Дата расчета балансов (YYYY-MM-DD) (обязательно)</span>
            </div>
          </div>
          <div class="flex items-start space-x-3">
            <span class="text-sm font-medium text-gray-700 min-w-24">include_zero_balances</span>
            <div class="flex-1">
              <span class="text-sm text-gray-600">Включить товары с нулевым балансом (true/false) (по умолчанию: false)</span>
            </div>
          </div>
          <div class="flex items-start space-x-3">
            <span class="text-sm font-medium text-gray-700 min-w-24">category_id</span>
            <div class="flex-1">
              <span class="text-sm text-gray-600">Фильтр по категории товаров</span>
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
              <span class="text-sm text-gray-600">Результаты расчета балансов</span>
            </div>
          </div>
          <div class="flex items-start space-x-3">
            <span class="text-sm font-medium text-gray-700 min-w-24">items</span>
            <div class="flex-1">
              <span class="text-sm text-gray-600">Массив позиций с рассчитанными балансами</span>
            </div>
          </div>
          <div class="flex items-start space-x-3">
            <span class="text-sm font-medium text-gray-700 min-w-24">product_id</span>
            <div class="flex-1">
              <span class="text-sm text-gray-600">ID товара</span>
            </div>
          </div>
          <div class="flex items-start space-x-3">
            <span class="text-sm font-medium text-gray-700 min-w-24">expected_quantity</span>
            <div class="flex-1">
              <span class="text-sm text-gray-600">Ожидаемое количество</span>
            </div>
          </div>
          <div class="flex items-start space-x-3">
            <span class="text-sm font-medium text-gray-700 min-w-24">actual_quantity</span>
            <div class="flex-1">
              <span class="text-sm text-gray-600">Фактическое количество</span>
            </div>
          </div>
          <div class="flex items-start space-x-3">
            <span class="text-sm font-medium text-gray-700 min-w-24">difference</span>
            <div class="flex-1">
              <span class="text-sm text-gray-600">Разница (фактическое - ожидаемое)</span>
            </div>
          </div>
          <div class="flex items-start space-x-3">
            <span class="text-sm font-medium text-gray-700 min-w-24">statistics</span>
            <div class="flex-1">
              <span class="text-sm text-gray-600">Общая статистика расчета</span>
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
            <div class="text-sm font-medium text-red-800">404 Not Found</div>
            <div class="text-sm text-red-600">Инвентаризация или склад не найдены</div>
          </div>
        </div>
        <div class="flex items-center space-x-3 p-3 bg-red-50 rounded-lg">
          <AlertCircle class="h-5 w-5 text-red-500 flex-shrink-0" />
          <div>
            <div class="text-sm font-medium text-red-800">400 Bad Request</div>
            <div class="text-sm text-red-600">Ошибка расчета балансов</div>
          </div>
        </div>
        <div class="flex items-center space-x-3 p-3 bg-red-50 rounded-lg">
          <AlertCircle class="h-5 w-5 text-red-500 flex-shrink-0" />
          <div>
            <div class="text-sm font-medium text-red-800">500 Internal Server Error</div>
            <div class="text-sm text-red-600">Внутренняя ошибка сервера</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ClipboardList, Copy, AlertCircle } from 'lucide-vue-next'

export default {
  name: 'InventoryCalculateBalancesMethod',
  components: {
    ClipboardList,
    Copy,
    AlertCircle
  },
  data() {
    return {
      curlExample: `curl -X POST "https://api.example.com/api/inventories/calculate-balances" \\
  -H "Authorization: Bearer YOUR_TOKEN" \\
  -H "Content-Type: application/json" \\
  -H "Accept: application/json" \\
  -d '{
    "inventory_id": 1,
    "warehouse_id": 1,
    "date": "2024-01-15",
    "include_zero_balances": false,
    "category_id": 5
  }'`,
      responseExample: `{
  "success": true,
  "data": {
    "inventory_id": 1,
    "warehouse_id": 1,
    "calculation_date": "2024-01-15",
    "items": [
      {
        "product_id": 1,
        "product_name": "Товар 1",
        "product_code": "T001",
        "category": "Электроника",
        "expected_quantity": 100,
        "actual_quantity": 98,
        "difference": -2,
        "unit": "шт",
        "price": 10000.00,
        "total_difference": -20000.00,
        "last_movement_date": "2024-01-10"
      }
    ],
    "statistics": {
      "total_items": 150,
      "items_with_discrepancy": 12,
      "total_expected_value": 1500000.00,
      "total_actual_value": 1485000.00,
      "total_difference_value": -15000.00,
      "calculation_time": "2.5s"
    },
    "message": "Балансы успешно рассчитаны"
  }
}`
    }
  },
  methods: {
    async copyEndpoint() {
      const endpoint = 'POST /api/inventories/calculate-balances'
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