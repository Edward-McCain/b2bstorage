<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-sm">
    <!-- Наименование и кнопки -->
    <div class="mb-6 w-full" style="position: sticky;top: 62px;background: #fff;z-index: 99;padding: 10px 0;">
      <div class="flex flex-col gap-3 sm:inline-flex sm:flex-row sm:items-center w-full px-4">
        <input v-model="product.name" @blur="handleNameBlur" type="text" class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm bg-white" :placeholder="t('ProductCreatePage_1')" style="border: 1px solid #ddd !important;" /> <!-- Наименование товара * -->
        <div class="flex gap-2 mt-3 sm:mt-0">
          <button @click="handleSave" :disabled="!product.name || (areCategoriesEnabled() && (!selectedCategory || !selectedSubcategory)) || !selectedWarehouse || !product.unit || !product.start_count || !productId || isSavingDraft || isSavingProduct" class="bg-lime-500 hover:bg-lime-600 text-white font-semibold px-6 py-2 rounded-lg shadow transition text-sm disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
            <svg v-if="isSavingDraft || isSavingProduct" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
            </svg>
            {{ isSavingDraft ? t('ProductCreatePage_2') : isSavingProduct ? t('ProductCreatePage_3') : t('ProductCreatePage_4') }} <!-- Создание черновика... Сохранение... Сохранить -->
          </button>
          <button @click="showCloseModal = true" class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold px-6 py-2 rounded-lg border shadow transition text-sm">{{ t('ProductCreatePage_5') }}</button> <!-- Закрыть -->
        </div>
      </div>
      <div v-if="draftError" class="text-red-500 text-xs mt-2">{{ draftError }}</div>
    </div>
    <!-- Правая колонка (табы и модификации) -->
    <!-- <div class="w-full mb-6">
      <div class="flex gap-2 mb-4">
        <button class="px-6 py-2 rounded bg-gray-100 text-gray-700 font-semibold">Цены</button>
        <button class="px-6 py-2 rounded bg-blue-600 text-white font-semibold">Модификации (0)</button>
        <button class="px-6 py-2 rounded bg-gray-100 text-gray-700 font-semibold">Упаковка (0)</button>
        <button class="px-6 py-2 rounded bg-gray-100 text-gray-700 font-semibold">Остатки</button>
        <button class="px-6 py-2 rounded bg-gray-100 text-gray-700 font-semibold">История</button>
        <button class="px-6 py-2 rounded bg-gray-100 text-gray-700 font-semibold">Файлы (0)</button>
      </div>
      <div class="bg-gray-50 rounded-lg p-4 mb-4">
        <button class="flex items-center gap-2 bg-white border border-blue-200 text-blue-700 font-medium px-4 py-2 rounded text-sm"><span class="text-lg">＋</span>Модификация</button>
      </div>
    </div> -->
    <!-- Область загрузки изображений -->
    <div class="w-full mb-6">
      <div class="bg-gray-50 rounded-xl p-4 shadow-sm">
        <div class="font-semibold mb-2">{{ t('ProductCreatePage_6') }}</div> <!-- Изображения -->
        <ImageDropzone :product-id="productId" :images="images" :disabled="!product.name || !productId" @uploaded="onImageUploaded" @deleted="handleDeleteImage" />
        <div v-if="imageUploadError" class="text-red-500 text-xs mt-2">{{ imageUploadError }}</div>
      </div>
    </div>
    <!-- Блоки с данными о товаре -->
    <div class="w-full flex flex-col gap-6">
      <!-- Общие данные -->
      <div class="bg-gray-50 rounded-xl p-4 shadow-sm">
        <div class="font-semibold mb-2">{{ t('ProductCreatePage_7') }}</div> <!-- Общие данные -->
        <div class="flex flex-col gap-3">
          <!-- Категория и подкатегория -->
          <div v-if="areCategoriesEnabled()" class="flex flex-col gap-2 w-full">
            <div class="w-full">
              <label class="block text-xs text-gray-700 mb-1">{{ t('ProductCreatePage_8') }} <span class="text-red-500">*</span></label> <!-- Категория -->
              <template v-if="loadingCategories">
                <div class="w-full h-10 flex items-center justify-center bg-white border border-gray-200 rounded-lg">
                  <svg class="animate-spin h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                  </svg>
                  <span class="ml-2 text-xs text-gray-500">{{ t('ProductCreatePage_9') }}</span> <!-- Загрузка категорий... -->
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
              <label class="block text-xs text-gray-700 mb-1">{{ t('ProductCreatePage_12') }} <span class="text-red-500">*</span></label> <!-- Подкатегория -->
              <template v-if="loadingSubcategories">
                <div class="w-full h-10 flex items-center justify-center bg-white border border-gray-200 rounded-lg">
                  <svg class="animate-spin h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                  </svg>
                  <span class="ml-2 text-xs text-gray-500">{{ t('ProductCreatePage_13') }}</span> <!-- Загрузка подкатегорий... -->
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
                  :no-options="subcategoryError || t('ProductCreatePage_15')"
                  searchable
                  class="w-full text-xs multiselect-custom bg-white"
                  @open="onSubcategoryOpen"
                  @close="onSubcategoryClose"
                />
              </template>
            </div>
          </div>
          <!-- Склад товара -->
          <div class="w-full">
            <label class="block text-xs text-gray-700 mb-1">{{ t('ProductCreatePage_16') }} <span class="text-red-500">*</span></label> <!-- Склад товара -->
            <template v-if="loadingWarehouses">
              <div class="w-full h-10 flex items-center justify-center bg-white border border-gray-200 rounded-lg">
                <svg class="animate-spin h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                </svg>
                <span class="ml-2 text-xs text-gray-500">{{ t('ProductCreatePage_17') }}</span> <!-- Загрузка складов... -->
              </div>
            </template>
            <template v-else>
              <Multiselect
                v-model="selectedWarehouse"
                :options="warehouseOptions"
                label="label"
                value="value"
                :object="true"
                :placeholder="t('ProductCreatePage_18')"
                :disabled="warehouses.length === 0"
                :max-height="400"
                class="w-full text-xs multiselect-custom"
              />
            </template>
            <!-- Сообщение если складов нет -->
            <div v-if="warehouses.length === 0 && !loadingWarehouses" class="mt-2 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
              <div class="text-sm text-yellow-800 mb-2">{{ t('ProductCreatePage_19') }}</div> <!-- Для добавления товаров вам необходимо сначала добавить склад. -->
              <button @click="goToCreateWarehouse" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg shadow transition text-sm">
                {{ t('ProductCreatePage_20') }} <!-- Добавить склад -->
              </button>
            </div>
          </div>
          <!-- Количество единиц товара и единица измерения -->
          <div class="flex flex-col sm:flex-row gap-2">
            <div class="flex-1">
              <label class="block text-xs text-gray-700 mb-1">{{ t('ProductCreatePage_21') }} <span class="text-red-500">*</span></label> <!-- Начальный остаток -->
              <input v-model.number="product.start_count" type="number" min="0" step="1" placeholder="0" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm bg-white" />
            </div>
            <div class="flex-1">
              <label class="block text-xs text-gray-700 mb-1">{{ t('ProductCreatePage_22') }} <span class="text-red-500">*</span></label> <!-- Ед-ца измерения -->
              <Multiselect
                v-model="product.unit"
                :options="[
                  { label: t('ProductCreatePage_23'), value: t('ProductCreatePage_23') }, // Штука
                  { label: t('ProductCreatePage_24'), value: t('ProductCreatePage_24') }, // Килограмм
                  { label: t('ProductCreatePage_25'), value: t('ProductCreatePage_25') }, // Грамм
                  { label: t('ProductCreatePage_26'), value: t('ProductCreatePage_26') }, // Тонна
                  { label: t('ProductCreatePage_27'), value: t('ProductCreatePage_27') }, // Литр
                  { label: t('ProductCreatePage_28'), value: t('ProductCreatePage_28') }, // Миллилитр
                  { label: t('ProductCreatePage_29'), value: t('ProductCreatePage_29') }, // Метр
                  { label: t('ProductCreatePage_30'), value: t('ProductCreatePage_30') }, // Сантиметр
                  { label: t('ProductCreatePage_31'), value: t('ProductCreatePage_31') }, // Квадратный метр
                  { label: t('ProductCreatePage_32'), value: t('ProductCreatePage_32') }, // Кубический метр
                  { label: t('ProductCreatePage_33'), value: t('ProductCreatePage_33') }, // Упаковка
                  { label: t('ProductCreatePage_34'), value: t('ProductCreatePage_34') }, // Пара
                  { label: t('ProductCreatePage_35'), value: t('ProductCreatePage_35') }, // Рулон
                  { label: t('ProductCreatePage_36'), value: t('ProductCreatePage_36') }, // Блок
                  { label: t('ProductCreatePage_37'), value: t('ProductCreatePage_37') }, // Бочка
                  { label: t('ProductCreatePage_38'), value: t('ProductCreatePage_38') }, // Пачка
                  { label: t('ProductCreatePage_39'), value: t('ProductCreatePage_39') }, // Комплект
                  { label: t('ProductCreatePage_40'), value: t('ProductCreatePage_40') }, // Лист
                  { label: t('ProductCreatePage_41'), value: t('ProductCreatePage_41') } // Погонный метр
                ]"
                label="label"
                value="value"
                :object="true"
                :placeholder="t('ProductCreatePage_42')"
                :max-height="400"
                class="w-full text-xs multiselect-custom bg-white"
              />
            </div>
          </div>
        </div>
      </div>

      <!-- Дополнительные данные (сворачиваемый блок) -->
      <div class="bg-gray-50 rounded-xl p-4 shadow-sm">
        <div class="flex items-center justify-between mb-4">
          <div class="font-semibold">{{ t('ProductCreatePage_43') }}</div> <!-- Дополнительные данные -->
          <button @click="showAdditionalData = !showAdditionalData" class="flex items-center gap-2 text-blue-600 hover:text-blue-700 text-sm">
            <span>{{ showAdditionalData ? t('ProductCreatePage_44') : t('ProductCreatePage_45') }}</span> <!-- Свернуть Развернуть -->
            <svg :class="showAdditionalData ? 'rotate-180' : ''" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
          </button>
        </div>
        
        <div v-show="showAdditionalData" class="flex flex-col gap-3">
          <template v-if="loadingProductFields">
            <div class="flex items-center justify-center py-8">
              <Loader2 class="animate-spin h-8 w-8 text-blue-500" />
              <span class="ml-3 text-sm text-gray-500">{{ t('ProductCreatePage_46') }}</span> <!-- Загрузка полей... -->
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
              <label class="block text-xs text-gray-700 mb-1">{{ t('ProductCreatePage_68') }}</label> <!-- Стоимость за единицу -->
              <input v-model="product.price" type="number" min="0" step="0.01" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm bg-white" />
            </div>
            <!-- Пользовательские поля -->
            <template v-for="field in customFields" :key="field.id">
              <div>
                <label class="block text-xs text-gray-700 mb-1">{{ field.field_name }}</label>
                
                <!-- Текстовое поле -->
                <input 
                  v-if="field.field_type === 'text'" 
                  v-model="customFieldValues[field.field_name]" 
                  type="text" 
                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm bg-white" 
                />
                
                <!-- Числовое поле -->
                <input 
                  v-else-if="field.field_type === 'number'" 
                  v-model="customFieldValues[field.field_name]" 
                  type="number" 
                  step="0.01"
                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm bg-white" 
                />
                
                <!-- Поле даты -->
                <LocalizedDatePicker 
                  v-else-if="field.field_type === 'date'" 
                  v-model="customFieldValues[field.field_name]"
                  :enable-time-picker="false"
                  :auto-apply="true"
                />
                
                <!-- Поле списка -->
                <Multiselect
                  v-else-if="field.field_type === 'list'"
                  v-model="customFieldValues[field.field_name]"
                  :options="getListOptionsForMultiselect(field)"
                  label="label"
                  value="value"
                  :object="false"
                  :placeholder="t('ProductCreatePage_69')"
                  :max-height="400"
                  class="w-full text-xs multiselect-custom bg-white"
                />
                
                <!-- По умолчанию текстовое поле -->
                <input 
                  v-else 
                  v-model="customFieldValues[field.field_name]" 
                  type="text" 
                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm bg-white" 
                />
              </div>
            </template>
          </template>
        </div>
      </div>
    </div>
    <!-- Модалка закрытия -->
    <div v-if="showCloseModal" class="fixed inset-0 z-50 flex items-center justify-center bg-white/70">
      <div class="bg-white rounded-lg shadow-2xl p-8 max-w-sm w-full text-sm">
        <div class="text-base font-semibold mb-4">{{ t('ProductCreatePage_70') }}</div> <!-- Выйти без сохранения? -->
        <div class="mb-6 text-gray-600">{{ t('ProductCreatePage_71') }}</div> <!-- Изменения не будут сохранены. Вы уверены, что хотите выйти? -->
        <div class="flex justify-end gap-3">
          <button @click="closeModalAndGo" class="bg-red-600 hover:bg-red-700 text-white font-semibold px-5 py-2 rounded-lg shadow transition text-sm">{{ t('ProductCreatePage_72') }}</button> <!-- Выйти -->
          <button @click="showCloseModal = false" class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold px-5 py-2 rounded-lg border shadow transition text-sm">{{ t('ProductCreatePage_73') }}</button> <!-- Отмена -->
        </div>
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
import { ref, reactive, computed, onMounted, watch, onBeforeUnmount } from 'vue'
import { useRouter } from 'vue-router'
import Multiselect from '@vueform/multiselect'
import '@vueform/multiselect/themes/default.css'
import { apiRequest, getFileUrl, getCategoriesByUserSettings } from '@/config/api'
import { createReactiveCategoryOptions, createReactiveSubcategoryOptions } from '@/utils/categoryDisplayUtils'
import { areCategoriesEnabled, isFieldRequired } from '@/utils/productFieldsUtils'
import { getUserCategoryType, getSubcategoriesApiEndpoint } from '@/utils/categoryTypeUtils'
import { useWarehouseCheck } from '@/composables/useWarehouseCheck'
import ImageDropzone from './ImageDropzone.vue'
import NoWarehousesModal from '../NoWarehousesModal.vue'
import LocalizedDatePicker from '../LocalizedDatePicker.vue'
import countriesData from '@/data/countries.json'
import toastr from 'toastr'
import { Loader2 } from 'lucide-vue-next'
import { t } from '@/locales'

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
const loadingCategories = ref(false)
/** @type {import('vue').Ref<boolean>} */
const loadingSubcategories = ref(false)
/** @type {import('vue').Ref<string>} */
const categoryError = ref('')
/** @type {import('vue').Ref<string>} */
const subcategoryError = ref('')

