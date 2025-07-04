<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-sm">
    <div v-if="loadingProduct" class="flex items-center justify-center py-20">
      <div class="text-center">
        <svg class="animate-spin h-8 w-8 text-blue-500 mx-auto mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
        </svg>
        <p class="text-gray-600">Загрузка товара...</p>
      </div>
    </div>

    <div v-else-if="productError" class="text-center py-20">
      <p class="text-red-500 mb-4">{{ productError }}</p>
      <button @click="loadProduct" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
        Попробовать снова
      </button>
    </div>

    <div v-else>
      <!-- Наименование и кнопки -->
      <div class="mb-6 w-full" style="position: sticky;top: 62px;background: #fff;z-index: 99;padding: 10px 0;">
        <div class="flex flex-col gap-3 sm:inline-flex sm:flex-row sm:items-center w-full">
          <input v-model="product.name" @blur="handleNameBlur" type="text" class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm bg-white" placeholder="* Наименование товара" />
          <div class="flex gap-2 mt-3 sm:mt-0">
            <button @click="handleSave" :disabled="!product.name || isSavingProduct" class="bg-lime-500 hover:bg-lime-600 text-white font-semibold px-6 py-2 rounded-lg shadow transition text-sm disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
              <svg v-if="isSavingProduct" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
              </svg>
              {{ isSavingProduct ? 'Сохранение...' : 'Сохранить' }}
            </button>
            <button @click="showCloseModal = true" class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold px-6 py-2 rounded-lg border shadow transition text-sm">Закрыть</button>
          </div>
        </div>
        <div v-if="draftError" class="text-red-500 text-xs mt-2">{{ draftError }}</div>
      </div>

      <!-- Область загрузки изображений -->
      <div class="w-full mb-6">
        <div class="bg-gray-50 rounded-xl p-4 shadow-sm">
          <div class="font-semibold mb-2">Изображения</div>
          <ImageDropzone :product-id="productId" :images="images" :disabled="!product.name" @uploaded="onImageUploaded" @deleted="handleDeleteImage" />
          <div v-if="imageUploadError" class="text-red-500 text-xs mt-2">{{ imageUploadError }}</div>
        </div>
      </div>

      <!-- Блоки с данными о товаре -->
      <div class="w-full flex flex-col gap-6">
        <!-- Общие данные -->
        <div class="bg-gray-50 rounded-xl p-4 shadow-sm">
          <div class="font-semibold mb-2">Общие данные</div>
          <div class="flex flex-col gap-3">
            <div>
              <label class="block text-xs text-gray-700 mb-1">Описание</label>
              <textarea v-model="product.description" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm"></textarea>
            </div>
            <!-- Категория и подкатегория -->
            <div class="flex flex-col gap-2 w-full">
              <div class="w-full">
                <label class="block text-xs text-gray-700 mb-1">Категория</label>
                <template v-if="loadingCategories">
                  <div class="w-full h-10 flex items-center justify-center bg-white border border-gray-200 rounded-lg">
                    <svg class="animate-spin h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                    </svg>
                    <span class="ml-2 text-xs text-gray-500">Загрузка категорий...</span>
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
                    class="w-full text-xs multiselect-custom"
                    @open="onCategoryOpen"
                    @close="onCategoryClose"
                  />
                </template>
              </div>
              <div class="w-full">
                <label class="block text-xs text-gray-700 mb-1">Подкатегория</label>
                <template v-if="loadingSubcategories">
                  <div class="w-full h-10 flex items-center justify-center bg-white border border-gray-200 rounded-lg">
                    <svg class="animate-spin h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                    </svg>
                    <span class="ml-2 text-xs text-gray-500">Загрузка подкатегорий...</span>
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
                    class="w-full text-xs multiselect-custom"
                    @open="onSubcategoryOpen"
                    @close="onSubcategoryClose"
                  />
                </template>
              </div>
            </div>
            <div class="flex gap-2">
              <div class="flex-1">
                <label class="block text-xs text-gray-700 mb-1">Страна</label>
                <Multiselect
                  v-model="product.country"
                  :options="countries"
                  label="label"
                  value="value"
                  :object="true"
                  placeholder="Выберите страну"
                  searchable
                  :search-placeholder="'Поиск страны'"
                  :max-height="400"
                  class="w-full text-xs multiselect-custom"
                />
              </div>
              <div class="flex-1">
                <label class="block text-xs text-gray-700 mb-1">Поставщик</label>
                <input v-model="product.supplier" type="text" placeholder="Поставщик" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm" />
              </div>
            </div>
            <div class="flex gap-2 relative">
              <div class="flex-1 relative">
                <label class="block text-xs text-gray-700 mb-1 flex items-center gap-1 relative">
                  Артикул
                  <span @mouseenter="showTooltip.article = true" @mouseleave="showTooltip.article = false" class="text-blue-400 cursor-pointer relative">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 20a8 8 0 100-16 8 8 0 000 16z" /></svg>
                    <span v-if="showTooltip.article" class="absolute left-0 top-full z-10 mt-2 max-w-xs w-max rounded bg-gray-900 text-white text-xs px-3 py-2 shadow-lg transition-opacity duration-200 whitespace-pre-line">
                      <span class="absolute -top-2 left-4 w-3 h-3 bg-gray-900 rotate-45"></span>
                      Назначенный производителем идентификатор товара.
                    </span>
                  </span>
                </label>
                <input v-model="product.article" type="text" placeholder="Артикул" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm" />
              </div>
              <div class="flex-1 relative">
                <label class="block text-xs text-gray-700 mb-1 flex items-center gap-1 relative">
                  Код
                  <span @mouseenter="showTooltip.code = true" @mouseleave="showTooltip.code = false" class="text-blue-400 cursor-pointer relative">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 20a8 8 0 100-16 8 8 0 000 16z" /></svg>
                    <span v-if="showTooltip.code" class="absolute left-0 top-full z-10 mt-2 max-w-xs w-max rounded bg-gray-900 text-white text-xs px-3 py-2 shadow-lg transition-opacity duration-200 whitespace-pre-line">
                      <span class="absolute -top-2 left-4 w-3 h-3 bg-gray-900 rotate-45"></span>
                      Внутренний код товара в вашей системе.
                    </span>
                  </span>
                </label>
                <input v-model="product.code" type="text" placeholder="Код" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm" />
              </div>
              <div class="flex-1">
                <label class="block text-xs text-gray-700 mb-1">Внешний код</label>
                <input v-model="product.external_code" type="text" placeholder="Внешний код" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm" />
              </div>
            </div>
            <div class="flex gap-2">
              <div class="flex-1">
                <label class="block text-xs text-gray-700 mb-1">Ед-ца измерения</label>
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
                  class="w-full text-xs multiselect-custom"
                />
              </div>
              <div class="flex-1">
                <label class="block text-xs text-gray-700 mb-1">Вес</label>
                <input v-model="product.weight" type="number" placeholder="0" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm" />
              </div>
              <div class="flex-1">
                <label class="block text-xs text-gray-700 mb-1">Объем</label>
                <input v-model="product.volume" type="number" placeholder="0" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm" />
              </div>
            </div>
            <div>
              <label class="block text-xs text-gray-700 mb-1">НДС</label>
              <input v-model="product.vat" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm" />
            </div>
          </div>
        </div>

        <!-- Особенности учета -->
        <div class="bg-gray-50 rounded-xl p-4 shadow-sm">
          <div class="font-semibold mb-2">Особенности учета</div>
          <div class="flex flex-col gap-2">
            <div class="flex gap-2">
              <div class="flex-1">
                <label class="block text-xs text-gray-700 mb-1">Фасовка</label>
                <Multiselect
                  v-model="product.packing"
                  :options="[
                    { label: 'Штучная', value: 'Штучная' },
                    { label: 'Весовая', value: 'Весовая' },
                    { label: 'Разливная', value: 'Разливная' }
                  ]"
                  label="label"
                  value="value"
                  :object="true"
                  placeholder="Выберите фасовку"
                  :max-height="400"
                  class="w-full text-xs multiselect-custom"
                />
              </div>
              <div class="flex-1">
                <label class="block text-xs text-gray-700 mb-1">Тип учета</label>
                <Multiselect
                  v-model="product.accounting_type"
                  :options="[
                    { label: 'Без специализированного учета', value: 'Без специализированного учета' },
                    { label: 'Алкогольный товар', value: 'Алкогольный товар' },
                    { label: 'Учет по серийным номерам', value: 'Учет по серийным номерам' },
                    { label: 'СИЗ', value: 'Средство индивидуальной защиты' }
                  ]"
                  label="label"
                  value="value"
                  :object="true"
                  placeholder="Выберите тип учета"
                  :max-height="400"
                  class="w-full text-xs multiselect-custom"
                />
              </div>
            </div>

            <div>
              <label class="block text-xs text-gray-700 mb-1">Тип продукции</label>
              <Multiselect
                v-model="product.product_type"
                :options="[
                  { label: 'Не маркируется', value: 'Не маркируется' },
                  { label: 'Табачная продукция', value: 'Табачная продукция' },
                  { label: 'Обувь', value: 'Обувь' },
                  { label: 'Одежда', value: 'Одежда' },
                  { label: 'Постельное белье', value: 'Постельное белье' },
                  { label: 'Духи и туалетная вода', value: 'Духи и туалетная вода' },
                  { label: 'Фотокамеры и лампы-вспышки', value: 'Фотокамеры и лампы-вспышки' },
                  { label: 'Шины и покрышки', value: 'Шины и покрышки' },
                  { label: 'Молочная продукция', value: 'Молочная продукция' },
                  { label: 'Упакованная вода', value: 'Упакованная вода' },
                  { label: 'Альтернативная табачная продукция', value: 'Альтернативная табачная продукция' },
                  { label: 'Никотиносодержащая продукция', value: 'Никотиносодержащая продукция' },
                  { label: 'Биологически активные добавки к пище', value: 'Биологически активные добавки к пище' },
                  { label: 'Антисептики', value: 'Антисептики' },
                  { label: 'Медизделия и кресла-коляски', value: 'Медизделия и кресла-коляски' },
                  { label: 'Безалкогольные напитки', value: 'Безалкогольные напитки' },
                  { label: 'Ветеринарные препараты', value: 'Ветеринарные препараты' },
                  { label: 'Икра и морепродукты', value: 'Икра и морепродукты' }
                ]"
                label="label"
                value="value"
                :object="true"
                placeholder="Выберите тип продукции"
                :max-height="400"
                class="w-full text-xs multiselect-custom"
              />
            </div>

            <div class="flex gap-2">
              <div class="flex-1">
                <label class="block text-xs text-gray-700 mb-1">Тип штрихкода</label>
                <Multiselect
                  v-model="product.barcode_type"
                  :options="[
                    { label: 'EAN8', value: 'EAN8' },
                    { label: 'EAN13', value: 'EAN13' },
                    { label: 'Code128', value: 'Code128' },
                    { label: 'Code39', value: 'Code39' },
                    { label: 'QR', value: 'QR' }
                  ]"
                  label="label"
                  value="value"
                  :object="true"
                  placeholder="Выберите тип штрихкода"
                  :max-height="400"
                  class="w-full text-xs multiselect-custom"
                />
              </div>
              <div class="flex-1">
                <label class="block text-xs text-gray-700 mb-1">Штрихкод</label>
                <input v-model="product.barcode" type="text" placeholder="Штрихкод" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm" />
              </div>
            </div>

            <div class="flex gap-2">
              <div class="flex-1">
                <label class="block text-xs text-gray-700 mb-1">Налог ККТ</label>
                <Multiselect
                  v-model="product.cash_register_tax"
                  :options="[
                    { label: 'ОСН', value: 'ОСН' },
                    { label: 'УСН', value: 'УСН' },
                    { label: 'ЕНВД', value: 'ЕНВД' },
                    { label: 'ПСН', value: 'ПСН' }
                  ]"
                  label="label"
                  value="value"
                  :object="true"
                  placeholder="Выберите налог ККТ"
                  :max-height="400"
                  class="w-full text-xs multiselect-custom"
                />
              </div>
              <div class="flex-1">
                <label class="block text-xs text-gray-700 mb-1">Тип ККТ</label>
                <Multiselect
                  v-model="product.cash_register_type"
                  :options="[
                    { label: 'Товар', value: 'Товар' },
                    { label: 'Услуга', value: 'Услуга' },
                    { label: 'Работа', value: 'Работа' }
                  ]"
                  label="label"
                  value="value"
                  :object="true"
                  placeholder="Выберите тип ККТ"
                  :max-height="400"
                  class="w-full text-xs multiselect-custom"
                />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Модальное окно закрытия -->
    <div v-if="showCloseModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-semibold mb-4">Закрыть редактирование?</h3>
        <p class="text-gray-600 mb-6">У вас есть несохраненные изменения. Вы уверены, что хотите закрыть страницу?</p>
        <div class="flex gap-3">
          <button @click="closeModalAndGo" class="flex-1 bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg font-medium">Закрыть</button>
          <button @click="showCloseModal = false" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg font-medium">Отмена</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, watch, onMounted, onBeforeUnmount } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import Multiselect from 'vue-multiselect'
