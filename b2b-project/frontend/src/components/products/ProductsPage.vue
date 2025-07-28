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
          <router-link to="/products/create" class="flex-1 flex items-center justify-center gap-1 bg-blue-50 border border-blue-200 text-blue-700 font-medium px-3 py-1.5 rounded text-sm relative group" title="Добавить начальный остаток">
            <Plus class="w-4 h-4" />
            Товар
            <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-900 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-10">
              Добавить начальный остаток
              <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
            </div>
          </router-link>
          <button 
            @click="openImportModal"
            :disabled="importLoading"
            class="flex-1 bg-white border border-gray-300 px-3 py-1.5 rounded font-medium text-sm relative group hover:bg-gray-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed inline-flex items-center justify-center" 
            :title="importLoading ? 'Обработка файла...' : 'Импорт начальных остатков из файла'"
          >
            <Loader2 v-if="importLoading" class="w-4 h-4 animate-spin mr-1" />
            {{ importLoading ? 'Обработка...' : 'Импорт' }}
            <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-900 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-10">
              {{ importLoading ? 'Обработка файла...' : 'Импорт начальных остатков из файла' }}
              <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
            </div>
          </button>
          <button 
            @click="exportProducts"
            :disabled="exportLoading"
            class="flex-1 bg-white border border-gray-300 px-3 py-1.5 rounded font-medium text-sm relative group hover:bg-gray-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed inline-flex items-center justify-center" 
            :title="exportLoading ? 'Выполняется экспорт...' : 'Экспорт остатков в файл'"
          >
            <Loader2 v-if="exportLoading" class="w-4 h-4 animate-spin mr-1" />
            {{ exportLoading ? 'Экспорт...' : 'Экспорт' }}
            <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-900 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-10">
              {{ exportLoading ? 'Выполняется экспорт...' : 'Экспорт остатков в файл' }}
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
          <p class="text-gray-600">Загрузка начальных остатков...</p>
        </div>
      </div>

      <!-- Ошибка -->
      <div v-else-if="error" class="text-center py-20">
        <p class="text-red-500 mb-4">{{ error }}</p>
        <button @click="loadProducts" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg relative group" title="Повторить загрузку начальных остатков">
          Попробовать снова
          <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-900 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-10">
            Повторить загрузку начальных остатков
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
                  <th class="px-4 py-3 font-semibold text-left text-gray-900">Остаток</th>
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
                  <td class="px-4 py-3 text-gray-900">{{ product.quantity || '-' }}</td>
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
                        @click="openDeleteModal(product.id)"
                        :disabled="deletingProductId === product.id"
                        class="text-red-600 hover:text-red-800 p-1 rounded hover:bg-red-50 transition-colors relative group disabled:opacity-50 disabled:cursor-not-allowed"
                        title="Удалить"
                      >
                        <Loader2 v-if="deletingProductId === product.id" class="w-4 h-4 animate-spin" />
                        <Trash2 v-else class="w-4 h-4" />
                        <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-900 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-10">
                          {{ deletingProductId === product.id ? 'Удаление...' : 'Удалить' }}
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
    <div v-if="showFilterModal" class="fixed inset-0 z-[9999999] sm:pt-20 flex items-center justify-center bg-white/60 backdrop-blur-sm md:z-auto">
      <div class="bg-white rounded-lg shadow-lg w-full max-w-4xl mx-auto p-6 relative max-h-[90vh] overflow-y-auto md:w-[700px] md:rounded-xl md:p-8 sm:max-w-full sm:h-full sm:rounded-none sm:p-4 sm:pt-20 sm:max-h-[calc(100vh-68px)]">
        <div>
          <h2 class="text-xl font-bold mb-4" style="display: inline-flex;align-items: center;justify-content: space-between;width: 100%;">
            Фильтр товаров
            <span class="text-sm text-gray-500 cursor-pointer" @click="closeFilterModal">
              <X class="w-5 h-5" />
            </span>
          </h2>
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
                class="w-full min-w-[200px] text-sm multiselect-custom"
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
                class="w-full min-w-[200px] text-xs multiselect-custom"
                :loading="loadingSubcategories"
                :disabled="!selectedCategory"
              />
            </div>
            <div>
              <label class="block text-xs text-gray-700 mb-1">Дата создания</label>
              <input v-model="filter.created_at" type="date" placeholder="Дата создания" class="border rounded px-3 py-2 text-sm w-full" />
            </div>
            <div>
              <label class="block text-xs text-gray-700 mb-1">Склад</label>
              <Multiselect
                v-model="filter.warehouse_id"
                :options="warehouseOptions"
                label="label"
                value="value"
                :object="true"
                placeholder="Выберите склад"
                :max-height="400"
                class="w-full min-w-[200px] text-sm multiselect-custom"
                :loading="loadingWarehouses"
              />
            </div>

            <!-- Динамические стандартные и кастомные поля -->
            <template v-if="loadingProductFields">
              <div class="col-span-2 flex items-center justify-center py-4"><Loader2 class="animate-spin h-6 w-6 text-blue-500" /><span class="ml-2 text-gray-500">Загрузка полей...</span></div>
            </template>
            <template v-else>
              <template v-for="field in standardProductFields" :key="field.key">
                <div v-if="productFieldsVisibility[field.key] === true && field.key !== 'description' && field.key !== 'unit'">
                  <label class="block text-xs text-gray-700 mb-1">{{ field.label }}</label>
                  <input v-model="filter[field.key]" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" />
                </div>
              </template>
              <template v-for="field in customFields" :key="field.id">
                <div>
                  <label class="block text-xs text-gray-700 mb-1">{{ field.field_name }}</label>
                  <input v-model="filter[field.field_name]" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" />
                </div>
              </template>
            </template>
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
            class="flex-1 bg-gray-100 hover:bg-gray-200 disabled:bg-gray-50 disabled:cursor-not-allowed text-gray-800 font-semibold px-4 py-2 rounded-lg transition-colors"
          >
            Отмена
          </button>
          <button 
            @click="confirmDelete" 
            :disabled="deletingProductId !== null"
            class="flex-1 bg-red-600 hover:bg-red-700 disabled:bg-red-400 disabled:cursor-not-allowed text-white font-semibold px-4 py-2 rounded-lg transition-colors flex items-center justify-center gap-2"
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
            <X class="w-5 h-5" />
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
              <p><strong>Обязательные поля:</strong> Наименование, Начальный остаток, Стоимость</p>
              <p><strong>Дополнительные поля:</strong> Категория, Подкатегория, Артикул, Единица измерения.</p>
              <p class="text-gray-500">Поддерживаемые названия колонок:</p>
              <p class="text-gray-500 text-xs">• Категория: "Категория", "Категория товара"</p>
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
                  <th class="px-3 py-2 text-left text-sm font-medium text-gray-500">Наименование</th>
                  <th class="px-3 py-2 text-left text-sm font-medium text-gray-500">Категория</th>
                  <th class="px-3 py-2 text-left text-sm font-medium text-gray-500">Данные</th>
                  <th class="px-3 py-2 text-left text-sm font-medium text-gray-500">Артикул</th>
                  <th class="px-3 py-2 text-left text-sm font-medium text-gray-500">Действия</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="(product, index) in parsedProducts" :key="index" class="hover:bg-gray-50">
                  <td class="px-3 py-2 text-sm text-gray-900 max-w-[200px] truncate" :title="product.name">
                    <input v-model="product.name" type="text" class="w-full text-sm border border-gray-300 rounded px-2 py-1" placeholder="Наименование" />
                  </td>
                  <td class="px-3 py-2 text-sm text-gray-900">
                    <div class="space-y-2">
                      <div class="relative">
                        <Multiselect 
                          style="margin-bottom: 5px;" 
                          v-model="product.selectedCategory"
                          :options="categoryOptions"
                          label="label"
                          value="value"
                          :object="true"
                          placeholder="Выберите категорию"
                          searchable
                          :search-placeholder="'Поиск категории'"
                          :max-height="200"
                          :disabled="loadingCategories"
                          class="w-full min-w-[180px] text-xs multiselect-custom"
                          @update:model-value="(val) => onCategoryChange(val, index)"
                        />
                        <div v-if="loadingCategories" class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-75 rounded">
                          <Loader2 class="w-4 h-4 animate-spin text-blue-500" />
                        </div>
                      </div>
                      <div class="relative">
                        <Multiselect
                          v-model="product.selectedSubcategory"
                          :options="product.subcategoryOptions || []"
                          label="label"
                          value="value"
                          :object="true"
                          placeholder="Выберите подкатегорию"
                          searchable
                          :search-placeholder="'Поиск подкатегории'"
                          :max-height="200"
                          :disabled="!product.selectedCategory || loadingSubcategories"
                          class="w-full min-w-[180px] text-xs multiselect-custom"
                        />
                        <div v-if="loadingSubcategories" class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-75 rounded">
                          <Loader2 class="w-4 h-4 animate-spin text-blue-500" />
                        </div>
                      </div>
                    </div>
                  </td>
                  <td class="px-3 py-2 text-sm text-gray-900">
                    <div class="space-y-2">
                      <div>
                        <label class="block text-xs text-gray-600 mb-1">Начальный остаток</label>
                        <input v-model.number="product.start_count" type="number" step="1" min="0" class="w-full text-sm border border-gray-300 rounded px-2 py-1" placeholder="Начальный остаток" />
                      </div>
                      <div>
                        <label class="block text-xs text-gray-600 mb-1">Единица измерения</label>
                        <Multiselect
                          v-model="product.selectedUnit"
                          :options="unitOptions"
                          label="label"
                          value="value"
                          :object="true"
                          placeholder="Выберите единицу"
                          :max-height="200"
                          class="w-full min-w-[150px] text-xs multiselect-custom"
                        />
                      </div>
                      <div v-if="productFieldsVisibility.price !== false">
                        <label class="block text-xs text-gray-600 mb-1">Стоимость</label>
                        <input v-model="product.price" type="number" min="0" step="0.01" class="w-full border border-gray-300 rounded px-2 py-1 text-xs" />
                      </div>
                    </div>
                  </td>
                  <td class="px-3 py-2 text-sm text-gray-900">
                    <input v-model="product.article" type="text" class="w-full text-sm border border-gray-300 rounded px-2 py-1" placeholder="Артикул" />
                  </td>
                  <td class="px-3 py-2 text-sm text-gray-900">
                    <button 
                      @click="removeProductFromImport(index)"
                      class="text-red-600 hover:text-red-800 p-1 rounded hover:bg-red-50 transition-colors relative group"
                      title="Удалить товар из списка"
                    >
                      <Trash2 class="w-4 h-4" />
                      <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-900 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-10">
                        Удалить товар из списка
                        <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
                      </div>
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

