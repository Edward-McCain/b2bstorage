<template>
  <div class="min-h-screen bg-gray-50" style="padding-top: 66px;">
    <!-- Внутреннее меню навигации -->
    <ProductsMenu />
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Заголовок страницы -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Перемещения</h1>
        <p class="mt-2 text-gray-600">Управление перемещениями товаров между складами</p>
      </div>
      
      <!-- Основной контент -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-xl font-semibold text-gray-900">Список перемещений</h2>
          <router-link
            to="/products/transfers/create"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 text-sm"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Создать перемещение
          </router-link>
        </div>

        <!-- Фильтры -->
        <div class="bg-gray-50 rounded-lg p-4 mb-6">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Склад</label>
              <Multiselect
                v-model="filters.warehouse_id"
                :options="warehouseOptions"
                label="label"
                value="value"
                :object="false"
                placeholder="Выберите склад"
                :max-height="400"
                class="w-full text-sm multiselect-custom"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Дата с</label>
              <input
                v-model="filters.date_from"
                type="date"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Дата по</label>
              <input
                v-model="filters.date_to"
                type="date"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
              />
            </div>
          </div>
          <div class="mt-4 flex gap-2">
            <button
              @click="() => filterTransfers(1)"
              class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm"
            >
              Применить фильтры
            </button>
            <button
              @click="clearFilters"
              class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm"
            >
              Сбросить
            </button>
          </div>
        </div>

        <!-- Загрузка -->
        <div v-if="loading" class="flex items-center justify-center py-12">
          <Loader2 class="animate-spin h-8 w-8 text-blue-600 mr-3" />
          <span class="text-sm text-gray-600">Загрузка перемещений...</span>
        </div>

        <!-- Список перемещений -->
        <div v-else class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 tracking-wider">
                  ID
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 tracking-wider">
                  От склада
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 tracking-wider">
                  В склад
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 tracking-wider">
                  Дата
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 tracking-wider">
                  Статус
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 tracking-wider">
                  Количество товаров
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 tracking-wider">
                  Создал
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 tracking-wider">
                  Действия
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="transfer in transfers" :key="transfer.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                  #{{ transfer.id }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ transfer.from_warehouse?.name }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ transfer.to_warehouse?.name }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ formatDate(transfer.transfer_date) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span
                    :class="getStatusClass(transfer.status)"
                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                  >
                    {{ transfer.status_text }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ transfer.total_items || 0 }} ед.
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ transfer.created_by_user?.name }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <div class="flex gap-2">
                    <button
                      @click="viewTransfer(transfer)"
                      class="text-blue-600 hover:text-blue-900"
                    >
                      Просмотр
                    </button>
                    <button
                      v-if="transfer.status === 'draft'"
                      @click="editTransfer(transfer)"
                      class="text-green-600 hover:text-green-900"
                    >
                      Редактировать
                    </button>
                    <button
                      v-if="transfer.can_be_confirmed"
                      @click="confirmTransfer(transfer.id)"
                      class="text-orange-600 hover:text-orange-900"
                    >
                      Подтвердить
                    </button>
                    <button
                      v-if="transfer.can_be_completed"
                      @click="completeTransfer(transfer)"
                      class="text-purple-600 hover:text-purple-900"
                    >
                      Выполнить
                    </button>
                    <button
                      v-if="transfer.can_be_cancelled"
                      @click="cancelTransfer(transfer.id)"
                      class="text-red-600 hover:text-red-900"
                    >
                      Отменить
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Пагинация -->
        <div v-if="pagination" class="mt-6 flex justify-between items-center">
          <div class="text-sm text-gray-700">
            Показано {{ pagination.from }}-{{ pagination.to }} из {{ pagination.total }}
          </div>
          <div class="flex gap-2">
                    <button
          v-if="pagination.prev_page_url"
          @click="loadTransfers(pagination.current_page - 1)"
          class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 text-sm"
        >
          Назад
        </button>
        <button
          v-if="pagination.next_page_url"
          @click="loadTransfers(pagination.current_page + 1)"
          class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 text-sm"
        >
          Вперед
        </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Модальное окно просмотра -->
    <TransferViewModal
      v-if="showViewModal"
      :transfer="viewingTransfer"
      @close="showViewModal = false"
    />

    <!-- Модальное окно выполнения -->
    <TransferCompleteModal
      v-if="showCompleteModal"
      :transfer="completingTransfer"
      @close="showCompleteModal = false"
      @completed="onTransferCompleted"
    />
  </div>
</template>

