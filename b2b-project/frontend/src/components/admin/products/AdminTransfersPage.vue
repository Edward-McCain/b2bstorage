<template>
  <AdminLayout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Заголовок страницы -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Перемещения</h1>
        <p class="mt-2 text-gray-600">Управление перемещениями товаров между складами всех пользователей</p>
      </div>
      
      <!-- Основной контент -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-xl font-semibold text-gray-900">Список перемещений</h2>
        </div>

        <!-- Фильтры -->
        <div class="bg-gray-50 rounded-lg p-4 mb-6">
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Склад</label>
              <Multiselect
                v-model="filters.warehouse_id"
                :options="warehouseOptions"
                label="label"
                value="value"
                :object="false"
                placeholder="Выберите склад"
                :max-height="400"
                class="w-full text-sm multiselect-custom"
                :loading="loadingWarehouses"
                :disabled="loadingWarehouses"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Дата с</label>
              <input
                v-model="filters.date_from"
                type="date"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Дата по</label>
              <input
                v-model="filters.date_to"
                type="date"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Статус</label>
              <Multiselect
                v-model="filters.status"
                :options="statusOptions"
                label="label"
                value="value"
                :object="false"
                placeholder="Все статусы"
                :max-height="400"
                class="w-full text-sm multiselect-custom"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Поиск по пользователю</label>
              <input
                v-model="filters.user_search"
                type="text"
                placeholder="Имя, email, компания..."
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm"
              />
            </div>
          </div>
          <div class="mt-4 flex gap-2">
            <button
              @click="applyFilters"
              class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm transition"
              :disabled="loading"
            >
              Применить фильтры
            </button>
            <button
              @click="clearFilters"
              class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm transition"
            >
              Сбросить
            </button>
          </div>
        </div>

        <!-- Загрузка -->
        <div v-if="loading" class="flex items-center justify-center py-12">
          <Loader2 class="animate-spin h-8 w-8 text-blue-600 mr-3" />
          <span class="text-sm text-gray-600">Загрузка перемещений...</span>
        </div>

        <!-- Список перемещений -->
        <div v-else class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 tracking-wider">
                  ID
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 tracking-wider">
                  От склада
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 tracking-wider">
                  В склад
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 tracking-wider">
                  Дата
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 tracking-wider">
                  Статус
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 tracking-wider">
                  Количество товаров
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 tracking-wider">
                  Пользователь
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 tracking-wider">
                  Действия
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="transfer in transfers" :key="transfer.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                  #{{ transfer.id }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  <div>
                    <div class="font-medium">{{ transfer.from_warehouse_name }}</div>
                    <div v-if="transfer.from_warehouse_address" class="text-xs text-gray-500">
                      {{ transfer.from_warehouse_address }}
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  <div>
                    <div class="font-medium">{{ transfer.to_warehouse_name }}</div>
                    <div v-if="transfer.to_warehouse_address" class="text-xs text-gray-500">
                      {{ transfer.to_warehouse_address }}
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ formatDate(transfer.transfer_date) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span
                    :class="getStatusClass(transfer.status)"
                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                  >
                    {{ transfer.status_text }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ transfer.total_items || 0 }} ед.
                  <span v-if="transfer.actual_total_items !== null && transfer.actual_total_items !== transfer.total_items" class="text-xs text-gray-500">
                    (факт: {{ transfer.actual_total_items }})
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  <div class="relative group cursor-pointer">
                    <span class="text-blue-600 hover:text-blue-800">
                      {{ transfer.user?.full_name || transfer.created_by }}
                    </span>
                    <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-900 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-10">
                      <div v-if="transfer.user?.email">{{ transfer.user.email }}</div>
                      <div v-if="transfer.user?.company_name">{{ transfer.user.company_name }}</div>
                      <div v-if="transfer.user?.inn">ИНН: {{ transfer.user.inn }}</div>
                      <span class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></span>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <button
                    @click="viewTransfer(transfer)"
                    class="text-blue-600 hover:text-blue-900 flex items-center gap-1"
                  >
                    <Eye class="w-4 h-4" />
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
          
          <div v-if="transfers.length === 0" class="text-center text-gray-500 py-8">
            Нет перемещений
          </div>
        </div>

        <!-- Пагинация -->
        <div v-if="pagination" class="mt-6 flex justify-between items-center">
          <div class="text-sm text-gray-700">
            Показано {{ pagination.from }}-{{ pagination.to }} из {{ pagination.total }}
          </div>
          <div class="flex gap-2">
            <button
              v-if="pagination.current_page > 1"
              @click="loadTransfers(pagination.current_page - 1)"
              class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 text-sm"
            >
              Назад
            </button>
            <button
              v-if="pagination.current_page < pagination.last_page"
              @click="loadTransfers(pagination.current_page + 1)"
              class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 text-sm"
            >
              Вперед
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Модальное окно просмотра -->
    <AdminTransferViewModal
      v-if="showViewModal"
      :transfer="viewingTransfer"
      @close="showViewModal = false"
    />
  </AdminLayout>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue'
