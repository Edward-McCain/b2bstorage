<template>
  <div class="min-h-screen bg-gray-50" style="padding-top: 66px;">
    <!-- Внутреннее меню навигации -->
    <ProductsMenu />
    <!-- Верхнее меню и фильтры -->
    <div class="bg-white border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-wrap items-center gap-2 py-4">
        <input 
          v-model="searchQuery" 
          @input="handleSearch"
          type="text" 
          placeholder="Наименование, код или артикул" 
          class="border border-gray-300 rounded px-3 py-1.5 text-sm w-full md:w-64" 
        />

        <div class="flex w-full md:w-auto gap-2">
          <button class="flex-1 bg-gray-100 border border-gray-200 text-gray-700 font-medium px-3 py-1.5 rounded text-sm relative group" @click="openFilterModal" title="Открыть фильтр товаров">
            Фильтр
            <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-900 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-10">
              Открыть фильтр товаров
              <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
            </div>
          </button>
          <router-link to="/products/create" class="flex-1 flex items-center justify-center gap-1 bg-blue-50 border border-blue-200 text-blue-700 font-medium px-3 py-1.5 rounded text-sm relative group" title="Создать новый товар">
            <Plus class="w-4 h-4" />
            Товар
            <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-900 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-10">
              Создать новый товар
              <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
            </div>
          </router-link>
          <button class="flex-1 bg-white border border-gray-300 px-3 py-1.5 rounded font-medium text-sm relative group" title="Импорт товаров из файла">
            Импорт
            <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-900 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-10">
              Импорт товаров из файла
              <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
            </div>
          </button>
          <button class="flex-1 bg-white border border-gray-300 px-3 py-1.5 rounded font-medium text-sm relative group" title="Экспорт товаров в файл">
            Экспорт
            <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-900 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-10">
              Экспорт товаров в файл
              <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
            </div>
          </button>
        </div>
      </div>
    </div>

    <!-- Центральный контент -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col py-3 min-h-[60vh]">
      <!-- Загрузка -->
      <div v-if="loading" class="flex items-center justify-center py-20">
        <div class="text-center">
          <Loader2 class="animate-spin h-8 w-8 text-blue-500 mx-auto mb-4" />
          <p class="text-gray-600">Загрузка товаров...</p>
        </div>
      </div>

      <!-- Ошибка -->
      <div v-else-if="error" class="text-center py-20">
        <p class="text-red-500 mb-4">{{ error }}</p>
        <button @click="loadProducts" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg relative group" title="Повторить загрузку товаров">
          Попробовать снова
          <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-900 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-10">
            Повторить загрузку товаров
            <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
          </div>
        </button>
      </div>

      <!-- Нет товаров -->
      <template v-else-if="products.length === 0">
        <div class="flex flex-col md:flex-row items-center justify-center w-full mt-12 gap-8">
          <div class="flex flex-col items-center md:items-start">
            <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-6 text-center md:text-left">Здесь будут все ваши товары</h1>
            <div class="flex gap-4 mb-6 w-full justify-center md:justify-center">
              <router-link to="/products/create" class="flex items-center gap-2 bg-blue-100 hover:bg-blue-200 text-blue-900 font-semibold px-6 py-3 rounded-lg text-lg transition relative group" title="Создать новый товар">
                <span>Добавить товар</span>
                <Plus class="w-6 h-6" />
                <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-900 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-10">
                  Создать новый товар
                  <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
                </div>
              </router-link>
            </div>
            <div class="text-gray-600 mb-4 text-center md:text-left">
              Если у вас уже есть каталог товаров, загрузите его из <a href="#" class="text-blue-600 hover:underline">документа Excel</a>.
            </div>
          </div>
        </div>
      </template>

      <!-- Таблица товаров -->
      <template v-else>
        <div class="w-full bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-4 py-3 font-semibold text-left text-gray-900"></th>
                  <th class="px-4 py-3 font-semibold text-left text-gray-900">Наименование</th>
                  <th class="px-4 py-3 font-semibold text-left text-gray-900">Код</th>
                  <th class="px-4 py-3 font-semibold text-left text-gray-900">Артикул</th>
                  <th class="px-4 py-3 font-semibold text-left text-gray-900">Категория</th>
                  <th class="px-4 py-3 font-semibold text-left text-gray-900">Поставщик</th>
                  <th class="px-4 py-3 font-semibold text-left text-gray-900">Действия</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="product in products" :key="product.id" class="hover:bg-gray-50">
                  <td class="px-4 py-3">
                    <div class="flex items-center">
                      <img 
                        v-if="product.images && product.images.length > 0" 
                        :src="product.images[0].image_url" 
                        :alt="product.images[0].alt_text || product.name"
                        class="w-12 h-12 object-cover rounded-lg border border-gray-200"
                      />
                      <div v-else class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center">
                        <Image class="w-6 h-6 text-gray-400" />
                      </div>
                    </div>
                  </td>
                  <td class="px-4 py-3 max-w-[200px]">
                    <div class="font-medium text-gray-900 truncate">{{ product.name }}</div>
                    <div v-if="product.description" class="text-gray-500 text-xs mt-1 truncate">
                      {{ product.description }}
                    </div>
                  </td>
                  <td class="px-4 py-3 text-gray-900">{{ product.code || '-' }}</td>
                  <td class="px-4 py-3 text-gray-900">{{ product.article || '-' }}</td>
                  <td class="px-4 py-3">
                    <div class="text-gray-900">{{ product.category_name || product.category || '-' }}</div>
                    <div v-if="product.subcategory_name || product.subcategory" class="text-gray-500 text-xs">{{ product.subcategory_name || product.subcategory }}</div>
                  </td>
                  <td class="px-4 py-3 text-gray-900">{{ product.supplier || '-' }}</td>
                  <td class="px-4 py-3">
                    <div class="flex items-center space-x-2">
                      <router-link 
                        :to="`/products/edit/${product.id}`"
                        class="text-blue-600 hover:text-blue-800 p-1 rounded hover:bg-blue-50 transition-colors relative group"
                        title="Редактировать"
                      >
                        <Edit class="w-4 h-4" />
                        <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-900 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-10">
                          Редактировать
                          <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
                        </div>
                      </router-link>
                      <button 
                        @click="deleteProduct(product.id)"
                        class="text-red-600 hover:text-red-800 p-1 rounded hover:bg-red-50 transition-colors relative group"
                        title="Удалить"
                      >
                        <Trash2 class="w-4 h-4" />
                        <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-900 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-10">
                          Удалить
                          <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
                        </div>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Пагинация -->
          <div v-if="pagination && pagination.last_page > 1" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            <div class="flex items-center justify-between">
              <div class="flex-1 flex justify-between sm:hidden">
                <button 
                  @click="changePage(pagination.current_page - 1)"
                  :disabled="pagination.current_page === 1"
                  class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed relative group"
                  title="Предыдущая страница"
                >
                  <ChevronLeft class="w-4 h-4 mr-1" />
                  Назад
                  <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-900 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-10">
                    Предыдущая страница
                    <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
                  </div>
                </button>
                <button 
                  @click="changePage(pagination.current_page + 1)"
                  :disabled="pagination.current_page === pagination.last_page"
                  class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed relative group"
                  title="Следующая страница"
                >
                  Вперед
                  <ChevronRight class="w-4 h-4 ml-1" />
                  <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-900 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-10">
                    Следующая страница
                    <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
                  </div>
                </button>
              </div>
              <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                  <p class="text-sm text-gray-700">
                    Показано 
                    <span class="font-medium">{{ pagination.from }}</span>
                    до 
                    <span class="font-medium">{{ pagination.to }}</span>
                    из 
                    <span class="font-medium">{{ pagination.total }}</span>
                    результатов
                  </p>
                </div>
                <div>
                  <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                    <button 
                      @click="changePage(pagination.current_page - 1)"
                      :disabled="pagination.current_page === 1"
                      class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed relative group"
                      title="Предыдущая страница"
                    >
                      <span class="sr-only">Предыдущая</span>
                      <ChevronLeft class="h-5 w-5" />
                      <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-900 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-10">
                        Предыдущая страница
                        <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
                      </div>
                    </button>
                    
                    <template v-for="page in visiblePages" :key="page">
                      <button 
                        v-if="page !== '...'"
                        @click="changePage(page)"
                        :class="[
                          page === pagination.current_page
                            ? 'z-10 bg-blue-50 border-blue-500 text-blue-600'
                            : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                          'relative inline-flex items-center px-4 py-2 border text-sm font-medium relative group'
                        ]"
                        :title="`Перейти на страницу ${page}`"
                      >
                        {{ page }}
                        <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-900 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-10">
                          Перейти на страницу {{ page }}
                          <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
                        </div>
                      </button>
                      <span 
                        v-else
                        class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700"
                      >
                        ...
                      </span>
                    </template>

                    <button 
                      @click="changePage(pagination.current_page + 1)"
                      :disabled="pagination.current_page === pagination.last_page"
                      class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed relative group"
                      title="Следующая страница"
                    >
                      <span class="sr-only">Следующая</span>
                      <ChevronRight class="h-5 w-5" />
                      <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-900 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-10">
                        Следующая страница
                        <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
                      </div>
                    </button>
                  </nav>
                </div>
              </div>
            </div>
          </div>
        </div>
      </template>
    </div>

    <!-- Модальное окно фильтра -->
    <div v-if="showFilterModal" class="fixed inset-0 md:z-[9999999] sm:pt-20 flex items-center justify-center bg-white/60 backdrop-blur-sm md:z-auto">
      <div class="bg-white rounded-lg shadow-lg w-full max-w-4xl mx-auto p-6 relative flex flex-col max-h-[90vh] overflow-y-auto md:w-[700px] md:rounded-xl md:p-8 sm:max-w-full sm:h-full sm:rounded-none sm:p-4 sm:pt-20 sm:max-h-[calc(100vh-68px)]">
        <button @click="closeFilterModal" class="absolute top-2 right-2 text-gray-400 hover:text-gray-700 p-1 rounded hover:bg-gray-100 transition-colors relative group" title="Закрыть">
          <X class="w-5 h-5" />
          <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-900 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-10">
            Закрыть
            <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
          </div>
        </button>
        <h2 class="text-xl font-bold mb-4">Фильтр товаров</h2>
        <form @submit.prevent="applyFilter" class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs text-gray-700 mb-1">Категория</label>
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
            <label class="block text-xs text-gray-700 mb-1">Подкатегория</label>
            <Multiselect
              v-model="selectedSubcategory"
              :options="subcategoryOptions"
              label="label"
              value="value"
              :object="true"
              placeholder="Выберите подкатегорию"
              searchable
              :max-height="400"
              class="w-full text-xs multiselect-custom"
              :loading="loadingSubcategories"
              :disabled="!selectedCategory"
            />
          </div>
          <div>
            <label class="block text-xs text-gray-700 mb-1">Страна</label>
            <Multiselect
              v-model="selectedCountry"
              :options="countries"
              label="label"
              value="value"
              :object="true"
              placeholder="Выберите страну"
              searchable
              :search-placeholder="'Поиск страны'"
              :max-height="400"
              class="w-full text-sm multiselect-custom"
            />
          </div>
          <div>
            <label class="block text-xs text-gray-700 mb-1">Поставщик</label>
            <input v-model="filter.supplier" type="text" placeholder="Поставщик" class="border rounded px-3 py-2 text-sm w-full" />
          </div>
          <div>
            <label class="block text-xs text-gray-700 mb-1">Артикул</label>
            <input v-model="filter.article" type="text" placeholder="Артикул" class="border rounded px-3 py-2 text-sm w-full" />
          </div>
          <div>
            <label class="block text-xs text-gray-700 mb-1">Код</label>
            <input v-model="filter.code" type="text" placeholder="Код" class="border rounded px-3 py-2 text-sm w-full" />
          </div>
          <div>
            <label class="block text-xs text-gray-700 mb-1">Внешний код</label>
            <input v-model="filter.external_code" type="text" placeholder="Внешний код" class="border rounded px-3 py-2 text-sm w-full" />
          </div>
          <div>
            <label class="block text-xs text-gray-700 mb-1">Ед. измерения</label>
            <Multiselect
              v-model="selectedUnit"
              :options="unitOptions"
              label="label"
              value="value"
              :object="true"
              placeholder="Выберите единицу измерения"
              :max-height="400"
              class="w-full text-sm multiselect-custom"
            />
          </div>
          <div>
            <label class="block text-xs text-gray-700 mb-1">Вес</label>
            <input v-model="filter.weight" type="number" step="0.001" placeholder="Вес" class="border rounded px-3 py-2 text-sm w-full" />
          </div>
          <div>
            <label class="block text-xs text-gray-700 mb-1">Объем</label>
            <input v-model="filter.volume" type="number" step="0.001" placeholder="Объем" class="border rounded px-3 py-2 text-sm w-full" />
          </div>
          <div>
            <label class="block text-xs text-gray-700 mb-1">Ставка НДС</label>
            <input v-model="filter.vat" type="text" placeholder="Ставка НДС" class="border rounded px-3 py-2 text-sm w-full" />
          </div>
          <div>
            <label class="block text-xs text-gray-700 mb-1">Фасовка</label>
            <Multiselect
              v-model="selectedPacking"
              :options="packingOptions"
              label="label"
              value="value"
              :object="true"
              placeholder="Выберите фасовку"
              :max-height="400"
              class="w-full text-sm multiselect-custom"
            />
          </div>
          <div>
            <label class="block text-xs text-gray-700 mb-1">Тип учета</label>
            <Multiselect
              v-model="selectedAccountingType"
              :options="accountingTypeOptions"
              label="label"
              value="value"
              :object="true"
              placeholder="Выберите тип учета"
              :max-height="400"
              class="w-full text-sm multiselect-custom"
            />
          </div>
          <div>
            <label class="block text-xs text-gray-700 mb-1">Маркировка</label>
            <input v-model="filter.marking" type="text" placeholder="Маркировка" class="border rounded px-3 py-2 text-sm w-full" />
          </div>
          <div>
            <label class="block text-xs text-gray-700 mb-1">Тип маркировки товара</label>
            <Multiselect
              v-model="selectedProductType"
              :options="productTypeOptions"
              label="label"
              value="value"
              :object="true"
              placeholder="Выберите тип маркировки товара"
              :max-height="400"
              class="w-full text-sm multiselect-custom"
            />
          </div>
          <div>
            <label class="block text-xs text-gray-700 mb-1">Тип штрихкода</label>
            <Multiselect
              v-model="selectedBarcodeType"
              :options="barcodeTypeOptions"
              label="label"
              value="value"
              :object="true"
              placeholder="Выберите тип штрихкода"
              :max-height="400"
              class="w-full text-sm multiselect-custom"
            />
          </div>
          <div class="md:col-span-2">
            <label class="block text-xs text-gray-700 mb-1">Штрихкод</label>
            <input v-model="filter.barcode" type="text" placeholder="Штрихкод" class="border rounded px-3 py-2 text-sm w-full" />
          </div>
          <div>
            <label class="block text-xs text-gray-700 mb-1">Дата создания</label>
            <input v-model="filter.created_at" type="date" placeholder="Дата создания" class="border rounded px-3 py-2 text-sm w-full" />
          </div>
          <div>
            <label class="block text-xs text-gray-700 mb-1">Дата изменения</label>
            <input v-model="filter.updated_at" type="date" placeholder="Дата изменения" class="border rounded px-3 py-2 text-sm w-full" />
          </div>
        </form>
        <div class="flex justify-center gap-4 mt-6">
          <button @click="resetFilter" class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold px-6 py-2 rounded-lg border text-sm flex items-center gap-2 relative group" title="Сбросить все фильтры">
            <RotateCcw class="w-4 h-4" />
            Сбросить
            <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-900 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-10">
              Сбросить все фильтры
              <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
            </div>
          </button>
          <button @click="closeFilterModal" class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold px-6 py-2 rounded-lg border text-sm relative group" title="Закрыть фильтр">
            Отмена
            <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-900 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-10">
              Закрыть фильтр
              <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
            </div>
          </button>
          <button 
            @click="applyFilter" 
            :disabled="filterLoading"
            class="bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white font-semibold px-6 py-2 rounded-lg text-sm flex items-center gap-2 relative group"
            :title="filterLoading ? 'Выполняется поиск...' : 'Применить фильтры'"
          >
            <Search v-if="filterLoading" class="animate-spin w-4 h-4" />
            <Search v-else class="w-4 h-4" />
            {{ filterLoading ? 'Поиск...' : 'Применить' }}
            <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-900 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-10">
              {{ filterLoading ? 'Выполняется поиск...' : 'Применить фильтры' }}
              <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
            </div>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, reactive, watch } from 'vue'