<script setup>
import { ref, computed, onMounted, reactive, watch } from 'vue'
import { apiRequest, getCategoriesWithCache } from '@/config/api'
import categoriesData from '../../../cats.json'
import ProductsMenu from './ProductsMenu.vue'
import toastr from 'toastr'
import Multiselect from '@vueform/multiselect'
import '@vueform/multiselect/themes/default.css'
import countriesData from '@/data/countries.json'
import { Edit, Trash2, RotateCcw, Search, X, Loader2, Image, ChevronLeft, ChevronRight, Plus } from 'lucide-vue-next'
import * as XLSX from 'xlsx'

// Состояние
const products = ref([])
const loading = ref(false)
const error = ref('')
const searchQuery = ref('')
const pagination = ref(null)
const searchTimeout = ref(null)
const showFilterModal = ref(false)
const filterLoading = ref(false)

// Состояния для удаления товара
const showDeleteModal = ref(false)
const productIdToDelete = ref(null)
const deletingProductId = ref(null)

// Состояния для импорта товаров
const showImportModal = ref(false)
const importFile = ref(null)
const parsedProducts = ref([])
const importLoading = ref(false)
const importSaving = ref(false)
const importError = ref('')

// Состояния для экспорта товаров
const exportLoading = ref(false)

// Состояния для складов
const warehouses = ref([])
const selectedWarehouseForImport = ref('')
const loadingWarehouses = ref(false)

