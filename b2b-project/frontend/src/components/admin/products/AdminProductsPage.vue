<template>
  <AdminLayout>
    <!-- Заголовок страницы -->
    <div class="bg-white shadow-sm border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="py-6">
          <h1 class="text-3xl font-bold text-gray-900">Управление товарами</h1>
          <p class="mt-2 text-sm text-gray-600">Просмотр и управление всеми товарами в системе</p>
        </div>
      </div>
    </div>

    <!-- Основной контент -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

      <!-- Фильтры и поиск -->
      <div class="bg-white shadow rounded-lg mb-8">
        <div class="px-4 py-5 sm:p-6">
          <!-- Первая строка: поиск и склад -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <!-- Поиск -->
            <div>
              <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Поиск товаров</label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <Search class="h-5 w-5 text-gray-400" />
                </div>
                <input
                  type="text"
                  id="search"
                  v-model="searchQuery"
                  placeholder="Поиск по названию, артикулу или ИНН..."
                  class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 transition text-sm"
                />
              </div>
            </div>

            <!-- Фильтр по складу -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Склад</label>
              <template v-if="loadingWarehouses">
                <div class="w-full h-10 flex items-center justify-center bg-white border border-gray-200 rounded-lg">
                  <Loader2 class="h-5 w-5 text-blue-500 animate-spin" />
                  <span class="ml-2 text-sm text-gray-500">Загрузка складов...</span>
                </div>
              </template>
              <template v-else>
                <Multiselect
                  v-model="selectedWarehouse"
                  :options="warehouseOptions"
                  label="label"
                  value="value"
                  :object="true"
                  placeholder="Все склады"
                  :max-height="400"
                  class="w-full text-sm multiselect-custom"
                />
              </template>
            </div>
          </div>

          <!-- Вторая строка: категории и подкатегории -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <!-- Фильтр по категории -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Категория</label>
              <template v-if="loadingCategories">
                <div class="w-full h-10 flex items-center justify-center bg-white border border-gray-200 rounded-lg">
                  <Loader2 class="h-5 w-5 text-blue-500 animate-spin" />
                  <span class="ml-2 text-sm text-gray-500">Загрузка категорий...</span>
                </div>
              </template>
              <template v-else>
                <Multiselect
                  v-model="selectedCategory"
                  :options="categoryOptions"
                  label="label"
                  value="value"
                  :object="true"
                  placeholder="Все категории"
                  searchable
                  :search-placeholder="'Поиск категории'"
                  :max-height="400"
                  class="w-full text-sm multiselect-custom"
                  @change="onCategoryChange"
                />
              </template>
            </div>

            <!-- Фильтр по подкатегории -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Подкатегория</label>
              <template v-if="loadingSubcategories">
                <div class="w-full h-10 flex items-center justify-center bg-white border border-gray-200 rounded-lg">
                  <Loader2 class="h-5 w-5 text-blue-500 animate-spin" />
                  <span class="ml-2 text-sm text-gray-500">Загрузка подкатегорий...</span>
                </div>
              </template>
              <template v-else>
                <Multiselect
                  v-model="selectedSubcategory"
                  :options="subcategoryOptions"
                  label="label"
                  value="value"
                  :object="true"
                  placeholder="Все подкатегории"
                  :disabled="!selectedCategory"
                  :max-height="400"
                  searchable
                  :search-placeholder="'Поиск подкатегории'"
                  class="w-full text-sm multiselect-custom"
                />
              </template>
            </div>
          </div>

          <!-- Кнопки поиска и сброса -->
          <div class="flex justify-end space-x-3">
            <button
              @click="resetFilters"
              class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-400 transition"
            >
              Сброс
            </button>
            <button
              @click="searchProducts"
              :disabled="isLoading"
              class="px-4 py-2 bg-blue-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-400 transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center"
            >
              <Loader2 v-if="isLoading" class="h-4 w-4 animate-spin mr-2" />
              Искать
            </button>
          </div>
        </div>
      </div>

      <!-- Таблица товаров -->
      <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
          <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Список товаров</h3>
          
          <!-- Индикатор загрузки -->
          <div v-if="isLoading" class="flex justify-center items-center py-12">
            <Loader2 class="h-8 w-8 text-blue-500 animate-spin" />
            <span class="ml-3 text-gray-600">Загрузка товаров...</span>
          </div>
          
          <!-- Таблица -->
          <div v-else class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-sm font-medium text-gray-500">
                    Товар
                  </th>
                  <th class="px-6 py-3 text-left text-sm font-medium text-gray-500">
                    Артикул
                  </th>
                  <th class="px-6 py-3 text-left text-sm font-medium text-gray-500">
                    Склад
                  </th>
                  <th class="px-6 py-3 text-left text-sm font-medium text-gray-500">
                    Категория
                  </th>
                  <th class="px-6 py-3 text-left text-sm font-medium text-gray-500">
                    Остаток
                  </th>
                  <th class="px-6 py-3 text-left text-sm font-medium text-gray-500">
                    Цена
                  </th>
                  <th class="px-6 py-3 text-left text-sm font-medium text-gray-500">
                    Добавил
                  </th>
                  <th class="px-6 py-3 text-left text-sm font-medium text-gray-500">
                    Дата добавления
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="product in products" :key="product.id">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="flex-shrink-0 h-10 w-10">
                        <img v-if="product.image_url" :src="product.image_url" alt="" class="h-10 w-10 rounded object-cover">
                        <div v-else class="h-10 w-10 rounded bg-gray-300 flex items-center justify-center">
                          <Package class="w-5 h-5 text-gray-600" />
                        </div>
                      </div>
                      <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900">{{ product.name }}</div>
                        <div v-if="product.description" class="text-sm text-gray-500 truncate max-w-xs">
                          {{ product.description }}
                        </div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ product.article || 'Не указан' }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ product.warehouse?.name || 'Не указан' }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    <div>
                      <div>{{ getCategoryDisplayName(product.category) || 'Не указана' }}</div>
                      <div v-if="product.subcategory?.name" class="text-xs text-gray-500">
                        {{ getSubcategoryDisplayName(product.subcategory) }}
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ product.quantity || 0 }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ formatPrice(product.price) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <button
                      @click="showUserModal(product.user)"
                      class="text-blue-600 hover:text-blue-800 text-sm font-medium"
                    >
                      {{ product.user?.user_name || 'Неизвестно' }}
                    </button>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ formatDate(product.created_at) }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Пагинация -->
          <div v-if="pagination.total > 0" class="mt-6 flex items-center justify-between">
            <div class="text-sm text-gray-700">
              Показано {{ (pagination.current_page - 1) * pagination.per_page + 1 }} - 
              {{ Math.min(pagination.current_page * pagination.per_page, pagination.total) }} 
              из {{ pagination.total }} товаров
            </div>
            <div class="flex space-x-2">
              <button
                @click="changePage(pagination.current_page - 1)"
                :disabled="pagination.current_page <= 1"
                class="px-3 py-1 border border-gray-300 rounded-md text-sm disabled:opacity-50 disabled:cursor-not-allowed"
              >
                Назад
              </button>
              <button
                @click="changePage(pagination.current_page + 1)"
                :disabled="pagination.current_page >= pagination.last_page"
                class="px-3 py-1 border border-gray-300 rounded-md text-sm disabled:opacity-50 disabled:cursor-not-allowed"
              >
                Вперед
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Модальное окно с данными пользователя -->
    <div v-if="showUserModalFlag" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900">Данные пользователя</h3>
            <button @click="closeUserModal" class="text-gray-400 hover:text-gray-600">
              <X class="h-6 w-6" />
            </button>
          </div>
          
          <div v-if="selectedUser" class="space-y-4">
            <!-- Аватар и основная информация -->
            <div class="flex items-center space-x-4">
              <div class="flex-shrink-0">
                <img v-if="selectedUser.avatar_url" :src="selectedUser.avatar_url" alt="" class="h-12 w-12 rounded-full">
                <div v-else class="h-12 w-12 rounded-full bg-gray-300 flex items-center justify-center">
                  <User class="w-6 h-6 text-gray-600" />
                </div>
              </div>
              <div>
                <div class="text-lg font-medium text-gray-900">{{ selectedUser.user_name }}</div>
                <div class="text-sm text-gray-500">{{ selectedUser.email }}</div>
              </div>
            </div>

            <!-- Информация о пользователе -->
            <div class="border-t pt-4">
              <h4 class="text-sm font-medium text-gray-900 mb-2">Личная информация</h4>
              <dl class="space-y-2 text-sm">
                <div class="flex justify-between">
                  <dt class="text-gray-500">Имя:</dt>
                  <dd class="text-gray-900">{{ selectedUser.first_name || 'Не указано' }}</dd>
                </div>
                <div class="flex justify-between">
                  <dt class="text-gray-500">Телефон:</dt>
                  <dd class="text-gray-900">{{ selectedUser.phone_number || 'Не указан' }}</dd>
                </div>
                <div class="flex justify-between">
                  <dt class="text-gray-500">Страна:</dt>
                  <dd class="text-gray-900">{{ selectedUser.country || 'Не указана' }}</dd>
                </div>
                <div class="flex justify-between">
                  <dt class="text-gray-500">Город:</dt>
                  <dd class="text-gray-900">{{ selectedUser.city || 'Не указан' }}</dd>
                </div>
                <div class="flex justify-between">
                  <dt class="text-gray-500">Дата регистрации:</dt>
                  <dd class="text-gray-900">{{ formatDate(selectedUser.created_at) }}</dd>
                </div>
              </dl>
            </div>

            <!-- Информация о компании -->
            <div v-if="selectedUser.company_name" class="border-t pt-4">
              <h4 class="text-sm font-medium text-gray-900 mb-2">Информация о компании</h4>
              <dl class="space-y-2 text-sm">
                <div class="flex justify-between">
                  <dt class="text-gray-500">Название:</dt>
                  <dd class="text-gray-900">{{ selectedUser.company_name }}</dd>
                </div>
                <div v-if="selectedUser.inn" class="flex justify-between">
                  <dt class="text-gray-500">ИНН:</dt>
                  <dd class="text-gray-900">{{ selectedUser.inn }}</dd>
                </div>
                <div v-if="selectedUser.company_description" class="flex justify-between">
                  <dt class="text-gray-500">Описание:</dt>
                  <dd class="text-gray-900">{{ selectedUser.company_description }}</dd>
                </div>
                <div v-if="selectedUser.com_address" class="flex justify-between">
                  <dt class="text-gray-500">Адрес:</dt>
                  <dd class="text-gray-900">{{ selectedUser.com_address }}</dd>
                </div>
                <div v-if="selectedUser.comp_phone" class="flex justify-between">
                  <dt class="text-gray-500">Телефон:</dt>
                  <dd class="text-gray-900">{{ selectedUser.comp_phone }}</dd>
                </div>
                <div v-if="selectedUser.comp_mail" class="flex justify-between">
                  <dt class="text-gray-500">Email:</dt>
                  <dd class="text-gray-900">{{ selectedUser.comp_mail }}</dd>
                </div>
              </dl>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import AdminLayout from '../AdminLayout.vue'
