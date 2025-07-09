<template>
  <div class="min-h-screen bg-gray-50" style="padding-top: 66px;">
    <ProductsMenu />
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Списания</h1>
        <router-link
          to="/products/write-offs/create"
          class="flex items-center gap-2 bg-blue-50 border border-blue-200 text-blue-700 font-medium px-4 py-2 rounded text-sm hover:bg-blue-100 transition-colors"
        >
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          Добавить
        </router-link>
      </div>

      <!-- Фильтры и поиск -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          <!-- Поиск по номеру -->
          <div>
            <label class="block text-sm text-gray-700 mb-1">Поиск по номеру</label>
            <input 
              v-model="filters.number" 
              type="text" 
              placeholder="Введите номер списания"
              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm"
              @keyup.enter="applyFilters"
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

          <!-- Фильтр по статусу -->
          <div>
            <label class="block text-sm text-gray-700 mb-1">Статус</label>
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
      <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <div v-if="loading" class="flex items-center justify-center py-8">
          <Loader2 class="animate-spin h-6 w-6 text-blue-600 mr-2" />
          <span class="text-sm text-gray-600">Загрузка списаний...</span>
        </div>
        <div v-else>
          <div class="mb-4 text-sm text-gray-600">
            Найдено записей: {{ writeOffs.length }}
          </div>
          <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead>
              <tr class="bg-gray-50">
                <th class="px-3 py-2 text-left font-semibold text-gray-700">№</th>
                <th class="px-3 py-2 text-left font-semibold text-gray-700">Дата</th>
                <th class="px-3 py-2 text-left font-semibold text-gray-700">Организация</th>
                <th class="px-3 py-2 text-left font-semibold text-gray-700">Склад</th>
                <th class="px-3 py-2 text-left font-semibold text-gray-700">Статус</th>
                <th class="px-3 py-2 text-left font-semibold text-gray-700">Сумма</th>
                <th class="px-3 py-2 text-left font-semibold text-gray-700">Пользователь</th>
                <th class="px-3 py-2 text-left font-semibold text-gray-700">Действия</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="writeOff in writeOffs" :key="writeOff.id" class="hover:bg-gray-50">
                <td class="px-3 py-2">{{ writeOff.number }}</td>
                <td class="px-3 py-2">{{ formatDate(writeOff.date) }}</td>
                <td class="px-3 py-2">{{ writeOff.organization }}</td>
                <td class="px-3 py-2">
                  <span v-if="writeOff.warehouse_name && writeOff.warehouse_name.length > 0" class="relative group cursor-pointer">
                    {{ writeOff.warehouse_name }}
                    <span v-if="writeOff.warehouse_address && writeOff.warehouse_address.length > 0" class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-900 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-10">
                      {{ writeOff.warehouse_address }}
                      <span class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></span>
                    </span>
                  </span>
                  <span v-else class="text-gray-400 cursor-pointer">Склад #{{ writeOff.warehouse_id }}</span>
                </td>
                <td class="px-3 py-2">
                  <span :class="writeOff.status === 'posted' ? 'text-green-600' : 'text-gray-500'">
                    {{ writeOff.status === 'posted' ? 'Проведено' : 'Черновик' }}
                  </span>
                </td>
                <td class="px-3 py-2">{{ Number(writeOff.total).toFixed(2) }}</td>
                <td class="px-3 py-2">{{ writeOff.created_by || '-' }}</td>
                <td class="px-3 py-2">
                  <div class="flex items-center space-x-2">
                    <button @click="viewWriteOff(writeOff.id)" class="text-gray-600 hover:text-gray-900 p-1 rounded hover:bg-gray-100 transition-colors cursor-pointer">
                      <Eye class="w-4 h-4" />
                    </button>
                    <router-link 
                      :to="`/products/write-offs/edit/${writeOff.id}`"
                      class="text-blue-600 hover:text-blue-800 p-1 rounded hover:bg-blue-50 transition-colors cursor-pointer"
                    >
                      <Edit class="w-4 h-4" />
                    </router-link>
                    <button @click="openDeleteModal(writeOff.id)" class="text-red-600 hover:text-red-800 p-1 rounded hover:bg-red-50 transition-colors cursor-pointer">
                      <Trash2 class="w-4 h-4" />
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
          <div v-if="writeOffs.length === 0" class="text-center text-gray-500 py-8">Нет списаний</div>
        </div>
      </div>
    </div>
  </div>
  <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-white/90">
    <div class="bg-white rounded-lg shadow-lg p-6 max-w-sm w-full relative">
      <div class="text-lg font-semibold mb-2">Удалить списание?</div>
      <div class="text-gray-600 mb-4 text-sm">Вы действительно хотите удалить это списание? Это действие необратимо.</div>
      <div class="flex justify-end gap-2 mt-4">
        <button @click="closeDeleteModal" class="px-4 py-2 rounded bg-gray-100 text-gray-700 hover:bg-gray-200 text-sm">Отмена</button>
        <button @click="deleteWriteOffConfirmed" :disabled="deleting" class="px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700 text-sm flex items-center min-w-[90px] justify-center">
          <Loader2 v-if="deleting" class="animate-spin h-4 w-4 mr-2" />
          <span v-if="!deleting">Удалить</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import ProductsMenu from './ProductsMenu.vue'
