<template>
  <div class="min-h-screen bg-gray-50" style="padding-top: 66px;">
    <ProductsMenu />
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="flex items-center justify-between mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Новое списание</h1>
        <button @click="goBack" class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold px-6 py-2 rounded-lg border shadow transition text-sm">
          Назад
        </button>
      </div>
      
      <form @submit.prevent="handleSubmit" class="space-y-6">
        <!-- Основные поля -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm text-gray-700 mb-1">Номер *</label>
            <input v-model="form.number" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm bg-white" :class="{'border-red-400': errors.number}" />
            <div v-if="errors.number" class="text-sm text-red-500 mt-1">{{ errors.number }}</div>
          </div>
          <div>
            <label class="block text-sm text-gray-700 mb-1">Дата *</label>
            <input v-model="form.date" type="datetime-local" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm bg-white" :class="{'border-red-400': errors.date}" />
            <div v-if="errors.date" class="text-sm text-red-500 mt-1">{{ errors.date }}</div>
          </div>
        </div>

        <div class="flex gap-4">
          <div class="flex-1 hidden">
            <label class="block text-sm text-gray-700 mb-1">Организация</label>
            <div v-if="loadingUserData" class="w-full h-10 bg-gray-100 rounded-lg flex items-center justify-center">
              <Loader2 class="animate-spin h-4 w-4 text-gray-400 mr-2" />
              <span class="text-xs text-gray-500">Загрузка данных пользователя...</span>
            </div>
            <input v-else v-model="form.organization" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm bg-white" :class="{'border-red-400': errors.organization}" />
            <div v-if="errors.organization" class="text-sm text-red-500 mt-1">{{ errors.organization }}</div>
          </div>
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

        <div class="flex flex-col md:flex-row gap-4 mt-4">
          <div class="flex-1">
            <label class="block text-sm text-gray-700 mb-1">Проект</label>
            <input v-model="form.project" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm bg-white" />
          </div>
          <div class="flex-1">
            <label class="block text-sm text-gray-700 mb-1">Накладные расходы</label>
            <input v-model="form.overhead_costs" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm bg-white" placeholder="0.00" />
          </div>
        </div>

        <div>
          <label class="block text-sm text-gray-700 mb-1">Комментарий</label>
          <textarea v-model="form.comment" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm bg-white"></textarea>
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

        <!-- Блок товаров для списания - всегда видимый -->
        <div class="mt-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Товары для списания</h3>
          
          <!-- Состояние: склад не выбран -->
          <div v-if="!form.warehouse" class="bg-gray-50 border border-gray-200 rounded-lg p-8 text-center">
            <div class="text-gray-500 text-sm">Для получения списка товаров выберите склад</div>
          </div>
          
          <!-- Состояние: загрузка товаров -->
          <div v-else-if="loadingWarehouseProducts" class="bg-gray-50 border border-gray-200 rounded-lg p-8 text-center">
            <Loader2 class="animate-spin h-8 w-8 text-blue-600 mx-auto mb-4" />
            <div class="text-gray-500 text-sm">Загрузка товаров склада...</div>
          </div>
          
          <!-- Состояние: товары загружены -->
          <div v-else-if="warehouseProducts.length > 0" class="bg-white border border-gray-200 rounded-lg">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
              <thead>
                <tr class="bg-gray-50">
                  <th class="px-3 py-2 text-left font-semibold text-gray-700">Товар</th>
                  <th class="px-3 py-2 text-center font-semibold text-gray-700">Артикул</th>
                  <th class="px-3 py-2 text-center font-semibold text-gray-700">Остаток на складе</th>
                  <th class="px-3 py-2 text-center font-semibold text-gray-700">Количество к списанию</th>
                  <th class="px-3 py-2 text-center font-semibold text-gray-700">Цена</th>
                  <th class="px-3 py-2 text-center font-semibold text-gray-700">Причина</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(product, index) in warehouseProducts" :key="product.id" class="hover:bg-gray-50">
                  <td class="px-3 py-2">
                    <div class="font-medium">{{ product.name }}</div>
                  </td>
                  <td class="px-3 py-2 text-center">
                    <span class="text-sm">{{ product.article || product.code || 'N/A' }}</span>
                  </td>
                  <td class="px-3 py-2 text-center">
                    <span class="font-medium text-blue-600">{{ product.warehouse_quantity }}</span>
                  </td>
                  <td class="px-3 py-2 text-center">
                    <input
                      v-model.number="product.writeoff_quantity"
                      type="number"
                      :max="product.warehouse_quantity"
                      min="0"
                      class="w-20 text-center border border-gray-300 rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
                      @input="validateWriteoff(product)"
                    />
                  </td>
                  <td class="px-3 py-2 text-center">
                    <input
                      v-model.number="product.price"
                      type="number"
                      min="0"
                      step="0.01"
                      class="w-24 text-center border border-gray-300 rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
                    />
                  </td>
                  <td class="px-3 py-2 text-center">
                    <input
                      v-model="product.reason"
                      type="text"
                      class="w-32 border border-gray-300 rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
                    />
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          
          <!-- Состояние: товары не найдены -->
          <div v-else class="bg-gray-50 border border-gray-200 rounded-lg p-8 text-center">
            <div class="text-gray-500 text-sm">На выбранном складе нет товаров для списания</div>
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
          <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded-lg shadow transition text-sm flex items-center gap-2" :disabled="saving || uploading || loadingUserData">
            <Loader2 v-if="saving" class="animate-spin h-4 w-4" />
            <span v-if="saving">Создание...</span>
            <span v-else-if="loadingUserData">Загрузка...</span>
            <span v-else>Создать списание</span>
          </button>
        </div>
      </form>
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
import { ref, computed, onMounted, watch } from 'vue'
import { apiRequest } from '@/config/api'
import { useWarehouseCheck } from '@/composables/useWarehouseCheck'
import Multiselect from '@vueform/multiselect'
import '@vueform/multiselect/themes/default.css'
import toastr from 'toastr'
import { Loader2, X } from 'lucide-vue-next'