import { apiRequest } from '@/config/api'
import AdminLayout from '../AdminLayout.vue'
import AdminTransferViewModal from './AdminTransferViewModal.vue'
import Multiselect from '@vueform/multiselect'
import '@vueform/multiselect/themes/default.css'
import { Loader2, Eye } from 'lucide-vue-next'
import toastr from 'toastr'

// Устанавливаем заголовок страницы
document.title = 'B2B SKLAD - Админ - Перемещения'

const transfers = ref([])
const warehouses = ref([])
const loadingWarehouses = ref(false)
const loading = ref(false)
const showViewModal = ref(false)
const viewingTransfer = ref(null)
const pagination = ref(null)

// Фильтры
const filters = reactive({
  warehouse_id: null,
  date_from: '',
  date_to: '',
  status: null,
  user_search: ''
})

// Опции для фильтров
const warehouseOptions = computed(() => {
  return warehouses.value.map(w => ({
    label: w.name,
    value: w.id
  }))
})

const statusOptions = [
  { label: 'Черновик', value: 'draft' },
  { label: 'Подтвержден', value: 'confirmed' },
  { label: 'Выполнен', value: 'completed' },
  { label: 'Отменен', value: 'cancelled' }
]

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

function getStatusClass(status) {
  const classes = {
    draft: 'bg-gray-100 text-gray-800',
    confirmed: 'bg-blue-100 text-blue-800',
    completed: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

async function loadTransfers(page = 1) {
  loading.value = true
  try {
    // Строим параметры запроса на основе фильтров
    const params = new URLSearchParams()
    
    if (filters.warehouse_id) {
      params.append('warehouse_id', filters.warehouse_id)
    }
    if (filters.date_from) {
      params.append('date_from', filters.date_from)
    }
    if (filters.date_to) {
      params.append('date_to', filters.date_to)
    }
    if (filters.status) {
      params.append('status', filters.status)
    }
    if (filters.user_search) {
      params.append('user_search', filters.user_search)
    }
    
    params.append('page', page)

    const url = params.toString() ? `/admin/transfers?${params.toString()}` : '/admin/transfers'
    const response = await apiRequest(url, { method: 'GET' })
    
    if (response.ok && response.data.success) {
      transfers.value = response.data.data
      pagination.value = response.data.pagination
    } else {
      transfers.value = []
      pagination.value = null
      toastr.error('Ошибка загрузки перемещений')
    }
  } catch (error) {
    console.error('Ошибка загрузки перемещений:', error)
    transfers.value = []
    pagination.value = null
    toastr.error('Ошибка загрузки перемещений')
  } finally {
    loading.value = false
  }
}

async function loadWarehouses() {
  try {
    loadingWarehouses.value = true
    const response = await apiRequest('/admin/warehouses', { method: 'GET' })
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
  loadTransfers(1)
}

function clearFilters() {
  Object.assign(filters, {
    warehouse_id: null,
    date_from: '',
    date_to: '',
    status: null,
    user_search: ''
  })
  loadTransfers(1)
}

async function loadTransferDetails(transferId) {
  try {
    const response = await apiRequest(`/admin/transfers/${transferId}`, { method: 'GET' })
    if (response.ok && response.data.success) {
      viewingTransfer.value = response.data.data
    } else {
      toastr.error('Ошибка загрузки деталей перемещения')
    }
  } catch (error) {
    console.error('Ошибка загрузки деталей перемещения:', error)
    toastr.error('Ошибка загрузки деталей перемещения')
  }
}

async function viewTransfer(transfer) {
  showViewModal.value = true
  await loadTransferDetails(transfer.id)
}

onMounted(async () => {
  await Promise.all([loadTransfers(), loadWarehouses()])
})
</script>

<style scoped>
.multiselect-custom {
  --ms-option-bg-selected: #3b82f6;
  --ms-option-color-selected: #ffffff;
  --ms-option-bg-selected-pointed: #2563eb;
  --ms-option-color-selected-pointed: #ffffff;
}
</style> 