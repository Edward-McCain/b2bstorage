<template>
  <div class="min-h-screen bg-gray-50" style="padding-top: 66px;">
    <ProductsMenu />
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
      <div class="bg-white rounded-xl shadow p-6 relative">
        
        <!-- Прелоадер -->
        <div v-if="loading" class="flex items-center justify-center py-20">
          <div class="text-center">
            <Loader2 class="animate-spin h-8 w-8 text-blue-600 mx-auto mb-4" />
            <p class="text-gray-600 text-sm">{{ t('WriteOffViewPage_1') }}</p> <!-- Загрузка списания... -->
          </div>
        </div>

        <!-- Контент -->
        <div v-else>
          <div class="mb-6">
            <div class="flex items-center justify-between">
              <div>
                <h1 class="text-sm lg:text-xl font-bold text-gray-900 mb-1">{{ t('WriteOffViewPage_2') }} {{ writeOff.number }}</h1> <!-- Списание номер -->
                <div class="text-gray-500 text-sm">{{ t('WriteOffViewPage_3') }} {{ formatDate(writeOff.date) }}</div> <!-- от -->
              </div>
              <div class="flex items-center gap-2">
                <button 
                  @click="downloadPDF"
                  class="flex items-center gap-2 bg-blue-50 hover:bg-blue-100 text-blue-700 p-2 rounded transition group relative cursor-pointer"
                >
                  <Download class="w-4 h-4" />
                  <span class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-900 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-10">
                    {{ t('WriteOffViewPage_4') }} <!-- Скачать PDF -->
                  </span>
                </button>
                <button 
                  @click="printDocument"
                  class="flex items-center gap-2 bg-gray-50 hover:bg-gray-100 text-gray-700 p-2 rounded transition group relative cursor-pointer"
                >
                  <Printer class="w-4 h-4" />
                  <span class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-900 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-10">
                    {{ t('WriteOffViewPage_5') }} <!-- Печать -->
                  </span>
                </button>
              </div>
            </div>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
              <div class="text-gray-500 text-xs mb-1">{{ t('WriteOffViewPage_6') }}</div> <!-- Организация -->
              <div class="text-gray-900 text-sm">{{ writeOff.organization }}</div>
            </div>
            <div>
              <div class="text-gray-500 text-xs mb-1">{{ t('WriteOffViewPage_7') }}</div> <!-- Склад -->
              <div class="text-gray-900 text-sm">{{ writeOff.warehouse_name }}<span v-if="writeOff.warehouse_address" class="text-gray-400 ml-2">({{ writeOff.warehouse_address }})</span></div>
            </div>
            <div>
              <div class="text-gray-500 text-xs mb-1">{{ t('WriteOffViewPage_8') }}</div> <!-- Статус -->
              <div class="text-gray-900 text-sm">{{ writeOff.status === 'posted' ? t('WriteOffViewPage_22') : t('WriteOffViewPage_23') }}</div> <!-- Проведено : Черновик -->
            </div>
            <div>
              <div class="text-gray-500 text-xs mb-1">{{ t('WriteOffViewPage_9') }}</div> <!-- Создано -->
              <div class="text-gray-900 text-sm">{{ writeOff.created_by || '-' }}</div>
            </div>
            <div>
              <div class="text-gray-500 text-xs mb-1">{{ t('WriteOffViewPage_10') }}</div> <!-- Комментарий -->
              <div class="text-gray-900 text-sm">{{ writeOff.comment || '-' }}</div>
            </div>
            <div>
              <div class="text-gray-500 text-xs mb-1">{{ t('WriteOffViewPage_11') }}</div> <!-- Накладные расходы -->
              <div class="text-gray-900 text-sm">{{ writeOff.overhead_costs }}</div>
            </div>
            <div>
              <div class="text-gray-500 text-xs mb-1">{{ t('WriteOffViewPage_12') }}</div> <!-- Валюта -->
              <div class="text-gray-900 text-sm">{{ userCurrency }}</div>
            </div>
          </div>
          
          <!-- Товары -->
          <div class="mb-6">
            <div class="font-semibold text-gray-800 mb-2">{{ t('WriteOffViewPage_13') }}</div> <!-- Товары -->
            <div v-if="writeOff.positions && writeOff.positions.length > 0">
              <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-3 py-2 text-left font-semibold text-gray-700">{{ t('WriteOffViewPage_14') }}</th> <!-- Наименование -->
                    <th class="px-3 py-2 text-center font-semibold text-gray-700">{{ t('WriteOffViewPage_15') }}</th> <!-- Количество -->
                    <th class="px-3 py-2 text-center font-semibold text-gray-700">{{ t('WriteOffViewPage_16') }}</th> <!-- Цена -->
                    <th class="px-3 py-2 text-center font-semibold text-gray-700">{{ t('WriteOffViewPage_17') }}</th> <!-- Сумма -->
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="pos in positions" :key="pos.id" class="hover:bg-gray-50">
                    <td class="px-3 py-2">{{ pos.name }}</td>
                    <td class="px-3 py-2 text-center">{{ parseFloat(pos.quantity) }}</td>
                    <td class="px-3 py-2 text-center">{{ pos.price }} {{ userCurrency }}</td>
                    <td class="px-3 py-2 text-center">{{ pos.amount }} {{ userCurrency }}</td>
                  </tr>
                </tbody>
              </table>
              <div class="text-right mt-2 text-base font-semibold text-gray-900">{{ t('WriteOffViewPage_18') }} {{ writeOff.total }} {{ userCurrency }}</div> <!-- Итого: -->
            </div>
            <div v-else class="text-center text-gray-500 py-8">
              {{ t('WriteOffViewPage_19') }} <!-- Товары не найдены -->
            </div>
          </div>
          
          <!-- Файлы -->
          <div v-if="writeOff.files && writeOff.files.length > 0" class="mb-4">
            <div class="font-semibold text-gray-800 mb-2">{{ t('WriteOffViewPage_20') }}</div> <!-- Файлы -->
            <ul class="list-disc pl-5">
              <li v-for="file in writeOff.files" :key="file.id" class="mb-1">
                <a :href="getFileUrl(file.file_url)" target="_blank" class="text-blue-600 hover:underline text-sm">{{ file.filename }}</a>
                <span class="text-gray-400 text-xs ml-2">({{ file.size_mb }} {{ t('WriteOffViewPage_21') }})</span> <!-- МБ -->
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import ProductsMenu from './ProductsMenu.vue'
import { useRouter, useRoute } from 'vue-router'
import { ref, onMounted } from 'vue'
import { apiRequest } from '@/config/api'
import toastr from 'toastr'
import { Loader2, Pencil, Download, Printer } from 'lucide-vue-next'
import { generatePDF, printElement, generatePDFSimple, generatePDFWithCanvas, generateSimplePDF, generateReceiptPDFWithCanvas, printReceipt, generateWriteOffPDFWithCanvas } from '@/utils/printUtils'
import { getUserCurrency, updateUserCurrency } from '@/utils/currencyUtils'
import { t } from '@/locales'