<script>
import { ref, reactive, onMounted, computed } from 'vue'
import api from '@/config/api'
import ProductsMenu from './ProductsMenu.vue'
import TransferViewModal from './TransferViewModal.vue'
import TransferCompleteModal from './TransferCompleteModal.vue'
import Multiselect from '@vueform/multiselect'
import '@vueform/multiselect/themes/default.css'
import { Loader2 } from 'lucide-vue-next'

export default {
  name: 'TransfersPage',
  components: {
    ProductsMenu,
    TransferViewModal,
    TransferCompleteModal,
    Multiselect,
    Loader2
  },
  setup() {
    const transfers = ref([])
    const warehouses = ref([])
    const statuses = ref({})
    const pagination = ref(null)
    const loading = ref(false)

    const filters = reactive({
      warehouse_id: '',
      date_from: '',
      date_to: ''
    })

    const showViewModal = ref(false)
    const showCompleteModal = ref(false)
    const viewingTransfer = ref(null)
    const completingTransfer = ref(null)

    // Computed свойства для опций фильтров

    const warehouseOptions = computed(() => {
      const options = [
        { label: 'Все склады', value: '' }
      ]
      warehouses.value.forEach(warehouse => {
        options.push({ label: warehouse.name, value: warehouse.id })
      })
      return options
    })

    const loadTransfers = async (page = 1) => {
      loading.value = true
      try {
        const response = await api.get('/transfers', {
          params: { page }
        })
        transfers.value = response.data.transfers.data
        pagination.value = response.data.transfers
        statuses.value = response.data.statuses
      } catch (error) {
        console.error('Ошибка загрузки перемещений:', error)
      } finally {
        loading.value = false
      }
    }

    const filterTransfers = async (page = 1) => {
      loading.value = true
      try {
        // Убеждаемся, что page является числом
        const pageNumber = typeof page === 'number' ? page : 1
        const requestData = { 
          page: pageNumber, 
          ...filters 
        }
        const response = await api.post('/transfers/filter', requestData)
        transfers.value = response.data.transfers.data
        pagination.value = response.data.transfers
        statuses.value = response.data.statuses
      } catch (error) {
        console.error('Ошибка фильтрации перемещений:', error)
      } finally {
        loading.value = false
      }
    }

    const loadWarehouses = async () => {
      try {
        const response = await api.get('/warehouses')
        warehouses.value = response.data.data || []
      } catch (error) {
        console.error('Ошибка загрузки складов:', error)
      }
    }

    const clearFilters = () => {
      Object.keys(filters).forEach(key => {
        filters[key] = ''
      })
      loadTransfers()
    }

    const viewTransfer = (transfer) => {
      viewingTransfer.value = transfer
      showViewModal.value = true
    }

    const editTransfer = (transfer) => {
      // Перенаправляем на страницу редактирования
      window.location.href = `/products/transfers/edit/${transfer.id}`
    }

    const confirmTransfer = async (id) => {
      try {
        await api.post(`/transfers/${id}/confirm`)
        await loadTransfers()
      } catch (error) {
        console.error('Ошибка подтверждения перемещения:', error)
      }
    }

    const completeTransfer = (transfer) => {
      completingTransfer.value = transfer
      showCompleteModal.value = true
    }

    const cancelTransfer = async (id) => {
      if (!confirm('Вы уверены, что хотите отменить это перемещение?')) {
        return
      }
      try {
        await api.post(`/transfers/${id}/cancel`)
        await loadTransfers()
      } catch (error) {
        console.error('Ошибка отмены перемещения:', error)
      }
    }

    const onTransferCompleted = () => {
      showCompleteModal.value = false
      completingTransfer.value = null
      loadTransfers()
    }

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

    const getStatusClass = (status) => {
      const classes = {
        draft: 'bg-gray-100 text-gray-800',
        confirmed: 'bg-blue-100 text-blue-800',
        completed: 'bg-green-100 text-green-800',
        cancelled: 'bg-red-100 text-red-800'
      }
      return classes[status] || 'bg-gray-100 text-gray-800'
    }

    onMounted(() => {
      loadTransfers()
      loadWarehouses()
    })

    return {
      transfers,
      warehouses,
      statuses,
      pagination,
      loading,
      filters,
      showViewModal,
      showCompleteModal,
      viewingTransfer,
      completingTransfer,
      warehouseOptions,
      loadTransfers,
      filterTransfers,
      loadWarehouses,
      clearFilters,
      viewTransfer,
      editTransfer,
      confirmTransfer,
      completeTransfer,
      cancelTransfer,
      onTransferCompleted,
      formatDate,
      getStatusClass
    }
  }
}
</script> 