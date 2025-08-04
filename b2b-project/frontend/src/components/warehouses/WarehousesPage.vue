<template>
  <div class="min-h-screen bg-gray-50" style="padding-top: 66px;">
    <ProductsMenu />
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900">{{ t('WarehousesPage_1') }}</h1> <!-- Склады -->
        <router-link
          to="/warehouses/create"
          class="flex items-center gap-2 bg-blue-50 border border-blue-200 text-blue-700 font-medium px-4 py-2 rounded text-sm hover:bg-blue-100 transition-colors"
        >
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          {{ t('WarehousesPage_2') }} <!-- Создать склад -->
        </router-link>
      </div>
      
      <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <div v-if="loading" class="flex items-center justify-center py-8">
          <Loader2 class="animate-spin h-6 w-6 text-blue-600 mr-2" />
          <span class="text-sm text-gray-600">{{ t('WarehousesPage_3') }}</span> <!-- Загрузка складов... -->
        </div>
        <div v-else>
          <div class="mb-4 text-sm text-gray-600">
            {{ t('WarehousesPage_4') }} {{ warehouses.length }} <!-- Найдено складов: -->
          </div>
                      <div v-if="warehouses.length === 0" class="text-center text-gray-500 py-8">
              {{ t('WarehousesPage_5') }} <!-- У вас пока нет складов. Создайте первый склад! -->
            </div>
          <div v-else class="space-y-4">
            <div v-for="warehouse in warehouses" :key="warehouse.id" class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
              <div class="flex-1">
                <h3 class="text-lg font-semibold text-gray-900">{{ warehouse.name }}</h3>
                <p v-if="warehouse.address" class="text-sm text-gray-600 mt-1">{{ warehouse.address }}</p>
                <p v-else class="text-sm text-gray-400 mt-1">{{ t('WarehousesPage_6') }}</p> <!-- Адрес не указан -->
              </div>
              <div class="flex items-center space-x-2">
                <router-link 
                  :to="`/warehouses/${warehouse.id}`"
                  class="text-green-600 hover:text-green-800 p-2 rounded hover:bg-green-50 transition-colors cursor-pointer"
                  :title="t('WarehousesPage_7')" 
                >
                  <Eye class="w-4 h-4" />
                </router-link>
                <router-link 
                  :to="`/warehouses/edit/${warehouse.id}`"
                  class="text-blue-600 hover:text-blue-800 p-2 rounded hover:bg-blue-50 transition-colors cursor-pointer"
                  :title="t('WarehousesPage_8')" 
                >
                  <Edit class="w-4 h-4" />
                </router-link>
                <button 
                  @click="openDeleteModal(warehouse.id, warehouse.name)" 
                  class="text-red-600 hover:text-red-800 p-2 rounded hover:bg-red-50 transition-colors cursor-pointer"
                  :title="t('WarehousesPage_9')" 
                >
                  <Trash2 class="w-4 h-4" />
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Модальное окно удаления -->
  <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-white/90">
    <div class="bg-white rounded-lg shadow-lg p-6 max-w-sm w-full relative">
      <div class="text-lg font-semibold mb-2">{{ t('WarehousesPage_10') }}</div> <!-- Удалить склад? -->
      <div class="text-gray-600 mb-4 text-sm">
        {{ t('WarehousesPage_11') }} "{{ warehouseToDelete.name }}"? {{ t('WarehousesPage_12') }} <!-- Вы действительно хотите удалить склад --> <!-- Это действие необратимо. -->
      </div>
      <div class="flex justify-end gap-2 mt-4">
        <button @click="closeDeleteModal" class="px-4 py-2 rounded bg-gray-100 text-gray-700 hover:bg-gray-200 text-sm">{{ t('WarehousesPage_13') }}</button> <!-- Отмена -->
        <button @click="deleteWarehouseConfirmed" :disabled="deleting" class="px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700 text-sm flex items-center min-w-[90px] justify-center">
          <Loader2 v-if="deleting" class="animate-spin h-4 w-4 mr-2" />
          <span v-if="!deleting">{{ t('WarehousesPage_14') }}</span> <!-- Удалить -->
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import ProductsMenu from '../products/ProductsMenu.vue'
import { apiRequest } from '@/config/api'
import { useRouter } from 'vue-router'
import { Edit, Trash2, Loader2, Eye } from 'lucide-vue-next'
import toastr from 'toastr'
import { t } from '@/locales'

// Устанавливаем заголовок страницы
document.title = 'B2B SKLAD - Склады'

const router = useRouter()

const warehouses = ref([])
const loading = ref(false)
const showDeleteModal = ref(false)
const deleting = ref(false)
const warehouseToDelete = ref({})

function openDeleteModal(id, name) {
  showDeleteModal.value = true
  warehouseToDelete.value = { id, name }
}

function closeDeleteModal() {
  showDeleteModal.value = false
  warehouseToDelete.value = {}
}

async function fetchWarehouses() {
  loading.value = true
  try {
    const res = await apiRequest('/warehouses', { method: 'GET' })
    if (res.ok && res.data.success) {
      warehouses.value = res.data.data || []
    } else {
      warehouses.value = []
    }
  } catch (error) {
    console.error(t('WarehousesPage_17') + error) // Ошибка загрузки складов:
    warehouses.value = []
  } finally {
    loading.value = false
  }
}

async function deleteWarehouseConfirmed() {
  if (!warehouseToDelete.value.id) return
  deleting.value = true
  try {
    const res = await apiRequest(`/warehouses/${warehouseToDelete.value.id}`, { method: 'DELETE' })
    if (res.ok && res.data && res.data.success) {
      warehouses.value = warehouses.value.filter(w => w.id !== warehouseToDelete.value.id)
      toastr.success(t('WarehousesPage_15')) // Склад успешно удален
      closeDeleteModal()
    } else {
      toastr.error(res.data?.message || t('WarehousesPage_16')) // Ошибка при удалении склада
    }
  } catch (e) {
    toastr.error(t('WarehousesPage_16')) // Ошибка при удалении склада
  } finally {
    deleting.value = false
  }
}

onMounted(fetchWarehouses)
</script> 