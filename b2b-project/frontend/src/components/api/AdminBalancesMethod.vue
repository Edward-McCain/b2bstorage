<template>
  <div class="bg-white rounded-lg shadow-sm p-6 lg:p-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
      <div class="flex items-center space-x-3">
        <div class="flex items-center space-x-2">
          <div class="px-3 py-1 bg-green-100 text-green-800 rounded-md text-sm font-medium">GET</div>
          <span class="font-mono text-lg text-gray-900">/admin/balances</span>
        </div>
      </div>
      <div class="flex items-center space-x-2">
        <div class="w-2 h-2 bg-red-500 rounded-full"></div>
        <span class="text-sm text-gray-600">Админ</span>
      </div>
    </div>

    <!-- Title -->
    <div class="mb-8">
      <h1 class="text-lg lg:text-xl font-semibold text-gray-900">Список остатков (админ)</h1>
    </div>

    <!-- Description -->
    <div class="mb-8">
      <p class="text-gray-700 leading-relaxed">
        Получает список всех остатков товаров в системе с расширенной информацией для администраторов. Включает данные о товарах, складах, пользователях и финансовую статистику.
      </p>
    </div>

    <!-- Query Parameters -->
    <div class="mb-8">
      <h3 class="text-lg font-semibold text-gray-900 mb-4">Параметры запроса</h3>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Параметр</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Тип</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Обязательный</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Описание</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">page</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">integer</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">Нет</span>
              </td>
              <td class="px-6 py-4 text-sm text-gray-500">Номер страницы (по умолчанию 1)</td>
            </tr>
            <tr>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">per_page</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">integer</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">Нет</span>
              </td>
              <td class="px-6 py-4 text-sm text-gray-500">Количество записей на странице (по умолчанию 15)</td>
            </tr>
            <tr>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">search</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">string</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">Нет</span>
              </td>
              <td class="px-6 py-4 text-sm text-gray-500">Поиск по названию товара или артикулу</td>
            </tr>
            <tr>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">product_id</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">integer</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">Нет</span>
              </td>
              <td class="px-6 py-4 text-sm text-gray-500">Фильтр по товару</td>
            </tr>
            <tr>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">warehouse_id</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">integer</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">Нет</span>
              </td>
              <td class="px-6 py-4 text-sm text-gray-500">Фильтр по складу</td>
            </tr>
            <tr>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">user_id</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">integer</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">Нет</span>
              </td>
              <td class="px-6 py-4 text-sm text-gray-500">Фильтр по владельцу товара</td>
            </tr>
            <tr>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">category_id</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">integer</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">Нет</span>
              </td>
              <td class="px-6 py-4 text-sm text-gray-500">Фильтр по категории товара</td>
            </tr>
            <tr>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">quantity_min</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">integer</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">Нет</span>
              </td>
              <td class="px-6 py-4 text-sm text-gray-500">Минимальное количество остатка</td>
            </tr>
            <tr>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">quantity_max</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">integer</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">Нет</span>
              </td>
              <td class="px-6 py-4 text-sm text-gray-500">Максимальное количество остатка</td>
            </tr>
            <tr>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">sort</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">string</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">Нет</span>
              </td>
              <td class="px-6 py-4 text-sm text-gray-500">Сортировка: quantity, value, product_name, warehouse_name</td>
            </tr>
            <tr>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">include_stats</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">boolean</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">Нет</span>
              </td>
              <td class="px-6 py-4 text-sm text-gray-500">Включить статистику по остаткам</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Response -->
    <div class="mb-8">
      <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
        <CheckCircle class="h-5 w-5 mr-2" />
        Ответ
      </h3>
      <div class="bg-gray-50 rounded-lg p-4">
        <div class="flex items-center justify-between mb-2">
          <span class="text-sm font-medium text-gray-700">JSON (200 OK)</span>
          <button
            @click="copyToClipboard(responseBody)"
            class="flex items-center space-x-1 text-blue-600 hover:text-blue-700 text-sm"
          >
            <Copy class="h-4 w-4" />
            <span>Копировать</span>
          </button>
        </div>
        <pre class="text-sm text-gray-800 overflow-x-auto"><code>{{ responseBody }}</code></pre>
      </div>
    </div>

    <!-- Errors -->
    <div class="mb-8">
      <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
        <AlertCircle class="h-5 w-5 mr-2" />
        Возможные ошибки
      </h3>
      <div class="space-y-4">
        <div class="border border-red-200 rounded-lg p-4 bg-red-50">
          <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium text-red-800">401 Unauthorized</span>
            <span class="text-xs text-red-600">Авторизация</span>
          </div>
          <p class="text-sm text-red-700">
            Не авторизован. Требуется токен доступа.
          </p>
        </div>
        <div class="border border-red-200 rounded-lg p-4 bg-red-50">
          <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium text-red-800">403 Forbidden</span>
            <span class="text-xs text-red-600">Доступ</span>
          </div>
          <p class="text-sm text-red-700">
            Доступ запрещен. Требуются права администратора.
          </p>
        </div>
        <div class="border border-red-200 rounded-lg p-4 bg-red-50">
          <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium text-red-800">400 Bad Request</span>
            <span class="text-xs text-red-600">Валидация</span>
          </div>
          <p class="text-sm text-red-700">
            Неверные параметры запроса. Проверьте типы данных и допустимые значения.
          </p>
        </div>
        <div class="border border-red-200 rounded-lg p-4 bg-red-50">
          <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium text-red-800">500 Internal Server Error</span>
            <span class="text-xs text-red-600">Сервер</span>
          </div>
          <p class="text-sm text-red-700">
            Внутренняя ошибка сервера. Попробуйте позже.
          </p>
        </div>
      </div>
    </div>

    <!-- Example -->
    <div class="mb-8">
      <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
        <Send class="h-5 w-5 mr-2" />
        Пример использования
      </h3>
      <div class="bg-gray-50 rounded-lg p-4">
        <div class="flex items-center justify-between mb-2">
          <span class="text-sm font-medium text-gray-700">cURL</span>
          <button
            @click="copyToClipboard(curlExample)"
            class="flex items-center space-x-1 text-blue-600 hover:text-blue-700 text-sm"
          >
            <Copy class="h-4 w-4" />
            <span>Копировать</span>
          </button>
        </div>
        <pre class="text-sm text-gray-800 overflow-x-auto"><code>{{ curlExample }}</code></pre>
      </div>
    </div>
  </div>