import { Search, Package, User, X, Loader2 } from 'lucide-vue-next'
import api from '../../../config/api.js'
import Multiselect from '@vueform/multiselect'
import '@vueform/multiselect/themes/default.css'
import { getCategoriesByUserSettings } from '../../../config/api.js'
import { transformCategoriesToOptions, transformSubcategoriesToOptions, getCategoryDisplayName, getSubcategoryDisplayName } from '../../../utils/categoryDisplayUtils'

// Данные
const products = ref([])
const filters = ref({
  warehouses: [],
  categories: []
})
const pagination = ref({
  current_page: 1,
  last_page: 1,
  per_page: 15,
  total: 0
})

// Фильтры
const searchQuery = ref('')
const isLoading = ref(false)

// Состояния загрузки фильтров
const loadingWarehouses = ref(false)
const loadingCategories = ref(false)
const loadingSubcategories = ref(false)

// Модальное окно пользователя
const showUserModalFlag = ref(false)
const selectedUser = ref(null)

// Данные категорий и подкатегорий
const categories = ref([])
const subcategories = ref([])

// Multiselect options
const warehouseOptions = ref([])
const categoryOptions = ref([])
const subcategoryOptions = ref([])

const selectedWarehouse = ref(null)
const selectedCategory = ref(null)
const selectedSubcategory = ref(null)

