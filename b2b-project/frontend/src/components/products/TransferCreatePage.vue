<template>
  <div class="min-h-screen bg-gray-50" style="padding-top: 66px;">
    <!-- Внутреннее меню навигации -->
    <ProductsMenu />
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-8">
      <!-- Заголовок страницы -->
      <div class="mb-6 sm:mb-8">
        <div class="flex items-center justify-between">
          <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">{{ t('TransferCreatePage_1') }}</h1> <!-- Создание перемещения -->
          <router-link
            to="/products/transfers"
            class="flex items-center gap-2 text-gray-600 hover:text-gray-900 font-medium px-4 py-2 rounded text-sm hover:bg-gray-100 transition-colors"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
          </router-link>
        </div>
      </div>
      
      <!-- Основной контент -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
        <form @submit.prevent="saveTransfer">
          <!-- Основная информация -->
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 mb-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">{{ t('TransferCreatePage_2') }}</label> <!-- От склада * -->
              <Multiselect
                v-model="form.from_warehouse_id"
                :options="warehouseOptions"
                label="label"
                value="value"
                :object="false"
                :placeholder="t('TransferCreatePage_3')"
                :max-height="400"
                class="w-full text-sm multiselect-custom"
                :loading="loadingWarehouses"
                :disabled="loadingWarehouses"
                @update:modelValue="onFromWarehouseChange"
                required
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">{{ t('TransferCreatePage_4') }}</label> <!-- В склад * -->
              <Multiselect
                v-model="form.to_warehouse_id"
                :options="availableToWarehouseOptions"
                label="label"
                value="value"
                :object="false"
                :placeholder="t('TransferCreatePage_3')"
                :max-height="400"
                class="w-full text-sm multiselect-custom"
                :loading="loadingWarehouses"
                :disabled="loadingWarehouses"
                required
              />
            </div>
          </div>

          <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 mb-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">{{ t('TransferCreatePage_5') }}</label> <!-- Дата перемещения * -->
              <LocalizedDatePicker 
                v-model="form.transfer_date"
                :enable-time-picker="false"
                :auto-apply="true"
                required
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">{{ t('TransferCreatePage_6') }}</label> <!-- Примечания -->
              <input
                v-model="form.notes"
                type="text"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm bg-white"
                :placeholder="t('TransferCreatePage_7')"
              />
            </div>
          </div>

          <!-- Список товаров -->
          <div class="mb-6">
            <div class="flex justify-between items-center mb-4">
              <h3 class="text-base sm:text-lg font-medium text-gray-900">{{ t('TransferCreatePage_8') }}</h3> <!-- Товары для перемещения -->
            </div>

            <!-- Сообщение о выборе склада -->
            <div v-if="!form.from_warehouse_id" class="text-center py-6 sm:py-8 text-gray-500 border-2 border-dashed border-gray-300 rounded-lg">
              <svg class="mx-auto h-8 w-8 sm:h-12 sm:w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
              </svg>
              <p class="mt-2 text-xs sm:text-sm">{{ t('TransferCreatePage_9') }}</p> <!-- Сначала выберите склад, откуда перемещаем товары -->
            </div>

            <!-- Поиск товаров -->
            <div v-else class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-2">{{ t('TransferCreatePage_10') }}</label> <!-- Поиск товаров -->
              <div class="relative">
                <input
                  v-model="productSearch"
                  type="text"
                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm bg-white pr-10"
                  :placeholder="t('TransferCreatePage_11')"
                  @input="onProductSearch"
                />
                <div v-if="loadingSearch" class="absolute inset-y-0 right-0 flex items-center pr-3">
                  <Loader2 class="animate-spin h-4 w-4 text-blue-600" />
                </div>
              </div>
            </div>

            <!-- Таблица товаров (десктоп) -->
            <div v-if="form.from_warehouse_id && availableProducts.length > 0" class="hidden md:block overflow-hidden">
              <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                  <thead class="bg-gray-50">
                    <tr>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ t('TransferCreatePage_12') }} <!-- Товар -->
                      </th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ t('TransferCreatePage_13') }} <!-- Артикул -->
                      </th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ t('TransferCreatePage_14') }} <!-- На складе -->
                      </th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ t('TransferCreatePage_15') }} <!-- Переместить -->
                      </th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ t('TransferCreatePage_16') }} <!-- Примечание -->
                      </th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="product in availableProducts" :key="product.id" class="hover:bg-gray-50">
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div>
                          <div class="text-sm font-medium text-gray-900">{{ product.name }}</div>
                          <div v-if="product.description" class="text-sm text-gray-500 truncate max-w-xs">
                            {{ product.description }}
                          </div>
                        </div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ product.article || t('TransferCreatePage_17') }} <!-- Не указан -->
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                          {{ product.warehouse_quantity }} {{ t('TransferCreatePage_18') }} <!-- шт. -->
                        </span>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <input
                          v-model.number="product.selected_quantity"
                          type="number"
                          min="0"
                          :max="product.warehouse_quantity"
                          class="w-24 border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition shadow-sm"
                          @input="updateProductQuantity(product)"
                        />
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <input
                          v-model="product.notes"
                          type="text"
                          class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition shadow-sm"
                          :placeholder="t('TransferCreatePage_19')"
                        />
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <!-- Карточки товаров (мобильные) -->
            <div v-if="form.from_warehouse_id && availableProducts.length > 0" class="md:hidden space-y-4">
              <div v-for="product in availableProducts" :key="product.id" class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <div class="space-y-3">
                  <!-- Название товара -->
                  <div>
                    <h4 class="text-sm font-medium text-gray-900">{{ product.name }}</h4>
                    <p v-if="product.description" class="text-xs text-gray-500 mt-1">{{ product.description }}</p>
                  </div>
                  
                  <!-- Артикул -->
                  <div class="flex justify-between items-center">
                    <span class="text-xs text-gray-500">{{ t('TransferCreatePage_20') }}</span> <!-- Артикул: -->
                    <span class="text-xs text-gray-900">{{ product.article || t('TransferCreatePage_17') }}</span> <!-- Не указан -->
                  </div>
                  
                  <!-- Количество на складе -->
                  <div class="flex justify-between items-center">
                    <span class="text-xs text-gray-500">{{ t('TransferCreatePage_21') }}</span> <!-- На складе: -->
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                      {{ product.warehouse_quantity }} {{ t('TransferCreatePage_18') }} <!-- шт. -->
                    </span>
                  </div>
                  
                  <!-- Количество для перемещения -->
                  <div class="flex justify-between items-center">
                    <span class="text-xs text-gray-500">{{ t('TransferCreatePage_22') }}</span> <!-- Переместить: -->
                    <input
                      v-model.number="product.selected_quantity"
                      type="number"
                      min="0"
                      :max="product.warehouse_quantity"
                      class="w-20 border border-gray-300 rounded-md px-2 py-1 text-xs focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition shadow-sm"
                      @input="updateProductQuantity(product)"
                    />
                  </div>
                  
                  <!-- Примечание -->
                  <div>
                    <label class="block text-xs text-gray-500 mb-1">{{ t('TransferCreatePage_23') }}</label> <!-- Примечание: -->
                    <input
                      v-model="product.notes"
                      type="text"
                      class="w-full border border-gray-300 rounded-md px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition shadow-sm"
                      :placeholder="t('TransferCreatePage_19')"
                    />
                  </div>
                </div>
              </div>
            </div>

            <!-- Сообщение об отсутствии товаров -->
            <div v-else-if="form.from_warehouse_id && !loadingProducts" class="text-center py-6 sm:py-8 text-gray-500 border-2 border-dashed border-gray-300 rounded-lg">
              <svg class="mx-auto h-8 w-8 sm:h-12 sm:w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
              </svg>
              <p class="mt-2 text-xs sm:text-sm">{{ t('TransferCreatePage_24') }}</p> <!-- На выбранном складе нет доступных товаров -->
            </div>

            <!-- Загрузка товаров -->
            <div v-else-if="loadingProducts" class="text-center py-6 sm:py-8">
              <div class="flex items-center justify-center">
                <Loader2 class="animate-spin h-5 w-5 sm:h-6 sm:w-6 text-blue-600 mr-2" />
                <span class="text-xs sm:text-sm text-gray-500">{{ t('TransferCreatePage_25') }}</span> <!-- Загрузка товаров склада... -->
              </div>
            </div>
          </div>

          <!-- Кнопки действий -->
          <div class="space-y-4 sm:space-y-0 sm:flex sm:justify-between sm:items-center">
            <!-- Подсказки и сообщения -->
            <div class="flex-1">
              <!-- Подсказки -->
              <div v-if="!isFormValid && !loading && !creatingTransfer" class="text-xs sm:text-sm text-gray-500">
                <div v-if="!form.from_warehouse_id" class="mb-1">• {{ t('TransferCreatePage_26') }}</div> <!-- Выберите склад отправления -->
                <div v-if="!form.to_warehouse_id" class="mb-1">• {{ t('TransferCreatePage_27') }}</div> <!-- Выберите склад назначения -->
                <div v-if="form.from_warehouse_id === form.to_warehouse_id && form.from_warehouse_id" class="mb-1">• {{ t('TransferCreatePage_28') }}</div> <!-- Склады должны быть разными -->
                <div v-if="!hasSelectedProducts && form.from_warehouse_id && form.to_warehouse_id" class="mb-1">• {{ t('TransferCreatePage_29') }}</div> <!-- Выберите товары для перемещения -->
              </div>
              
              <!-- Позитивное сообщение -->
              <div v-if="isFormValid && !loading && !creatingTransfer" class="text-xs sm:text-sm text-green-600 font-medium">
                ✓ {{ t('TransferCreatePage_30') }} <!-- Можно перемещать -->
              </div>

              <!-- Сообщение о процессе создания -->
              <div v-if="creatingTransfer" class="text-xs sm:text-sm text-blue-600 font-medium flex items-center">
                <Loader2 class="animate-spin h-4 w-4 mr-2" />
                {{ t('TransferCreatePage_31') }} <!-- В процессе... -->
              </div>
            </div>
            
            <!-- Кнопки -->
            <div class="flex flex-col sm:flex-row gap-3">
              <router-link
                to="/products/transfers"
                class="w-full sm:w-auto px-4 py-3 sm:py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 text-sm text-center"
              >
                {{ t('TransferCreatePage_32') }} <!-- Отмена -->
              </router-link>
              <button
                type="submit"
                :disabled="loading || creatingTransfer || !isFormValid"
                class="w-full sm:w-auto px-4 py-3 sm:py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg disabled:opacity-50 disabled:cursor-not-allowed text-sm"
              >
                {{ creatingTransfer ? t('TransferCreatePage_31') : (loading ? t('TransferCreatePage_34') : t('TransferCreatePage_33')) }} <!-- В процессе... : Сохранение... : Создать перемещение -->
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- Модальное окно для случая отсутствия складов -->
    <NoWarehousesModal 
      :is-visible="showNoWarehousesModal"
      @close="closeNoWarehousesModal"
    />
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import api from '@/config/api'
import ProductsMenu from './ProductsMenu.vue'
import NoWarehousesModal from '../NoWarehousesModal.vue'
import LocalizedDatePicker from '../LocalizedDatePicker.vue'
import { useWarehouseCheck } from '@/composables/useWarehouseCheck'
import Multiselect from '@vueform/multiselect'
import '@vueform/multiselect/themes/default.css'
import { Loader2 } from 'lucide-vue-next'
import { t } from '@/locales'

