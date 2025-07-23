<template>
  <div class="min-h-screen bg-gray-50" style="padding-top: 66px;">
    <ProductsMenu />
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="flex items-center justify-between mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Новая инвентаризация</h1>
        <button @click="goBack" class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold px-6 py-2 rounded-lg border shadow transition text-sm">
          Назад
        </button>
      </div>
      
      <form @submit.prevent="handleSubmit" class="space-y-6">
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

        <!-- Товары склада -->
        <div v-if="form.warehouse">
          <div class="flex items-center justify-between mb-4">
            <label class="block text-sm text-gray-700">Товары склада</label>
            <div v-if="loadingWarehouseProducts" class="flex items-center gap-2 text-sm text-blue-600">
              <Loader2 class="animate-spin h-4 w-4" />
              <span>Загрузка товаров...</span>
            </div>
          </div>
          
          <div v-if="warehouseProducts.length > 0" class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
            <div class="text-sm text-green-700">
              Загружено {{ warehouseProducts.length }} товаров со склада "{{ selectedWarehouseName }}"
            </div>
          </div>
        </div>

        <!-- Таблица товаров склада -->
        <div v-if="warehouseProducts.length > 0" class="mt-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Товары для инвентаризации</h3>
          <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead>
              <tr class="bg-gray-50">
                <th class="px-3 py-2 text-left font-semibold text-gray-700">Товар</th>
                <th class="px-3 py-2 text-center font-semibold text-gray-700">Артикул</th>
                <th class="px-3 py-2 text-center font-semibold text-gray-700">Расчетный остаток</th>
                <th class="px-3 py-2 text-center font-semibold text-gray-700">Фактический остаток</th>
                <th class="px-3 py-2 text-center font-semibold text-gray-700">Разница</th>
                <th class="px-3 py-2 text-center font-semibold text-gray-700">Статус</th>
                <th class="px-3 py-2 text-center font-semibold text-gray-700">Действия</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(product, index) in warehouseProducts" :key="product.id" class="hover:bg-gray-50">
                <td class="px-3 py-2">
                  <div class="flex items-center gap-2">
                    <div class="flex-1">
                      <div class="font-medium">{{ product.name }}</div>
                      <!-- <div class="text-xs text-gray-500">{{ product.supplier || 'N/A' }}</div> -->
                      <!-- Индикаторы фото и комментария -->
                      <div v-if="hasDiscrepancy(product) && (product.tempPhoto || product.tempNotes)" class="flex items-center gap-1 mt-1">
                        <span v-if="product.tempPhoto" class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs bg-blue-100 text-blue-700">
                          📷 Фото
                        </span>
                        <span v-if="product.tempNotes" class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs bg-green-100 text-green-700">
                          💬 Комментарий
                        </span>
                      </div>
                    </div>

                  </div>
                </td>
                <td class="px-3 py-2 text-center">
                  <span class="text-sm">{{ product.article || product.code || 'N/A' }}</span>
                </td>
                <td class="px-3 py-2 text-center">
                  <span class="font-medium text-blue-600">{{ product.calculated_balance }}</span>
                </td>
                <td class="px-3 py-2 text-center">
                  <input 
                    v-model="product.actual_quantity" 
                    type="text" 
                    @input="validateIntegerInput($event, product)"
                    @keypress="onlyNumbers"
                    class="w-20 text-center border border-gray-300 rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
                    placeholder="0"
                  />
                </td>
                <td class="px-3 py-2 text-center">
                  <span :class="getDifferenceClass(product)">
                    {{ calculateDifference(product) }}
                  </span>
                </td>
                <td class="px-3 py-2 text-center">
                  <span :class="getExcessShortageClass(product)">
                    {{ getExcessShortageText(product) }}
                  </span>
                </td>
                <td class="px-3 py-2 text-center">
                  <div class="flex justify-center gap-1">
                    <!-- Кнопка/изображение для фото -->
                    <div class="relative">
                      <!-- Loader во время загрузки -->
                      <button 
                        v-if="photoUploading[product.id]"
                        type="button"
                        disabled
                        class="p-1 rounded transition-colors text-blue-600 cursor-not-allowed"
                        title="Загрузка фото..."
                      >
                        <Loader2 class="w-4 h-4 animate-spin" />
                      </button>
                      
                      <!-- Превью изображения если есть фото -->
                      <button 
                        v-else-if="product.tempPhoto && hasDiscrepancy(product)"
                        type="button"
                        @click="handlePhotoReplace(product, index)"
                        class="p-0 rounded transition-colors hover:opacity-80 cursor-pointer border border-gray-200 hover:border-blue-400"
                        title="Изменить фото"
                      >
                        <img 
                          :src="product.tempPhoto" 
                          alt="Фото товара" 
                          class="w-6 h-6 rounded object-cover"
                        />
                      </button>
                      
                      <!-- Иконка Camera если нет фото -->
                      <button 
                        v-else
                        type="button"
                        @click="handlePhotoUpload(product, index)" 
                        :disabled="!hasDiscrepancy(product)"
                        :class="[
                          'p-1 rounded transition-colors',
                          hasDiscrepancy(product) 
                            ? 'text-blue-600 hover:text-blue-800 hover:bg-blue-50 cursor-pointer'
                            : 'text-gray-300 cursor-not-allowed'
                        ]"
                        :title="hasDiscrepancy(product) ? 'Прикрепить фото' : 'Доступно только при расхождениях'"
                      >
                        <Camera class="w-4 h-4" />
                      </button>
                    </div>
                    <button 
                      type="button"
                      @click="handleCommentEdit(product, index)" 
                      :disabled="!hasDiscrepancy(product) && !product.tempNotes"
                      :class="[
                        'p-1 rounded transition-colors',
                        (hasDiscrepancy(product) || product.tempNotes)
                          ? (product.tempNotes ? 'text-green-800 bg-green-100 hover:bg-green-200' : 'text-green-600 hover:text-green-800 hover:bg-green-50') + ' cursor-pointer'
                          : 'text-gray-300 cursor-not-allowed'
                      ]"
                      :title="(hasDiscrepancy(product) || product.tempNotes) ? (product.tempNotes ? 'Редактировать комментарий' : 'Добавить комментарий') : 'Доступно только при расхождениях'"
                    >
                      <MessageSquare class="w-4 h-4" />
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Toggle для автоматических операций -->
        <div v-if="hasDiscrepancies" class="flex items-center gap-2 mb-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
          <input 
            id="auto-operations" 
            type="checkbox" 
            v-model="autoCreateOperations"
            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
          />
          <label for="auto-operations" class="text-sm text-gray-700 cursor-pointer">
            Создать автоматически оприходование и списание по расхождениям
          </label>
          <div class="text-xs text-gray-500 ml-2">
            (При включении этой опции автоматически создадутся документы для корректировки остатков)
          </div>
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

    <!-- Модальное окно для случая отсутствия складов -->
    <NoWarehousesModal 
      :is-visible="showNoWarehousesModal"
      @close="closeNoWarehousesModal"
    />

    <!-- Модальное окно для комментариев -->
    <CommentModal
      :is-visible="showCommentModal"
      :product-name="currentProduct?.name || ''"
      :product-article="currentProduct?.article || currentProduct?.code || ''"
      :difference-text="currentProduct ? getCommentDifferenceText(currentProduct) : ''"
      :difference-class="currentProduct ? getDifferenceClass(currentProduct) : ''"
      :initial-comment="currentProduct?.tempNotes || ''"
      @close="handleCommentModalClose"
      @save="handleCommentSave"
      @delete="handleCommentDelete"
    />
  </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import ProductsMenu from './ProductsMenu.vue'