import { apiRequest, getCategoriesWithCache } from '@/config/api'
import ProductsMenu from './ProductsMenu.vue'
import toastr from 'toastr'
import Multiselect from '@vueform/multiselect'
import countriesData from '@/data/countries.json'
import { Edit, Trash2, RotateCcw, Search, X, Loader2, Image, ChevronLeft, ChevronRight, Plus } from 'lucide-vue-next'

// Состояние
const products = ref([])
const loading = ref(false)
const error = ref('')
const searchQuery = ref('')
const pagination = ref(null)
const searchTimeout = ref(null)
const showFilterModal = ref(false)
const filterLoading = ref(false)
const filter = reactive({
  category: null,
  subcategory: null,
  country: null,
  supplier: '',
  article: '',
  code: '',
  external_code: '',
  unit: null,
  weight: '',
  volume: '',
  vat: '',
  packing: null,
  accounting_type: null,
  marking: '',
  product_type: null,
  barcode_type: null,
  barcode: '',
  created_at: '',
  updated_at: ''
})

const categoryOptions = ref([])
const subcategoryOptions = ref([])
const loadingCategories = ref(false)
const loadingSubcategories = ref(false)
const selectedCategory = ref(null)
const selectedSubcategory = ref(null)
const selectedCountry = ref(null)
const selectedUnit = ref(null)
const selectedPacking = ref(null)
const selectedAccountingType = ref(null)
const selectedProductType = ref(null)
const selectedBarcodeType = ref(null)