const router = useRouter()
const route = useRoute()
const loading = ref(false)
const creatingTransfer = ref(false)
const loadingProducts = ref(false)
const loadingSearch = ref(false)
const availableProducts = ref([])
const productSearch = ref('')
const originalProducts = ref([]) // Сохраняем оригинальный список товаров

// Используем композабл для проверки складов
const {
  warehouses,
  loadingWarehouses,
  showNoWarehousesModal,
  hasWarehouses,
  warehouseOptions,
  loadWarehouses,
  checkWarehousesAndShowModal,
  closeNoWarehousesModal
} = useWarehouseCheck()

const form = reactive({
  from_warehouse_id: '',
  to_warehouse_id: '',
  transfer_date: new Date().toISOString().split('T')[0],
  notes: '',
  positions: []
})

// Вычисляемые свойства для селектов складов

const availableToWarehouseOptions = computed(() => {
  return warehouses.value
    .filter(warehouse => warehouse.id !== form.from_warehouse_id)
    .map(warehouse => ({
      label: warehouse.name,
      value: warehouse.id
    }))
})

// Проверяем, есть ли выбранные товары
const hasSelectedProducts = computed(() => {
  return availableProducts.value.some(product => 
    product.selected_quantity && product.selected_quantity > 0
  )
})

// Проверяем, заполнены ли все обязательные поля
const isFormValid = computed(() => {
  // Проверяем, что выбраны склады
  const warehousesSelected = form.from_warehouse_id && form.to_warehouse_id
  
  // Проверяем, что склады разные
  const warehousesDifferent = form.from_warehouse_id !== form.to_warehouse_id
  
  // Проверяем, что есть выбранные товары
  const productsSelected = hasSelectedProducts.value
  
  return warehousesSelected && warehousesDifferent && productsSelected
})



