<template>
  <AdminLayout>
    <!-- Заголовок страницы -->
    <div class="bg-white shadow-sm border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="py-6">
          <h1 class="text-3xl font-bold text-gray-900">Склады</h1>
          <p class="mt-2 text-sm text-gray-600">Просмотр всех складов в системе</p>
        </div>
      </div>
    </div>

    <!-- Основной контент -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      
      <!-- Основной контент -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-xl font-semibold text-gray-900">Список складов</h2>
        </div>

        <!-- Загрузка складов -->
        <div v-if="loading" class="flex items-center justify-center py-12">
          <Loader2 class="animate-spin h-8 w-8 text-blue-600 mr-3" />
          <span class="text-lg text-gray-600">Загрузка складов...</span>
        </div>

        <!-- Пустой список -->
        <div v-else-if="!loading && warehouses.length === 0" class="text-center py-12">
          <Package class="mx-auto h-12 w-12 text-gray-400" />
          <h3 class="mt-2 text-sm font-medium text-gray-900">Склады не найдены</h3>
          <p class="mt-1 text-sm text-gray-500">В системе пока нет складов</p>
        </div>

        <!-- Список складов -->
        <div v-else class="space-y-4">
          <div v-for="warehouse in warehouses" :key="warehouse.id" class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
            <div class="flex-1">
              <h3 class="text-lg font-semibold text-gray-900">{{ warehouse.name }}</h3>
              <p v-if="warehouse.address" class="text-sm text-gray-600 mt-1">{{ warehouse.address }}</p>
              <p v-else class="text-sm text-gray-400 mt-1">Адрес не указан</p>
              <div v-if="warehouse.user" class="text-xs text-gray-500 mt-1">
                Пользователь: {{ warehouse.user.user_name || warehouse.user.first_name }}
                <span v-if="warehouse.user.company_name"> - {{ warehouse.user.company_name }}</span>
              </div>
            </div>
            <div class="flex items-center space-x-2">
              <button 
                @click="viewWarehouse(warehouse.id)"
                class="text-blue-600 hover:text-blue-800 p-2 rounded hover:bg-blue-50 transition-colors cursor-pointer"
                title="Просмотр склада"
              >
                <Eye class="w-4 h-4" />
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Модальное окно просмотра склада -->
    <AdminWarehouseViewModal
      v-if="showWarehouseModal"
      :warehouse-id="selectedWarehouseId"
      @close="showWarehouseModal = false"
    />
  </AdminLayout>
</template>

<script>
import { ref, onMounted } from 'vue'
import api from '@/config/api'
import AdminLayout from '../AdminLayout.vue'
import AdminWarehouseViewModal from './AdminWarehouseViewModal.vue'
import { Eye, Package, Loader2 } from 'lucide-vue-next'

export default {
  name: 'AdminWarehousesPage',
  components: {
    AdminLayout,
    AdminWarehouseViewModal,
    Eye,
    Package,
    Loader2
  },
  setup() {
    const warehouses = ref([])
    const loading = ref(false)
    const showWarehouseModal = ref(false)
    const selectedWarehouseId = ref(null)

    const loadWarehouses = async () => {
      loading.value = true
      try {
        const response = await api.get('/admin/warehouses')
        if (response.data.success) {
          warehouses.value = response.data.data || []
        }
      } catch (error) {
        console.error('Ошибка загрузки складов:', error)
      } finally {
        loading.value = false
      }
    }

    const viewWarehouse = (warehouseId) => {
      selectedWarehouseId.value = warehouseId
      showWarehouseModal.value = true
    }

    onMounted(() => {
      loadWarehouses()
    })

    return {
      warehouses,
      loading,
      showWarehouseModal,
      selectedWarehouseId,
      loadWarehouses,
      viewWarehouse
    }
  }
}
</script> 