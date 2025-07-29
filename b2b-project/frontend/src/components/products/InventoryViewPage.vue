<template>
  <div class="min-h-screen bg-gray-50" style="padding-top: 66px;">
    <ProductsMenu />
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
      <div class="bg-white rounded-xl shadow p-6 relative">
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
            <div class="flex items-center justify-between">
              <div>
                <h1 class="text-xl font-bold text-gray-900 mb-1">{{ inventory.name }}</h1>
                <div class="text-gray-500 text-sm">от {{ formatDate(inventory.created_at) }}</div>
              </div>
              <div class="flex items-center gap-2">
                <button 
                  @click="downloadPDF"
                  class="flex items-center gap-2 bg-blue-50 hover:bg-blue-100 text-blue-700 p-2 rounded transition group relative cursor-pointer"
                >
                  <Download class="w-4 h-4" />
                  <span class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-900 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-10">
                    Скачать PDF
                  </span>
                </button>
                <button 
                  @click="printDocument"
                  class="flex items-center gap-2 bg-gray-50 hover:bg-gray-100 text-gray-700 p-2 rounded transition group relative cursor-pointer"
                >
                  <Printer class="w-4 h-4" />
                  <span class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-900 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-10">
                    Печать
                  </span>
                </button>
              </div>
            </div>
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
              <div class="text-gray-500 text-xs mb-1">Название</div>
              <div class="text-gray-900 text-sm">{{ inventory.name }}</div>
            </div>
            <div>
              <div class="text-gray-500 text-xs mb-1">Склад</div>
              <div class="text-gray-900 text-sm">{{ inventory.warehouse_name }}</div>
            </div>
            <div>
              <div class="text-gray-500 text-xs mb-1">Статус</div>
              <div class="text-gray-900 text-sm">{{ getStatusText(inventory.status) }}</div>
            </div>
            <div>
              <div class="text-gray-500 text-xs mb-1">Создано</div>
              <div class="text-gray-900 text-sm">{{ inventory.created_by || '-' }}</div>
            </div>
            <div>
              <div class="text-gray-500 text-xs mb-1">Комментарий</div>
              <div class="text-gray-900 text-sm">{{ inventory.comment || '-' }}</div>
            </div>
            <div>
              <div class="text-gray-500 text-xs mb-1">Валюта</div>
              <div class="text-gray-900 text-sm">{{ userCurrency }}</div>
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
                    <th class="px-3 py-2 text-center font-semibold text-gray-700">Детали</th>
                  </tr>
                </thead>
                <tbody>
                  <template v-for="item in items" :key="item.id">
                    <tr class="hover:bg-gray-50">
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
                      <td class="px-3 py-2 text-center">
                        <div v-if="hasDiscrepancy(item) && item.photo" class="flex items-center justify-center gap-2">
                          <button 
                            @click="viewFullPhoto(item.photo)"
                            class="p-0 rounded transition-colors hover:opacity-80 cursor-pointer border border-gray-200 hover:border-blue-400"
                            title="Просмотреть фото"
                          >
                            <img 
                              :src="item.photo" 
                              alt="Фото товара" 
                              class="w-6 h-6 rounded object-cover"
                            />
                          </button>
                        </div>
                        <span v-else class="text-gray-400 text-xs">—</span>
                      </td>
                    </tr>
                    <!-- Комментарий под строкой -->
                    <tr v-if="item.notes && item.notes.trim() !== ''" :key="item.id + '-comment'">
                      <td colspan="6" class="bg-gray-50 px-3 py-2 text-sm text-black border-t border-b border-gray-100">
                        <div class="flex items-start gap-2">
                          <span style="font-size: 12px;color: #747474;">Комментарий: {{ item.notes }}</span>
                        </div>
                      </td>
                    </tr>
                  </template>
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
    
    <!-- Модальное окно для просмотра комментария -->
    <div v-if="showCommentDialogVisible" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-semibold">Комментарий</h3>
          <button 
            @click="closeCommentDialog"
            class="text-gray-400 hover:text-gray-600 transition-colors"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
        <div class="text-gray-700 mb-4">
          {{ currentComment }}
        </div>
        <div class="flex justify-end">
          <button 
            @click="closeCommentDialog"
            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors"
          >
            Закрыть
          </button>
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
import { Loader2, Pencil, MessageSquare, Download, Printer } from 'lucide-vue-next'
import toastr from 'toastr'
import { generatePDF, printElement, generatePDFSimple, generatePDFWithCanvas, generateSimplePDF, generateReceiptPDFWithCanvas, printReceipt, generateWriteOffPDFWithCanvas, generateInventoryPDFWithCanvas } from '@/utils/printUtils'
import { getUserCurrency, updateUserCurrency } from '@/utils/currencyUtils'

// Устанавливаем заголовок страницы
document.title = 'B2B SKLAD - Просмотр инвентаризации'

const router = useRouter()
const route = useRoute()

// Загрузка данных
const loading = ref(true)
const inventoryId = route.params.id

// Данные
const inventory = ref({})
const items = ref([])
const files = ref([])
const userCurrency = ref('UZS')

// Получаем валюту пользователя
const fetchUserCurrency = async () => {
  try {
    const currency = await getUserCurrency()
    userCurrency.value = currency
  } catch (error) {
    console.error('Ошибка получения валюты пользователя:', error)
  }
}

// Модальное окно для комментария
const showCommentDialogVisible = ref(false)
const currentComment = ref('')

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

// Функция проверки наличия расхождения у товара
function hasDiscrepancy(item) {
  const diff = (item.actual_quantity || 0) - (item.calculated_quantity || 0)
  return diff !== 0
}

// Функция для просмотра полного фото
function viewFullPhoto(photoUrl) {
  window.open(photoUrl, '_blank')
}

// Функция для показа модального окна с комментарием
function showCommentModal(comment) {
  currentComment.value = comment
  showCommentDialogVisible.value = true
}

// Функция для закрытия модального окна комментария
function closeCommentDialog() {
  showCommentDialogVisible.value = false
  currentComment.value = ''
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
        // Отладка: логируем данные товаров
        console.log('Loaded inventory items:', response.data.data.items)
        response.data.data.items.forEach(item => {
          if (item.notes || item.photo) {
            console.log(`Item ${item.product_id}:`, {
              notes: item.notes,
              photo: item.photo,
              hasNotes: !!item.notes,
              hasPhoto: !!item.photo,
              notesTrimmed: item.notes?.trim(),
              shouldShowComment: !!(item.notes && item.notes.trim() !== '')
            })
          }
        })
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
  fetchUserCurrency()
})

// Функция для скачивания PDF
async function downloadPDF() {
  try {
    if (inventory.value) {
      const filename = `inventory-${inventory.value.name || inventoryId}.pdf`
      generateInventoryPDFWithCanvas(inventory.value, filename, userCurrency.value)
      toastr.success('PDF успешно скачан')
    } else {
      console.error('Не удалось найти данные инвентаризации')
    }
  } catch (error) {
    console.error('Ошибка скачивания PDF:', error)
    toastr.error('Ошибка при скачивании PDF')
  }
}

// Функция для печати
function printDocument() {
  try {
    const contentElement = document.querySelector('.bg-white.rounded-xl.shadow.p-6')
    if (contentElement) {
      printElement(contentElement)
    } else {
      toastr.error('Не удалось найти контент для печати')
    }
  } catch (error) {
    console.error('Ошибка печати:', error)
    toastr.error('Ошибка печати')
  }
}
</script>