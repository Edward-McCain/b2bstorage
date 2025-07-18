<template>
  <AdminLayout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Заголовок страницы -->
      <div class="mb-8">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ inventory?.name }}</h1>
            <p class="mt-2 text-gray-600">Детальная информация об инвентаризации</p>
          </div>
          <button 
            @click="goBack"
            class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium px-4 py-2 rounded-lg text-sm transition"
          >
            Назад к списку
          </button>
        </div>
      </div>
      
      <div v-if="loading" class="flex items-center justify-center py-12">
        <Loader2 class="animate-spin h-8 w-8 text-blue-600 mr-3" />
        <span class="text-lg text-gray-600">Загрузка инвентаризации...</span>
      </div>
      
      <div v-else-if="inventory" class="space-y-6">
        <!-- Основная информация -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
          <h2 class="text-xl font-semibold text-gray-900 mb-4">Основная информация</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Название</label>
              <p class="text-sm text-gray-900">{{ inventory.name }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Дата создания</label>
              <p class="text-sm text-gray-900">{{ formatDate(inventory.created_at) }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Склад</label>
              <p class="text-sm text-gray-900">{{ inventory.warehouse_name }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Статус</label>
              <span 
                :class="{
                  'px-2 py-1 text-xs rounded-full': true,
                  'bg-gray-100 text-gray-800': inventory.status === 'draft',
                  'bg-blue-100 text-blue-800': inventory.status === 'in_progress',
                  'bg-green-100 text-green-800': inventory.status === 'completed',
                  'bg-red-100 text-red-800': inventory.status === 'cancelled'
                }"
              >
                {{ getStatusText(inventory.status) }}
              </span>
            </div>
            <div v-if="inventory.completed_at">
              <label class="block text-sm font-medium text-gray-700 mb-1">Дата завершения</label>
              <p class="text-sm text-gray-900">{{ formatDate(inventory.completed_at) }}</p>
            </div>
            <div v-if="inventory.description">
              <label class="block text-sm font-medium text-gray-700 mb-1">Описание</label>
              <p class="text-sm text-gray-900">{{ inventory.description }}</p>
            </div>
            <div v-if="inventory.notes">
              <label class="block text-sm font-medium text-gray-700 mb-1">Примечания</label>
              <p class="text-sm text-gray-900">{{ inventory.notes }}</p>
            </div>
          </div>
        </div>

        <!-- Информация о пользователе -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
          <h2 class="text-xl font-semibold text-gray-900 mb-4">Пользователь</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Имя пользователя</label>
              <p class="text-sm text-gray-900">{{ inventory.user?.first_name }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
              <p class="text-sm text-gray-900">{{ inventory.user?.email }}</p>
            </div>
            <div v-if="inventory.user?.phone_number">
              <label class="block text-sm font-medium text-gray-700 mb-1">Телефон</label>
              <p class="text-sm text-gray-900">{{ inventory.user.phone_number }}</p>
            </div>
            <div v-if="inventory.user?.company_name">
              <label class="block text-sm font-medium text-gray-700 mb-1">Компания</label>
              <p class="text-sm text-gray-900">{{ inventory.user.company_name }}</p>
            </div>
            <div v-if="inventory.user?.inn">
              <label class="block text-sm font-medium text-gray-700 mb-1">ИНН</label>
              <p class="text-sm text-gray-900">{{ inventory.user.inn }}</p>
            </div>
          </div>
        </div>

        <!-- Статистика -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
          <h2 class="text-xl font-semibold text-gray-900 mb-4">Статистика</h2>
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-gray-50 rounded-lg p-4">
              <div class="text-gray-500 text-sm mb-1">Всего товаров</div>
              <div class="text-2xl font-semibold text-gray-900">{{ inventory.items?.length || 0 }}</div>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
              <div class="text-gray-500 text-sm mb-1">Норма</div>
              <div class="text-2xl font-semibold text-green-600">{{ normalCount }}</div>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
              <div class="text-gray-500 text-sm mb-1">Недостача</div>
              <div class="text-2xl font-semibold text-red-600">{{ shortageCount }}</div>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
              <div class="text-gray-500 text-sm mb-1">Избыток</div>
              <div class="text-2xl font-semibold text-yellow-600">{{ excessCount }}</div>
            </div>
          </div>
        </div>

        <!-- Товары -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
          <h2 class="text-xl font-semibold text-gray-900 mb-4">Товары</h2>
          <div v-if="inventory.items && inventory.items.length > 0">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
              <thead>
                <tr class="bg-gray-50">
                  <th class="px-3 py-2 text-left font-semibold text-gray-700">Наименование</th>
                  <th class="px-3 py-2 text-center font-semibold text-gray-700">Артикул</th>
                  <th class="px-3 py-2 text-center font-semibold text-gray-700">Расчетный остаток</th>
                  <th class="px-3 py-2 text-center font-semibold text-gray-700">Фактический остаток</th>
                  <th class="px-3 py-2 text-center font-semibold text-gray-700">Разница</th>
                  <th class="px-3 py-2 text-center font-semibold text-gray-700">Статус</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="item in inventory.items" :key="item.id" class="hover:bg-gray-50">
                  <td class="px-3 py-2 text-gray-900">
                    <div>
                      <div class="font-medium">{{ item.product_name }}</div>
                      <div class="text-xs text-gray-500">{{ item.product_sku }}</div>
                    </div>
                  </td>
                  <td class="px-3 py-2 text-center text-gray-900">{{ item.product_sku || '-' }}</td>
                  <td class="px-3 py-2 text-center text-gray-900">{{ formatNumber(item.calculated_quantity) }}</td>
                  <td class="px-3 py-2 text-center text-gray-900">{{ formatNumber(item.actual_quantity) }}</td>
                  <td class="px-3 py-2 text-center">
                    <span :class="getDifferenceClass(item)">
                      {{ formatNumber(item.difference_quantity) }}
                    </span>
                  </td>
                  <td class="px-3 py-2 text-center">
                    <span :class="getExcessShortageClass(item)">
                      {{ getExcessShortageText(item) }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div v-else class="text-center py-8 text-gray-500">
            Товары не найдены
          </div>
        </div>

        <!-- Файлы -->
        <div v-if="inventory.files && inventory.files.length > 0" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
          <h2 class="text-xl font-semibold text-gray-900 mb-4">Файлы</h2>
          <ul class="list-disc pl-5">
            <li v-for="file in inventory.files" :key="file.id" class="mb-1">
              <a :href="file.file_url || '#'" target="_blank" class="text-blue-600 hover:underline text-sm">{{ file.filename }}</a>
              <span class="text-gray-400 text-xs ml-2">({{ formatFileSize(file.file_size) }})</span>
            </li>
          </ul>
        </div>
      </div>
      
      <div v-else class="text-center py-12">
        <div class="mx-auto h-12 w-12 text-gray-400 mb-4">
          <AlertCircle class="h-12 w-12" />
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Инвентаризация не найдена</h3>
        <p class="text-gray-500">Запрашиваемая инвентаризация не существует или была удалена.</p>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import AdminLayout from '../AdminLayout.vue'
import { apiRequest } from '@/config/api'
import { Loader2, AlertCircle } from 'lucide-vue-next'

// Устанавливаем заголовок страницы
document.title = 'B2B SKLAD - Админ - Просмотр инвентаризации'

const route = useRoute()
const router = useRouter()

const inventory = ref(null)
const loading = ref(true)

// Вычисляемые свойства для статистики
const normalCount = computed(() => {
  return inventory.value?.items?.filter(item => item.excess_shortage === 'normal').length || 0
})

const shortageCount = computed(() => {
  return inventory.value?.items?.filter(item => item.excess_shortage === 'shortage').length || 0
})

const excessCount = computed(() => {
  return inventory.value?.items?.filter(item => item.excess_shortage === 'excess').length || 0
})

function formatDate(date) {
  if (!date) return ''
  return new Date(date).toLocaleDateString('ru-RU')
}

function formatNumber(num) {
  if (num === null || num === undefined) return '0'
  const number = Number(num)
  // Если число целое, показываем без десятичных знаков
  return Number.isInteger(number) ? number.toString() : number.toFixed(3)
}

function formatFileSize(bytes) {
  if (!bytes) return '0 Б'
  const sizes = ['Б', 'КБ', 'МБ', 'ГБ']
  const i = Math.floor(Math.log(bytes) / Math.log(1024))
  return Math.round(bytes / Math.pow(1024, i) * 100) / 100 + ' ' + sizes[i]
}

function getStatusText(status) {
  const statusMap = {
    'draft': 'Черновик',
    'in_progress': 'В процессе',
    'completed': 'Завершена',
    'cancelled': 'Отменена'
  }
  return statusMap[status] || status
}

function getDifferenceClass(item) {
  const diff = item.difference_quantity || 0
  if (diff > 0) return 'text-green-600 font-medium'
  if (diff < 0) return 'text-red-600 font-medium'
  return 'text-gray-600'
}

function getExcessShortageText(item) {
  const status = item.excess_shortage
  if (status === 'excess') return 'Избыток'
  if (status === 'shortage') return 'Недостача'
  return 'Норма'
}

function getExcessShortageClass(item) {
  const status = item.excess_shortage
  if (status === 'excess') return 'text-green-600'
  if (status === 'shortage') return 'text-red-600'
  return 'text-gray-600'
}

function goBack() {
  router.push('/admin/products/inventory')
}

async function fetchInventory() {
  loading.value = true
  try {
    const inventoryId = route.params.id
    const res = await apiRequest(`/admin/inventories/${inventoryId}`, { method: 'GET' })
    
    if (res.ok && res.data && res.data.success) {
      inventory.value = res.data.data
    } else {
      inventory.value = null
    }
  } catch (error) {
    console.error('Ошибка загрузки инвентаризации:', error)
    inventory.value = null
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchInventory()
})
</script> 