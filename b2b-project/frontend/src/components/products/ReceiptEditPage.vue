<template>
  <div class="min-h-screen bg-gray-50" style="padding-top: 66px;">
    <ProductsMenu />
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="flex items-center justify-between mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Редактирование оприходования</h1>
        <button @click="goBack" class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold px-6 py-2 rounded-lg border shadow transition text-sm">
          Назад
        </button>
      </div>
      
      <!-- Форма -->
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
              <input v-else v-model="form.number" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm bg-white" required>
            </div>
            <div>
              <label class="block text-sm text-gray-700 mb-1">Дата *</label>
              <div v-if="loading" class="flex items-center justify-center h-10 bg-gray-100 rounded-lg">
                <Loader2 class="animate-spin h-4 w-4 text-blue-600 mr-2" />
                <span class="text-sm text-gray-500">Загрузка...</span>
              </div>
              <input v-else v-model="form.date" type="datetime-local" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm bg-white" required>
            </div>
          </div>

          <div class="flex gap-4 mt-4">
            <div class="flex-1">
              <label class="block text-sm text-gray-700 mb-1">Организация *</label>
              <div v-if="loading" class="flex items-center justify-center h-10 bg-gray-100 rounded-lg">
                <Loader2 class="animate-spin h-4 w-4 text-blue-600 mr-2" />
                <span class="text-sm text-gray-500">Загрузка...</span>
              </div>
              <input v-else v-model="form.organization" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm bg-white" required>
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
                :disabled="loadingWarehouses || loading"
              />
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
                :disabled="loading"
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
              <input v-else v-model="form.project" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm bg-white">
            </div>
            <div class="flex-1">
              <label class="block text-sm text-gray-700 mb-1">Накладные расходы</label>
              <div v-if="loading" class="flex items-center justify-center h-10 bg-gray-100 rounded-lg">
                <Loader2 class="animate-spin h-4 w-4 text-blue-600 mr-2" />
                <span class="text-sm text-gray-500">Загрузка...</span>
              </div>
              <input v-else v-model="form.overhead_costs" type="number" step="0.01" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm bg-white" placeholder="0.00">
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
              <div class="text-center">
                <Loader2 class="animate-spin h-8 w-8 text-blue-600 mx-auto mb-2" />
                <p class="text-sm text-blue-700">Загрузка файлов...</p>
              </div>
            </div>
            <input ref="fileInput" type="file" multiple @change="handleFileUpload" class="hidden" :disabled="uploading || loading" />
            <button type="button" @click="$refs.fileInput.click()" class="bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white font-semibold px-4 py-2 rounded-lg transition text-sm" :disabled="uploading || loading">
              <span v-if="uploading">Загрузка...</span>
              <span v-else-if="loading">Загрузка данных...</span>
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
          <div v-else-if="files.length > 0" class="mt-4">
            <h4 class="text-sm font-medium text-gray-700 mb-2">Загруженные файлы:</h4>
            <div class="space-y-2">
              <div v-for="(file, index) in files" :key="`file-${index}-${file.id}`" class="flex items-center justify-between bg-gray-50 p-3 rounded-lg">
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
                <button type="button" @click="deleteFile(file.id)" class="text-red-500 hover:text-red-700" :disabled="file.uploading || file.deleting">
                  <Loader2 v-if="file.deleting" class="animate-spin h-4 w-4 text-red-500" />
                  <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                :disabled="loadingProducts || loading"
                @search-change="onProductSearch"
              />
            </div>
            <button type="button" @click="addProduct" :disabled="!selectedProduct || loading" class="bg-green-600 hover:bg-green-700 disabled:bg-gray-400 text-white font-semibold px-4 py-2 rounded-lg transition text-sm">
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
                <th class="px-3 py-2 text-center font-semibold text-gray-700">Действия</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(position, index) in positions" :key="`position-${index}-${position.product_id}`" class="hover:bg-gray-50">
                <td class="px-3 py-2 text-left">{{ position.name }}</td>
                <td class="px-3 py-2 text-center">
                  <input :value="position.quantity" @input="updatePositionQuantity(index, $event.target.value)" type="number" step="0.001" class="w-20 border border-gray-300 rounded px-2 py-1 text-sm text-center bg-white" :disabled="loading" />
                </td>
                <td class="px-3 py-2 text-center">
                  <input :value="position.price" @input="updatePositionPrice(index, $event.target.value)" type="number" step="0.01" class="w-24 border border-gray-300 rounded px-2 py-1 text-sm text-center bg-white" :disabled="loading" />
                </td>
                <td class="px-3 py-2 text-center">{{ calculatePositionTotal(position) }}</td>
                <td class="px-3 py-2 text-center">
                  <button type="button" @click="removePosition(index)" class="text-red-500 hover:text-red-700" :disabled="loading || position.deleting">
                    <Loader2 v-if="position.deleting" class="animate-spin h-4 w-4 text-red-500" />
                    <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Итого -->
        <div v-if="positions.length > 0" class="bg-white rounded-xl shadow p-6">
          <div class="text-right">
            <div class="text-lg font-semibold text-gray-900">
              Итого: {{ total.toFixed(2) }} ₽
            </div>
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
          <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded-lg shadow transition text-sm" :disabled="saving || uploading || loading">
            <span v-if="saving">Сохранение...</span>
            <span v-else-if="loading">Загрузка...</span>
            <span v-else>Сохранить</span>
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
import { Loader2 } from 'lucide-vue-next'

