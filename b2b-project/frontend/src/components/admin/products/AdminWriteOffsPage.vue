<template>
  <AdminLayout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Заголовок страницы -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Списания</h1>
        <p class="mt-2 text-gray-600">Административная панель для управления списаниями всех пользователей</p>
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

          <!-- Поиск по пользователю -->
          <div>
            <label class="block text-sm text-gray-700 mb-1">Поиск по пользователю</label>
            <input 
              v-model="filters.user_search" 
              type="text" 
              placeholder="Имя, email, компания, ИНН"
              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm"
              @keyup.enter="applyFilters"
            />
          </div>

          <!-- Фильтр по дате от -->
          <div>
            <label class="block text-sm text-gray-700 mb-1">Дата от</label>
            <LocalizedDatePicker 
              v-model="filters.date_from"
              :enable-time-picker="false"
              :auto-apply="true"
            />
          </div>

          <!-- Фильтр по дате до -->
          <div>
            <label class="block text-sm text-gray-700 mb-1">Дата до</label>
            <LocalizedDatePicker 
              v-model="filters.date_to"
              :enable-time-picker="false"
              :auto-apply="true"
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

      <!-- Таблица списаний -->
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
                <td class="px-3 py-2">{{ formatPrice(writeOff.total) }}</td>
                <td class="px-3 py-2">
                  <button 
                    @click="openUserModal(writeOff.user)" 
                    class="text-blue-600 hover:text-blue-800 hover:underline cursor-pointer"
                  >
                    {{ writeOff.user?.first_name || writeOff.created_by || '-' }}
                  </button>
                </td>
                <td class="px-3 py-2">
                  <div class="flex items-center space-x-2">
                    <button @click="viewWriteOff(writeOff.id)" class="text-gray-600 hover:text-gray-900 p-1 rounded hover:bg-gray-100 transition-colors cursor-pointer">
                      <Eye class="w-4 h-4" />
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

    <!-- Модальное окно пользователя -->
    <div v-if="showUserModal" class="fixed inset-0 z-50 flex items-center justify-center bg-white/90  bg-opacity-50">
      <div class="bg-white rounded-lg shadow-lg p-6 max-w-md w-full mx-4 relative">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-gray-900">Информация о пользователе</h3>
          <button @click="closeUserModal" class="text-gray-400 hover:text-gray-600">
            <X class="h-5 w-5" />
          </button>
        </div>
        
        <div v-if="selectedUser" class="space-y-3">
          <div>
            <label class="block text-sm font-medium text-gray-700">Имя пользователя</label>
            <p class="text-sm text-gray-900">{{ selectedUser.first_name }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Email</label>
            <p class="text-sm text-gray-900">{{ selectedUser.email }}</p>
          </div>
          <div v-if="selectedUser.phone_number">
            <label class="block text-sm font-medium text-gray-700">Телефон</label>
            <p class="text-sm text-gray-900">{{ selectedUser.phone_number }}</p>
          </div>
          <div v-if="selectedUser.company_name">
            <label class="block text-sm font-medium text-gray-700">Компания</label>
            <p class="text-sm text-gray-900">{{ selectedUser.company_name }}</p>
          </div>
          <div v-if="selectedUser.inn">
            <label class="block text-sm font-medium text-gray-700">ИНН</label>
            <p class="text-sm text-gray-900">{{ selectedUser.inn }}</p>
          </div>
        </div>
        
        <div class="mt-6 flex justify-end">
          <button @click="closeUserModal" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm">
            Закрыть
          </button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import AdminLayout from '../AdminLayout.vue'
import LocalizedDatePicker from '../../LocalizedDatePicker.vue'
import { apiRequest } from '@/config/api'
import { Eye, X, Loader2 } from 'lucide-vue-next'
import Multiselect from '@vueform/multiselect'
import '@vueform/multiselect/themes/default.css'

// Устанавливаем заголовок страницы
document.title = 'B2B SKLAD - Админ - Списания'

const router = useRouter()

const writeOffs = ref([])
const loading = ref(false)
const showUserModal = ref(false)
const selectedUser = ref(null)

// Фильтры
const filters = ref({
  number: '',
  user_search: '',
  date_from: '',
  date_to: '',
  warehouse: null,
  status: null
})

// Склады
const warehouses = ref([])
const loadingWarehouses = ref(false)

// Опции для фильтров
const warehouseOptions = computed(() => {
  return warehouses.value.map(w => ({
    label: w.name,
    value: w.id
  }))
})

const statusOptions = [
  { label: 'Черновик', value: 'draft' },
  { label: 'Проведено', value: 'posted' }
]

function openUserModal(user) {
  selectedUser.value = user
  showUserModal.value = true
}

function closeUserModal() {
  showUserModal.value = false
  selectedUser.value = null
}

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

function formatPrice(price) {
  if (!price) return '0 ₽'
  return new Intl.NumberFormat('ru-RU', {
    style: 'currency',
    currency: 'RUB',
    minimumFractionDigits: 0
  }).format(price)
}

async function fetchWriteOffs() {
  loading.value = true
  try {
    // Строим параметры запроса на основе фильтров
    const params = new URLSearchParams()
    
    if (filters.value.number) {
      params.append('number', filters.value.number)
    }
    if (filters.value.user_search) {
      params.append('user_search', filters.value.user_search)
    }
    if (filters.value.date_from) {
      params.append('date_from', filters.value.date_from)
    }
    if (filters.value.date_to) {
      params.append('date_to', filters.value.date_to)
    }
    if (filters.value.warehouse) {
      params.append('warehouse', filters.value.warehouse)
    }
    if (filters.value.status) {
      params.append('status', filters.value.status)
    }

    const url = params.toString() ? `/admin/write-offs?${params.toString()}` : '/admin/write-offs'
    const res = await apiRequest(url, { method: 'GET' })
    
    if (res.ok && res.data && res.data.success) {
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
  fetchWriteOffs()
}

function clearFilters() {
  filters.value = {
    number: '',
    user_search: '',
    date_from: '',
    date_to: '',
    warehouse: null,
    status: null
  }
  fetchWriteOffs()
}

function viewWriteOff(id) {
  router.push(`/admin/write-offs/${id}`)
}

onMounted(async () => {
  await Promise.all([fetchWriteOffs(), loadWarehouses()])
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