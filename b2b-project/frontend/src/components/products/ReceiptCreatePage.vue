<template>
  <div class="min-h-screen bg-gray-50" style="padding-top: 66px;">
    <ProductsMenu />
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="flex items-center justify-between mb-8">
        <h1 class="text-2xl font-bold text-gray-900">{{ t('ReceiptCreatePage_1') }}</h1> <!-- Новое оприходование -->
        <router-link
          to="/products/receipts"
          class="flex items-center gap-2 text-gray-600 hover:text-gray-900 font-medium px-4 py-2 rounded text-sm hover:bg-gray-100 transition-colors"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
          </svg>
        </router-link>
      </div>
      
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
        <form @submit.prevent="handleSubmit" class="space-y-6">
          <!-- Основные поля -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm text-gray-700 mb-1">{{ t('ReceiptCreatePage_2') }}</label> <!-- Номер * -->
              <input v-model="form.number" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm bg-white" :class="{'border-red-400': errors.number}" />
              <div v-if="errors.number" class="text-sm text-red-500 mt-1">{{ errors.number }}</div>
            </div>
            <div>
              <label class="block text-sm text-gray-700 mb-1">{{ t('ReceiptCreatePage_3') }}</label> <!-- Дата * -->
              <LocalizedDatePicker 
                v-model="form.date"
                :enable-time-picker="true"
                :auto-apply="true"
                :class="{'border-red-400': errors.date}"
              />
              <div v-if="errors.date" class="text-sm text-red-500 mt-1">{{ errors.date }}</div>
            </div>
          </div>

          <div class="flex flex-col sm:flex-row gap-2">
            <div class="flex-1">
              <label class="block text-sm text-gray-700 mb-1">{{ t('ReceiptCreatePage_4') }}</label> <!-- Организация * -->
              <div v-if="loadingUserData" class="w-full h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                <Loader2 class="animate-spin h-4 w-4 text-gray-400 mr-2" />
                <span class="text-xs text-gray-500">{{ t('ReceiptCreatePage_5') }}</span> <!-- Загрузка данных пользователя... -->
              </div>
              <input v-else v-model="form.organization" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm bg-white" :class="{'border-red-400': errors.organization}" />
              <div v-if="errors.organization" class="text-sm text-red-500 mt-1">{{ errors.organization }}</div>
            </div>
            <div class="flex-1">
              <label class="block text-sm text-gray-700 mb-1">{{ t('ReceiptCreatePage_6') }}</label> <!-- Склад * -->
              <Multiselect
                v-model="form.warehouse"
                :options="warehouseOptions"
                label="label"
                value="value"
                :object="false"
                :placeholder="t('ReceiptCreatePage_7')"
                :max-height="400"
                class="w-full text-sm multiselect-custom"
                :loading="loadingWarehouses"
                :disabled="loadingWarehouses"
                @click="handleWarehouseClick"
              />
              <div v-if="errors.warehouse" class="text-sm text-red-500 mt-1">{{ errors.warehouse }}</div>
              
              <!-- Блок добавления склада -->
              <div v-if="showWarehouseForm" class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                <h3 class="text-sm font-medium text-gray-700 mb-3">{{ t('ReceiptCreatePage_8') }}</h3> <!-- Создать новый склад -->
                
                <form @submit.prevent="createWarehouse" class="space-y-3">
                  <div>
                    <label class="block text-xs text-gray-600 mb-1">{{ t('ReceiptCreatePage_9') }}</label> <!-- Название склада * -->
                    <input v-model="warehouseForm.name" type="text" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm bg-white" :class="{'border-red-400': warehouseErrors.name}" required />
                    <div v-if="warehouseErrors.name" class="text-xs text-red-500 mt-1">{{ warehouseErrors.name }}</div>
                  </div>
                  
                  <div>
                    <label class="block text-xs text-gray-600 mb-1">{{ t('ReceiptCreatePage_10') }}</label> <!-- Адрес склада -->
                    <textarea v-model="warehouseForm.address" rows="2" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm bg-white"></textarea>
                  </div>

                  <!-- Сообщения об ошибках -->
                  <div v-if="warehouseServerError" class="bg-red-50 border border-red-200 rounded p-3">
                    <div class="text-xs text-red-700">{{ warehouseServerError }}</div>
                  </div>

                  <!-- Кнопки -->
                  <div class="flex justify-end gap-2">
                    <button type="button" @click="closeWarehouseForm" class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium px-3 py-1.5 rounded text-sm transition">
                      {{ t('ReceiptCreatePage_11') }} <!-- Отмена -->
                    </button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-3 py-1.5 rounded text-sm transition flex items-center gap-2" :disabled="warehouseSaving">
                      <Loader2 v-if="warehouseSaving" class="animate-spin h-4 w-4" />
                      <span v-if="warehouseSaving">{{ t('ReceiptCreatePage_12') }}</span> <!-- Создание... -->
                      <span v-else>{{ t('ReceiptCreatePage_13') }}</span> <!-- Создать склад -->
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </div>

          <div class="flex gap-4">
            <div class="flex-1">
              <label class="block text-sm text-gray-700 mb-1">{{ t('ReceiptCreatePage_14') }}</label> <!-- Статус -->
              <Multiselect
                v-model="form.status"
                :options="statusOptions"
                label="label"
                value="value"
                :object="false"
                :placeholder="t('ReceiptCreatePage_15')"
                :max-height="400"
                class="w-full text-sm multiselect-custom"
              />
            </div>
          </div>

          <div class="flex flex-col md:flex-row gap-4 mt-4">
            <div class="flex-1">
              <label class="block text-sm text-gray-700 mb-1">{{ t('ReceiptCreatePage_16') }}</label> <!-- Проект -->
              <input v-model="form.project" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm bg-white" />
            </div>
            <div class="flex-1">
              <label class="block text-sm text-gray-700 mb-1">{{ t('ReceiptCreatePage_17') }}</label> <!-- Накладные расходы -->
              <input v-model="form.overhead_costs" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm bg-white" :placeholder="t('ReceiptCreatePage_18')" /> <!-- 0.00 -->
            </div>
          </div>

          <div>
            <label class="block text-sm text-gray-700 mb-1">{{ t('ReceiptCreatePage_19') }}</label> <!-- Комментарий -->
            <textarea v-model="form.comment" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm bg-white"></textarea>
          </div>

          <!-- Загрузка файлов -->
          <div>
            <label class="block text-sm text-gray-700 mb-1">{{ t('ReceiptCreatePage_20') }}</label> <!-- Файлы -->
            <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center relative" :class="{ 'border-blue-400 bg-blue-50': uploading }">
              <div v-if="uploading" class="absolute inset-0 bg-blue-50 bg-opacity-75 flex items-center justify-center rounded-lg z-10">
                <div class="text-center flex flex-col items-center">
                  <Loader2 class="animate-spin h-8 w-8 text-blue-600 mb-2" />
                  <p class="text-sm text-blue-700">{{ t('ReceiptCreatePage_21') }}</p> <!-- Загрузка файлов... -->
                </div>
              </div>
              <input ref="fileInput" type="file" multiple @change="handleFileUpload" class="hidden" :disabled="uploading" />
              <button type="button" @click="$refs.fileInput.click()" class="bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white font-semibold px-4 py-2 rounded-lg transition text-sm" :disabled="uploading">
                <span v-if="uploading">{{ t('ReceiptCreatePage_23') }}</span> <!-- Загрузка... -->
                <span v-else>{{ t('ReceiptCreatePage_22') }}</span> <!-- Выбрать файлы -->
              </button>
              <p class="text-xs text-gray-500 mt-2">{{ t('ReceiptCreatePage_24') }}</p> <!-- Перетащите файлы сюда или нажмите кнопку -->
            </div>
            <div v-if="uploadedFiles.length > 0" class="mt-4">
              <h4 class="text-sm font-medium text-gray-700 mb-2">{{ t('ReceiptCreatePage_25') }}</h4> <!-- Загруженные файлы: -->
              <div class="space-y-2">
                <div v-for="(file, index) in uploadedFiles" :key="`file-${index}-${file.id}`" class="flex items-center justify-between bg-gray-50 p-3 rounded-lg">
                  <div class="flex items-center gap-3">
                    <div v-if="file.uploading" class="flex items-center gap-2">
                      <Loader2 class="animate-spin h-4 w-4 text-blue-600" />
                      <span class="text-sm text-gray-500">{{ t('ReceiptCreatePage_26') }}</span> <!-- Загрузка... -->
                    </div>
                    <template v-else>
                      <a v-if="file.file_url" :href="file.file_url" target="_blank" class="text-blue-600 hover:underline text-sm">{{ file.filename }}</a>
                      <span v-else class="text-sm text-gray-700">{{ file.filename }}</span>
                      <span class="text-xs text-gray-500">{{ file.size_mb }} {{ t('ReceiptCreatePage_27') }}</span> <!-- МБ -->
                      <span class="text-xs text-gray-500">{{ file.employee }}</span>
                    </template>
                  </div>
                  <button type="button" @click="removeFile(file.id)" class="text-red-500 hover:text-red-700" :disabled="file.uploading">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Добавление товаров -->
          <div>
            <label class="block text-sm text-gray-700 mb-1">{{ t('ReceiptCreatePage_28') }}</label> <!-- Товары -->
            <div class="flex gap-2">
              <div class="flex-1">
                <Multiselect
                  v-model="selectedProduct"
                  :options="productOptions"
                  label="label"
                  value="value"
                  :object="true"
                  :placeholder="t('ReceiptCreatePage_29')"
                  searchable
                  :search-placeholder="t('ReceiptCreatePage_30')"
                  :max-height="400"
                  class="w-full text-sm multiselect-custom"
                  :loading="loadingProducts"
                  :disabled="loadingProducts"
                  @search-change="onProductSearch"
                />
              </div>
              <button type="button" @click="addProduct" :disabled="!selectedProduct" class="bg-green-600 hover:bg-green-700 disabled:bg-gray-400 text-white font-semibold px-4 py-2 rounded-lg transition text-sm">
                {{ t('ReceiptCreatePage_31') }} <!-- Добавить -->
              </button>
            </div>
          </div>

          <!-- Таблица позиций -->
          <div v-if="positions.length > 0" class="mt-6">
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200 text-sm">
              <thead>
                <tr class="bg-gray-50">
                  <th class="px-3 py-2 text-left font-semibold text-gray-700">{{ t('ReceiptCreatePage_32') }}</th> <!-- Товар -->
                  <th class="px-3 py-2 text-center font-semibold text-gray-700">{{ t('ReceiptCreatePage_33') }}</th> <!-- Количество -->
                  <th class="px-3 py-2 text-center font-semibold text-gray-700">{{ t('ReceiptCreatePage_34') }}</th> <!-- Цена -->
                  <th class="px-3 py-2 text-center font-semibold text-gray-700">{{ t('ReceiptCreatePage_35') }}</th> <!-- Сумма -->
                  <th class="px-3 py-2 text-center font-semibold text-gray-700">{{ t('ReceiptCreatePage_36') }}</th> <!-- Действия -->
                </tr>
              </thead>
              <tbody>
                <tr v-for="(position, index) in positions" :key="`position-${index}-${position.product_id}`" class="hover:bg-gray-50">
                  <td class="px-3 py-2 text-left">{{ position.name }}</td>
                  <td class="px-3 py-2 text-center">
                    <input :value="position.quantity" @input="updatePositionQuantity(index, $event.target.value)" type="number" step="0.001" class="w-full sm:w-20 border border-gray-300 rounded px-2 py-1 text-sm text-center bg-white" />
                  </td>
                  <td class="px-3 py-2 text-center">
                    <input :value="position.price" @input="updatePositionPrice(index, $event.target.value)" type="number" step="0.01" class="w-full sm:w-24 border border-gray-300 rounded px-2 py-1 text-sm text-center bg-white" />
                  </td>
                  <td class="px-3 py-2 text-center">{{ calculatePositionTotal(position) }}</td>
                  <td class="px-3 py-2 text-center">
                    <button type="button" @click="removePosition(index)" class="text-red-500 hover:text-red-700">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                      </svg>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
            </div>
          </div>

          <!-- Итого -->
          <div v-if="positions.length > 0" class="text-right">
            <div class="text-lg font-semibold text-gray-900">
              {{ t('ReceiptCreatePage_37') }} {{ total.toFixed(2) }}  <!-- Итого: -->
            </div>
          </div>

          <!-- Сообщения об ошибках -->
          <div v-if="serverError" class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="text-sm text-red-700">{{ serverError }}</div>
          </div>

          <!-- Сообщения об успехе -->
          <div v-if="successMessage" class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="text-sm text-green-700">{{ successMessage }}</div>
          </div>

          <!-- Кнопки -->
          <div class="flex justify-end gap-2 mt-6">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded-lg shadow transition text-sm flex items-center gap-2" :disabled="saving || uploading">
              <Loader2 v-if="saving" class="animate-spin h-4 w-4" />
              <span v-if="saving">{{ t('ReceiptCreatePage_39') }}</span> <!-- Сохранение... -->
              <span v-else>{{ t('ReceiptCreatePage_40') }}</span> <!-- Сохранить -->
            </button>
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
import ProductsMenu from './ProductsMenu.vue'
import NoWarehousesModal from '../NoWarehousesModal.vue'
import { useRouter } from 'vue-router'
import { ref, computed, onMounted } from 'vue'
import { apiRequest } from '@/config/api'
import { useWarehouseCheck } from '@/composables/useWarehouseCheck'
import Multiselect from '@vueform/multiselect'
import '@vueform/multiselect/themes/default.css'
import toastr from 'toastr'
import { Loader2 } from 'lucide-vue-next'
import { t } from '@/locales'

