<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-sm">
    <!-- Наименование и кнопки -->
    <div class="mb-6 w-full" style="position: sticky;top: 62px;background: #fff;z-index: 99;padding: 10px 0;">
      <div class="flex flex-col gap-3 sm:inline-flex sm:flex-row sm:items-center w-full">
        <input v-model="product.name" @blur="handleNameBlur" type="text" class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm bg-white" placeholder="Наименование товара *" />
        <div class="flex gap-2 mt-3 sm:mt-0">
          <button @click="handleSave" :disabled="!product.name || !selectedCategory || !selectedSubcategory || !selectedWarehouse || !product.unit || !product.quantity || !productId || isSavingDraft || isSavingProduct" class="bg-lime-500 hover:bg-lime-600 text-white font-semibold px-6 py-2 rounded-lg shadow transition text-sm disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
            <svg v-if="isSavingDraft || isSavingProduct" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
            </svg>
            {{ isSavingDraft ? 'Создание черновика...' : isSavingProduct ? 'Сохранение...' : 'Сохранить' }}
          </button>
          <button @click="showCloseModal = true" class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold px-6 py-2 rounded-lg border shadow transition text-sm">Закрыть</button>
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
        <div class="font-semibold mb-2">Изображения</div>
        <ImageDropzone :product-id="productId" :images="images" :disabled="!product.name || !productId" @uploaded="onImageUploaded" @deleted="handleDeleteImage" />
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
          <div class="flex flex-col gap-2 w-full">
            <div class="w-full">
              <label class="block text-xs text-gray-700 mb-1">Категория <span class="text-red-500">*</span></label>
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
                  class="w-full text-xs multiselect-custom bg-white"
                  @open="onCategoryOpen"
                  @close="onCategoryClose"
                />
              </template>
            </div>
            <div class="w-full">
              <label class="block text-xs text-gray-700 mb-1">Подкатегория <span class="text-red-500">*</span></label>
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
                  class="w-full text-xs multiselect-custom bg-white"
                  @open="onSubcategoryOpen"
                  @close="onSubcategoryClose"
                />
              </template>
            </div>
          </div>
          <!-- Склад товара -->
                      <div class="w-full">
              <label class="block text-xs text-gray-700 mb-1">Склад товара <span class="text-red-500">*</span></label>
            <template v-if="loadingWarehouses">
              <div class="w-full h-10 flex items-center justify-center bg-white border border-gray-200 rounded-lg">
                <svg class="animate-spin h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                </svg>
                <span class="ml-2 text-xs text-gray-500">Загрузка складов...</span>
              </div>
            </template>
            <template v-else>
              <Multiselect
                v-model="selectedWarehouse"
                :options="warehouseOptions"
                label="label"
                value="value"
                :object="true"
                placeholder="Выберите склад"
                :disabled="warehouses.length === 0"
                :max-height="400"
                class="w-full text-xs multiselect-custom"
              />
            </template>
            <!-- Сообщение если складов нет -->
            <div v-if="warehouses.length === 0 && !loadingWarehouses" class="mt-2 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
              <div class="text-sm text-yellow-800 mb-2">Для добавления товаров вам необходимо сначала добавить склад.</div>
              <button @click="goToCreateWarehouse" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg shadow transition text-sm">
                Добавить склад
              </button>
            </div>
          </div>
          <!-- Количество единиц товара, единица измерения и стоимость -->
          <div class="flex gap-2">
            <div class="flex-1">
              <label class="block text-xs text-gray-700 mb-1">Количество единиц товара <span class="text-red-500">*</span></label>
              <input v-model="product.quantity" type="number" min="0" step="1" placeholder="0" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm bg-white" />
            </div>
            <div class="flex-1">
              <label class="block text-xs text-gray-700 mb-1">Ед-ца измерения <span class="text-red-500">*</span></label>
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
            </div>
            <div class="flex-1">
              <label class="block text-xs text-gray-700 mb-1">Стоимость за единицу</label>
              <input v-model="product.price" type="number" min="0" step="0.01" placeholder="0.00" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm bg-white" />
            </div>
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
          <!-- Описание -->
          <div>
            <label class="block text-xs text-gray-700 mb-1">Описание</label>
            <textarea v-model="product.description" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm bg-white"></textarea>
          </div>
          
          <!-- Страна и поставщик -->
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
              <input v-model="product.supplier" type="text" placeholder="" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm bg-white" />
            </div>
          </div>
          
          <!-- Артикул, код, внешний код -->
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
              <input v-model="product.article" type="text" placeholder="" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm bg-white" />
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
              <input v-model="product.code" type="text" placeholder="" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm bg-white" />
            </div>
            <div class="flex-1">
              <label class="block text-xs text-gray-700 mb-1">Внешний код</label>
              <input v-model="product.external_code" type="text" placeholder="" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm bg-white" />
            </div>
          </div>
          
          <!-- Вес, объем, НДС -->
          <div class="flex gap-2">
            <div class="flex-1">
              <label class="block text-xs text-gray-700 mb-1">Вес</label>
              <input v-model="product.weight" type="number" placeholder="" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm bg-white" />
            </div>
            <div class="flex-1">
              <label class="block text-xs text-gray-700 mb-1">Объем</label>
              <input v-model="product.volume" type="number" placeholder="" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm bg-white" />
            </div>
            <div class="flex-1">
              <label class="block text-xs text-gray-700 mb-1">НДС</label>
              <input v-model="product.vat" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm bg-white" />
            </div>
          </div>
          
          <!-- Особенности учета -->
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
                  { label: 'Икра и морепродукты', value: 'Икра и морепродукты' },
                  { label: 'Велосипеды', value: 'Велосипеды' },
                  { label: 'Безалкогольное пиво', value: 'Безалкогольное пиво' }
                ]"
                label="label"
                value="value"
                :object="true"
                placeholder="Выберите тип продукции"
                :max-height="400"
                class="w-full text-xs multiselect-custom"
              />
            </div>
          </div>
          
          <!-- Штрихкоды товара -->
          <div>
            <div class="font-semibold mb-2 flex items-center gap-2">Штрихкоды товара <span class="text-blue-400 cursor-pointer text-xs">?</span></div>
            <div class="bg-blue-50 text-xs text-gray-700 rounded p-3 mb-3">
              Укажите штрихкод товара, чтобы добавлять его в документы при помощи сканера штрихкодов. Код GTIN включает только цифры и может иметь длину 8, 12, 13 или 14 цифр. В учетных системах всегда используется 14 знаков, коды с меньшим количеством цифр дополняют ведущими нулями.
            </div>
            <div class="flex gap-2 mb-2">
              <div class="w-32">
                <label class="block text-xs text-gray-700 mb-1">Тип штрихкода</label>
                <Multiselect
                  v-model="product.barcode_type"
                  :options="[
                    { label: 'EAN8', value: 'EAN8' },
                    { label: 'EAN13', value: 'EAN13' },
                    { label: 'Code128', value: 'Code128' },
                    { label: 'GTIN', value: 'GTIN' },
                    { label: 'UPC', value: 'UPC' }
                  ]"
                  label="label"
                  value="value"
                  :object="true"
                  placeholder="Тип"
                  :max-height="400"
                  class="w-full text-xs multiselect-custom bg-white"
                />
              </div>
              <div class="flex-1">
                <label class="block text-xs text-gray-700 mb-1">Штрихкод</label>
                <input v-model="product.barcode" type="text" placeholder="" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm bg-white" />
              </div>
            </div>
          </div>
          
          <!-- Кассовый чек -->
          <div>
            <div class="font-semibold mb-2">Кассовый чек</div>
            <div class="flex flex-col gap-2">
              <div class="flex gap-2">
                <div class="flex-1">
                  <label class="block text-xs text-gray-700 mb-1">Система налогообложения</label>
                  <input v-model="product.cash_register_tax" type="text" placeholder="" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm bg-white" />
                </div>
                <div class="flex-1">
                  <label class="block text-xs text-gray-700 mb-1">Признак предмета расчета</label>
                  <input v-model="product.cash_register_type" type="text" placeholder="" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm bg-white" />
                </div>
              </div>
            </div>
          </div>
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
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch, onBeforeUnmount } from 'vue'
import { useRouter } from 'vue-router'
import Multiselect from '@vueform/multiselect'
import '@vueform/multiselect/themes/default.css'
import { apiRequest, getCategoriesWithCache } from '@/config/api'
import ImageDropzone from './ImageDropzone.vue'
import countriesData from '@/data/countries.json'
import toastr from 'toastr'

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