// Загрузка подкатегорий
const loadSubcategories = async (categoryId) => {
  if (!categoryId) {
    subcategoryOptions.value = []
    return
  }
  
  try {
    loadingSubcategories.value = true
    const response = await api.get(`/subcategories?category_id=${encodeURIComponent(categoryId)}`)
    
    if (response.data.success) {
      subcategoryOptions.value = transformSubcategoriesToOptions(response.data.data)
    }
  } catch (error) {
    console.error('Ошибка загрузки подкатегорий:', error)
    subcategoryOptions.value = []
  } finally {
    loadingSubcategories.value = false
  }
}

// Загрузка всех складов
const loadWarehouses = async () => {
  try {
    loadingWarehouses.value = true
    const response = await api.get('/admin/warehouses')
    if (response.data.success) {
      warehouseOptions.value = response.data.data.map(w => ({
        label: w.name,
        value: w.id
      }))
    }
  } catch (error) {
    console.error('Ошибка загрузки складов:', error)
    warehouseOptions.value = []
  } finally {
    loadingWarehouses.value = false
  }
}

// Загрузка всех категорий
const loadCategories = async () => {
  try {
    loadingCategories.value = true
    categories.value = await getCategoriesByUserSettings()
    categoryOptions.value = transformCategoriesToOptions(categories.value)
  } catch (error) {
    console.error('Ошибка загрузки категорий:', error)
    categoryOptions.value = []
  } finally {
    loadingCategories.value = false
  }
}

