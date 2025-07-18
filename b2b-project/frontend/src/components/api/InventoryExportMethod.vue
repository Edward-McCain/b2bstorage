<template>
  <div class="bg-white rounded-lg shadow-sm p-6 lg:p-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
      <div class="flex items-center space-x-3">
        <div class="bg-green-100 p-2 rounded-lg">
          <ClipboardList class="h-5 w-5 text-green-600" />
        </div>
        <div>
          <div class="flex items-center space-x-2">
            <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-medium">GET</span>
            <span class="font-mono text-sm lg:text-base text-gray-900">/inventories/{id}/export</span>
          </div>
          <h1 class="text-lg lg:text-xl font-bold text-gray-900 mt-1">Экспорт инвентаризации</h1>
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
        Экспортирует инвентаризацию в формате Excel или PDF. Включает все позиции товаров, 
        статистику и информацию о складе. Доступно для завершенных инвентаризаций.
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
              <span class="text-sm text-gray-600">Уникальный идентификатор инвентаризации (обязательно)</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Query Parameters -->
    <div class="mb-6">
      <h2 class="text-base font-semibold text-gray-900 mb-3">Параметры запроса</h2>
      <div class="bg-gray-50 rounded-lg p-4">
        <div class="space-y-3">
          <div class="flex items-start space-x-3">
            <span class="text-sm font-medium text-gray-700 min-w-20">format</span>
            <div class="flex-1">
              <span class="text-sm text-gray-600">Формат экспорта (excel, pdf) (по умолчанию: excel)</span>
            </div>
          </div>
          <div class="flex items-start space-x-3">
            <span class="text-sm font-medium text-gray-700 min-w-20">include_details</span>
            <div class="flex-1">
              <span class="text-sm text-gray-600">Включить детальную информацию (true/false) (по умолчанию: true)</span>
            </div>
          </div>
          <div class="flex items-start space-x-3">
            <span class="text-sm font-medium text-gray-700 min-w-20">language</span>
            <div class="flex-1">
              <span class="text-sm text-gray-600">Язык отчета (ru, en, uz) (по умолчанию: ru)</span>
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
          <span class="text-sm text-gray-400">File Download</span>
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
            <span class="text-sm font-medium text-gray-700 min-w-24">Content-Type</span>
            <div class="flex-1">
              <span class="text-sm text-gray-600">application/vnd.openxmlformats-officedocument.spreadsheetml.sheet (Excel) или application/pdf</span>
            </div>
          </div>
          <div class="flex items-start space-x-3">
            <span class="text-sm font-medium text-gray-700 min-w-24">Content-Disposition</span>
            <div class="flex-1">
              <span class="text-sm text-gray-600">attachment; filename="inventory_2024_001.xlsx"</span>
            </div>
          </div>
          <div class="flex items-start space-x-3">
            <span class="text-sm font-medium text-gray-700 min-w-24">Content-Length</span>
            <div class="flex-1">
              <span class="text-sm text-gray-600">Размер файла в байтах</span>
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
            <div class="text-sm text-red-600">Инвентаризация не найдена</div>
          </div>
        </div>
        <div class="flex items-center space-x-3 p-3 bg-red-50 rounded-lg">
          <AlertCircle class="h-5 w-5 text-red-500 flex-shrink-0" />
          <div>
            <div class="text-sm font-medium text-red-800">403 Forbidden</div>
            <div class="text-sm text-red-600">Нет доступа к инвентаризации</div>
          </div>
        </div>
        <div class="flex items-center space-x-3 p-3 bg-yellow-50 rounded-lg">
          <AlertCircle class="h-5 w-5 text-yellow-500 flex-shrink-0" />
          <div>
            <div class="text-sm font-medium text-yellow-800">400 Bad Request</div>
            <div class="text-sm text-yellow-600">Инвентаризация не завершена</div>
          </div>
        </div>
        <div class="flex items-center space-x-3 p-3 bg-red-50 rounded-lg">
          <AlertCircle class="h-5 w-5 text-red-500 flex-shrink-0" />
          <div>
            <div class="text-sm font-medium text-red-800">500 Internal Server Error</div>
            <div class="text-sm text-red-600">Ошибка генерации файла</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ClipboardList, Copy, AlertCircle } from 'lucide-vue-next'

export default {
  name: 'InventoryExportMethod',
  components: {
    ClipboardList,
    Copy,
    AlertCircle
  },
  data() {
    return {
      curlExample: `curl -X GET "https://api.example.com/api/inventories/1/export?format=excel&include_details=true&language=ru" \\
  -H "Authorization: Bearer YOUR_TOKEN" \\
  -H "Accept: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" \\
  --output "inventory_2024_001.xlsx"`,
      responseExample: `HTTP/1.1 200 OK
Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet
Content-Disposition: attachment; filename="inventory_2024_001.xlsx"
Content-Length: 24576

[Binary file content - Excel file with inventory data]`
    }
  },
  methods: {
    async copyEndpoint() {
      const endpoint = 'GET /api/inventories/{id}/export'
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