/** @type {import('vue').Ref<number|null>} */
const productId = ref(null)
/** @type {import('vue').Ref<boolean>} */
const isSavingDraft = ref(false)
/** @type {import('vue').Ref<boolean>} */
const isSavingProduct = ref(false)
/** @type {import('vue').Ref<string>} */
const draftError = ref('')

/** @type {import('vue').Ref<string>} */
const categoryPlaceholder = ref(t('ProductCreatePage_10')) // Выберите категорию
/** @type {import('vue').Ref<string>} */
const categorySearchPlaceholder = ref(t('ProductCreatePage_11')) // Поиск
/** @type {import('vue').Ref<string>} */
const subcategoryPlaceholder = ref(t('ProductCreatePage_14')) // Выберите подкатегорию
/** @type {import('vue').Ref<string>} */
const subcategorySearchPlaceholder = ref(t('ProductCreatePage_11')) // Поиск

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
  start_count: null,
  warehouse: null,
  price: null
})

/** @type {import('vue').Ref<Array>} */
const images = ref([])
/** @type {import('vue').Ref<string>} */
const imageUploadError = ref('')
/** @type {import('vue').Ref<boolean>} */
const isUploadingImage = ref(false)
/** @type {import('vue').Ref<string>} */
const newAltText = ref('')

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

