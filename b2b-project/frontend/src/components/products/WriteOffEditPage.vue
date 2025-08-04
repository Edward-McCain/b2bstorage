<template>
  <div class="min-h-screen bg-gray-50" style="padding-top: 66px;">
    <ProductsMenu />
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="flex items-center justify-between mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Редактировать списание</h1>
        <button @click="goBack" class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold px-6 py-2 rounded-lg border shadow transition text-sm">
          Назад
        </button>
      </div>
      
      <form @submit.prevent="handleSubmit" class="space-y-6">
        <!-- Основные поля -->
        <div class="bg-white rounded-xl shadow p-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm text-gray-700 mb-1">Номер *</label>
              <div v-if="loading" class="flex items-center justify-center h-10 bg-gray-100 rounded-lg">
                <Loader2 class="animate-spin h-4 w-4 text-blue-600 mr-2" />
                <span class="text-sm text-gray-500">Загрузка...</span>
              </div>
              <input v-else v-model="form.number" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm bg-white" :class="{'border-red-400': errors.number}" />
              <div v-if="errors.number" class="text-sm text-red-500 mt-1">{{ errors.number }}</div>
            </div>
            <div>
              <label class="block text-sm text-gray-700 mb-1">Дата *</label>
              <div v-if="loading" class="flex items-center justify-center h-10 bg-gray-100 rounded-lg">
                <Loader2 class="animate-spin h-4 w-4 text-blue-600 mr-2" />
                <span class="text-sm text-gray-500">Загрузка...</span>
              </div>
              <LocalizedDatePicker 
              v-else 
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
              <label class="block text-sm text-gray-700 mb-1">Организация *</label>
              <div v-if="loading" class="flex items-center justify-center h-10 bg-gray-100 rounded-lg">
                <Loader2 class="animate-spin h-4 w-4 text-blue-600 mr-2" />
                <span class="text-sm text-gray-500">Загрузка...</span>
              </div>
              <input v-else v-model="form.organization" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm bg-white" :class="{'border-red-400': errors.organization}" />
              <div v-if="errors.organization" class="text-sm text-red-500 mt-1">{{ errors.organization }}</div>
            </div>
            <div class="flex-1">
              <label class="block text-sm text-gray-700 mb-1">Склад *</label>
              <div v-if="loading || loadingWarehouses" class="flex items-center justify-center h-10 bg-gray-100 rounded-lg">
                <Loader2 class="animate-spin h-4 w-4 text-blue-600 mr-2" />
                <span class="text-sm text-gray-500">Загрузка складов...</span>
              </div>
              <Multiselect
                v-else
                v-model="form.warehouse"
                :options="warehouseOptions"
                label="label"
                value="value"
                :object="false"
                placeholder="Выберите склад"
                :max-height="400"
                class="w-full text-sm multiselect-custom"
                :loading="loadingWarehouses"
                :disabled="loadingWarehouses"
                @click="handleWarehouseClick"
              />
              <div v-if="errors.warehouse" class="text-sm text-red-500 mt-1">{{ errors.warehouse }}</div>
              
              <!-- Блок добавления склада -->
              <div v-if="showWarehouseForm" class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                <h3 class="text-sm font-medium text-gray-700 mb-3">Создать новый склад</h3>
                
                <form @submit.prevent="createWarehouse" class="space-y-3">
                  <div>
                    <label class="block text-xs text-gray-600 mb-1">Название склада *</label>
                    <input v-model="warehouseForm.name" type="text" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm bg-white" :class="{'border-red-400': warehouseErrors.name}" required />
                    <div v-if="warehouseErrors.name" class="text-xs text-red-500 mt-1">{{ warehouseErrors.name }}</div>
                  </div>
                  
                  <div>
                    <label class="block text-xs text-gray-600 mb-1">Адрес склада</label>
                    <textarea v-model="warehouseForm.address" rows="2" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm bg-white"></textarea>
                  </div>

                  <!-- Сообщения об ошибках -->
                  <div v-if="warehouseServerError" class="bg-red-50 border border-red-200 rounded p-3">
                    <div class="text-xs text-red-700">{{ warehouseServerError }}</div>
                  </div>

                  <!-- Кнопки -->
                  <div class="flex justify-end gap-2">
                    <button type="button" @click="closeWarehouseForm" class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium px-3 py-1.5 rounded text-sm transition">
                      Отмена
                    </button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-3 py-1.5 rounded text-sm transition flex items-center gap-2" :disabled="warehouseSaving">
                      <Loader2 v-if="warehouseSaving" class="animate-spin h-4 w-4" />
                      <span v-if="warehouseSaving">Создание...</span>
                      <span v-else>Создать склад</span>
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </div>

          <div class="flex gap-4 mt-4">
            <div class="flex-1">
              <label class="block text-sm text-gray-700 mb-1">Статус</label>
              <div v-if="loading" class="flex items-center justify-center h-10 bg-gray-100 rounded-lg">
                <Loader2 class="animate-spin h-4 w-4 text-blue-600 mr-2" />
                <span class="text-sm text-gray-500">Загрузка...</span>
              </div>
              <Multiselect
                v-else
                v-model="form.status"
                :options="statusOptions"
                label="label"
                value="value"
                :object="false"
                placeholder="Выберите статус"
                :max-height="400"
                class="w-full text-sm multiselect-custom"
              />
            </div>
          </div>

          <div class="flex flex-col md:flex-row gap-4 mt-4">
            <div class="flex-1">
              <label class="block text-sm text-gray-700 mb-1">Проект</label>
              <div v-if="loading" class="flex items-center justify-center h-10 bg-gray-100 rounded-lg">
                <Loader2 class="animate-spin h-4 w-4 text-blue-600 mr-2" />
                <span class="text-sm text-gray-500">Загрузка...</span>
              </div>
              <input v-else v-model="form.project" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm bg-white" />
            </div>
            <div class="flex-1">
              <label class="block text-sm text-gray-700 mb-1">Накладные расходы</label>
              <div v-if="loading" class="flex items-center justify-center h-10 bg-gray-100 rounded-lg">
                <Loader2 class="animate-spin h-4 w-4 text-blue-600 mr-2" />
                <span class="text-sm text-gray-500">Загрузка...</span>
              </div>
              <input v-else v-model="form.overhead_costs" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm bg-white" placeholder="0.00" />
            </div>
          </div>

          <div class="mt-4">
            <label class="block text-sm text-gray-700 mb-1">Комментарий</label>
            <div v-if="loading" class="flex items-center justify-center h-20 bg-gray-100 rounded-lg">
              <Loader2 class="animate-spin h-4 w-4 text-blue-600 mr-2" />
              <span class="text-sm text-gray-500">Загрузка...</span>
            </div>
            <textarea v-else v-model="form.comment" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm bg-white"></textarea>
          </div>
        </div>

        <!-- Загрузка файлов -->
        <div class="bg-white rounded-xl shadow p-6">
          <label class="block text-sm text-gray-700 mb-1">Файлы</label>
          <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center relative" :class="{ 'border-blue-400 bg-blue-50': uploading }">
            <div v-if="uploading" class="absolute inset-0 bg-blue-50 bg-opacity-75 flex items-center justify-center rounded-lg z-10">
              <div class="text-center flex flex-col items-center">
                <Loader2 class="animate-spin h-8 w-8 text-blue-600 mb-2" />
                <p class="text-sm text-blue-700">Загрузка файлов...</p>
              </div>
            </div>
            <input ref="fileInput" type="file" multiple @change="handleFileUpload" class="hidden" :disabled="uploading" />
            <button type="button" @click="$refs.fileInput.click()" class="bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white font-semibold px-4 py-2 rounded-lg transition text-sm" :disabled="uploading">
              <span v-if="uploading">Загрузка...</span>
              <span v-else>Выбрать файлы</span>
            </button>
            <p class="text-xs text-gray-500 mt-2">Перетащите файлы сюда или нажмите кнопку</p>
          </div>
          <div v-if="loadingFiles" class="mt-4">
            <div class="flex items-center justify-center py-4">
              <Loader2 class="animate-spin h-6 w-6 text-blue-600 mr-2" />
              <span class="text-sm text-gray-600">Загрузка файлов...</span>
            </div>
          </div>
          <div v-else-if="uploadedFiles.length > 0" class="mt-4">
            <h4 class="text-sm font-medium text-gray-700 mb-2">Загруженные файлы:</h4>
            <div class="space-y-2">
              <div v-for="(file, index) in uploadedFiles" :key="`file-${index}-${file.id}`" class="flex items-center justify-between bg-gray-50 p-3 rounded-lg">
                <div class="flex items-center gap-3">
                  <div v-if="file.uploading" class="flex items-center gap-2">
                    <Loader2 class="animate-spin h-4 w-4 text-blue-600" />
                    <span class="text-sm text-gray-500">Загрузка...</span>
                  </div>
                  <template v-else>
                    <a v-if="file.file_url" :href="file.file_url" target="_blank" class="text-blue-600 hover:underline text-sm">{{ file.filename }}</a>
                    <span v-else class="text-sm text-gray-700">{{ file.filename }}</span>
                    <span class="text-xs text-gray-500">{{ file.size_mb }} МБ</span>
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
        <div class="bg-white rounded-xl shadow p-6">
          <label class="block text-sm text-gray-700 mb-1">Товары</label>
          <div class="flex gap-2">
            <div class="flex-1">
              <Multiselect
                v-model="selectedProduct"
                :options="productOptions"
                label="label"
                value="value"
                :object="true"
                placeholder="Выберите товар"
                searchable
                :search-placeholder="'Поиск товара'"
                :max-height="400"
                class="w-full text-sm multiselect-custom"
                :loading="loadingProducts"
                :disabled="loadingProducts"
                @search-change="onProductSearch"
              />
            </div>
            <button type="button" @click="addProduct" :disabled="!selectedProduct" class="bg-green-600 hover:bg-green-700 disabled:bg-gray-400 text-white font-semibold px-4 py-2 rounded-lg transition text-sm">
              Добавить
            </button>
          </div>
        </div>

        <!-- Таблица позиций -->
        <div v-if="loadingPositions" class="bg-white rounded-xl shadow p-6">
          <div class="flex items-center justify-center py-8">
            <Loader2 class="animate-spin h-6 w-6 text-blue-600 mr-2" />
            <span class="text-sm text-gray-600">Загрузка позиций...</span>
          </div>
        </div>
        <div v-else-if="positions.length > 0" class="bg-white rounded-xl shadow p-6">
          <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead>
              <tr class="bg-gray-50">
                <th class="px-3 py-2 text-left font-semibold text-gray-700">Товар</th>
                <th class="px-3 py-2 text-center font-semibold text-gray-700">Количество</th>
                <th class="px-3 py-2 text-center font-semibold text-gray-700">Цена</th>
                <th class="px-3 py-2 text-center font-semibold text-gray-700">Сумма</th>
                <th class="px-3 py-2 text-center font-semibold text-gray-700">Причина списания</th>
                <th class="px-3 py-2 text-center font-semibold text-gray-700">Действия</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(position, index) in positions" :key="index" class="hover:bg-gray-50">
                <td class="px-3 py-2">
                  <div class="text-sm font-medium text-gray-900">{{ position.name }}</div>
                  <div class="text-xs text-gray-500">{{ position.code }}</div>
                </td>
                <td class="px-3 py-2 text-center">
                  <input 
                    v-model="position.quantity" 
                    type="number" 
                    step="0.001"
                    class="w-20 text-center border border-gray-300 rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition"
                    @input="updatePositionQuantity(index, $event.target.value)"
                  />
                </td>
                <td class="px-3 py-2 text-center">
                  <input 
                    v-model="position.price" 
                    type="number" 
                    step="0.01"
                    class="w-24 text-center border border-gray-300 rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition"
                    @input="updatePositionPrice(index, $event.target.value)"
                  />
                </td>
                <td class="px-3 py-2 text-center text-sm font-medium">
                  {{ calculatePositionTotal(position) }}
                </td>
                <td class="px-3 py-2 text-center">
                  <input 
                    v-model="position.reason" 
                    type="text" 
                    class="w-full text-center border border-gray-300 rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition"
                    placeholder="Причина списания"
                  />
                </td>
                <td class="px-3 py-2 text-center">
                  <button 
                    type="button"
                    @click="removePosition(index)"
                    :disabled="position.deleting"
                    class="text-red-600 hover:text-red-800 p-1 rounded hover:bg-red-50 transition-colors"
                  >
                    <X v-if="!position.deleting" class="w-4 h-4" />
                    <Loader2 v-else class="animate-spin w-4 h-4" />
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
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
            <span v-if="saving">Сохранение...</span>
            <span v-else-if="uploading">Загрузка...</span>
            <span v-else>Сохранить изменения</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import ProductsMenu from './ProductsMenu.vue'