// Устанавливаем заголовок страницы
document.title = 'B2B SKLAD - Списания'

const router = useRouter()
const route = useRoute()
const writeOffId = route.params.id

const writeOff = ref({})
const positions = ref([])
const files = ref([])
const loading = ref(true)
const loadingPositions = ref(true)
const loadingFiles = ref(true)
const userCurrency = ref('UZS')

// Получаем валюту пользователя
const fetchUserCurrency = async () => {
  try {
    const currency = await getUserCurrency()
    userCurrency.value = currency
  } catch (error) {
    console.error(t('WriteOffViewPage_26') + error) // Ошибка получения валюты пользователя:
  }
}

function formatDate(dateString) {
  if (!dateString) return ''
  const date = new Date(dateString)
  return date.toLocaleString('ru-RU', {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit',
    hour: '2-digit',
    minute: '2-digit'
  })
}

function getFileUrl(fileUrl) {
  if (!fileUrl) return '#'
  // Если URL уже полный (начинается с http), используем его
  if (fileUrl.startsWith('http')) {
    return fileUrl
  }
  // Иначе добавляем домен
  return `${window.location.origin}${fileUrl}`
}

async function loadWriteOff() {
  try {
    const response = await apiRequest(`/write-offs/${writeOffId}`, { method: 'GET' })
    if (response.ok && response.data.success) {
      writeOff.value = response.data.data
      positions.value = writeOff.value.positions || []
    } else {
      toastr.error(t('WriteOffViewPage_24')) // Списание не найдено
      router.push('/products/write-offs')
    }
  } catch (error) {
    console.error(t('WriteOffViewPage_25') + error) // Ошибка при загрузке списания
    toastr.error(t('WriteOffViewPage_25')) // Ошибка при загрузке списания
    router.push('/products/write-offs')
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadWriteOff()
  fetchUserCurrency()
})

async function downloadPDF() {
  try {
    if (writeOff.value) {
      const filename = `writeoff-${writeOff.value.number || 'writeoff'}.pdf`
      generateWriteOffPDFWithCanvas(writeOff.value, filename, userCurrency.value)
    } else {
      console.error(t('WriteOffViewPage_27')) // Не удалось найти данные списания
    }
  } catch (error) {
    console.error(t('WriteOffViewPage_28') + error) // Ошибка скачивания PDF:
  }
}

function printDocument() {
  try {
    const contentElement = document.querySelector('.bg-white.rounded-xl.shadow.p-6')
    if (contentElement) {
      printElement(contentElement)
    } else {
      console.error(t('WriteOffViewPage_29')) // Не удалось найти контент для печати
    }
  } catch (error) {
    console.error(t('WriteOffViewPage_30') + error) // Ошибка печати:
  }
}
</script> 