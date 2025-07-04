<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-sm">
    <!-- Наименование и кнопки -->
    <div class="w-full mb-6">
      <div class="flex flex-col gap-3 sm:inline-flex sm:flex-row sm:items-center w-full">
        <input v-model="product.name" type="text" class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm" placeholder="* Наименование товара" />
        <div class="flex gap-2 mt-3 sm:mt-0">
          <button class="bg-lime-500 hover:bg-lime-600 text-white font-semibold px-6 py-2 rounded-lg shadow transition text-sm">Сохранить</button>
          <button @click="showCloseModal = true" class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold px-6 py-2 rounded-lg border shadow transition text-sm">Закрыть</button>
        </div>
      </div>
    </div>
    <!-- Модалка закрытия -->
    <div v-if="showCloseModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/30">
      <div class="bg-white rounded-lg shadow-2xl p-8 max-w-sm w-full text-sm">
        <div class="text-base font-semibold mb-4">Выйти без сохранения?</div>
        <div class="mb-6 text-gray-600">Изменения не будут сохранены. Вы уверены, что хотите выйти?</div>
        <div class="flex justify-end gap-3">
          <button @click="closeModalAndGo" class="bg-red-600 hover:bg-red-700 text-white font-semibold px-5 py-2 rounded-lg shadow transition text-sm">Выйти</button>
          <button @click="showCloseModal = false" class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold px-5 py-2 rounded-lg border shadow transition text-sm">Отмена</button>
        </div>
      </div>
    </div>
    <div class="flex flex-col md:flex-row gap-8">
      <!-- Левая колонка -->
      <div class="flex-1 flex flex-col gap-6">
        <!-- Изображения -->
        <div class="bg-gray-50 rounded-xl p-4 shadow-sm">
          <div class="font-semibold mb-2">Изображения</div>
          <button class="flex items-center gap-2 bg-white border border-blue-200 text-blue-700 font-medium px-4 py-2 rounded-lg shadow-sm hover:bg-blue-50 transition text-sm"><span class="text-lg">＋</span>Изображение</button>
        </div>
        <!-- Общие данные -->
        <div class="bg-gray-50 rounded-xl p-4 shadow-sm">
          <div class="font-semibold mb-2">Общие данные</div>
          <div class="flex flex-col gap-3">
            <div>
              <label class="block text-xs text-gray-700 mb-1">Описание</label>
              <textarea v-model="product.description" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm"></textarea>
            </div>
            <div class="flex gap-2">
              <div class="flex-1">
                <label class="block text-xs text-gray-700 mb-1">Категория</label>
                <Combobox v-model="selectedCategory">
                  <div class="relative">
                    <ComboboxInput
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm"
                      :display-value="cat => cat ? (cat.name_ru || cat.name) : ''"
                      @input="categoryQuery = $event.target.value"
                      placeholder="Категория"
                    />
                    <ComboboxOptions v-if="filteredCategories.length > 0" class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black/5 focus:outline-none sm:text-sm">
                      <ComboboxOption v-for="cat in filteredCategories" :key="cat.category_id" :value="cat" class="ui-active:bg-blue-100 ui-selected:bg-blue-200 cursor-pointer px-4 py-2">
                        {{ cat.name_ru || cat.name }}
                      </ComboboxOption>
                    </ComboboxOptions>
                  </div>
                </Combobox>
              </div>
              <div class="flex-1">
                <label class="block text-xs text-gray-700 mb-1">Подкатегория</label>
                <Combobox v-model="selectedSubcategory" :disabled="!selectedCategory">
                  <div class="relative">
                    <ComboboxInput
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm"
                      :display-value="sub => sub ? (sub.name_ru || sub.name) : ''"
                      @input="subcategoryQuery = $event.target.value"
                      placeholder="Подкатегория"
                      :disabled="!selectedCategory"
                    />
                    <ComboboxOptions v-if="filteredSubcategories.length > 0" class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black/5 focus:outline-none sm:text-sm">
                      <ComboboxOption v-for="sub in filteredSubcategories" :key="sub.subcategory_id" :value="sub" class="ui-active:bg-blue-100 ui-selected:bg-blue-200 cursor-pointer px-4 py-2">
                        {{ sub.name_ru || sub.name }}
                      </ComboboxOption>
                    </ComboboxOptions>
                  </div>
                </Combobox>
              </div>
            </div>
            <div class="flex gap-2">
              <div class="flex-1">
                <label class="block text-xs text-gray-700 mb-1">Страна</label>
                <input v-model="product.country" type="text" placeholder="Страна" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm" />
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
                <label class="block text-xs text-gray-700 mb-1">Единица измерения</label>
                <input v-model="product.unit" type="text" placeholder="шт" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm" />
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
        <!-- Неснижаемый остаток -->
        <div class="bg-gray-50 rounded-xl p-4 shadow-sm">
          <div class="font-semibold mb-2 flex items-center gap-2">Неснижаемый остаток <span class="text-blue-400 cursor-pointer text-xs">?</span></div>
          <div class="bg-blue-50 text-xs text-gray-700 rounded p-3 mb-3">
            Минимальное количество товара, которое всегда должно быть на складе. Когда количество становится меньше заданного, приходит уведомление. В разделе Товары → Остатки можно <a href="#" class="text-blue-600 underline">Пополнить резервы</a> — заказать товары для пополнения до указанного значения.
          </div>
          <div class="flex flex-col gap-2">
            <label class="flex items-center gap-2"><input v-model="product.stock_type" type="radio" value="sum" name="stock_type" class="accent-blue-600" /> <span>В сумме на всех складах</span> <input v-model="product.min_stock" type="number" class="ml-2 border border-gray-300 rounded px-2 py-1 text-xs w-24 focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm" placeholder="Не указан" /></label>
            <label class="flex items-center gap-2"><input v-model="product.stock_type" type="radio" value="same" name="stock_type" class="accent-blue-600" /> <span>Одинаковый на всех складах</span></label>
            <label class="flex items-center gap-2"><input v-model="product.stock_type" type="radio" value="by_warehouse" name="stock_type" class="accent-blue-600" /> <span>Задать для каждого склада</span></label>
          </div>
        </div>
        <!-- Особенности учета -->
        <div class="bg-gray-50 rounded-xl p-4 shadow-sm">
          <div class="font-semibold mb-2">Особенности учета</div>
          <div class="flex flex-col gap-2">
            <div class="flex gap-2">
              <div class="flex-1">
                <label class="block text-xs text-gray-700 mb-1">Фасовка</label>
                <input v-model="product.packing" type="text" placeholder="Штучная" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm" />
              </div>
              <div class="flex-1">
                <label class="block text-xs text-gray-700 mb-1">Тип учета</label>
                <input v-model="product.accounting_type" type="text" placeholder="Без специализированного учета" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm" />
              </div>
            </div>
            <label class="flex items-center gap-2"><input v-model="product.traceable" type="checkbox" class="accent-blue-600" /> <span>Прослеживаемый</span></label>
            <div class="flex gap-2 items-center">
              <label class="block text-xs text-gray-700 mb-1">Маркировка</label>
              <input v-model="product.marking" type="text" placeholder="" class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm" />
              <a href="#" class="text-blue-600 underline text-xs ml-2">Продление опции</a>
            </div>
            <div>
              <label class="block text-xs text-gray-700 mb-1">Тип продукции</label>
              <input v-model="product.product_type" type="text" placeholder="Не маркируется" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm" />
            </div>
          </div>
        </div>
        <!-- Штрихкоды товара -->
        <div class="bg-gray-50 rounded-xl p-4 shadow-sm">
          <div class="font-semibold mb-2 flex items-center gap-2">Штрихкоды товара <span class="text-blue-400 cursor-pointer text-xs">?</span></div>
          <div class="bg-blue-50 text-xs text-gray-700 rounded p-3 mb-3">
            Укажите штрихкод товара, чтобы добавлять его в документы при помощи сканера штрихкодов. Код GTIN включает только цифры и может иметь длину 8, 12, 13 или 14 цифр. В учетных системах всегда используется 14 знаков, коды с меньшим количеством цифр дополняют ведущими нулями.
          </div>
          <div class="flex gap-2 mb-2">
            <select v-model="product.barcode_type" class="border border-gray-300 rounded-lg px-2 py-1 text-xs focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm">
              <option>EAN13</option>
              <option>EAN8</option>
              <option>GTIN</option>
            </select>
            <input v-model="product.barcode" type="text" placeholder="2000000000091" class="border border-gray-300 rounded-lg px-2 py-1 text-xs flex-1 focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm" />
            <button class="bg-gray-100 border border-gray-300 px-2 py-1 rounded text-xs">...</button>
          </div>
          <button class="flex items-center gap-2 bg-white border border-blue-200 text-blue-700 font-medium px-4 py-2 rounded-lg shadow-sm hover:bg-blue-50 transition text-sm"><span class="text-lg">＋</span>Штрихкод</button>
        </div>
        <!-- Кассовый чек -->
        <div class="bg-gray-50 rounded-xl p-4 shadow-sm">
          <div class="font-semibold mb-2">Кассовый чек</div>
          <div class="flex flex-col gap-2">
            <div class="flex gap-2">
              <div class="flex-1">
                <label class="block text-xs text-gray-700 mb-1">Система налогообложения</label>
                <input v-model="product.cash_register_tax" type="text" placeholder="Совпадает с точкой" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm" />
              </div>
              <div class="flex-1">
                <label class="block text-xs text-gray-700 mb-1">Признак предмета расчета</label>
                <input v-model="product.cash_register_type" type="text" placeholder="Товар" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm" />
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- Правая колонка -->
      <div class="flex-[2] flex flex-col gap-6">
        <!-- Табы -->
        <div class="flex gap-2 mb-4">
          <button class="px-6 py-2 rounded bg-gray-100 text-gray-700 font-semibold">Цены</button>
          <button class="px-6 py-2 rounded bg-blue-600 text-white font-semibold">Модификации (0)</button>
          <button class="px-6 py-2 rounded bg-gray-100 text-gray-700 font-semibold">Упаковка (0)</button>
          <button class="px-6 py-2 rounded bg-gray-100 text-gray-700 font-semibold">Остатки</button>
          <button class="px-6 py-2 rounded bg-gray-100 text-gray-700 font-semibold">История</button>
          <button class="px-6 py-2 rounded bg-gray-100 text-gray-700 font-semibold">Файлы (0)</button>
        </div>
        <!-- Модификации -->
        <div class="bg-gray-50 rounded-lg p-4 mb-4">
          <button class="flex items-center gap-2 bg-white border border-blue-200 text-blue-700 font-medium px-4 py-2 rounded text-sm"><span class="text-lg">＋</span>Модификация</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import { Combobox, ComboboxInput, ComboboxOptions, ComboboxOption } from '@headlessui/vue'