import { useRouter, useRoute } from 'vue-router'
import { ref, computed, onMounted } from 'vue'
import { apiRequest } from '@/config/api'
import Multiselect from '@vueform/multiselect'
import '@vueform/multiselect/themes/default.css'
import toastr from 'toastr'
import { Loader2, X } from 'lucide-vue-next'

// Устанавливаем заголовок страницы
document.title = 'B2B SKLAD - Списания'

const router = useRouter()
const route = useRoute()
const writeOffId = route.params.id

function goBack() {
  router.push('/products/write-offs')
}

const loading = ref(true)
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

const userData = ref(null)
const loadingUserData = ref(true)
const products = ref([])
const loadingProducts = ref(true)
const selectedProduct = ref(null)
const positions = ref([])
const loadingPositions = ref(false)
const uploadedFiles = ref([])
const loadingFiles = ref(false)
const fileInput = ref(null)
const productSearch = ref('')

// Переменные для складов
const warehouses = ref([])
const loadingWarehouses = ref(true)
const showWarehouseForm = ref(false)
const warehouseForm = ref({
  name: '',
  address: ''
})
const warehouseErrors = ref({})
const warehouseServerError = ref('')
const warehouseSaving = ref(false)

const statusOptions = [
  { label: 'Черновик', value: 'draft' },
  { label: 'Проведено', value: 'posted' }
]