// Загрузка товаров
async function loadProducts(page = 1, filterParams = null) {
  loading.value = true
  error.value = ''
  
  try {
    const params = new URLSearchParams({
      page: page.toString(),
      per_page: '15'
    })
    
    // Добавляем поисковый запрос
    if (searchQuery.value.trim()) {
      params.append('search', searchQuery.value.trim())
    }
    
    // Добавляем параметры фильтра
    if (filterParams) {
      Object.keys(filterParams).forEach(key => {
        const value = filterParams[key]
        if (value !== null && value !== undefined && value !== '') {
          params.append(key, value.toString())
        }
      })
    }
    
    const response = await apiRequest(`/products?${params.toString()}`)
    
    if (response.ok) {
      products.value = response.data.data.data || []
      pagination.value = {
        current_page: response.data.data.current_page,
        last_page: response.data.data.last_page,
        per_page: response.data.data.per_page,
        total: response.data.data.total,
        from: response.data.data.from,
        to: response.data.data.to
      }
    } else {
      error.value = response.data.message || 'Ошибка загрузки товаров'
    }
  } catch (err) {
    console.error('Ошибка загрузки товаров:', err)
    error.value = 'Ошибка загрузки товаров'
  } finally {
    loading.value = false
  }
}

// Поиск с debounce
function handleSearch() {
  if (searchTimeout.value) {
    clearTimeout(searchTimeout.value)
  }
  
  searchTimeout.value = setTimeout(() => {
    // При поиске сбрасываем фильтры
    loadProducts(1, null)
  }, 500)
}

