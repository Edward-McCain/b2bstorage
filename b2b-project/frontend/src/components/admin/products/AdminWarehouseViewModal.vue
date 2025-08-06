<template>
  <div class="fixed inset-0 bg-white/90 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-2/3 shadow-lg rounded-md bg-white">
      <div class="mt-3">
        <div class="flex justify-between items-center mb-4">
          <div>
            <h3 class="text-lg font-medium text-gray-900">{{ warehouse?.name || 'Склад' }}</h3>
            <div v-if="warehouse?.address" class="text-sm text-gray-600 mt-1">{{ warehouse.address }}</div>
            <div v-else class="text-sm text-gray-400 mt-1">Адрес не указан</div>
            <div v-if="warehouse?.user" class="text-xs text-gray-500 mt-1">
              Пользователь: {{ warehouse.user.user_name || warehouse.user.first_name }}
              <span v-if="warehouse.user.company_name"> - {{ warehouse.user.company_name }}</span>
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
          <div class="text-gray-600">Загрузка данных склада...</div>
        </div>

        <div v-else-if="!warehouse" class="text-center py-8">
          <div class="text-gray-500">Склад не найден</div>
        </div>

        <div v-else class="space-y-4">
          <!-- Информация о складе -->
          <div class="bg-gray-50 rounded-lg p-4">
            <h4 class="text-sm font-medium text-gray-900 mb-2">Информация о складе</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
              <div>
                <span class="text-gray-500">Название:</span>
                <span class="ml-2 font-medium">{{ warehouse.name }}</span>
              </div>
              <div>
                <span class="text-gray-500">Адрес:</span>
                <span class="ml-2">{{ warehouse.address || 'Не указан' }}</span>
              </div>
              <div>
                <span class="text-gray-500">Пользователь:</span>
                <span class="ml-2">{{ warehouse.user.user_name || warehouse.user.first_name }}</span>
              </div>
              <div>
                <span class="text-gray-500">Компания:</span>
                <span class="ml-2">{{ warehouse.user.company_name || 'Не указана' }}</span>
              </div>
            </div>
          </div>

          <!-- Товары на складе -->
          <div class="bg-white border border-gray-200 rounded-lg">
            <div class="px-4 py-3 border-b border-gray-200">
              <h4 class="text-sm font-medium text-gray-900">Товары на складе</h4>
              <div class="text-xs text-gray-500 mt-1">
                Всего товаров: {{ products.length }}
              </div>
            </div>

            <div v-if="products.length === 0" class="text-center py-8">
              <Package class="mx-auto h-12 w-12 text-gray-400" />
              <h3 class="mt-2 text-sm font-medium text-gray-900">Склад пуст</h3>
              <p class="mt-1 text-sm text-gray-500">На этом складе пока нет товаров</p>
            </div>

            <div v-else class="overflow-x-auto">
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
                      Пользователь
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Количество
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="product in products" :key="product.product_id" class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div>
                        <div class="text-sm font-medium text-gray-900">{{ product.product_name }}</div>
                        <div v-if="product.product_description" class="text-sm text-gray-500 truncate max-w-xs">
                          {{ product.product_description }}
                        </div>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {{ product.product_article || 'Не указан' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      <div v-if="product.user_name || product.first_name">
                        <div class="font-medium">{{ product.user_name || product.first_name }}</div>
                        <div class="text-gray-500">{{ product.company_name }}</div>
                      </div>
                      <div v-else class="text-gray-400">Не указан</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        {{ product.quantity }} шт.
                      </span>
                    </td>
                  </tr>
                </tbody>
              </table>
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
import { ref, onMounted } from 'vue'
import api from '@/config/api'
import { Loader2, Package } from 'lucide-vue-next'

export default {
  name: 'AdminWarehouseViewModal',
  components: {
    Loader2,
    Package
  },
  props: {
    warehouseId: {
      type: Number,
      required: true
    }
  },
  emits: ['close'],
  setup(props) {
    const warehouse = ref(null)
    const products = ref([])
    const loading = ref(false)

    const loadWarehouseDetails = async () => {
      if (!props.warehouseId) return
      
      loading.value = true
      try {
        const response = await api.get(`/admin/warehouses/${props.warehouseId}`)
        if (response.data.success) {
          warehouse.value = response.data.data.warehouse
          products.value = response.data.data.products
        }
      } catch (error) {
        console.error('Ошибка загрузки данных склада:', error)
      } finally {
        loading.value = false
      }
    }

    onMounted(() => {
      loadWarehouseDetails()
    })

    return {
      warehouse,
      products,
      loading
    }
  }
}
</script> 