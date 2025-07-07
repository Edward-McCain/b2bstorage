<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-sm">
    <!-- Tailwind Notification -->
    <transition name="fade">
      <div v-if="notification.show" :class="['fixed top-6 right-6 z-50 px-4 py-3 rounded-lg shadow-lg text-sm font-medium', notification.type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white']">
        {{ notification.message }}
      </div>
    </transition>

    <div v-if="loadingProduct" class="flex items-center justify-center py-20">
      <div class="text-center">
        <Loader2 class="animate-spin h-8 w-8 text-blue-500 mx-auto mb-4" />
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
          <div class="flex-1">
            <div v-if="loadingProduct" class="h-10 bg-gray-200 rounded w-full animate-pulse"></div>
            <input v-else v-model="product.name" type="text" class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm bg-white w-full" placeholder="* Наименование товара" />
          </div>
          <div class="flex gap-2 mt-3 sm:mt-0">
            <div v-if="loadingProduct" class="h-10 w-32 bg-gray-200 rounded animate-pulse"></div>
            <button v-else @click="handleSave" :disabled="!product.name || isSavingProduct" class="bg-lime-500 hover:bg-lime-600 text-white font-semibold px-6 py-2 rounded-lg shadow transition text-sm disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
              <Loader2 v-if="isSavingProduct" class="animate-spin h-4 w-4" />
              {{ isSavingProduct ? 'Сохранение...' : 'Сохранить' }}
            </button>
            <!-- <button @click="showCloseModal = true" class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold px-6 py-2 rounded-lg border shadow transition text-sm">Закрыть</button> -->
          </div>
        </div>
        <div v-if="saveError" class="text-red-500 text-xs mt-2">{{ saveError }}</div>
      </div>

      <!-- Область загрузки изображений -->
      <div class="w-full mb-6">
        <div class="bg-gray-50 rounded-xl p-4 shadow-sm">
          <div class="font-semibold mb-2">Изображения</div>
          <div v-if="loadingProduct" class="h-32 bg-gray-200 rounded animate-pulse"></div>
          <ImageDropzone v-else :product-id="productId" :images="images" :disabled="!product.name" @uploaded="onImageUploaded" @deleted="handleDeleteImage" />
          <div v-if="imageUploadError" class="text-red-500 text-xs mt-2">{{ imageUploadError }}</div>
        </div>
      </div>

      <!-- Существующие изображения -->
      <div v-if="existingImages.length > 0 || loadingProduct" class="w-full mb-6">
        <div class="bg-gray-50 rounded-xl p-4 shadow-sm">
          <div class="font-semibold mb-4">Существующие изображения</div>
          <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
            <template v-if="loadingProduct">
              <div v-for="n in 2" :key="n" class="h-24 bg-gray-200 rounded-lg animate-pulse"></div>
            </template>
            <template v-else>
              <div v-for="image in existingImages" :key="image.id" class="relative group">
                <img 
                  :src="image.image_url" 
                  :alt="image.alt_text || product.name"
                  class="w-full h-24 object-cover rounded-lg border border-gray-200"
                />
                <!-- Оверлей прелоадера -->
                <div v-if="deletingImageIds.includes(image.id)" class="absolute inset-0 bg-white bg-opacity-70 flex items-center justify-center z-10 rounded-lg">
                  <Loader2 class="animate-spin w-6 h-6 text-blue-500" />
                </div>
                <button 
                  @click="deleteExistingImage(image.id)"
                  class="absolute top-1 right-1 bg-red-500 hover:bg-red-600 text-white p-1 rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-200"
                  title="Удалить"
                >
                  <X class="w-3 h-3" />
                </button>
              </div>
            </template>
          </div>
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
              <div v-if="loadingProduct" class="h-20 bg-gray-200 rounded w-full animate-pulse"></div>
              <textarea v-else v-model="product.description" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm"></textarea>
            </div>
            <!-- Категория и подкатегория -->
            <div class="flex flex-col gap-2 w-full">
              <div class="w-full">
                <label class="block text-xs text-gray-700 mb-1">Категория</label>
                <div v-if="loadingProduct" class="h-10 bg-gray-200 rounded w-full animate-pulse"></div>
                <template v-else>
                  <Multiselect
                    v-model="selectedCategory"
                    :options="categoryOptions"
                    label="label"
                    value="value"
                    track-by="value"
                    :object="true"
                    placeholder="Выберите категорию"
                    searchable
                    :search-placeholder="'Поиск категории'"
                    :max-height="400"
                    class="w-full text-xs multiselect-custom"
                    @open="onCategoryOpen"
                    @close="onCategoryClose"
                  />
                </template>
              </div>
              <div class="w-full">
                <label class="block text-xs text-gray-700 mb-1">Подкатегория</label>
                <div v-if="loadingProduct" class="h-10 bg-gray-200 rounded w-full animate-pulse"></div>
                <template v-else>
                  <Multiselect
                    v-model="selectedSubcategory"
                    :options="subcategoryOptions"
                    label="label"
                    value="value"
                    track-by="value"
                    :object="true"
                    placeholder="Выберите подкатегорию"
                    :search-placeholder="'Поиск подкатегории'"
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
                <div v-if="loadingProduct" class="h-10 bg-gray-200 rounded w-full animate-pulse"></div>
                <Multiselect v-else
                  v-model="selectedCountry"
                  :options="countries"
                  label="label"
                  value="value"
                  track-by="value"
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
                <div v-if="loadingProduct" class="h-10 bg-gray-200 rounded w-full animate-pulse"></div>
                <input v-else v-model="product.supplier" type="text" placeholder="Поставщик" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm" />
              </div>
            </div>
            <div class="flex gap-2 relative">
              <div class="flex-1 relative">
                <label class="block text-xs text-gray-700 mb-1 flex items-center gap-1 relative">
                  Артикул
                  <span @mouseenter="showTooltip.article = true" @mouseleave="showTooltip.article = false" class="text-blue-400 cursor-pointer relative">
                    <HelpCircle class="h-4 w-4 inline" />
                    <span v-if="showTooltip.article" class="absolute left-0 top-full z-10 mt-2 max-w-xs w-max rounded bg-gray-900 text-white text-xs px-3 py-2 shadow-lg transition-opacity duration-200 whitespace-pre-line">
                      <span class="absolute -top-2 left-4 w-3 h-3 bg-gray-900 rotate-45"></span>
                      Назначенный производителем идентификатор товара.
                    </span>
                  </span>
                </label>
                <div v-if="loadingProduct" class="h-10 bg-gray-200 rounded w-full animate-pulse"></div>
                <input v-else v-model="product.article" type="text" placeholder="Артикул" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm" />
              </div>
              <div class="flex-1 relative">
                <label class="block text-xs text-gray-700 mb-1 flex items-center gap-1 relative">
                  Код
                  <span @mouseenter="showTooltip.code = true" @mouseleave="showTooltip.code = false" class="text-blue-400 cursor-pointer relative">
                    <HelpCircle class="h-4 w-4 inline" />
                    <span v-if="showTooltip.code" class="absolute left-0 top-full z-10 mt-2 max-w-xs w-max rounded bg-gray-900 text-white text-xs px-3 py-2 shadow-lg transition-opacity duration-200 whitespace-pre-line">
                      <span class="absolute -top-2 left-4 w-3 h-3 bg-gray-900 rotate-45"></span>
                      Внутренний код товара в вашей системе.
                    </span>
                  </span>
                </label>
                <div v-if="loadingProduct" class="h-10 bg-gray-200 rounded w-full animate-pulse"></div>
                <input v-else v-model="product.code" type="text" placeholder="Код" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm" />
              </div>
              <div class="flex-1">
                <label class="block text-xs text-gray-700 mb-1">Внешний код</label>
                <div v-if="loadingProduct" class="h-10 bg-gray-200 rounded w-full animate-pulse"></div>
                <input v-else v-model="product.external_code" type="text" placeholder="Внешний код" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm" />
              </div>
            </div>
            <div class="flex gap-2">
              <div class="flex-1">
                <label class="block text-xs text-gray-700 mb-1">Ед-ца измерения</label>
                <div v-if="loadingProduct" class="h-10 bg-gray-200 rounded w-full animate-pulse"></div>
                <Multiselect v-else
                  v-model="selectedUnit"
                  :options="unitOptions"
                  label="label"
                  value="value"
                  track-by="value"
                  :object="true"
                  placeholder="Выберите единицу измерения"
                  :max-height="400"
                  class="w-full text-xs multiselect-custom"
                />
              </div>
              <div class="flex-1">
                <label class="block text-xs text-gray-700 mb-1">Вес</label>
                <div v-if="loadingProduct" class="h-10 bg-gray-200 rounded w-full animate-pulse"></div>
                <input v-else v-model="product.weight" type="number" step="0.001" placeholder="Вес" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm" />
              </div>
              <div class="flex-1">
                <label class="block text-xs text-gray-700 mb-1">Объем</label>
                <div v-if="loadingProduct" class="h-10 bg-gray-200 rounded w-full animate-pulse"></div>
                <input v-else v-model="product.volume" type="number" step="0.001" placeholder="Объем" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm" />
              </div>
            </div>
            <div class="flex gap-2">
              <div class="flex-1">
                <label class="block text-xs text-gray-700 mb-1">Ставка НДС</label>
                <div v-if="loadingProduct" class="h-10 bg-gray-200 rounded w-full animate-pulse"></div>
                <input v-else v-model="product.vat" type="text" placeholder="Ставка НДС" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm" />
              </div>
              <div class="flex-1">
                <label class="block text-xs text-gray-700 mb-1">Фасовка</label>
                <div v-if="loadingProduct" class="h-10 bg-gray-200 rounded w-full animate-pulse"></div>
                <Multiselect v-else
                  v-model="selectedPacking"
                  :options="packingOptions"
                  label="label"
                  value="value"
                  track-by="value"
                  :object="true"
                  placeholder="Выберите фасовку"
                  :max-height="400"
                  class="w-full text-xs multiselect-custom"
                />
              </div>
              <div class="flex-1">
                <label class="block text-xs text-gray-700 mb-1">Тип учета</label>
                <div v-if="loadingProduct" class="h-10 bg-gray-200 rounded w-full animate-pulse"></div>
                <Multiselect v-else
                  v-model="selectedAccountingType"
                  :options="accountingTypeOptions"
                  label="label"
                  value="value"
                  track-by="value"
                  :object="true"
                  placeholder="Выберите тип учета"
                  :max-height="400"
                  class="w-full text-xs multiselect-custom"
                />
              </div>
            </div>
            <div class="flex gap-2">
              <div class="flex-1">
                <label class="block text-xs text-gray-700 mb-1">Маркировка</label>
                <div v-if="loadingProduct" class="h-10 bg-gray-200 rounded w-full animate-pulse"></div>
                <input v-else v-model="product.marking" type="text" placeholder="Маркировка" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm" />
              </div>
              <div class="flex-1">
                <label class="block text-xs text-gray-700 mb-1">Тип маркировки товара</label>
                <div v-if="loadingProduct" class="h-10 bg-gray-200 rounded w-full animate-pulse"></div>
                <Multiselect v-else
                  v-model="selectedProductType"
                  :options="productTypeOptions"
                  label="label"
                  value="value"
                  track-by="value"
                  :object="true"
                  placeholder="Выберите тип маркировки товара"
                  :max-height="400"
                  class="w-full text-xs multiselect-custom"
                />
              </div>
            </div>
          </div>
        </div>

        <!-- Штрихкоды товара -->
        <div class="bg-gray-50 rounded-xl p-4 shadow-sm">
          <div class="font-semibold mb-2 flex items-center gap-2">Штрихкоды товара <span class="text-blue-400 cursor-pointer text-xs">?</span></div>
          <div class="text-xs text-gray-600 mb-4">
            Укажите штрихкод товара, чтобы добавлять его в документы при помощи сканера штрихкодов. Код GTIN включает только цифры и может иметь длину 8, 12, 13 или 14 цифр. В учетных системах всегда используется 14 знаков, коды меньшей длины дополняются нулями слева.
          </div>
          <div class="flex flex-col gap-3">
            <div class="flex gap-2">
              <div class="flex-1">
                <label class="block text-xs text-gray-700 mb-1">Тип штрихкода</label>
                <Multiselect
                  v-model="selectedBarcodeType"
                  :options="barcodeTypeOptions"
                  label="label"
                  value="value"
                  track-by="value"
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
          </div>
        </div>

        <!-- Кассовый аппарат -->
        <div class="bg-gray-50 rounded-xl p-4 shadow-sm">
          <div class="font-semibold mb-2">Кассовый аппарат</div>
          <div class="flex flex-col gap-3">
            <div class="flex gap-2">
              <div class="flex-1">
                <label class="block text-xs text-gray-700 mb-1">Налог</label>
                <input v-model="product.cash_register_tax" type="text" placeholder="ОСН" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm" />
              </div>
              <div class="flex-1">
                <label class="block text-xs text-gray-700 mb-1">Тип</label>
                <input v-model="product.cash_register_type" type="text" placeholder="Товар" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm" />
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
        <p class="text-gray-600 mb-6">Все несохраненные изменения будут потеряны.</p>
        <div class="flex gap-3">
          <button @click="showCloseModal = false" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold px-4 py-2 rounded-lg">
            Отмена
          </button>
          <router-link to="/products" class="flex-1 bg-red-500 hover:bg-red-600 text-white font-semibold px-4 py-2 rounded-lg text-center">
            Закрыть
          </router-link>
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
import { apiRequest, getCategoriesWithCache } from '@/config/api'
import countriesData from '@/data/countries.json'
import toastr from 'toastr'
import { Loader2, X, HelpCircle } from 'lucide-vue-next'

