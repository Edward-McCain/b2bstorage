<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-sm">
    <!-- Наименование и кнопки -->
    <div class="mb-6 w-full" style="position: sticky;top: 62px;background: #fff;z-index: 99;padding: 10px 0;">
      <div class="flex flex-col gap-3 sm:inline-flex sm:flex-row sm:items-center w-full px-4">
        <input v-model="product.name" @blur="handleNameBlur" type="text" class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm bg-white" placeholder="Наименование товара *" />
        <div class="flex gap-2 mt-3 sm:mt-0">
          <button @click="handleSave" :disabled="!product.name || (areCategoriesEnabled() && (!selectedCategory || !selectedSubcategory)) || !product.unit || !product.quantity || isSavingProduct" class="bg-lime-500 hover:bg-lime-600 text-white font-semibold px-6 py-2 rounded-lg shadow transition text-sm disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
            <svg v-if="isSavingProduct" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
            </svg>
            {{ isSavingProduct ? 'Сохранение...' : 'Сохранить' }}
          </button>
          <button @click="showCloseModal = true" class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold px-6 py-2 rounded-lg border shadow transition text-sm">Закрыть</button>
        </div>
      </div>
      <div v-if="saveError" class="text-red-500 text-xs mt-2">{{ saveError }}</div>
    </div>

    <!-- Область загрузки изображений -->
    <div class="w-full mb-6">
      <div class="bg-gray-50 rounded-xl p-4 shadow-sm">
        <div class="font-semibold mb-2">Изображения</div>
        <template v-if="loadingProduct">
          <div class="w-full h-32 flex items-center justify-center bg-white border border-gray-200 rounded-lg">
            <Loader2 class="animate-spin h-8 w-8 text-blue-500" />
            <span class="ml-3 text-sm text-gray-500">Загрузка изображений...</span>
          </div>
        </template>
        <template v-else>
          <ImageDropzone :product-id="productId" :images="images" :disabled="!product.name" @uploaded="onImageUploaded" @deleted="handleDeleteImage" />
        </template>
        <div v-if="imageUploadError" class="text-red-500 text-xs mt-2">{{ imageUploadError }}</div>
      </div>
    </div>

    <!-- Блоки с данными о товаре -->
    <div class="w-full flex flex-col gap-6">
      <!-- Общие данные -->
      <div class="bg-gray-50 rounded-xl p-4 shadow-sm">
        <div class="font-semibold mb-2">Общие данные</div>
        <div class="flex flex-col gap-3">
          <!-- Категория и подкатегория -->
          <div v-if="areCategoriesEnabled()" class="flex flex-col gap-2 w-full">
            <div class="w-full">
              <label class="block text-xs text-gray-700 mb-1">Категория <span class="text-red-500">*</span></label>
              <template v-if="loadingProduct">
                <div class="w-full h-10 flex items-center justify-center bg-white border border-gray-200 rounded-lg">
                  <Loader2 class="animate-spin h-5 w-5 text-blue-500" />
                  <span class="ml-2 text-xs text-gray-500">Загрузка данных товара...</span>
                </div>
              </template>
              <template v-else>
                <Multiselect
                  v-model="selectedCategory"
                  :options="categoryOptions"
                  label="label"
                  value="value"
                  :object="true"
                  :placeholder="categoryPlaceholder"
                  searchable
                  :search-placeholder="categorySearchPlaceholder"
                  :max-height="400"
                  class="w-full text-xs multiselect-custom bg-white"
                  @open="onCategoryOpen"
                  @close="onCategoryClose"
                />
              </template>
            </div>
            <div class="w-full">
              <label class="block text-xs text-gray-700 mb-1">Подкатегория <span class="text-red-500">*</span></label>
              <template v-if="loadingProduct">
                <div class="w-full h-10 flex items-center justify-center bg-white border border-gray-200 rounded-lg">
                  <Loader2 class="animate-spin h-5 w-5 text-blue-500" />
                  <span class="ml-2 text-xs text-gray-500">Загрузка данных товара...</span>
                </div>
              </template>
              <template v-else>
                <Multiselect
                  v-model="selectedSubcategory"
                  :options="subcategoryOptions"
                  label="label"
                  value="value"
                  :object="true"
                  :placeholder="subcategoryPlaceholder"
                  :search-placeholder="subcategorySearchPlaceholder"
                  :max-height="400"
                  :disabled="!selectedCategory"
                  :no-options="subcategoryError || 'Нет подкатегорий'"
                  searchable
                  class="w-full text-xs multiselect-custom bg-white"
                  @open="onSubcategoryOpen"
                  @close="onSubcategoryClose"
                />
              </template>
            </div>
          </div>
          <!-- Склад товара (скрыт) -->
          <div class="w-full hidden">
            <label class="block text-xs text-gray-700 mb-1">Склад товара</label>
            <div class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-gray-50 text-gray-600">
              Склад выбран при создании товара
            </div>
          </div>
          <!-- Начальный остаток и единица измерения -->
          <div class="flex gap-2">
            <div class="flex-1">
              <div class="flex items-center gap-1 mb-1">
                <label class="block text-xs text-gray-700">Начальный остаток <span class="text-red-500">*</span></label>
                <div class="group relative">
                  <svg class="w-4 h-4 text-gray-400 cursor-help" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                  </svg>
                  <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 text-xs text-white bg-gray-900 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none w-32 z-10">
                    При изменении начального остатка создается автоматическая инвентаризация.
                    <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
                  </div>
                </div>
              </div>
              <template v-if="loadingProduct">
                <div class="w-full h-10 flex items-center justify-center bg-white border border-gray-200 rounded-lg">
                  <Loader2 class="animate-spin h-5 w-5 text-blue-500" />
                  <span class="ml-2 text-xs text-gray-500">Загрузка данных товара...</span>
                </div>
              </template>
              <template v-else>
                <input v-model="product.quantity" type="number" min="0" step="1" placeholder="0" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm bg-white" />
              </template>
            </div>
            <div class="flex-1">
              <label class="block text-xs text-gray-700 mb-1">Ед-ца измерения <span class="text-red-500">*</span></label>
              <template v-if="loadingProduct">
                <div class="w-full h-10 flex items-center justify-center bg-white border border-gray-200 rounded-lg">
                  <Loader2 class="animate-spin h-5 w-5 text-blue-500" />
                  <span class="ml-2 text-xs text-gray-500">Загрузка данных товара...</span>
                </div>
              </template>
              <template v-else>
                <Multiselect
                  v-model="product.unit"
                  :options="[
                    { label: 'Штука', value: 'Штука' },
                    { label: 'Килограмм', value: 'Килограмм' },
                    { label: 'Грамм', value: 'Грамм' },
                    { label: 'Тонна', value: 'Тонна' },
                    { label: 'Литр', value: 'Литр' },
                    { label: 'Миллилитр', value: 'Миллилитр' },
                    { label: 'Метр', value: 'Метр' },
                    { label: 'Сантиметр', value: 'Сантиметр' },
                    { label: 'Квадратный метр', value: 'Квадратный метр' },
                    { label: 'Кубический метр', value: 'Кубический метр' },
                    { label: 'Упаковка', value: 'Упаковка' },
                    { label: 'Пара', value: 'Пара' },
                    { label: 'Рулон', value: 'Рулон' },
                    { label: 'Блок', value: 'Блок' },
                    { label: 'Бочка', value: 'Бочка' },
                    { label: 'Пачка', value: 'Пачка' },
                    { label: 'Комплект', value: 'Комплект' },
                    { label: 'Лист', value: 'Лист' },
                    { label: 'Погонный метр', value: 'Погонный метр' }
                  ]"
                  label="label"
                  value="value"
                  :object="true"
                  placeholder="Выберите единицу измерения"
                  :max-height="400"
                  class="w-full text-xs multiselect-custom bg-white"
                />
              </template>
            </div>
          </div>
        </div>
      </div>

      <!-- Информация о последнем оприходовании -->
      <div v-if="product.latestReceiptData" class="bg-blue-50 rounded-xl p-4 shadow-sm mb-6">
        <div class="font-semibold mb-3 text-blue-800">Информация о последнем оприходовании</div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
          <div v-if="product.latestReceiptData.quantity">
            <div class="text-gray-600 text-xs">Количество</div>
            <div class="font-medium">{{ product.latestReceiptData.quantity }}</div>
          </div>
          <div v-if="product.latestReceiptData.price">
            <div class="text-gray-600 text-xs">Цена</div>
            <div class="font-medium">{{ product.latestReceiptData.price }}</div>
          </div>
          <div v-if="product.latestReceiptData.amount">
            <div class="text-gray-600 text-xs">Сумма</div>
            <div class="font-medium">{{ product.latestReceiptData.amount }}</div>
          </div>
          <div v-if="product.latestReceiptData.balance">
            <div class="text-gray-600 text-xs">Остаток</div>
            <div class="font-medium">{{ product.latestReceiptData.balance }}</div>
          </div>
          <div v-if="product.latestReceiptData.gtd">
            <div class="text-gray-600 text-xs">ГТД</div>
            <div class="font-medium">{{ product.latestReceiptData.gtd }}</div>
          </div>
          <div v-if="product.latestReceiptData.rnpt">
            <div class="text-gray-600 text-xs">РНПТ</div>
            <div class="font-medium">{{ product.latestReceiptData.rnpt }}</div>
          </div>
          <div v-if="product.latestReceiptData.reason">
            <div class="text-gray-600 text-xs">Причина</div>
            <div class="font-medium">{{ product.latestReceiptData.reason }}</div>
          </div>
        </div>
      </div>

      <!-- Дополнительные данные (сворачиваемый блок) -->
      <div class="bg-gray-50 rounded-xl p-4 shadow-sm">
        <div class="flex items-center justify-between mb-4">
          <div class="font-semibold">Дополнительные данные</div>
          <button @click="showAdditionalData = !showAdditionalData" class="flex items-center gap-2 text-blue-600 hover:text-blue-700 text-sm">
            <span>{{ showAdditionalData ? 'Свернуть' : 'Развернуть' }}</span>
            <svg :class="showAdditionalData ? 'rotate-180' : ''" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
          </button>
        </div>
        
        <div v-show="showAdditionalData" class="flex flex-col gap-3">
          <template v-if="loadingProductFields">
            <div class="flex items-center justify-center py-8">
              <Loader2 class="animate-spin h-8 w-8 text-blue-500" />
              <span class="ml-3 text-sm text-gray-500">Загрузка полей...</span>
            </div>
          </template>
          <template v-else>
            <!-- Активные стандартные поля -->
            <template v-for="field in standardProductFields" :key="field.key">
              <div v-if="isFieldRequired(field.key) && field.key !== 'price'">
                <label class="block text-xs text-gray-700 mb-1">{{ field.label }}</label>
                <input v-model="product[field.key]" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm bg-white" />
              </div>
            </template>
            <!-- Цена отдельным блоком -->
            <div v-if="isFieldRequired('price')">
              <label class="block text-xs text-gray-700 mb-1">Стоимость за единицу</label>
              <input v-model="product.price" type="number" min="0" step="0.01" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm bg-white" />
            </div>
            <!-- Пользовательские поля -->
            <template v-for="field in customFields" :key="field.id">
              <div>
                <label class="block text-xs text-gray-700 mb-1">{{ field.field_name }}</label>
                <input v-model="customFieldValues[field.field_name]" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm bg-white" />
              </div>
            </template>
          </template>
        </div>
      </div>
    </div>

    <!-- Модалка закрытия -->
    <div v-if="showCloseModal" class="fixed inset-0 z-50 flex items-center justify-center bg-white/90">
      <div class="bg-white rounded-lg shadow-2xl p-8 max-w-sm w-full text-sm">
        <div class="text-base font-semibold mb-4">Выйти без сохранения?</div>
        <div class="mb-6 text-gray-600">Изменения не будут сохранены. Вы уверены, что хотите выйти?</div>
        <div class="flex justify-end gap-3">
          <button @click="closeModalAndGo" class="bg-red-600 hover:bg-red-700 text-white font-semibold px-5 py-2 rounded-lg shadow transition text-sm">Выйти</button>
          <button @click="showCloseModal = false" class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold px-5 py-2 rounded-lg border shadow transition text-sm">Отмена</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch, onBeforeUnmount } from 'vue'