// Устанавливаем заголовок страницы
document.title = 'B2B SKLAD - Оприходования'

const router = useRouter()
function goBack() {
  router.push('/products/receipts')
}

const form = ref({
  number: '',
  date: '',
  organization: '',
  project: '',
  warehouse: null,
  status: 'draft',
  comment: '',
  overhead_costs: '',
  total: 0
})
const errors = ref({})
const serverError = ref('')
const saving = ref(false)
const uploading = ref(false)
const successMessage = ref('')

const products = ref([])
const loadingProducts = ref(true)
const loadingUserData = ref(true)
const selectedProduct = ref(null)
const positions = ref([])
const uploadedFiles = ref([])
const fileInput = ref(null)
const userData = ref(null)
const productSearch = ref('')

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

// Переменные для формы создания склада
const showWarehouseForm = ref(false)
const warehouseForm = ref({
  name: '',
  address: ''
})
const warehouseErrors = ref({})
const warehouseServerError = ref('')
const warehouseSaving = ref(false)

const statusOptions = [
  { label: t('ReceiptCreatePage_54'), value: 'draft' }, // Черновик
  { label: t('ReceiptCreatePage_55'), value: 'posted' } // Проведено
]

const productOptions = computed(() => {
  if (!Array.isArray(products.value)) return []
  return products.value.map(p => ({
    label: `${p.code ? p.code + ' ' : ''}${p.name}${p.article ? ' (' + p.article + ')' : ''}`,
    value: p.id,
    product: p
  }))
})



