<template>
  <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <div class="flex items-center gap-3 mb-4">
      <div class="flex items-center justify-center w-10 h-10 bg-blue-100 rounded-lg">
        <BarChart3 class="w-5 h-5 text-blue-600" />
      </div>
      <div>
        <h3 class="text-lg font-semibold text-gray-900">GET/POST /balances/movements</h3>
        <p class="text-sm text-gray-600">Движения по остаткам товаров</p>
      </div>
    </div>

    <div class="space-y-4">
      <!-- Описание -->
      <div>
        <h4 class="font-medium text-gray-900 mb-2">Описание</h4>
        <p class="text-gray-700 text-sm">
          Возвращает историю движений по остаткам товаров (приходы, списания, перемещения и т.д.) с возможностью фильтрации по складам, товарам, датам и операциям. Поддерживает как GET, так и POST запросы для расширенного поиска.
        </p>
      </div>

      <!-- Параметры GET -->
      <div>
        <h4 class="font-medium text-gray-900 mb-2">Параметры GET-запроса</h4>
        <div class="bg-gray-50 rounded-lg p-4">
          <div class="space-y-2 text-sm">
            <div class="flex justify-between">
              <span class="font-medium">warehouse_id</span>
              <span class="text-gray-600">ID склада для фильтрации (optional)</span>
            </div>
            <div class="flex justify-between">
              <span class="font-medium">product_id</span>
              <span class="text-gray-600">ID товара для фильтрации (optional)</span>
            </div>
            <div class="flex justify-between">
              <span class="font-medium">operation_type</span>
              <span class="text-gray-600">Тип операции (приход, списание, перемещение и т.д.)</span>
            </div>
            <div class="flex justify-between">
              <span class="font-medium">date_from</span>
              <span class="text-gray-600">Дата начала периода (YYYY-MM-DD)</span>
            </div>
            <div class="flex justify-between">
              <span class="font-medium">date_to</span>
              <span class="text-gray-600">Дата окончания периода (YYYY-MM-DD)</span>
            </div>
            <div class="flex justify-between">
              <span class="font-medium">page</span>
              <span class="text-gray-600">Номер страницы (по умолчанию: 1)</span>
            </div>
            <div class="flex justify-between">
              <span class="font-medium">per_page</span>
              <span class="text-gray-600">Количество элементов на странице (по умолчанию: 20)</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Параметры POST -->
      <div>
        <h4 class="font-medium text-gray-900 mb-2">Параметры POST-запроса</h4>
        <div class="bg-gray-50 rounded-lg p-4">
          <div class="space-y-2 text-sm">
            <div class="flex justify-between">
              <span class="font-medium">warehouse_ids</span>
              <span class="text-gray-600">Массив ID складов для фильтрации</span>
            </div>
            <div class="flex justify-between">
              <span class="font-medium">product_ids</span>
              <span class="text-gray-600">Массив ID товаров для фильтрации</span>
            </div>
            <div class="flex justify-between">
              <span class="font-medium">operation_types</span>
              <span class="text-gray-600">Массив типов операций</span>
            </div>
            <div class="flex justify-between">
              <span class="font-medium">date_from</span>
              <span class="text-gray-600">Дата начала периода (YYYY-MM-DD)</span>
            </div>
            <div class="flex justify-between">
              <span class="font-medium">date_to</span>
              <span class="text-gray-600">Дата окончания периода (YYYY-MM-DD)</span>
            </div>
            <div class="flex justify-between">
              <span class="font-medium">page</span>
              <span class="text-gray-600">Номер страницы (по умолчанию: 1)</span>
            </div>
            <div class="flex justify-between">
              <span class="font-medium">per_page</span>
              <span class="text-gray-600">Количество элементов на странице (по умолчанию: 20)</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Пример GET-запроса -->
      <div>
        <h4 class="font-medium text-gray-900 mb-2">Пример GET-запроса</h4>
        <div class="bg-gray-900 rounded-lg p-4 overflow-x-auto">
          <pre class="text-green-400 text-sm"><code>GET /api/balances/movements?warehouse_id=1&product_id=2&operation_type=приход&date_from=2024-01-01&date_to=2024-01-31&page=1&per_page=20

Headers:
Authorization: Bearer {token}
Accept: application/json</code></pre>
        </div>
      </div>

      <!-- Пример POST-запроса -->
      <div>
        <h4 class="font-medium text-gray-900 mb-2">Пример POST-запроса</h4>
        <div class="bg-gray-900 rounded-lg p-4 overflow-x-auto">
          <pre class="text-green-400 text-sm"><code>POST /api/balances/movements

Headers:
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json

Body:
{
  "warehouse_ids": [1],
  "product_ids": [2],
  "operation_types": ["приход", "списание"],
  "date_from": "2024-01-01",
  "date_to": "2024-01-31",
  "page": 1,
  "per_page": 20
}</code></pre>
        </div>
      </div>

      <!-- Пример ответа -->
      <div>
        <h4 class="font-medium text-gray-900 mb-2">Пример ответа</h4>
        <div class="bg-gray-900 rounded-lg p-4 overflow-x-auto">
          <pre class="text-blue-400 text-sm"><code>{
  "data": [
    {
      "id": 1,
      "date": "2024-01-10",
      "warehouse": {
        "id": 1,
        "name": "Основной склад"
      },
      "product": {
        "id": 2,
        "name": "Товар 2"
      },
      "operation_type": "приход",
      "quantity": 50,
      "before_quantity": 25,
      "after_quantity": 75,
      "price": 450.00,
      "total_value": 22500.00,
      "document": {
        "type": "receipt",
        "id": 10,
        "number": "ПР-2024-0010"
      }
    }
  ],
  "links": {
    "first": "http://api.example.com/balances/movements?page=1",
    "last": "http://api.example.com/balances/movements?page=2",
    "prev": null,
    "next": "http://api.example.com/balances/movements?page=2"
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 2,
    "per_page": 20,
    "to": 20,
    "total": 25
  }
}</code></pre>
        </div>
      </div>

      <!-- Коды ошибок -->
      <div>
        <h4 class="font-medium text-gray-900 mb-2">Коды ошибок</h4>
        <div class="space-y-2">
          <div class="flex items-center gap-2">
            <div class="w-16 text-center py-1 bg-red-100 text-blue-800 text-xs font-medium rounded">
              401
            </div>
            <span class="text-sm text-gray-700">Unauthorized - Не авторизован</span>
          </div>
          <div class="flex items-center gap-2">
            <div class="w-16 text-center py-1 bg-red-100 text-blue-800 text-xs font-medium rounded">
              422
            </div>
            <span class="text-sm text-gray-700">Validation Error - Ошибка валидации параметров</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { BarChart3 } from 'lucide-vue-next'
</script> 