import { useRouter, useRoute } from 'vue-router'

const route = useRoute()
import Multiselect from '@vueform/multiselect'
import '@vueform/multiselect/themes/default.css'
import { apiRequest, getFileUrl } from '@/config/api'
import { areCategoriesEnabled, isFieldRequired } from '@/utils/productFieldsUtils'
import ImageDropzone from './ImageDropzone.vue'
import countriesData from '@/data/countries.json'
import toastr from 'toastr'
import { Loader2 } from 'lucide-vue-next'

const showCloseModal = ref(false)
const hasUnsavedChanges = ref(false)
const router = useRouter()

/** @type {import('vue').Ref<Array>} */
const categories = ref([])
/** @type {import('vue').Ref<Array>} */
const subcategories = ref([])
/** @type {import('vue').Ref<Object|null>} */
const selectedCategory = ref(null)
/** @type {import('vue').Ref<Object|null>} */
const selectedSubcategory = ref(null)
/** @type {import('vue').Ref<boolean>} */
const loadingProduct = ref(true)
/** @type {import('vue').Ref<boolean>} */
const loadingCategories = ref(false)
/** @type {import('vue').Ref<boolean>} */
const loadingSubcategories = ref(false)
/** @type {import('vue').Ref<boolean>} */
const isInitialLoad = ref(true)
/** @type {import('vue').Ref<string>} */
const componentId = ref(Math.random().toString(36).substr(2, 9))
/** @type {import('vue').Ref<string>} */
const categoryError = ref('')
/** @type {import('vue').Ref<string>} */
const subcategoryError = ref('')

