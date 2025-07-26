<template>
  <div class="min-h-screen bg-gray-50" style="padding-top: 66px;">
    <ProductsMenu />
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Логи операций</h1>
      </div>

      <!-- Фильтры и поиск -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          <!-- Фильтр по типу операции -->
          <div>
            <label class="block text-sm text-gray-700 mb-1">Тип операции</label>
            <Multiselect
              v-model="filters.operation_type"
              :options="operationTypeOptions"
              label="label"
              value="value"
              :object="false"
              placeholder="Все типы"
              :max-height="400"
              class="w-full text-sm multiselect-custom"
            />
          </div>

          <!-- Фильтр по складу -->
          <div>
            <label class="block text-sm text-gray-700 mb-1">Склад</label>
            <Multiselect
              v-model="filters.warehouse"
              :options="warehouseOptions"
              label="label"
              value="value"
              :object="false"
              placeholder="Все склады"
              :max-height="400"
              class="w-full text-sm multiselect-custom"
              :loading="loadingWarehouses"
              :disabled="loadingWarehouses"
            />
          </div>

          <!-- Фильтр по дате от -->
          <div>
            <label class="block text-sm text-gray-700 mb-1">Дата от</label>
            <input 
              v-model="filters.date_from" 
              type="date" 
              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm"
            />
          </div>

          <!-- Фильтр по дате до -->
          <div>
            <label class="block text-sm text-gray-700 mb-1">Дата до</label>
            <input 
              v-model="filters.date_to" 
              type="date" 
              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm"
            />
          </div>

          <!-- Кнопки фильтров -->
          <div class="flex gap-2 items-end">
            <button 
              @click="applyFilters" 
              class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2 rounded-lg text-sm transition"
              :disabled="loading"
            >
              Применить
            </button>
            <button 
              @click="clearFilters" 
              class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium px-4 py-2 rounded-lg text-sm transition"
            >
              Сбросить
            </button>
          </div>
        </div>
      </div>

      <!-- Таблица логов -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <div v-if="loading" class="flex items-center justify-center py-8">
          <Loader2 class="animate-spin h-6 w-6 text-blue-600 mr-2" />
          <span class="text-sm text-gray-600">Загрузка логов...</span>
        </div>
        <div v-else>
          <div class="mb-4 text-sm text-gray-600">
            Найдено записей: {{ logs.length }}
          </div>
          <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead>
              <tr class="bg-gray-50">
                <th class="px-3 py-2 text-left font-semibold text-gray-700">Дата</th>
                <th class="px-3 py-2 text-left font-semibold text-gray-700">Товар</th>
                <th class="px-3 py-2 text-left font-semibold text-gray-700">Склад</th>
                <th class="px-3 py-2 text-center font-semibold text-gray-700">Тип операции</th>
                <th class="px-3 py-2 text-center font-semibold text-gray-700">Количество</th>
                <th class="px-3 py-2 text-left font-semibold text-gray-700">Пользователь</th>
                <th class="px-3 py-2 text-left font-semibold text-gray-700">Примечания</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="log in logs" :key="log.id" class="hover:bg-gray-50">
                <td class="px-3 py-2">{{ formatDate(log.created_at) }}</td>
                <td class="px-3 py-2">
                  <div class="font-medium">{{ log.product_name }}</div>
                  <div class="text-xs text-gray-500">{{ log.product_code }}</div>
                </td>
                <td class="px-3 py-2">{{ log.warehouse_name }}</td>
                <td class="px-3 py-2 text-center">
                  <span :class="`px-2 py-1 rounded-full text-xs font-medium ${getOperationTypeClass(log.operation_type)}`">
                    {{ getOperationTypeText(log.operation_type) }}
                  </span>
                </td>
                <td class="px-3 py-2 text-center">
                  <span :class="getQuantityClass(log.operation_type)">
                    {{ formatQuantity(log.quantity) }}
                  </span>
                </td>
                <td class="px-3 py-2">{{ log.created_by_name || '-' }}</td>
                <td class="px-3 py-2">
                  <span v-if="log.notes" class="text-sm text-gray-600">{{ log.notes }}</span>
                  <span v-else class="text-gray-400">—</span>
                </td>
              </tr>
            </tbody>
          </table>
          <div v-if="logs.length === 0" class="text-center text-gray-500 py-8">Логи не найдены</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import ProductsMenu from './ProductsMenu.vue'