// Устанавливаем заголовок страницы
document.title = 'B2B SKLAD - Оприходования'

const router = useRouter()
const route = useRoute()
function goBack() {
  router.push('/products/receipts')
}

const loading = ref(true)
const saving = ref(false)
const uploading = ref(false)
const loadingFiles = ref(false)
const loadingPositions = ref(false)
const serverError = ref('')
const successMessage = ref('')

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

const products = ref([])
const loadingProducts = ref(true)
const selectedProduct = ref(null)
const positions = ref([])
const files = ref([])
const fileInput = ref(null)
const productSearch = ref('')

// Переменные для складов
const warehouses = ref([])
const loadingWarehouses = ref(true)

// Опции статуса
const statusOptions = [
  { label: 'Черновик', value: 'draft' },
  { label: 'Проведено', value: 'posted' }
]

const warehouseOptions = computed(() => {
  if (!Array.isArray(warehouses.value)) return []
  return warehouses.value.map(w => ({
    label: w.name,
    value: w.id
  }))
})

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
  
  // Устанавливаем флаг удаления для конкретного товара
  position.deleting = true

  try {
    // Имитируем задержку для UX
    await new Promise(resolve => setTimeout(resolve, 300))
    
    // Удаляем товар из списка
    positions.value.splice(index, 1)
  } catch (error) {
    console.error('Ошибка при удалении товара:', error)
  } finally {
    // Убираем флаг удаления (хотя товар уже удален)
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

async function fetchReceipt() {
  const id = route.params.id
  const res = await apiRequest(`/receipts/${id}`, { method: 'GET' })
  if (res.ok && res.data && res.data.data) {
    const receipt = res.data.data
    
    // Заполняем форму данными
    form.value = {
      number: receipt.number || '',
      date: receipt.date ? receipt.date.replace('T', ' ').substring(0, 16) : '',
      organization: receipt.organization || '',
      warehouse: receipt.warehouse || '',
      project: receipt.project || '',
      status: receipt.status || 'draft',
      comment: receipt.comment || '',
      overhead_costs: receipt.overhead_costs || 0,
      positions: receipt.positions ? receipt.positions.map(pos => ({
        product_id: pos.id,
        name: pos.name || '',
        code: pos.code || '',
        article: pos.article || '',
        quantity: pos.quantity || 0,
        price: pos.price || 0,
        balance: pos.balance || 0,
        reason: pos.reason || '',
        gtd: pos.gtd || '',
        rnpt: pos.rnpt || '',
        country: pos.country || ''
      })) : []
    }
    
    positions.value = form.value.positions
  } else {
    router.push('/products/receipts')
  }
}

async function fetchFiles() {
  try {
    loadingFiles.value = true
    const id = route.params.id
    const res = await apiRequest(`/receipt-files/${id}`, { method: 'GET' })
    if (res.ok && res.data && res.data.data) {
      files.value = res.data.data
    }
  } catch (error) {
    console.error('Ошибка загрузки файлов:', error)
    files.value = []
  } finally {
    loadingFiles.value = false
  }
}

async function handleFileUpload(event) {
  const file = event.target.files[0]
  if (!file) return

  uploading.value = true
  
  // Добавляем файл в список с флагом загрузки
  const fileId = Date.now() + Math.random()
  files.value.push({
    id: fileId,
    filename: file.name,
    size_mb: (file.size / 1048576).toFixed(2),
    uploading: true
  })

  try {
    const formData = new FormData()
    formData.append('file', file)
    formData.append('receipt_id', route.params.id)

    const res = await apiRequest('/receipt-files', {
      method: 'POST',
      body: formData,
      headers: {}
    })

    if (res.ok) {
      await fetchFiles()
      event.target.value = '' // Очищаем input
    } else {
      console.error('Ошибка при загрузке файла:', res.data)
      // Удаляем файл из списка при ошибке
      const fileIndex = files.value.findIndex(f => f.id === fileId)
      if (fileIndex !== -1) {
        files.value.splice(fileIndex, 1)
      }
    }
  } catch (error) {
    console.error('Ошибка при загрузке файла:', error)
    // Удаляем файл из списка при ошибке
    const fileIndex = files.value.findIndex(f => f.id === fileId)
    if (fileIndex !== -1) {
      files.value.splice(fileIndex, 1)
    }
  } finally {
    uploading.value = false
  }
}

async function deleteFile(fileId) {
  const file = files.value.find(f => f.id === fileId)
  
  // Не удаляем файл, если он в процессе загрузки
  if (file && file.uploading) {
    return
  }

  // Устанавливаем флаг удаления для конкретного файла
  file.deleting = true

  try {
    const res = await apiRequest(`/receipt-files/${fileId}`, {
      method: 'DELETE'
    })

    if (res.ok) {
      // Удаляем файл из списка без перезагрузки всего списка
      const fileIndex = files.value.findIndex(f => f.id === fileId)
      if (fileIndex !== -1) {
        files.value.splice(fileIndex, 1)
      }
    } else {
      console.error('Ошибка при удалении файла:', res.data)
    }
  } catch (error) {
    console.error('Ошибка при удалении файла:', error)
  } finally {
    // Убираем флаг удаления
    if (file) {
      file.deleting = false
    }
  }
}

async function handleSubmit() {
  saving.value = true
  serverError.value = ''
  successMessage.value = ''
  
  try {
    const id = route.params.id
    const res = await apiRequest(`/receipts/${id}`, {
      method: 'PUT',
      body: JSON.stringify({
        ...form.value,
        positions: positions.value,
        total: total.value
      })
    })

    if (res.ok && res.data.success) {
      successMessage.value = 'Оприходование успешно обновлено!'
      setTimeout(() => {
        router.push('/products/receipts')
      }, 1000)
    } else {
      serverError.value = res.data.message || 'Произошла ошибка при обновлении оприходования.'
    }
  } catch (error) {
    console.error('Ошибка при обновлении оприходования:', error)
    serverError.value = 'Произошла ошибка при обновлении оприходования.'
  } finally {
    saving.value = false
  }
}

onMounted(async () => {
  try {
    loadingPositions.value = true
    await Promise.all([loadProducts(), loadWarehouses(), fetchReceipt(), fetchFiles()])
  } catch (error) {
    console.error('Ошибка при загрузке данных:', error)
  } finally {
    loading.value = false
    loadingPositions.value = false
  }
})
</script> 