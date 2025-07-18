<template>
  <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <div class="flex items-center gap-3 mb-4">
      <div class="flex items-center justify-center w-10 h-10 bg-green-100 rounded-lg">
        <Plus class="w-5 h-5 text-green-600" />
      </div>
      <div>
        <h3 class="text-lg font-semibold text-gray-900">POST /transfers</h3>
        <p class="text-sm text-gray-600">Создание нового перемещения</p>
      </div>
    </div>

    <div class="space-y-4">
      <!-- Описание -->
      <div>
        <h4 class="font-medium text-gray-900 mb-2">Описание</h4>
        <p class="text-gray-700 text-sm">
          Создает новое перемещение товаров между складами с указанными позициями.
        </p>
      </div>

      <!-- Параметры -->
      <div>
        <h4 class="font-medium text-gray-900 mb-2">Параметры запроса</h4>
        <div class="bg-gray-50 rounded-lg p-4">
          <div class="space-y-2 text-sm">
            <div class="flex justify-between">
              <span class="font-medium">from_warehouse_id</span>
              <span class="text-gray-600">ID склада отправления (required)</span>
            </div>
            <div class="flex justify-between">
              <span class="font-medium">to_warehouse_id</span>
              <span class="text-gray-600">ID склада назначения (required)</span>
            </div>
            <div class="flex justify-between">
              <span class="font-medium">notes</span>
              <span class="text-gray-600">Примечания к перемещению (optional)</span>
            </div>
            <div class="flex justify-between">
              <span class="font-medium">positions</span>
              <span class="text-gray-600">Массив позиций товаров (required)</span>
            </div>
            <div class="flex justify-between">
              <span class="font-medium">positions[].product_id</span>
              <span class="text-gray-600">ID товара (required)</span>
            </div>
            <div class="flex justify-between">
              <span class="font-medium">positions[].quantity</span>
              <span class="text-gray-600">Количество для перемещения (required)</span>
            </div>
            <div class="flex justify-between">
              <span class="font-medium">positions[].price</span>
              <span class="text-gray-600">Цена за единицу (optional)</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Пример запроса -->
      <div>
        <h4 class="font-medium text-gray-900 mb-2">Пример запроса</h4>
        <div class="bg-gray-900 rounded-lg p-4 overflow-x-auto">
          <pre class="text-green-400 text-sm"><code>POST /api/transfers

Headers:
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json

Body:
{
  "from_warehouse_id": 1,
  "to_warehouse_id": 2,
  "notes": "Перемещение товаров на склад №2",
  "positions": [
    {
      "product_id": 1,
      "quantity": 50,
      "price": 300.00
    },
    {
      "product_id": 2,
      "quantity": 25,
      "price": 450.00
    }
  ]
}</code></pre>
        </div>
      </div>

      <!-- Пример ответа -->
      <div>
        <h4 class="font-medium text-gray-900 mb-2">Пример ответа</h4>
        <div class="bg-gray-900 rounded-lg p-4 overflow-x-auto">
          <pre class="text-blue-400 text-sm"><code>{
  "data": {
    "id": 1,
    "from_warehouse_id": 1,
    "to_warehouse_id": 2,
    "from_warehouse": {
      "id": 1,
      "name": "Основной склад"
    },
    "to_warehouse": {
      "id": 2,
      "name": "Склад №2"
    },
    "status": "draft",
    "status_text": "Черновик",
    "total_quantity": 75,
    "total_amount": 26250.00,
    "notes": "Перемещение товаров на склад №2",
    "created_at": "2024-01-20T09:15:00Z",
    "updated_at": "2024-01-20T09:15:00Z",
    "positions": [
      {
        "id": 1,
        "product_id": 1,
        "product_name": "Товар 1",
        "quantity": 50,
        "price": 300.00
      },
      {
        "id": 2,
        "product_id": 2,
        "product_name": "Товар 2",
        "quantity": 25,
        "price": 450.00
      }
    ]
  },
  "message": "Перемещение успешно создано"
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
            <span class="text-sm text-gray-700">Validation Error - Ошибка валидации данных</span>
          </div>
          <div class="flex items-center gap-2">
            <div class="w-16 text-center py-1 bg-red-100 text-red-800 text-xs font-medium rounded">
              409
            </div>
            <span class="text-sm text-gray-700">Conflict - Недостаточно товара на складе</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Plus } from 'lucide-vue-next'
</script> 