const filter = reactive({
  category: null,
  subcategory: null,
  country: null,
  quantity: '',
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
  updated_at: '',
  warehouse_id: null,
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

const productFieldsVisibility = reactive({})
const customFields = ref([])
const loadingProductFields = ref(true)

async function loadProductFieldsVisibilityAndCustomFields() {
  loadingProductFields.value = true
  try {
    // Загрузка стандартных полей
    const settingsResp = await apiRequest('/user/settings', { method: 'GET' })
    let vis = settingsResp.data?.data?.personal?.product_fields_visibility
    if (typeof vis === 'string') {
      try { vis = JSON.parse(vis) } catch (e) { vis = null }
    }
    const defaults = Object.fromEntries(standardProductFields.map(f => [f.key, true]))
    Object.assign(productFieldsVisibility, { ...defaults, ...vis })
    // Загрузка кастомных полей
    const customResp = await apiRequest('/product-fields', { method: 'GET' })
    if (customResp.ok && customResp.data.success) {
      customFields.value = customResp.data.data || []
    } else {
      customFields.value = []
    }
  } catch (e) {
    customFields.value = []
    Object.assign(productFieldsVisibility, Object.fromEntries(standardProductFields.map(f => [f.key, true])))
  } finally {
    loadingProductFields.value = false
  }
}

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

// Открытие модалки удаления
function openDeleteModal(productId) {
  productIdToDelete.value = productId
  showDeleteModal.value = true
}

// Закрытие модалки удаления
function closeDeleteModal() {
  showDeleteModal.value = false
  productIdToDelete.value = null
  deletingProductId.value = null
}

// Подтверждение удаления товара
async function confirmDelete() {
  if (!productIdToDelete.value) return
  
  deletingProductId.value = productIdToDelete.value
  
  try {
    const response = await apiRequest(`/products/${productIdToDelete.value}`, {
      method: 'DELETE'
    })
    
    if (response.ok) {
      // Удаляем товар из списка без перезагрузки
      const index = products.value.findIndex(p => p.id === productIdToDelete.value)
      if (index !== -1) {
        products.value.splice(index, 1)
      }
      
      toastr.success('Товар успешно удален')
      closeDeleteModal()
    } else {
      toastr.error(response.data.message || 'Ошибка при удалении товара')
    }
  } catch (err) {
    console.error('Ошибка удаления товара:', err)
    toastr.error('Ошибка при удалении товара')
  } finally {
    deletingProductId.value = null
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
    console.log('Загружаем категории...')
    const categoriesData = await getCategoriesWithCache()
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

async function loadSubcategories(categoryId) {
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
  // Добавляем кастомные поля с префиксом custom_
  if (Array.isArray(customFields.value)) {
    customFields.value.forEach(field => {
      const val = filter[field.field_name]
      if (val !== null && val !== undefined && val !== '') {
        filterParams[`custom_${field.field_name}`] = val
      }
    })
  }
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
  filter.quantity = ''
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

// Открытие модалки импорта
async function openImportModal() {
  showImportModal.value = true
  parsedProducts.value = []
  importError.value = ''
  importFile.value = null
  
  // Загружаем категории если еще не загружены
  if (!categoryOptions.value || categoryOptions.value.length === 0) {
    try {
      await loadCategories()
    } catch (error) {
      console.error('Ошибка загрузки категорий при открытии модалки:', error)
    }
  }
  
  // Загружаем склады если еще не загружены
  if (!warehouses.value || warehouses.value.length === 0) {
    try {
      await loadWarehouses()
    } catch (error) {
      console.error('Ошибка загрузки складов при открытии модалки:', error)
    }
  }
  
  // Единицы измерения определены статически, загрузка не требуется
}

// Закрытие модалки импорта
function closeImportModal() {
  showImportModal.value = false
  parsedProducts.value = []
  importError.value = ''
  importFile.value = null
  importLoading.value = false
  importSaving.value = false
  selectedWarehouseForImport.value = ''
}

// Обработка загрузки файла
async function handleFileUpload(event) {
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
      const parsedProduct = {
        name: product['Название']?.toString() || '',
        description: product['Описание']?.toString() || '',
        category: product['Категория']?.toString() || product['Категория товара']?.toString() || '',
        subcategory: product['Подкатегория']?.toString() || product['Подкатегория товара']?.toString() || '',
        country: product['Страна']?.toString() || '',
        supplier: product['Поставщик']?.toString() || '',
        article: product['Артикул']?.toString() || '',
        code: product['Код']?.toString() || '',
        external_code: product['Внешний код']?.toString() || '',
        unit: product['Единица измерения']?.toString() || product['Ед. изм.']?.toString() || product['Единица']?.toString() || '',
                    start_count: (product['Начальный остаток'] !== undefined && product['Начальный остаток'] !== '')
                        ? parseInt(product['Начальный остаток'])
                        : (product['Количество'] !== undefined && product['Количество'] !== '')
                            ? parseInt(product['Количество'])
                            : 0,
        price: product['Стоимость'] ? parseFloat(product['Стоимость']) : 0,
        weight: product['Вес (кг)'] ? parseFloat(product['Вес (кг)']) : null,
        volume: product['Объем (л)'] ? parseFloat(product['Объем (л)']) : null,
        vat: product['НДС (%)']?.toString() || '',
        packing: product['Фасовка']?.toString() || '',
        accounting_type: product['Тип учета']?.toString() || '',
        product_type: product['Тип продукции']?.toString() || '',
        barcode_type: product['Тип штрихкода']?.toString() || '',
        barcode: product['Штрихкод']?.toString() || '',
        cash_register_tax: product['Система налогообложения']?.toString() || '',
        cash_register_type: product['Признак предмета расчета']?.toString() || 'Товар',
        // Добавляем селекты для интерфейса
        selectedCategory: null,
        selectedSubcategory: null,
        selectedUnit: null,
        subcategoryOptions: []
      }
      
      products.push(parsedProduct)
    }
    
    if (products.length === 0) {
      throw new Error('Не найдено товаров для импорта')
    }
    
    // Убеждаемся, что категории и единицы измерения загружены
    if (!categoryOptions.value || !Array.isArray(categoryOptions.value)) {
      console.warn('Категории не загружены, пропускаем автоматическое заполнение категорий')
      // Попробуем загрузить категории
      try {
        await loadCategories()
      } catch (error) {
        console.error('Не удалось загрузить категории:', error)
      }
    }
    if (!unitOptions || !Array.isArray(unitOptions)) {
      console.warn('Единицы измерения не определены, пропускаем автоматическое заполнение единиц')
    }
    
    // Автоматически заполняем категории и единицы измерения из Excel файла
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
              if (product.subcategory) {
                try {
                  const response = await apiRequest(`/categories/${foundCategory.value}/subcategories`)
                  if (response.ok && response.data && response.data.data) {
                    product.subcategoryOptions = response.data.data.map(sub => ({
                      label: sub.name_ru || sub.name || 'Без названия',
                      value: sub.subcategory_id || sub.id
                    }))
                    
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
                } catch (error) {
                  console.error('Ошибка загрузки подкатегорий:', error)
                }
              }
            }
          }
        }
      }
      
      // Заполняем единицу измерения, если она есть в Excel
      if (product.unit && unitOptions && Array.isArray(unitOptions)) {
        const foundUnit = unitOptions.find(unit => 
          unit && unit.label && product.unit &&
          (unit.label.toLowerCase() === product.unit.toLowerCase() ||
           unit.label.toLowerCase().includes(product.unit.toLowerCase()) ||
           product.unit.toLowerCase().includes(unit.label.toLowerCase()))
        )
        if (foundUnit) {
          product.selectedUnit = foundUnit
        }
      }
    }
    
    parsedProducts.value = products
    
  } catch (error) {
    console.error('Ошибка парсинга файла:', error)
    importError.value = error.message || 'Ошибка обработки файла'
  } finally {
    importLoading.value = false
  }
}