import { apiRequest } from '@/config/api'
import { useRouter } from 'vue-router'
import { Eye, Edit, Trash2, Loader2 } from 'lucide-vue-next'
import toastr from 'toastr'
import Multiselect from '@vueform/multiselect'
import '@vueform/multiselect/themes/default.css'

// Устанавливаем заголовок страницы
document.title = 'B2B SKLAD - Списания'

const router = useRouter()

const writeOffs = ref([])
const loading = ref(false)
const showDeleteModal = ref(false)
const deleting = ref(false)
const deleteTarget = ref(null)
const loadingWarehouses = ref(false)
const warehouses = ref([])

const filters = ref({
  number: '',
  date_from: '',
  date_to: '',
  warehouse: null,
  status: null
})

const statusOptions = [
  { label: 'Черновик', value: 'draft' },
  { label: 'Проведено', value: 'posted' }
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
  return new Date(date).toLocaleDateString('ru-RU')
}

async function fetchWriteOffs() {
  loading.value = true
  try {
    const params = new URLSearchParams()
    
    if (filters.value.number) params.append('number', filters.value.number)
    if (filters.value.date_from) params.append('date_from', filters.value.date_from)
    if (filters.value.date_to) params.append('date_to', filters.value.date_to)
    if (filters.value.warehouse) params.append('warehouse', filters.value.warehouse)
    if (filters.value.status) params.append('status', filters.value.status)

    const url = params.toString() ? `/write-offs?${params.toString()}` : '/write-offs'
    const res = await apiRequest(url, { method: 'GET' })
    
    if (res.ok && res.data.success) {
      writeOffs.value = Array.isArray(res.data.data) ? res.data.data : []
    } else {
      writeOffs.value = []
    }
  } catch (error) {
    console.error('Ошибка загрузки списаний:', error)
    writeOffs.value = []
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
  fetchWriteOffs()
}

function clearFilters() {
  filters.value = {
    number: '',
    date_from: '',
    date_to: '',
    warehouse: null,
    status: null
  }
  fetchWriteOffs()
}

function viewWriteOff(id) {
  router.push(`/products/write-offs/${id}`)
}

function createWriteOff() {
  router.push('/products/write-offs/create')
}

function openDeleteModal(id) {
  deleteTarget.value = id
  showDeleteModal.value = true
}

function closeDeleteModal() {
  showDeleteModal.value = false
  deleteTarget.value = null
}

async function deleteWriteOffConfirmed() {
  if (!deleteTarget.value) return
  
  deleting.value = true
  try {
    const res = await apiRequest(`/write-offs/${deleteTarget.value}`, { method: 'DELETE' })
    if (res.ok && res.data && res.data.success) {
      writeOffs.value = writeOffs.value.filter(w => w.id !== deleteTarget.value)
      toastr.success('Списание успешно удалено')
      closeDeleteModal()
    } else {
      toastr.error(res.data?.message || 'Ошибка при удалении списания')
    }
  } catch (e) {
    toastr.error('Ошибка при удалении списания')
  } finally {
    deleting.value = false
  }
}

onMounted(async () => {
  await Promise.all([fetchWriteOffs(), loadWarehouses()])
})
</script> 