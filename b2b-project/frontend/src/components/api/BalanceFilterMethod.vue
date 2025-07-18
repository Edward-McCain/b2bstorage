<template>
  <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <div class="flex items-center gap-3 mb-4">
      <div class="flex items-center justify-center w-10 h-10 bg-purple-100 rounded-lg">
        <Filter class="w-5 h-5 text-purple-600" />
      </div>
      <div>
        <h3 class="text-lg font-semibold text-gray-900">POST /balances</h3>
        <p class="text-sm text-gray-600">Фильтрация остатков товаров</p>
      </div>
    </div>

    <div class="space-y-4">
      <!-- Описание -->
      <div>
        <h4 class="font-medium text-gray-900 mb-2">Описание</h4>
        <p class="text-gray-700 text-sm">
          Позволяет выполнить расширенную фильтрацию остатков товаров с использованием сложных критериев поиска.
        </p>
      </div>

      <!-- Параметры -->
      <div>
        <h4 class="font-medium text-gray-900 mb-2">Параметры запроса</h4>
        <div class="bg-gray-50 rounded-lg p-4">
          <div class="space-y-2 text-sm">
            <div class="flex justify-between">
              <span class="font-medium">page</span>
              <span class="text-gray-600">Номер страницы (по умолчанию: 1)</span>
            </div>
            <div class="flex justify-between">
              <span class="font-medium">per_page</span>
              <span class="text-gray-600">Количество элементов на странице (по умолчанию: 20)</span>
            </div>
            <div class="flex justify-between">
              <span class="font-medium">warehouse_ids</span>
              <span class="text-gray-600">Массив ID складов для фильтрации</span>
            </div>
            <div class="flex justify-between">
              <span class="font-medium">product_ids</span>
              <span class="text-gray-600">Массив ID товаров для фильтрации</span>
            </div>
            <div class="flex justify-between">
              <span class="font-medium">category_ids</span>
              <span class="text-gray-600">Массив ID категорий товаров</span>
            </div>
            <div class="flex justify-between">
              <span class="font-medium">search</span>
              <span class="text-gray-600">Поиск по названию товара</span>
            </div>
            <div class="flex justify-between">
              <span class="font-medium">min_quantity</span>
              <span class="text-gray-600">Минимальное количество остатка</span>
            </div>
            <div class="flex justify-between">
              <span class="font-medium">max_quantity</span>
              <span class="text-gray-600">Максимальное количество остатка</span>
            </div>
            <div class="flex justify-between">
              <span class="font-medium">min_value</span>
              <span class="text-gray-600">Минимальная стоимость остатка</span>
            </div>
            <div class="flex justify-between">
              <span class="font-medium">max_value</span>
              <span class="text-gray-600">Максимальная стоимость остатка</span>
            </div>
            <div class="flex justify-between">
              <span class="font-medium">include_zero</span>
              <span class="text-gray-600">Включать товары с нулевым остатком (boolean)</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Пример запроса -->
      <div>
        <h4 class="font-medium text-gray-900 mb-2">Пример запроса</h4>
        <div class="bg-gray-900 rounded-lg p-4 overflow-x-auto">
          <pre class="text-green-400 text-sm"><code>POST /api/balances

Headers:
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json

Body:
{
  "page": 1,
  "per_page": 25,
  "warehouse_ids": [1, 2],
  "product_ids": [1, 5, 10],
  "category_ids": [5, 8],
  "search": "электроника",
  "min_quantity": 10,
  "max_quantity": 1000,
  "min_value": 1000,
  "max_value": 100000,
  "include_zero": false
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
      "product_id": 1,
      "product": {
        "id": 1,
        "name": "Товар 1",
        "sku": "T001",
        "category": {
          "id": 5,
          "name": "Электроника"
        }
      },
      "warehouse_id": 1,
      "warehouse": {
        "id": 1,
        "name": "Основной склад"
      },
      "quantity": 150,
      "reserved_quantity": 25,
      "available_quantity": 125,
      "average_price": 300.00,
      "total_value": 45000.00,
      "last_movement_date": "2024-01-20T10:30:00Z"
    }
  ],
  "links": {
    "first": "http://api.example.com/balances?page=1",
    "last": "http://api.example.com/balances?page=3",
    "prev": null,
    "next": "http://api.example.com/balances?page=2"
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 3,
    "per_page": 25,
    "to": 25,
    "total": 65
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
import { Filter } from 'lucide-vue-next'
</script> 