// Смена страницы
function changePage(page) {
  if (page >= 1 && page <= pagination.value.last_page) {
    loadProducts(page, createFilterParams())
  }
}

// Удаление товара
async function deleteProduct(productId) {
  if (!confirm('Вы уверены, что хотите удалить этот товар?')) {
    return
  }
  
  try {
    const response = await apiRequest(`/products/${productId}`, {
      method: 'DELETE'
    })
    
    if (response.ok) {
      toastr.success('Товар успешно удален')
      loadProducts(pagination.value.current_page, createFilterParams())
    } else {
      toastr.error('Ошибка при удалении товара')
    }
  } catch (err) {
    console.error('Ошибка удаления товара:', err)
    toastr.error('Ошибка при удалении товара')
  }
}

// Вычисление видимых страниц для пагинации
const visiblePages = computed(() => {
  if (!pagination.value) return []
  
  const current = pagination.value.current_page
  const last = pagination.value.last_page
  const pages = []
  
  if (last <= 7) {
    for (let i = 1; i <= last; i++) {
      pages.push(i)
    }
  } else {
    if (current <= 4) {
      for (let i = 1; i <= 5; i++) {
        pages.push(i)
      }
      pages.push('...')
      pages.push(last)
    } else if (current >= last - 3) {
      pages.push(1)
      pages.push('...')
      for (let i = last - 4; i <= last; i++) {
        pages.push(i)
      }
    } else {
      pages.push(1)
      pages.push('...')
      for (let i = current - 1; i <= current + 1; i++) {
        pages.push(i)
      }
      pages.push('...')
      pages.push(last)
    }
  }
  
  return pages
})

