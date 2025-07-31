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
              <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Остатки</h1>
              <button
                @click="toggleFilters"
                class="flex items-center gap-2 text-gray-700 font-medium px-4 py-2 rounded text-sm hover:bg-gray-100 transition-colors cursor-pointer group"
              >
                <Filter v-if="!showFilters" class="w-4 h-4" />
                <FunnelX v-else class="w-4 h-4" />
                <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-900 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-10">
                  {{ showFilters ? 'Скрыть фильтры' : 'Показать фильтры' }}
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
                Остаток
                <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-900 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-10">
                  Добавить новый остаток
                  <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
                </div>
              </router-link>
              <button 
                @click="openImportModal"
                :disabled="importLoading"
                class="bg-white border border-gray-300 px-4 py-2 rounded font-medium text-sm hover:bg-gray-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed inline-flex items-center justify-center gap-2 relative group"
              >
                <Loader2 v-if="importLoading" class="w-4 h-4 animate-spin" />
                {{ importLoading ? 'Обработка...' : 'Импорт' }}
                <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-900 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-10">
                  {{ importLoading ? 'Обработка файла...' : 'Импорт остатков из файла Excel' }}
                  <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
                </div>
              </button>
              <button 
                @click="exportBalances"
                :disabled="exportLoading"
                class="bg-white border border-gray-300 px-4 py-2 rounded font-medium text-sm hover:bg-gray-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed inline-flex items-center justify-center gap-2 relative group"
              >
                <Loader2 v-if="exportLoading" class="w-4 h-4 animate-spin" />
                {{ exportLoading ? 'Экспорт...' : 'Экспорт' }}
                <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-900 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-10">
                  {{ exportLoading ? 'Выполняется экспорт...' : 'Экспорт остатков в файл Excel' }}
                  <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
                </div>
              </button>
            </div>
          </div>
          
          <!-- ПК версия: однострочная как раньше -->
          <div class="hidden items-center justify-between sm:flex w-full">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Остатки</h1>
            <div class="flex items-center gap-2">
              <button
                @click="toggleFilters"
                class="flex items-center gap-2 text-gray-700 font-medium px-4 py-2 rounded text-sm hover:bg-gray-100 transition-colors cursor-pointer relative group"
              >
                <Filter v-if="!showFilters" class="w-4 h-4" />
                <FunnelX v-else class="w-4 h-4" />
                <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-900 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-10">
                  {{ showFilters ? 'Скрыть фильтры' : 'Показать фильтры' }}
                  <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
                </div>
              </button>
              <router-link 
                to="/products/create" 
                class="flex items-center gap-2 bg-blue-50 border border-blue-200 text-blue-700 font-medium px-4 py-2 rounded text-sm hover:bg-blue-100 transition-colors relative group"
              >
                <Plus class="w-4 h-4 text-blue-700" />
                Остаток
                <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-900 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-10">
                  Добавить новый остаток
                  <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
                </div>
              </router-link>
              <button 
                @click="openImportModal"
                :disabled="importLoading"
                class="bg-white border border-gray-300 px-4 py-2 rounded font-medium text-sm hover:bg-gray-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed inline-flex items-center justify-center gap-2 relative group"
              >
                <Loader2 v-if="importLoading" class="w-4 h-4 animate-spin" />
                {{ importLoading ? 'Обработка...' : 'Импорт' }}
                <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-900 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-10">
                  {{ importLoading ? 'Обработка файла...' : 'Импорт остатков из файла Excel' }}
                  <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
                </div>
              </button>
              <button 
                @click="exportBalances"
                :disabled="exportLoading"
                class="bg-white border border-gray-300 px-4 py-2 rounded font-medium text-sm hover:bg-gray-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed inline-flex items-center justify-center gap-2 relative group"
              >
                <Loader2 v-if="exportLoading" class="w-4 h-4 animate-spin" />
                {{ exportLoading ? 'Экспорт...' : 'Экспорт' }}
                <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-900 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-10">
                  {{ exportLoading ? 'Выполняется экспорт...' : 'Экспорт остатков в файл Excel' }}
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
            <label class="block text-sm text-gray-700 mb-1">Склад</label>
            <Multiselect
              v-model="filters.warehouse_id"
              :options="warehouseOptions"
              label="label"
              value="value"
              :object="false"
              placeholder="Все склады"
              :max-height="400"
              class="w-full text-sm multiselect-custom"
              :loading="loadingWarehouses"
              :disabled="loadingWarehouses"
            />
          </div>
          <div>
            <label class="block text-sm text-gray-700 mb-1">Поиск товара</label>
            <input
              v-model="filters.search"
              type="text"
              placeholder="Название товара..."
              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm"
            />
          </div>
          <div>
            <label class="block text-sm text-gray-700 mb-1">Мин. остаток</label>
            <input
              v-model.number="filters.min_quantity"
              type="number"
              min="0"
              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm"
            />
          </div>
          <div>
            <label class="block text-sm text-gray-700 mb-1">Макс. остаток</label>
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
          <h3 class="text-lg font-medium text-gray-900 mb-4">Дополнительные фильтры</h3>
          
          <!-- Обязательные поля -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
              <label class="block text-sm text-gray-700 mb-1">Категория</label>
              <Multiselect
                v-model="selectedCategory"
                :options="categoryOptions"
                label="label"
                value="value"
                :object="true"
                placeholder="Выберите категорию"
                searchable
                :max-height="400"
                class="w-full text-sm multiselect-custom"
                :loading="loadingCategories"
              />
            </div>
            <div>
              <label class="block text-sm text-gray-700 mb-1">Подкатегория</label>
              <Multiselect
                v-model="selectedSubcategory"
                :options="subcategoryOptions"
                label="label"
                value="value"
                :object="true"
                placeholder="Выберите подкатегорию"
                searchable
                :max-height="400"
                class="w-full text-sm multiselect-custom"
                :loading="loadingSubcategories"
                :disabled="!selectedCategory"
              />
            </div>
            <div>
              <label class="block text-sm text-gray-700 mb-1">Дата создания</label>
              <input
                v-model="filters.created_at"
                type="date"
                placeholder="дд.мм.гггг"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm"
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
            <span class="ml-2 text-gray-500 text-sm">Загрузка полей...</span>
          </div>
        </div>

        <div class="mt-6 flex gap-2 justify-end">
          <button
            @click="clearFilters"
            :disabled="loading"
            class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm transition"
          >
            Сбросить
          </button>
          <button
            @click="loadBalances"
            :disabled="loading"
            class="bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white px-4 py-2 rounded-lg text-sm transition"
          >
            Применить фильтры
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
              <div class="text-gray-600 text-sm">Загрузка сводки...</div>
            </div>
          </div>
        </div>
        
        <div v-else-if="summary" class="bg-blue-50 rounded-lg p-4 mb-6">
          <!-- <h3 class="text-lg font-medium text-gray-900 mb-4">Сводка по остаткам</h3> -->
          <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
            <div class="text-center">
              <div class="text-2xl font-bold text-blue-600" style="font-size: 20px;">{{ summary.total_products }}</div>
              <div class="text-sm text-gray-600">Товаров</div>
            </div>
            <div class="text-center">
              <div class="text-2xl font-bold text-green-600" style="font-size: 20px;">{{ summary.total_warehouses }}</div>
              <div class="text-sm text-gray-600">Складов</div>
            </div>
            <div class="text-center">
              <div class="text-2xl font-bold text-purple-600" style="font-size: 20px;">{{ summary.total_quantity }}</div>
              <div class="text-sm text-gray-600">Общее количество</div>
            </div>
            <div class="text-center">
              <div class="text-2xl font-bold text-indigo-600" style="font-size: 20px;">{{ formatCurrency(summary.total_value) }}</div>
              <div class="text-sm text-gray-600">Общая стоимость</div>
            </div>
            <div class="text-center">
              <div class="text-2xl font-bold text-orange-600" style="font-size: 20px;">{{ summary.low_stock_items }}</div>
              <div class="text-sm text-gray-600">Низкий остаток</div>
            </div>
            <div class="text-center">
              <div class="text-2xl font-bold text-red-600" style="font-size: 20px;">{{ summary.out_of_stock_items }}</div>
              <div class="text-sm text-gray-600">Нет в наличии</div>
            </div>
          </div>
        </div>

        <!-- Список остатков -->
        <div v-if="loading" class="flex justify-center items-center py-12">
          <div class="text-center">
            <Loader2 class="animate-spin h-8 w-8 text-blue-600 mx-auto mb-4" />
            <div class="text-gray-600 text-sm">Загрузка остатков...</div>
          </div>
        </div>
        
        <div v-else class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">
                  Товар
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">
                  Остаток
                </th>
                <th v-if="productFieldsVisibility.price !== false" class="px-6 py-3 text-left text-xs font-medium text-gray-500">
                  Цена ед/итого
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">
                  Статус
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">
                  Действия
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
                          Движение товара
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
                          Редактировать товар
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
                          Удалить товар
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
                        <span class="font-medium text-gray-600">Категория:</span>
                        <span class="text-gray-900">{{ product.category_name || product.category }}</span>
                      </div>
                      <div v-if="product?.subcategory_name || product?.subcategory" class="flex items-center gap-2">
                        <span class="font-medium text-gray-600">Подкатегория:</span>
                        <span class="text-gray-900">{{ product.subcategory_name || product.subcategory }}</span>
                      </div>
                      
                      <!-- Дополнительные поля (активные) -->
                      <div v-if="productFieldsVisibility.description && balance.product?.description" class="flex items-center gap-2">
                        <span class="font-medium text-gray-600">Описание:</span>
                        <span class="text-gray-900">{{ balance.product.description }}</span>
                      </div>
                      <div v-if="productFieldsVisibility.country && balance.product?.country" class="flex items-center gap-2">
                        <span class="font-medium text-gray-600">Страна:</span>
                        <span class="text-gray-900">{{ balance.product.country }}</span>
                      </div>
                      <div v-if="productFieldsVisibility.supplier && balance.product?.supplier" class="flex items-center gap-2">
                        <span class="font-medium text-gray-600">Поставщик:</span>
                        <span class="text-gray-900">{{ balance.product.supplier }}</span>
                      </div>
                      <div v-if="productFieldsVisibility.article && balance.product?.article" class="flex items-center gap-2">
                        <span class="font-medium text-gray-600">Артикул:</span>
                        <span class="text-gray-900">{{ balance.product.article }}</span>
                      </div>
                      <div v-if="productFieldsVisibility.code && balance.product?.code" class="flex items-center gap-2">
                        <span class="font-medium text-gray-600">Код:</span>
                        <span class="text-gray-900">{{ balance.product.code }}</span>
                      </div>
                      <div v-if="productFieldsVisibility.external_code && balance.product?.external_code" class="flex items-center gap-2">
                        <span class="font-medium text-gray-600">Внешний код:</span>
                        <span class="text-gray-900">{{ balance.product.external_code }}</span>
                      </div>
                      <div v-if="productFieldsVisibility.unit && balance.product?.unit" class="flex items-center gap-2">
                        <span class="font-medium text-gray-600">Единица измерения:</span>
                        <span class="text-gray-900">{{ balance.product.unit }}</span>
                      </div>
                      <div v-if="productFieldsVisibility.weight && balance.product?.weight" class="flex items-center gap-2">
                        <span class="font-medium text-gray-600">Вес:</span>
                        <span class="text-gray-900">{{ balance.product.weight }}</span>
                      </div>
                      <div v-if="productFieldsVisibility.volume && balance.product?.volume" class="flex items-center gap-2">
                        <span class="font-medium text-gray-600">Объем:</span>
                        <span class="text-gray-900">{{ balance.product.volume }}</span>
                      </div>
                      <div v-if="productFieldsVisibility.vat && balance.product?.vat" class="flex items-center gap-2">
                        <span class="font-medium text-gray-600">Ставка НДС:</span>
                        <span class="text-gray-900">{{ balance.product.vat }}</span>
                      </div>
                      <div v-if="productFieldsVisibility.min_stock && balance.product?.min_stock" class="flex items-center gap-2">
                        <span class="font-medium text-gray-600">Минимальный остаток:</span>
                        <span class="text-gray-900">{{ balance.product.min_stock }}</span>
                      </div>
                      <div v-if="productFieldsVisibility.stock_type && balance.product?.stock_type" class="flex items-center gap-2">
                        <span class="font-medium text-gray-600">Тип запаса:</span>
                        <span class="text-gray-900">{{ balance.product.stock_type }}</span>
                      </div>
                      <div v-if="productFieldsVisibility.packing && balance.product?.packing" class="flex items-center gap-2">
                        <span class="font-medium text-gray-600">Упаковка:</span>
                        <span class="text-gray-900">{{ balance.product.packing }}</span>
                      </div>
                      <div v-if="productFieldsVisibility.accounting_type && balance.product?.accounting_type" class="flex items-center gap-2">
                        <span class="font-medium text-gray-600">Тип учета:</span>
                        <span class="text-gray-900">{{ balance.product.accounting_type }}</span>
                      </div>
                      <div v-if="productFieldsVisibility.traceable && balance.product?.traceable" class="flex items-center gap-2">
                        <span class="font-medium text-gray-600">Маркируемый:</span>
                        <span class="text-gray-900">{{ balance.product.traceable ? 'Да' : 'Нет' }}</span>
                      </div>
                      <div v-if="productFieldsVisibility.marking && balance.product?.marking" class="flex items-center gap-2">
                        <span class="font-medium text-gray-600">Маркировка:</span>
                        <span class="text-gray-900">{{ balance.product.marking }}</span>
                      </div>
                      <div v-if="productFieldsVisibility.product_type && balance.product?.product_type" class="flex items-center gap-2">
                        <span class="font-medium text-gray-600">Тип товара:</span>
                        <span class="text-gray-900">{{ balance.product.product_type }}</span>
                      </div>
                      <div v-if="productFieldsVisibility.barcode_type && balance.product?.barcode_type" class="flex items-center gap-2">
                        <span class="font-medium text-gray-600">Тип штрихкода:</span>
                        <span class="text-gray-900">{{ balance.product.barcode_type }}</span>
                      </div>
                      <div v-if="productFieldsVisibility.barcode && balance.product?.barcode" class="flex items-center gap-2">
                        <span class="font-medium text-gray-600">Штрихкод:</span>
                        <span class="text-gray-900">{{ balance.product.barcode }}</span>
                      </div>
                      <div v-if="productFieldsVisibility.cash_register_tax && balance.product?.cash_register_tax" class="flex items-center gap-2">
                        <span class="font-medium text-gray-600">Налог ККМ:</span>
                        <span class="text-gray-900">{{ balance.product.cash_register_tax }}</span>
                      </div>
                      <div v-if="productFieldsVisibility.cash_register_type && balance.product?.cash_register_type" class="flex items-center gap-2">
                        <span class="font-medium text-gray-600">Тип ККМ:</span>
                        <span class="text-gray-900">{{ balance.product.cash_register_type }}</span>
                      </div>
                      <div v-if="productFieldsVisibility.price && balance.product?.price" class="flex items-center gap-2">
                        <span class="font-medium text-gray-600">Цена:</span>
                        <span class="text-gray-900">{{ formatCurrency(balance.product.price) }}</span>
                      </div>
                      
                      <!-- Кастомные поля -->
                      <template v-for="field in customFields" :key="field.id">
                        <div v-if="customFields && Array.isArray(customFields) && field && typeof field === 'object' && field.field_name && typeof field.field_name === 'string' && getCustomFieldValue(balance.product, field.field_name) !== '-'" class="flex items-center gap-2">
                          <span class="font-medium text-gray-600">{{ field.field_name }}:</span>
                          <span class="text-gray-900">{{ getCustomFieldValue(balance.product, field.field_name) }}</span>
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
            Показано {{ pagination.from }}-{{ pagination.to }} из {{ pagination.total }}
          </div>
          <div class="flex gap-2">
            <button
              v-if="pagination.prev_page_url"
              @click="loadBalances(pagination.current_page - 1)"
              :disabled="loading"
              class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:bg-gray-100 disabled:text-gray-400 text-sm flex items-center gap-2"
            >
              <Loader2 v-if="loading" class="animate-spin h-4 w-4" />
              <span v-if="loading">Загрузка...</span>
              <span v-else>Назад</span>
            </button>
            <button
              v-if="pagination.next_page_url"
              @click="loadBalances(pagination.current_page + 1)"
              :disabled="loading"
              class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:bg-gray-100 disabled:text-gray-400 text-sm flex items-center gap-2"
            >
              <Loader2 v-if="loading" class="animate-spin h-4 w-4" />
              <span v-if="loading">Загрузка...</span>
              <span v-else>Вперед</span>
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
            <h3 class="text-lg font-semibold text-gray-900">Удалить товар?</h3>
            <p class="text-sm text-gray-500">Это действие нельзя отменить. Товар будет удален навсегда.</p>
          </div>
        </div>
        <div class="flex gap-3">
          <button 
            @click="closeDeleteModal" 
            :disabled="deletingProductId !== null"
            class="flex-1 bg-gray-100 hover:bg-gray-200 disabled:bg-gray-50 disabled:cursor-not-allowed text-gray-800 font-semibold px-4 py-2 rounded-lg transition-colors text-sm cursor-pointer"
          >
            Отмена
          </button>
          <button 
            @click="confirmDelete" 
            :disabled="deletingProductId !== null"
            class="flex-1 bg-red-600 hover:bg-red-700 disabled:bg-red-400 disabled:cursor-not-allowed text-white font-semibold px-4 py-2 rounded-lg transition-colors flex items-center justify-center gap-2 cursor-pointer text-sm"
          >
            <Loader2 v-if="deletingProductId !== null" class="w-4 h-4 animate-spin" />
            {{ deletingProductId !== null ? 'Удаление...' : 'Удалить' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Модальное окно импорта товаров -->
    <div v-if="showImportModal" class="fixed inset-0 z-[99999999] flex items-center justify-center bg-white/90 bg-opacity-50">
      <div class="bg-white rounded-lg shadow-lg w-full max-w-6xl mx-4 p-6 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-xl font-bold">Импорт начальных остатков</h2>
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
                Выбрать Excel файл
              </label>
              <input 
                id="file-upload" 
                type="file" 
                accept=".xlsx,.xls" 
                @change="handleFileUpload" 
                class="hidden"
              />
            </div>
            <p class="text-sm text-gray-500">Поддерживаются файлы .xlsx и .xls</p>
            <div class="text-xs text-gray-600 mt-4 space-y-2">
              <p><strong>Обязательные поля:</strong> Наименование, Стоимость</p>
              <p v-if="areCategoriesEnabled()"><strong>Дополнительные поля:</strong> Категория, Подкатегория, Артикул, Единица измерения, Начальный остаток.</p>
              <p v-else><strong>Дополнительные поля:</strong> Артикул, Единица измерения, Начальный остаток.</p>
              <p class="text-gray-500">Поддерживаемые названия колонок:</p>
              <p v-if="areCategoriesEnabled()" class="text-gray-500 text-xs">• Категория: "Категория", "Категория товара"</p>
              <p class="text-gray-500 text-xs">• Единица измерения: "Единица измерения", "Ед. изм.", "Единица"</p>
              <p class="text-gray-500">Если в файле есть эти колонки - они будут автоматически заполнены при загрузке</p>
              <p class="text-gray-500">Остальные поля можно будет заполнить после загрузки файла в форме на сайте</p>
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
            <h3 class="text-sm font-semibold">Найдено товаров: {{ parsedProducts.length }}</h3>
            <div class="flex items-center gap-4">
              <div class="flex items-center gap-2">
                <label class="text-sm font-medium text-gray-700">Склад:</label>
                <div class="relative">
                  <Multiselect
                    v-model="selectedWarehouseForImport"
                    :options="warehouseOptions"
                    label="label"
                    value="value"
                    :object="true"
                    placeholder="Выберите склад"
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
                {{ importSaving ? 'Сохранение...' : 'Сохранить начальные остатки' }}
              </button>
            </div>
          </div>
          
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-3 py-2 text-left text-sm font-medium text-gray-500 whitespace-nowrap">Наименование</th>
                  <th v-if="areCategoriesEnabled()" class="px-3 py-2 text-left text-sm font-medium text-gray-500 whitespace-nowrap">Категория</th>
                  <th v-if="areCategoriesEnabled()" class="px-3 py-2 text-left text-sm font-medium text-gray-500 whitespace-nowrap">Подкатегория</th>
                  <th class="px-3 py-2 text-left text-sm font-medium text-gray-500 whitespace-nowrap">Остаток</th>
                  <th v-if="productFieldsVisibility.price !== false" class="px-3 py-2 text-left text-sm font-medium text-gray-500 whitespace-nowrap">Цена</th>
                  <th class="px-3 py-2 text-left text-sm font-medium text-gray-500 whitespace-nowrap">Ед.изм</th>
                  <th v-if="productFieldsVisibility.article === true" class="px-3 py-2 text-left text-sm font-medium text-gray-500 whitespace-nowrap">Артикул</th>
                  
                  <!-- Кастомные поля в заголовке -->
                  <th v-for="field in customFields" :key="field.id" class="px-3 py-2 text-left text-sm font-medium text-gray-500 whitespace-nowrap">
                    {{ field.field_name }}
                  </th>
                  
                  <th class="px-3 py-2 text-left text-sm font-medium text-gray-500 whitespace-nowrap">Действия</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="(product, index) in parsedProducts" :key="index" class="hover:bg-gray-50">
                  <td class="px-3 py-2 text-sm text-gray-900 whitespace-nowrap">
                    <input v-model="product.name" type="text" class="w-full sm:w-32 text-sm border border-gray-300 rounded px-2 py-1" placeholder="Наименование" />
                  </td>
                  
                  <td v-if="areCategoriesEnabled()" class="px-3 py-2 text-sm text-gray-900 whitespace-nowrap">
                    <Multiselect 
                      v-model="product.selectedCategory"
                      :options="categoryOptions"
                      label="label"
                      value="value"
                      :object="true"
                      placeholder="Выберите категорию"
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
                      placeholder="Выберите подкатегорию"
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
                      placeholder="Выберите"
                      :max-height="200"
                      class="w-24 text-xs multiselect-custom"
                    />
                  </td>
                  
                  <td v-if="productFieldsVisibility.article === true" class="px-3 py-2 text-sm text-gray-900 whitespace-nowrap">
                    <input v-model="product.article" type="text" class="w-24 text-sm border border-gray-300 rounded px-2 py-1" placeholder="Артикул" />
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
                    <input 
                      v-else-if="field.field_type === 'date'" 
                      v-model="product.customFields[field.field_name]" 
                      type="date" 
                      class="w-28 text-sm border border-gray-300 rounded px-2 py-1" 
                    />
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
import ProductsMenu from './ProductsMenu.vue'
import MovementsModal from './MovementsModal.vue'
import Multiselect from '@vueform/multiselect'
import '@vueform/multiselect/themes/default.css'
import { Filter, FunnelX, Loader2, ArrowRightLeft, Edit, Trash2, Plus } from 'lucide-vue-next'
import * as XLSX from 'xlsx'
import { areCategoriesEnabled, isFieldRequired } from '@/utils/productFieldsUtils'
import { getSubcategoriesApiEndpoint } from '@/utils/categoryTypeUtils'

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
      { key: 'description', label: 'Описание' },
      { key: 'country', label: 'Страна' },
      { key: 'supplier', label: 'Поставщик' },
      { key: 'article', label: 'Артикул' },
      { key: 'code', label: 'Код' },
      { key: 'external_code', label: 'Внешний код' },
      { key: 'unit', label: 'Единица измерения' },
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
          categoryOptions.value = categoriesData.map(cat => ({
            label: cat.name_ru || cat.name || 'Без названия',
            value: cat.category_id || cat.id,
            category_id: cat.category_id || cat.id
          }))
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
      if (!categoryId) {
        subcategoryOptions.value = []
        return
      }
      loadingSubcategories.value = true
      try {
        console.log('Загружаем подкатегории для категории:', categoryId)
        const res = await apiRequest(`/subcategories?category_id=${categoryId}`)
        console.log('Ответ API подкатегорий:', res)
        
        if (res.ok && res.data && res.data.data && Array.isArray(res.data.data)) {
          subcategoryOptions.value = res.data.data.map(subcat => ({ 
            label: subcat.name_ru || subcat.name || 'Без названия', 
            value: subcat.subcategory_id || subcat.id,
            subcategory_id: subcat.subcategory_id || subcat.id
          }))
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
      if (quantity === 0) return 'Нет в наличии'
      if (quantity <= 10) return 'Низкий остаток'
      return 'В наличии'
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
          
          showNotification('Товар успешно удален', 'success')
          closeDeleteModal()
        } else {
          showNotification(response.data.message || 'Ошибка при удалении товара', 'error')
        }
      } catch (err) {
        console.error('Ошибка удаления товара:', err)
        showNotification('Ошибка при удалении товара', 'error')
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
      filters.category = val ? val.category_id : null
      selectedSubcategory.value = null
      filters.subcategory = null
      loadSubcategories(val ? val.category_id : null)
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
          throw new Error('Поддерживаются только файлы .xlsx и .xls')
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
          throw new Error('Файл не содержит данных')
        }

        // Получаем заголовки (первая строка)
        const headers = jsonData[0]
        
        // Проверяем обязательные колонки
        const requiredColumns = ['Название', 'Стоимость']
        const missingColumns = requiredColumns.filter(col => !headers.includes(col))
        
        if (missingColumns.length > 0) {
          throw new Error(`Отсутствуют обязательные колонки: ${missingColumns.join(', ')}`)
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
          
          // Проверяем обязательные поля
          if (!product['Название'] || !product['Стоимость']) {
            continue // Пропускаем строки без обязательных полей
          }
          
          // Преобразуем данные в нужный формат
          const unitValue = product['Единица измерения'] || product['Ед. изм.'] || product['Единица'] || 'Штука'
          const parsedProduct = {
            name: product['Название']?.toString() || '',
            price: parseFloat(product['Стоимость']) || 0,
            quantity: parseFloat(product['Начальный остаток'] || product['Остаток'] || 0) || 0,
            unit: { label: unitValue, value: unitValue },
            article: product['Артикул'] || '',
            selectedCategory: null,
            selectedSubcategory: null,
            subcategoryOptions: [],
            customFields: {}
          }
          
          // Добавляем категории только если они включены в настройках
          if (areCategoriesEnabled()) {
            parsedProduct.category = product['Категория'] || product['Категория товара'] || ''
          }
          
          products.push(parsedProduct)
        }
        
        if (products.length === 0) {
          throw new Error('Не найдено товаров для импорта')
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
                  product.subcategoryOptions = localCategory.subcategories.map(sub => ({
                    label: sub.name_ru || sub.name || 'Без названия',
                    value: sub.subcategory_id
                  }))
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
        importError.value = 'Выберите склад для импорта'
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
          showNotification('Товары успешно импортированы', 'success')
          closeImportModal()
          loadBalances() // Перезагружаем остатки
        } else {
          importError.value = response.data.message || 'Ошибка импорта товаров'
        }
      } catch (error) {
        console.error('Ошибка импорта:', error)
        importError.value = 'Ошибка импорта товаров'
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
              'Название': product.name || '-',
              'Категория': product.category_name || product.category || '-',
              'Подкатегория': product.subcategory_name || product.subcategory || '-',
              'Общий остаток': formatQuantity(product.total_quantity),
              'Единица измерения': product.unit || '-',
              'Стоимость': formatPrice(product.price),
              'Артикул': product.article || '-'
            }
            
            // Добавляем информацию по складам
            if (product.warehouse_balances && Array.isArray(product.warehouse_balances)) {
              product.warehouse_balances.forEach((warehouseBalance, index) => {
                const warehouseName = warehouseBalance.warehouse_name || `Склад ${warehouseBalance.warehouse_id}`
                exportRow[`Остаток на ${warehouseName}`] = formatQuantity(warehouseBalance.quantity)
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
          const fileName = `остатки_${dateStr}_${timeStr}.xlsx`
          
          // Скачиваем файл
          XLSX.writeFile(workbook, fileName)
          
          showNotification(`Экспортировано ${exportData.length} товаров`, 'success')
        }
        
      } catch (error) {
        console.error('Ошибка экспорта:', error)
        showNotification('Ошибка экспорта остатков', 'error')
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
        const res = await apiRequest(`/subcategories?category_id=${categoryId}`)
        console.log('Ответ API подкатегорий для товара:', res)
        
        if (res.ok && res.data && res.data.data && Array.isArray(res.data.data)) {
          product.subcategoryOptions = res.data.data.map(subcat => ({ 
            label: subcat.name_ru || subcat.name || 'Без названия', 
            value: subcat.subcategory_id || subcat.id,
            subcategory_id: subcat.subcategory_id || subcat.id
          }))
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