// Сохранение импортированных товаров
async function saveImportedProducts() {
  if (parsedProducts.value.length === 0 || !selectedWarehouseForImport.value) return;

  importSaving.value = true;
  importError.value = '';

  try { 
    const response = await apiRequest('/products/import-with-receipt', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        warehouse_id: selectedWarehouseForImport.value.value || selectedWarehouseForImport.value,
        products: parsedProducts.value.map(product => ({
          name: product.name,
          description: product.description,
          category: product.selectedCategory?.value || product.category,
          subcategory: product.selectedSubcategory?.value || product.subcategory,
          country: product.country,
          supplier: product.supplier,
          article: product.article,
          code: product.code,
          external_code: product.external_code,
          unit: product.selectedUnit?.value || product.unit,
          weight: product.weight,
          volume: product.volume,
          vat: product.vat,
          packing: product.packing,
          accounting_type: product.accounting_type,
          product_type: product.product_type,
          barcode_type: product.barcode_type,
          barcode: product.barcode,
          cash_register_tax: product.cash_register_tax,
          cash_register_type: product.cash_register_type,
          start_count: product.start_count,
          price: productFieldsVisibility.price === false ? 0 : (product.price ?? 0),
        }))
      })
    });

    if (response.ok && response.data.success) {
      toastr.success('Товары и оприходование успешно созданы!');
      // Сбросить модалку и форму
      closeImportModal();
      // Можно обновить список товаров
      loadProducts();
    } else {
      throw new Error(response.data.error || 'Ошибка массового импорта');
    }
  } catch (error) {
    importError.value = error.message || 'Ошибка массового импорта';
    toastr.error(importError.value);
  } finally {
    importSaving.value = false;
  }
}

