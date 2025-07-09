<template>
  <div class="min-h-screen bg-gray-50" style="padding-top: 66px;">
    <ProductsMenu />
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Оприходования</h1>
        <router-link
          to="/products/receipts/create"
          class="flex items-center gap-2 bg-blue-50 border border-blue-200 text-blue-700 font-medium px-4 py-2 rounded text-sm relative group hover:bg-blue-100 transition-colors"
          title="Добавить оприходование"
        >
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          Добавить
          <span class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-900 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-10">
            Добавить оприходование
            <span class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></span>
          </span>
        </router-link>
      </div>
      <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <div v-if="loading" class="text-center py-8 text-gray-500">Загрузка...</div>
        <div v-else>
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
              <tr v-for="receipt in receipts" :key="receipt.id" class="hover:bg-gray-50">
                <td class="px-3 py-2">{{ receipt.number }}</td>
                <td class="px-3 py-2">{{ formatDate(receipt.date) }}</td>
                <td class="px-3 py-2">{{ receipt.organization }}</td>
                <td class="px-3 py-2">
                  <span v-if="receipt.warehouse_name && receipt.warehouse_name.length > 0" class="relative group cursor-pointer">
                    {{ receipt.warehouse_name }}
                    <span v-if="receipt.warehouse_address && receipt.warehouse_address.length > 0" class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-900 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-10">
                      {{ receipt.warehouse_address }}
                      <span class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></span>
                    </span>
                  </span>
                  <span v-else class="text-gray-400">Склад #{{ receipt.warehouse_id }}</span>
                </td>
                <td class="px-3 py-2">
                  <span :class="receipt.status === 'posted' ? 'text-green-600' : 'text-gray-500'">
                    {{ receipt.status === 'posted' ? 'Проведено' : 'Черновик' }}
                  </span>
                </td>
                <td class="px-3 py-2">{{ Number(receipt.total).toFixed(2) }}</td>
                <td class="px-3 py-2">{{ receipt.created_by || '-' }}</td>
                <td class="px-3 py-2">
                  <div class="flex items-center space-x-2">
                    <button @click="viewReceipt(receipt.id)" class="text-gray-600 hover:text-gray-900 p-1 rounded hover:bg-gray-100 transition-colors relative group" title="Просмотр">
                      <Eye class="w-4 h-4" />
                      <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-900 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-10">
                        Просмотр
                        <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
                      </div>
                    </button>
                    <router-link 
                      :to="`/products/receipts/edit/${receipt.id}`"
                      class="text-blue-600 hover:text-blue-800 p-1 rounded hover:bg-blue-50 transition-colors relative group"
                      title="Редактировать"
                    >
                      <Edit class="w-4 h-4" />
                      <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-900 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-10">
                        Редактировать
                        <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
                      </div>
                    </router-link>
                    <button @click="deleteReceipt(receipt.id)" class="text-red-600 hover:text-red-800 p-1 rounded hover:bg-red-50 transition-colors relative group" title="Удалить">
                      <Trash2 class="w-4 h-4" />
                      <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-900 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-10">
                        Удалить
                        <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
                      </div>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
          <div v-if="receipts.length === 0" class="text-center text-gray-500 py-8">Нет оприходований</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import ProductsMenu from './ProductsMenu.vue'
import { apiRequest } from '@/config/api'
import { useRouter } from 'vue-router'
import { Eye, Edit, Trash2 } from 'lucide-vue-next'
const router = useRouter()

const receipts = ref([])
const loading = ref(false)

function formatDate(date) {
  if (!date) return ''
  return new Date(date).toLocaleString('ru-RU')
}

async function fetchReceipts() {
  loading.value = true
  try {
    const res = await apiRequest('/receipts', { method: 'GET' })
    if (res.ok && res.data) {
      receipts.value = Array.isArray(res.data.data) ? res.data.data : []
    } else {
      receipts.value = []
    }
  } finally {
    loading.value = false
  }
}

function goToCreate() {
  router.push('/products/receipts/create')
}

function viewReceipt(id) {
  router.push(`/products/receipts/${id}`)
}

function editReceipt(id) {
  router.push(`/products/receipts/edit/${id}`)
}

async function deleteReceipt(id) {
  if (!confirm('Вы уверены, что хотите удалить оприходование?')) return
  try {
    const res = await apiRequest(`/receipts/${id}`, { method: 'DELETE' })
    if (res.ok && res.data && res.data.success) {
      receipts.value = receipts.value.filter(r => r.id !== id)
    } else {
      alert(res.data?.message || 'Ошибка при удалении')
    }
  } catch (e) {
    alert('Ошибка при удалении')
  }
}

onMounted(fetchReceipts)
</script>

<style scoped>
.tooltip {
  display: none;
  position: absolute;
  bottom: 125%;
  left: 50%;
  transform: translateX(-50%);
  background: #222;
  color: #fff;
  padding: 2px 8px;
  border-radius: 4px;
  font-size: 12px;
  white-space: nowrap;
  z-index: 10;
}
button.group:hover .tooltip {
  display: block;
}
</style> 