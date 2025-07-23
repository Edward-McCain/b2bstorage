<template>
  <div class="min-h-screen bg-gray-50" style="padding-top: 66px;">
    <!-- Внутреннее меню навигации -->
    <ProductsMenu />
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Заголовок страницы -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Остатки</h1>
        <p class="mt-2 text-gray-600">Просмотр складских остатков товаров</p>
      </div>
      
      <!-- Основной контент -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-xl font-semibold text-gray-900">Остатки товаров</h2>
          <div class="flex gap-2">
            <button
              @click="loadSummary"
              class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm hidden"
            >
              Сводка
            </button>
          </div>
        </div>

        <!-- Фильтры -->
        <div class="bg-gray-50 rounded-lg p-4 mb-6">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Склад</label>
              <Multiselect
                v-model="filters.warehouse_id"
                :options="warehouseOptions"
                label="label"
                value="value"
                :object="false"
                placeholder="Все склады"
                :max-height="400"
                class="w-full text-sm multiselect-custom"
                :loading="loadingWarehouses"
                :disabled="loadingWarehouses"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Поиск товара</label>
              <input
                v-model="filters.search"
                type="text"
                placeholder="Название товара..."
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Мин. остаток</label>
              <input
                v-model.number="filters.min_quantity"
                type="number"
                min="0"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Макс. остаток</label>
              <input
                v-model.number="filters.max_quantity"
                type="number"
                min="0"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
              />
            </div>
          </div>
          <div class="mt-4 flex gap-2">
            <button
              @click="loadBalances"
              :disabled="loading"
              class="bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white px-4 py-2 rounded-lg text-sm flex items-center gap-2"
            >
              <svg v-if="loading" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span v-if="loading">Загрузка...</span>
              <span v-else>Применить фильтры</span>
            </button>
            <button
              @click="clearFilters"
              :disabled="loading"
              class="bg-gray-500 hover:bg-gray-600 disabled:bg-gray-400 text-white px-4 py-2 rounded-lg text-sm flex items-center gap-2"
            >
              <svg v-if="loading" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span v-if="loading">Загрузка...</span>
              <span v-else>Сбросить</span>
            </button>
          </div>
        </div>

        <!-- Сводка -->
        <div v-if="loadingSummary" class="bg-blue-50 rounded-lg p-4 mb-6">
          <!-- <h3 class="text-lg font-medium text-gray-900 mb-4">Сводка по остаткам</h3> -->
          <div class="flex justify-center items-center py-8">
            <div class="text-center">
              <svg class="animate-spin h-8 w-8 text-blue-600 mx-auto mb-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <div class="text-gray-600">Загрузка сводки...</div>
            </div>
          </div>
        </div>
        
        <div v-else-if="summary" class="bg-blue-50 rounded-lg p-4 mb-6">
          <!-- <h3 class="text-lg font-medium text-gray-900 mb-4">Сводка по остаткам</h3> -->
          <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
            <div class="text-center">
              <div class="text-2xl font-bold text-blue-600">{{ summary.total_products }}</div>
              <div class="text-sm text-gray-600">Товаров</div>
            </div>
            <div class="text-center">
              <div class="text-2xl font-bold text-green-600">{{ summary.total_warehouses }}</div>
              <div class="text-sm text-gray-600">Складов</div>
            </div>
            <div class="text-center">
              <div class="text-2xl font-bold text-purple-600">{{ summary.total_quantity }}</div>
              <div class="text-sm text-gray-600">Общее количество</div>
            </div>
            <div class="text-center">
              <div class="text-2xl font-bold text-indigo-600">{{ formatCurrency(summary.total_value) }}</div>
              <div class="text-sm text-gray-600">Общая стоимость</div>
            </div>
            <div class="text-center">
              <div class="text-2xl font-bold text-orange-600">{{ summary.low_stock_items }}</div>
              <div class="text-sm text-gray-600">Низкий остаток</div>
            </div>
            <div class="text-center">
              <div class="text-2xl font-bold text-red-600">{{ summary.out_of_stock_items }}</div>
              <div class="text-sm text-gray-600">Нет в наличии</div>
            </div>
          </div>
        </div>

        <!-- Список остатков -->
        <div v-if="loading" class="flex justify-center items-center py-12">
          <div class="text-center">
            <svg class="animate-spin h-8 w-8 text-blue-600 mx-auto mb-4" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <div class="text-gray-600">Загрузка остатков...</div>
          </div>
        </div>
        
        <div v-else class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">
                  Товар
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">
                  Склад
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">
                  Остаток
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">
                  Цена
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">
                  Итого
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">
                  Статус
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">
                  Действия
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="balance in balances" :key="`${balance.product_id}-${balance.warehouse_id}`" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10">
                      <img
                        v-if="balance.product?.images?.length > 0"
                        :src="balance.product.images[0].image_url"
                        :alt="balance.product.images[0].alt_text || balance.product.name"
                        class="h-10 w-10 rounded-lg object-cover"
                      />
                      <div v-else class="h-10 w-10 bg-gray-200 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                      </div>
                    </div>
                    <div class="ml-4">
                      <div class="text-sm font-medium text-gray-900">{{ balance.product?.name }}</div>
                      <div class="text-sm text-gray-500">{{ balance.product?.category?.name }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ balance.warehouse?.name }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  <span :class="getQuantityClass(balance.quantity)">
                    {{ balance.quantity }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ formatCurrency(balance.product?.price) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ formatCurrency(balance.quantity * (balance.product?.price || 0)) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="getStatusClass(balance.quantity)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                    {{ getStatusText(balance.quantity) }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <button
                    @click="viewMovements(balance.product_id, balance.warehouse_id)"
                    class="text-blue-600 hover:text-blue-900 cursor-pointer"
                  >
                    Движение
                  </button>
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
              @click="loadBalances(pagination.current_page - 1)"
              :disabled="loading"
              class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:bg-gray-100 disabled:text-gray-400 text-sm flex items-center gap-2"
            >
              <svg v-if="loading" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span v-if="loading">Загрузка...</span>
              <span v-else>Назад</span>
            </button>
            <button
              v-if="pagination.next_page_url"
              @click="loadBalances(pagination.current_page + 1)"
              :disabled="loading"
              class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:bg-gray-100 disabled:text-gray-400 text-sm flex items-center gap-2"
            >
              <svg v-if="loading" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span v-if="loading">Загрузка...</span>
              <span v-else>Вперед</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Модальное окно движения товаров -->
    <MovementsModal
      v-if="showMovementsModal"
      :product-id="selectedProductId"
      :warehouse-id="selectedWarehouseId"
      @close="showMovementsModal = false"
    />
  </div>
</template>

<script>
import { ref, reactive, onMounted, computed } from 'vue'
import api from '@/config/api'
import ProductsMenu from './ProductsMenu.vue'
import MovementsModal from './MovementsModal.vue'
import Multiselect from '@vueform/multiselect'
import '@vueform/multiselect/themes/default.css'

export default {
  name: 'BalancesPage',
  components: {
    ProductsMenu,
    MovementsModal,
    Multiselect
  },
  setup() {
    const balances = ref([])
    const warehouses = ref([])
    const summary = ref(null)
    const pagination = ref(null)
    const loading = ref(false)
    const loadingSummary = ref(false)
    const currency = ref('UZS') // по умолчанию UZS

    const filters = reactive({
      warehouse_id: '',
      search: '',
      min_quantity: '',
      max_quantity: ''
    })

    const showMovementsModal = ref(false)
    const selectedProductId = ref(null)
    const selectedWarehouseId = ref(null)
    const loadingWarehouses = ref(false)

    // Computed свойства
    const warehouseOptions = computed(() => {
      return warehouses.value.map(w => ({
        label: w.name,
        value: w.id
      }))
    })

    const loadBalances = async (page = 1) => {
      loading.value = true
      try {
        const requestData = { page, ...filters }
        const response = await api.post('/balances', requestData)
        balances.value = response.data.data
        pagination.value = response.data
      } catch (error) {
        console.error('Ошибка загрузки остатков:', error)
      } finally {
        loading.value = false
      }
    }

    const loadWarehouses = async () => {
      try {
        loadingWarehouses.value = true
        const response = await api.get('/warehouses')
        warehouses.value = response.data.data || []
      } catch (error) {
        console.error('Ошибка загрузки складов:', error)
      } finally {
        loadingWarehouses.value = false
      }
    }

    const loadSummary = async () => {
      loadingSummary.value = true
      try {
        const response = await api.post('/balances/summary', filters)
        summary.value = response.data.summary
        currency.value = response.data.currency || 'UZS'
      } catch (error) {
        console.error('Ошибка загрузки сводки:', error)
      } finally {
        loadingSummary.value = false
      }
    }

    const clearFilters = () => {
      Object.keys(filters).forEach(key => {
        filters[key] = ''
      })
      loadBalances()
      loadSummary() // Обновляем сводку вместо уничтожения
    }

    const viewMovements = (productId, warehouseId) => {
      selectedProductId.value = productId
      selectedWarehouseId.value = warehouseId
      showMovementsModal.value = true
    }

    const formatCurrency = (amount) => {
      if (!amount) return '0'
      return new Intl.NumberFormat('ru-RU', {
        style: 'currency',
        currency: currency.value
      }).format(amount)
    }

    const getQuantityClass = (quantity) => {
      if (quantity === 0) return 'text-red-600 font-semibold'
      if (quantity <= 10) return 'text-orange-600 font-semibold'
      return 'text-green-600 font-semibold'
    }

    const getStatusClass = (quantity) => {
      if (quantity === 0) return 'bg-red-100 text-red-800'
      if (quantity <= 10) return 'bg-orange-100 text-orange-800'
      return 'bg-green-100 text-green-800'
    }

    const getStatusText = (quantity) => {
      if (quantity === 0) return 'Нет в наличии'
      if (quantity <= 10) return 'Низкий остаток'
      return 'В наличии'
    }

    onMounted(() => {
      loadBalances()
      loadWarehouses()
      loadSummary() // Автоматически загружаем сводку при загрузке страницы
    })

    return {
      balances,
      warehouses,
      summary,
      pagination,
      loading,
      loadingSummary,
      filters,
      showMovementsModal,
      selectedProductId,
      selectedWarehouseId,
      loadingWarehouses,
      warehouseOptions,
      loadBalances,
      loadWarehouses,
      loadSummary,
      clearFilters,
      viewMovements,
      formatCurrency,
      getQuantityClass,
      getStatusClass,
      getStatusText,
      currency
    }
  }
}
</script> 