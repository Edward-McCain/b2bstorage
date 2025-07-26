<template>
  <div class="min-h-screen bg-gray-50" style="padding-top: 66px;">
    <ProductsMenu />
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
      <div class="bg-white rounded-xl shadow p-6 relative">
        
        <!-- Прелоадер -->
        <div v-if="loading" class="flex items-center justify-center py-20">
          <div class="text-center">
            <Loader2 class="animate-spin h-8 w-8 text-blue-600 mx-auto mb-4" />
            <p class="text-gray-600 text-sm">Загрузка списания...</p>
          </div>
        </div>

        <!-- Контент -->
        <div v-else>
          <div class="mb-6">
            <h1 class="text-xl font-bold text-gray-900 mb-1">Списание номер {{ writeOff.number }}</h1>
            <div class="text-gray-500 text-sm">от {{ formatDate(writeOff.date) }}</div>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
              <div class="text-gray-500 text-xs mb-1">Организация</div>
              <div class="text-gray-900 text-sm">{{ writeOff.organization }}</div>
            </div>
            <div>
              <div class="text-gray-500 text-xs mb-1">Склад</div>
              <div class="text-gray-900 text-sm">{{ writeOff.warehouse_name }}<span v-if="writeOff.warehouse_address" class="text-gray-400 ml-2">({{ writeOff.warehouse_address }})</span></div>
            </div>
            <div>
              <div class="text-gray-500 text-xs mb-1">Статус</div>
              <div class="text-gray-900 text-sm">{{ writeOff.status === 'posted' ? 'Проведено' : 'Черновик' }}</div>
            </div>
            <div>
              <div class="text-gray-500 text-xs mb-1">Создано</div>
              <div class="text-gray-900 text-sm">{{ writeOff.created_by || '-' }}</div>
            </div>
            <div>
              <div class="text-gray-500 text-xs mb-1">Комментарий</div>
              <div class="text-gray-900 text-sm">{{ writeOff.comment || '-' }}</div>
            </div>
            <div>
              <div class="text-gray-500 text-xs mb-1">Накладные расходы</div>
              <div class="text-gray-900 text-sm">{{ writeOff.overhead_costs || '0.00' }}</div>
            </div>
          </div>
          
          <!-- Товары -->
          <div class="mb-6">
            <div class="font-semibold text-gray-800 mb-2">Товары</div>
            <div v-if="writeOff.positions && writeOff.positions.length > 0">
              <table class="min-w-full divide-y divide-gray-200 text-xs">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-2 py-1.5 text-left font-semibold text-gray-700">Наименование</th>
                    <th class="px-1 py-1.5 text-center font-semibold text-gray-700">Кол-во</th>
                    <th class="px-1 py-1.5 text-center font-semibold text-gray-700">Цена</th>
                    <th class="px-1 py-1.5 text-center font-semibold text-gray-700">Сумма</th>
                    <th class="px-1 py-1.5 text-center font-semibold text-gray-700">Причина</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="pos in writeOff.positions" :key="pos.id" class="hover:bg-gray-50">
                    <td class="px-2 py-1.5">{{ pos.name }}</td>
                    <td class="px-1 py-1.5 text-center">{{ parseFloat(pos.quantity) }}</td>
                    <td class="px-1 py-1.5 text-center">{{ parseFloat(pos.price).toFixed(2) }}</td>
                    <td class="px-1 py-1.5 text-center">{{ (parseFloat(pos.quantity) * parseFloat(pos.price)).toFixed(2) }}</td>
                    <td class="px-1 py-1.5 text-center">{{ pos.reason || '-' }}</td>
                  </tr>
                </tbody>
              </table>
              <div class="text-right mt-2 text-base font-semibold text-gray-900">Итого: {{ parseFloat(writeOff.total).toFixed(2) }}</div>
            </div>
            <div v-else class="text-center text-gray-500 py-8">
              Товары не найдены
            </div>
          </div>
          
          <!-- Файлы -->
          <div v-if="writeOff.files && writeOff.files.length > 0" class="mb-4">
            <div class="font-semibold text-gray-800 mb-2">Файлы</div>
            <ul class="list-disc pl-5">
              <li v-for="file in writeOff.files" :key="file.id" class="mb-1">
                <a :href="getFileUrl(file.file_url)" target="_blank" class="text-blue-600 hover:underline text-sm">{{ file.filename }}</a>
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
import ProductsMenu from './ProductsMenu.vue'
import { useRouter, useRoute } from 'vue-router'
import { ref, onMounted } from 'vue'
import { apiRequest } from '@/config/api'
import toastr from 'toastr'
import { Loader2, Pencil } from 'lucide-vue-next'

// Устанавливаем заголовок страницы
document.title = 'B2B SKLAD - Списания'

const router = useRouter()
const route = useRoute()
const writeOffId = route.params.id

const loading = ref(true)
const writeOff = ref({})

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
    } else {
      toastr.error('Списание не найдено')
      router.push('/products/write-offs')
    }
  } catch (error) {
    console.error('Ошибка загрузки списания:', error)
    toastr.error('Ошибка при загрузке списания')
    router.push('/products/write-offs')
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadWriteOff()
})
</script> 