/** @type {import('vue').Ref<number|null>} */
const productId = ref(null)
/** @type {import('vue').Ref<number|null>} */
const lastLoadedProductId = ref(null)
/** @type {import('vue').Ref<boolean>} */
const isSavingProduct = ref(false)
/** @type {import('vue').Ref<string>} */
const saveError = ref('')

/** @type {import('vue').Ref<string>} */
const categoryPlaceholder = ref('Выберите категорию')
/** @type {import('vue').Ref<string>} */
const categorySearchPlaceholder = ref('Поиск')
/** @type {import('vue').Ref<string>} */
const subcategoryPlaceholder = ref('Выберите подкатегорию')
/** @type {import('vue').Ref<string>} */
const subcategorySearchPlaceholder = ref('Поиск')

/** @type {import('vue').Reactive<Object>} */
const product = reactive({
  name: '',
  description: '',
  category: '',
  subcategory: '',
  country: null,
  supplier: '',
  article: '',
  code: '',
  external_code: '',
  unit: null,
  weight: null,
  volume: null,
  vat: '',
  packing: null,
  accounting_type: null,
  product_type: null,
  barcode_type: null,
  barcode: '',
  cash_register_tax: '',
  cash_register_type: '',
  quantity: null,
  warehouse: null,
  price: null,
  latestReceiptData: null
})