const total = computed(() => {
  let sum = 0
  for (const pos of positions.value) {
    const quantity = parseFloat(pos.quantity || 0)
    const price = parseFloat(pos.price || 0)
    sum += quantity * price
  }
  const overhead = parseFloat(form.value.overhead_costs || 0)
  return sum + overhead
})

async function loadUserData() {
  try {
    loadingUserData.value = true
    const response = await apiRequest('/user', { method: 'GET' })
    if (response.ok) {
      // Проверяем разные возможные структуры ответа
      let user = null
      if (response.data.user) {
        user = response.data.user
      } else if (response.data.data && response.data.data.user) {
        user = response.data.data.user
      } else if (response.data.id) {
        // Если ответ содержит данные пользователя напрямую
        user = response.data
      }
      
      if (user) {
        userData.value = user
        if (user.company_name && !form.value.organization) {
          form.value.organization = user.company_name
        }
      }
    }
  } catch (error) {
    console.error(t('ReceiptCreatePage_52') + error) // Ошибка загрузки данных пользователя:
  } finally {
    loadingUserData.value = false
  }
}

async function loadProducts(search = '') {
  try {
    loadingProducts.value = true
    const response = await apiRequest(`/products?search=${encodeURIComponent(search)}`, { method: 'GET' })
    if (response.ok && response.data) {
      let productsData = response.data
      if (response.data.data && Array.isArray(response.data.data)) {
        productsData = response.data.data
      } else if (response.data.data && response.data.data.data && Array.isArray(response.data.data.data)) {
        productsData = response.data.data.data
      }
      products.value = Array.isArray(productsData) ? productsData : []
    }
  } catch (error) {
    console.error(t('ReceiptCreatePage_53') + error) // Ошибка загрузки товаров:
    products.value = []
  } finally {
    loadingProducts.value = false
  }
}

