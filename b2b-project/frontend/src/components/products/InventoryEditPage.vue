<template>
  <div class="min-h-screen bg-gray-50" style="padding-top: 66px;">
    <ProductsMenu />
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="flex items-center justify-between mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Редактирование инвентаризации</h1>
        <button @click="goBack" class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold px-6 py-2 rounded-lg border shadow transition text-sm">
          Назад
        </button>
      </div>
      
      <div v-if="loading" class="flex items-center justify-center py-8">
        <Loader2 class="animate-spin h-6 w-6 text-blue-600 mr-2" />
        <span class="text-sm text-gray-600">Загрузка данных...</span>
      </div>
      
      <form v-else @submit.prevent="handleSubmit" class="space-y-6">
        <!-- Основные поля -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm text-gray-700 mb-1">Название *</label>
            <input v-model="form.name" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm bg-white" :class="{'border-red-400': errors.name}" />
            <div v-if="errors.name" class="text-sm text-red-500 mt-1">{{ errors.name }}</div>
          </div>
          <div>
            <label class="block text-sm text-gray-700 mb-1">Дата *</label>
            <input v-model="form.date" type="datetime-local" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm bg-white" :class="{'border-red-400': errors.date}" />
            <div v-if="errors.date" class="text-sm text-red-500 mt-1">{{ errors.date }}</div>
          </div>
        </div>

        <div class="flex gap-4">
          <div class="flex-1">
            <label class="block text-sm text-gray-700 mb-1">Склад *</label>
            <Multiselect
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
          <div class="flex-1">
            <label class="block text-sm text-gray-700 mb-1">Статус</label>
            <Multiselect
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

        <div>
          <label class="block text-sm text-gray-700 mb-1">Описание</label>
          <textarea v-model="form.description" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm bg-white"></textarea>
        </div>

        <!-- Загрузка файлов -->
        <div>
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
          <div v-if="uploadedFiles.length > 0" class="mt-4">
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
        <div>
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
        <div v-if="positions.length > 0" class="mt-6">
          <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead>
              <tr class="bg-gray-50">
                <th class="px-3 py-2 text-left font-semibold text-gray-700">Товар</th>
                <th class="px-3 py-2 text-center font-semibold text-gray-700">Расчетный остаток</th>
                <th class="px-3 py-2 text-center font-semibold text-gray-700">Фактический остаток</th>
                <th class="px-3 py-2 text-center font-semibold text-gray-700">Разница</th>
                <th class="px-3 py-2 text-center font-semibold text-gray-700">Статус</th>
                <th class="px-3 py-2 text-center font-semibold text-gray-700">Действия</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(position, index) in positions" :key="index" class="hover:bg-gray-50">
                <td class="px-3 py-2">
                  <div>
                    <div class="font-medium">{{ position.product_name }}</div>
                    <div class="text-xs text-gray-500">{{ position.product_sku }}</div>
                  </div>
                </td>
                <td class="px-3 py-2 text-center">
                  <input 
                    v-model.number="position.calculated_quantity" 
                    type="number" 
                    step="0.001"
                    class="w-20 text-center border border-gray-300 rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
                  />
                </td>
                <td class="px-3 py-2 text-center">
                  <input 
                    v-model.number="position.actual_quantity" 
                    type="number" 
                    step="0.001"
                    class="w-20 text-center border border-gray-300 rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
                  />
                </td>
                <td class="px-3 py-2 text-center">
                  <span :class="getDifferenceClass(position)">
                    {{ calculateDifference(position) }}
                  </span>
                </td>
                <td class="px-3 py-2 text-center">
                  <span :class="getExcessShortageClass(position)">
                    {{ getExcessShortageText(position) }}
                  </span>
                </td>
                <td class="px-3 py-2 text-center">
                  <button @click="removePosition(index)" class="text-red-600 hover:text-red-800 p-1 rounded hover:bg-red-50 transition-colors">
                    <Trash2 class="w-4 h-4" />
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Кнопки действий -->
        <div class="flex justify-end gap-4 pt-6 border-t border-gray-200">
          <button type="button" @click="goBack" class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold px-6 py-2 rounded-lg border shadow transition text-sm">
            Отмена
          </button>
          <button type="submit" :disabled="saving" class="bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white font-semibold px-6 py-2 rounded-lg shadow transition text-sm flex items-center gap-2">
            <Loader2 v-if="saving" class="animate-spin h-4 w-4" />
            <span v-if="saving">Сохранение...</span>
            <span v-else>Сохранить</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import ProductsMenu from './ProductsMenu.vue'
