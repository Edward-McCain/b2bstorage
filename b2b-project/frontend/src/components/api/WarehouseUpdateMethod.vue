<template>
  <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <div class="flex items-center gap-3 mb-4">
      <div class="flex items-center justify-center w-10 h-10 bg-yellow-100 rounded-lg">
        <Edit class="w-5 h-5 text-yellow-600" />
      </div>
      <div>
        <h3 class="text-lg font-semibold text-gray-900">PUT /warehouses/{id}</h3>
        <p class="text-sm text-gray-600">Обновление информации о складе</p>
      </div>
    </div>

    <div class="space-y-4">
      <!-- Описание -->
      <div>
        <h4 class="font-medium text-gray-900 mb-2">Описание</h4>
        <p class="text-gray-700 text-sm">
          Обновляет информацию о существующем складе по его ID.
        </p>
      </div>

      <!-- Параметры -->
      <div>
        <h4 class="font-medium text-gray-900 mb-2">Параметры</h4>
        <div class="bg-gray-50 rounded-lg p-4">
          <div class="space-y-2 text-sm">
            <div class="flex justify-between">
              <span class="font-medium">id</span>
              <span class="text-gray-600">ID склада (integer, path parameter)</span>
            </div>
            <div class="flex justify-between">
              <span class="font-medium">name</span>
              <span class="text-gray-600">Название склада (string, optional)</span>
            </div>
            <div class="flex justify-between">
              <span class="font-medium">address</span>
              <span class="text-gray-600">Адрес склада (string, optional)</span>
            </div>
            <div class="flex justify-between">
              <span class="font-medium">description</span>
              <span class="text-gray-600">Описание склада (string, optional)</span>
            </div>
            <div class="flex justify-between">
              <span class="font-medium">is_active</span>
              <span class="text-gray-600">Активность склада (boolean, optional)</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Пример запроса -->
      <div>
        <h4 class="font-medium text-gray-900 mb-2">Пример запроса</h4>
        <div class="bg-gray-900 rounded-lg p-4 overflow-x-auto">
          <pre class="text-green-400 text-sm"><code>PUT /api/warehouses/1

Headers:
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json

Body:
{
  "name": "Обновленный склад",
  "address": "ул. Ленина, 1А",
  "description": "Обновленное описание склада",
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
    "id": 1,
    "name": "Обновленный склад",
    "address": "ул. Ленина, 1А",
    "description": "Обновленное описание склада",
    "is_active": true,
    "created_at": "2024-01-15T10:30:00Z",
    "updated_at": "2024-01-20T14:45:00Z"
  },
  "message": "Склад успешно обновлен"
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
              404
            </div>
            <span class="text-sm text-gray-700">Not Found - Склад не найден</span>
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
import { Edit } from 'lucide-vue-next'
</script> 