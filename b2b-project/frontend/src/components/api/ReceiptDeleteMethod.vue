<template>
  <div class="bg-white rounded-lg shadow-sm p-6 lg:p-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
      <div class="flex items-center space-x-3">
        <div class="bg-red-100 p-2 rounded-lg">
          <Receipt class="h-5 w-5 text-red-600" />
        </div>
        <div>
          <div class="flex items-center space-x-2">
            <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs font-medium">DELETE</span>
            <span class="font-mono text-sm lg:text-base text-gray-900">/receipts/{id}</span>
          </div>
          <h1 class="text-lg lg:text-xl font-bold text-gray-900 mt-1">Удаление оприходования</h1>
        </div>
      </div>
      <button
        @click="copyEndpoint"
        class="flex items-center space-x-2 text-gray-500 hover:text-gray-700 transition-colors"
      >
        <Copy class="h-4 w-4" />
        <span class="text-sm">Копировать</span>
      </button>
    </div>

    <!-- Description -->
    <div class="mb-6">
      <h2 class="text-base font-semibold text-gray-900 mb-3">Описание</h2>
      <p class="text-sm lg:text-base text-gray-600 leading-relaxed">
        Удаляет оприходование по его уникальному идентификатору. Удаление возможно только для оприходований 
        в статусе "draft". При удалении также удаляются все связанные позиции товаров и файлы.
      </p>
    </div>

    <!-- Parameters -->
    <div class="mb-6">
      <h2 class="text-base font-semibold text-gray-900 mb-3">Параметры пути</h2>
      <div class="bg-gray-50 rounded-lg p-4">
        <div class="space-y-3">
          <div class="flex items-start space-x-3">
            <span class="text-sm font-medium text-gray-700 min-w-20">id</span>
            <div class="flex-1">
              <span class="text-sm text-gray-600">Уникальный идентификатор оприходования (обязательно)</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Example Request -->
    <div class="mb-6">
      <h2 class="text-base font-semibold text-gray-900 mb-3">Пример запроса</h2>
      <div class="bg-gray-900 rounded-lg p-4">
        <div class="flex items-center justify-between mb-2">
          <span class="text-sm text-gray-400">cURL</span>
          <button
            @click="copyCode('curl')"
            class="text-gray-400 hover:text-white transition-colors"
          >
            <Copy class="h-4 w-4" />
          </button>
        </div>
        <pre class="text-sm text-gray-300 overflow-x-auto"><code>{{ curlExample }}</code></pre>
      </div>
    </div>

    <!-- Example Response -->
    <div class="mb-6">
      <h2 class="text-base font-semibold text-gray-900 mb-3">Пример ответа</h2>
      <div class="bg-gray-900 rounded-lg p-4">
        <div class="flex items-center justify-between mb-2">
          <span class="text-sm text-gray-400">JSON</span>
          <button
            @click="copyCode('response')"
            class="text-gray-400 hover:text-white transition-colors"
          >
            <Copy class="h-4 w-4" />
          </button>
        </div>
        <pre class="text-sm text-gray-300 overflow-x-auto"><code>{{ responseExample }}</code></pre>
      </div>
    </div>

    <!-- Response Fields -->
    <div class="mb-6">
      <h2 class="text-base font-semibold text-gray-900 mb-3">Поля ответа</h2>
      <div class="bg-gray-50 rounded-lg p-4">
        <div class="space-y-3">
          <div class="flex items-start space-x-3">
            <span class="text-sm font-medium text-gray-700 min-w-24">success</span>
            <div class="flex-1">
              <span class="text-sm text-gray-600">Статус операции (true/false)</span>
            </div>
          </div>
          <div class="flex items-start space-x-3">
            <span class="text-sm font-medium text-gray-700 min-w-24">message</span>
            <div class="flex-1">
              <span class="text-sm text-gray-600">Сообщение об успешном удалении</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Error Codes -->
    <div class="mb-6">
      <h2 class="text-base font-semibold text-gray-900 mb-3">Коды ошибок</h2>
      <div class="space-y-3">
        <div class="flex items-center space-x-3 p-3 bg-red-50 rounded-lg">
          <AlertCircle class="h-5 w-5 text-red-500 flex-shrink-0" />
          <div>
            <div class="text-sm font-medium text-red-800">401 Unauthorized</div>
            <div class="text-sm text-red-600">Не авторизован</div>
          </div>
        </div>
        <div class="flex items-center space-x-3 p-3 bg-red-50 rounded-lg">
          <AlertCircle class="h-5 w-5 text-red-500 flex-shrink-0" />
          <div>
            <div class="text-sm font-medium text-red-800">404 Not Found</div>
            <div class="text-sm text-red-600">Оприходование не найдено</div>
          </div>
        </div>
        <div class="flex items-center space-x-3 p-3 bg-red-50 rounded-lg">
          <AlertCircle class="h-5 w-5 text-red-500 flex-shrink-0" />
          <div>
            <div class="text-sm font-medium text-red-800">403 Forbidden</div>
            <div class="text-sm text-red-600">Нет доступа к оприходованию</div>
          </div>
        </div>
        <div class="flex items-center space-x-3 p-3 bg-red-50 rounded-lg">
          <AlertCircle class="h-5 w-5 text-red-500 flex-shrink-0" />
          <div>
            <div class="text-sm font-medium text-red-800">400 Bad Request</div>
            <div class="text-sm text-red-600">Оприходование не может быть удалено</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Important Notes -->
    <div class="mb-6">
      <h2 class="text-base font-semibold text-gray-900 mb-3">Важные замечания</h2>
      <div class="bg-yellow-50 rounded-lg p-4">
        <div class="flex items-start space-x-3">
          <AlertCircle class="h-5 w-5 text-yellow-500 flex-shrink-0 mt-0.5" />
          <div class="space-y-2">
            <p class="text-sm text-yellow-800">
              <strong>Удаление возможно только для оприходований в статусе "draft"</strong>
            </p>
            <p class="text-sm text-yellow-700">
              При удалении оприходования также удаляются:
            </p>
            <ul class="text-sm text-yellow-700 list-disc list-inside ml-4 space-y-1">
              <li>Все позиции товаров</li>
              <li>Прикрепленные файлы</li>
              <li>Связанные записи в базе данных</li>
            </ul>
            <p class="text-sm text-yellow-700 mt-2">
              <strong>Внимание:</strong> Операция необратима!
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Receipt, Copy, AlertCircle } from 'lucide-vue-next'

export default {
  name: 'ReceiptDeleteMethod',
  components: {
    Receipt,
    Copy,
    AlertCircle
  },
  data() {
    return {
      curlExample: `curl -X DELETE "https://api.example.com/api/receipts/1" \\
  -H "Authorization: Bearer YOUR_TOKEN" \\
  -H "Accept: application/json"`,
      responseExample: `{
  "success": true,
  "message": "Оприходование успешно удалено"
}`
    }
  },
  methods: {
    async copyEndpoint() {
      const endpoint = 'DELETE /api/receipts/{id}'
      try {
        await navigator.clipboard.writeText(endpoint)
      } catch (err) {
        console.error('Failed to copy endpoint: ', err)
      }
    },
    async copyCode(type) {
      const text = type === 'curl' ? this.curlExample : this.responseExample
      try {
        await navigator.clipboard.writeText(text)
      } catch (err) {
        console.error('Failed to copy code: ', err)
      }
    }
  }
}
</script> 