<template>
  <div class="min-h-screen bg-gray-50" style="padding-top: 66px;">
    <!-- Внутреннее меню навигации -->
    <ProductsMenu />
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Заголовок страницы -->
        <div class="mb-6">
          <!-- Мобильная версия: двухстрочная -->
          <div class="block sm:hidden w-full">
            <!-- Первая строка: заголовок и кнопка фильтра -->
            <div class="flex items-center justify-between mb-2">
              <h1 class="text-2xl md:text-3xl font-bold text-gray-900">{{ t('BalancesPage_1') }}</h1> <!-- Остатки -->
              <button
                @click="toggleFilters"
                class="flex items-center gap-2 text-gray-700 font-medium px-4 py-2 rounded text-sm hover:bg-gray-100 transition-colors cursor-pointer group"
              >
                <Filter v-if="!showFilters" class="w-4 h-4" />
                <FunnelX v-else class="w-4 h-4" />
                <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-900 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-10">
                  {{ showFilters ? t('BalancesPage_2') : t('BalancesPage_3') }} <!-- Скрыть фильтры : Показать фильтры -->
                  <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
                </div>
              </button>
            </div>
            <!-- Вторая строка: кнопки действий -->
            <div class="flex items-center gap-2 flex-wrap">
              <router-link 
                to="/products/create" 
                class="flex items-center gap-2 bg-blue-50 border border-blue-200 text-blue-700 font-medium px-4 py-2 rounded text-sm hover:bg-blue-100 transition-colors relative group"
              >
                <Plus class="w-4 h-4 text-blue-700" />
                {{ t('BalancesPage_4') }} <!-- Остаток -->
                <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-900 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-10">
                  {{ t('BalancesPage_5') }} <!-- Добавить новый остаток -->
                  <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
                </div>
              </router-link>
              <button 
                @click="openImportModal"
                :disabled="importLoading"
                class="bg-white border border-gray-300 px-4 py-2 rounded font-medium text-sm hover:bg-gray-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed inline-flex items-center justify-center gap-2 relative group"
              >
                <Loader2 v-if="importLoading" class="w-4 h-4 animate-spin" />
                {{ importLoading ? t('BalancesPage_6') : t('BalancesPage_7') }} <!-- Обработка... : Импорт -->
                <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-900 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-10">
                  {{ importLoading ? t('BalancesPage_8') : t('BalancesPage_9') }} <!-- Обработка файла... : Импорт остатков из файла Excel -->
                  <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
                </div>
              </button>
              <button 
                @click="exportBalances"
                :disabled="exportLoading"
                class="bg-white border border-gray-300 px-4 py-2 rounded font-medium text-sm hover:bg-gray-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed inline-flex items-center justify-center gap-2 relative group"
              >
                <Loader2 v-if="exportLoading" class="w-4 h-4 animate-spin" />
                {{ exportLoading ? t('BalancesPage_10') : t('BalancesPage_11') }} <!-- Экспорт... : Экспорт -->
                <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-900 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-10">
                  {{ exportLoading ? t('BalancesPage_12') : t('BalancesPage_13') }} <!-- Выполняется экспорт... : Экспорт остатков в файл Excel -->
                  <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
                </div>
              </button>
            </div>
          </div>
          
          <!-- ПК версия: однострочная как раньше -->
          <div class="hidden items-center justify-between sm:flex w-full">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900">{{ t('BalancesPage_1') }}</h1> <!-- Остатки -->
            <div class="flex items-center gap-2">
              <button
                @click="toggleFilters"
                class="flex items-center gap-2 text-gray-700 font-medium px-4 py-2 rounded text-sm hover:bg-gray-100 transition-colors cursor-pointer relative group"
              >
                <Filter v-if="!showFilters" class="w-4 h-4" />
                <FunnelX v-else class="w-4 h-4" />
                <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-900 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-10">
                  {{ showFilters ? t('BalancesPage_2') : t('BalancesPage_3') }} <!-- Скрыть фильтры : Показать фильтры -->
                  <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
                </div>
              </button>
              <router-link 
                to="/products/create" 
                class="flex items-center gap-2 bg-blue-50 border border-blue-200 text-blue-700 font-medium px-4 py-2 rounded text-sm hover:bg-blue-100 transition-colors relative group"
              >
                <Plus class="w-4 h-4 text-blue-700" />
                {{ t('BalancesPage_4') }} <!-- Остаток -->
                <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-900 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-10">
                  {{ t('BalancesPage_5') }} <!-- Добавить новый остаток -->
                  <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
                </div>
              </router-link>
              <button 
                @click="openImportModal"
                :disabled="importLoading"
                class="bg-white border border-gray-300 px-4 py-2 rounded font-medium text-sm hover:bg-gray-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed inline-flex items-center justify-center gap-2 relative group"
              >
                <Loader2 v-if="importLoading" class="w-4 h-4 animate-spin" />
                {{ importLoading ? t('BalancesPage_6') : t('BalancesPage_7') }} <!-- Обработка... : Импорт -->
                <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-900 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-10">
                  {{ importLoading ? t('BalancesPage_8') : t('BalancesPage_9') }} <!-- Обработка файла... : Импорт остатков из файла Excel -->
                  <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
                </div>
              </button>
              <button 
                @click="exportBalances"
                :disabled="exportLoading"
                class="bg-white border border-gray-300 px-4 py-2 rounded font-medium text-sm hover:bg-gray-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed inline-flex items-center justify-center gap-2 relative group"
              >
                <Loader2 v-if="exportLoading" class="w-4 h-4 animate-spin" />
                {{ exportLoading ? t('BalancesPage_10') : t('BalancesPage_11') }} <!-- Экспорт... : Экспорт -->
                <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-900 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-10">
                  {{ exportLoading ? t('BalancesPage_12') : t('BalancesPage_13') }} <!-- Выполняется экспорт... : Экспорт остатков в файл Excel -->
                  <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
                </div>
              </button>
            </div>
          </div>
        </div>
      
      <!-- Фильтры и поиск -->
      <div v-if="showFilters" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <!-- Основные фильтры -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
          <div>
            <label class="block text-sm text-gray-700 mb-1">{{ t('BalancesPage_14') }}</label> <!-- Склад -->
            <Multiselect
              v-model="filters.warehouse_id"
              :options="warehouseOptions"
              label="label"
              value="value"
              :object="false"
              :placeholder="t('BalancesPage_15')"
              :max-height="400"
              class="w-full text-sm multiselect-custom"
              :loading="loadingWarehouses"
              :disabled="loadingWarehouses"
            />
          </div>
          <div>
            <label class="block text-sm text-gray-700 mb-1">{{ t('BalancesPage_16') }}</label> <!-- Поиск товара -->
            <input
              v-model="filters.search"
              type="text"
              :placeholder="t('BalancesPage_17')"
              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm"
            />
          </div>
          <div>
            <label class="block text-sm text-gray-700 mb-1">{{ t('BalancesPage_18') }}</label> <!-- Мин. остаток -->
            <input
              v-model.number="filters.min_quantity"
              type="number"
              min="0"
              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm"
            />
          </div>
          <div>
            <label class="block text-sm text-gray-700 mb-1">{{ t('BalancesPage_19') }}</label> <!-- Макс. остаток -->
            <input
              v-model.number="filters.max_quantity"
              type="number"
              min="0"
              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm"
            />
          </div>
        </div>

        <!-- Дополнительные фильтры -->
        <div v-if="!loadingProductFields" class="pt-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">{{ t('BalancesPage_20') }}</h3> <!-- Дополнительные фильтры -->
          
          <!-- Обязательные поля -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
              <label class="block text-sm text-gray-700 mb-1">{{ t('BalancesPage_21') }}</label> <!-- Категория -->
              <Multiselect
                v-model="selectedCategory"
                :options="categoryOptions"
                label="label"
                value="value"
                :object="true"
                :placeholder="t('BalancesPage_22')"
                searchable
                :max-height="400"
                class="w-full text-sm multiselect-custom"
                :loading="loadingCategories"
              />
            </div>
            <div>
              <label class="block text-sm text-gray-700 mb-1">{{ t('BalancesPage_23') }}</label> <!-- Подкатегория -->
              <Multiselect
                v-model="selectedSubcategory"
                :options="subcategoryOptions"
                label="label"
                value="value"
                :object="true"
                :placeholder="t('BalancesPage_24')"
                searchable
                :max-height="400"
                class="w-full text-sm multiselect-custom"
                :loading="loadingSubcategories"
                :disabled="!selectedCategory"
              />
            </div>
            <div>
              <label class="block text-sm text-gray-700 mb-1">{{ t('BalancesPage_25') }}</label> <!-- Дата создания -->
              <LocalizedDatePicker 
                v-model="filters.created_at"
                :enable-time-picker="false"
                :auto-apply="true"
              />
            </div>
          </div>
          
          <!-- Дополнительные поля (активные в настройках) -->
          <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <template v-for="field in standardProductFields" :key="field.key">
              <div v-if="productFieldsVisibility[field.key] === true">
                <label class="block text-sm text-gray-700 mb-1">{{ field.label }}</label>
                <input 
                  v-model="filters[field.key]" 
                  type="text" 
                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm"
                  :placeholder="`Поиск по ${field.label.toLowerCase()}...`"
                />
              </div>
            </template>
          </div>
        </div>

        <!-- Загрузка полей -->
        <div v-else class="pt-6">
          <div class="flex items-center justify-center py-4">
            <Loader2 class="animate-spin h-6 w-6 text-blue-500" />
            <span class="ml-2 text-gray-500 text-sm">{{ t('BalancesPage_27') }}</span> <!-- Загрузка полей... -->
          </div>
        </div>

        <div class="mt-6 flex gap-2 justify-end">
          <button
            @click="clearFilters"
            :disabled="loading"
            class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm transition"
          >
            {{ t('BalancesPage_28') }} <!-- Сбросить -->
          </button>
          <button
            @click="loadBalances"
            :disabled="loading"
            class="bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white px-4 py-2 rounded-lg text-sm transition"
          >
            {{ t('BalancesPage_29') }} <!-- Применить фильтры -->
          </button>
        </div>
      </div>

      <!-- Основной контент -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">

        <!-- Сводка -->
        <div v-if="loadingSummary" class="bg-blue-50 rounded-lg p-4 mb-6">
          <!-- <h3 class="text-lg font-medium text-gray-900 mb-4">Сводка по остаткам</h3> -->
          <div class="flex justify-center items-center py-8">
            <div class="text-center">
              <Loader2 class="animate-spin h-8 w-8 text-blue-600 mx-auto mb-4" />
              <div class="text-gray-600 text-sm">{{ t('BalancesPage_30') }}</div> <!-- Загрузка сводки... -->
            </div>
          </div>
        </div>
        
        <div v-else-if="summary" class="bg-blue-50 rounded-lg p-4 mb-6">
          <!-- <h3 class="text-lg font-medium text-gray-900 mb-4">Сводка по остаткам</h3> -->
          <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
            <div class="text-center">
              <div class="text-2xl font-bold text-blue-600" style="font-size: 20px;">{{ summary.total_products }}</div>
              <div class="text-sm text-gray-600">{{ t('BalancesPage_31') }}</div> <!-- Товаров -->
            </div>
            <div class="text-center">
              <div class="text-2xl font-bold text-green-600" style="font-size: 20px;">{{ summary.total_warehouses }}</div>
              <div class="text-sm text-gray-600">{{ t('BalancesPage_32') }}</div> <!-- Складов -->
            </div>
            <div class="text-center">
              <div class="text-2xl font-bold text-purple-600" style="font-size: 20px;">{{ summary.total_quantity }}</div>
              <div class="text-sm text-gray-600">{{ t('BalancesPage_33') }}</div> <!-- Общее количество -->
            </div>
            <div class="text-center">
              <div class="text-2xl font-bold text-indigo-600" style="font-size: 20px;">{{ formatCurrency(summary.total_value) }}</div>
              <div class="text-sm text-gray-600">{{ t('BalancesPage_34') }}</div> <!-- Общая стоимость -->
            </div>
            <div class="text-center">
              <div class="text-2xl font-bold text-orange-600" style="font-size: 20px;">{{ summary.low_stock_items }}</div>
              <div class="text-sm text-gray-600">{{ t('BalancesPage_35') }}</div> <!-- Низкий остаток -->
            </div>
            <div class="text-center">
              <div class="text-2xl font-bold text-red-600" style="font-size: 20px;">{{ summary.out_of_stock_items }}</div>
              <div class="text-sm text-gray-600">{{ t('BalancesPage_36') }}</div> <!-- Нет в наличии -->
            </div>
          </div>
        </div>

        <!-- Список остатков -->
        <div v-if="loading" class="flex justify-center items-center py-12">
          <div class="text-center">
            <Loader2 class="animate-spin h-8 w-8 text-blue-600 mx-auto mb-4" />
            <div class="text-gray-600 text-sm">{{ t('BalancesPage_37') }}</div> <!-- Загрузка остатков... -->
          </div>
        </div>
        
        <div v-else class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">
                  {{ t('BalancesPage_38') }} <!-- Товар -->
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">
                  {{ t('BalancesPage_39') }} <!-- Остаток -->
                </th>
                <th v-if="productFieldsVisibility.price !== false" class="px-6 py-3 text-left text-xs font-medium text-gray-500">
                  {{ t('BalancesPage_40') }} <!-- Цена ед/итого -->
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">
                  {{ t('BalancesPage_41') }} <!-- Статус -->
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">
                  {{ t('BalancesPage_42') }} <!-- Действия -->
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <template v-for="product in balances" :key="`${product.id}`">
                <tr class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="flex-shrink-0 h-10 w-10">
                        <img
                          v-if="product?.images?.length > 0"
                          :src="product.images[0].image_url"
                          :alt="product.images[0].alt_text || product.name"
                          class="h-10 w-10 rounded-lg object-cover"
                        />
                        <div v-else class="h-10 w-10 bg-gray-200 rounded-lg flex items-center justify-center">
                          <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                          </svg>
                        </div>
                      </div>
                      <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900">{{ product?.name }}</div>
                        <div class="text-sm text-gray-500">{{ product?.category_name || product?.category }}</div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    <!-- Показываем общий остаток -->
                    <span :class="getQuantityClass(product.total_quantity || 0)">
                      {{ product.total_quantity || 0 }}
                    </span>
                  </td>
                  <td v-if="productFieldsVisibility.price !== false" class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ formatCurrency(Number(product?.price) || 0) }} <br>
                    {{ formatCurrency((product.total_quantity || 0) * (Number(product?.price) || 0)) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="getStatusClass(product.total_quantity || 0)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                      {{ getStatusText(product.total_quantity || 0) }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex items-center gap-2">
                      <div class="relative group">
                        <button
                          @click="viewMovements(product.id)"
                          class="text-blue-600 hover:text-blue-900 cursor-pointer p-1 rounded hover:bg-blue-50 transition-colors"
                        >
                          <ArrowRightLeft class="w-4 h-4" />
                        </button>
                        <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-900 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-10">
                          {{ t('BalancesPage_43') }} <!-- Движение товара -->
                          <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
                        </div>
                      </div>
                      <div class="relative group">
                        <router-link
                          v-if="product?.id"
                          :to="`/products/edit/${product.id}`"
                          class="text-green-600 hover:text-green-900 cursor-pointer p-1 rounded hover:bg-green-50 transition-colors"
                        >
                          <Edit class="w-4 h-4" />
                        </router-link>
                        <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-900 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-10">
                          {{ t('BalancesPage_44') }} <!-- Редактировать товар -->
                          <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
                        </div>
                      </div>
                      <div class="relative group">
                        <button
                          @click="openDeleteModal(product.id)"
                          class="text-red-600 hover:text-red-900 cursor-pointer p-1 rounded hover:bg-red-50 transition-colors"
                        >
                          <Trash2 class="w-4 h-4" />
                        </button>
                        <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-900 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-10">
                          {{ t('BalancesPage_45') }} <!-- Удалить товар -->
                          <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
                        </div>
                      </div>
                    </div>
                  </td>
                </tr>
                <!-- Дополнительная строка с полями товара -->
                <tr class="bg-gray-50">
                  <td :colspan="productFieldsVisibility.price !== false ? 5 : 4" class="px-6 py-3">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 text-sm">
                      <!-- Обязательные поля -->
                      <div v-if="product?.category_name || product?.category" class="flex items-center gap-2">
                        <span class="font-medium text-gray-600">{{ t('BalancesPage_46') }}</span> <!-- Категория: -->
                        <span class="text-gray-900">{{ product.category_name || product.category }}</span>
                      </div>
                      <div v-if="product?.subcategory_name || product?.subcategory" class="flex items-center gap-2">
                        <span class="font-medium text-gray-600">{{ t('BalancesPage_47') }}</span> <!-- Подкатегория: -->
                        <span class="text-gray-900">{{ product.subcategory_name || product.subcategory }}</span>
                      </div>
                      
                      <!-- Дополнительные поля (активные) -->
                      <div v-if="productFieldsVisibility.description && product?.description" class="flex items-center gap-2">
                        <span class="font-medium text-gray-600">{{ t('BalancesPage_48') }}</span> <!-- Описание: -->
                        <span class="text-gray-900">{{ product.description }}</span>
                      </div>
                      <div v-if="productFieldsVisibility.country && product?.country" class="flex items-center gap-2">
                        <span class="font-medium text-gray-600">{{ t('BalancesPage_49') }}</span> <!-- Страна: -->
                        <span class="text-gray-900">{{ product.country }}</span>
                      </div>
                      <div v-if="productFieldsVisibility.supplier && product?.supplier" class="flex items-center gap-2">
                        <span class="font-medium text-gray-600">{{ t('BalancesPage_50') }}</span> <!-- Поставщик: -->
                        <span class="text-gray-900">{{ product.supplier }}</span>
                      </div>
                      <div v-if="productFieldsVisibility.article && product?.article" class="flex items-center gap-2">
                        <span class="font-medium text-gray-600">{{ t('BalancesPage_51') }}</span> <!-- Артикул: -->
                        <span class="text-gray-900">{{ product.article }}</span>
                      </div>
                      <div v-if="productFieldsVisibility.code && product?.code" class="flex items-center gap-2">
                        <span class="font-medium text-gray-600">{{ t('BalancesPage_52') }}</span> <!-- Код: -->
                        <span class="text-gray-900">{{ product.code }}</span>
                      </div>
                      <div v-if="productFieldsVisibility.external_code && product?.external_code" class="flex items-center gap-2">
                        <span class="font-medium text-gray-600">{{ t('BalancesPage_53') }}</span> <!-- Внешний код: -->
                        <span class="text-gray-900">{{ product.external_code }}</span>
                      </div>
                      <div v-if="productFieldsVisibility.unit && product?.unit" class="flex items-center gap-2">
                        <span class="font-medium text-gray-600">{{ t('BalancesPage_54') }}</span> <!-- Единица измерения: -->
                        <span class="text-gray-900">{{ product.unit }}</span>
                      </div>
                      <div v-if="productFieldsVisibility.weight && product?.weight" class="flex items-center gap-2">
                        <span class="font-medium text-gray-600">{{ t('BalancesPage_55') }}</span> <!-- Вес: -->
                        <span class="text-gray-900">{{ product.weight }}</span>
                      </div>
                      <div v-if="productFieldsVisibility.volume && product?.volume" class="flex items-center gap-2">
                        <span class="font-medium text-gray-600">{{ t('BalancesPage_56') }}</span> <!-- Объем: -->
                        <span class="text-gray-900">{{ product.volume }}</span>
                      </div>
                      <div v-if="productFieldsVisibility.vat && product?.vat" class="flex items-center gap-2">
                        <span class="font-medium text-gray-600">{{ t('BalancesPage_57') }}</span> <!-- Ставка НДС: -->
                        <span class="text-gray-900">{{ product.vat }}</span>
                      </div>
                      <div v-if="productFieldsVisibility.min_stock && product?.min_stock" class="flex items-center gap-2">
                        <span class="font-medium text-gray-600">{{ t('BalancesPage_58') }}</span> <!-- Минимальный остаток: -->
                        <span class="text-gray-900">{{ product.min_stock }}</span>
                      </div>
                      <div v-if="productFieldsVisibility.stock_type && product?.stock_type" class="flex items-center gap-2">
                        <span class="font-medium text-gray-600">{{ t('BalancesPage_59') }}</span> <!-- Тип запаса: -->
                        <span class="text-gray-900">{{ product.stock_type }}</span>
                      </div>
                      <div v-if="productFieldsVisibility.packing && product?.packing" class="flex items-center gap-2">
                        <span class="font-medium text-gray-600">{{ t('BalancesPage_60') }}</span> <!-- Упаковка: -->
                        <span class="text-gray-900">{{ product.packing }}</span>
                      </div>
                      <div v-if="productFieldsVisibility.accounting_type && product?.accounting_type" class="flex items-center gap-2">
                        <span class="font-medium text-gray-600">{{ t('BalancesPage_61') }}</span> <!-- Тип учета: -->
                        <span class="text-gray-900">{{ product.accounting_type }}</span>
                      </div>
                      <div v-if="productFieldsVisibility.traceable && product?.traceable" class="flex items-center gap-2">
                        <span class="font-medium text-gray-600">{{ t('BalancesPage_62') }}</span> <!-- Маркируемый: -->
                        <span class="text-gray-900">{{ product.traceable ? t('BalancesPage_70') : t('BalancesPage_71') }}</span> <!-- Да : Нет -->
                      </div>
                      <div v-if="productFieldsVisibility.marking && product?.marking" class="flex items-center gap-2">
                        <span class="font-medium text-gray-600">{{ t('BalancesPage_63') }}</span> <!-- Маркировка: -->
                        <span class="text-gray-900">{{ product.marking }}</span>
                      </div>
                      <div v-if="productFieldsVisibility.product_type && product?.product_type" class="flex items-center gap-2">
                        <span class="font-medium text-gray-600">{{ t('BalancesPage_64') }}</span> <!-- Тип товара: -->
                        <span class="text-gray-900">{{ product.product_type }}</span>
                      </div>
                      <div v-if="productFieldsVisibility.barcode_type && product?.barcode_type" class="flex items-center gap-2">
                        <span class="font-medium text-gray-600">{{ t('BalancesPage_65') }}</span> <!-- Тип штрихкода: -->
                        <span class="text-gray-900">{{ product.barcode_type }}</span>
                      </div>
                      <div v-if="productFieldsVisibility.barcode && product?.barcode" class="flex items-center gap-2">
                        <span class="font-medium text-gray-600">{{ t('BalancesPage_66') }}</span> <!-- Штрихкод: -->
                        <span class="text-gray-900">{{ product.barcode }}</span>
                      </div>
                      <div v-if="productFieldsVisibility.cash_register_tax && product?.cash_register_tax" class="flex items-center gap-2">
                        <span class="font-medium text-gray-600">{{ t('BalancesPage_67') }}</span> <!-- Налог ККМ: -->
                        <span class="text-gray-900">{{ product.cash_register_tax }}</span>
                      </div>
                      <div v-if="productFieldsVisibility.cash_register_type && product?.cash_register_type" class="flex items-center gap-2">
                        <span class="font-medium text-gray-600">{{ t('BalancesPage_68') }}</span> <!-- Тип ККМ: -->
                        <span class="text-gray-900">{{ product.cash_register_type }}</span>
                      </div>
                      <div v-if="productFieldsVisibility.price && product?.price" class="flex items-center gap-2">
                        <span class="font-medium text-gray-600">{{ t('BalancesPage_69') }}</span> <!-- Цена: -->
                        <span class="text-gray-900">{{ formatCurrency(product.price) }}</span>
                      </div>
                      
                      <!-- Кастомные поля -->
                      <template v-for="field in customFields" :key="field.id">
                        <div v-if="customFields && Array.isArray(customFields) && field && typeof field === 'object' && field.field_name && typeof field.field_name === 'string' && getCustomFieldValue(product, field.field_name) !== '-'" class="flex items-center gap-2">
                          <span class="font-medium text-gray-600">{{ field.field_name }}:</span>
                          <span class="text-gray-900">{{ getCustomFieldValue(product, field.field_name) }}</span>
                        </div>
                      </template>
                    </div>
                  </td>
                </tr>
              </template>
            </tbody>
          </table>
        </div>

        <!-- Пагинация -->
        <div v-if="pagination" class="mt-6 flex justify-between items-center">
          <div class="text-sm text-gray-700">
            {{ t('BalancesPage_72') }} {{ pagination.from }}-{{ pagination.to }} {{ t('BalancesPage_73') }} {{ pagination.total }} <!-- Показано : из -->
          </div>
          <div class="flex gap-2">
            <button
              v-if="pagination.prev_page_url"
              @click="loadBalances(pagination.current_page - 1)"
              :disabled="loading"
              class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:bg-gray-100 disabled:text-gray-400 text-sm flex items-center gap-2"
            >
              <Loader2 v-if="loading" class="animate-spin h-4 w-4" />
              <span v-if="loading">{{ t('BalancesPage_74') }}</span> <!-- Загрузка... -->
              <span v-else>{{ t('BalancesPage_75') }}</span> <!-- Назад -->
            </button>
            <button
              v-if="pagination.next_page_url"
              @click="loadBalances(pagination.current_page + 1)"
              :disabled="loading"
              class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:bg-gray-100 disabled:text-gray-400 text-sm flex items-center gap-2"
            >
              <Loader2 v-if="loading" class="animate-spin h-4 w-4" />
              <span v-if="loading">{{ t('BalancesPage_74') }}</span> <!-- Загрузка... -->
              <span v-else>{{ t('BalancesPage_76') }}</span> <!-- Вперед -->
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Модальное окно движения товаров -->
    <MovementsModal
      v-if="showMovementsModal"
      :product-id="selectedProductId"
      :warehouse-id="selectedWarehouseId"
      @close="showMovementsModal = false"
    />
    
    <!-- Модальное окно подтверждения удаления -->
    <div v-if="showDeleteModal" class="fixed inset-0 z-[99999999] flex items-center justify-center bg-white/90 bg-opacity-50">
      <div class="bg-white rounded-lg shadow-lg w-full max-w-md mx-4 p-6">
        <div class="flex items-center mb-4">
          <div class="flex-shrink-0">
            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
              <Trash2 class="w-6 h-6 text-red-600" />
            </div>
          </div>
          <div class="ml-4">
            <h3 class="text-lg font-semibold text-gray-900">{{ t('BalancesPage_77') }}</h3> <!-- Удалить товар? -->
            <p class="text-sm text-gray-500">{{ t('BalancesPage_78') }}</p> <!-- Это действие нельзя отменить. Товар будет удален навсегда. -->
          </div>
        </div>
        <div class="flex gap-3">
          <button 
            @click="closeDeleteModal" 
            :disabled="deletingProductId !== null"
            class="flex-1 bg-gray-100 hover:bg-gray-200 disabled:bg-gray-50 disabled:cursor-not-allowed text-gray-800 font-semibold px-4 py-2 rounded-lg transition-colors text-sm cursor-pointer"
          >
            {{ t('BalancesPage_79') }} <!-- Отмена -->
          </button>
          <button 
            @click="confirmDelete" 
            :disabled="deletingProductId !== null"
            class="flex-1 bg-red-600 hover:bg-red-700 disabled:bg-red-400 disabled:cursor-not-allowed text-white font-semibold px-4 py-2 rounded-lg transition-colors flex items-center justify-center gap-2 cursor-pointer text-sm"
          >
            <Loader2 v-if="deletingProductId !== null" class="w-4 h-4 animate-spin" />
            {{ deletingProductId !== null ? t('BalancesPage_80') : t('BalancesPage_81') }} <!-- Удаление... : Удалить -->
          </button>
        </div>
      </div>
    </div>

    <!-- Модальное окно импорта товаров -->
    <div v-if="showImportModal" class="fixed inset-0 z-[99999999] flex items-center justify-center bg-white/90 bg-opacity-50">
      <div class="bg-white rounded-lg shadow-lg w-full max-w-6xl mx-4 p-6 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-xl font-bold">{{ t('BalancesPage_82') }}</h2> <!-- Импорт начальных остатков -->
          <button @click="closeImportModal" class="text-gray-400 hover:text-gray-700 p-1 rounded hover:bg-gray-100 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <!-- Загрузка файла -->
        <div v-if="!parsedProducts.length" class="mb-6">
          <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center">
            <div class="mb-4">
              <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
            </div>
            <div class="mb-4">
              <label for="file-upload" class="cursor-pointer bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-lg transition-colors">
                {{ t('BalancesPage_83') }} <!-- Выбрать Excel файл -->
              </label>
              <input 
                id="file-upload" 
                type="file" 
                accept=".xlsx,.xls" 
                @change="handleFileUpload" 
                class="hidden"
              />
            </div>
            <p class="text-sm text-gray-500">{{ t('BalancesPage_84') }}</p> <!-- Поддерживаются файлы .xlsx и .xls -->
            <div class="text-xs text-gray-600 mt-4 space-y-2">
              <p><strong>{{ t('BalancesPage_85') }}</strong> {{ t('BalancesPage_86') }}</p> <!-- Обязательные поля: : Наименование, Стоимость -->
              <p v-if="areCategoriesEnabled()"><strong>{{ t('BalancesPage_87') }}</strong> {{ t('BalancesPage_88') }}</p> <!-- Дополнительные поля: : Категория, Подкатегория, Артикул, Единица измерения, Начальный остаток. -->
              <p v-else><strong>{{ t('BalancesPage_87') }}</strong> {{ t('BalancesPage_89') }}</p> <!-- Дополнительные поля: : Артикул, Единица измерения, Начальный остаток. -->
              <p class="text-gray-500">{{ t('BalancesPage_90') }}</p> <!-- Поддерживаемые названия колонок: -->
              <p v-if="areCategoriesEnabled()" class="text-gray-500 text-xs">• {{ t('BalancesPage_91') }} "{{ t('BalancesPage_92') }}"</p> <!-- Категория: : Категория товара -->
              <p class="text-gray-500 text-xs">• {{ t('BalancesPage_93') }} "{{ t('BalancesPage_94') }}", "{{ t('BalancesPage_95') }}"</p> <!-- Единица измерения: : Ед. изм. : Единица -->
              <p class="text-gray-500">{{ t('BalancesPage_96') }}</p> <!-- Если в файле есть эти колонки - они будут автоматически заполнены при загрузке -->
              <p class="text-gray-500">{{ t('BalancesPage_97') }}</p> <!-- Остальные поля можно будет заполнить после загрузки файла в форме на сайте -->
            </div>
          </div>
        </div>

        <!-- Ошибка импорта -->
        <div v-if="importError" class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
          <p class="text-red-700 text-sm">{{ importError }}</p>
        </div>

        <!-- Загрузка -->
        <div v-if="importLoading" class="text-center py-8">
          <Loader2 class="animate-spin h-8 w-8 text-blue-500 mx-auto mb-4" />
          <p class="text-gray-600">Обработка файла...</p>
        </div>

        <!-- Таблица с товарами -->
        <div v-if="parsedProducts.length && !importLoading" class="mb-6">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold">{{ t('BalancesPage_98') }} {{ parsedProducts.length }}</h3> <!-- Найдено товаров: -->
            <div class="flex items-center gap-4">
              <div class="flex items-center gap-2">
                <label class="text-sm font-medium text-gray-700">{{ t('BalancesPage_99') }}</label> <!-- Склад: -->
                <div class="relative">
                  <Multiselect
                    v-model="selectedWarehouseForImport"
                    :options="warehouseOptions"
                    label="label"
                    value="value"
                    :object="true"
                    :placeholder="t('BalancesPage_100')"
                    :max-height="200"
                    :disabled="loadingWarehouses"
                    class="w-64 min-w-[200px] text-xs multiselect-custom"
                  />
                  <div v-if="loadingWarehouses" class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-75 rounded">
                    <Loader2 class="w-4 h-4 animate-spin text-blue-500" />
                  </div>
                </div>
              </div>
              <button 
                @click="saveImportedProducts" 
                :disabled="importSaving || !selectedWarehouseForImport"
                class="bg-green-600 hover:bg-green-700 disabled:bg-green-400 text-white font-semibold px-6 py-2 rounded-lg transition-colors flex items-center gap-2 text-sm"
              >
                <Loader2 v-if="importSaving" class="animate-spin h-4 w-4" />
                {{ importSaving ? t('BalancesPage_101') : t('BalancesPage_102') }} <!-- Сохранение... : Сохранить начальные остатки -->
              </button>
            </div>
          </div>
          
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-3 py-2 text-left text-sm font-medium text-gray-500 whitespace-nowrap">{{ t('BalancesPage_103') }}</th> <!-- Наименование -->
                  <th v-if="areCategoriesEnabled()" class="px-3 py-2 text-left text-sm font-medium text-gray-500 whitespace-nowrap">{{ t('BalancesPage_21') }}</th> <!-- Категория -->
                  <th v-if="areCategoriesEnabled()" class="px-3 py-2 text-left text-sm font-medium text-gray-500 whitespace-nowrap">{{ t('BalancesPage_23') }}</th> <!-- Подкатегория -->
                  <th class="px-3 py-2 text-left text-sm font-medium text-gray-500 whitespace-nowrap">{{ t('BalancesPage_104') }}</th> <!-- Остаток -->
                  <th v-if="productFieldsVisibility.price !== false" class="px-3 py-2 text-left text-sm font-medium text-gray-500 whitespace-nowrap">{{ t('BalancesPage_105') }}</th> <!-- Цена -->
                  <th class="px-3 py-2 text-left text-sm font-medium text-gray-500 whitespace-nowrap">{{ t('BalancesPage_106') }}</th> <!-- Ед.изм -->
                  <th v-if="productFieldsVisibility.article === true" class="px-3 py-2 text-left text-sm font-medium text-gray-500 whitespace-nowrap">{{ t('BalancesPage_107') }}</th> <!-- Артикул -->
                  
                  <!-- Кастомные поля в заголовке -->
                  <th v-for="field in customFields" :key="field.id" class="px-3 py-2 text-left text-sm font-medium text-gray-500 whitespace-nowrap">
                    {{ field.field_name }}
                  </th>
                  
                  <th class="px-3 py-2 text-left text-sm font-medium text-gray-500 whitespace-nowrap">{{ t('BalancesPage_42') }}</th> <!-- Действия -->
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="(product, index) in parsedProducts" :key="index" class="hover:bg-gray-50">
                  <td class="px-3 py-2 text-sm text-gray-900 whitespace-nowrap">
                    <input v-model="product.name" type="text" class="w-full sm:w-32 text-sm border border-gray-300 rounded px-2 py-1" :placeholder="t('BalancesPage_103')" />
                  </td>
                  
                  <td v-if="areCategoriesEnabled()" class="px-3 py-2 text-sm text-gray-900 whitespace-nowrap">
                    <Multiselect 
                      v-model="product.selectedCategory"
                      :options="categoryOptions"
                      label="label"
                      value="value"
                      :object="true"
                      :placeholder="t('BalancesPage_22')"
                      :max-height="200"
                      class="w-32 text-xs multiselect-custom"
                      @update:model-value="(val) => handleCategoryChange(product, val)"
                    />
                  </td>
                  
                  <td v-if="areCategoriesEnabled()" class="px-3 py-2 text-sm text-gray-900 whitespace-nowrap">
                    <Multiselect 
                      v-model="product.selectedSubcategory"
                      :options="product.subcategoryOptions"
                      label="label"
                      value="value"
                      :object="true"
                      :placeholder="t('BalancesPage_24')"
                      :max-height="200"
                      :disabled="!product.selectedCategory"
                      class="w-32 text-xs multiselect-custom"
                    />
                  </td>
                  
                  <td class="px-3 py-2 text-sm text-gray-900 whitespace-nowrap">
                    <input v-model.number="product.quantity" type="number" min="0" step="0.01" class="w-20 text-sm border border-gray-300 rounded px-2 py-1" />
                  </td>
                  
                  <td v-if="productFieldsVisibility.price !== false" class="px-3 py-2 text-sm text-gray-900 whitespace-nowrap">
                    <input v-model.number="product.price" type="number" min="0" step="0.01" class="w-20 text-sm border border-gray-300 rounded px-2 py-1" />
                  </td>
                  
                  <td class="px-3 py-2 text-sm text-gray-900 whitespace-nowrap">
                    <Multiselect
                      v-model="product.unit"
                      :options="[
                        { label: t('BalancesPage_109'), value: t('BalancesPage_109') },
                        { label: t('BalancesPage_110'), value: t('BalancesPage_110') },
                        { label: t('BalancesPage_111'), value: t('BalancesPage_111') },
                        { label: t('BalancesPage_112'), value: t('BalancesPage_112') },
                        { label: t('BalancesPage_113'), value: t('BalancesPage_113') },
                        { label: t('BalancesPage_114'), value: t('BalancesPage_114') },
                        { label: t('BalancesPage_115'), value: t('BalancesPage_115') },
                        { label: t('BalancesPage_116'), value: t('BalancesPage_116') },
                        { label: t('BalancesPage_117'), value: t('BalancesPage_117') },
                        { label: t('BalancesPage_118'), value: t('BalancesPage_118') },
                        { label: t('BalancesPage_119'), value: t('BalancesPage_119') },
                        { label: t('BalancesPage_120'), value: t('BalancesPage_120') },
                        { label: t('BalancesPage_121'), value: t('BalancesPage_121') },
                        { label: t('BalancesPage_122'), value: t('BalancesPage_122') },
                        { label: t('BalancesPage_123'), value: t('BalancesPage_123') },
                        { label: t('BalancesPage_124'), value: t('BalancesPage_124') },
                        { label: t('BalancesPage_125'), value: t('BalancesPage_125') },
                        { label: t('BalancesPage_126'), value: t('BalancesPage_126') },
                        { label: t('BalancesPage_127'), value: t('BalancesPage_127') }
                      ]"
                      label="label"
                      value="value"
                      :object="true"
                      :placeholder="t('BalancesPage_108')"
                      :max-height="200"
                      class="w-24 text-xs multiselect-custom"
                    />
                  </td>
                  
                  <td v-if="productFieldsVisibility.article === true" class="px-3 py-2 text-sm text-gray-900 whitespace-nowrap">
                    <input v-model="product.article" type="text" class="w-24 text-sm border border-gray-300 rounded px-2 py-1" :placeholder="t('BalancesPage_107')" />
                  </td>
                  
                  <!-- Кастомные поля в строках -->
                  <td v-for="field in customFields" :key="field.id" class="px-3 py-2 text-sm text-gray-900 whitespace-nowrap">
                    <!-- Текстовое поле -->
                    <input 
                      v-if="field.field_type === 'text'" 
                      v-model="product.customFields[field.field_name]" 
                      type="text" 
                      class="w-24 text-sm border border-gray-300 rounded px-2 py-1" 
                    />
                    <!-- Числовое поле -->
                    <input 
                      v-else-if="field.field_type === 'number'" 
                      v-model.number="product.customFields[field.field_name]" 
                      type="number" 
                      step="0.01"
                      class="w-20 text-sm border border-gray-300 rounded px-2 py-1" 
                    />
                    <!-- Поле даты -->
                    <div v-else-if="field.field_type === 'date'" class="w-32">
                      <LocalizedDatePicker 
                        v-model="product.customFields[field.field_name]"
                        :enable-time-picker="false"
                        :auto-apply="true"
                      />
                    </div>
                    <!-- Поле списка -->
                    <Multiselect
                      v-else-if="field.field_type === 'list'"
                      v-model="product.customFields[field.field_name]"
                      :options="getListOptionsForMultiselect(field)"
                      label="label"
                      value="value"
                      :object="false"
                      placeholder="Выберите"
                      :max-height="200"
                      class="w-24 text-xs multiselect-custom"
                    />
                    <!-- По умолчанию текстовое поле -->
                    <input 
                      v-else 
                      v-model="product.customFields[field.field_name]" 
                      type="text" 
                      class="w-24 text-sm border border-gray-300 rounded px-2 py-1" 
                    />
                  </td>
                  
                  <td class="px-3 py-2 text-sm text-gray-900 whitespace-nowrap">
                    <button 
                      @click="parsedProducts.splice(index, 1)" 
                      class="text-red-600 hover:text-red-800 p-1 rounded hover:bg-red-50 transition-colors"
                    >
                      <Trash2 class="w-4 h-4" />
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, reactive, onMounted, computed, watch } from 'vue'
import api, { apiRequest, getCategoriesByUserSettings } from '@/config/api'
import { transformCategoriesToOptions, transformSubcategoriesToOptions } from '@/utils/categoryDisplayUtils'
import ProductsMenu from './ProductsMenu.vue'
import MovementsModal from './MovementsModal.vue'
import Multiselect from '@vueform/multiselect'
import '@vueform/multiselect/themes/default.css'
import { Filter, FunnelX, Loader2, ArrowRightLeft, Edit, Trash2, Plus } from 'lucide-vue-next'
import * as XLSX from 'xlsx'
import { areCategoriesEnabled, isFieldRequired } from '@/utils/productFieldsUtils'
import { getSubcategoriesApiEndpoint } from '@/utils/categoryTypeUtils'
import { t } from '@/locales'