import { apiRequest } from '@/config/api'
import { useRouter, useRoute } from 'vue-router'
import { Trash2, Loader2 } from 'lucide-vue-next'
import toastr from 'toastr'
import Multiselect from '@vueform/multiselect'
import '@vueform/multiselect/themes/default.css'

// Устанавливаем заголовок страницы
document.title = 'B2B SKLAD - Редактирование инвентаризации'

const router = useRouter()
const route = useRoute()

// Загрузка данных
const loading = ref(false)
const inventoryId = route.params.id

// Форма
const form = ref({
  name: '',
  description: '',
  warehouse: null,
  status: 'draft',
  date: new Date().toISOString().slice(0, 16)
})

const errors = ref({})
const saving = ref(false)

// Склады
const warehouses = ref([])
const loadingWarehouses = ref(false)
const showWarehouseForm = ref(false)
const warehouseForm = ref({
  name: '',
  address: ''
})
const warehouseErrors = ref({})
const warehouseSaving = ref(false)
const warehouseServerError = ref('')

// Товары
const products = ref([])
const loadingProducts = ref(false)
const selectedProduct = ref(null)
const positions = ref([])

// Файлы
const uploadedFiles = ref([])
const uploading = ref(false)

// Опции для фильтров
const warehouseOptions = computed(() => {
  return warehouses.value.map(w => ({
    label: w.name,
    value: w.id
  }))
})

const productOptions = computed(() => {
  if (!Array.isArray(products.value)) {
    return []
  }
  return products.value.map(p => ({
    label: `${p.name} (${p.code || p.article || 'N/A'})`,
    value: p.id,
    product: p
  }))
})

const statusOptions = [
  { label: 'Черновик', value: 'draft' },
  { label: 'В процессе', value: 'in_progress' },
  { label: 'Завершена', value: 'completed' },
  { label: 'Отменена', value: 'cancelled' }
]

function goBack() {
  router.push('/products/inventory')
}

function handleWarehouseClick() {
  if (warehouses.value.length === 0) {
    showWarehouseForm.value = true
  }
}

async function createWarehouse() {
  warehouseSaving.value = true
  warehouseServerError.value = ''
  warehouseErrors.value = {}

  try {
    const response = await apiRequest('/warehouses', {
      method: 'POST',
      body: JSON.stringify(warehouseForm.value)
    })

    if (response.ok && response.data.success) {
      warehouses.value.push(response.data.data)
      form.value.warehouse = response.data.data.id
      showWarehouseForm.value = false
      warehouseForm.value = { name: '', address: '' }
      toastr.success('Склад создан')
    } else {
      warehouseServerError.value = response.data?.message || 'Ошибка при создании склада'
    }
  } catch (error) {
    warehouseServerError.value = 'Ошибка при создании склада'
  } finally {
    warehouseSaving.value = false
  }
}

