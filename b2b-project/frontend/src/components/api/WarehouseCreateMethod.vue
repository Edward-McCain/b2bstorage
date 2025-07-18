<template>
  <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <div class="flex items-center gap-3 mb-4">
      <div class="flex items-center justify-center w-10 h-10 bg-green-100 rounded-lg">
        <Plus class="w-5 h-5 text-green-600" />
      </div>
      <div>
        <h3 class="text-lg font-semibold text-gray-900">POST /warehouses</h3>
        <p class="text-sm text-gray-600">Создание нового склада</p>
      </div>
    </div>

    <div class="space-y-4">
      <!-- Описание -->
      <div>
        <h4 class="font-medium text-gray-900 mb-2">Описание</h4>
        <p class="text-gray-700 text-sm">
          Создает новый склад с указанными параметрами.
        </p>
      </div>

      <!-- Параметры -->
      <div>
        <h4 class="font-medium text-gray-900 mb-2">Параметры запроса</h4>
        <div class="bg-gray-50 rounded-lg p-4">
          <div class="space-y-2 text-sm">
            <div class="flex justify-between">
              <span class="font-medium">name</span>
              <span class="text-gray-600">Название склада (string, required)</span>
            </div>
            <div class="flex justify-between">
              <span class="font-medium">address</span>
              <span class="text-gray-600">Адрес склада (string, required)</span>
            </div>
            <div class="flex justify-between">
              <span class="font-medium">description</span>
              <span class="text-gray-600">Описание склада (string, optional)</span>
            </div>
            <div class="flex justify-between">
              <span class="font-medium">is_active</span>
              <span class="text-gray-600">Активность склада (boolean, default: true)</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Пример запроса -->
      <div>
        <h4 class="font-medium text-gray-900 mb-2">Пример запроса</h4>
        <div class="bg-gray-900 rounded-lg p-4 overflow-x-auto">
          <pre class="text-green-400 text-sm"><code>POST /api/warehouses

Headers:
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json

Body:
{
  "name": "Новый склад",
  "address": "ул. Гагарина, 25",
  "description": "Склад для хранения товаров",
  "is_active": true
}</code></pre>
        </div>
      </div>

      <!-- Пример ответа -->
      <div>
        <h4 class="font-medium text-gray-900 mb-2">Пример ответа</h4>
        <div class="bg-gray-900 rounded-lg p-4 overflow-x-auto">
          <pre class="text-blue-400 text-sm"><code>{
  "data": {
    "id": 3,
    "name": "Новый склад",
    "address": "ул. Гагарина, 25",
    "description": "Склад для хранения товаров",
    "is_active": true,
    "created_at": "2024-01-20T09:15:00Z",
    "updated_at": "2024-01-20T09:15:00Z"
  },
  "message": "Склад успешно создан"
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
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Plus } from 'lucide-vue-next'
</script> 