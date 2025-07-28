<template>
  <div class="min-h-screen bg-gray-50" style="padding-top: 66px;">
    <!-- Внутреннее меню навигации -->
    <ProductsMenu />
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Заголовок страницы -->
      <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Остатки</h1>
        <div class="flex items-center gap-2">
          <button
            @click="toggleFilters"
            class="flex items-center gap-2 text-gray-700 font-medium px-4 py-2 rounded text-sm hover:bg-gray-100 transition-colors cursor-pointer"
          >
            <Filter v-if="!showFilters" class="w-4 h-4" />
            <FunnelX v-else class="w-4 h-4" />
          </button>
        </div>
      </div>
      
      <!-- Фильтры и поиск -->
      <div v-if="showFilters" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div>
            <label class="block text-sm text-gray-700 mb-1">Склад</label>
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
            <label class="block text-sm text-gray-700 mb-1">Поиск товара</label>
            <input
              v-model="filters.search"
              type="text"
              placeholder="Название товара..."
              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm"
            />
          </div>
          <div>
            <label class="block text-sm text-gray-700 mb-1">Мин. остаток</label>
            <input
              v-model.number="filters.min_quantity"
              type="number"
              min="0"
              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm"
            />
          </div>
          <div>
            <label class="block text-sm text-gray-700 mb-1">Макс. остаток</label>
            <input
              v-model.number="filters.max_quantity"
              type="number"
              min="0"
              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm"
            />
          </div>
        </div>
        <div class="mt-4 flex gap-2">
          <button
            @click="loadBalances"
            :disabled="loading"
            class="bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white px-4 py-2 rounded-lg text-sm transition"
          >
            Применить фильтры
          </button>
          <button
            @click="clearFilters"
            :disabled="loading"
            class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm transition"
          >
            Сбросить
          </button>
        </div>
      </div>

      <!-- Основной контент -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">

        <!-- Сводка -->
        <div v-if="loadingSummary" class="bg-blue-50 rounded-lg p-4 mb-6">
          <!-- <h3 class="text-lg font-medium text-gray-900 mb-4">Сводка по остаткам</h3> -->
          <div class="flex justify-center items-center py-8">
            <div class="text-center">
              <Loader2 class="animate-spin h-8 w-8 text-blue-600 mx-auto mb-4" />
              <div class="text-gray-600">Загрузка сводки...</div>
            </div>
          </div>
        </div>
        
        <div v-else-if="summary" class="bg-blue-50 rounded-lg p-4 mb-6">
          <!-- <h3 class="text-lg font-medium text-gray-900 mb-4">Сводка по остаткам</h3> -->
          <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
            <div class="text-center">
              <div class="text-2xl font-bold text-blue-600" style="font-size: 20px;">{{ summary.total_products }}</div>
              <div class="text-sm text-gray-600">Товаров</div>
            </div>
            <div class="text-center">
              <div class="text-2xl font-bold text-green-600" style="font-size: 20px;">{{ summary.total_warehouses }}</div>
              <div class="text-sm text-gray-600">Складов</div>
            </div>
            <div class="text-center">
              <div class="text-2xl font-bold text-purple-600" style="font-size: 20px;">{{ summary.total_quantity }}</div>
              <div class="text-sm text-gray-600">Общее количество</div>
            </div>
            <div class="text-center">
              <div class="text-2xl font-bold text-indigo-600" style="font-size: 20px;">{{ formatCurrency(summary.total_value) }}</div>
              <div class="text-sm text-gray-600">Общая стоимость</div>
            </div>
            <div class="text-center">
              <div class="text-2xl font-bold text-orange-600" style="font-size: 20px;">{{ summary.low_stock_items }}</div>
              <div class="text-sm text-gray-600">Низкий остаток</div>
            </div>
            <div class="text-center">
              <div class="text-2xl font-bold text-red-600" style="font-size: 20px;">{{ summary.out_of_stock_items }}</div>
              <div class="text-sm text-gray-600">Нет в наличии</div>
            </div>
          </div>
        </div>

        <!-- Список остатков -->
        <div v-if="loading" class="flex justify-center items-center py-12">
          <div class="text-center">
            <Loader2 class="animate-spin h-8 w-8 text-blue-600 mx-auto mb-4" />
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
                <th v-if="productFieldsVisibility.price !== false" class="px-6 py-3 text-left text-xs font-medium text-gray-500">
                  Цена
                </th>
                <th v-if="productFieldsVisibility.price !== false" class="px-6 py-3 text-left text-xs font-medium text-gray-500">
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
              <template v-for="balance in balances" :key="`${balance.product_id}-${balance.warehouse_id}`">
                <tr class="hover:bg-gray-50">
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
                  <td v-if="productFieldsVisibility.price !== false" class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ formatCurrency(balance.product?.price) }}
                  </td>
                  <td v-if="productFieldsVisibility.price !== false" class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ formatCurrency(balance.quantity * (balance.product?.price || 0)) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="getStatusClass(balance.quantity)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                      {{ getStatusText(balance.quantity) }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex items-center gap-2">
                      <button
                        @click="viewMovements(balance.product_id, balance.warehouse_id)"
                        class="text-blue-600 hover:text-blue-900 cursor-pointer p-1 rounded hover:bg-blue-50 transition-colors"
                        title="Движение товара"
                      >
                        <ArrowRightLeft class="w-4 h-4" />
                      </button>
                      <router-link
                        :to="`/products/${balance.product_id}/edit`"
                        class="text-green-600 hover:text-green-900 cursor-pointer p-1 rounded hover:bg-green-50 transition-colors"
                        title="Редактировать товар"
                      >
                        <Edit class="w-4 h-4" />
                      </router-link>
                      <button
                        @click="openDeleteModal(balance.product_id)"
                        class="text-red-600 hover:text-red-900 cursor-pointer p-1 rounded hover:bg-red-50 transition-colors"
                        title="Удалить товар"
                      >
                        <Trash2 class="w-4 h-4" />
                      </button>
                    </div>
                  </td>
                </tr>
                <!-- Дополнительная строка с полями товара -->
                <tr class="bg-gray-50">
                  <td :colspan="productFieldsVisibility.price !== false ? 7 : 5" class="px-6 py-3">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 text-sm">
                      <!-- Обязательные поля -->
                      <div v-if="balance.product?.category?.name" class="flex items-center gap-2">
                        <span class="font-medium text-gray-600">Категория:</span>
                        <span class="text-gray-900">{{ balance.product.category.name }}</span>
                      </div>
                      <div v-if="balance.product?.subcategory?.name" class="flex items-center gap-2">
                        <span class="font-medium text-gray-600">Подкатегория:</span>
                        <span class="text-gray-900">{{ balance.product.subcategory.name }}</span>
                      </div>
                      
                      <!-- Дополнительные поля (активные) -->
                      <div v-if="productFieldsVisibility.description && balance.product?.description" class="flex items-center gap-2">
                        <span class="font-medium text-gray-600">Описание:</span>
                        <span class="text-gray-900">{{ balance.product.description }}</span>
                      </div>
                      <div v-if="productFieldsVisibility.country && balance.product?.country" class="flex items-center gap-2">
                        <span class="font-medium text-gray-600">Страна:</span>
                        <span class="text-gray-900">{{ balance.product.country }}</span>
                      </div>
                      <div v-if="productFieldsVisibility.supplier && balance.product?.supplier" class="flex items-center gap-2">
                        <span class="font-medium text-gray-600">Поставщик:</span>
                        <span class="text-gray-900">{{ balance.product.supplier }}</span>
                      </div>
                      <div v-if="productFieldsVisibility.article && balance.product?.article" class="flex items-center gap-2">
                        <span class="font-medium text-gray-600">Артикул:</span>
                        <span class="text-gray-900">{{ balance.product.article }}</span>
                      </div>
                      <div v-if="productFieldsVisibility.code && balance.product?.code" class="flex items-center gap-2">
                        <span class="font-medium text-gray-600">Код:</span>
                        <span class="text-gray-900">{{ balance.product.code }}</span>
                      </div>
                      <div v-if="productFieldsVisibility.external_code && balance.product?.external_code" class="flex items-center gap-2">
                        <span class="font-medium text-gray-600">Внешний код:</span>
                        <span class="text-gray-900">{{ balance.product.external_code }}</span>
                      </div>
                      <div v-if="productFieldsVisibility.unit && balance.product?.unit" class="flex items-center gap-2">
                        <span class="font-medium text-gray-600">Единица измерения:</span>
                        <span class="text-gray-900">{{ balance.product.unit }}</span>
                      </div>
                      <div v-if="productFieldsVisibility.weight && balance.product?.weight" class="flex items-center gap-2">
                        <span class="font-medium text-gray-600">Вес:</span>
                        <span class="text-gray-900">{{ balance.product.weight }}</span>
                      </div>
                      <div v-if="productFieldsVisibility.volume && balance.product?.volume" class="flex items-center gap-2">
                        <span class="font-medium text-gray-600">Объем:</span>
                        <span class="text-gray-900">{{ balance.product.volume }}</span>
                      </div>
                      <div v-if="productFieldsVisibility.vat && balance.product?.vat" class="flex items-center gap-2">
                        <span class="font-medium text-gray-600">Ставка НДС:</span>
                        <span class="text-gray-900">{{ balance.product.vat }}</span>
                      </div>
                      <div v-if="productFieldsVisibility.min_stock && balance.product?.min_stock" class="flex items-center gap-2">
                        <span class="font-medium text-gray-600">Минимальный остаток:</span>
                        <span class="text-gray-900">{{ balance.product.min_stock }}</span>
                      </div>
                      <div v-if="productFieldsVisibility.stock_type && balance.product?.stock_type" class="flex items-center gap-2">
                        <span class="font-medium text-gray-600">Тип запаса:</span>
                        <span class="text-gray-900">{{ balance.product.stock_type }}</span>
                      </div>
                      <div v-if="productFieldsVisibility.packing && balance.product?.packing" class="flex items-center gap-2">
                        <span class="font-medium text-gray-600">Упаковка:</span>
                        <span class="text-gray-900">{{ balance.product.packing }}</span>
                      </div>
                      <div v-if="productFieldsVisibility.accounting_type && balance.product?.accounting_type" class="flex items-center gap-2">
                        <span class="font-medium text-gray-600">Тип учета:</span>
                        <span class="text-gray-900">{{ balance.product.accounting_type }}</span>
                      </div>
                      <div v-if="productFieldsVisibility.traceable && balance.product?.traceable" class="flex items-center gap-2">
                        <span class="font-medium text-gray-600">Маркируемый:</span>
                        <span class="text-gray-900">{{ balance.product.traceable ? 'Да' : 'Нет' }}</span>
                      </div>
                      <div v-if="productFieldsVisibility.marking && balance.product?.marking" class="flex items-center gap-2">
                        <span class="font-medium text-gray-600">Маркировка:</span>
                        <span class="text-gray-900">{{ balance.product.marking }}</span>
                      </div>
                      <div v-if="productFieldsVisibility.product_type && balance.product?.product_type" class="flex items-center gap-2">
                        <span class="font-medium text-gray-600">Тип товара:</span>
                        <span class="text-gray-900">{{ balance.product.product_type }}</span>
                      </div>
                      <div v-if="productFieldsVisibility.barcode_type && balance.product?.barcode_type" class="flex items-center gap-2">
                        <span class="font-medium text-gray-600">Тип штрихкода:</span>
                        <span class="text-gray-900">{{ balance.product.barcode_type }}</span>
                      </div>
                      <div v-if="productFieldsVisibility.barcode && balance.product?.barcode" class="flex items-center gap-2">
                        <span class="font-medium text-gray-600">Штрихкод:</span>
                        <span class="text-gray-900">{{ balance.product.barcode }}</span>
                      </div>
                      <div v-if="productFieldsVisibility.cash_register_tax && balance.product?.cash_register_tax" class="flex items-center gap-2">
                        <span class="font-medium text-gray-600">Налог ККМ:</span>
                        <span class="text-gray-900">{{ balance.product.cash_register_tax }}</span>
                      </div>
                      <div v-if="productFieldsVisibility.cash_register_type && balance.product?.cash_register_type" class="flex items-center gap-2">
                        <span class="font-medium text-gray-600">Тип ККМ:</span>
                        <span class="text-gray-900">{{ balance.product.cash_register_type }}</span>
                      </div>
                      <div v-if="productFieldsVisibility.price && balance.product?.price" class="flex items-center gap-2">
                        <span class="font-medium text-gray-600">Цена:</span>
                        <span class="text-gray-900">{{ formatCurrency(balance.product.price) }}</span>
                      </div>
                      
                      <!-- Кастомные поля -->
                      <div v-for="field in customFields" :key="field.id" class="flex items-center gap-2">
                        <span class="font-medium text-gray-600">{{ field.name }}:</span>
                        <span class="text-gray-900">{{ balance.product?.[field.key] || '-' }}</span>
                      </div>
                    </div>
                  </td>
                </tr>
              </template>
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
              <Loader2 v-if="loading" class="animate-spin h-4 w-4" />
              <span v-if="loading">Загрузка...</span>
              <span v-else>Назад</span>
            </button>
            <button
              v-if="pagination.next_page_url"
              @click="loadBalances(pagination.current_page + 1)"
              :disabled="loading"
              class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:bg-gray-100 disabled:text-gray-400 text-sm flex items-center gap-2"
            >
              <Loader2 v-if="loading" class="animate-spin h-4 w-4" />
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
    
    <!-- Модальное окно подтверждения удаления -->
    <div v-if="showDeleteModal" class="fixed inset-0 z-[99999999] flex items-center justify-center bg-white/90 bg-opacity-50">
      <div class="bg-white rounded-lg shadow-lg w-full max-w-md mx-4 p-6">
        <div class="flex items-center mb-4">
          <div class="flex-shrink-0">
            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
              <Trash2 class="w-6 h-6 text-red-600" />
            </div>
          </div>
          <div class="ml-4">
            <h3 class="text-lg font-semibold text-gray-900">Удалить товар?</h3>
            <p class="text-sm text-gray-500">Это действие нельзя отменить. Товар будет удален навсегда.</p>
          </div>
        </div>
        <div class="flex gap-3">
          <button 
            @click="closeDeleteModal" 
            :disabled="deletingProductId !== null"
            class="flex-1 bg-gray-100 hover:bg-gray-200 disabled:bg-gray-50 disabled:cursor-not-allowed text-gray-800 font-semibold px-4 py-2 rounded-lg transition-colors"
          >
            Отмена
          </button>
          <button 
            @click="confirmDelete" 
            :disabled="deletingProductId !== null"
            class="flex-1 bg-red-600 hover:bg-red-700 disabled:bg-red-400 disabled:cursor-not-allowed text-white font-semibold px-4 py-2 rounded-lg transition-colors flex items-center justify-center gap-2"
          >
            <Loader2 v-if="deletingProductId !== null" class="w-4 h-4 animate-spin" />
            {{ deletingProductId !== null ? 'Удаление...' : 'Удалить' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, reactive, onMounted, computed } from 'vue'
import api from '@/config/api'
import ProductsMenu from './ProductsMenu.vue'
import MovementsModal from './MovementsModal.vue'
import Multiselect from '@vueform/multiselect'
import '@vueform/multiselect/themes/default.css'
import { Filter, FunnelX, Loader2, ArrowRightLeft, Edit, Trash2 } from 'lucide-vue-next'

export default {
  name: 'BalancesPage',
  components: {
    ProductsMenu,
    MovementsModal,
    Multiselect,
    Filter,
    FunnelX,
    Loader2,
    ArrowRightLeft,
    Edit,
    Trash2
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
    const showFilters = ref(false)
    
    // Состояния для удаления товара
    const showDeleteModal = ref(false)
    const productIdToDelete = ref(null)
    const deletingProductId = ref(null)
    
    // Состояния для настроек полей товара
    const productFieldsVisibility = reactive({})
    const customFields = ref([])
    const loadingProductFields = ref(true)

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

    const toggleFilters = () => {
      showFilters.value = !showFilters.value
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
    
    // Загрузка настроек полей товара
    const loadProductFieldsVisibilityAndCustomFields = async () => {
      loadingProductFields.value = true
      try {
        // Загрузка стандартных полей
        const userResponse = await api.get('/user/settings')
        if (userResponse.data.success && userResponse.data.data.product_fields_visibility) {
          Object.assign(productFieldsVisibility, userResponse.data.data.product_fields_visibility)
        }
        
        // Загрузка кастомных полей
        const customFieldsResponse = await api.get('/product-fields')
        if (customFieldsResponse.data.success) {
          customFields.value = customFieldsResponse.data.data || []
        }
      } catch (error) {
        console.error('Ошибка загрузки настроек полей:', error)
      } finally {
        loadingProductFields.value = false
      }
    }
    
    // Функции для удаления товара
    const openDeleteModal = (productId) => {
      productIdToDelete.value = productId
      showDeleteModal.value = true
    }
    
    const closeDeleteModal = () => {
      showDeleteModal.value = false
      productIdToDelete.value = null
      deletingProductId.value = null
    }
    
    const confirmDelete = async () => {
      if (!productIdToDelete.value) return
      
      deletingProductId.value = productIdToDelete.value
      
      try {
        const response = await api.delete(`/products/${productIdToDelete.value}`)
        
        if (response.data.success) {
          // Удаляем товар из списка остатков
          balances.value = balances.value.filter(balance => 
            balance.product_id !== productIdToDelete.value
          )
          
          alert('Товар успешно удален')
          closeDeleteModal()
        } else {
          alert(response.data.message || 'Ошибка при удалении товара')
        }
      } catch (err) {
        console.error('Ошибка удаления товара:', err)
        alert('Ошибка при удалении товара')
      } finally {
        deletingProductId.value = null
      }
    }

    onMounted(() => {
      loadBalances()
      loadWarehouses()
      loadSummary() // Автоматически загружаем сводку при загрузке страницы
      loadProductFieldsVisibilityAndCustomFields() // Загружаем настройки полей
    })

    return {
      balances,
      warehouses,
      summary,
      pagination,
      loading,
      loadingSummary,
      filters,
      showFilters,
      showMovementsModal,
      selectedProductId,
      selectedWarehouseId,
      loadingWarehouses,
      warehouseOptions,
      loadBalances,
      loadWarehouses,
      loadSummary,
      clearFilters,
      toggleFilters,
      viewMovements,
      formatCurrency,
      getQuantityClass,
      getStatusClass,
      getStatusText,
      currency,
      productFieldsVisibility,
      customFields,
      loadingProductFields,
      showDeleteModal,
      productIdToDelete,
      deletingProductId,
      openDeleteModal,
      closeDeleteModal,
      confirmDelete
    }
  }
}
</script> 