// Обработка изменения категории
const onCategoryChange = () => {
  selectedSubcategory.value = null
  loadSubcategories(selectedCategory.value?.value)
}

// Watch для автоматической загрузки подкатегорий при изменении категории
watch(selectedCategory, async (cat) => {
  selectedSubcategory.value = null
  subcategories.value = []
  if (cat && cat.value) {
    loadingSubcategories.value = true
    try {
      const response = await api.get(`/subcategories?category_id=${encodeURIComponent(cat.value)}`)
      if (response.data.success) {
        subcategories.value = response.data.data || []
        subcategoryOptions.value = transformSubcategoriesToOptions(subcategories.value)
      }
    } catch (error) {
      console.error('Ошибка загрузки подкатегорий:', error)
    } finally {
      loadingSubcategories.value = false
    }
  }
})

// Форматирование цены
const formatPrice = (price) => {
  if (!price) return '0 ₽'
  return new Intl.NumberFormat('ru-RU', {
    style: 'currency',
    currency: 'RUB'
  }).format(price)
}

// Форматирование даты
const formatDate = (dateString) => {
  if (!dateString) return 'Не указано'
  const date = new Date(dateString)
  return date.toLocaleDateString('ru-RU', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

// Загрузка товаров
const loadProducts = async () => {
  try {
    isLoading.value = true
    
    // Подготавливаем данные для POST запроса
    const searchData = {
      page: pagination.value.current_page,
      per_page: pagination.value.per_page
    }
    
    if (searchQuery.value) {
      searchData.search = searchQuery.value
    }
    if (selectedWarehouse.value) {
      searchData.warehouse_id = selectedWarehouse.value.value
    }
    if (selectedCategory.value) {
      searchData.category_id = selectedCategory.value.value
    }
    if (selectedSubcategory.value) {
      searchData.subcategory_id = selectedSubcategory.value.value
    }
    
    const response = await api.post('/admin/products/search', searchData)
    if (response.data.success) {
      products.value = response.data.data.products
      pagination.value = response.data.data.pagination
    }
  } catch (error) {
    console.error('Ошибка загрузки товаров:', error)
  } finally {
    isLoading.value = false
  }
}

// Смена страницы
const changePage = (page) => {
  if (page >= 1 && page <= pagination.value.last_page) {
    pagination.value.current_page = page
    loadProducts()
  }
}

// Показать модальное окно пользователя
const showUserModal = (user) => {
  if (user) {
    selectedUser.value = user
    showUserModalFlag.value = true
  }
}

// Закрыть модальное окно
const closeUserModal = () => {
  showUserModalFlag.value = false
  selectedUser.value = null
}

// Сброс фильтров
const resetFilters = () => {
  searchQuery.value = ''
  selectedWarehouse.value = null
  selectedCategory.value = null
  selectedSubcategory.value = null
  loadProducts()
}

// Поиск товаров
const searchProducts = () => {
  pagination.value.current_page = 1
  loadProducts()
}

// Автоматическая перезагрузка при изменении фильтров - отключено
// watch([searchQuery, selectedWarehouse, selectedCategory, selectedSubcategory], () => {
//   pagination.value.current_page = 1
//   loadProducts()
// }, { debounce: 300 })

onMounted(async () => {
  // Загружаем фильтры и товары параллельно
  await Promise.all([
    loadWarehouses(),
    loadCategories(),
    loadProducts()
  ])
})
</script>

<style scoped>
.multiselect-custom,
.multiselect,
.multiselect__input,
.multiselect__option {
  font-size: 0.875rem !important;
}
.multiselect__content-wrapper {
  max-height: 400px !important;
}
</style> 