import NoWarehousesModal from '../NoWarehousesModal.vue'
import CommentModal from '../CommentModal.vue'
import { apiRequest } from '@/config/api'
import { useWarehouseCheck } from '@/composables/useWarehouseCheck'
import { useRouter } from 'vue-router'
import { Camera, MessageSquare, Loader2 } from 'lucide-vue-next'
import toastr from 'toastr'
import Multiselect from '@vueform/multiselect'
import '@vueform/multiselect/themes/default.css'

// Устанавливаем заголовок страницы
document.title = 'B2B SKLAD - Новая инвентаризация'

const router = useRouter()

// Форма
const form = ref({
  name: '',
  description: '',
  warehouse: null,
  status: 'draft',
  date: new Date().toISOString().slice(0, 16)
})

// Переменная для автоматического создания операций
const autoCreateOperations = ref(false)

const errors = ref({})
const saving = ref(false)

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
const warehouseSaving = ref(false)
const warehouseServerError = ref('')

// Товары (больше не нужны для инвентаризации)
const positions = ref([])

// Товары склада для инвентаризации
const warehouseProducts = ref([])
const loadingWarehouseProducts = ref(false)
const selectedWarehouseName = ref('')

// Файлы
const uploadedFiles = ref([])
const uploading = ref(false)

// Модальное окно комментариев
const showCommentModal = ref(false)
const currentProduct = ref(null)
const currentProductIndex = ref(-1)

// Состояние загрузки фото для каждого товара
const photoUploading = ref({})

// Опции для фильтров

