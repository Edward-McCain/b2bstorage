<template>
  <div class="fixed inset-0 bg-white/90 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
      <div class="mt-3">
        <div class="flex justify-between items-center mb-4">
          <div>
            <h3 class="text-lg font-medium text-gray-900">{{ t('MovementsModal_1') }}</h3> <!-- Движение товаров -->
            <div v-if="product" class="text-sm text-gray-600 mt-1">
              {{ product.name }}
              <span v-if="product.article" class="text-gray-500">({{ product.article }})</span>
              <div v-if="product.category_name || product.subcategory_name" class="text-xs text-gray-500 mt-1">
                <span v-if="product.category_name">{{ product.category_name }}</span>
                <span v-if="product.category_name && product.subcategory_name"> → </span>
                <span v-if="product.subcategory_name">{{ product.subcategory_name }}</span>
              </div>
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
          <div class="text-gray-600 text-sm">{{ t('MovementsModal_2') }}</div> <!-- Загрузка движения товаров... -->
        </div>

        <div v-else-if="movements.length === 0" class="text-center py-8">
          <div class="text-gray-500 text-sm">{{ t('MovementsModal_3') }}</div> <!-- Нет данных о движении товаров -->
        </div>

        <div v-else class="space-y-4">
          <!-- Фильтры -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">{{ t('MovementsModal_4') }}</label> <!-- Дата с -->
              <LocalizedDatePicker 
                v-model="filters.date_from"
                :enable-time-picker="false"
                :auto-apply="true"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">{{ t('MovementsModal_5') }}</label> <!-- Дата по -->
              <LocalizedDatePicker 
                v-model="filters.date_to"
                :enable-time-picker="false"
                :auto-apply="true"
              />
            </div>
            <div class="flex items-end">
              <button
                @click="loadMovements"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm"
              >
                {{ t('MovementsModal_6') }} <!-- Применить -->
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
                    {{ formatCurrency(productPrice || 0) }} {{ t('MovementsModal_12') }} <!-- за шт. -->
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Пагинация -->
          <div v-if="pagination" class="flex justify-between items-center">
            <div class="text-sm text-gray-700">
              {{ t('MovementsModal_13') }} {{ pagination.from }}-{{ pagination.to }} {{ t('MovementsModal_14') }} {{ pagination.total }} <!-- Показано из -->
            </div>
            <div class="flex gap-2">
              <button
                v-if="pagination.prev_page_url"
                @click="loadMovements(pagination.current_page - 1)"
                class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 text-sm"
              >
                {{ t('MovementsModal_15') }} <!-- Назад -->
              </button>
              <button
                v-if="pagination.next_page_url"
                @click="loadMovements(pagination.current_page + 1)"
                class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 text-sm"
              >
                {{ t('MovementsModal_16') }} <!-- Вперед -->
              </button>
            </div>
          </div>
        </div>

        <div class="flex justify-end mt-6">
          <button
            @click="$emit('close')"
            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 text-sm"
          >
            {{ t('MovementsModal_17') }} <!-- Закрыть -->
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import api from '@/config/api'
import { Loader2 } from 'lucide-vue-next'
import { t } from '@/locales'
import LocalizedDatePicker from '../LocalizedDatePicker.vue'

export default {
  name: 'MovementsModal',
  components: {
    Loader2,
    LocalizedDatePicker
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
  data() {
    return {
      movements: [],
      pagination: null,
      loading: false,
      productPrice: 0,
      product: null,
      filters: {
        date_from: '',
        date_to: ''
      },
      t: t
    }
  },
  methods: {
    // Получаем валюту пользователя из localStorage
    getUserCurrency() {
      const userData = localStorage.getItem('user')
      if (userData) {
        try {
          const user = JSON.parse(userData)
          return user.currency || 'UZS'
        } catch (e) {
          console.error('Ошибка парсинга данных пользователя:', e)
        }
      }
      return 'UZS'
    },

    async loadMovements(page = 1) {
      if (!this.productId) {
        console.error('productId не передан')
        return
      }

      this.loading = true
      try {
        const requestData = {
          product_id: this.productId,
          page,
          ...this.filters
        }
        
        if (this.warehouseId) {
          requestData.warehouse_id = this.warehouseId
        }

        const response = await api.post('/balances/movements', requestData)
        
        this.movements = response.data.movements.data
        this.pagination = response.data.movements
        this.product = response.data.product
        this.productPrice = response.data.product_price
      } catch (error) {
        console.error('Ошибка загрузки движения товаров:', error)
      } finally {
        this.loading = false
      }
    },

    getOperationClass(type) {
      const classes = {
        receipt: 'bg-green-100 text-green-800',
        write_off: 'bg-red-100 text-red-800',
        inventory: 'bg-blue-100 text-blue-800',
        transfer_in: 'bg-purple-100 text-purple-800',
        transfer_out: 'bg-orange-100 text-orange-800'
      }
      return classes[type] || 'bg-gray-100 text-gray-800'
    },

    getOperationText(type) {
      const texts = {
        receipt: this.t('MovementsModal_7'), // Оприходование
        write_off: this.t('MovementsModal_8'), // Списание
        inventory: this.t('MovementsModal_9'), // Инвентаризация
        transfer_in: this.t('MovementsModal_10'), // Перемещение (в)
        transfer_out: this.t('MovementsModal_11') // Перемещение (из)
      }
      return texts[type] || type
    },

    formatDateTime(date) {
      return new Date(date).toLocaleString('ru-RU')
    },

    formatCurrency(amount) {
      const userCurrency = this.getUserCurrency()
      if (!amount) return `0 ${userCurrency}`
      
      // Маппинг символов валют
      const currencySymbols = {
        'USD': '$',
        'EUR': '€',
        'RUB': '₽',
        'UZS': 'сум',
        'GBP': '£',
        'JPY': '¥',
        'CNY': '¥',
        'AUD': 'A$',
        'CAD': 'C$',
        'CHF': 'CHF',
        'HKD': 'HK$',
        'NZD': 'NZ$'
      }
      
      const symbol = currencySymbols[userCurrency] || userCurrency
      
      // Форматирование числа
      const formattedNumber = new Intl.NumberFormat('ru-RU', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      }).format(amount)
      
      // Для валют с символом справа (например, UZS)
      if (userCurrency === 'UZS') {
        return `${formattedNumber} ${symbol}`
      }
      
      // Для валют с символом слева
      return `${symbol}${formattedNumber}`
    }
  },

  mounted() {
    this.loadMovements()
  }
}
</script> 