// Экспорт товаров в Excel
async function exportProducts() {
  exportLoading.value = true
  
  try {
    // Получаем все товары для экспорта (без пагинации)
    const params = new URLSearchParams({
      per_page: '10000' // Получаем все товары
    })
    
    // Добавляем поисковый запрос
    if (searchQuery.value.trim()) {
      params.append('search', searchQuery.value.trim())
    }
    
    // Добавляем параметры фильтра
    const filterParams = createFilterParams()
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
      const allProducts = response.data.data.data || []
      
      // Подготавливаем данные для экспорта (только обязательные поля)
      const exportData = allProducts.map(product => {
        // Форматируем остаток - убираем лишние нули после запятой
        const formatQuantity = (qty) => {
          if (qty === null || qty === undefined || qty === '') return '-'
          const num = parseFloat(qty)
          return isNaN(num) ? '-' : num.toString()
        }
        
        // Форматируем цену - убираем лишние нули после запятой
        const formatPrice = (price) => {
          if (price === null || price === undefined || price === '') return '-'
          const num = parseFloat(price)
          return isNaN(num) ? '-' : num.toString()
        }
        
        return {
          'Название': product.name || '-',
          'Категория': product.category_name || product.category || '-',
          'Подкатегория': product.subcategory_name || product.subcategory || '-',
          'Склад': product.warehouse_name || product.warehouse || '-',
          'Остаток': formatQuantity(product.quantity),
          'Единица измерения': product.unit || '-',
                      'Стоимость': formatPrice(product.price),
          'Артикул': product.article || '-'
        }
      })
      
      // Создаем рабочую книгу Excel
      const workbook = XLSX.utils.book_new()
      const worksheet = XLSX.utils.json_to_sheet(exportData)
      
      // Устанавливаем ширину столбцов
      const columnWidths = [
        { wch: 30 }, // Название
        { wch: 20 }, // Категория
        { wch: 20 }, // Подкатегория
        { wch: 15 }, // Склад
        { wch: 12 }, // Остаток
        { wch: 15 }, // Единица измерения
        { wch: 12 }, // Стоимость
        { wch: 15 }  // Артикул
      ]
      worksheet['!cols'] = columnWidths
      
      // Добавляем лист в книгу
      XLSX.utils.book_append_sheet(workbook, worksheet, 'Товары')
      
      // Генерируем имя файла с текущей датой
      const now = new Date()
      const dateStr = now.toISOString().split('T')[0]
      const timeStr = now.toTimeString().split(' ')[0].replace(/:/g, '-')
      const fileName = `товары_${dateStr}_${timeStr}.xlsx`
      
      // Скачиваем файл
      XLSX.writeFile(workbook, fileName)
      
      toastr.success(`Экспортировано ${exportData.length} товаров`)
      
    } else {
      toastr.error('Ошибка загрузки товаров для экспорта')
    }
    
  } catch (error) {
    console.error('Ошибка экспорта:', error)
    toastr.error('Ошибка экспорта товаров')
  } finally {
    exportLoading.value = false
  }
}