/** @type {import('vue').Ref<Array>} */
const images = ref([])
/** @type {import('vue').Ref<string>} */
const imageUploadError = ref('')
/** @type {import('vue').Ref<boolean>} */
const isUploadingImage = ref(false)
/** @type {import('vue').Ref<string>} */
const newAltText = ref('')

/** @type {import('vue').Ref<Object|null>} */
const selectedWarehouse = ref(null)

/** @type {import('vue').Ref<boolean>} */
const showAdditionalData = ref(false)

// Список стандартных необязательных полей products_sklad
const standardProductFields = [
  { key: 'description', label: 'Описание' },
  { key: 'country', label: 'Страна' },
  { key: 'supplier', label: 'Поставщик' },
  { key: 'article', label: 'Артикул' },
  { key: 'code', label: 'Код' },
  { key: 'external_code', label: 'Внешний код' },
  { key: 'weight', label: 'Вес' },
  { key: 'volume', label: 'Объем' },
  { key: 'vat', label: 'Ставка НДС' },
  { key: 'min_stock', label: 'Минимальный остаток' },
  { key: 'stock_type', label: 'Тип запаса' },
  { key: 'packing', label: 'Упаковка' },
  { key: 'accounting_type', label: 'Тип учета' },
  { key: 'traceable', label: 'Маркируемый' },
  { key: 'marking', label: 'Маркировка' },
  { key: 'product_type', label: 'Тип товара' },
  { key: 'barcode_type', label: 'Тип штрихкода' },
  { key: 'barcode', label: 'Штрихкод' },
  { key: 'cash_register_tax', label: 'Налог ККМ' },
  { key: 'cash_register_type', label: 'Тип ККМ' },
  { key: 'price', label: 'Цена' },
]

const productFieldsVisibility = reactive({})
const customFields = ref([])
const customFieldValues = reactive({})
const loadingProductFields = ref(true)

async function loadProductFieldsVisibilityAndCustomFields() {
  loadingProductFields.value = true
  try {
    // Сначала проверяем localStorage
    const savedSettings = localStorage.getItem('product_fields_visibility')
    if (savedSettings) {
      try {
        const vis = JSON.parse(savedSettings)
        const defaults = Object.fromEntries(standardProductFields.map(f => [f.key, true]))
        Object.assign(productFieldsVisibility, { ...defaults, ...vis })
        console.log('Настройки полей загружены из localStorage')
      } catch (e) {
        console.error('Ошибка парсинга настроек из localStorage:', e)
        // Если ошибка парсинга, загружаем с сервера
        await loadSettingsFromServer()
      }
    } else {
      // Если в localStorage нет настроек, загружаем с сервера
      await loadSettingsFromServer()
    }
    
    // Загрузка пользовательских полей
    const fieldsResp = await apiRequest('/product-fields', { method: 'GET' })
    if (fieldsResp.ok && fieldsResp.data.success) {
      customFields.value = fieldsResp.data.data || []
      customFields.value.forEach(f => { if (!(f.field_name in customFieldValues)) customFieldValues[f.field_name] = '' })
    }
  } catch (e) {
    console.error('Ошибка загрузки настроек полей:', e)
    Object.assign(productFieldsVisibility, Object.fromEntries(standardProductFields.map(f => [f.key, true])))
  } finally {
    loadingProductFields.value = false
  }
}

async function loadSettingsFromServer() {
  const settingsResp = await apiRequest('/user/settings', { method: 'GET' })
  let vis = settingsResp.data?.data?.personal?.product_fields_visibility
  if (typeof vis === 'string') {
    try { vis = JSON.parse(vis) } catch (e) { vis = null }
  }
  const defaults = Object.fromEntries(standardProductFields.map(f => [f.key, true]))
  Object.assign(productFieldsVisibility, { ...defaults, ...(vis || {}) })
  
  // Сохраняем настройки в localStorage для будущего использования
  localStorage.setItem('product_fields_visibility', JSON.stringify(productFieldsVisibility))
  console.log('Настройки полей загружены с сервера и сохранены в localStorage')
}

const countries = computed(() =>
  countriesData.map(country => ({
    label: country.name,
    value: country.id,
    code: country.code,
    raw: country
  }))
)