const route = useRoute()
const router = useRouter()

// Получаем ID товара из URL
const productId = ref(parseInt(route.params.id))

// Состояние загрузки
const loadingProduct = ref(true)
const loadingCategories = ref(false)
const loadingSubcategories = ref(false)
const isSavingProduct = ref(false)
const isInitializing = ref(false)

// Ошибки
const productError = ref('')
const saveError = ref('')
const categoryError = ref('')
const subcategoryError = ref('')
const imageUploadError = ref('')

// Данные
const categories = ref([])
const subcategories = ref([])
const images = ref([])
const existingImages = ref([])

// Выбранные значения
const selectedCategory = ref(null)
const selectedSubcategory = ref(null)
const selectedCountry = ref(null)
const selectedUnit = ref(null)
const selectedPacking = ref(null)
const selectedAccountingType = ref(null)
const selectedProductType = ref(null)
const selectedBarcodeType = ref(null)

// Модальное окно
const showCloseModal = ref(false)

// Тултипы
const showTooltip = reactive({
  article: false,
  code: false
})

// Данные товара
const product = reactive({
  name: '',
  description: '',
  category: '',
  subcategory: '',
  country: '',
  supplier: '',
  article: '',
  code: '',
  external_code: '',
  unit: '',
  weight: null,
  volume: null,
  vat: '',
  packing: '',
  accounting_type: '',
  marking: '',
  product_type: '',
  barcode_type: '',
  barcode: '',
  cash_register_tax: '',
  cash_register_type: ''
})