function onProductSearch(query) {
  // Не отправлять запрос, если query пустой или совпадает с выбранным товаром
  if (!query || (selectedProduct.value && selectedProduct.value.label === query)) return
  productSearch.value = query
  loadProducts(query)
}



function handleWarehouseClick() {
  if (warehouses.value.length === 0) {
    showWarehouseForm.value = true
  }
}

function closeWarehouseForm() {
  showWarehouseForm.value = false
  warehouseForm.value = { name: '', address: '' }
  warehouseErrors.value = {}
  warehouseServerError.value = ''
}

async function createWarehouse() {
  warehouseSaving.value = true
  warehouseErrors.value = {}
  warehouseServerError.value = ''
  
  try {
    const response = await apiRequest('/warehouses', {
      method: 'POST',
      body: JSON.stringify(warehouseForm.value)
    })
    
    if (response.ok && response.data.success) {
      // Обновляем список складов
      await loadWarehouses()
      
      // Устанавливаем новый склад как выбранный
      form.value.warehouse = response.data.data.id
      
      // Скрываем блок добавления склада
      closeWarehouseForm()
      
      // Показываем уведомление
      toastr.success(t('ReceiptCreatePage_49')) // Склад успешно создан
    } else {
      warehouseServerError.value = response.data.message || t('ReceiptCreatePage_50') // Ошибка при создании склада
    }
  } catch (error) {
    console.error(t('ReceiptCreatePage_50') + error) // Ошибка при создании склада:
    warehouseServerError.value = t('ReceiptCreatePage_51') // Произошла ошибка при создании склада
  } finally {
    warehouseSaving.value = false
  }
}