// productOptions больше не нужны для инвентаризации

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



// Функции loadProducts и onProductSearch больше не нужны для инвентаризации

async function loadWarehouseProducts() {
  try {
    loadingWarehouseProducts.value = true
    
    const response = await apiRequest('/transfers/available-products', {
      method: 'POST',
      body: JSON.stringify({
        warehouse_id: form.value.warehouse
      })
    })

    if (response.ok) {
      // Преобразуем данные в формат для инвентаризации
      warehouseProducts.value = response.data.map(product => ({
        id: product.id,
        name: product.name,
        article: product.article,
        code: product.article, // Используем article как code
        unit: product.unit,
        supplier: product.description || '', // Используем description как supplier
        country: '',
        calculated_balance: product.warehouse_quantity, // Используем warehouse_quantity как calculated_balance
        actual_quantity: product.warehouse_quantity // Автоматически заполняем фактический остаток расчетным значением
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
    console.error('Ошибка загрузки товаров склада:', error)
    toastr.error('Ошибка при загрузке товаров склада')
  } finally {
    loadingWarehouseProducts.value = false
  }
}

// Функция проверки наличия расхождения у товара
function hasDiscrepancy(product) {
  const diff = (product.actual_quantity || 0) - (product.calculated_balance || 0)
  return diff !== 0
}



// Функция для редактирования комментария товара
function handleCommentEdit(product, index) {
  // Разрешаем редактирование если есть расхождения ИЛИ уже есть комментарий
  if (!hasDiscrepancy(product) && !product.tempNotes) return
  
  currentProduct.value = product
  currentProductIndex.value = index
  showCommentModal.value = true
}

// Функция для закрытия модального окна комментариев
function handleCommentModalClose() {
  showCommentModal.value = false
  currentProduct.value = null
  currentProductIndex.value = -1
}

// Функция для сохранения комментария
function handleCommentSave(comment) {
  if (currentProduct.value) {
    // Сохраняем комментарий во временном хранилище
    currentProduct.value.tempNotes = comment
    toastr.success('Комментарий сохранен')
  }
  handleCommentModalClose()
}

// Функция для удаления комментария
function handleCommentDelete() {
  if (currentProduct.value) {
    // Удаляем комментарий из временного хранилища
    currentProduct.value.tempNotes = ''
    toastr.success('Комментарий удален')
  }
  handleCommentModalClose()
}

// Функция для получения текста разницы в комментарии
function getCommentDifferenceText(product) {
  const diff = (product.actual_quantity || 0) - (product.calculated_balance || 0)
  if (diff > 0) return `Избыток: +${diff}`
  if (diff < 0) return `Недостача: ${diff}`
  return 'Без расхождений'
}

// Функция для просмотра полного фото
function viewFullPhoto(photoUrl) {
  window.open(photoUrl, '_blank')
}

// Функция загрузки фото (первый раз)
function handlePhotoUpload(product, index) {
  if (!hasDiscrepancy(product)) return
  selectAndUploadPhoto(product, index)
}

// Функция замены фото (когда кликаем на превью)
function handlePhotoReplace(product, index) {
  if (!hasDiscrepancy(product)) return
  
  const action = confirm('Выберите действие:\nOK - Заменить фото\nОтмена - Удалить фото')
  if (action) {
    // Заменить фото
    selectAndUploadPhoto(product, index)
  } else {
    // Удалить фото
    product.tempPhoto = null
    toastr.success('Фото удалено')
  }
}

// Функция выбора и загрузки фото
function selectAndUploadPhoto(product, index) {
  const input = document.createElement('input')
  input.type = 'file'
  input.accept = 'image/*'
  input.onchange = async (event) => {
    const file = event.target.files[0]
    if (file) {
      await uploadProductPhoto(product, file, index)
    }
  }
  input.click()
}

// Функция валидации ввода только цифр
function onlyNumbers(event) {
  const charCode = event.which ? event.which : event.keyCode
  // Разрешаем только цифры (48-57), Backspace (8), Delete (46), Tab (9), Enter (13), стрелки (37-40)
  if (charCode > 31 && (charCode < 48 || charCode > 57)) {
    event.preventDefault()
  }
}

// Функция валидации целого числа в инпуте
function validateIntegerInput(event, product) {
  let value = event.target.value
  
  // Убираем все нецифровые символы
  value = value.replace(/[^0-9]/g, '')
  
  // Обновляем значение в инпуте
  event.target.value = value
  
  // Обновляем модель с числовым значением
  product.actual_quantity = value === '' ? 0 : parseInt(value, 10)
}

// Функция загрузки фото товара
async function uploadProductPhoto(product, file, index) {
  try {
    // Устанавливаем состояние загрузки
    photoUploading.value[product.id] = true
    
    const formData = new FormData()
    formData.append('photo', file)
    
    const response = await apiRequest('/inventory-files/upload-item-photo', {
      method: 'POST',
      body: formData,
      headers: {}
    })
    
    if (response.ok && response.data.success) {
      // Сохраняем URL фото во временном хранилище товара
      product.tempPhoto = response.data.data.photo_url
      toastr.success('Фото загружено успешно')
    } else {
      toastr.error('Ошибка загрузки фото')
    }
  } catch (error) {
    console.error('Ошибка загрузки фото:', error)
    toastr.error('Ошибка загрузки фото')
  } finally {
    // Убираем состояние загрузки
    photoUploading.value[product.id] = false
  }
}

// Функция addProduct больше не нужна для инвентаризации

function removePosition(index) {
  positions.value.splice(index, 1)
}

function calculateDifference(product) {
  const diff = (product.actual_quantity || 0) - (product.calculated_balance || 0)
  // Если разница целое число, показываем без десятичных знаков
  return Number.isInteger(diff) ? diff.toString() : diff.toFixed(3)
}

function getDifferenceClass(product) {
  const diff = (product.actual_quantity || 0) - (product.calculated_balance || 0)
  if (diff > 0) return 'text-green-600 font-medium'
  if (diff < 0) return 'text-red-600 font-medium'
  return 'text-gray-600'
}

function getExcessShortageText(product) {
  const diff = (product.actual_quantity || 0) - (product.calculated_balance || 0)
  if (diff > 0) return 'Избыток'
  if (diff < 0) return 'Недостача'
  return 'Норма'
}

function getExcessShortageClass(product) {
  const diff = (product.actual_quantity || 0) - (product.calculated_balance || 0)
  if (diff > 0) return 'text-green-600'
  if (diff < 0) return 'text-red-600'
  return 'text-gray-600'
}

// Вычисляемое свойство для проверки наличия расхождений
const hasDiscrepancies = computed(() => {
  return warehouseProducts.value.some(product => {
    const diff = (product.actual_quantity || 0) - (product.calculated_balance || 0)
    return diff !== 0
  })
})

async function handleFileUpload(event) {
  const files = Array.from(event.target.files)
  if (files.length === 0) return

  uploading.value = true

  for (const file of files) {
    const formData = new FormData()
    formData.append('file', file)

    try {
      // Используем upload-draft, не требующий inventory_id
      const response = await apiRequest('/inventory-files/upload-draft', {
        method: 'POST',
        body: formData,
        headers: {}
      })

      if (response.ok && response.data) {
        uploadedFiles.value.push({
          id: Date.now() + Math.random(),
          filename: response.data.filename || file.name,
          file_url: response.data.file_url || '',
          file_size: response.data.file_size || file.size,
          size_mb: response.data.size_mb || (file.size / 1024 / 1024).toFixed(2),
          employee: response.data.uploaded_by || 'Система',
          uploading: false
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
    // Преобразуем товары склада в позиции для отправки
    const positions = warehouseProducts.value.map(product => ({
      product_id: product.id,
      calculated_quantity: product.calculated_balance,
      actual_quantity: product.actual_quantity || 0,
      notes: product.tempNotes || '',
      photo: product.tempPhoto || null
    }))

    const submitData = {
      ...form.value,
      auto_create_operations: autoCreateOperations.value,
      positions: positions,
      inventory_files: uploadedFiles.value.map(f => ({
        filename: f.filename,
        file_url: f.file_url,
        file_size: f.file_size,
        uploaded_by: f.employee
      }))
    }

    const response = await apiRequest('/inventories', {
      method: 'POST',
      body: JSON.stringify(submitData)
    })

    if (response.ok && response.data.success) {
      toastr.success('Инвентаризация создана')
      router.push('/products/inventory')
    } else {
      if (response.data.errors) {
        errors.value = response.data.errors
      } else {
        toastr.error(response.data?.message || 'Ошибка при создании инвентаризации')
      }
    }
  } catch (error) {
    toastr.error('Ошибка при создании инвентаризации')
  } finally {
    saving.value = false
  }
}

// Автоматическая загрузка товаров при выборе склада
watch(() => form.value.warehouse, async (newWarehouseId) => {
  if (newWarehouseId) {
    await loadWarehouseProducts()
  } else {
    warehouseProducts.value = []
    selectedWarehouseName.value = ''
  }
})

onMounted(async () => {
  // Проверяем наличие складов и показываем модальное окно если их нет
  await checkWarehousesAndShowModal()
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