// Опции для селектов
const countries = computed(() => countriesData.map(country => ({
  label: country.name,
  value: String(country.name)
})))

const unitOptions = [
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
]

const packingOptions = [
  { label: 'Штучная', value: 'Штучная' },
  { label: 'Весовая', value: 'Весовая' },
  { label: 'Разливная', value: 'Разливная' }
]

const accountingTypeOptions = [
  { label: 'Без специализированного учета', value: 'Без специализированного учета' },
  { label: 'Алкогольный товар', value: 'Алкогольный товар' },
  { label: 'Учет по серийным номерам', value: 'Учет по серийным номерам' },
  { label: 'СИЗ', value: 'Средство индивидуальной защиты' }
]

const productTypeOptions = [
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
]

const barcodeTypeOptions = [
  { label: 'EAN8', value: 'EAN8' },
  { label: 'EAN13', value: 'EAN13' },
  { label: 'Code128', value: 'Code128' },
  { label: 'Code39', value: 'Code39' },
  { label: 'QR', value: 'QR' }
]

// Опции для категорий и подкатегорий
const categoryOptions = computed(() => categories.value.map(category => ({
  label: category.name,
  value: String(category.category_id)
})))

const subcategoryOptions = computed(() => subcategories.value.map(subcategory => ({
  label: subcategory.name,
  value: String(subcategory.subcategory_id)
})))