onMounted(async () => {
  const today = new Date()
  const year = today.getFullYear()
  const month = String(today.getMonth() + 1).padStart(2, '0')
  const day = String(today.getDate()).padStart(2, '0')
  const hours = String(today.getHours()).padStart(2, '0')
  const minutes = String(today.getMinutes()).padStart(2, '0')
  form.value.date = `${year}-${month}-${day}T${hours}:${minutes}`
  
  await loadUserData()
  loadProducts()
  // Проверяем наличие складов и показываем модальное окно если их нет
  await checkWarehousesAndShowModal()
})

function validate() {
  let isValid = true
  errors.value = {}
  if (!form.value.number) {
    errors.value.number = t('ReceiptCreatePage_41') // Номер обязателен
    isValid = false
  }
  if (!form.value.date) {
    errors.value.date = t('ReceiptCreatePage_42') // Дата обязательна
    isValid = false
  }
  if (!form.value.organization) {
    errors.value.organization = t('ReceiptCreatePage_43') // Организация обязательна
    isValid = false
  }
  if (!form.value.warehouse) {
    errors.value.warehouse = t('ReceiptCreatePage_44') // Склад обязателен
    isValid = false
  }
  if (positions.value.length === 0) {
    errors.value.positions = t('ReceiptCreatePage_45') // Добавьте хотя бы одну позицию
    isValid = false
  }
  return isValid
}