const showTooltip = reactive({
  article: false,
  code: false
})

async function handleNameBlur() {
  // Для редактирования товара это не нужно, так как товар уже существует
}





async function handleImageUpload(event) {
  if (!productId.value) return
  const file = event.target.files[0]
  if (!file) return
  imageUploadError.value = ''
  isUploadingImage.value = true
  try {
    const formData = new FormData()
    formData.append('image', file)
    if (newAltText.value) formData.append('alt_text', newAltText.value)
    
    const response = await apiRequest(`/products/${productId.value}/images`, {
      method: 'POST',
      headers: {}, // Убираем Content-Type для FormData
      body: formData
    })
    
    if (response.ok && response.data.image) {
      images.value.push(response.data.image)
      newAltText.value = ''
    } else {
      imageUploadError.value = response.data.message || 'Ошибка загрузки изображения'
    }
  } catch (e) {
    console.error('Upload error:', e)
    imageUploadError.value = 'Ошибка загрузки изображения'
  } finally {
    isUploadingImage.value = false
    event.target.value = '' // сбросить input
  }
}

async function handleDeleteImage(imgId) {
  console.log('handleDeleteImage called in ProductEditPage with ID:', imgId)
  console.log('Images array before deletion:', images.value.map(img => img.id))
  
  // Удаляем изображение из массива (API запрос уже выполнен в ImageDropzone)
  images.value = images.value.filter(img => img.id !== imgId)
  
  console.log('Images array after deletion:', images.value.map(img => img.id))
  console.log('Image removed from parent component array:', imgId)
}



onMounted(async () => {
  // Явно устанавливаем productId из route.params.id
  productId.value = parseInt(route.params.id)
  // 1. Загружаем категории
  loadingCategories.value = true
  try {
    categories.value = await getCategoriesWithCache()
  } catch (e) {
    categoryError.value = 'Ошибка загрузки категорий'
  } finally {
    loadingCategories.value = false
  }
  // 2. Загружаем кастомные поля и настройки видимости
  await loadProductFieldsVisibilityAndCustomFields()
  // 3. Загружаем сам товар и подставляем значения
  await loadProduct()
  // Устанавливаем флаг завершения начальной загрузки
  isInitialLoad.value = false
  // Добавляем обработчики для предотвращения случайного закрытия
  window.addEventListener('beforeunload', handleBeforeUnload)
  // Добавляем обработчик для навигации внутри приложения
  router.beforeEach(handleBeforeRouteLeave)
})

onBeforeUnmount(() => {
  console.log('Component unmounting:', componentId.value)
  // Удаляем обработчики при размонтировании компонента
  window.removeEventListener('beforeunload', handleBeforeUnload)
  // Удаляем обработчик маршрутов - используем пустую функцию
  router.beforeEach(() => true)
})

// Обработчик для навигации внутри приложения
function handleBeforeRouteLeave(to, from, next) {
  console.log('Route guard triggered:', { to: to.path, from: from.path, hasUnsavedChanges: hasUnsavedChanges.value })
  if (hasUnsavedChanges.value) {
    showCloseModal.value = true
    next(false)
  } else {
    next()
  }
}