/** @type {import('vue').Ref<Object|null>} */
const selectedWarehouse = ref(null)
/** @type {import('vue').Ref<string>} */
const warehouseError = ref('')

/** @type {import('vue').Ref<boolean>} */
const showAdditionalData = ref(false)

/** @type {import('vue').Reactive<Object>} */
const productFieldsVisibility = reactive({})
/** @type {import('vue').Ref<Array>} */
const customFields = ref([])
/** @type {import('vue').Reactive<Object>} */
const customFieldValues = reactive({})

/** @type {import('vue').Ref<boolean>} */
const loadingProductFields = ref(true)

// Список стандартных необязательных полей products_sklad
const standardProductFields = [
  { key: 'description', label: t('ProductCreatePage_47') }, // Описание
  { key: 'country', label: t('ProductCreatePage_48') }, // Страна
  { key: 'supplier', label: t('ProductCreatePage_49') }, // Поставщик
  { key: 'article', label: t('ProductCreatePage_50') }, // Артикул
  { key: 'code', label: t('ProductCreatePage_51') }, // Код
  { key: 'external_code', label: t('ProductCreatePage_52') }, // Внешний код
  { key: 'weight', label: t('ProductCreatePage_53') }, // Вес
  { key: 'volume', label: t('ProductCreatePage_54') }, // Объем
  { key: 'vat', label: t('ProductCreatePage_55') }, // Ставка НДС
  { key: 'min_stock', label: t('ProductCreatePage_56') }, // Минимальный остаток
  { key: 'stock_type', label: t('ProductCreatePage_57') }, // Тип запаса
  { key: 'packing', label: t('ProductCreatePage_58') }, // Упаковка
  { key: 'accounting_type', label: t('ProductCreatePage_59') }, // Тип учета
  { key: 'traceable', label: t('ProductCreatePage_60') }, // Маркируемый
  { key: 'marking', label: t('ProductCreatePage_61') }, // Маркировка
  { key: 'product_type', label: t('ProductCreatePage_62') }, // Тип товара
  { key: 'barcode_type', label: t('ProductCreatePage_63') }, // Тип штрихкода
  { key: 'barcode', label: t('ProductCreatePage_64') }, // Штрихкод
  { key: 'cash_register_tax', label: t('ProductCreatePage_65') }, // Налог ККМ
  { key: 'cash_register_type', label: t('ProductCreatePage_66') }, // Тип ККМ
  { key: 'price', label: t('ProductCreatePage_67') }, // Цена
]

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
      // Инициализация значений
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