const deletingImageIds = ref([])

// Tailwind Notification state
const notification = reactive({ show: false, message: '', type: 'success' })
let notificationTimeout = null
function showNotification(message, type = 'success') {
  notification.message = message
  notification.type = type
  notification.show = true
  if (notificationTimeout) clearTimeout(notificationTimeout)
  notificationTimeout = setTimeout(() => {
    notification.show = false
  }, 2500)
}

// Загрузка данных товара
async function loadProduct() {
  isInitializing.value = true
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
      product.supplier = productData.supplier || ''
      product.article = productData.article || ''
      product.code = productData.code || ''
      product.external_code = productData.external_code || ''
      product.weight = productData.weight
      product.volume = productData.volume
      product.vat = productData.vat || ''
      product.marking = productData.marking || ''
      product.barcode = productData.barcode || ''
      product.cash_register_tax = productData.cash_register_tax || ''
      product.cash_register_type = productData.cash_register_type || ''

      // Загружаем изображения
      if (productData.images) {
        existingImages.value = productData.images
      }

      // Загружаем категории и подкатегории
      await loadCategories()
      
      // Устанавливаем категорию и подкатегорию
      if (productData.category) {
        selectedCategory.value = categoryOptions.value.find(c => c.value === String(productData.category)) || null
        await loadSubcategories(productData.category)
        
        if (productData.subcategory) {
          selectedSubcategory.value = subcategoryOptions.value.find(s => s.value === String(productData.subcategory)) || null
        }
      }

      // Устанавливаем остальные селекты
      if (productData.country) {
        selectedCountry.value = countries.value.find(c => c.value === String(productData.country)) || null
      }
      
      if (productData.unit) {
        selectedUnit.value = unitOptions.find(u => u.value === String(productData.unit)) || null
      }
      
      if (productData.packing) {
        selectedPacking.value = packingOptions.find(p => p.value === String(productData.packing)) || null
      }
      
      if (productData.accounting_type) {
        selectedAccountingType.value = accountingTypeOptions.find(a => a.value === String(productData.accounting_type)) || null
      }
      
      if (productData.product_type) {
        selectedProductType.value = productTypeOptions.find(t => t.value === String(productData.product_type)) || null
      }
      
      if (productData.barcode_type) {
        selectedBarcodeType.value = barcodeTypeOptions.find(b => b.value === String(productData.barcode_type)) || null
      }
    } else {
      productError.value = response.data.message || 'Ошибка загрузки товара'
    }
  } catch (error) {
    console.error('Ошибка загрузки товара:', error)
    productError.value = 'Ошибка загрузки товара'
  } finally {
    loadingProduct.value = false
    isInitializing.value = false
  }
}

