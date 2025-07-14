<template>
  <div class="min-h-screen bg-gray-50" style="padding-top: 66px;">
    <ProductsMenu />
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Заголовок страницы -->
      <div class="mb-8">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ warehouse?.name || 'Склад' }}</h1>
            <p v-if="warehouse?.address" class="mt-2 text-gray-600">{{ warehouse.address }}</p>
            <p v-else class="mt-2 text-gray-400">Адрес не указан</p>
          </div>
          <div class="flex items-center gap-3">
            <router-link
              :to="`/warehouses/edit/${warehouseId}`"
              class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 text-sm"
            >
              <Edit class="w-4 h-4" />
              Редактировать
            </router-link>
            <router-link
              :to="`/products/transfers/create?from_warehouse=${warehouseId}`"
              class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 text-sm"
            >
              <Truck class="w-4 h-4" />
              Переместить товары
            </router-link>
          </div>
        </div>
      </div>

      <!-- Основной контент -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-xl font-semibold text-gray-900">Товары на складе</h2>
          <div class="flex items-center gap-4">
            <div class="text-sm text-gray-600">
              Всего товаров: {{ products.length }}
            </div>
            <div class="text-sm text-gray-600">
              Общее количество: {{ totalQuantity }}
            </div>
          </div>
        </div>

        <!-- Загрузка товаров -->
        <div v-if="loadingProducts" class="flex items-center justify-center py-12">
          <Loader2 class="animate-spin h-8 w-8 text-blue-600 mr-3" />
          <span class="text-lg text-gray-600">Загрузка товаров склада...</span>
        </div>

        <!-- Пустой склад -->
        <div v-else-if="!loadingProducts && products.length === 0" class="text-center py-12">
          <Package class="mx-auto h-12 w-12 text-gray-400" />
          <h3 class="mt-2 text-sm font-medium text-gray-900">Склад пуст</h3>
          <p class="mt-1 text-sm text-gray-500">На этом складе пока нет товаров</p>
        </div>

        <!-- Таблица товаров -->
        <div v-else-if="!loadingProducts" class="overflow-hidden">
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Товар
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Артикул
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Количество
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Действия
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="product in products" :key="product.id" class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div>
                      <div class="text-sm font-medium text-gray-900">{{ product.name }}</div>
                      <div v-if="product.description" class="text-sm text-gray-500 truncate max-w-xs">
                        {{ product.description }}
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ product.article || 'Не указан' }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                      {{ product.warehouse_quantity }} шт.
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex items-center gap-2">
                      <button
                        @click="openMovementsModal(product)"
                        class="text-gray-600 hover:text-gray-900 px-3 py-1 rounded text-sm transition-colors flex items-center gap-1 cursor-pointer"
                      >
                        <TimerReset class="w-4 h-4" />
                        Движения
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Модальное окно движений товара -->
    <MovementsModal
      v-if="showMovementsModal && selectedProduct?.id"
      :product-id="selectedProduct.id"
      :warehouse-id="warehouseId"
      @close="closeMovementsModal"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { apiRequest } from '@/config/api'
import ProductsMenu from '../products/ProductsMenu.vue'
import MovementsModal from '../products/MovementsModal.vue'
import { Edit, Truck, Loader2, Package, TimerReset } from 'lucide-vue-next'
import toastr from 'toastr'

const route = useRoute()
const router = useRouter()

const warehouseId = route.params.id
const warehouse = ref(null)
const products = ref([])
const loadingWarehouse = ref(false)
const loadingProducts = ref(false)
const showMovementsModal = ref(false)
const selectedProduct = ref(null)

// Вычисляемые свойства
const totalQuantity = computed(() => {
  return products.value.reduce((sum, product) => sum + product.quantity, 0)
})

// Загрузка данных склада
const loadWarehouse = async () => {
  loadingWarehouse.value = true
  try {
    const response = await apiRequest(`/warehouses/${warehouseId}`, { method: 'GET' })
    if (response.ok && response.data.success) {
      warehouse.value = response.data.data
    } else {
      toastr.error('Ошибка загрузки данных склада')
    }
  } catch (error) {
    console.error('Ошибка загрузки склада:', error)
    toastr.error('Ошибка загрузки данных склада')
  } finally {
    loadingWarehouse.value = false
  }
}

// Загрузка товаров склада
const loadWarehouseProducts = async () => {
  loadingProducts.value = true
  try {
    const response = await apiRequest(`/transfers/available-products`, { 
      method: 'POST',
      body: JSON.stringify({ warehouse_id: warehouseId })
    })
    if (response.ok) {
      products.value = response.data || []
    } else {
      products.value = []
    }
  } catch (error) {
    console.error('Ошибка загрузки товаров склада:', error)
    products.value = []
  } finally {
    loadingProducts.value = false
  }
}

// Открытие модального окна движений
const openMovementsModal = (product) => {
  if (product && product.id) {
    selectedProduct.value = product
    showMovementsModal.value = true
  } else {
    console.error('Неверный объект товара:', product)
  }
}

// Закрытие модального окна движений
const closeMovementsModal = () => {
  showMovementsModal.value = false
  selectedProduct.value = null
}

// Перемещение товара
const transferProduct = (product) => {
  router.push({
    path: '/products/transfers/create',
    query: {
      from_warehouse: warehouseId,
      product_id: product.id,
      quantity: product.warehouse_quantity
    }
  })
}

onMounted(() => {
  loadWarehouse()
  loadWarehouseProducts()
})
</script> 