</template>

<script>
import { CheckCircle, AlertCircle, Copy, Send } from 'lucide-vue-next'

export default {
  name: 'AdminBalancesMethod',
  components: {
    CheckCircle,
    AlertCircle,
    Copy,
    Send
  },
  data() {
    return {
      responseBody: `{
  "success": true,
  "data": [
    {
      "id": 1,
      "product_id": 1,
      "product": {
        "id": 1,
        "name": "Смартфон iPhone 15 Pro",
        "article": "IPHONE15PRO",
        "price": 89990.00,
        "currency": "RUB",
        "category_id": 1,
        "category": {
          "id": 1,
          "name": "Электроника",
          "slug": "electronics"
        },
        "user_id": 1,
        "user": {
          "id": 1,
          "name": "Иван Петров",
          "email": "ivan.petrov@example.com"
        }
      },
      "warehouse_id": 1,
      "warehouse": {
        "id": 1,
        "name": "Основной склад",
        "address": "г. Москва, ул. Примерная, д. 1"
      },
      "quantity": 48,
      "reserved_quantity": 5,
      "available_quantity": 43,
      "total_value": 4319520.00,
      "available_value": 3879570.00,
      "last_operation_at": "2024-01-20T14:25:00.000000Z",
      "created_at": "2024-01-15T10:30:00.000000Z",
      "updated_at": "2024-01-20T14:25:00.000000Z",
      "movements": {
        "total_in": 55,
        "total_out": 7,
        "last_in": "2024-01-20T10:30:00.000000Z",
        "last_out": "2024-01-19T16:45:00.000000Z"
      }
    },
    {
      "id": 2,
      "product_id": 2,
      "product": {
        "id": 2,
        "name": "Ноутбук MacBook Air M2",
        "article": "MBAIRM2",
        "price": 129990.00,
        "currency": "RUB",
        "category_id": 1,
        "category": {
          "id": 1,
          "name": "Электроника",
          "slug": "electronics"
        },
        "user_id": 2,
        "user": {
          "id": 2,
          "name": "Мария Сидорова",
          "email": "maria.sidorova@example.com"
        }
      },
      "warehouse_id": 1,
      "warehouse": {
        "id": 1,
        "name": "Основной склад",
        "address": "г. Москва, ул. Примерная, д. 1"
      },
      "quantity": 25,
      "reserved_quantity": 2,
      "available_quantity": 23,
      "total_value": 3249750.00,
      "available_value": 2989770.00,
      "last_operation_at": "2024-01-19T09:15:00.000000Z",
      "created_at": "2024-01-16T11:45:00.000000Z",
      "updated_at": "2024-01-19T09:15:00.000000Z",
      "movements": {
        "total_in": 27,
        "total_out": 2,
        "last_in": "2024-01-19T09:15:00.000000Z",
        "last_out": "2024-01-18T14:30:00.000000Z"
      }
    },
    {
      "id": 3,
      "product_id": 3,
      "product": {
        "id": 3,
        "name": "Бумага А4",
        "article": "PAPER_A4",
        "price": 750.00,
        "currency": "RUB",
        "category_id": 2,
        "category": {
          "id": 2,
          "name": "Канцелярия",
          "slug": "stationery"
        },
        "user_id": 2,
        "user": {
          "id": 2,
          "name": "Мария Сидорова",
          "email": "maria.sidorova@example.com"
        }
      },
      "warehouse_id": 2,
      "warehouse": {
        "id": 2,
        "name": "Склад №2",
        "address": "г. Санкт-Петербург, ул. Другая, д. 5"
      },
      "quantity": 995,
      "reserved_quantity": 0,
      "available_quantity": 995,
      "total_value": 746250.00,
      "available_value": 746250.00,
      "last_operation_at": "2024-01-18T11:45:00.000000Z",
      "created_at": "2024-01-18T11:45:00.000000Z",
      "updated_at": "2024-01-18T11:45:00.000000Z",
      "movements": {
        "total_in": 1000,
        "total_out": 5,
        "last_in": "2024-01-18T11:45:00.000000Z",
        "last_out": "2024-01-17T10:20:00.000000Z"
      }
    }
  ],
  "meta": {
    "total": 3,
    "per_page": 15,
    "current_page": 1,
    "last_page": 1,
    "from": 1,
    "to": 3
  },
  "stats": {
    "total_products": 1250,
    "total_warehouses": 5,
    "total_quantity": 56700,
    "total_value": 156789000.00,
    "avg_quantity_per_product": 45.36,
    "avg_value_per_product": 125431.20,
    "low_stock_products": 45,
    "out_of_stock_products": 12,
    "by_category": {
      "electronics": 450,
      "stationery": 350,
      "clothing": 250,
      "other": 200
    },
    "by_warehouse": {
      "Основной склад": 850,
      "Склад №2": 400
    }
  }
}`,
      curlExample: `curl -X GET "https://api.example.com/api/admin/balances?page=1&per_page=15&warehouse_id=1&quantity_min=10&include_stats=true" \\
  -H "Accept: application/json" \\
  -H "Authorization: Bearer YOUR_ADMIN_TOKEN"`
    }
  },
  methods: {
    async copyToClipboard(text) {
      try {
        await navigator.clipboard.writeText(text)
        // Можно добавить уведомление об успешном копировании
      } catch (err) {
        console.error('Failed to copy text: ', err)
      }
    }
  }
}
</script> 