const API_URL = '/api'; // если нужен абсолютный путь, замените на полный URL

const showCloseModal = ref(false)
const router = useRouter()

const categories = ref([])
const subcategories = ref([])
const selectedCategory = ref(null)
const selectedSubcategory = ref(null)
const categoryQuery = ref('')
const subcategoryQuery = ref('')

onMounted(async () => {
  // Получаем категории с API
  const res = await fetch(`${API_URL}/categories`)
  categories.value = await res.json()
})

watch(selectedCategory, async (cat) => {
  selectedSubcategory.value = null
  product.category = cat ? cat.category_id : ''
  subcategories.value = []
  if (cat) {
    // Получаем подкатегории с API
    const res = await fetch(`${API_URL}/subcategories?category_id=${encodeURIComponent(cat.category_id)}`)
    subcategories.value = await res.json()
  }
})

watch(selectedSubcategory, (subcat) => {
  product.subcategory = subcat ? subcat.subcategory_id : ''
})

const filteredCategories = computed(() => {
  if (!categoryQuery.value) return categories.value
  return categories.value.filter(c => (c.name_ru || c.name || '').toLowerCase().includes(categoryQuery.value.toLowerCase()))
})

const filteredSubcategories = computed(() => {
  if (!selectedCategory.value) return []
  let list = subcategories.value
  if (!subcategoryQuery.value) return list
  return list.filter(s => (s.name_ru || s.name || '').toLowerCase().includes(subcategoryQuery.value.toLowerCase()))
})

const showTooltip = reactive({
  article: false,
  code: false
})

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
  min_stock: null,
  stock_type: 'sum',
  packing: '',
  accounting_type: '',
  traceable: false,
  marking: '',
  product_type: '',
  barcode_type: 'EAN13',
  barcode: '',
  cash_register_tax: '',
  cash_register_type: ''
})

function closeModalAndGo() {
  showCloseModal.value = false
  router.push('/products')
}
</script> 