async function loadProduct() {
  if (!productId.value) return
  
  // Проверяем, не загружали ли мы уже этот товар
  if (lastLoadedProductId.value === productId.value) {
    console.log('Product already loaded, skipping:', productId.value)
    return
  }
  
  console.log('loadProduct called for productId:', productId.value)
  loadingProduct.value = true
  
  try {
    const response = await apiRequest(`/products/${productId.value}`)
    if (response.ok && response.data.success) {
      const productData = response.data.data
      
      // Заполняем данные товара
      product.name = productData.name || ''
      product.description = productData.description || ''
      product.supplier = productData.supplier || ''
      product.article = productData.article || ''
      product.code = productData.code || ''
      product.external_code = productData.external_code || ''
      product.weight = productData.weight
      product.volume = productData.volume
      product.vat = productData.vat || ''
      product.barcode = productData.barcode || ''
      product.cash_register_tax = productData.cash_register_tax || ''
      product.cash_register_type = productData.cash_register_type || ''
      // Приоритетно используем current_quantity, если он есть
      if (productData.current_quantity !== undefined) {
        product.quantity = productData.current_quantity
      } else {
        product.quantity = productData.quantity || 0
      }
      product.price = productData.price || 0
      
      // Сохраняем данные из receipt_positions для отображения
      if (productData.latest_quantity !== undefined || productData.latest_price !== undefined) {
        product.latestReceiptData = {
          quantity: productData.latest_quantity,
          price: productData.latest_price,
          amount: productData.latest_amount,
          balance: productData.latest_balance,
          gtd: productData.latest_gtd,
          rnpt: productData.latest_rnpt,
          reason: productData.latest_reason
        }
      }
      
      // Используем данные из receipt_positions для цены
      if (productData.latest_price !== undefined) {
        product.price = productData.latest_price
      }
      
      // Дополняем данные из receipt_positions, если они отсутствуют в основном товаре
      if (productData.latest_code !== undefined && !product.code) {
        product.code = productData.latest_code
      }
      if (productData.latest_article !== undefined && !product.article) {
        product.article = productData.latest_article
      }
      if (productData.latest_barcode !== undefined && !product.barcode) {
        product.barcode = productData.latest_barcode
      }
      if (productData.latest_country !== undefined && !product.country) {
        product.country = countries.value.find(c => c.value === productData.latest_country) || null
      }
      
      // Устанавливаем единицу измерения
      if (productData.unit) {
        product.unit = { label: productData.unit, value: productData.unit }
      }
      
      // Устанавливаем страну (если не установлена из receipt_positions)
      if (productData.country && !product.country) {
        product.country = countries.value.find(c => c.value === productData.country) || null
      }
      
      // Устанавливаем фасовку
      if (productData.packing) {
        product.packing = { label: productData.packing, value: productData.packing }
      }
      
      // Устанавливаем тип учета
      if (productData.accounting_type) {
        product.accounting_type = { label: productData.accounting_type, value: productData.accounting_type }
      }
      
      // Устанавливаем тип продукции
      if (productData.product_type) {
        product.product_type = { label: productData.product_type, value: productData.product_type }
      }
      
      // Устанавливаем тип штрихкода
      if (productData.barcode_type) {
        product.barcode_type = { label: productData.barcode_type, value: productData.barcode_type }
      }
      
      // Устанавливаем категорию и подкатегорию
      if (productData.category) {
        if (categoryOptions.value.length > 0) {
          selectedCategory.value = categoryOptions.value.find(c => c.value === productData.category) || null
          // Загружаем подкатегории напрямую, без watch
          if (productData.category) {
            loadingSubcategories.value = true
            try {
              const response = await apiRequest(`/subcategories?category_id=${encodeURIComponent(productData.category)}`)
              if (response.ok && response.data.success) {
                subcategories.value = response.data.data || []
              } else {
                subcategoryError.value = 'Ошибка загрузки подкатегорий'
              }
            } catch (e) {
              subcategoryError.value = 'Ошибка загрузки подкатегорий'
            } finally {
              loadingSubcategories.value = false
            }
          }
          if (productData.subcategory) {
            selectedSubcategory.value = subcategoryOptions.value.find(s => s.value === productData.subcategory) || null
          }
        } else {
          // Категории ещё не загружены — ждём их появления
          const unwatch = watch(categoryOptions, (opts) => {
            if (opts.length > 0) {
              selectedCategory.value = opts.find(c => c.value === productData.category) || null
              // Загружаем подкатегории напрямую
              loadingSubcategories.value = true
              apiRequest(`/subcategories?category_id=${encodeURIComponent(productData.category)}`)
                .then(response => {
                  if (response.ok && response.data.success) {
                    subcategories.value = response.data.data || []
                  } else {
                    subcategoryError.value = 'Ошибка загрузки подкатегорий'
                  }
                })
                .catch(() => {
                  subcategoryError.value = 'Ошибка загрузки подкатегорий'
                })
                .finally(() => {
                  loadingSubcategories.value = false
                  if (productData.subcategory) {
                    selectedSubcategory.value = subcategoryOptions.value.find(s => s.value === productData.subcategory) || null
                  }
                  unwatch()
                })
            }
          })
        }
      }
      
      // Устанавливаем склад (просто сохраняем ID)
      if (productData.warehouse_id) {
        selectedWarehouse.value = { label: 'Склад товара', value: productData.warehouse_id }
      }
      
      // Изображения уже загружены вместе с товаром
      if (productData.images) {
        images.value = productData.images
      }
      
      // Отмечаем, что товар загружен
      lastLoadedProductId.value = productId.value
      
      // После загрузки товара — корректно подставить значения в customFieldValues
      if (productData.fields && typeof productData.fields === 'object') {
        customFields.value.forEach(f => {
          customFieldValues[f.field_name] = productData.fields[f.field_name] ?? ''
        })
      } else {
        customFields.value.forEach(f => {
          customFieldValues[f.field_name] = ''
        })
      }
      // Категория и подкатегория
      if (productData.category) {
        selectedCategory.value = categoryOptions.value.find(c => c.value === productData.category) || null
        // Загружаем подкатегории напрямую
        if (productData.category) {
          loadingSubcategories.value = true
          try {
            const response = await apiRequest(`/subcategories?category_id=${encodeURIComponent(productData.category)}`)
            if (response.ok && response.data.success) {
              subcategories.value = response.data.data || []
            } else {
              subcategoryError.value = 'Ошибка загрузки подкатегорий'
            }
          } catch (e) {
            subcategoryError.value = 'Ошибка загрузки подкатегорий'
          } finally {
            loadingSubcategories.value = false
          }
        }
        if (productData.subcategory) {
          selectedSubcategory.value = subcategoryOptions.value.find(s => s.value === productData.subcategory) || null
        }
      }
      
    } else {
      saveError.value = response.data.message || 'Ошибка загрузки товара'
    }
  } catch (error) {
    console.error('Ошибка загрузки товара:', error)
    saveError.value = 'Ошибка загрузки товара'
  } finally {
    loadingProduct.value = false
  }
}

