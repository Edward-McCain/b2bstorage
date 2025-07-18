<template>
  <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <div class="flex items-center gap-3 mb-4">
      <div class="flex items-center justify-center w-10 h-10 bg-purple-100 rounded-lg">
        <Filter class="w-5 h-5 text-purple-600" />
      </div>
      <div>
        <h3 class="text-lg font-semibold text-gray-900">POST /transfers/filter</h3>
        <p class="text-sm text-gray-600">Фильтрация перемещений</p>
      </div>
    </div>

    <div class="space-y-4">
      <!-- Описание -->
      <div>
        <h4 class="font-medium text-gray-900 mb-2">Описание</h4>
        <p class="text-gray-700 text-sm">
          Позволяет выполнить расширенную фильтрацию перемещений с использованием сложных критериев поиска.
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
              <span class="text-gray-600">Количество элементов на странице (по умолчанию: 15)</span>
            </div>
            <div class="flex justify-between">
              <span class="font-medium">status</span>
              <span class="text-gray-600">Статус перемещения (draft, confirmed, completed, cancelled)</span>
            </div>
            <div class="flex justify-between">
              <span class="font-medium">from_warehouse_id</span>
              <span class="text-gray-600">ID склада отправления</span>
            </div>
            <div class="flex justify-between">
              <span class="font-medium">to_warehouse_id</span>
              <span class="text-gray-600">ID склада назначения</span>
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
              <span class="font-medium">product_id</span>
              <span class="text-gray-600">ID товара для фильтрации</span>
            </div>
            <div class="flex justify-between">
              <span class="font-medium">min_amount</span>
              <span class="text-gray-600">Минимальная сумма перемещения</span>
            </div>
            <div class="flex justify-between">
              <span class="font-medium">max_amount</span>
              <span class="text-gray-600">Максимальная сумма перемещения</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Пример запроса -->
      <div>
        <h4 class="font-medium text-gray-900 mb-2">Пример запроса</h4>
        <div class="bg-gray-900 rounded-lg p-4 overflow-x-auto">
          <pre class="text-green-400 text-sm"><code>POST /api/transfers/filter

Headers:
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json

Body:
{
  "page": 1,
  "per_page": 20,
  "status": "confirmed",
  "from_warehouse_id": 1,
  "to_warehouse_id": 2,
  "date_from": "2024-01-01",
  "date_to": "2024-01-31",
  "product_id": 5,
  "min_amount": 1000,
  "max_amount": 50000
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
      "status": "confirmed",
      "status_text": "Подтверждено",
      "total_quantity": 150,
      "total_amount": 45000.00,
      "notes": "Перемещение товаров",
      "created_at": "2024-01-15T10:30:00Z",
      "updated_at": "2024-01-15T14:20:00Z"
    }
  ],
  "links": {
    "first": "http://api.example.com/transfers/filter?page=1",
    "last": "http://api.example.com/transfers/filter?page=2",
    "prev": null,
    "next": "http://api.example.com/transfers/filter?page=2"
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 2,
    "per_page": 20,
    "to": 20,
    "total": 35
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