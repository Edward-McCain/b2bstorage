<template>
  <div class="bg-white rounded-lg shadow-sm p-6 lg:p-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
      <div class="flex items-center space-x-3">
        <div class="bg-blue-100 p-2 rounded-lg">
          <FileText class="h-5 w-5 text-blue-600" />
        </div>
        <div>
          <div class="flex items-center space-x-2">
            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-medium">POST</span>
            <span class="font-mono text-sm lg:text-base text-gray-900">/receipt-files/draft</span>
          </div>
          <h1 class="text-lg lg:text-xl font-bold text-gray-900 mt-1">Загрузка черновика файла</h1>
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
        Загружает файл в черновик без привязки к конкретному оприходованию. Файл сохраняется во временной папке 
        и может быть позже привязан к оприходованию. Поддерживаются те же форматы, что и для обычной загрузки.
      </p>
    </div>

    <!-- Request Body -->
    <div class="mb-6">
      <h2 class="text-base font-semibold text-gray-900 mb-3">Параметры запроса</h2>
      <div class="bg-gray-50 rounded-lg p-4">
        <div class="space-y-3">
          <div class="flex items-start space-x-3">
            <span class="text-sm font-medium text-gray-700 min-w-24">file</span>
            <div class="flex-1">
              <span class="text-sm text-gray-600">Файл для загрузки (обязательно)</span>
            </div>
          </div>
          <div class="flex items-start space-x-3">
            <span class="text-sm font-medium text-gray-700 min-w-24">description</span>
            <div class="flex-1">
              <span class="text-sm text-gray-600">Описание файла</span>
            </div>
          </div>
          <div class="flex items-start space-x-3">
            <span class="text-sm font-medium text-gray-700 min-w-24">temp_id</span>
            <div class="flex-1">
              <span class="text-sm text-gray-600">Временный идентификатор для группировки файлов</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Supported Formats -->
    <div class="mb-6">
      <h2 class="text-base font-semibold text-gray-900 mb-3">Поддерживаемые форматы</h2>
      <div class="bg-blue-50 rounded-lg p-4">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
          <div class="flex items-center space-x-2">
            <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
            <span class="text-sm text-blue-800">PDF</span>
          </div>
          <div class="flex items-center space-x-2">
            <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
            <span class="text-sm text-blue-800">JPG/JPEG</span>
          </div>
          <div class="flex items-center space-x-2">
            <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
            <span class="text-sm text-blue-800">PNG</span>
          </div>
          <div class="flex items-center space-x-2">
            <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
            <span class="text-sm text-blue-800">DOC/DOCX</span>
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
            <span class="text-sm font-medium text-gray-700 min-w-24">data</span>
            <div class="flex-1">
              <span class="text-sm text-gray-600">Информация о загруженном файле</span>
            </div>
          </div>
          <div class="flex items-start space-x-3">
            <span class="text-sm font-medium text-gray-700 min-w-24">id</span>
            <div class="flex-1">
              <span class="text-sm text-gray-600">Уникальный идентификатор файла</span>
            </div>
          </div>
          <div class="flex items-start space-x-3">
            <span class="text-sm font-medium text-gray-700 min-w-24">temp_id</span>
            <div class="flex-1">
              <span class="text-sm text-gray-600">Временный идентификатор</span>
            </div>
          </div>
          <div class="flex items-start space-x-3">
            <span class="text-sm font-medium text-gray-700 min-w-24">name</span>
            <div class="flex-1">
              <span class="text-sm text-gray-600">Имя файла в системе</span>
            </div>
          </div>
          <div class="flex items-start space-x-3">
            <span class="text-sm font-medium text-gray-700 min-w-24">original_name</span>
            <div class="flex-1">
              <span class="text-sm text-gray-600">Оригинальное имя файла</span>
            </div>
          </div>
          <div class="flex items-start space-x-3">
            <span class="text-sm font-medium text-gray-700 min-w-24">url</span>
            <div class="flex-1">
              <span class="text-sm text-gray-600">URL для скачивания файла</span>
            </div>
          </div>
          <div class="flex items-start space-x-3">
            <span class="text-sm font-medium text-gray-700 min-w-24">size</span>
            <div class="flex-1">
              <span class="text-sm text-gray-600">Размер файла в байтах</span>
            </div>
          </div>
          <div class="flex items-start space-x-3">
            <span class="text-sm font-medium text-gray-700 min-w-24">type</span>
            <div class="flex-1">
              <span class="text-sm text-gray-600">MIME-тип файла</span>
            </div>
          </div>
          <div class="flex items-start space-x-3">
            <span class="text-sm font-medium text-gray-700 min-w-24">status</span>
            <div class="flex-1">
              <span class="text-sm text-gray-600">Статус: draft</span>
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
        <div class="flex items-center space-x-3 p-3 bg-yellow-50 rounded-lg">
          <AlertCircle class="h-5 w-5 text-yellow-500 flex-shrink-0" />
          <div>
            <div class="text-sm font-medium text-yellow-800">422 Validation Error</div>
            <div class="text-sm text-yellow-600">Ошибка валидации файла</div>
          </div>
        </div>
        <div class="flex items-center space-x-3 p-3 bg-red-50 rounded-lg">
          <AlertCircle class="h-5 w-5 text-red-500 flex-shrink-0" />
          <div>
            <div class="text-sm font-medium text-red-800">413 Payload Too Large</div>
            <div class="text-sm text-red-600">Файл слишком большой (максимум 10MB)</div>
          </div>
        </div>
        <div class="flex items-center space-x-3 p-3 bg-red-50 rounded-lg">
          <AlertCircle class="h-5 w-5 text-red-500 flex-shrink-0" />
          <div>
            <div class="text-sm font-medium text-red-800">415 Unsupported Media Type</div>
            <div class="text-sm text-red-600">Неподдерживаемый формат файла</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Important Notes -->
    <div class="mb-6">
      <h2 class="text-base font-semibold text-gray-900 mb-3">Важные замечания</h2>
      <div class="bg-blue-50 rounded-lg p-4">
        <div class="flex items-start space-x-3">
          <AlertCircle class="h-5 w-5 text-blue-500 flex-shrink-0 mt-0.5" />
          <div class="space-y-2">
            <p class="text-sm text-blue-800">
              <strong>Черновики файлов автоматически удаляются через 24 часа</strong>
            </p>
            <p class="text-sm text-blue-700">
              Особенности черновиков:
            </p>
            <ul class="text-sm text-blue-700 list-disc list-inside ml-4 space-y-1">
              <li>Файлы сохраняются во временной папке</li>
              <li>Не привязаны к конкретному оприходованию</li>
              <li>Могут быть привязаны позже через обычную загрузку</li>
              <li>Автоматическая очистка через 24 часа</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { FileText, Copy, AlertCircle } from 'lucide-vue-next'

export default {
  name: 'ReceiptFileDraftMethod',
  components: {
    FileText,
    Copy,
    AlertCircle
  },
  data() {
    return {
      curlExample: `curl -X POST "https://api.example.com/api/receipt-files/draft" \\
  -H "Authorization: Bearer YOUR_TOKEN" \\
  -H "Accept: application/json" \\
  -F "file=@invoice.pdf" \\
  -F "description=Счет-фактура от поставщика" \\
  -F "temp_id=receipt_2024_01_15"`,
      responseExample: `{
  "success": true,
  "data": {
    "id": 1,
    "temp_id": "receipt_2024_01_15",
    "name": "invoice.pdf",
    "original_name": "invoice_2024_01_15.pdf",
    "url": "https://api.example.com/storage/temp/invoice.pdf",
    "size": 1024000,
    "type": "application/pdf",
    "description": "Счет-фактура от поставщика",
    "status": "draft",
    "created_at": "2024-01-15T10:30:00Z",
    "expires_at": "2024-01-16T10:30:00Z"
  },
  "message": "Файл успешно загружен в черновик"
}`
    }
  },
  methods: {
    async copyEndpoint() {
      const endpoint = 'POST /api/receipt-files/draft'
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