// Загрузка складов
async function loadWarehouses() {
  loadingWarehouses.value = true
  try {
    const response = await apiRequest('/warehouses', { method: 'GET' })
    if (response.ok && response.data.success) {
      warehouses.value = response.data.data || []
    } else {
      warehouses.value = []
    }
  } catch (e) {
    warehouses.value = []
  } finally {
    loadingWarehouses.value = false
  }
}

// Computed свойства для селектов
const warehouseOptions = computed(() => {
  return warehouses.value.map(w => ({ label: w.name, value: w.id }))
})

// Функция для обработки изменения категории
async function onCategoryChange(category, productIndex) {
  const product = parsedProducts.value[productIndex]
  if (!category) {
    product.selectedSubcategory = null
    product.subcategoryOptions = []
    return
  }
  
  try {
    console.log('Загружаем подкатегории для категории:', category.value)
    
    // Сначала ищем в локальном файле
    const localCategory = categoriesData.find(cat => cat.category_id === category.value)
    if (localCategory && Array.isArray(localCategory.subcategories)) {
      console.log('Найдены подкатегории в локальном файле:', localCategory.subcategories)
      product.subcategoryOptions = localCategory.subcategories.map(sub => ({
        label: sub.name_ru || sub.name || 'Без названия',
        value: sub.subcategory_id
      }))
      console.log('Подкатегории в модалке обработаны:', product.subcategoryOptions)
    } else {
      // Если не найдено в локальном файле, загружаем через API
      const response = await apiRequest(`/categories/${category.value}/subcategories`)
      console.log('Ответ API подкатегорий в модалке:', response)
      
      if (response.ok && response.data && response.data.data) {
        product.subcategoryOptions = response.data.data.map(sub => ({
          label: sub.name_ru || sub.name || 'Без названия',
          value: sub.subcategory_id || sub.id
        }))
        console.log('Подкатегории в модалке обработаны:', product.subcategoryOptions)
      } else {
        console.error('Неверная структура данных подкатегорий в модалке:', response.data)
        product.subcategoryOptions = []
      }
    }
  } catch (error) {
    console.error('Ошибка загрузки подкатегорий в модалке:', error)
    product.subcategoryOptions = []
  }
}

// Функция для удаления товара из списка импорта
function removeProductFromImport(index) {
  parsedProducts.value.splice(index, 1)
}

// Функция для быстрого поиска категории в локальном файле
function findCategoryByName(categoryName) {
  if (!categoryName || !Array.isArray(categoriesData)) return null
  
  const normalizedName = categoryName.toLowerCase().trim()
  
  return categoriesData.find(cat => 
    (cat.name_ru && cat.name_ru.toLowerCase().includes(normalizedName)) ||
    (cat.name && cat.name.toLowerCase().includes(normalizedName)) ||
    (cat.name_en && cat.name_en.toLowerCase().includes(normalizedName)) ||
    (cat.name_uz && cat.name_uz.toLowerCase().includes(normalizedName))
  )
}

// Функция для быстрого поиска подкатегории в локальном файле
function findSubcategoryByName(categoryId, subcategoryName) {
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
  document.title = 'B2B SKLAD - Товары'
  loadProducts()
  loadWarehouses()
  
  // Очищаем кеш категорий для принудительной загрузки
  localStorage.removeItem('categories_cache')
  loadCategories()
  loadProductFieldsVisibilityAndCustomFields()
})
</script> 