// Устанавливаем заголовок страницы
document.title = 'B2B SKLAD - Списания'

const router = useRouter()
function goBack() {
  router.push('/products/write-offs')
}

const form = ref({
  number: '',
  date: new Date().toISOString().slice(0, 16), // Текущая дата и время
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

// Восстанавливаю недостающие переменные
const loadingUserData = ref(true)
const userData = ref(null)
const uploadedFiles = ref([])
const fileInput = ref(null)

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

// Товары склада для списания
const warehouseProducts = ref([])
const loadingWarehouseProducts = ref(false)
const selectedWarehouseName = ref('')

const statusOptions = [
  { label: 'Черновик', value: 'draft' },
  { label: 'Проведено', value: 'posted' }
]



const total = computed(() => {
  let sum = 0
  for (const pos of warehouseProducts.value) {
    const quantity = parseFloat(pos.writeoff_quantity || 0)
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
    console.error('Ошибка загрузки данных пользователя:', error)
  } finally {
    loadingUserData.value = false
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
      // Обновляем список складов
      await loadWarehouses()
      
      // Устанавливаем новый склад как выбранный
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

// Восстанавливаю функции для работы с файлами
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
    
    // При создании списания файлы еще не сохранены в БД, 
    // поэтому просто удаляем из списка загруженных файлов
    uploadedFiles.value.splice(index, 1)
  }
}

async function loadWarehouseProducts() {
  console.log('loadWarehouseProducts вызвана, warehouse_id:', form.value.warehouse)
  try {
    loadingWarehouseProducts.value = true
    const response = await apiRequest('/transfers/available-products', {
      method: 'POST',
      body: JSON.stringify({ warehouse_id: form.value.warehouse })
    })
    console.log('Ответ от /transfers/available-products:', response)
    if (response.ok) {
      warehouseProducts.value = response.data.map(product => ({
        id: product.id,
        name: product.name,
        article: product.article,
        code: product.article,
        unit: product.unit,
        supplier: product.description || '',
        country: '',
        warehouse_quantity: product.warehouse_quantity,
        writeoff_quantity: 0, // поле для ввода пользователем
        price: 0,
        reason: ''
      }))
      // Получаем название склада
      const warehouseResponse = await apiRequest(`/warehouses/${form.value.warehouse}`, { method: 'GET' })
      if (warehouseResponse.ok && warehouseResponse.data.success) {
        selectedWarehouseName.value = warehouseResponse.data.data.name
      }
      if (warehouseProducts.value.length > 0) {
        toastr.success(`Загружено ${warehouseProducts.value.length} товаров`)
      } else {
        toastr.info('На выбранном складе нет товаров с остатками')
      }
    } else {
      toastr.error('Ошибка при загрузке товаров склада')
    }
  } catch (error) {
    console.error('Ошибка в loadWarehouseProducts:', error)
    toastr.error('Ошибка при загрузке товаров склада')
  } finally {
    loadingWarehouseProducts.value = false
  }
}

function validateWriteoff(product) {
  if (product.writeoff_quantity > product.warehouse_quantity) {
    product.writeoff_quantity = product.warehouse_quantity
    toastr.warning('Нельзя списать больше, чем есть на складе')
  }
  if (product.writeoff_quantity < 0) {
    product.writeoff_quantity = 0
  }
}

async function handleSubmit() {
  errors.value = {}
  saving.value = true
  try {
    const positions = warehouseProducts.value
      .filter(p => p.writeoff_quantity > 0)
      .map(p => ({
        product_id: p.id,
        name: p.name,
        code: p.code,
        article: p.article,
        quantity: p.writeoff_quantity,
        price: p.price,
        reason: p.reason
      }))
    const submitData = {
      ...form.value,
      positions,
      write_off_files: uploadedFiles.value.map(f => ({
        filename: f.filename,
        file_url: f.file_url,
        file_size: f.size_mb,
        uploaded_by: f.employee
      }))
    }
    const response = await apiRequest('/write-offs', {
      method: 'POST',
      body: JSON.stringify(submitData)
    })
    if (response.ok && response.data.success) {
      toastr.success('Списание создано')
      router.push('/products/write-offs')
    } else {
      if (response.data.errors) {
        errors.value = response.data.errors
      } else {
        toastr.error(response.data?.message || 'Ошибка при создании списания')
      }
    }
  } catch (error) {
    toastr.error('Ошибка при создании списания')
  } finally {
    saving.value = false
  }
}

onMounted(async () => {
  await loadUserData()
  // Проверяем наличие складов и показываем модальное окно если их нет
  await checkWarehousesAndShowModal()
})

// Автоматическая загрузка товаров при выборе склада
watch(() => form.value.warehouse, async (newWarehouseId) => {
  console.log('Watch сработал: form.value.warehouse изменился', { newWarehouseId })
  if (newWarehouseId) {
    console.log('Вызываем loadWarehouseProducts для warehouse_id:', newWarehouseId)
    await loadWarehouseProducts()
  } else {
    console.log('Очищаем warehouseProducts')
    warehouseProducts.value = []
    selectedWarehouseName.value = ''
  }
})
</script> 