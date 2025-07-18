<template>
  <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <div class="flex items-center gap-3 mb-4">
      <div class="flex items-center justify-center w-10 h-10 bg-blue-100 rounded-lg">
        <Warehouse class="w-5 h-5 text-blue-600" />
      </div>
      <div>
        <h3 class="text-lg font-semibold text-gray-900">GET /balances/by-warehouse</h3>
        <p class="text-sm text-gray-600">Остатки товаров по складам</p>
      </div>
    </div>

    <div class="space-y-4">
      <!-- Описание -->
      <div>
        <h4 class="font-medium text-gray-900 mb-2">Описание</h4>
        <p class="text-gray-700 text-sm">
          Возвращает остатки товаров, сгруппированные по складам, с возможностью фильтрации.
        </p>
      </div>

      <!-- Параметры -->
      <div>
        <h4 class="font-medium text-gray-900 mb-2">Параметры запроса</h4>
        <div class="bg-gray-50 rounded-lg p-4">
          <div class="space-y-2 text-sm">
            <div class="flex justify-between">
              <span class="font-medium">warehouse_id</span>
              <span class="text-gray-600">ID конкретного склада (optional)</span>
            </div>
            <div class="flex justify-between">
              <span class="font-medium">category_id</span>
              <span class="text-gray-600">ID категории товаров (optional)</span>
            </div>
            <div class="flex justify-between">
              <span class="font-medium">search</span>
              <span class="text-gray-600">Поиск по названию товара (optional)</span>
            </div>
            <div class="flex justify-between">
              <span class="font-medium">min_quantity</span>
              <span class="text-gray-600">Минимальное количество остатка (optional)</span>
            </div>
            <div class="flex justify-between">
              <span class="font-medium">include_zero</span>
              <span class="text-gray-600">Включать товары с нулевым остатком (boolean, default: false)</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Пример запроса -->
      <div>
        <h4 class="font-medium text-gray-900 mb-2">Пример запроса</h4>
        <div class="bg-gray-900 rounded-lg p-4 overflow-x-auto">
          <pre class="text-green-400 text-sm"><code>GET /api/balances/by-warehouse?warehouse_id=1&category_id=5&min_quantity=10&include_zero=false

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
      "warehouse_id": 1,
      "warehouse_name": "Основной склад",
      "total_products": 100,
      "total_quantity": 15000,
      "total_value": 750000.00,
      "products": [
        {
          "product_id": 1,
          "product_name": "Товар 1",
          "sku": "T001",
          "category": {
            "id": 5,
            "name": "Электроника"
          },
          "quantity": 150,
          "reserved_quantity": 25,
          "available_quantity": 125,
          "average_price": 300.00,
          "total_value": 45000.00
        },
        {
          "product_id": 2,
          "product_name": "Товар 2",
          "sku": "T002",
          "category": {
            "id": 5,
            "name": "Электроника"
          },
          "quantity": 75,
          "reserved_quantity": 0,
          "available_quantity": 75,
          "average_price": 450.00,
          "total_value": 33750.00
        }
      ]
    },
    {
      "warehouse_id": 2,
      "warehouse_name": "Склад №2",
      "total_products": 75,
      "total_quantity": 8000,
      "total_value": 400000.00,
      "products": [
        {
          "product_id": 3,
          "product_name": "Товар 3",
          "sku": "T003",
          "category": {
            "id": 5,
            "name": "Электроника"
          },
          "quantity": 200,
          "reserved_quantity": 50,
          "available_quantity": 150,
          "average_price": 250.00,
          "total_value": 50000.00
        }
      ]
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
            <div class="w-16 text-center py-1 bg-red-100 text-red-800 text-xs font-medium rounded">
              401
            </div>
            <span class="text-sm text-gray-700">Unauthorized - Не авторизован</span>
          </div>
          <div class="flex items-center gap-2">
            <div class="w-16 text-center py-1 bg-red-100 text-red-800 text-xs font-medium rounded">
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
import { Warehouse } from 'lucide-vue-next'
</script> 