// Импорт для уведомлений
const showNotification = (message, type = 'success') => {
  if (window.toastr) {
    window.toastr[type](message)
  } else {
    // Fallback на alert если toastr не доступен
    alert(message)
  }
}

export default {
  name: 'BalancesPage',
  components: {
    ProductsMenu,
    MovementsModal,
    Multiselect,
    Filter,
    FunnelX,
    Loader2,
    ArrowRightLeft,
    Edit,
    Trash2,
    Plus
  },
  setup() {
    const balances = ref([])
    const warehouses = ref([])
    const summary = ref(null)
    const pagination = ref(null)
    const loading = ref(false)
    const loadingSummary = ref(false)
    const currency = ref('UZS') // по умолчанию UZS

    const filters = reactive({
      warehouse_id: '',
      search: '',
      min_quantity: '',
      max_quantity: ''
    })

    const showMovementsModal = ref(false)
    const selectedProductId = ref(null)
    const selectedWarehouseId = ref(null)
    const loadingWarehouses = ref(false)
    const showFilters = ref(false)
    
    // Состояния для удаления товара
    const showDeleteModal = ref(false)
    const productIdToDelete = ref(null)
    const deletingProductId = ref(null)
    
    // Состояния для настроек полей товара
    const productFieldsVisibility = reactive({})
    const customFields = ref([])
    const loadingProductFields = ref(true)
    
    // Состояния для категорий и подкатегорий
    const categoryOptions = ref([])
    const subcategoryOptions = ref([])
    const selectedCategory = ref(null)
    const selectedSubcategory = ref(null)
    const loadingCategories = ref(false)
    const loadingSubcategories = ref(false)
    
    // Состояния для импорта и экспорта
    const showImportModal = ref(false)
    const importLoading = ref(false)
    const exportLoading = ref(false)
    const importError = ref('')
    const importFile = ref(null)
    const parsedProducts = ref([])
    const importSaving = ref(false)
    const selectedWarehouseForImport = ref(null)
    const categoriesData = ref([])

    // Список стандартных необязательных полей products_sklad
    const standardProductFields = [
      { key: 'description', label: t('BalancesPage_48').replace(':', '') }, // Описание
      { key: 'country', label: t('BalancesPage_49').replace(':', '') }, // Страна
      { key: 'supplier', label: t('BalancesPage_50').replace(':', '') }, // Поставщик
      { key: 'article', label: t('BalancesPage_51').replace(':', '') }, // Артикул
      { key: 'code', label: t('BalancesPage_52').replace(':', '') }, // Код
      { key: 'external_code', label: t('BalancesPage_53').replace(':', '') }, // Внешний код
      { key: 'unit', label: t('BalancesPage_54').replace(':', '') }, // Единица измерения
      { key: 'weight', label: t('BalancesPage_55').replace(':', '') }, // Вес
      { key: 'volume', label: t('BalancesPage_56').replace(':', '') }, // Объем
      { key: 'vat', label: t('BalancesPage_57').replace(':', '') }, // Ставка НДС
      { key: 'min_stock', label: t('BalancesPage_58').replace(':', '') }, // Минимальный остаток
      { key: 'stock_type', label: t('BalancesPage_59').replace(':', '') }, // Тип запаса
      { key: 'packing', label: t('BalancesPage_60').replace(':', '') }, // Упаковка
      { key: 'accounting_type', label: t('BalancesPage_61').replace(':', '') }, // Тип учета
      { key: 'traceable', label: t('BalancesPage_62').replace(':', '') }, // Маркируемый
      { key: 'marking', label: t('BalancesPage_63').replace(':', '') }, // Маркировка
      { key: 'product_type', label: t('BalancesPage_64').replace(':', '') }, // Тип товара
      { key: 'barcode_type', label: t('BalancesPage_65').replace(':', '') }, // Тип штрихкода
      { key: 'barcode', label: t('BalancesPage_66').replace(':', '') }, // Штрихкод
      { key: 'cash_register_tax', label: t('BalancesPage_67').replace(':', '') }, // Налог ККМ
      { key: 'cash_register_type', label: t('BalancesPage_68').replace(':', '') }, // Тип ККМ
      { key: 'price', label: t('BalancesPage_69').replace(':', '') }, // Цена
    ]

    // Computed свойства
    const warehouseOptions = computed(() => {
      return warehouses.value.map(w => ({
        label: w.name,
        value: w.id
      }))
    })

    const loadBalances = async (page = 1) => {
      loading.value = true
      try {
        const requestData = { page, ...filters }
        const response = await api.post('/balances', requestData)
        balances.value = response.data.data
        pagination.value = response.data
      } catch (error) {
        console.error('Ошибка загрузки остатков:', error)
      } finally {
        loading.value = false
      }
    }

    const loadWarehouses = async () => {
      try {
        loadingWarehouses.value = true
        const response = await api.get('/warehouses')
        warehouses.value = response.data.data || []
      } catch (error) {
        console.error('Ошибка загрузки складов:', error)
      } finally {
        loadingWarehouses.value = false
      }
    }

    const loadCategories = async () => {
      loadingCategories.value = true
      try {
        console.log('Загружаем категории...')
        const categoriesData = await getCategoriesByUserSettings()
        console.log('Получены категории:', categoriesData)
        
        // Проверяем структуру данных
        if (Array.isArray(categoriesData)) {
          categoryOptions.value = transformCategoriesToOptions(categoriesData)
          console.log('Категории обработаны:', categoryOptions.value)
        } else {
          console.error('Неверная структура данных категорий:', categoriesData)
          categoryOptions.value = []
        }
      } catch (error) {
        console.error('Ошибка загрузки категорий:', error)
        categoryOptions.value = []
      } finally {
        loadingCategories.value = false
      }
    }

    const loadSubcategories = async (categoryId) => {
      console.log('=== loadSubcategories called ===')
      console.log('CategoryId parameter:', categoryId)
      console.log('Type of categoryId:', typeof categoryId)
      
      if (!categoryId) {
        console.log('CategoryId is empty, clearing subcategories')
        subcategoryOptions.value = []
        return
      }
      loadingSubcategories.value = true
      try {
        console.log('Загружаем подкатегории для категории:', categoryId)
        // Используем правильный endpoint в зависимости от типа категорий
        const endpoint = getSubcategoriesApiEndpoint(categoryId)
        console.log('Используем endpoint:', endpoint)
        const res = await apiRequest(endpoint)
        console.log('Ответ API подкатегорий:', res)
        
        if (res.ok && res.data && res.data.data && Array.isArray(res.data.data)) {
          subcategoryOptions.value = transformSubcategoriesToOptions(res.data.data)
          console.log('Подкатегории обработаны:', subcategoryOptions.value)
        } else {
          console.error('Неверная структура данных подкатегорий:', res.data)
          subcategoryOptions.value = []
        }
      } catch (error) {
        console.error('Ошибка загрузки подкатегорий:', error)
        subcategoryOptions.value = []
      } finally {
        loadingSubcategories.value = false
      }
    }

    const loadSummary = async () => {
      loadingSummary.value = true
      try {
        const response = await api.post('/balances/summary', filters)
        summary.value = response.data.summary
        currency.value = response.data.currency || 'UZS'
      } catch (error) {
        console.error('Ошибка загрузки сводки:', error)
      } finally {
        loadingSummary.value = false
      }
    }

    const clearFilters = () => {
      Object.keys(filters).forEach(key => {
        filters[key] = ''
      })
      selectedCategory.value = null
      selectedSubcategory.value = null
      subcategoryOptions.value = []
      loadBalances()
      loadSummary() // Обновляем сводку вместо уничтожения
    }

    const toggleFilters = () => {
      showFilters.value = !showFilters.value
    }

    const viewMovements = (productId) => {
      selectedProductId.value = productId
      selectedWarehouseId.value = null // Теперь показываем движения по всем складам
      showMovementsModal.value = true
    }

    const formatCurrency = (amount) => {
      if (!amount) return '0'
      return new Intl.NumberFormat('ru-RU', {
        style: 'currency',
        currency: currency.value
      }).format(amount)
    }

    const getQuantityClass = (quantity) => {
      if (quantity === 0) return 'text-red-600 font-semibold'
      if (quantity <= 10) return 'text-orange-600 font-semibold'
      return 'text-green-600 font-semibold'
    }

    const getStatusClass = (quantity) => {
      if (quantity === 0) return 'bg-red-100 text-red-800'
      if (quantity <= 10) return 'bg-orange-100 text-orange-800'
      return 'bg-green-100 text-green-800'
    }

    const getStatusText = (quantity) => {
      if (quantity === 0) return t('BalancesPage_128') // Нет в наличии
      if (quantity <= 10) return t('BalancesPage_129') // Низкий остаток
      return t('BalancesPage_130') // В наличии
    }
    
    // Загрузка настроек полей товара
    const loadProductFieldsVisibilityAndCustomFields = async () => {
      loadingProductFields.value = true
      try {
        // Загрузка стандартных полей
        const userResponse = await api.get('/user/settings')
        console.log('Ответ настроек пользователя:', userResponse.data)
        if (userResponse.data.success && userResponse.data.data.product_fields_visibility) {
          Object.assign(productFieldsVisibility, userResponse.data.data.product_fields_visibility)
          console.log('Настройки видимости полей загружены:', productFieldsVisibility)
          console.log('Значение article:', productFieldsVisibility.article)
        }
        
        // Загрузка кастомных полей
        const customFieldsResponse = await api.get('/product-fields')
        if (customFieldsResponse.data.success) {
          // Фильтруем только валидные поля с field_name
          const validFields = (customFieldsResponse.data.data || []).filter(field => 
            field && field.field_name && typeof field.field_name === 'string'
          )
          customFields.value = validFields
        }
      } catch (error) {
        console.error('Ошибка загрузки настроек полей:', error)
      } finally {
        loadingProductFields.value = false
      }
    }
    
    // Функции для удаления товара
    const openDeleteModal = (productId) => {
      productIdToDelete.value = productId
      showDeleteModal.value = true
    }
    
    const closeDeleteModal = () => {
      showDeleteModal.value = false
      productIdToDelete.value = null
      deletingProductId.value = null
    }
    
    const confirmDelete = async () => {
      if (!productIdToDelete.value) return
      
      deletingProductId.value = productIdToDelete.value
      
      try {
        const response = await api.delete(`/products/${productIdToDelete.value}`)
        
        if (response.data.success) {
          // Удаляем товар из списка остатков
          balances.value = balances.value.filter(product => 
            product.id !== productIdToDelete.value
          )
          
          showNotification(t('BalancesPage_135'), 'success') // Товар успешно удален
          closeDeleteModal()
        } else {
          showNotification(response.data.message || t('BalancesPage_134'), 'error') // Ошибка при удалении товара
        }
              } catch (err) {
          console.error('Ошибка удаления товара:', err)
          showNotification(t('BalancesPage_134'), 'error') // Ошибка при удалении товара
        } finally {
        deletingProductId.value = null
      }
    }

    // Функция для получения значения кастомного поля
    const getCustomFieldValue = (product, fieldKey) => {
      if (!product || !product.fields || !fieldKey || typeof fieldKey !== 'string') return '-'
      
      try {
        const fields = typeof product.fields === 'string' ? JSON.parse(product.fields) : product.fields
        
        // Проверяем, что fields является объектом
        if (!fields || typeof fields !== 'object') return '-'
        
        const value = fields[fieldKey]
        
        // Проверяем, что значение не пустое
        if (value === null || value === undefined || value === '' || value === '-') {
          return '-'
        }
        
        // Если значение является объектом (для обратной совместимости со старыми данными)
        if (typeof value === 'object' && value !== null) {
          // Если у объекта есть свойство value, используем его
          if (value.value !== undefined) {
            return String(value.value)
          }
          // Если у объекта есть свойство label, используем его
          if (value.label !== undefined) {
            return String(value.label)
          }
          // Если у объекта есть свойство name, используем его
          if (value.name !== undefined) {
            return String(value.name)
          }
          // Если ничего не подходит, возвращаем JSON строку
          return JSON.stringify(value)
        }
        
        return String(value)
      } catch (error) {
        console.error('Ошибка парсинга кастомных полей:', error)
        return '-'
      }
    }

    // Watchers для категорий и подкатегорий
    watch(selectedCategory, (val) => {
      console.log('=== Category Selection Debug ===')
      console.log('Selected category object:', val)
      console.log('Category value:', val ? val.value : null)
      console.log('Category category_id:', val ? val.category_id : null)
      
      filters.category = val ? val.value : null
      selectedSubcategory.value = null
      filters.subcategory = null
      loadSubcategories(val ? val.value : null)
    })

    watch(selectedSubcategory, (val) => {
      filters.subcategory = val ? val.subcategory_id : null
    })

    // Функции для импорта и экспорта
    const openImportModal = async () => {
      showImportModal.value = true
      parsedProducts.value = []
      importError.value = ''
      importFile.value = null
      
      // Отладочная информация о настройках полей
      console.log('Настройки полей при открытии модалки:', productFieldsVisibility)
      console.log('Значение article при открытии модалки:', productFieldsVisibility.article)
      
      // Загружаем категории если еще не загружены
      if (!categoryOptions.value || categoryOptions.value.length === 0) {
        try {
          await loadCategories()
        } catch (error) {
          console.error('Ошибка загрузки категорий при открытии модалки:', error)
        }
      }
      
      // Загружаем полные данные категорий для поиска
      try {
        categoriesData.value = await getCategoriesByUserSettings()
        console.log('Категории загружены для импорта:', categoriesData.value)
      } catch (error) {
        console.error('Ошибка загрузки полных данных категорий:', error)
        categoriesData.value = []
      }
      
      // Загружаем склады если еще не загружены
      if (!warehouses.value || warehouses.value.length === 0) {
        try {
          await loadWarehouses()
        } catch (error) {
          console.error('Ошибка загрузки складов при открытии модалки:', error)
        }
      }
    }

    const closeImportModal = () => {
      showImportModal.value = false
      parsedProducts.value = []
      importError.value = ''
      importFile.value = null
      importLoading.value = false
      importSaving.value = false
      selectedWarehouseForImport.value = null
    }

    const handleFileUpload = async (event) => {
      const file = event.target.files[0]
      if (!file) return

      importError.value = ''
      importLoading.value = true
      importFile.value = file

      try {
        // Проверяем расширение файла
        const fileName = file.name.toLowerCase()
        if (!fileName.endsWith('.xlsx') && !fileName.endsWith('.xls')) {
          throw new Error(t('BalancesPage_139')) // Поддерживаются только файлы .xlsx и .xls
        }

        // Читаем файл
        const arrayBuffer = await file.arrayBuffer()
        const workbook = XLSX.read(arrayBuffer, { type: 'array' })
        
        // Получаем первый лист
        const sheetName = workbook.SheetNames[0]
        const worksheet = workbook.Sheets[sheetName]
        
        // Конвертируем в JSON
        const jsonData = XLSX.utils.sheet_to_json(worksheet, { header: 1 })
        
        if (jsonData.length < 2) {
          throw new Error(t('BalancesPage_140')) // Файл не содержит данных
        }

        // Получаем заголовки (первая строка)
        const headers = jsonData[0]
        
        // Проверяем обязательные колонки (поддерживаем все языки)
        const nameColumns = ['Название', 'Name', 'Nomi', '名称'] // Название на всех языках
        const costColumns = ['Стоимость', 'Cost', 'Narxi', '成本'] // Стоимость на всех языках
        
        const hasNameColumn = nameColumns.some(col => headers.includes(col))
        const hasCostColumn = costColumns.some(col => headers.includes(col))
        
        const missingColumns = []
        if (!hasNameColumn) missingColumns.push(t('BalancesPage_143'))
        if (!hasCostColumn) missingColumns.push(t('BalancesPage_144'))
        
        if (missingColumns.length > 0) {
          throw new Error(`${t('BalancesPage_141')} ${missingColumns.join(', ')}`) // Отсутствуют обязательные колонки:
        }

        // Парсим данные
        const products = []
        for (let i = 1; i < jsonData.length; i++) {
          const row = jsonData[i]
          if (row.length === 0) continue // Пропускаем пустые строки
          
          const product = {}
          headers.forEach((header, index) => {
            product[header] = row[index] || ''
          })
          
          // Проверяем обязательные поля (поддерживаем все языки)
          const productName = product['Название'] || product['Name'] || product['Nomi'] || product['名称'] || product[t('BalancesPage_143')]
          const productCost = product['Стоимость'] || product['Cost'] || product['Narxi'] || product['成本'] || product[t('BalancesPage_144')]
          
          if (!productName || !productCost) { // Название, Стоимость
            continue // Пропускаем строки без обязательных полей
          }
          
          // Преобразуем данные в нужный формат
          const unitValue = product[t('BalancesPage_147')] || product[t('BalancesPage_148')] || product[t('BalancesPage_149')] || t('BalancesPage_109') // Единица измерения, Ед. изм., Единица, Штука
          const parsedProduct = {
            name: productName?.toString() || '', // Название
            price: parseFloat(productCost) || 0, // Стоимость
            quantity: parseFloat(product[t('BalancesPage_145')] || product[t('BalancesPage_146')] || 0) || 0, // Начальный остаток, Остаток
            unit: { label: unitValue, value: unitValue },
            article: product[t('BalancesPage_107')] || '', // Артикул
            selectedCategory: null,
            selectedSubcategory: null,
            subcategoryOptions: [],
            customFields: {}
          }
          
          // Добавляем категории только если они включены в настройках
          if (areCategoriesEnabled()) {
            // Поддерживаем категории на всех языках
            const categoryColumns = ['Категория', 'Category', 'Kategoriya', '类别', 'Категория товара', 'Product category', 'Tovar kategoriyasi', '商品类别']
            parsedProduct.category = categoryColumns.find(col => product[col]) || product[t('BalancesPage_21')] || product[t('BalancesPage_150')] || ''
          }
          
          products.push(parsedProduct)
        }
        
        if (products.length === 0) {
          throw new Error(t('BalancesPage_142')) // Не найдено товаров для импорта
        }
        
        // Убеждаемся, что категории загружены (только если они включены)
        if (areCategoriesEnabled() && (!categoryOptions.value || !Array.isArray(categoryOptions.value))) {
          console.warn('Категории не загружены, пропускаем автоматическое заполнение категорий')
          // Попробуем загрузить категории
          try {
            await loadCategories()
          } catch (error) {
            console.error('Не удалось загрузить категории:', error)
          }
        }
        
        // Автоматически заполняем категории из Excel файла (только если они включены)
        if (areCategoriesEnabled()) {
          for (const product of products) {
            // Заполняем категорию, если она есть в Excel
            if (product.category) {
              // Сначала ищем в локальном файле
              const localCategory = findCategoryByName(product.category)
              if (localCategory) {
                // Ищем соответствующую категорию в селекте
                const foundCategory = categoryOptions.value.find(cat => 
                  String(cat.value) === String(localCategory.category_id)
                )
                if (foundCategory) {
                  product.selectedCategory = foundCategory;
                  product.subcategoryOptions = transformSubcategoriesToOptions(localCategory.subcategories)
                  if (product.subcategory) {
                    const localSubcategory = findSubcategoryByName(localCategory.category_id, product.subcategory)
                    if (localSubcategory) {
                      const foundSubcategory = product.subcategoryOptions.find(sub => 
                        String(sub.value) === String(localSubcategory.subcategory_id)
                      )
                      if (foundSubcategory) {
                        product.selectedSubcategory = foundSubcategory
                      } else {
                        product.selectedSubcategory = null
                      }
                    } else {
                      product.selectedSubcategory = null
                    }
                  } else {
                    product.selectedSubcategory = null
                  }
                } else {
                  product.selectedCategory = null;
                  product.selectedSubcategory = null;
                  product.subcategoryOptions = [];
                }
              } else {
                // Если не найдено в локальном файле, ищем в селекте (старый способ)
                if (categoryOptions.value && Array.isArray(categoryOptions.value)) {
                  const foundCategory = categoryOptions.value.find(cat => 
                    cat && cat.label && product.category &&
                    (cat.label.toLowerCase() === product.category.toLowerCase() ||
                     cat.label.toLowerCase().includes(product.category.toLowerCase()) ||
                     product.category.toLowerCase().includes(cat.label.toLowerCase()))
                  )
                  if (foundCategory) {
                    product.selectedCategory = foundCategory
                    
                    // Загружаем подкатегории через API
                    await loadSubcategoriesForProduct(product, foundCategory.value)
                    
                    if (product.subcategory) {
                      const foundSubcategory = product.subcategoryOptions.find(sub => 
                        sub && sub.label && product.subcategory &&
                        (sub.label.toLowerCase() === product.subcategory.toLowerCase() ||
                         sub.label.toLowerCase().includes(product.subcategory.toLowerCase()) ||
                         product.subcategory.toLowerCase().includes(sub.label.toLowerCase()))
                      )
                      if (foundSubcategory) {
                        product.selectedSubcategory = foundSubcategory
                      }
                    }
                  }
                }
              }
            }
          }
        }
        
        parsedProducts.value = products
        importError.value = ''
        
      } catch (error) {
        console.error('Ошибка обработки файла:', error)
        importError.value = error.message
        parsedProducts.value = []
      } finally {
        importLoading.value = false
      }
    }

    const saveImportedProducts = async () => {
      if (!selectedWarehouseForImport.value) {
        importError.value = t('BalancesPage_131') // Выберите склад для импорта
        return
      }
      
      importSaving.value = true
      importError.value = ''
      
      try {
        const productsToSave = parsedProducts.value.map(product => {
          const productData = {
            name: product.name,
            price: productFieldsVisibility.price !== false ? product.price : 0,
            start_count: product.quantity || 0, // Используем start_count вместо quantity
            unit: product.unit?.value || product.unit || 'Штука',
            article: product.article,
            warehouse_id: selectedWarehouseForImport.value.value
          }
          
          // Добавляем категории только если они включены
          if (areCategoriesEnabled()) {
            // Передаем ID категорий из выбранных значений
            if (product.selectedCategory && product.selectedCategory.value) {
              productData.category = product.selectedCategory.value
            } else if (product.category && typeof product.category === 'object' && product.category.value) {
              productData.category = product.category.value
            } else if (product.category) {
              productData.category = product.category
            }
            
            if (product.selectedSubcategory && product.selectedSubcategory.value) {
              productData.subcategory = product.selectedSubcategory.value
            } else if (product.subcategory && typeof product.subcategory === 'object' && product.subcategory.value) {
              productData.subcategory = product.subcategory.value
            } else if (product.subcategory) {
              productData.subcategory = product.subcategory
            }
          }
          
          // Добавляем кастомные поля
          if (product.customFields && Object.keys(product.customFields).length > 0) {
            // Фильтруем только непустые значения
            const nonEmptyCustomFields = {}
            Object.keys(product.customFields).forEach(key => {
              const value = product.customFields[key]
              if (value !== null && value !== undefined && value !== '') {
                nonEmptyCustomFields[key] = value
              }
            })
            
            if (Object.keys(nonEmptyCustomFields).length > 0) {
              productData.fields = nonEmptyCustomFields
            }
          }
          
          return productData
        })
        
        const response = await api.post('/products/import-with-receipt', {
          products: productsToSave
        })
        
        if (response.data.success) {
          showNotification(t('BalancesPage_132'), 'success') // Товары успешно импортированы
          closeImportModal()
          loadBalances() // Перезагружаем остатки
        } else {
          importError.value = response.data.message || t('BalancesPage_133') // Ошибка импорта товаров
        }
              } catch (error) {
          console.error('Ошибка импорта:', error)
          importError.value = t('BalancesPage_133') // Ошибка импорта товаров
        } finally {
        importSaving.value = false
      }
    }

    const exportBalances = async () => {
      exportLoading.value = true
      
      try {
        // Получаем все остатки для экспорта (без пагинации)
        const params = new URLSearchParams({
          per_page: '10000' // Получаем все остатки
        })
        
        // Добавляем параметры фильтра
        Object.keys(filters).forEach(key => {
          const value = filters[key]
          if (value !== null && value !== undefined && value !== '') {
            params.append(key, value.toString())
          }
        })
        
        const response = await api.post('/balances', params)
        
        if (response.data.data) {
          const allProducts = response.data.data || []
          
          // Подготавливаем данные для экспорта
          const exportData = allProducts.map(product => {
            const formatQuantity = (qty) => {
              if (qty === null || qty === undefined || qty === '') return '-'
              const num = parseFloat(qty)
              return isNaN(num) ? '-' : num.toString()
            }
            
            const formatPrice = (price) => {
              if (price === null || price === undefined || price === '') return '-'
              const num = parseFloat(price)
              return isNaN(num) ? '-' : num.toString()
            }
            
            // Базовые поля
            const exportRow = {
              [t('BalancesPage_143')]: product.name || '-', // Название
              [t('BalancesPage_21')]: product.category_name || product.category || '-', // Категория
              [t('BalancesPage_23')]: product.subcategory_name || product.subcategory || '-', // Подкатегория
              [t('BalancesPage_151')]: formatQuantity(product.total_quantity), // Общий остаток
              [t('BalancesPage_152')]: product.unit || '-', // Единица измерения
              [t('BalancesPage_144')]: formatPrice(product.price), // Стоимость
              [t('BalancesPage_107')]: product.article || '-' // Артикул
            }
            
            // Добавляем информацию по складам
            if (product.warehouse_balances && Array.isArray(product.warehouse_balances)) {
              product.warehouse_balances.forEach((warehouseBalance, index) => {
                const warehouseName = warehouseBalance.warehouse_name || `${t('BalancesPage_154')} ${warehouseBalance.warehouse_id}` // Склад
                exportRow[`${t('BalancesPage_153')} ${warehouseName}`] = formatQuantity(warehouseBalance.quantity) // Остаток на
              })
            }
            
            // Добавляем дополнительные поля в зависимости от настроек
            standardProductFields.forEach(field => {
              if (productFieldsVisibility[field.key] === true && product[field.key]) {
                exportRow[field.label] = product[field.key] || '-'
              }
            })
            
            // Добавляем кастомные поля
            customFields.value.forEach(field => {
              if (field && field.field_name) {
                const customValue = getCustomFieldValue(product, field.field_name)
                if (customValue !== '-') {
                  exportRow[field.field_name] = customValue
                }
              }
            })
            
            return exportRow
          })
          
          // Создаем рабочую книгу Excel
          const workbook = XLSX.utils.book_new()
          const worksheet = XLSX.utils.json_to_sheet(exportData)
          
          // Устанавливаем ширину столбцов динамически
          const baseColumnWidths = [
            { wch: 30 }, // Название
            { wch: 20 }, // Категория
            { wch: 20 }, // Подкатегория
            { wch: 15 }, // Общий остаток
            { wch: 15 }, // Единица измерения
            { wch: 12 }, // Стоимость
            { wch: 15 }  // Артикул
          ]
          
          // Добавляем ширину для дополнительных полей
          const additionalColumnWidths = standardProductFields
            .filter(field => productFieldsVisibility[field.key] === true)
            .map(() => ({ wch: 15 }))
          
          // Добавляем ширину для кастомных полей
          const customColumnWidths = customFields.value
            .filter(field => field && field.field_name)
            .map(() => ({ wch: 20 }))
          
          const columnWidths = [...baseColumnWidths, ...additionalColumnWidths, ...customColumnWidths]
          worksheet['!cols'] = columnWidths
          
          // Добавляем лист в книгу
          XLSX.utils.book_append_sheet(workbook, worksheet, 'Остатки')
          
          // Генерируем имя файла с текущей датой
          const now = new Date()
          const dateStr = now.toISOString().split('T')[0]
          const timeStr = now.toTimeString().split(' ')[0].replace(/:/g, '-')
          const fileName = `${t('BalancesPage_155')}_${dateStr}_${timeStr}.xlsx` // остатки
          
          // Скачиваем файл
          XLSX.writeFile(workbook, fileName)
          
          showNotification(`${t('BalancesPage_136')} ${exportData.length} ${t('BalancesPage_137')}`, 'success') // Экспортировано : товаров
        }
        
              } catch (error) {
          console.error('Ошибка экспорта:', error)
          showNotification(t('BalancesPage_138'), 'error') // Ошибка экспорта остатков
        } finally {
        exportLoading.value = false
      }
    }

    // Функции для работы с кастомными полями
    const getListOptions = (field) => {
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

    const getListOptionsForMultiselect = (field) => {
      const options = getListOptions(field)
      return options.map(option => ({
        label: option,
        value: option
      }))
    }

    // Функция для загрузки подкатегорий для товара в модалке импорта
    const loadSubcategoriesForProduct = async (product, categoryId) => {
      if (!categoryId) {
        product.subcategoryOptions = []
        product.selectedSubcategory = null
        return
      }
      
      try {
        console.log('Загружаем подкатегории для товара, категория:', categoryId)
        // Используем правильный endpoint в зависимости от типа категорий
        const endpoint = getSubcategoriesApiEndpoint(categoryId)
        console.log('Используем endpoint для товара:', endpoint)
        const res = await apiRequest(endpoint)
        console.log('Ответ API подкатегорий для товара:', res)
        
        if (res.ok && res.data && res.data.data && Array.isArray(res.data.data)) {
          product.subcategoryOptions = transformSubcategoriesToOptions(res.data.data)
          console.log('Подкатегории для товара загружены:', product.subcategoryOptions)
        } else {
          console.error('Неверная структура данных подкатегорий для товара:', res.data)
          product.subcategoryOptions = []
        }
      } catch (error) {
        console.error('Ошибка загрузки подкатегорий для товара:', error)
        product.subcategoryOptions = []
      }
    }

    // Функции для быстрого поиска категории в локальном файле
    const findCategoryByName = (categoryName) => {
      if (!categoryName || !Array.isArray(categoriesData)) return null
      
      const normalizedName = categoryName.toLowerCase().trim()
      
      return categoriesData.find(cat => 
        (cat.name_ru && cat.name_ru.toLowerCase().includes(normalizedName)) ||
        (cat.name && cat.name.toLowerCase().includes(normalizedName)) ||
        (cat.name_en && cat.name_en.toLowerCase().includes(normalizedName)) ||
        (cat.name_uz && cat.name_uz.toLowerCase().includes(normalizedName))
      )
    }

    // Функция для обработки изменения категории в модалке импорта
    const handleCategoryChange = async (product, selectedCategory) => {
      console.log('Изменение категории для товара:', product.name, 'Новая категория:', selectedCategory)
      
      // Сбрасываем подкатегорию при изменении категории
      product.selectedSubcategory = null
      
      if (selectedCategory && selectedCategory.value) {
        // Загружаем подкатегории для выбранной категории
        await loadSubcategoriesForProduct(product, selectedCategory.value)
      } else {
        // Если категория не выбрана, очищаем подкатегории
        product.subcategoryOptions = []
      }
    }

    // Функция для быстрого поиска подкатегории в локальном файле
    const findSubcategoryByName = (categoryId, subcategoryName) => {
      if (!categoryId || !subcategoryName || !Array.isArray(categoriesData)) return null
      
      const category = categoriesData.find(cat => cat.category_id === categoryId)
      if (!category || !Array.isArray(category.subcategories)) return null
      
      const normalizedName = subcategoryName.toLowerCase().trim()
      
      return category.subcategories.find(sub => 
        (sub.name_ru && sub.name_ru.toLowerCase().includes(normalizedName)) ||
        (sub.name && sub.name.toLowerCase().includes(normalizedName)) ||
        (sub.name_en && sub.name_en.toLowerCase().includes(normalizedName)) ||
        (sub.name_uz && sub.name_uz.toLowerCase().includes(normalizedName))
      )
    }

    onMounted(() => {
      loadBalances()
      loadWarehouses()
      loadSummary() // Автоматически загружаем сводку при загрузке страницы
      loadProductFieldsVisibilityAndCustomFields() // Загружаем настройки полей
      loadCategories() // Загружаем категории
    })

    return {
      t,
      getListOptions,
      getListOptionsForMultiselect,
      balances,
      warehouses,
      summary,
      pagination,
      loading,
      loadingSummary,
      filters,
      showFilters,
      showMovementsModal,
      selectedProductId,
      selectedWarehouseId,
      loadingWarehouses,
      warehouseOptions,
      loadBalances,
      loadWarehouses,
      loadSummary,
      clearFilters,
      toggleFilters,
      viewMovements,
      formatCurrency,
      getQuantityClass,
      getStatusClass,
      getStatusText,
      currency,
      productFieldsVisibility,
      loadingProductFields,
      standardProductFields,
      customFields,
      categoryOptions,
      subcategoryOptions,
      selectedCategory,
      selectedSubcategory,
      loadingCategories,
      loadingSubcategories,
      showImportModal,
      importLoading,
      exportLoading,
      importError,
      parsedProducts,
      importSaving,
      selectedWarehouseForImport,
      openImportModal,
      closeImportModal,
      handleFileUpload,
      saveImportedProducts,
      exportBalances,
      showDeleteModal,
      productIdToDelete,
      deletingProductId,
      openDeleteModal,
      closeDeleteModal,
      confirmDelete,
      getCustomFieldValue,
      findCategoryByName,
      findSubcategoryByName,
      handleCategoryChange,
      areCategoriesEnabled,
      isFieldRequired
    }
  }
}
</script> 