// Установка склада из URL параметра
const setWarehouseFromUrl = () => {
  const fromWarehouseId = route.query.from_warehouse
  if (fromWarehouseId && warehouses.value.length > 0) {
    const warehouseId = parseInt(fromWarehouseId)
    const warehouse = warehouses.value.find(w => w.id === warehouseId)
    
    if (warehouse) {
      form.from_warehouse_id = warehouseId
      // loadAvailableProducts() будет вызван автоматически через watch
    }
  }
}

// Установка предвыбранного товара из URL параметра
const setProductFromUrl = () => {
  const productId = route.query.product_id
  const quantity = route.query.quantity
  
  if (productId && availableProducts.value.length > 0) {
    const product = availableProducts.value.find(p => p.id === parseInt(productId))
    
    if (product) {
      // Устанавливаем количество для выбранного товара
      const selectedQuantity = quantity ? parseInt(quantity) : product.warehouse_quantity
      product.selected_quantity = Math.min(selectedQuantity, product.warehouse_quantity)
    }
  }
}

const onFromWarehouseChange = (value) => {
  console.log('onFromWarehouseChange: Склад изменен на', value)
  // Не вызываем loadAvailableProducts здесь, так как watch уже это сделает
}

const loadAvailableProducts = async () => {
  console.log('loadAvailableProducts: Начало загрузки товаров для склада', form.from_warehouse_id)
  
  if (!form.from_warehouse_id) {
    console.log('loadAvailableProducts: Склад не выбран, очищаем список товаров')
    availableProducts.value = []
    originalProducts.value = []
    return
  }

  try {
    loadingProducts.value = true
    console.log('loadAvailableProducts: Отправляем запрос к API')
    const response = await api.post('/transfers/available-products', {
      warehouse_id: form.from_warehouse_id
    })
    
    console.log('loadAvailableProducts: Получен ответ от API', response.data)
    
    // Добавляем поля для выбора количества и примечаний
    const products = response.data.map(product => ({
      ...product,
      selected_quantity: 0,
      notes: ''
    }))
    
    availableProducts.value = products
    originalProducts.value = [...products] // Сохраняем оригинальный список
    
    console.log('loadAvailableProducts: Товары загружены', products.length)
    
    // Устанавливаем предвыбранный товар из URL параметра
    setProductFromUrl()
  } catch (error) {
    console.error('loadAvailableProducts: ' + t('TransferCreatePage_37') + error) // Ошибка сохранения перемещения:
    availableProducts.value = []
    originalProducts.value = []
  } finally {
    loadingProducts.value = false
    console.log('loadAvailableProducts: Загрузка завершена')
  }
}

