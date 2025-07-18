<template>
  <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <div class="flex items-center gap-3 mb-4">
      <div class="flex items-center justify-center w-10 h-10 bg-blue-100 rounded-lg">
        <Package class="w-5 h-5 text-blue-600" />
      </div>
      <div>
        <h3 class="text-lg font-semibold text-gray-900">GET /transfers/all-products</h3>
        <p class="text-sm text-gray-600">Получение всех товаров для перемещения</p>
      </div>
    </div>

    <div class="space-y-4">
      <!-- Описание -->
      <div>
        <h4 class="font-medium text-gray-900 mb-2">Описание</h4>
        <p class="text-gray-700 text-sm">
          Возвращает полный список всех товаров с информацией о количестве на всех складах для создания перемещения.
        </p>
      </div>

      <!-- Параметры -->
      <div>
        <h4 class="font-medium text-gray-900 mb-2">Параметры запроса</h4>
        <div class="bg-gray-50 rounded-lg p-4">
          <div class="space-y-2 text-sm">
            <div class="flex justify-between">
              <span class="font-medium">search</span>
              <span class="text-gray-600">Поиск по названию товара</span>
            </div>
            <div class="flex justify-between">
              <span class="font-medium">category_id</span>
              <span class="text-gray-600">ID категории товаров</span>
            </div>
            <div class="flex justify-between">
              <span class="font-medium">warehouse_id</span>
              <span class="text-gray-600">ID склада для фильтрации</span>
            </div>
            <div class="flex justify-between">
              <span class="font-medium">page</span>
              <span class="text-gray-600">Номер страницы (по умолчанию: 1)</span>
            </div>
            <div class="flex justify-between">
              <span class="font-medium">per_page</span>
              <span class="text-gray-600">Количество элементов на странице (по умолчанию: 50)</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Пример запроса -->
      <div>
        <h4 class="font-medium text-gray-900 mb-2">Пример запроса</h4>
        <div class="bg-gray-900 rounded-lg p-4 overflow-x-auto">
          <pre class="text-green-400 text-sm"><code>GET /api/transfers/all-products?search=товар&category_id=5&warehouse_id=1&page=1&per_page=50

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
      "id": 1,
      "name": "Товар 1",
      "sku": "T001",
      "category": {
        "id": 5,
        "name": "Электроника"
      },
      "warehouses": [
        {
          "warehouse_id": 1,
          "warehouse_name": "Основной склад",
          "quantity": 150,
          "price": 300.00
        },
        {
          "warehouse_id": 2,
          "warehouse_name": "Склад №2",
          "quantity": 75,
          "price": 320.00
        }
      ],
      "unit": "шт",
      "image_url": "https://example.com/images/product1.jpg"
    },
    {
      "id": 2,
      "name": "Товар 2",
      "sku": "T002",
      "category": {
        "id": 5,
        "name": "Электроника"
      },
      "warehouses": [
        {
          "warehouse_id": 1,
          "warehouse_name": "Основной склад",
          "quantity": 200,
          "price": 450.00
        }
      ],
      "unit": "шт",
      "image_url": "https://example.com/images/product2.jpg"
    }
  ],
  "links": {
    "first": "http://api.example.com/transfers/all-products?page=1",
    "last": "http://api.example.com/transfers/all-products?page=5",
    "prev": null,
    "next": "http://api.example.com/transfers/all-products?page=2"
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 5,
    "per_page": 50,
    "to": 50,
    "total": 250
  }
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
import { Package } from 'lucide-vue-next'
</script> 