/** @type {import('vue').Ref<Array>} */
const warehouses = ref([])
/** @type {import('vue').Ref<Object|null>} */
const selectedWarehouse = ref(null)
/** @type {import('vue').Ref<boolean>} */
const loadingWarehouses = ref(false)
/** @type {import('vue').Ref<string>} */
const warehouseError = ref('')

/** @type {import('vue').Ref<boolean>} */
const showAdditionalData = ref(false)

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
        draftError.value = 'Ошибка создания черновика товара'
      }
    } catch (e) {
      draftError.value = 'Ошибка создания черновика товара'
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
  try {
    const response = await apiRequest(`/products/images/${imgId}`, { method: 'DELETE' })
    if (response.ok) {
      images.value = images.value.filter(img => img.id !== imgId)
    }
  } catch (e) {}
}

async function fetchWarehouses() {
  loadingWarehouses.value = true
  try {
    const response = await apiRequest('/warehouses')
    if (response.ok && response.data.success) {
      warehouses.value = response.data.data || []
    } else {
      warehouseError.value = 'Ошибка загрузки складов'
    }
  } catch (e) {
    warehouseError.value = 'Ошибка загрузки складов'
  } finally {
    loadingWarehouses.value = false
  }
}

function goToCreateWarehouse() {
  router.push('/warehouses/create')
}

onMounted(async () => {
  loadingCategories.value = true
  try {
    categories.value = await getCategoriesWithCache()
  } catch (e) {
    categoryError.value = 'Ошибка загрузки категорий'
  } finally {
    loadingCategories.value = false
  }
  
  // Загружаем склады
  await fetchWarehouses()
  
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
      const response = await apiRequest(`/subcategories?category_id=${encodeURIComponent(cat.value)}`)
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

watch(() => product.quantity, () => {
  if (product.quantity) hasUnsavedChanges.value = true
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

const warehouseOptions = computed(() =>
  Array.isArray(warehouses.value)
    ? warehouses.value.map(w => ({
        label: w.name,
        value: w.id,
        raw: w
      }))
    : []
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

async function handleSave() {
  if (!productId.value) {
    toastr.error('Сначала создайте черновик товара, указав название')
    return
  }

  isSavingProduct.value = true

  try {
    // Подготавливаем данные для отправки
    const productData = {
      id: productId.value,
      name: product.name,
      description: product.description,
      category_id: product.category,
      subcategory_id: product.subcategory,
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
      quantity: product.quantity,
      warehouse_id: selectedWarehouse.value?.value || product.warehouse,
      price: product.price
    }

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
      // Перенаправляем на страницу всех товаров
      router.push('/products')
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