import ImageDropzone from './ImageDropzone.vue'
import { apiRequest } from '@/config/api'
import countriesData from '@/data/countries.json'
import toastr from 'toastr'

const route = useRoute()
const router = useRouter()

// Получаем ID товара из URL
const productId = ref(parseInt(route.params.id))

// Состояние загрузки
const loadingProduct = ref(true)
const loadingCategories = ref(false)
const loadingSubcategories = ref(false)
const isSavingProduct = ref(false)

// Ошибки
const productError = ref('')
const categoryError = ref('')
const subcategoryError = ref('')
const imageUploadError = ref('')

// Данные
const categories = ref([])
const subcategories = ref([])
const images = ref([])

// Плейсхолдеры
const categoryPlaceholder = ref('Выберите категорию')
const categorySearchPlaceholder = ref('Поиск категории')
const subcategoryPlaceholder = ref('Выберите подкатегорию')
const subcategorySearchPlaceholder = ref('Поиск подкатегории')

// Выбранные значения
const selectedCategory = ref(null)
const selectedSubcategory = ref(null)

// Модальное окно
const showCloseModal = ref(false)
const hasUnsavedChanges = ref(false)

// Данные товара
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
  cash_register_type: ''
})

// Загрузка данных товара
async function loadProduct() {
  if (!productId.value) {
    productError.value = 'ID товара не указан'
    return
  }

  loadingProduct.value = true
  try {
    const response = await apiRequest(`/products/${productId.value}`)
    if (response.ok) {
      const productData = response.data.data
      
      // Заполняем данные товара
      product.name = productData.name || ''
      product.description = productData.description || ''
      product.category = productData.category || ''
      product.subcategory = productData.subcategory || ''
      product.country = productData.country ? { label: getCountryName(productData.country), value: parseInt(productData.country) } : null
      product.supplier = productData.supplier || ''
      product.article = productData.article || ''
      product.code = productData.code || ''
      product.external_code = productData.external_code || ''
      product.unit = productData.unit ? { label: productData.unit, value: productData.unit } : null
      product.weight = productData.weight
      product.volume = productData.volume
      product.vat = productData.vat || ''
      product.packing = productData.packing ? { label: productData.packing, value: productData.packing } : null
      product.accounting_type = productData.accounting_type ? { label: productData.accounting_type, value: productData.accounting_type } : null
      product.product_type = productData.product_type ? { label: productData.product_type, value: productData.product_type } : null
      product.barcode_type = productData.barcode_type ? { label: productData.barcode_type, value: productData.barcode_type } : null
      product.barcode = productData.barcode || ''
      product.cash_register_tax = productData.cash_register_tax || ''
      product.cash_register_type = productData.cash_register_type || ''

      // Загружаем изображения
      if (productData.images) {
        images.value = productData.images
      }

      // Устанавливаем категорию и подкатегорию
      if (productData.category) {
        await loadCategories()
        selectedCategory.value = { label: getCategoryName(productData.category), value: productData.category }
        if (productData.subcategory) {
          await loadSubcategories(productData.category)
          selectedSubcategory.value = { label: getSubcategoryName(productData.subcategory), value: productData.subcategory }
        }
      }
    } else {
      productError.value = response.data.message || 'Ошибка загрузки товара'
    }
  } catch (error) {
    console.error('Ошибка загрузки товара:', error)
    productError.value = 'Ошибка загрузки товара'
  } finally {
    loadingProduct.value = false
  }
}

