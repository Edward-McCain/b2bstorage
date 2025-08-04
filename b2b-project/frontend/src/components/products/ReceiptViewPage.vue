<template>
  <div class="min-h-screen bg-gray-50" style="padding-top: 66px;">
    <ProductsMenu />
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
      <div class="bg-white rounded-xl shadow p-6 relative">
        
        <!-- Прелоадер -->
        <div v-if="loading" class="flex items-center justify-center py-20">
          <div class="text-center">
            <Loader2 class="animate-spin h-8 w-8 text-blue-600 mx-auto mb-4" />
            <p class="text-gray-600 text-sm">{{ t('ReceiptViewPage_1') }}</p> <!-- Загрузка оприходования... -->
          </div>
        </div>

        <!-- Контент -->
        <div v-else>
          <div class="mb-6">
            <div class="flex items-center justify-between">
              <div>
                <h1 class="text-sm lg:text-xl font-bold text-gray-900 mb-1">{{ t('ReceiptViewPage_2') }} {{ receipt.number }}</h1> <!-- Оприходование номер -->
                <div class="text-gray-500 text-sm">{{ t('ReceiptViewPage_3') }} {{ formatDate(receipt.date) }}</div> <!-- от -->
              </div>
              <div class="flex items-center gap-2">
                <button 
                  @click="downloadPDF"
                  class="flex items-center gap-2 bg-blue-50 hover:bg-blue-100 text-blue-700 p-2 rounded transition group relative cursor-pointer"
                >
                  <Download class="w-4 h-4" />
                  <span class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-900 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-10">
                    {{ t('ReceiptViewPage_4') }} <!-- Скачать PDF -->
                  </span>
                </button>
                <button 
                  @click="printDocument"
                  class="flex items-center gap-2 bg-gray-50 hover:bg-gray-100 text-gray-700 p-2 rounded transition group relative cursor-pointer"
                >
                  <Printer class="w-4 h-4" />
                  <span class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-900 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-10">
                    {{ t('ReceiptViewPage_5') }} <!-- Печать -->
                  </span>
                </button>
              </div>
            </div>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
              <div class="text-gray-500 text-xs mb-1">{{ t('ReceiptViewPage_6') }}</div> <!-- Организация -->
              <div class="text-gray-900 text-sm">{{ receipt.organization }}</div>
            </div>
            <div>
              <div class="text-gray-500 text-xs mb-1">{{ t('ReceiptViewPage_7') }}</div> <!-- Склад -->
              <div class="text-gray-900 text-sm">{{ receipt.warehouse_name }}<span v-if="receipt.warehouse_address" class="text-gray-400 ml-2">({{ receipt.warehouse_address }})</span></div>
            </div>
            <div>
              <div class="text-gray-500 text-xs mb-1">{{ t('ReceiptViewPage_8') }}</div> <!-- Статус -->
              <div class="text-gray-900 text-sm">{{ receipt.status === 'posted' ? t('ReceiptViewPage_23') : t('ReceiptViewPage_24') }}</div> <!-- Проведено : Черновик -->
            </div>
            <div>
              <div class="text-gray-500 text-xs mb-1">{{ t('ReceiptViewPage_9') }}</div> <!-- Создано -->
              <div class="text-gray-900 text-sm">{{ receipt.created_by || '-' }}</div>
            </div>
            <div>
              <div class="text-gray-500 text-xs mb-1">{{ t('ReceiptViewPage_10') }}</div> <!-- Комментарий -->
              <div class="text-gray-900 text-sm">{{ receipt.comment || '-' }}</div>
            </div>
            <div>
              <div class="text-gray-500 text-xs mb-1">{{ t('ReceiptViewPage_11') }}</div> <!-- Накладные расходы -->
              <div class="text-gray-900 text-sm">{{ receipt.overhead_costs }}</div>
            </div>
            <div>
              <div class="text-gray-500 text-xs mb-1">{{ t('ReceiptViewPage_12') }}</div> <!-- Валюта -->
              <div class="text-gray-900 text-sm">{{ userCurrency }}</div>
            </div>
          </div>
          
          <!-- Товары -->
          <div class="mb-6">
            <div class="font-semibold text-gray-800 mb-2">{{ t('ReceiptViewPage_13') }}</div> <!-- Товары -->
            <div v-if="loadingPositions" class="flex items-center justify-center py-8">
              <div class="text-center">
                <Loader2 class="animate-spin h-6 w-6 text-blue-600 mx-auto mb-2" />
                <p class="text-gray-600 text-sm">{{ t('ReceiptViewPage_14') }}</p> <!-- Загрузка товаров... -->
              </div>
            </div>
            <div v-else>
              <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-3 py-2 text-left font-semibold text-gray-700">{{ t('ReceiptViewPage_15') }}</th> <!-- Наименование -->
                    <th class="px-3 py-2 text-center font-semibold text-gray-700">{{ t('ReceiptViewPage_16') }}</th> <!-- Количество -->
                    <th class="px-3 py-2 text-center font-semibold text-gray-700">{{ t('ReceiptViewPage_17') }}</th> <!-- Цена -->
                    <th class="px-3 py-2 text-center font-semibold text-gray-700">{{ t('ReceiptViewPage_18') }}</th> <!-- Сумма -->
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
              <div class="text-right mt-2 text-base font-semibold text-gray-900">{{ t('ReceiptViewPage_19') }} {{ receipt.total }} {{ userCurrency }}</div> <!-- Итого: -->
            </div>
          </div>
          
          <!-- Файлы -->
          <div v-if="loadingFiles" class="mb-4">
            <div class="font-semibold text-gray-800 mb-2">{{ t('ReceiptViewPage_20') }}</div> <!-- Файлы -->
            <div class="flex items-center justify-center py-4">
              <div class="text-center">
                <Loader2 class="animate-spin h-6 w-6 text-blue-600 mx-auto mb-2" />
                <p class="text-gray-600 text-sm">{{ t('ReceiptViewPage_21') }}</p> <!-- Загрузка файлов... -->
              </div>
            </div>
          </div>
          <div v-else-if="files.length > 0" class="mb-4">
            <div class="font-semibold text-gray-800 mb-2">{{ t('ReceiptViewPage_20') }}</div> <!-- Файлы -->
            <ul class="list-disc pl-5">
              <li v-for="file in files" :key="file.id" class="mb-1">
                <a :href="file.file_url || '#'" target="_blank" class="text-blue-600 hover:underline text-sm">{{ file.filename }}</a>
                <span class="text-gray-400 text-xs ml-2">({{ file.size_mb }} {{ t('ReceiptViewPage_22') }})</span> <!-- МБ -->
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import ProductsMenu from './ProductsMenu.vue'
import { useRoute, useRouter } from 'vue-router'
import { Pencil, Loader2, Download, Printer } from 'lucide-vue-next'
import { apiRequest } from '@/config/api'
import { generatePDF, printElement, generatePDFSimple, generatePDFWithCanvas, generateSimplePDF, generateReceiptPDFWithCanvas, printReceipt } from '@/utils/printUtils'
import { getUserCurrency, updateUserCurrency } from '@/utils/currencyUtils'
import { t } from '@/locales'

