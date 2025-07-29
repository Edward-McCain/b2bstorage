<template>
  <AdminLayout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Заголовок страницы -->
      <div class="mb-8">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">Оприходование №{{ receipt?.number }}</h1>
            <p class="mt-2 text-gray-600">Детальная информация об оприходовании</p>
          </div>
          <div class="flex items-center gap-2">
            <button 
              @click="goBack"
              class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium px-4 py-2 rounded-lg text-sm transition"
            >
              Назад к списку
            </button>
          </div>
        </div>
      </div>
      
      <div v-if="loading" class="flex items-center justify-center py-12">
        <Loader2 class="animate-spin h-8 w-8 text-blue-600 mr-3" />
        <span class="text-lg text-gray-600">Загрузка оприходования...</span>
      </div>
      
      <div v-else-if="receipt" class="space-y-6">
        <!-- Основная информация -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
          <h2 class="text-xl font-semibold text-gray-900 mb-4">Основная информация</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Номер</label>
              <p class="text-sm text-gray-900">{{ receipt.number }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Дата</label>
              <p class="text-sm text-gray-900">{{ formatDate(receipt.date) }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Организация</label>
              <p class="text-sm text-gray-900">{{ receipt.organization }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Проект</label>
              <p class="text-sm text-gray-900">{{ receipt.project || 'Не указан' }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Склад</label>
              <p class="text-sm text-gray-900">{{ receipt.warehouse_name }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Статус</label>
              <span 
                :class="{
                  'px-2 py-1 text-xs rounded-full': true,
                  'bg-yellow-100 text-yellow-800': receipt.status === 'draft',
                  'bg-green-100 text-green-800': receipt.status === 'posted'
                }"
              >
                {{ receipt.status === 'draft' ? 'Черновик' : 'Проведено' }}
              </span>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Общая сумма</label>
              <p class="text-sm text-gray-900 font-semibold">{{ formatPrice(receipt.total) }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Накладные расходы</label>
              <p class="text-sm text-gray-900">{{ formatPrice(receipt.overhead_costs) }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Комментарий</label>
              <p class="text-sm text-gray-900">{{ receipt.comment || 'Нет комментария' }}</p>
            </div>
          </div>
        </div>

        <!-- Информация о пользователе -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
          <h2 class="text-xl font-semibold text-gray-900 mb-4">Пользователь</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Имя пользователя</label>
              <p class="text-sm text-gray-900">{{ receipt.user?.first_name }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
              <p class="text-sm text-gray-900">{{ receipt.user?.email }}</p>
            </div>
            <div v-if="receipt.user?.phone_number">
              <label class="block text-sm font-medium text-gray-700 mb-1">Телефон</label>
              <p class="text-sm text-gray-900">{{ receipt.user.phone_number }}</p>
            </div>
            <div v-if="receipt.user?.company_name">
              <label class="block text-sm font-medium text-gray-700 mb-1">Компания</label>
              <p class="text-sm text-gray-900">{{ receipt.user.company_name }}</p>
            </div>
            <div v-if="receipt.user?.inn">
              <label class="block text-sm font-medium text-gray-700 mb-1">ИНН</label>
              <p class="text-sm text-gray-900">{{ receipt.user.inn }}</p>
            </div>
          </div>
        </div>

        <!-- Позиции -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
          <h2 class="text-xl font-semibold text-gray-900 mb-4">Позиции</h2>
          <div v-if="receipt.positions && receipt.positions.length > 0">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
              <thead>
                <tr class="bg-gray-50">
                  <th class="px-3 py-2 text-left font-semibold text-gray-700">Наименование</th>
                  <th class="px-3 py-2 text-center font-semibold text-gray-700">Код</th>
                  <th class="px-3 py-2 text-center font-semibold text-gray-700">Количество</th>
                  <th class="px-3 py-2 text-center font-semibold text-gray-700">Цена</th>
                  <th class="px-3 py-2 text-center font-semibold text-gray-700">Сумма</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="position in receipt.positions" :key="position.id" class="hover:bg-gray-50">
                  <td class="px-3 py-2 text-gray-900">{{ position.name }}</td>
                  <td class="px-3 py-2 text-center text-gray-900">{{ position.code }}</td>
                  <td class="px-3 py-2 text-center text-gray-900">{{ parseFloat(position.quantity) }}</td>
                  <td class="px-3 py-2 text-center text-gray-900">{{ position.price }}</td>
                  <td class="px-3 py-2 text-center text-gray-900 font-semibold">{{ position.amount }}</td>
                </tr>
              </tbody>
            </table>
            <div class="text-right mt-2 text-base font-semibold text-gray-900">Итого: {{ receipt.total }}</div>
          </div>
          <div v-else class="text-center py-8 text-gray-500">
            Позиции не найдены
          </div>
        </div>

        <!-- Файлы -->
        <div v-if="receipt.files && receipt.files.length > 0" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
          <h2 class="text-xl font-semibold text-gray-900 mb-4">Файлы</h2>
          <ul class="list-disc pl-5">
            <li v-for="file in receipt.files" :key="file.id" class="mb-1">
              <a :href="file.file_url || '#'" target="_blank" class="text-blue-600 hover:underline text-sm">{{ file.filename }}</a>
              <span class="text-gray-400 text-xs ml-2">({{ file.size_mb }} МБ)</span>
            </li>
          </ul>
        </div>
      </div>
      
      <div v-else class="text-center py-12">
        <div class="mx-auto h-12 w-12 text-gray-400 mb-4">
          <AlertCircle class="h-12 w-12" />
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Оприходование не найдено</h3>
        <p class="text-gray-500">Запрашиваемое оприходование не существует или было удалено.</p>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import AdminLayout from '../AdminLayout.vue'
import { apiRequest } from '@/config/api'
import { Loader2, AlertCircle, Download, Printer } from 'lucide-vue-next'
import { generatePDF, printElement, generatePDFSimple, generatePDFWithCanvas } from '@/utils/printUtils'

// Устанавливаем заголовок страницы
document.title = 'B2B SKLAD - Админ - Просмотр оприходования'

const route = useRoute()
const router = useRouter()

const receipt = ref(null)
const loading = ref(true)

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



function goBack() {
  router.push('/admin/products/receipts')
}

async function fetchReceipt() {
  loading.value = true
  try {
    const receiptId = route.params.id
    const res = await apiRequest(`/admin/receipts/${receiptId}`, { method: 'GET' })
    
    if (res.ok && res.data && res.data.success) {
      receipt.value = res.data.data
    } else {
      receipt.value = null
    }
  } catch (error) {
    console.error('Ошибка загрузки оприходования:', error)
    receipt.value = null
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchReceipt()
})

async function downloadPDF() {
  try {
    const contentElement = document.querySelector('.space-y-6')
    if (contentElement) {
      const filename = `admin-receipt-${receipt.value?.number || 'receipt'}.pdf`
      const title = `Оприходование №${receipt.value?.number}`
      await generatePDFWithCanvas(contentElement, filename, title)
    } else {
      console.error('Не удалось найти контент для скачивания')
    }
  } catch (error) {
    console.error('Ошибка скачивания PDF:', error)
  }
}

function printDocument() {
  try {
    const contentElement = document.querySelector('.space-y-6')
    if (contentElement) {
      printElement(contentElement)
    } else {
      console.error('Не удалось найти контент для печати')
    }
  } catch (error) {
    console.error('Ошибка печати:', error)
  }
}
</script> 