async function loadSubcategories(categoryId) {
  if (!categoryId) return
  
  loadingSubcategories.value = true
  try {
    const response = await apiRequest(`/subcategories?category_id=${encodeURIComponent(categoryId)}`)
    if (response.ok && response.data.success) {
      subcategories.value = response.data.data || []
    } else {
      subcategoryError.value = 'Ошибка загрузки подкатегорий'
    }
  } catch (e) {
    subcategoryError.value = 'Ошибка загрузки подкатегорий'
  } finally {
    loadingSubcategories.value = false
  }
}

watch(selectedCategory, async (cat) => {
  // Пропускаем при начальной загрузке
  if (isInitialLoad.value) return
  
  selectedSubcategory.value = null
  product.category = cat ? cat.value : ''
  subcategories.value = []
  if (cat && cat.value) {
    await loadSubcategories(cat.value)
  }
})

watch(selectedSubcategory, (subcat) => {
  product.subcategory = subcat ? subcat.value : ''
})

watch(selectedWarehouse, (warehouse) => {
  product.warehouse = warehouse ? warehouse.value : null
})

// Отслеживаем изменения в форме для показа предупреждения
watch(() => product.name, () => {
  if (isInitialLoad.value) return
  if (product.name) hasUnsavedChanges.value = true
})

watch(() => product.description, () => {
  if (isInitialLoad.value) return
  if (product.description) hasUnsavedChanges.value = true
})

watch(() => product.category, () => {
  if (isInitialLoad.value) return
  if (product.category) hasUnsavedChanges.value = true
})

watch(() => product.subcategory, () => {
  if (isInitialLoad.value) return
  if (product.subcategory) hasUnsavedChanges.value = true
})

watch(() => product.country, () => {
  if (isInitialLoad.value) return
  if (product.country && product.country.value) hasUnsavedChanges.value = true
})

watch(() => product.supplier, () => {
  if (isInitialLoad.value) return
  if (product.supplier) hasUnsavedChanges.value = true
})

watch(() => product.article, () => {
  if (isInitialLoad.value) return
  if (product.article) hasUnsavedChanges.value = true
})

watch(() => product.code, () => {
  if (isInitialLoad.value) return
  if (product.code) hasUnsavedChanges.value = true
})

watch(() => product.external_code, () => {
  if (isInitialLoad.value) return
  if (product.external_code) hasUnsavedChanges.value = true
})

watch(() => product.unit, () => {
  if (isInitialLoad.value) return
  if (product.unit && product.unit.value) hasUnsavedChanges.value = true
})

watch(() => product.weight, () => {
  if (isInitialLoad.value) return
  if (product.weight) hasUnsavedChanges.value = true
})

watch(() => product.volume, () => {
  if (isInitialLoad.value) return
  if (product.volume) hasUnsavedChanges.value = true
})

watch(() => product.vat, () => {
  if (isInitialLoad.value) return
  if (product.vat) hasUnsavedChanges.value = true
})

watch(() => product.packing, () => {
  if (isInitialLoad.value) return
  if (product.packing && product.packing.value) hasUnsavedChanges.value = true
})

watch(() => product.accounting_type, () => {
  if (isInitialLoad.value) return
  if (product.accounting_type && product.accounting_type.value) hasUnsavedChanges.value = true
})

watch(() => product.product_type, () => {
  if (isInitialLoad.value) return
  if (product.product_type && product.product_type.value) hasUnsavedChanges.value = true
})

watch(() => product.barcode, () => {
  if (isInitialLoad.value) return
  if (product.barcode) hasUnsavedChanges.value = true
})

watch(() => product.barcode_type, () => {
  if (isInitialLoad.value) return
  if (product.barcode_type && product.barcode_type.value) hasUnsavedChanges.value = true
})

watch(() => product.cash_register_tax, () => {
  if (isInitialLoad.value) return
  if (product.cash_register_tax) hasUnsavedChanges.value = true
})

watch(() => product.cash_register_type, () => {
  if (isInitialLoad.value) return
  if (product.cash_register_type) hasUnsavedChanges.value = true
})

watch(() => product.quantity, () => {
  if (isInitialLoad.value) return
  if (product.quantity) hasUnsavedChanges.value = true
})

watch(() => product.warehouse, () => {
  if (isInitialLoad.value) return
  if (product.warehouse) hasUnsavedChanges.value = true
})

watch(() => product.price, () => {
  if (isInitialLoad.value) return
  if (product.price) hasUnsavedChanges.value = true
})