// Функция для получения опций списка из поля
function getListOptions(field) {
  if (field.field_type === 'list' && field.list_options) {
    try {
      return typeof field.list_options === 'string' 
        ? JSON.parse(field.list_options) 
        : field.list_options
    } catch (e) {
      console.error('Ошибка парсинга опций списка:', e)
      return []
    }
  }
  return []
}

// Функция для получения опций списка в формате для Multiselect
function getListOptionsForMultiselect(field) {
  const options = getListOptions(field)
  return options.map(option => ({
    label: option,
    value: option
  }))
}

async function handleNameBlur() {
  if (product.name && !productId.value && !isSavingDraft.value) {
    isSavingDraft.value = true
    draftError.value = ''
    try {
      const response = await apiRequest('/products/draft', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ name: product.name })
      })
      if (response.ok && response.data.id) {
        productId.value = response.data.id
        // Небольшая задержка для лучшего UX
        setTimeout(() => {
          // Можно добавить уведомление об успешном создании черновика
        }, 500)
      } else {
        draftError.value = t('ProductCreatePage_74') // Ошибка создания черновика товара
      }
    } catch (e) {
      draftError.value = t('ProductCreatePage_74') // Ошибка создания черновика товара
    } finally {
      isSavingDraft.value = false
    }
  }
}