// Получение названия страны по ID
function getCountryName(countryId) {
  const country = countriesData.find(c => c.id === parseInt(countryId))
  return country ? country.name : 'Неизвестная страна'
}

// Получение названия категории
function getCategoryName(categoryId) {
  const category = categories.value.find(c => c.category_id === categoryId)
  return category ? (category.name_ru || category.name) : categoryId
}

// Получение названия подкатегории
function getSubcategoryName(subcategoryId) {
  const subcategory = subcategories.value.find(s => s.subcategory_id === subcategoryId)
  return subcategory ? (subcategory.name_ru || subcategory.name) : subcategoryId
}

// Загрузка категорий
async function loadCategories() {
  loadingCategories.value = true
  try {
    const response = await apiRequest('/categories')
    categories.value = response.data
  } catch (e) {
    categoryError.value = 'Ошибка загрузки категорий'
  } finally {
    loadingCategories.value = false
  }
}

// Загрузка подкатегорий
async function loadSubcategories(categoryId) {
  loadingSubcategories.value = true
  try {
    const response = await apiRequest(`/subcategories?category_id=${encodeURIComponent(categoryId)}`)
    subcategories.value = response.data
  } catch (e) {
    subcategoryError.value = 'Ошибка загрузки подкатегорий'
  } finally {
    loadingSubcategories.value = false
  }
}

