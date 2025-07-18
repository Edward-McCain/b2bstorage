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
            <span class="font-mono text-sm lg:text-base text-gray-900">/inventories</span>
          </div>
          <h1 class="text-lg lg:text-xl font-bold text-gray-900 mt-1">Получение списка инвентаризаций</h1>
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
        Возвращает список всех инвентаризаций пользователя с возможностью фильтрации и пагинации. 
        Метод поддерживает сортировку по дате создания и статусу.
      </p>
    </div>

    <!-- Parameters -->
    <div class="mb-6">
      <h2 class="text-base font-semibold text-gray-900 mb-3">Параметры запроса</h2>
      <div class="bg-gray-50 rounded-lg p-4">
        <div class="space-y-3">
          <div class="flex items-start space-x-3">
            <span class="text-sm font-medium text-gray-700 min-w-20">page</span>
            <div class="flex-1">
              <span class="text-sm text-gray-600">Номер страницы (по умолчанию: 1)</span>
            </div>
          </div>
          <div class="flex items-start space-x-3">
            <span class="text-sm font-medium text-gray-700 min-w-20">per_page</span>
            <div class="flex-1">
              <span class="text-sm text-gray-600">Количество элементов на странице (по умолчанию: 15)</span>
            </div>
          </div>
          <div class="flex items-start space-x-3">
            <span class="text-sm font-medium text-gray-700 min-w-20">status</span>
            <div class="flex-1">
              <span class="text-sm text-gray-600">Фильтр по статусу (draft, in_progress, completed, cancelled)</span>
            </div>
          </div>
          <div class="flex items-start space-x-3">
            <span class="text-sm font-medium text-gray-700 min-w-20">date_from</span>
            <div class="flex-1">
              <span class="text-sm text-gray-600">Дата начала периода (YYYY-MM-DD)</span>
            </div>
          </div>
          <div class="flex items-start space-x-3">
            <span class="text-sm font-medium text-gray-700 min-w-20">date_to</span>
            <div class="flex-1">
              <span class="text-sm text-gray-600">Дата окончания периода (YYYY-MM-DD)</span>
            </div>
          </div>
          <div class="flex items-start space-x-3">
            <span class="text-sm font-medium text-gray-700 min-w-20">warehouse_id</span>
            <div class="flex-1">
              <span class="text-sm text-gray-600">Фильтр по складу</span>
            </div>
          </div>
          <div class="flex items-start space-x-3">
            <span class="text-sm font-medium text-gray-700 min-w-20">search</span>
            <div class="flex-1">
              <span class="text-sm text-gray-600">Поиск по номеру или описанию</span>
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
            <span class="text-sm font-medium text-gray-700 min-w-24">data</span>
            <div class="flex-1">
              <span class="text-sm text-gray-600">Массив инвентаризаций</span>
            </div>
          </div>
          <div class="flex items-start space-x-3">
            <span class="text-sm font-medium text-gray-700 min-w-24">id</span>
            <div class="flex-1">
              <span class="text-sm text-gray-600">Уникальный идентификатор инвентаризации</span>
            </div>
          </div>
          <div class="flex items-start space-x-3">
            <span class="text-sm font-medium text-gray-700 min-w-24">number</span>
            <div class="flex-1">
              <span class="text-sm text-gray-600">Номер инвентаризации</span>
            </div>
          </div>
          <div class="flex items-start space-x-3">
            <span class="text-sm font-medium text-gray-700 min-w-24">date</span>
            <div class="flex-1">
              <span class="text-sm text-gray-600">Дата инвентаризации (YYYY-MM-DD)</span>
            </div>
          </div>
          <div class="flex items-start space-x-3">
            <span class="text-sm font-medium text-gray-700 min-w-24">status</span>
            <div class="flex-1">
              <span class="text-sm text-gray-600">Статус: draft, in_progress, completed, cancelled</span>
            </div>
          </div>
          <div class="flex items-start space-x-3">
            <span class="text-sm font-medium text-gray-700 min-w-24">warehouse</span>
            <div class="flex-1">
              <span class="text-sm text-gray-600">Информация о складе</span>
            </div>
          </div>
          <div class="flex items-start space-x-3">
            <span class="text-sm font-medium text-gray-700 min-w-24">description</span>
            <div class="flex-1">
              <span class="text-sm text-gray-600">Описание инвентаризации</span>
            </div>
          </div>
          <div class="flex items-start space-x-3">
            <span class="text-sm font-medium text-gray-700 min-w-24">items_count</span>
            <div class="flex-1">
              <span class="text-sm text-gray-600">Количество позиций</span>
            </div>
          </div>
          <div class="flex items-start space-x-3">
            <span class="text-sm font-medium text-gray-700 min-w-24">created_at</span>
            <div class="flex-1">
              <span class="text-sm text-gray-600">Дата создания записи</span>
            </div>
          </div>
          <div class="flex items-start space-x-3">
            <span class="text-sm font-medium text-gray-700 min-w-24">pagination</span>
            <div class="flex-1">
              <span class="text-sm text-gray-600">Информация о пагинации</span>
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
            <div class="text-sm text-yellow-600">Ошибка валидации параметров</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ClipboardList, Copy, AlertCircle } from 'lucide-vue-next'

export default {
  name: 'InventoriesMethod',
  components: {
    ClipboardList,
    Copy,
    AlertCircle
  },
  data() {
    return {
      curlExample: `curl -X GET "https://api.example.com/api/inventories?page=1&per_page=15&status=completed" \\
  -H "Authorization: Bearer YOUR_TOKEN" \\
  -H "Accept: application/json"`,
      responseExample: `{
  "data": [
    {
      "id": 1,
      "number": "INV-2024-001",
      "date": "2024-01-15",
      "status": "completed",
      "warehouse": {
        "id": 1,
        "name": "Основной склад"
      },
      "description": "Годовая инвентаризация",
      "items_count": 150,
      "total_items": 150,
      "counted_items": 148,
      "discrepancy_items": 2,
      "created_at": "2024-01-15T10:30:00Z",
      "updated_at": "2024-01-15T14:20:00Z"
    }
  ],
  "pagination": {
    "current_page": 1,
    "per_page": 15,
    "total": 8,
    "last_page": 1,
    "from": 1,
    "to": 8
  },
  "meta": {
    "total_inventories": 8,
    "completed_count": 5,
    "in_progress_count": 2,
    "draft_count": 1
  }
}`
    }
  },
  methods: {
    async copyEndpoint() {
      const endpoint = 'GET /api/inventories'
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