async function fetchImages() {
  if (!productId.value) return
  try {
    const response = await apiRequest(`/products/${productId.value}/images`)
    if (response.ok && Array.isArray(response.data.images)) {
      images.value = response.data.images
    }
  } catch (e) {}
}

watch(productId, (id) => {
  if (id) fetchImages()
})

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
    
    console.log('Uploading file:', file.name, 'Size:', file.size)
    console.log('FormData entries:')
    for (let [key, value] of formData.entries()) {
      console.log(key, value)
    }
    
    const response = await apiRequest(`/products/${productId.value}/images`, {
      method: 'POST',
      headers: {}, // Убираем Content-Type для FormData
      body: formData
    })
    
    console.log('Upload response:', response)
    
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
  console.log('handleDeleteImage called in ProductCreatePage with ID:', imgId)
  console.log('Images array before deletion:', images.value.map(img => img.id))
  
  // Удаляем изображение из массива (API запрос уже выполнен в ImageDropzone)
  images.value = images.value.filter(img => img.id !== imgId)
  
  console.log('Images array after deletion:', images.value.map(img => img.id))
  console.log('Image removed from parent component array:', imgId)
}



function goToCreateWarehouse() {
  router.push('/warehouses/create')
}

onMounted(async () => {
  loadingCategories.value = true
  try {
    // Загружаем категории в зависимости от настроек пользователя
    categories.value = await getCategoriesByUserSettings()
  } catch (e) {
    categoryError.value = t('ProductCreatePage_79') // Ошибка загрузки категорий
  } finally {
    loadingCategories.value = false
  }
  
  // Проверяем наличие складов и показываем модальное окно если их нет
  await checkWarehousesAndShowModal()
  await loadProductFieldsVisibilityAndCustomFields()
  
  // Добавляем обработчики для предотвращения случайного закрытия
  window.addEventListener('beforeunload', handleBeforeUnload)
  
  // Добавляем обработчик для навигации внутри приложения
  router.beforeEach(handleBeforeRouteLeave)
})