// Обработчики
async function handleNameBlur() {
  // В режиме редактирования просто отмечаем изменения
  hasUnsavedChanges.value = true
}

async function handleSave() {
  isSavingProduct.value = true

  try {
    const productData = {
      id: productId.value,
      name: product.name,
      description: product.description,
      category: product.category,
      subcategory: product.subcategory,
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
      cash_register_type: product.cash_register_type
    }

    const response = await apiRequest(`/products/${productId.value}`, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(productData)
    })

    if (response.ok) {
      hasUnsavedChanges.value = false
      toastr.success('Товар успешно сохранен')
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

// Обработчики изображений
const onImageUploaded = (img) => {
  images.value.push(img)
}

async function handleDeleteImage(imgId) {
  try {
    const response = await apiRequest(`/products/images/${imgId}`, { method: 'DELETE' })
    if (response.ok) {
      images.value = images.value.filter(img => img.id !== imgId)
    }
  } catch (e) {}
}

// Watch функции
watch(selectedCategory, async (cat) => {
  selectedSubcategory.value = null
  product.category = cat ? cat.value : ''
  subcategories.value = []
  if (cat && cat.value) {
    loadingSubcategories.value = true
    try {
      const response = await apiRequest(`/subcategories?category_id=${encodeURIComponent(cat.value)}`)
      subcategories.value = response.data
    } catch (e) {
      subcategoryError.value = 'Ошибка загрузки подкатегорий'
    } finally {
      loadingSubcategories.value = false
    }
  }
})

watch(selectedSubcategory, (subcat) => {
  product.subcategory = subcat ? subcat.value : ''
})

// Отслеживаем изменения в форме
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

// Отслеживаем изменения в изображениях
watch(images, () => {
  if (images.value.length > 0) hasUnsavedChanges.value = true
}, { deep: true })

// Computed свойства
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
  subcategories.value.map(s => ({
    label: s.name_ru || s.name,
    value: s.subcategory_id,
    raw: s
  }))
)

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

// Функции
function closeModalAndGo() {
  showCloseModal.value = false
  hasUnsavedChanges.value = false
  router.push('/products')
}

function handleBeforeUnload(event) {
  if (hasUnsavedChanges.value) {
    event.preventDefault()
    event.returnValue = 'У вас есть несохраненные изменения. Вы уверены, что хотите покинуть страницу?'
    return 'У вас есть несохраненные изменения. Вы уверены, что хотите покинуть страницу?'
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

// Обработчик для навигации внутри приложения
function handleBeforeRouteLeave(to, from, next) {
  if (hasUnsavedChanges.value) {
    showCloseModal.value = true
    next(false)
  } else {
    next()
  }
}

// Lifecycle
onMounted(async () => {
  await loadProduct()
  
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