let searchTimeout = null

const onProductSearch = () => {
  // Очищаем предыдущий таймаут
  if (searchTimeout) {
    clearTimeout(searchTimeout)
  }

  // Если поиск очищен, восстанавливаем оригинальный список
  if (!productSearch.value) {
    availableProducts.value = [...originalProducts.value]
    return
  }

  // Устанавливаем задержку для поиска
  searchTimeout = setTimeout(() => {
    // Фильтруем товары по поисковому запросу из оригинального списка
    const searchLower = productSearch.value.toLowerCase()
    availableProducts.value = originalProducts.value.filter(product => 
      product.name.toLowerCase().includes(searchLower) ||
      (product.article && product.article.toLowerCase().includes(searchLower))
    )
  }, 300) // Задержка 300мс
}

const updateProductQuantity = (product) => {
  // Убеждаемся, что количество не превышает доступное на складе
  if (product.selected_quantity > product.warehouse_quantity) {
    product.selected_quantity = product.warehouse_quantity
  }
  if (product.selected_quantity < 0) {
    product.selected_quantity = 0
  }
}

const saveTransfer = async () => {
  // Собираем позиции только из товаров с выбранным количеством
  const selectedPositions = availableProducts.value
    .filter(product => product.selected_quantity > 0)
    .map(product => ({
      product_id: product.id,
      quantity: product.selected_quantity,
      notes: product.notes || ''
    }))

  if (selectedPositions.length === 0) {
    alert(t('TransferCreatePage_35')) // Выберите товары для перемещения
    return
  }

  if (form.from_warehouse_id === form.to_warehouse_id) {
    alert(t('TransferCreatePage_36')) // Склад отправления и назначения не могут быть одинаковыми
    return
  }

  creatingTransfer.value = true
  try {
    const transferData = {
      ...form,
      positions: selectedPositions
    }
    
    await api.post('/transfers', transferData)
    router.push('/products/transfers')
  } catch (error) {
    console.error(t('TransferCreatePage_37') + error) // Ошибка сохранения перемещения:
    alert(t('TransferCreatePage_38')) // Ошибка сохранения перемещения
  } finally {
    creatingTransfer.value = false
  }
}

// Следим за изменениями склада отправления
watch(() => form.from_warehouse_id, (newValue, oldValue) => {
  console.log('watch: Склад отправления изменился с', oldValue, 'на', newValue)
  if (newValue && newValue !== oldValue) {
    console.log('watch: Вызываем loadAvailableProducts для склада', newValue)
    loadAvailableProducts()
  }
})

onMounted(async () => {
  // Проверяем наличие складов и показываем модальное окно если их нет
  const hasWarehouses = await checkWarehousesAndShowModal()
  if (hasWarehouses) {
    // Устанавливаем склад из URL параметра если есть склады
    setWarehouseFromUrl()
  }
})
</script> 