const productOptions = computed(() => {
  if (!Array.isArray(products.value)) return []
  return products.value.map(p => ({
    label: `${p.code ? p.code + ' ' : ''}${p.name}${p.article ? ' (' + p.article + ')' : ''}`,
    value: p.id,
    product: p
  }))
})

const warehouseOptions = computed(() => {
  if (!Array.isArray(warehouses.value)) return []
  return warehouses.value.map(w => ({
    label: w.name,
    value: w.id
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

async function loadWriteOff() {
  try {
    const response = await apiRequest(`/write-offs/${writeOffId}`, { method: 'GET' })
    if (response.ok && response.data.success) {
      const writeOff = response.data.data
      
      // Форматируем дату для input datetime-local
      const date = new Date(writeOff.date)
      const formattedDate = date.toISOString().slice(0, 16)
      
      form.value = {
        number: writeOff.number || '',
        date: formattedDate,
        organization: writeOff.organization || '',
        project: writeOff.project || '',
        warehouse: writeOff.warehouse_id,
        status: writeOff.status || 'draft',
        comment: writeOff.comment || '',
        overhead_costs: writeOff.overhead_costs || '',
        total: writeOff.total || 0
      }
      
      // Загружаем позиции
      loadingPositions.value = true
      if (writeOff.positions && Array.isArray(writeOff.positions)) {
        positions.value = writeOff.positions.map(pos => ({
          id: pos.id,
          product_id: pos.product_id,
          name: pos.product_name || pos.name,
          code: pos.code || '',
          article: pos.article || '',
          quantity: pos.quantity,
          price: pos.price,
          balance: pos.balance || 0,
          reason: pos.reason || '',
          gtd: pos.gtd || '',
          rnpt: pos.rnpt || '',
          country: pos.country || ''
        }))
      }
      loadingPositions.value = false
      
      // Загружаем файлы
      loadingFiles.value = true
      if (writeOff.files && Array.isArray(writeOff.files)) {
        uploadedFiles.value = writeOff.files.map(file => ({
          id: file.id,
          filename: file.filename,
          file_url: file.file_url,
          size_mb: file.size_mb
        }))
      }
      loadingFiles.value = false
    } else {
      toastr.error('Списание не найдено')
      router.push('/products/write-offs')
    }
  } catch (error) {
    console.error('Ошибка загрузки списания:', error)
    toastr.error('Ошибка при загрузке списания')
    router.push('/products/write-offs')
  } finally {
    loading.value = false
  }
}

async function loadUserData() {
  try {
    loadingUserData.value = true
    const response = await apiRequest('/user', { method: 'GET' })
    if (response.ok && response.data.success) {
      userData.value = response.data.data
    }
  } catch (error) {
    console.error('Ошибка загрузки данных пользователя:', error)
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
    console.error('Ошибка загрузки товаров:', error)
    products.value = []
  } finally {
    loadingProducts.value = false
  }
}

function onProductSearch(query) {
  if (!query || (selectedProduct.value && selectedProduct.value.label === query)) return
  productSearch.value = query
  loadProducts(query)
}

async function loadWarehouses() {
  try {
    loadingWarehouses.value = true
    const response = await apiRequest('/warehouses', { method: 'GET' })
    if (response.ok && response.data.success) {
      warehouses.value = response.data.data || []
    } else {
      warehouses.value = []
    }
  } catch (error) {
    console.error('Ошибка загрузки складов:', error)
    warehouses.value = []
  } finally {
    loadingWarehouses.value = false
  }
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
      await loadWarehouses()
      form.value.warehouse = response.data.data.id
      toastr.success('Склад успешно создан')
      closeWarehouseForm()
    } else {
      warehouseServerError.value = response.data.message || 'Произошла ошибка при создании склада'
    }
  } catch (error) {
    console.error('Ошибка при создании склада:', error)
    warehouseServerError.value = 'Произошла ошибка при создании склада'
  } finally {
    warehouseSaving.value = false
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

async function removePosition(index) {
  const position = positions.value[index]
  
  position.deleting = true

  try {
    await new Promise(resolve => setTimeout(resolve, 300))
    positions.value.splice(index, 1)
  } catch (error) {
    console.error('Ошибка при удалении товара:', error)
  } finally {
    if (position) {
      position.deleting = false
    }
  }
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
      employee: userData.value?.username || 'Неизвестный'
    })
    
    try {
      const formData = new FormData()
      formData.append('file', file)
      
      const response = await apiRequest('/write-off-files/draft', {
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
            employee: response.data.employee || (userData.value?.username || 'Неизвестный'),
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
    
    // При редактировании списания новые файлы еще не сохранены в БД,
    // а существующие файлы удаляются через обновление списания
    // поэтому просто удаляем из списка загруженных файлов
    uploadedFiles.value.splice(index, 1)
  }
}

function validate() {
  let isValid = true
  errors.value = {}
  
  if (!form.value.number.trim()) {
    errors.value.number = 'Номер списания обязателен'
    isValid = false
  }
  
  if (!form.value.date) {
    errors.value.date = 'Дата обязательна'
    isValid = false
  }
  
  if (!form.value.organization.trim()) {
    errors.value.organization = 'Организация обязательна'
    isValid = false
  }
  
  if (!form.value.warehouse) {
    errors.value.warehouse = 'Склад обязателен'
    isValid = false
  }
  
  if (positions.value.length === 0) {
    errors.value.positions = 'Добавьте хотя бы один товар'
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
    // Подготавливаем данные для отправки
    const submitData = {
      ...form.value,
      positions: positions.value,
      write_off_files: uploadedFiles.value.map(file => ({
        filename: file.filename,
        size_mb: file.size_mb,
        file_url: file.file_url,
        employee: file.employee
      }))
    }
    
    const response = await apiRequest(`/write-offs/${writeOffId}`, {
      method: 'PUT',
      body: JSON.stringify(submitData)
    })
    
    if (response.ok && response.data.success) {
      successMessage.value = 'Списание успешно обновлено!'
      toastr.success('Списание успешно обновлено')
      setTimeout(() => {
        router.push('/products/write-offs')
      }, 1000)
    } else {
      serverError.value = response.data.message || 'Произошла ошибка при обновлении списания'
    }
  } catch (error) {
    console.error('Ошибка при обновлении списания:', error)
    serverError.value = 'Произошла ошибка при обновлении списания'
  } finally {
    saving.value = false
  }
}

onMounted(async () => {
  await Promise.all([loadWriteOff(), loadUserData(), loadProducts(), loadWarehouses()])
})
</script> 