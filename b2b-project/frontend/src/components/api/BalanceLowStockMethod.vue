<template>
  <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <div class="flex items-center gap-3 mb-4">
      <div class="flex items-center justify-center w-10 h-10 bg-yellow-100 rounded-lg">
        <AlertCircle class="w-5 h-5 text-yellow-600" />
      </div>
      <div>
        <h3 class="text-lg font-semibold text-gray-900">GET /balances/low-stock</h3>
        <p class="text-sm text-gray-600">Товары с низким остатком</p>
      </div>
    </div>

    <div class="space-y-4">
      <!-- Описание -->
      <div>
        <h4 class="font-medium text-gray-900 mb-2">Описание</h4>
        <p class="text-gray-700 text-sm">
          Возвращает список товаров, у которых остаток ниже минимального порога. Используется для контроля дефицита на складах.
        </p>
      </div>

      <!-- Параметры -->
      <div>
        <h4 class="font-medium text-gray-900 mb-2">Параметры запроса</h4>
        <div class="bg-gray-50 rounded-lg p-4">
          <div class="space-y-2 text-sm">
            <div class="flex justify-between">
              <span class="font-medium">warehouse_id</span>
              <span class="text-gray-600">ID склада для фильтрации (optional)</span>
            </div>
            <div class="flex justify-between">
              <span class="font-medium">category_id</span>
              <span class="text-gray-600">ID категории товаров (optional)</span>
            </div>
            <div class="flex justify-between">
              <span class="font-medium">search</span>
              <span class="text-gray-600">Поиск по названию товара (optional)</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Пример запроса -->
      <div>
        <h4 class="font-medium text-gray-900 mb-2">Пример запроса</h4>
        <div class="bg-gray-900 rounded-lg p-4 overflow-x-auto">
          <pre class="text-green-400 text-sm"><code>GET /api/balances/low-stock?warehouse_id=1&category_id=5

Headers:
Authorization: Bearer {token}
Accept: application/json</code></pre>
        </div>
      </div>

      <!-- Пример ответа -->
      <div>
        <h4 class="font-medium text-gray-900 mb-2">Пример ответа</h4>
        <div class="bg-gray-900 rounded-lg p-4 overflow-x-auto">
          <pre class="text-blue-400 text-sm"><code>{
  "data": [
    {
      "product_id": 1,
      "product_name": "Товар 1",
      "sku": "T001",
      "category": {
        "id": 5,
        "name": "Электроника"
      },
      "warehouse_id": 1,
      "warehouse_name": "Основной склад",
      "quantity": 5,
      "min_quantity": 10,
      "average_price": 300.00,
      "total_value": 1500.00
    }
  ]
}</code></pre>
        </div>
      </div>

      <!-- Коды ошибок -->
      <div>
        <h4 class="font-medium text-gray-900 mb-2">Коды ошибок</h4>
        <div class="space-y-2">
          <div class="flex items-center gap-2">
            <div class="w-16 text-center py-1 bg-red-100 text-yellow-800 text-xs font-medium rounded">
              401
            </div>
            <span class="text-sm text-gray-700">Unauthorized - Не авторизован</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { AlertCircle } from 'lucide-vue-next'
</script> 