function openFilterModal() {
  showFilterModal.value = true
  loadCategories()
}
function closeFilterModal() {
  showFilterModal.value = false
}

// Загрузка категорий для фильтра
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

async function loadSubcategories(categoryId) {
  if (!categoryId) {
    subcategoryOptions.value = []
    return
  }
  loadingSubcategories.value = true
  try {
    const res = await apiRequest(`/subcategories?category_id=${categoryId}`)
    if (res.data && Array.isArray(res.data)) {
      subcategoryOptions.value = res.data.map(subcat => ({ 
        label: subcat.name_ru, 
        value: subcat.id,
        subcategory_id: subcat.subcategory_id 
      }))
    } else {
      subcategoryOptions.value = []
    }
  } catch (error) {
    console.error('Ошибка загрузки подкатегорий:', error)
    subcategoryOptions.value = []
  } finally {
    loadingSubcategories.value = false
  }
}

watch(selectedCategory, (val) => {
  filter.category = val ? val.category_id : null
  selectedSubcategory.value = null
  filter.subcategory = null
  loadSubcategories(val ? val.category_id : null)
})
watch(selectedSubcategory, (val) => {
  filter.subcategory = val ? val.subcategory_id : null
})

watch(selectedCountry, (val) => {
  filter.country = val ? val.value : null
})

