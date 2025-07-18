<template>
  <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <div class="flex items-center gap-3 mb-4">
      <div class="flex items-center justify-center w-10 h-10 bg-red-100 rounded-lg">
        <Trash2 class="w-5 h-5 text-red-600" />
      </div>
      <div>
        <h3 class="text-lg font-semibold text-gray-900">DELETE /transfers/{id}</h3>
        <p class="text-sm text-gray-600">Удаление перемещения</p>
      </div>
    </div>

    <div class="space-y-4">
      <!-- Описание -->
      <div>
        <h4 class="font-medium text-gray-900 mb-2">Описание</h4>
        <p class="text-gray-700 text-sm">
          Удаляет перемещение по его ID. Доступно только для черновиков.
        </p>
      </div>

      <!-- Параметры -->
      <div>
        <h4 class="font-medium text-gray-900 mb-2">Параметры пути</h4>
        <div class="bg-gray-50 rounded-lg p-4">
          <div class="space-y-2 text-sm">
            <div class="flex justify-between">
              <span class="font-medium">id</span>
              <span class="text-gray-600">ID перемещения (integer)</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Пример запроса -->
      <div>
        <h4 class="font-medium text-gray-900 mb-2">Пример запроса</h4>
        <div class="bg-gray-900 rounded-lg p-4 overflow-x-auto">
          <pre class="text-green-400 text-sm"><code>DELETE /api/transfers/1

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
  "message": "Перемещение успешно удалено"
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
            <span class="text-sm text-gray-700">Not Found - Перемещение не найдено</span>
          </div>
          <div class="flex items-center gap-2">
            <div class="w-16 text-center py-1 bg-red-100 text-red-800 text-xs font-medium rounded">
              409
            </div>
            <span class="text-sm text-gray-700">Conflict - Невозможно удалить подтвержденное перемещение</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Trash2 } from 'lucide-vue-next'
</script> 