<template>
  <div class="fixed inset-0 bg-white/90 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
      <div class="mt-3">
        <div class="flex justify-between items-center mb-4">
          <div>
            <h3 class="text-lg font-medium text-gray-900">Движение товаров</h3>
            <div v-if="product" class="text-sm text-gray-600 mt-1">
              {{ product.name }}
              <span v-if="product.article" class="text-gray-500">({{ product.article }})</span>
            </div>
          </div>
          <button
            @click="$emit('close')"
            class="text-gray-400 hover:text-gray-600"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <div v-if="loading" class="text-center py-8">
          <Loader2 class="animate-spin h-8 w-8 text-blue-600 mx-auto mb-2" />
          <div class="text-gray-600">Загрузка движения товаров...</div>
        </div>

        <div v-else-if="movements.length === 0" class="text-center py-8">
          <div class="text-gray-500">Нет данных о движении товаров</div>
        </div>

        <div v-else class="space-y-4">
          <!-- Фильтры -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Дата с</label>
              <input
                v-model="filters.date_from"
                type="date"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Дата по</label>
              <input
                v-model="filters.date_to"
                type="date"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
              />
            </div>
            <div class="flex items-end">
              <button
                @click="loadMovements"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm"
              >
                Применить
              </button>
            </div>
          </div>

          <!-- Список операций -->
          <div class="max-h-96 overflow-y-auto">
            <div
              v-for="movement in movements"
              :key="movement.id"
              class="border border-gray-200 rounded-lg p-3 mb-3"
            >
              <div class="flex justify-between items-start">
                <div class="flex-1">
                  <div class="flex items-center gap-2">
                    <span
                      :class="getOperationClass(movement.operation_type)"
                      class="px-2 py-1 text-xs font-semibold rounded-full"
                    >
                      {{ getOperationText(movement.operation_type) }}
                    </span>
                    <span class="text-sm font-medium text-gray-900">
                      {{ movement.quantity > 0 ? '+' : '' }}{{ movement.quantity }}
                    </span>
                  </div>
                  <div class="text-sm text-gray-600 mt-1">
                    {{ movement.notes }}
                  </div>
                  <div class="text-xs text-gray-500 mt-1">
                    {{ formatDateTime(movement.created_at) }}
                  </div>
                </div>
                <div class="text-right">
                  <div class="text-sm font-medium text-gray-900">
                    {{ formatCurrency(movement.quantity * (productPrice || 0)) }}
                  </div>
                  <div class="text-xs text-gray-500">
                    {{ formatCurrency(productPrice || 0) }} за шт.
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Пагинация -->
          <div v-if="pagination" class="flex justify-between items-center">
            <div class="text-sm text-gray-700">
              Показано {{ pagination.from }}-{{ pagination.to }} из {{ pagination.total }}
            </div>
            <div class="flex gap-2">
              <button
                v-if="pagination.prev_page_url"
                @click="loadMovements(pagination.current_page - 1)"
                class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 text-sm"
              >
                Назад
              </button>
              <button
                v-if="pagination.next_page_url"
                @click="loadMovements(pagination.current_page + 1)"
                class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 text-sm"
              >
                Вперед
              </button>
            </div>
          </div>
        </div>

        <div class="flex justify-end mt-6">
          <button
            @click="$emit('close')"
            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 text-sm"
          >
            Закрыть
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, reactive, onMounted } from 'vue'
import api from '@/config/api'
import { Loader2 } from 'lucide-vue-next'

export default {
  name: 'MovementsModal',
  components: {
    Loader2
  },
  props: {
    productId: {
      type: Number,
      required: true
    },
    warehouseId: {
      type: Number,
      default: null
    }
  },
  emits: ['close'],
  setup(props) {
    const movements = ref([])
    const pagination = ref(null)
    const loading = ref(false)
    const productPrice = ref(0)
    const product = ref(null)

    const filters = reactive({
      date_from: '',
      date_to: ''
    })

    const loadMovements = async (page = 1) => {
      if (!props.productId) {
        console.error('productId не передан')
        return
      }

      loading.value = true
      try {
        const requestData = {
          product_id: props.productId,
          page,
          ...filters
        }
        
        if (props.warehouseId) {
          requestData.warehouse_id = props.warehouseId
        }

        const response = await api.post('/balances/movements', requestData)
        movements.value = response.data.movements.data
        pagination.value = response.data.movements
        product.value = response.data.product
        productPrice.value = response.data.product_price
      } catch (error) {
        console.error('Ошибка загрузки движения товаров:', error)
      } finally {
        loading.value = false
      }
    }

    const getOperationClass = (type) => {
      const classes = {
        receipt: 'bg-green-100 text-green-800',
        write_off: 'bg-red-100 text-red-800',
        inventory: 'bg-blue-100 text-blue-800',
        transfer_in: 'bg-purple-100 text-purple-800',
        transfer_out: 'bg-orange-100 text-orange-800'
      }
      return classes[type] || 'bg-gray-100 text-gray-800'
    }

    const getOperationText = (type) => {
      const texts = {
        receipt: 'Оприходование',
        write_off: 'Списание',
        inventory: 'Инвентаризация',
        transfer_in: 'Перемещение (в)',
        transfer_out: 'Перемещение (из)'
      }
      return texts[type] || type
    }

    const formatDateTime = (date) => {
      return new Date(date).toLocaleString('ru-RU')
    }

    const formatCurrency = (amount) => {
      if (!amount) return '0 ₽'
      return new Intl.NumberFormat('ru-RU', {
        style: 'currency',
        currency: 'RUB'
      }).format(amount)
    }

    onMounted(() => {
      loadMovements()
    })

    return {
      movements,
      pagination,
      loading,
      productPrice,
      product,
      filters,
      loadMovements,
      getOperationClass,
      getOperationText,
      formatDateTime,
      formatCurrency
    }
  }
}
</script> 