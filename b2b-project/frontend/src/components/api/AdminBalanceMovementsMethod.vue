<template>
  <div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <!-- Header -->
    <div class="px-6 py-4 border-b border-gray-200">
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-3">
          <div class="flex items-center space-x-2">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
              POST
            </span>
            <span class="text-sm font-mono text-gray-600">/admin/balances/movements</span>
          </div>
        </div>
        <div class="flex items-center space-x-2">
          <span class="text-sm text-gray-500">Администратор</span>
          <Shield class="h-4 w-4 text-gray-400" />
        </div>
      </div>
    </div>

    <!-- Content -->
    <div class="p-6 space-y-6">
      <!-- Description -->
      <div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Движения остатков</h3>
        <p class="text-gray-600">
          Получение истории движений остатков товаров с возможностью фильтрации по различным критериям. 
          Метод позволяет администраторам отслеживать изменения остатков товаров.
        </p>
      </div>

      <!-- Request -->
      <div>
        <h4 class="text-sm font-medium text-gray-900 mb-3 flex items-center">
          <Send class="h-4 w-4 mr-2" />
          Запрос
        </h4>
        <div class="bg-gray-50 rounded-lg p-4">
          <div class="flex items-center justify-between mb-3">
            <span class="text-sm font-medium text-gray-700">Headers:</span>
            <button @click="copyToClipboard(headers)" class="text-blue-600 hover:text-blue-700">
              <Copy class="h-4 w-4" />
            </button>
          </div>
          <pre class="text-sm text-gray-800 overflow-x-auto"><code>{{ headers }}</code></pre>
        </div>
        
        <div class="bg-gray-50 rounded-lg p-4 mt-3">
          <div class="flex items-center justify-between mb-3">
            <span class="text-sm font-medium text-gray-700">Body:</span>
            <button @click="copyToClipboard(requestBody)" class="text-blue-600 hover:text-blue-700">
              <Copy class="h-4 w-4" />
            </button>
          </div>
          <pre class="text-sm text-gray-800 overflow-x-auto"><code>{{ requestBody }}</code></pre>
        </div>
      </div>

      <!-- Response -->
      <div>
        <h4 class="text-sm font-medium text-gray-900 mb-3 flex items-center">
          <CheckCircle class="h-4 w-4 mr-2" />
          Ответ
        </h4>
        <div class="bg-green-50 rounded-lg p-4">
          <div class="flex items-center justify-between mb-3">
            <span class="text-sm font-medium text-gray-700">Success (200):</span>
            <button @click="copyToClipboard(successResponse)" class="text-blue-600 hover:text-blue-700">
              <Copy class="h-4 w-4" />
            </button>
          </div>
          <pre class="text-sm text-gray-800 overflow-x-auto"><code>{{ successResponse }}</code></pre>
        </div>
      </div>

      <!-- Parameters -->
      <div>
        <h4 class="text-sm font-medium text-gray-900 mb-3">Параметры фильтрации</h4>
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Параметр</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Тип</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Обязательный</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Описание</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr>
                <td class="px-4 py-3 text-sm font-mono text-gray-900">product_id</td>
                <td class="px-4 py-3 text-sm text-gray-600">integer</td>
                <td class="px-4 py-3 text-sm text-gray-600">Нет</td>
                <td class="px-4 py-3 text-sm text-gray-600">ID товара для фильтрации</td>
              </tr>
              <tr>
                <td class="px-4 py-3 text-sm font-mono text-gray-900">warehouse_id</td>
                <td class="px-4 py-3 text-sm text-gray-600">integer</td>
                <td class="px-4 py-3 text-sm text-gray-600">Нет</td>
                <td class="px-4 py-3 text-sm text-gray-600">ID склада для фильтрации</td>
              </tr>
              <tr>
                <td class="px-4 py-3 text-sm font-mono text-gray-900">operation_type</td>
                <td class="px-4 py-3 text-sm text-gray-600">string</td>
                <td class="px-4 py-3 text-sm text-gray-600">Нет</td>
                <td class="px-4 py-3 text-sm text-gray-600">Тип операции (receipt, write_off, transfer_in, transfer_out)</td>
              </tr>
              <tr>
                <td class="px-4 py-3 text-sm font-mono text-gray-900">date_from</td>
                <td class="px-4 py-3 text-sm text-gray-600">date</td>
                <td class="px-4 py-3 text-sm text-gray-600">Нет</td>
                <td class="px-4 py-3 text-sm text-gray-600">Дата начала периода (YYYY-MM-DD)</td>
              </tr>
              <tr>
                <td class="px-4 py-3 text-sm font-mono text-gray-900">date_to</td>
                <td class="px-4 py-3 text-sm text-gray-600">date</td>
                <td class="px-4 py-3 text-sm text-gray-600">Нет</td>
                <td class="px-4 py-3 text-sm text-gray-600">Дата окончания периода (YYYY-MM-DD)</td>
              </tr>
              <tr>
                <td class="px-4 py-3 text-sm font-mono text-gray-900">sort_by</td>
                <td class="px-4 py-3 text-sm text-gray-600">string</td>
                <td class="px-4 py-3 text-sm text-gray-600">Нет</td>
                <td class="px-4 py-3 text-sm text-gray-600">Поле для сортировки (created_at, quantity, operation_type)</td>
              </tr>
              <tr>
                <td class="px-4 py-3 text-sm font-mono text-gray-900">sort_order</td>
                <td class="px-4 py-3 text-sm text-gray-600">string</td>
                <td class="px-4 py-3 text-sm text-gray-600">Нет</td>
                <td class="px-4 py-3 text-sm text-gray-600">Порядок сортировки (asc, desc)</td>
              </tr>
              <tr>
                <td class="px-4 py-3 text-sm font-mono text-gray-900">page</td>
                <td class="px-4 py-3 text-sm text-gray-600">integer</td>
                <td class="px-4 py-3 text-sm text-gray-600">Нет</td>
                <td class="px-4 py-3 text-sm text-gray-600">Номер страницы (по умолчанию: 1)</td>
              </tr>
              <tr>
                <td class="px-4 py-3 text-sm font-mono text-gray-900">per_page</td>
                <td class="px-4 py-3 text-sm text-gray-600">integer</td>
                <td class="px-4 py-3 text-sm text-gray-600">Нет</td>
                <td class="px-4 py-3 text-sm text-gray-600">Количество элементов на странице (по умолчанию: 20)</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Errors -->
      <div>
        <h4 class="text-sm font-medium text-gray-900 mb-3 flex items-center">
          <AlertCircle class="h-4 w-4 mr-2" />
          Ошибки
        </h4>
        <div class="space-y-3">
          <div class="bg-red-50 rounded-lg p-4">
            <div class="flex items-center justify-between mb-2">
              <span class="text-sm font-medium text-red-800">401 Unauthorized</span>
              <button @click="copyToClipboard(unauthorizedError)" class="text-red-600 hover:text-red-700">
                <Copy class="h-4 w-4" />
              </button>
            </div>
            <p class="text-sm text-red-700 mb-2">Не авторизован или отсутствуют права администратора</p>
            <pre class="text-sm text-red-800 overflow-x-auto"><code>{{ unauthorizedError }}</code></pre>
          </div>
          
          <div class="bg-red-50 rounded-lg p-4">
            <div class="flex items-center justify-between mb-2">
              <span class="text-sm font-medium text-red-800">422 Validation Error</span>
              <button @click="copyToClipboard(validationError)" class="text-red-600 hover:text-red-700">
                <Copy class="h-4 w-4" />
              </button>
            </div>
            <p class="text-sm text-red-700 mb-2">Ошибка валидации параметров</p>
            <pre class="text-sm text-red-800 overflow-x-auto"><code>{{ validationError }}</code></pre>
          </div>
        </div>
      </div>

      <!-- Examples -->
      <div>
        <h4 class="text-sm font-medium text-gray-900 mb-3">Примеры использования</h4>
        <div class="space-y-4">
          <div class="bg-blue-50 rounded-lg p-4">
            <h5 class="text-sm font-medium text-blue-900 mb-2">Движения по товару</h5>
            <pre class="text-sm text-blue-800 overflow-x-auto"><code>{{ movementsByProductExample }}</code></pre>
          </div>
          
          <div class="bg-blue-50 rounded-lg p-4">
            <h5 class="text-sm font-medium text-blue-900 mb-2">Движения по складу</h5>
            <pre class="text-sm text-blue-800 overflow-x-auto"><code>{{ movementsByWarehouseExample }}</code></pre>
          </div>
          
          <div class="bg-blue-50 rounded-lg p-4">
            <h5 class="text-sm font-medium text-blue-900 mb-2">Движения за период</h5>
            <pre class="text-sm text-blue-800 overflow-x-auto"><code>{{ movementsByDateExample }}</code></pre>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Send, CheckCircle, AlertCircle, Copy, Shield } from 'lucide-vue-next'