// Загрузка категорий
async function loadCategories() {
  loadingCategories.value = true
  try {
    categories.value = await getCategoriesWithCache()
  } catch (error) {
    console.error('Ошибка загрузки категорий:', error)
    categoryError.value = 'Ошибка загрузки категорий'
  } finally {
    loadingCategories.value = false
  }
}

// Загрузка подкатегорий
async function loadSubcategories(categoryId) {
  if (!categoryId) return
  
  loadingSubcategories.value = true
  try {
    const response = await apiRequest(`/categories/${categoryId}/subcategories`)
    if (response.ok) {
      subcategories.value = response.data.data
    } else {
      subcategoryError.value = response.data.message || 'Ошибка загрузки подкатегорий'
    }
  } catch (error) {
    console.error('Ошибка загрузки подкатегорий:', error)
    subcategoryError.value = 'Ошибка загрузки подкатегорий'
  } finally {
    loadingSubcategories.value = false
  }
}

// Сохранение товара
async function handleSave() {
  if (!product.name) {
    saveError.value = 'Наименование товара обязательно'
    return
  }

  isSavingProduct.value = true
  saveError.value = ''

  try {
    const productData = {
      name: product.name,
      description: product.description,
      category_id: selectedCategory.value?.value,
      subcategory_id: selectedSubcategory.value?.value,
      country: selectedCountry.value?.value,
      supplier: product.supplier,
      article: product.article,
      code: product.code,
      external_code: product.external_code,
      unit: selectedUnit.value?.value,
      weight: product.weight,
      volume: product.volume,
      vat: product.vat,
      packing: selectedPacking.value?.value,
      accounting_type: selectedAccountingType.value?.value,
      marking: product.marking,
      product_type: selectedProductType.value?.value,
      barcode_type: selectedBarcodeType.value?.value,
      barcode: product.barcode,
      cash_register_tax: product.cash_register_tax,
      cash_register_type: product.cash_register_type
    }

    const response = await apiRequest(`/products/${productId.value}`, {
      method: 'PUT',
      body: JSON.stringify(productData)
    })

    if (response.ok) {
      showNotification('Данные товара успешно изменены', 'success')
    } else {
      showNotification(response.data.message || 'Ошибка сохранения товара', 'error')
    }
  } catch (error) {
    console.error('Ошибка сохранения товара:', error)
    saveError.value = 'Ошибка сохранения товара'
  } finally {
    isSavingProduct.value = false
  }
}