function closeWarehouseForm() {
  showWarehouseForm.value = false
  warehouseForm.value = { name: '', address: '' }
  warehouseErrors.value = {}
  warehouseServerError.value = ''
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

async function loadProducts(search = '') {
  try {
    loadingProducts.value = true
    const params = search ? `?search=${encodeURIComponent(search)}` : ''
    const response = await apiRequest(`/products${params}`, { method: 'GET' })
    if (response.ok && response.data.success) {
      // Handle paginated response - products are in data.data
      products.value = response.data.data?.data || []
    } else {
      products.value = []
    }
  } catch (error) {
    console.error('Ошибка загрузки товаров:', error)
    products.value = []
  } finally {
    loadingProducts.value = false
  }
}

async function loadInventory() {
  loading.value = true
  try {
    const response = await apiRequest(`/inventories/${inventoryId}`, { method: 'GET' })
    if (response.ok && response.data.success) {
      const inventory = response.data.data
      
      form.value = {
        name: inventory.name || '',
        description: inventory.description || '',
        warehouse: inventory.warehouse_id || null,
        status: inventory.status || 'draft',
        date: inventory.created_at ? new Date(inventory.created_at).toISOString().slice(0, 16) : new Date().toISOString().slice(0, 16)
      }
      
      // Загружаем позиции
      if (inventory.items && Array.isArray(inventory.items)) {
        positions.value = inventory.items.map(item => ({
          id: item.id,
          product_id: item.product_id,
          product_name: item.product_name || '',
          product_sku: item.product_sku || '',
          calculated_quantity: item.calculated_quantity || 0,
          actual_quantity: item.actual_quantity || 0,
          notes: item.notes || ''
        }))
      }
      
      // Загружаем файлы
      if (inventory.files && Array.isArray(inventory.files)) {
        uploadedFiles.value = inventory.files.map(file => ({
          id: file.id,
          filename: file.original_filename || file.filename,
          file_url: file.file_path,
          size_mb: file.file_size ? (file.file_size / 1024 / 1024).toFixed(2) : '0',
          employee: file.uploaded_by || 'Система'
        }))
      }
    } else {
      toastr.error('Ошибка загрузки инвентаризации')
      router.push('/products/inventory')
    }
  } catch (error) {
    console.error('Ошибка загрузки инвентаризации:', error)
    toastr.error('Ошибка загрузки инвентаризации')
    router.push('/products/inventory')
  } finally {
    loading.value = false
  }
}

function onProductSearch(search) {
  if (search && search.length > 2) {
    loadProducts(search)
  }
}

function addProduct() {
  if (!selectedProduct.value) return

  const product = selectedProduct.value.product
  const existingIndex = positions.value.findIndex(p => p.product_id === product.id)
  
  if (existingIndex !== -1) {
    toastr.warning('Товар уже добавлен')
    return
  }

  positions.value.push({
    product_id: product.id,
    product_name: product.name,
    product_sku: product.code || product.article || 'N/A',
    calculated_quantity: 0,
    actual_quantity: 0,
    notes: ''
  })

  selectedProduct.value = null
}

function removePosition(index) {
  positions.value.splice(index, 1)
}

function calculateDifference(position) {
  const diff = (position.actual_quantity || 0) - (position.calculated_quantity || 0)
  // Если разница целое число, показываем без десятичных знаков
  return Number.isInteger(diff) ? diff.toString() : diff.toFixed(3)
}

function getDifferenceClass(position) {
  const diff = (position.actual_quantity || 0) - (position.calculated_quantity || 0)
  if (diff > 0) return 'text-green-600 font-medium'
  if (diff < 0) return 'text-red-600 font-medium'
  return 'text-gray-600'
}

function getExcessShortageText(position) {
  const diff = (position.actual_quantity || 0) - (position.calculated_quantity || 0)
  if (diff > 0) return 'Избыток'
  if (diff < 0) return 'Недостача'
  return 'Норма'
}

function getExcessShortageClass(position) {
  const diff = (position.actual_quantity || 0) - (position.calculated_quantity || 0)
  if (diff > 0) return 'text-green-600'
  if (diff < 0) return 'text-red-600'
  return 'text-gray-600'
}

async function handleFileUpload(event) {
  const files = Array.from(event.target.files)
  if (files.length === 0) return

  uploading.value = true

  for (const file of files) {
    const formData = new FormData()
    formData.append('file', file)
    formData.append('inventory_id', inventoryId)

    try {
      const response = await apiRequest('/inventory-files/upload', {
        method: 'POST',
        body: formData,
        headers: {}
      })

      if (response.ok && response.data.success) {
        uploadedFiles.value.push({
          id: response.data.data.id,
          filename: response.data.data.filename,
          file_url: response.data.data.file_url,
          size_mb: (file.size / 1024 / 1024).toFixed(2),
          employee: response.data.data.uploaded_by || 'Система'
        })
      } else {
        toastr.error(`Ошибка загрузки файла ${file.name}`)
      }
    } catch (error) {
      toastr.error(`Ошибка загрузки файла ${file.name}`)
    }
  }

  uploading.value = false
  event.target.value = ''
}

function removeFile(fileId) {
  uploadedFiles.value = uploadedFiles.value.filter(f => f.id !== fileId)
}

async function handleSubmit() {
  errors.value = {}
  saving.value = true

  try {
    const submitData = {
      ...form.value,
      positions: positions.value,
      files: uploadedFiles.value.map(f => f.id)
    }

    const response = await apiRequest(`/inventories/${inventoryId}`, {
      method: 'PUT',
      body: JSON.stringify(submitData)
    })

    if (response.ok && response.data.success) {
      toastr.success('Инвентаризация обновлена')
      router.push('/products/inventory')
    } else {
      if (response.data.errors) {
        errors.value = response.data.errors
      } else {
        toastr.error(response.data?.message || 'Ошибка при обновлении инвентаризации')
      }
    }
  } catch (error) {
    toastr.error('Ошибка при обновлении инвентаризации')
  } finally {
    saving.value = false
  }
}

onMounted(async () => {
  await Promise.all([loadWarehouses(), loadProducts(), loadInventory()])
})
</script>

<style scoped>
.multiselect-custom {
  --ms-option-bg-selected: #3b82f6;
  --ms-option-color-selected: #ffffff;
  --ms-option-bg-selected-pointed: #2563eb;
  --ms-option-color-selected-pointed: #ffffff;
}
</style> 