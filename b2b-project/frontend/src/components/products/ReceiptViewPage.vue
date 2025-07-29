<template>
  <div class="min-h-screen bg-gray-50" style="padding-top: 66px;">
    <ProductsMenu />
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
      <div class="bg-white rounded-xl shadow p-6 relative">
        
        <!-- Прелоадер -->
        <div v-if="loading" class="flex items-center justify-center py-20">
          <div class="text-center">
            <Loader2 class="animate-spin h-8 w-8 text-blue-600 mx-auto mb-4" />
            <p class="text-gray-600 text-sm">Загрузка оприходования...</p>
          </div>
        </div>

        <!-- Контент -->
        <div v-else>
          <div class="mb-6">
            <div class="flex items-center justify-between">
              <div>
                <h1 class="text-xl font-bold text-gray-900 mb-1">Оприходование номер {{ receipt.number }}</h1>
                <div class="text-gray-500 text-sm">от {{ formatDate(receipt.date) }}</div>
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
              <div class="text-gray-500 text-xs mb-1">Организация</div>
              <div class="text-gray-900 text-sm">{{ receipt.organization }}</div>
            </div>
            <div>
              <div class="text-gray-500 text-xs mb-1">Склад</div>
              <div class="text-gray-900 text-sm">{{ receipt.warehouse_name }}<span v-if="receipt.warehouse_address" class="text-gray-400 ml-2">({{ receipt.warehouse_address }})</span></div>
            </div>
            <div>
              <div class="text-gray-500 text-xs mb-1">Статус</div>
              <div class="text-gray-900 text-sm">{{ receipt.status === 'posted' ? 'Проведено' : 'Черновик' }}</div>
            </div>
            <div>
              <div class="text-gray-500 text-xs mb-1">Создано</div>
              <div class="text-gray-900 text-sm">{{ receipt.created_by || '-' }}</div>
            </div>
            <div>
              <div class="text-gray-500 text-xs mb-1">Комментарий</div>
              <div class="text-gray-900 text-sm">{{ receipt.comment || '-' }}</div>
            </div>
            <div>
              <div class="text-gray-500 text-xs mb-1">Накладные расходы</div>
              <div class="text-gray-900 text-sm">{{ receipt.overhead_costs }}</div>
            </div>
            <div>
              <div class="text-gray-500 text-xs mb-1">Валюта</div>
              <div class="text-gray-900 text-sm">{{ userCurrency }}</div>
            </div>
          </div>
          
          <!-- Товары -->
          <div class="mb-6">
            <div class="font-semibold text-gray-800 mb-2">Товары</div>
            <div v-if="loadingPositions" class="flex items-center justify-center py-8">
              <div class="text-center">
                <Loader2 class="animate-spin h-6 w-6 text-blue-600 mx-auto mb-2" />
                <p class="text-gray-600 text-sm">Загрузка товаров...</p>
              </div>
            </div>
            <div v-else>
              <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-3 py-2 text-left font-semibold text-gray-700">Наименование</th>
                    <th class="px-3 py-2 text-center font-semibold text-gray-700">Количество</th>
                    <th class="px-3 py-2 text-center font-semibold text-gray-700">Цена</th>
                    <th class="px-3 py-2 text-center font-semibold text-gray-700">Сумма</th>
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
              <div class="text-right mt-2 text-base font-semibold text-gray-900">Итого: {{ receipt.total }} {{ userCurrency }}</div>
            </div>
          </div>
          
          <!-- Файлы -->
          <div v-if="loadingFiles" class="mb-4">
            <div class="font-semibold text-gray-800 mb-2">Файлы</div>
            <div class="flex items-center justify-center py-4">
              <div class="text-center">
                <Loader2 class="animate-spin h-6 w-6 text-blue-600 mx-auto mb-2" />
                <p class="text-gray-600 text-sm">Загрузка файлов...</p>
              </div>
            </div>
          </div>
          <div v-else-if="files.length > 0" class="mb-4">
            <div class="font-semibold text-gray-800 mb-2">Файлы</div>
            <ul class="list-disc pl-5">
              <li v-for="file in files" :key="file.id" class="mb-1">
                <a :href="file.file_url || '#'" target="_blank" class="text-blue-600 hover:underline text-sm">{{ file.filename }}</a>
                <span class="text-gray-400 text-xs ml-2">({{ file.size_mb }} МБ)</span>
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
    console.error('Ошибка получения валюты пользователя:', error)
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
      console.error('Не удалось найти данные оприходования')
    }
  } catch (error) {
    console.error('Ошибка скачивания PDF:', error)
  }
}

function printDocument() {
  try {
    if (receipt.value) {
      printReceipt(receipt.value, userCurrency.value)
    } else {
      console.error('Не удалось найти данные оприходования')
    }
  } catch (error) {
    console.error('Ошибка печати:', error)
  }
}
</script> 