<template>
  <div class="min-h-screen bg-gray-50" style="padding-top: 66px;">
    <ProductsMenu />
    <div class="max-w-3xl mx-auto bg-white rounded-xl shadow p-6 relative mt-4">
      <!-- <router-link 
        :to="`/products/inventory/edit/${inventoryId}`"
        class="absolute top-4 right-4 bg-blue-50 hover:bg-blue-100 text-blue-700 px-3 py-1.5 rounded flex items-center gap-1 text-sm transition"
      >
        <Pencil class="w-4 h-4" />
        Редактировать
      </router-link> -->
      
      <!-- Прелоадер -->
      <div v-if="loading" class="flex items-center justify-center py-20">
        <div class="text-center">
          <Loader2 class="animate-spin h-8 w-8 text-blue-600 mx-auto mb-4" />
          <p class="text-gray-600 text-sm">Загрузка инвентаризации...</p>
        </div>
      </div>

      <!-- Контент -->
      <div v-else>
        <div class="mb-6">
          <h1 class="text-xl font-bold text-gray-900 mb-1">{{ inventory.name }}</h1>
          <div class="text-gray-500 text-sm">от {{ formatDate(inventory.created_at) }}</div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
          <div>
            <div class="text-gray-500 text-xs mb-1">Статус</div>
            <div class="text-gray-900 text-sm">
              <span :class="getStatusClass(inventory.status)">
                {{ getStatusText(inventory.status) }}
              </span>
            </div>
          </div>
          <div>
            <div class="text-gray-500 text-xs mb-1">Склад</div>
            <div class="text-gray-900 text-sm">{{ inventory.warehouse_name || `Склад #${inventory.warehouse_id}` }}</div>
          </div>
          <div>
            <div class="text-gray-500 text-xs mb-1">Создал</div>
            <div class="text-gray-900 text-sm">{{ inventory.created_by_name || '-' }}</div>
          </div>
          <div v-if="inventory.completed_at">
            <div class="text-gray-500 text-xs mb-1">Завершена</div>
            <div class="text-gray-900 text-sm">{{ formatDate(inventory.completed_at) }}</div>
          </div>
          <div v-if="inventory.description">
            <div class="text-gray-500 text-xs mb-1">Описание</div>
            <div class="text-gray-900 text-sm">{{ inventory.description }}</div>
          </div>
        </div>

        <!-- Статистика -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
          <div class="bg-gray-50 rounded-lg p-3">
            <div class="text-gray-500 text-xs mb-1">Всего товаров</div>
            <div class="text-lg font-semibold text-gray-900">{{ inventory.items_count || 0 }}</div>
          </div>
          <div class="bg-gray-50 rounded-lg p-3">
            <div class="text-gray-500 text-xs mb-1">Норма</div>
            <div class="text-lg font-semibold text-green-600">{{ normalCount }}</div>
          </div>
          <div class="bg-gray-50 rounded-lg p-3">
            <div class="text-gray-500 text-xs mb-1">Недостача</div>
            <div class="text-lg font-semibold text-red-600">{{ shortageCount }}</div>
          </div>
          <div class="bg-gray-50 rounded-lg p-3">
            <div class="text-gray-500 text-xs mb-1">Избыток</div>
            <div class="text-lg font-semibold text-yellow-600">{{ excessCount }}</div>
          </div>
        </div>
        
        <!-- Товары -->
        <div class="mb-6">
          <div class="font-semibold text-gray-800 mb-2">Товары</div>
          <div v-if="items.length === 0" class="text-center py-8 text-gray-500">
            Нет товаров в инвентаризации
          </div>
          <div v-else>
            <table class="min-w-full divide-y divide-gray-200 text-sm">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-3 py-2 text-left font-semibold text-gray-700">Наименование</th>
                  <th class="px-3 py-2 text-center font-semibold text-gray-700">Расчетный остаток</th>
                  <th class="px-3 py-2 text-center font-semibold text-gray-700">Фактический остаток</th>
                  <th class="px-3 py-2 text-center font-semibold text-gray-700">Разница</th>
                  <th class="px-3 py-2 text-center font-semibold text-gray-700">Статус</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="item in items" :key="item.id" class="hover:bg-gray-50">
                  <td class="px-3 py-2">
                    <div>
                      <div class="font-medium">{{ item.product_name }}</div>
                      <div class="text-xs text-gray-500">{{ item.product_sku }}</div>
                    </div>
                  </td>
                  <td class="px-3 py-2 text-center">{{ formatNumber(item.calculated_quantity) }}</td>
                  <td class="px-3 py-2 text-center">{{ formatNumber(item.actual_quantity) }}</td>
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
        </div>
        
        <!-- Файлы -->
        <div v-if="files.length > 0" class="mb-4">
          <div class="font-semibold text-gray-800 mb-2">Файлы</div>
          <ul class="list-disc pl-5">
            <li v-for="file in files" :key="file.id" class="mb-1">
              <a :href="getFileUrl(file.file_url)" target="_blank" class="text-blue-600 hover:underline text-sm">{{ file.original_filename || file.filename }}</a>
              <span class="text-gray-400 text-xs ml-2">({{ formatFileSize(file.file_size) }})</span>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import ProductsMenu from './ProductsMenu.vue'
import { apiRequest } from '@/config/api'
import { useRouter, useRoute } from 'vue-router'
import { Loader2, Pencil } from 'lucide-vue-next'
import toastr from 'toastr'

// Устанавливаем заголовок страницы
document.title = 'B2B SKLAD - Просмотр инвентаризации'

const router = useRouter()
const route = useRoute()

// Загрузка данных
const loading = ref(false)
const inventoryId = route.params.id

// Данные
const inventory = ref({})
const items = ref([])
const files = ref([])

// Вычисляемые свойства для статистики
const normalCount = computed(() => {
  return items.value.filter(item => item.excess_shortage === 'normal').length
})

const shortageCount = computed(() => {
  return items.value.filter(item => item.excess_shortage === 'shortage').length
})

const excessCount = computed(() => {
  return items.value.filter(item => item.excess_shortage === 'excess').length
})

function formatDate(date) {
  if (!date) return ''
  return new Date(date).toLocaleString('ru-RU')
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

function getStatusClass(status) {
  const classMap = {
    'draft': 'text-gray-500',
    'in_progress': 'text-blue-600',
    'completed': 'text-green-600',
    'cancelled': 'text-red-600'
  }
  return classMap[status] || 'text-gray-500'
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

function getFileUrl(fileUrl) {
  if (!fileUrl) return '#'
  if (fileUrl.startsWith('http')) {
    return fileUrl
  }
  return `${window.location.origin}${fileUrl}`
}

async function loadInventory() {
  loading.value = true
  try {
    const response = await apiRequest(`/inventories/${inventoryId}`, { method: 'GET' })
    if (response.ok && response.data.success) {
      inventory.value = response.data.data
      // Загружаем товары
      if (response.data.data.items && Array.isArray(response.data.data.items)) {
        items.value = response.data.data.items
      }
      // Загружаем файлы
      if (response.data.data.files && Array.isArray(response.data.data.files)) {
        files.value = response.data.data.files
      }
    } else {
      toastr.error('Ошибка загрузки инвентаризации')
      router.push('/products/inventory')
    }
  } catch (error) {
    console.error('Ошибка загрузки инвентаризации:', error)
    toastr.error('Ошибка загрузки инвентаризации')
    router.push('/products/inventory')
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadInventory()
})
</script>