import { apiRequest } from '@/config/api'
import { Loader2 } from 'lucide-vue-next'
import Multiselect from '@vueform/multiselect'
import '@vueform/multiselect/themes/default.css'

// Устанавливаем заголовок страницы
document.title = 'B2B SKLAD - Логи операций'

const logs = ref([])
const loading = ref(false)
const loadingWarehouses = ref(false)
const warehouses = ref([])

const filters = ref({
  operation_type: null,
  warehouse: null,
  date_from: '',
  date_to: ''
})

const operationTypeOptions = [
  // { label: 'Приход', value: 'income' },
  // { label: 'Расход', value: 'expense' },
  { label: 'Оприходование', value: 'receipt' },
  { label: 'Списание', value: 'write_off' },
  { label: 'Перемещение (входящее)', value: 'transfer_in' },
  { label: 'Перемещение (исходящее)', value: 'transfer_out' }
]

const warehouseOptions = computed(() => {
  if (!Array.isArray(warehouses.value)) return []
  return warehouses.value.map(w => ({
    label: w.name,
    value: w.id
  }))
})

function formatDate(date) {
  if (!date) return ''
  return new Date(date).toLocaleString('ru-RU', {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit',
    hour: '2-digit',
    minute: '2-digit'
  })
}

function formatQuantity(quantity) {
  if (!quantity) return '0'
  return parseFloat(quantity)
}

function getOperationTypeText(type) {
  const types = {
    'income': 'Приход',
    'expense': 'Расход',
    'receipt': 'Оприходование',
    'write_off': 'Списание',
    'transfer_in': 'Перемещение (входящее)',
    'transfer_out': 'Перемещение (исходящее)'
  }
  return types[type] || type
}

function getOperationTypeClass(type) {
  const classes = {
    'income': 'bg-green-100 text-green-800',
    'expense': 'bg-red-100 text-red-800',
    'receipt': 'bg-green-100 text-green-800',
    'write_off': 'bg-red-100 text-red-800',
    'transfer_in': 'bg-blue-100 text-blue-800',
    'transfer_out': 'bg-blue-100 text-blue-800'
  }
  return classes[type] || 'bg-gray-100 text-gray-800'
}

function getQuantityClass(type) {
  const classes = {
    'income': 'text-green-600 font-medium',
    'expense': 'text-red-600 font-medium',
    'receipt': 'text-green-600 font-medium',
    'write_off': 'text-red-600 font-medium',
    'transfer_in': 'text-blue-600 font-medium',
    'transfer_out': 'text-blue-600 font-medium'
  }
  return classes[type] || 'text-gray-600'
}

function getReferenceTypeText(type) {
  const types = {
    'receipt': 'Оприходование',
    'write_off': 'Списание',
    'transfer': 'Перемещение',
    'inventory': 'Инвентаризация'
  }
  return types[type] || type
}

async function fetchLogs() {
  loading.value = true
  try {
    const params = new URLSearchParams()
    
    if (filters.value.operation_type) params.append('operation_type', filters.value.operation_type)
    if (filters.value.warehouse) params.append('warehouse', filters.value.warehouse)
    if (filters.value.date_from) params.append('date_from', filters.value.date_from)
    if (filters.value.date_to) params.append('date_to', filters.value.date_to)

    const url = params.toString() ? `/product-operations?${params.toString()}` : '/product-operations'
    const res = await apiRequest(url, { method: 'GET' })
    
    if (res.ok && res.data.success) {
      logs.value = Array.isArray(res.data.data) ? res.data.data : []
    } else {
      logs.value = []
    }
  } catch (error) {
    console.error('Ошибка загрузки логов:', error)
    logs.value = []
  } finally {
    loading.value = false
  }
}

async function loadWarehouses() {
  try {
    loadingWarehouses.value = true
    const response = await apiRequest('/warehouses', { method: 'GET' })
    if (response.ok && response.data.success) {
      warehouses.value = response.data.data || []
    } else {
      warehouses.value = []
    }
  } catch (error) {
    console.error('Ошибка загрузки складов:', error)
    warehouses.value = []
  } finally {
    loadingWarehouses.value = false
  }
}

function applyFilters() {
  fetchLogs()
}

function clearFilters() {
  filters.value = {
    operation_type: null,
    warehouse: null,
    date_from: '',
    date_to: ''
  }
  fetchLogs()
}

onMounted(async () => {
  await Promise.all([fetchLogs(), loadWarehouses()])
})
</script> 