// Отслеживаем изменения в изображениях
watch(images, () => {
  if (isInitialLoad.value) return
  if (images.value.length > 0) hasUnsavedChanges.value = true
}, { deep: true })

// Отслеживаем изменения в route.params
watch(() => route.params.id, (newId) => {
  if (newId && parseInt(newId) !== productId.value) {
    console.log('Route ID changed to:', newId, 'component:', componentId.value)
    // Сбрасываем флаги при изменении ID товара
    lastLoadedProductId.value = null
    isInitialLoad.value = true
    productId.value = parseInt(newId)
    // Перезагружаем данные
    loadProduct()
  }
})

const categoryOptions = computed(() =>
  Array.isArray(categories.value)
    ? categories.value.map(c => ({
        label: c.name_ru || c.name,
        value: c.category_id,
        raw: c
      }))
    : []
)
const subcategoryOptions = computed(() =>
  Array.isArray(subcategories.value)
    ? subcategories.value.map(s => ({
        label: s.name_ru || s.name,
        value: s.subcategory_id,
        raw: s
      }))
    : []
)



function closeModalAndGo() {
  showCloseModal.value = false
  hasUnsavedChanges.value = false
  router.push('/products/balances')
}

function handleBeforeUnload(event) {
  if (hasUnsavedChanges.value) {
    event.preventDefault()
    event.returnValue = 'У вас есть несохраненные изменения. Вы уверены, что хотите покинуть страницу?'
    return 'У вас есть несохраненные изменения. Вы уверены, что хотите покинуть страницу?'
  }
}

async function handleSave() {
  if (!productId.value) {
    toastr.error('Товар не найден')
    return
  }

  isSavingProduct.value = true

  try {
    // Подготавливаем данные для отправки
    const productData = {}
    if (productId.value) productData.id = productId.value
    // Обязательные поля
    productData.name = product.name
    productData.unit = (product.unit && typeof product.unit === 'object' && product.unit.value) ? product.unit.value : product.unit
    productData.start_count = product.quantity // Количество (отображается, не редактируется)
    productData.price = product.price // Стоимость (отображается, не редактируется)
    
    // Добавляем категории только если они включены
    if (areCategoriesEnabled()) {
      productData.category_id = product.category
      productData.subcategory_id = product.subcategory
    }
    
    // Остальные поля, если заполнены
    if (product.description) productData.description = product.description
    if (product.country && typeof product.country === 'object' && product.country.value) productData.country = product.country.value
    if (product.supplier) productData.supplier = product.supplier
    if (product.article) productData.article = product.article
    if (product.code) productData.code = product.code
    if (product.external_code) productData.external_code = product.external_code
    if (product.weight !== null && product.weight !== undefined && product.weight !== '') productData.weight = product.weight
    if (product.volume !== null && product.volume !== undefined && product.volume !== '') productData.volume = product.volume
    if (product.vat) productData.vat = product.vat
    if (product.accounting_type && typeof product.accounting_type === 'object' && product.accounting_type.value) productData.accounting_type = product.accounting_type.value
    if (product.product_type && typeof product.product_type === 'object' && product.product_type.value) productData.product_type = product.product_type.value
    if (product.barcode_type && typeof product.barcode_type === 'object' && product.barcode_type.value) productData.barcode_type = product.barcode_type.value
    if (product.barcode) productData.barcode = product.barcode
    if (product.cash_register_tax) productData.cash_register_tax = product.cash_register_tax
    if (product.cash_register_type) productData.cash_register_type = product.cash_register_type
    // warehouse_id не отправляем при редактировании
    productData.fields = { ...customFieldValues }

    // Отправляем запрос на сохранение
    const response = await apiRequest(`/products/${productId.value}`, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(productData)
    })

    if (response.ok) {
      hasUnsavedChanges.value = false
      toastr.success('Товар успешно сохранен')
      console.log('Сохраненные данные:', productData)
      // Перенаправляем на страницу Остатки
      router.push('/products/balances')
    } else {
      toastr.error('Ошибка при сохранении товара: ' + (response.error || 'Неизвестная ошибка'))
    }
  } catch (error) {
    console.error('Ошибка при сохранении товара:', error)
    toastr.error('Ошибка при сохранении товара: ' + error.message)
  } finally {
    isSavingProduct.value = false
  }
}

function onCategoryOpen() {
  categoryPlaceholder.value = ''
}
function onCategoryClose() {
  categoryPlaceholder.value = 'Выберите категорию'
}
function onSubcategoryOpen() {
  subcategoryPlaceholder.value = ''
}
function onSubcategoryClose() {
  subcategoryPlaceholder.value = 'Выберите подкатегорию'
}

const onImageUploaded = (img) => {
  images.value.push(img)
}

</script>

<style scoped>
.multiselect-custom,
.multiselect,
.multiselect__input,
.multiselect__option {
  font-size: 0.95rem !important;
}
.multiselect__content-wrapper {
  max-height: 400px !important;
}
</style> 