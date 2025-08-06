<template>
  <div class="min-h-screen bg-gray-50" style="padding-top: 66px;">
    <ProductsMenu />
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900">{{ t('ProductsLogs_1') }}</h1> <!-- Логи операций -->
        <div class="flex items-center gap-2">
          <button
            @click="toggleFilters"
            class="flex items-center gap-2 text-gray-700 font-medium px-4 py-2 rounded text-sm hover:bg-gray-100 transition-colors cursor-pointer"
          >
            <Filter v-if="!showFilters" class="w-4 h-4" />
            <FunnelX v-else class="w-4 h-4" />
          </button>
        </div>
      </div>

      <!-- Фильтры и поиск -->
      <div v-if="showFilters" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          <!-- Фильтр по типу операции -->
          <div>
            <label class="block text-sm text-gray-700 mb-1">{{ t('ProductsLogs_2') }}</label> <!-- Тип операции -->
            <Multiselect
              v-model="filters.operation_type"
              :options="operationTypeOptions"
              label="label"
              value="value"
              :object="false"
              :placeholder="t('ProductsLogs_3')" 
              :max-height="400"
              class="w-full text-sm multiselect-custom"
            />
          </div>

          <!-- Фильтр по складу -->
          <div>
            <label class="block text-sm text-gray-700 mb-1">{{ t('ProductsLogs_4') }}</label> <!-- Склад -->
            <Multiselect
              v-model="filters.warehouse"
              :options="warehouseOptions"
              label="label"
              value="value"
              :object="false"
              :placeholder="t('ProductsLogs_5')" 
              :max-height="400"
              class="w-full text-sm multiselect-custom"
              :loading="loadingWarehouses"
              :disabled="loadingWarehouses"
            />
          </div>

          <!-- Фильтр по дате от -->
          <div>
            <label class="block text-sm text-gray-700 mb-1">{{ t('ProductsLogs_6') }}</label> <!-- Дата от -->
            <LocalizedDatePicker 
              v-model="filters.date_from"
              :enable-time-picker="false"
              :auto-apply="true"
            />
          </div>

          <!-- Фильтр по дате до -->
          <div>
            <label class="block text-sm text-gray-700 mb-1">{{ t('ProductsLogs_7') }}</label> <!-- Дата до -->
            <LocalizedDatePicker 
              v-model="filters.date_to"
              :enable-time-picker="false"
              :auto-apply="true"
            />
          </div>

          <!-- Кнопки фильтров -->
          <div class="flex gap-2 items-end">
            <button 
              @click="applyFilters" 
              class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2 rounded-lg text-sm transition"
              :disabled="loading"
            >
              {{ t('ProductsLogs_8') }} <!-- Применить -->
            </button>
            <button 
              @click="clearFilters" 
              class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium px-4 py-2 rounded-lg text-sm transition"
            >
              {{ t('ProductsLogs_9') }} <!-- Сбросить -->
            </button>
          </div>
        </div>
      </div>

      <!-- Таблица логов -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <div v-if="loading" class="flex items-center justify-center py-8">
          <Loader2 class="animate-spin h-6 w-6 text-blue-600 mr-2" />
          <span class="text-sm text-gray-600">{{ t('ProductsLogs_10') }}</span> <!-- Загрузка логов... -->
        </div>
        <div v-else>
          <div class="mb-4 text-sm text-gray-600">
            {{ t('ProductsLogs_11') }} {{ logs.length }} <!-- Найдено записей: -->
          </div>
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead>
              <tr class="bg-gray-50">
                <th class="px-3 py-2 text-left font-semibold text-gray-700">{{ t('ProductsLogs_12') }}</th> <!-- Дата -->
                <th class="px-3 py-2 text-left font-semibold text-gray-700">{{ t('ProductsLogs_13') }}</th> <!-- Товар -->
                <th class="px-3 py-2 text-left font-semibold text-gray-700">{{ t('ProductsLogs_14') }}</th> <!-- Склад -->
                <th class="px-3 py-2 text-center font-semibold text-gray-700 min-w-[140px]">{{ t('ProductsLogs_15') }}</th> <!-- Тип операции -->
                <th class="px-3 py-2 text-center font-semibold text-gray-700">{{ t('ProductsLogs_16') }}</th> <!-- Количество -->
                <th class="px-3 py-2 text-left font-semibold text-gray-700">{{ t('ProductsLogs_17') }}</th> <!-- Пользователь -->
                <th class="px-3 py-2 text-left font-semibold text-gray-700">{{ t('ProductsLogs_18') }}</th> <!-- Примечания -->
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
                <td class="px-3 py-2 text-center whitespace-nowrap">
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
          </div>
          <div v-if="logs.length === 0" class="text-center text-gray-500 py-8">{{ t('ProductsLogs_19') }}</div> <!-- Логи не найдены -->
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import ProductsMenu from './ProductsMenu.vue'
import LocalizedDatePicker from '../LocalizedDatePicker.vue'
import { apiRequest } from '@/config/api'
import { Loader2, Filter, FunnelX } from 'lucide-vue-next'
import Multiselect from '@vueform/multiselect'
import '@vueform/multiselect/themes/default.css'
import { t } from '@/locales'

// Устанавливаем заголовок страницы
document.title = 'B2B SKLAD - Логи операций'

const logs = ref([])
const loading = ref(false)
const loadingWarehouses = ref(false)
const warehouses = ref([])
const showFilters = ref(false)

const filters = ref({
  operation_type: null,
  warehouse: null,
  date_from: '',
  date_to: ''
})

const operationTypeOptions = [
  // { label: t('ProductsLogs_20'), value: 'income' }, // Приход
  // { label: t('ProductsLogs_21'), value: 'expense' }, // Расход
  { label: t('ProductsLogs_22'), value: 'receipt' }, // Оприходование
  { label: t('ProductsLogs_23'), value: 'write_off' }, // Списание
  { label: t('ProductsLogs_24'), value: 'transfer_in' }, // Вх. перемещение
  { label: t('ProductsLogs_25'), value: 'transfer_out' } // Исх. перемещение
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
    'income': t('ProductsLogs_20'), // Приход
    'expense': t('ProductsLogs_21'), // Расход
    'receipt': t('ProductsLogs_22'), // Оприходование
    'write_off': t('ProductsLogs_23'), // Списание
    'transfer_in': t('ProductsLogs_24'), // Вх. перемещение
    'transfer_out': t('ProductsLogs_25') // Исх. перемещение
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
    'receipt': t('ProductsLogs_22'), // Оприходование
    'write_off': t('ProductsLogs_23'), // Списание
    'transfer': t('ProductsLogs_26'), // Перемещение
    'inventory': t('ProductsLogs_27') // Инвентаризация
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
    console.error(t('ProductsLogs_28') + error) // Ошибка загрузки логов:
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
    console.error(t('ProductsLogs_29') + error) // Ошибка загрузки складов:
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

function toggleFilters() {
  showFilters.value = !showFilters.value
}

onMounted(async () => {
  await Promise.all([fetchLogs(), loadWarehouses()])
})
</script> 