// Обработчики событий
async function onImageUploaded() {
  // После загрузки изображения — обновляем список изображений с сервера
  try {
    const response = await apiRequest(`/products/${productId.value}/images`);
    if (response.ok) {
      existingImages.value = response.data.images;
    }
  } catch (e) {
    // обработка ошибки (можно добавить toastr)
  }
  imageUploadError.value = '';
}

async function deleteExistingImage(imageId) {
  deletingImageIds.value.push(imageId)
  try {
    const response = await apiRequest(`/products/images/${imageId}`, {
      method: 'DELETE'
    })
    
    if (response.ok) {
      existingImages.value = existingImages.value.filter(img => img.id !== imageId)
      showNotification('Изображение удалено', 'success')
    } else {
      showNotification(response.data.message || 'Ошибка удаления изображения', 'error')
    }
  } catch (error) {
    console.error('Ошибка удаления изображения:', error)
    showNotification('Ошибка удаления изображения', 'error')
  } finally {
    deletingImageIds.value = deletingImageIds.value.filter(id => id !== imageId)
  }
}

function handleDeleteImage(imageId) {
  existingImages.value = existingImages.value.filter(img => img.id !== imageId)
}

function onCategoryOpen() {
  if (categories.value.length === 0) {
    loadCategories()
  }
}

function onCategoryClose() {
  // Логика при закрытии категории
}

function onSubcategoryOpen() {
  if (selectedCategory.value && subcategories.value.length === 0) {
    loadSubcategories(selectedCategory.value.value)
  }
}

function onSubcategoryClose() {
  // Логика при закрытии подкатегории
}

// Наблюдатели
watch(selectedCategory, (newCategory) => {
  if (isInitializing.value) return;
  if (newCategory) {
    selectedSubcategory.value = null
    subcategories.value = []
    loadSubcategories(newCategory.value)
  }
})

// Инициализация
onMounted(() => {
  loadProduct()
})
</script>

<style scoped>
.multiselect-custom {
  --ms-option-bg-selected: #3b82f6;
  --ms-option-color-selected: #ffffff;
  --ms-option-bg-pointed: #f3f4f6;
  --ms-option-color-pointed: #374151;
  --ms-tag-bg: #3b82f6;
  --ms-tag-color: #ffffff;
  --ms-border-color: #d1d5db;
  --ms-border-width: 1px;
  --ms-border-radius: 0.5rem;
  --ms-py: 0.5rem;
  --ms-px: 0.75rem;
  --ms-font-size: 0.875rem;
  min-width: 0;
  width: 100%;
}

.multiselect-custom .multiselect-tag {
  background: var(--ms-tag-bg);
  color: var(--ms-tag-color);
  border-radius: 0.25rem;
  padding: 0.125rem 0.375rem;
  margin: 0.125rem;
  font-size: 0.75rem;
}

.multiselect-custom .multiselect-option {
  padding: var(--ms-py) var(--ms-px);
  font-size: var(--ms-font-size);
}

.multiselect-custom .multiselect-option.is-selected {
  background: var(--ms-option-bg-selected);
  color: var(--ms-option-color-selected);
}

.multiselect-custom .multiselect-option.is-pointed {
  background: var(--ms-option-bg-pointed);
  color: var(--ms-option-color-pointed);
}

.multiselect-custom .multiselect-single-label {
  font-size: var(--ms-font-size);
  padding: var(--ms-py) var(--ms-px);
  text-align: center;
}

.multiselect-custom .multiselect-search {
  font-size: var(--ms-font-size);
  padding: var(--ms-py) var(--ms-px);
}

.multiselect-custom .multiselect-dropdown {
  border: var(--ms-border-width) solid var(--ms-border-color);
  border-radius: var(--ms-border-radius);
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style> 