async function handleSubmit() {
  if (!validate()) return
  saving.value = true
  serverError.value = ''
  successMessage.value = ''
  try {
    const receiptData = {
      ...form.value,
      positions: positions.value,
      receipt_files: uploadedFiles.value.map(file => ({
        filename: file.filename,
        size_mb: file.size_mb,
        file_url: file.file_url,
        employee: file.employee
      }))
    }
    const response = await apiRequest('/receipts', {
      method: 'POST',
      body: JSON.stringify(receiptData)
    })
    if (response.ok && response.data.success) {
      successMessage.value = t('ReceiptCreatePage_46') // Оприходование успешно создано!
      setTimeout(() => {
        router.push('/products/receipts')
      }, 1000)
    } else {
      serverError.value = response.data.message || t('ReceiptCreatePage_47') // Произошла ошибка при создании оприходования.
    }
  } catch (error) {
    console.error(t('ReceiptCreatePage_48') + error) // Ошибка при создании оприходования:
    serverError.value = t('ReceiptCreatePage_47') // Произошла ошибка при создании оприходования.
  } finally {
    saving.value = false
  }
}

function addProduct() {
  if (selectedProduct.value) {
    const product = selectedProduct.value.product
    positions.value.push({
      product_id: product.id,
      name: product.name,
      code: product.code || '',
      article: product.article || '',
      quantity: 1,
      price: 0,
      balance: 0,
      reason: '',
      gtd: '',
      rnpt: '',
      country: product.country || ''
    })
    selectedProduct.value = null
  }
}

function updatePositionQuantity(index, value) {
  positions.value[index].quantity = value
}
function updatePositionPrice(index, value) {
  positions.value[index].price = value
}

function removePosition(index) {
  positions.value.splice(index, 1)
}

function calculatePositionTotal(position) {
  const quantity = parseFloat(position.quantity || 0)
  const price = parseFloat(position.price || 0)
  return (quantity * price).toFixed(2)
}

async function handleFileUpload(event) {
  const files = event.target.files
  if (!files.length) return
  uploading.value = true
  
  // Загружаем каждый файл на сервер
  for (const file of files) {
    // Добавляем файл в список с флагом загрузки
    const fileId = Date.now() + Math.random()
    uploadedFiles.value.push({
      id: fileId,
      filename: file.name,
      size_mb: (file.size / 1048576).toFixed(2),
      uploading: true,
      employee: userData.value?.username || t('ReceiptCreatePage_56') // Неизвестный
    })
    
    try {
      const formData = new FormData()
      formData.append('file', file)
      
      const response = await apiRequest('/receipt-files/draft', {
        method: 'POST',
        body: formData,
        headers: {}
      })
      
      if (response.ok && response.data) {
        // Обновляем файл с полученными данными
        const fileIndex = uploadedFiles.value.findIndex(f => f.id === fileId)
        if (fileIndex !== -1) {
          uploadedFiles.value[fileIndex] = {
            id: response.data.id || fileId,
            filename: response.data.filename || file.name,
            size_mb: response.data.size_mb || (file.size / 1048576).toFixed(2),
            file_url: response.data.file_url || '',
            employee: response.data.employee || (userData.value?.username || t('ReceiptCreatePage_56')), // Неизвестный
            uploading: false
          }
        }
      } else {
        console.error('Ошибка загрузки файла:', response.data)
        // Удаляем файл из списка при ошибке
        const fileIndex = uploadedFiles.value.findIndex(f => f.id === fileId)
        if (fileIndex !== -1) {
          uploadedFiles.value.splice(fileIndex, 1)
        }
      }
    } catch (error) {
      console.error('Ошибка при загрузке файла:', error)
      // Удаляем файл из списка при ошибке
      const fileIndex = uploadedFiles.value.findIndex(f => f.id === fileId)
      if (fileIndex !== -1) {
        uploadedFiles.value.splice(fileIndex, 1)
      }
    }
  }
  
  uploading.value = false
  event.target.value = ''
}

async function removeFile(id) {
  const index = uploadedFiles.value.findIndex(file => file.id === id)
  if (index !== -1) {
    const file = uploadedFiles.value[index]
    
    // Не удаляем файл, если он в процессе загрузки
    if (file.uploading) {
      return
    }
    
    // Если у файла есть числовой id (загружен на сервер), удаляем его
    if (typeof file.id === 'number') {
      try {
        await apiRequest(`/receipt-files/${file.id}`, {
          method: 'DELETE'
        })
      } catch (error) {
        console.error('Ошибка при удалении файла с сервера:', error)
      }
    }
    
    uploadedFiles.value.splice(index, 1)
  }
}
</script> 