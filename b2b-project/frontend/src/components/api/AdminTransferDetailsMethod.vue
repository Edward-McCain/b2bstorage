<template>
  <div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <!-- Header -->
    <div class="px-6 py-4 border-b border-gray-200">
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-3">
          <div class="flex items-center space-x-2">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
              GET
            </span>
            <span class="text-sm font-mono text-gray-600">/admin/transfers/{id}</span>
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
        <h3 class="text-lg font-medium text-gray-900 mb-2">Детали перемещения</h3>
        <p class="text-gray-600">
          Получение подробной информации о перемещении товаров включая все позиции, статус и связанные данные. 
          Метод доступен только администраторам системы.
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
            <span class="text-sm font-medium text-gray-700">URL Parameters:</span>
            <button @click="copyToClipboard(urlParams)" class="text-blue-600 hover:text-blue-700">
              <Copy class="h-4 w-4" />
            </button>
          </div>
          <pre class="text-sm text-gray-800 overflow-x-auto"><code>{{ urlParams }}</code></pre>
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
        <h4 class="text-sm font-medium text-gray-900 mb-3">Параметры</h4>
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
                <td class="px-4 py-3 text-sm font-mono text-gray-900">id</td>
                <td class="px-4 py-3 text-sm text-gray-600">integer</td>
                <td class="px-4 py-3 text-sm text-gray-600">Да</td>
                <td class="px-4 py-3 text-sm text-gray-600">ID перемещения</td>
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
              <span class="text-sm font-medium text-red-800">404 Not Found</span>
              <button @click="copyToClipboard(notFoundError)" class="text-red-600 hover:text-red-700">
                <Copy class="h-4 w-4" />
              </button>
            </div>
            <p class="text-sm text-red-700 mb-2">Перемещение не найдено</p>
            <pre class="text-sm text-red-800 overflow-x-auto"><code>{{ notFoundError }}</code></pre>
          </div>
        </div>
      </div>

      <!-- Examples -->
      <div>
        <h4 class="text-sm font-medium text-gray-900 mb-3">Примеры использования</h4>
        <div class="space-y-4">
          <div class="bg-blue-50 rounded-lg p-4">
            <h5 class="text-sm font-medium text-blue-900 mb-2">Получение деталей перемещения</h5>
            <pre class="text-sm text-blue-800 overflow-x-auto"><code>{{ getTransferDetailsExample }}</code></pre>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Send, CheckCircle, AlertCircle, Copy, Shield } from 'lucide-vue-next'

export default {
  name: 'AdminTransferDetailsMethod',
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
  "Accept": "application/json"
}`,
      urlParams: `{
  "id": 1
}`,
      successResponse: `{
  "success": true,
  "data": {
    "id": 1,
    "number": "T-2024-001",
    "date": "2024-01-15",
    "from_warehouse_id": 1,
    "from_warehouse_name": "Основной склад",
    "to_warehouse_id": 2,
    "to_warehouse_name": "Склад в Ташкенте",
    "status": "completed",
    "total_items": 3,
    "total_quantity": 25,
    "total_value": 1125000.00,
    "currency": "UZS",
    "notes": "Перемещение товаров между складами",
    "created_at": "2024-01-15T10:30:00Z",
    "updated_at": "2024-01-15T10:30:00Z",
    "completed_at": "2024-01-15T11:00:00Z",
    "positions": [
      {
        "id": 1,
        "product_id": 1,
        "product_name": "Ноутбук Dell Inspiron",
        "quantity": 10,
        "unit_price": 45000.00,
        "total_price": 450000.00,
        "currency": "UZS"
      },
      {
        "id": 2,
        "product_id": 2,
        "product_name": "iPhone 15",
        "quantity": 15,
        "unit_price": 45000.00,
        "total_price": 675000.00,
        "currency": "UZS"
      }
    ],
    "user_id": 1,
    "user_name": "Администратор",
    "history": [
      {
        "id": 1,
        "action": "created",
        "description": "Перемещение создано",
        "user_id": 1,
        "user_name": "Администратор",
        "created_at": "2024-01-15T10:30:00Z"
      },
      {
        "id": 2,
        "action": "confirmed",
        "description": "Перемещение подтверждено",
        "user_id": 1,
        "user_name": "Администратор",
        "created_at": "2024-01-15T10:45:00Z"
      },
      {
        "id": 3,
        "action": "completed",
        "description": "Перемещение завершено",
        "user_id": 1,
        "user_name": "Администратор",
        "created_at": "2024-01-15T11:00:00Z"
      }
    ]
  }
}`,
      unauthorizedError: `{
  "success": false,
  "message": "Unauthorized",
  "error": "Требуется авторизация администратора"
}`,
      notFoundError: `{
  "success": false,
  "message": "Not Found",
  "error": "Перемещение не найдено"
}`,
      getTransferDetailsExample: `GET /api/admin/transfers/1
Authorization: Bearer {admin_token}`
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