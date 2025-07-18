<template>
  <div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <!-- Header -->
    <div class="px-6 py-4 border-b border-gray-200">
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-3">
          <div class="flex items-center space-x-2">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
              GET
            </span>
            <span class="text-sm font-mono text-gray-600">/admin/subcategories</span>
          </div>
        </div>
        <div class="flex items-center space-x-2">
          <span class="text-sm text-gray-500">Администратор</span>
          <Shield class="h-4 w-4 text-gray-400" />
        </div>
      </div>
    </div>

    <!-- Content -->
    <div class="p-6 space-y-6">
      <!-- Description -->
      <div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Список подкатегорий</h3>
        <p class="text-gray-600">
          Получение списка всех подкатегорий товаров в системе с возможностью фильтрации и пагинации. 
          Метод доступен только администраторам системы.
        </p>
      </div>

      <!-- Request -->
      <div>
        <h4 class="text-sm font-medium text-gray-900 mb-3 flex items-center">
          <Send class="h-4 w-4 mr-2" />
          Запрос
        </h4>
        <div class="bg-gray-50 rounded-lg p-4">
          <div class="flex items-center justify-between mb-3">
            <span class="text-sm font-medium text-gray-700">Headers:</span>
            <button @click="copyToClipboard(headers)" class="text-blue-600 hover:text-blue-700">
              <Copy class="h-4 w-4" />
            </button>
          </div>
          <pre class="text-sm text-gray-800 overflow-x-auto"><code>{{ headers }}</code></pre>
        </div>
        
        <div class="bg-gray-50 rounded-lg p-4 mt-3">
          <div class="flex items-center justify-between mb-3">
            <span class="text-sm font-medium text-gray-700">Query Parameters:</span>
            <button @click="copyToClipboard(queryParams)" class="text-blue-600 hover:text-blue-700">
              <Copy class="h-4 w-4" />
            </button>
          </div>
          <pre class="text-sm text-gray-800 overflow-x-auto"><code>{{ queryParams }}</code></pre>
        </div>
      </div>

      <!-- Response -->
      <div>
        <h4 class="text-sm font-medium text-gray-900 mb-3 flex items-center">
          <CheckCircle class="h-4 w-4 mr-2" />
          Ответ
        </h4>
        <div class="bg-green-50 rounded-lg p-4">
          <div class="flex items-center justify-between mb-3">
            <span class="text-sm font-medium text-gray-700">Success (200):</span>
            <button @click="copyToClipboard(successResponse)" class="text-blue-600 hover:text-blue-700">
              <Copy class="h-4 w-4" />
            </button>
          </div>
          <pre class="text-sm text-gray-800 overflow-x-auto"><code>{{ successResponse }}</code></pre>
        </div>
      </div>

      <!-- Parameters -->
      <div>
        <h4 class="text-sm font-medium text-gray-900 mb-3">Параметры запроса</h4>
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Параметр</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Тип</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Обязательный</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Описание</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr>
                <td class="px-4 py-3 text-sm font-mono text-gray-900">category_id</td>
                <td class="px-4 py-3 text-sm text-gray-600">integer</td>
                <td class="px-4 py-3 text-sm text-gray-600">Нет</td>
                <td class="px-4 py-3 text-sm text-gray-600">ID категории для фильтрации</td>
              </tr>
              <tr>
                <td class="px-4 py-3 text-sm font-mono text-gray-900">search</td>
                <td class="px-4 py-3 text-sm text-gray-600">string</td>
                <td class="px-4 py-3 text-sm text-gray-600">Нет</td>
                <td class="px-4 py-3 text-sm text-gray-600">Поиск по названию подкатегории</td>
              </tr>
              <tr>
                <td class="px-4 py-3 text-sm font-mono text-gray-900">sort_by</td>
                <td class="px-4 py-3 text-sm text-gray-600">string</td>
                <td class="px-4 py-3 text-sm text-gray-600">Нет</td>
                <td class="px-4 py-3 text-sm text-gray-600">Поле для сортировки (name, created_at)</td>
              </tr>
              <tr>
                <td class="px-4 py-3 text-sm font-mono text-gray-900">sort_order</td>
                <td class="px-4 py-3 text-sm text-gray-600">string</td>
                <td class="px-4 py-3 text-sm text-gray-600">Нет</td>
                <td class="px-4 py-3 text-sm text-gray-600">Порядок сортировки (asc, desc)</td>
              </tr>
              <tr>
                <td class="px-4 py-3 text-sm font-mono text-gray-900">page</td>
                <td class="px-4 py-3 text-sm text-gray-600">integer</td>
                <td class="px-4 py-3 text-sm text-gray-600">Нет</td>
                <td class="px-4 py-3 text-sm text-gray-600">Номер страницы (по умолчанию: 1)</td>
              </tr>
              <tr>
                <td class="px-4 py-3 text-sm font-mono text-gray-900">per_page</td>
                <td class="px-4 py-3 text-sm text-gray-600">integer</td>
                <td class="px-4 py-3 text-sm text-gray-600">Нет</td>
                <td class="px-4 py-3 text-sm text-gray-600">Количество элементов на странице (по умолчанию: 20)</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Errors -->
      <div>
        <h4 class="text-sm font-medium text-gray-900 mb-3 flex items-center">
          <AlertCircle class="h-4 w-4 mr-2" />
          Ошибки
        </h4>
        <div class="space-y-3">
          <div class="bg-red-50 rounded-lg p-4">
            <div class="flex items-center justify-between mb-2">
              <span class="text-sm font-medium text-red-800">401 Unauthorized</span>
              <button @click="copyToClipboard(unauthorizedError)" class="text-red-600 hover:text-red-700">
                <Copy class="h-4 w-4" />
              </button>
            </div>
            <p class="text-sm text-red-700 mb-2">Не авторизован или отсутствуют права администратора</p>
            <pre class="text-sm text-red-800 overflow-x-auto"><code>{{ unauthorizedError }}</code></pre>
          </div>
          
          <div class="bg-red-50 rounded-lg p-4">
            <div class="flex items-center justify-between mb-2">
              <span class="text-sm font-medium text-red-800">422 Validation Error</span>
              <button @click="copyToClipboard(validationError)" class="text-red-600 hover:text-red-700">
                <Copy class="h-4 w-4" />
              </button>
            </div>
            <p class="text-sm text-red-700 mb-2">Ошибка валидации параметров</p>
            <pre class="text-sm text-red-800 overflow-x-auto"><code>{{ validationError }}</code></pre>
          </div>
        </div>
      </div>

      <!-- Examples -->
      <div>
        <h4 class="text-sm font-medium text-gray-900 mb-3">Примеры использования</h4>
        <div class="space-y-4">
          <div class="bg-blue-50 rounded-lg p-4">
            <h5 class="text-sm font-medium text-blue-900 mb-2">Получение всех подкатегорий</h5>
            <pre class="text-sm text-blue-800 overflow-x-auto"><code>{{ getAllSubcategoriesExample }}</code></pre>
          </div>
          
          <div class="bg-blue-50 rounded-lg p-4">
            <h5 class="text-sm font-medium text-blue-900 mb-2">Фильтр по категории</h5>
            <pre class="text-sm text-blue-800 overflow-x-auto"><code>{{ filterByCategoryExample }}</code></pre>
          </div>
          
          <div class="bg-blue-50 rounded-lg p-4">
            <h5 class="text-sm font-medium text-blue-900 mb-2">Поиск подкатегорий</h5>
            <pre class="text-sm text-blue-800 overflow-x-auto"><code>{{ searchSubcategoriesExample }}</code></pre>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Send, CheckCircle, AlertCircle, Copy, Shield } from 'lucide-vue-next'