watch(selectedUnit, (val) => {
  filter.unit = val ? val.value : null
})

watch(selectedPacking, (val) => {
  filter.packing = val ? val.value : null
})

watch(selectedAccountingType, (val) => {
  filter.accounting_type = val ? val.value : null
})

watch(selectedProductType, (val) => {
  filter.product_type = val ? val.value : null
})

watch(selectedBarcodeType, (val) => {
  filter.barcode_type = val ? val.value : null
})

// Вспомогательная функция для создания параметров фильтра
function createFilterParams() {
  const filterParams = {}
  Object.keys(filter).forEach(key => {
    const value = filter[key]
    if (value !== null && value !== undefined && value !== '') {
      filterParams[key] = value
    }
  })
  return filterParams
}

// Computed свойства для селектов
const countries = computed(() =>
  countriesData.map(country => ({
    label: country.name,
    value: country.id,
    code: country.code,
    raw: country
  }))
)

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
  { label: 'Икра и морепродукты', value: 'Икра и морепродукты' },
  { label: 'Велосипеды', value: 'Велосипеды' },
  { label: 'Безалкогольное пиво', value: 'Безалкогольное пиво' }
]

const barcodeTypeOptions = [
  { label: 'EAN8', value: 'EAN8' },
  { label: 'EAN13', value: 'EAN13' },
  { label: 'Code128', value: 'Code128' },
  { label: 'GTIN', value: 'GTIN' },
  { label: 'UPC', value: 'UPC' }
]

async function applyFilter() {
  filterLoading.value = true
  try {
    await loadProducts(1, createFilterParams())
    closeFilterModal()
  } finally {
    filterLoading.value = false
  }
}

function resetFilter() {
  // Сбрасываем все поля фильтра
  Object.keys(filter).forEach(key => {
    filter[key] = null
  })
  
  // Сбрасываем выбранные значения в селектах
  selectedCategory.value = null
  selectedSubcategory.value = null
  selectedCountry.value = null
  selectedUnit.value = null
  selectedPacking.value = null
  selectedAccountingType.value = null
  selectedProductType.value = null
  selectedBarcodeType.value = null
  
  // Очищаем текстовые поля
  filter.supplier = ''
  filter.article = ''
  filter.code = ''
  filter.external_code = ''
  filter.weight = ''
  filter.volume = ''
  filter.vat = ''
  filter.marking = ''
  filter.barcode = ''
  filter.created_at = ''
  filter.updated_at = ''
}

onMounted(() => {
  document.title = 'B2B Storage - Товары'
  loadProducts()
})
</script> 