onBeforeUnmount(() => {
  // Удаляем обработчики при размонтировании компонента
  window.removeEventListener('beforeunload', handleBeforeUnload)
  // Удаляем обработчик маршрутов
  router.beforeEach(() => {}) // Очищаем обработчик
})

// Обработчик для навигации внутри приложения
function handleBeforeRouteLeave(to, from, next) {
  if (hasUnsavedChanges.value) {
    showCloseModal.value = true
    next(false)
  } else {
    next()
  }
}

watch(selectedCategory, async (cat) => {
  selectedSubcategory.value = null
  product.category = cat ? cat.value : ''
  subcategories.value = []
  if (cat && cat.value) {
    loadingSubcategories.value = true
    try {
      // Получаем правильный endpoint для подкатегорий
      const endpoint = getSubcategoriesApiEndpoint(cat.value)
      const response = await apiRequest(endpoint)
      if (response.ok && response.data.success) {
        subcategories.value = response.data.data || []
      } else {
        subcategoryError.value = t('ProductCreatePage_80') // Ошибка загрузки подкатегорий
      }
    } catch (e) {
      subcategoryError.value = t('ProductCreatePage_80') // Ошибка загрузки подкатегорий
    } finally {
      loadingSubcategories.value = false
    }
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
  if (product.name) hasUnsavedChanges.value = true
})

watch(() => product.description, () => {
  if (product.description) hasUnsavedChanges.value = true
})

watch(() => product.category, () => {
  if (product.category) hasUnsavedChanges.value = true
})

watch(() => product.subcategory, () => {
  if (product.subcategory) hasUnsavedChanges.value = true
})

watch(() => product.country, () => {
  if (product.country && product.country.value) hasUnsavedChanges.value = true
})

watch(() => product.supplier, () => {
  if (product.supplier) hasUnsavedChanges.value = true
})

watch(() => product.article, () => {
  if (product.article) hasUnsavedChanges.value = true
})

watch(() => product.code, () => {
  if (product.code) hasUnsavedChanges.value = true
})

watch(() => product.external_code, () => {
  if (product.external_code) hasUnsavedChanges.value = true
})

watch(() => product.unit, () => {
  if (product.unit && product.unit.value) hasUnsavedChanges.value = true
})

watch(() => product.weight, () => {
  if (product.weight) hasUnsavedChanges.value = true
})

watch(() => product.volume, () => {
  if (product.volume) hasUnsavedChanges.value = true
})

watch(() => product.vat, () => {
  if (product.vat) hasUnsavedChanges.value = true
})

watch(() => product.packing, () => {
  if (product.packing && product.packing.value) hasUnsavedChanges.value = true
})

watch(() => product.accounting_type, () => {
  if (product.accounting_type && product.accounting_type.value) hasUnsavedChanges.value = true
})

watch(() => product.product_type, () => {
  if (product.product_type && product.product_type.value) hasUnsavedChanges.value = true
})

watch(() => product.barcode, () => {
  if (product.barcode) hasUnsavedChanges.value = true
})

watch(() => product.barcode_type, () => {
  if (product.barcode_type && product.barcode_type.value) hasUnsavedChanges.value = true
})

watch(() => product.cash_register_tax, () => {
  if (product.cash_register_tax) hasUnsavedChanges.value = true
})

watch(() => product.cash_register_type, () => {
  if (product.cash_register_type) hasUnsavedChanges.value = true
})

watch(() => product.start_count, () => {
  if (product.start_count) hasUnsavedChanges.value = true
})

watch(() => product.warehouse, () => {
  if (product.warehouse) hasUnsavedChanges.value = true
})

watch(() => product.price, () => {
  if (product.price) hasUnsavedChanges.value = true
})

// Отслеживаем изменения в изображениях
watch(images, () => {
  if (images.value.length > 0) hasUnsavedChanges.value = true
}, { deep: true })

const categoryOptions = createReactiveCategoryOptions(categories)
const subcategoryOptions = createReactiveSubcategoryOptions(subcategories)



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

function closeModalAndGo() {
  showCloseModal.value = false
  hasUnsavedChanges.value = false
  router.push('/products/balances')
}

function handleBeforeUnload(event) {
  if (hasUnsavedChanges.value) {
    event.preventDefault()
    event.returnValue = t('ProductCreatePage_81') // У вас есть несохраненные изменения. Вы уверены, что хотите покинуть страницу?
    return t('ProductCreatePage_81') // У вас есть несохраненные изменения. Вы уверены, что хотите покинуть страницу?
  }
}

async function handleSave() {
  if (!productId.value) {
    toastr.error(t('ProductCreatePage_75')) // Сначала создайте черновик товара, указав название
    return
  }

  isSavingProduct.value = true

  try {
    // Подготавливаем данные для отправки
    const productData = {
      id: productId.value,
      name: product.name,
      description: product.description,
      country: product.country ? product.country.value : null,
      supplier: product.supplier,
      article: product.article,
      code: product.code,
      external_code: product.external_code,
      unit: product.unit ? product.unit.value : null,
      weight: product.weight,
      volume: product.volume,
      vat: product.vat,
      packing: product.packing ? product.packing.value : null,
      accounting_type: product.accounting_type ? product.accounting_type.value : null,
      product_type: product.product_type ? product.product_type.value : null,
      barcode_type: product.barcode_type ? product.barcode_type.value : null,
      barcode: product.barcode,
      cash_register_tax: product.cash_register_tax,
      cash_register_type: product.cash_register_type,
      start_count: product.start_count,
      warehouse_id: selectedWarehouse.value?.value || product.warehouse,
      price: product.price,
      fields: { ...customFieldValues },
      is_creation: true // Указываем, что это создание товара
    }

    // Добавляем категории только если они включены
    if (areCategoriesEnabled()) {
      productData.category_id = product.category
      productData.subcategory_id = product.subcategory
    }

    // Отправляем запрос на сохранение
    const response = await apiRequest(`/products/${productId.value}`, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(productData)
    })

    if (response.ok) {
      hasUnsavedChanges.value = false
      toastr.success(t('ProductCreatePage_76')) // Товар успешно сохранен
      console.log('Сохраненные данные:', productData)
      // Перенаправляем на страницу Остатки
      router.push('/products/balances')
    } else {
      toastr.error(t('ProductCreatePage_77') + ' ' + (response.error || t('ProductCreatePage_78'))) // Ошибка при сохранении товара: Неизвестная ошибка
    }
  } catch (error) {
    console.error('Ошибка при сохранении товара:', error)
    toastr.error(t('ProductCreatePage_77') + ' ' + error.message) // Ошибка при сохранении товара:
  } finally {
    isSavingProduct.value = false
  }
}

function onCategoryOpen() {
  categoryPlaceholder.value = ''
}
function onCategoryClose() {
  categoryPlaceholder.value = t('ProductCreatePage_10') // Выберите категорию
}
function onSubcategoryOpen() {
  subcategoryPlaceholder.value = ''
}
function onSubcategoryClose() {
  subcategoryPlaceholder.value = t('ProductCreatePage_14') // Выберите подкатегорию
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