export default {
  name: 'AdminSubcategoriesMethod',
  components: {
    Send,
    CheckCircle,
    AlertCircle,
    Copy,
    Shield
  },
  data() {
    return {
      headers: `{
  "Authorization": "Bearer {token}",
  "Accept": "application/json"
}`,
      queryParams: `{
  "category_id": 1,
  "search": "ноутбук",
  "sort_by": "name",
  "sort_order": "asc",
  "page": 1,
  "per_page": 20
}`,
      successResponse: `{
  "success": true,
  "data": {
    "subcategories": [
      {
        "id": 1,
        "name": "Ноутбуки",
        "description": "Портативные компьютеры для работы и учебы",
        "category_id": 1,
        "category_name": "Электроника",
        "products_count": 15,
        "created_at": "2024-01-15T10:30:00Z",
        "updated_at": "2024-01-15T10:30:00Z"
      },
      {
        "id": 2,
        "name": "Планшеты",
        "description": "Планшетные компьютеры",
        "category_id": 1,
        "category_name": "Электроника",
        "products_count": 8,
        "created_at": "2024-01-15T10:30:00Z",
        "updated_at": "2024-01-15T10:30:00Z"
      }
    ],
    "pagination": {
      "current_page": 1,
      "per_page": 20,
      "total": 2,
      "last_page": 1
    },
    "summary": {
      "total_subcategories": 2,
      "total_products": 23
    }
  }
}`,
      unauthorizedError: `{
  "success": false,
  "message": "Unauthorized",
  "error": "Требуется авторизация администратора"
}`,
      validationError: `{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "category_id": ["Категория не найдена"],
    "sort_by": ["Неверное поле для сортировки"]
  }
}`,
      getAllSubcategoriesExample: `GET /api/admin/subcategories
Authorization: Bearer {admin_token}`,
      filterByCategoryExample: `GET /api/admin/subcategories?category_id=1&sort_by=name&sort_order=asc
Authorization: Bearer {admin_token}`,
      searchSubcategoriesExample: `GET /api/admin/subcategories?search=ноутбук&per_page=10
Authorization: Bearer {admin_token}`
    }
  },
  methods: {
    copyToClipboard(text) {
      navigator.clipboard.writeText(text).then(() => {
        // Можно добавить уведомление об успешном копировании
      })
    }
  }
}
</script> 