// Устанавливаем заголовок страницы
document.title = 'B2B SKLAD - Оприходования'
const route = useRoute()
const router = useRouter()

const receipt = ref({})
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
    console.error(t('ReceiptViewPage_25') + error) // Ошибка получения валюты пользователя:
  }
}

function formatDate(date) {
  if (!date) return ''
  return new Date(date).toLocaleString('ru-RU')
}

async function fetchReceipt() {
  const id = route.params.id
  const res = await apiRequest(`/receipts/${id}`, { method: 'GET' })
  if (res.ok && res.data && res.data.data) {
    receipt.value = res.data.data
    positions.value = res.data.data.positions || []
    files.value = res.data.data.files || []
    loadingPositions.value = false
    loadingFiles.value = false
  } else {
    router.push('/products/receipts')
  }
  loading.value = false
}

onMounted(() => {
  fetchReceipt()
  fetchUserCurrency()
})

async function downloadPDF() {
  try {
    if (receipt.value) {
      const filename = `receipt-${receipt.value.number || 'receipt'}.pdf`
      generateReceiptPDFWithCanvas(receipt.value, filename, userCurrency.value)
    } else {
      console.error(t('ReceiptViewPage_26')) // Не удалось найти данные оприходования
    }
  } catch (error) {
    console.error(t('ReceiptViewPage_27') + error) // Ошибка скачивания PDF:
  }
}

function printDocument() {
  try {
    if (receipt.value) {
      printReceipt(receipt.value, userCurrency.value)
    } else {
      console.error(t('ReceiptViewPage_26')) // Не удалось найти данные оприходования
    }
  } catch (error) {
    console.error(t('ReceiptViewPage_28') + error) // Ошибка печати:
  }
}
</script> 