export default {
  name: 'AdminBalanceMovementsMethod',
  components: {
    Send,
    CheckCircle,
    AlertCircle,
    Copy,
    Shield
  },
  data() {
    return {
      headers: `{
  "Authorization": "Bearer {token}",
  "Content-Type": "application/json",
  "Accept": "application/json"
}`,
      requestBody: `{
  "product_id": 1,
  "warehouse_id": 1,
  "operation_type": "receipt",
  "date_from": "2024-01-01",
  "date_to": "2024-01-31",
  "sort_by": "created_at",
  "sort_order": "desc",
  "page": 1,
  "per_page": 20
}`,
      successResponse: `{
  "success": true,
  "data": {
    "movements": [
      {
        "id": 1,
        "product_id": 1,
        "product_name": "Ноутбук Dell Inspiron",
        "warehouse_id": 1,
        "warehouse_name": "Основной склад",
        "operation_type": "receipt",
        "operation_id": 1,
        "operation_number": "R-2024-001",
        "quantity": 10,
        "quantity_before": 5,
        "quantity_after": 15,
        "unit_price": 45000.00,
        "total_amount": 450000.00,
        "currency": "UZS",
        "notes": "Оприходование от поставщика",
        "created_at": "2024-01-15T10:30:00Z",
        "user_id": 1,
        "user_name": "Администратор"
      }
    ],
    "pagination": {
      "current_page": 1,
      "per_page": 20,
      "total": 1,
      "last_page": 1
    },
    "summary": {
      "total_movements": 1,
      "total_quantity": 10,
      "total_amount": 450000.00,
      "currency": "UZS"
    }
  }
}`,
      unauthorizedError: `{
  "success": false,
  "message": "Unauthorized",
  "error": "Требуется авторизация администратора"
}`,
      validationError: `{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "product_id": ["Товар не найден"],
    "warehouse_id": ["Склад не найден"],
    "date_from": ["Неверный формат даты"]
  }
}`,
      movementsByProductExample: `POST /api/admin/balances/movements
{
  "product_id": 1,
  "sort_by": "created_at",
  "sort_order": "desc"
}`,
      movementsByWarehouseExample: `POST /api/admin/balances/movements
{
  "warehouse_id": 1,
  "operation_type": "receipt",
  "date_from": "2024-01-01",
  "date_to": "2024-01-31"
}`,
      movementsByDateExample: `POST /api/admin/balances/movements
{
  "date_from": "2024-01-01",
  "date_to": "2024-01-31",
  "operation_type": "write_off",
  "sort_by": "quantity",
  "sort_order": "desc"
}`
    }
  },
  methods: {
    copyToClipboard(text) {
      navigator.clipboard.writeText(text).then(() => {
        // Можно добавить уведомление об успешном копировании
      })
    }
  }
}
</script> 