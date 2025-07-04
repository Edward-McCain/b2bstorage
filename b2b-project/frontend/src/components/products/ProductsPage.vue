<template>
  <div class="min-h-screen bg-gray-50" style="padding-top: 66px;">
    <!-- Внутреннее меню навигации -->
    <ProductsMenu />

    <!-- Верхнее меню и фильтры -->
    <div class="bg-white border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-wrap items-center gap-2 py-4">
        <router-link to="/products/create" class="flex items-center gap-1 bg-blue-50 border border-blue-200 text-blue-700 font-medium px-3 py-1.5 rounded text-sm">
          <span class="text-lg">＋</span>Товар
        </router-link>
        <button class="bg-gray-100 border border-gray-200 text-gray-700 font-medium px-3 py-1.5 rounded text-sm">Фильтр</button>
        <input 
          v-model="searchQuery" 
          @input="handleSearch"
          type="text" 
          placeholder="Наименование, код или артикул" 
          class="border border-gray-300 rounded px-3 py-1.5 text-sm w-64" 
        />
        <button class="bg-white border border-gray-300 px-3 py-1.5 rounded font-medium text-sm">Импорт <svg class="h-4 w-4 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg></button>
        <button class="bg-white border border-gray-300 px-3 py-1.5 rounded font-medium text-sm">Экспорт</button>
      </div>
    </div>

    <!-- Центральный контент -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col items-center justify-center min-h-[60vh]">
      <!-- Загрузка -->
      <div v-if="loading" class="flex items-center justify-center py-20">
        <div class="text-center">
          <svg class="animate-spin h-8 w-8 text-blue-500 mx-auto mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
          </svg>
          <p class="text-gray-600">Загрузка товаров...</p>
        </div>
      </div>

      <!-- Ошибка -->
      <div v-else-if="error" class="text-center py-20">
        <p class="text-red-500 mb-4">{{ error }}</p>
        <button @click="loadProducts" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
          Попробовать снова
        </button>
      </div>

      <!-- Нет товаров -->
      <template v-else-if="products.length === 0">
        <div class="flex flex-col md:flex-row items-center justify-center w-full mt-12 gap-8">
          <div class="flex flex-col items-center md:items-start">
            <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-6 text-center md:text-left">Здесь будут все ваши товары</h1>
            <div class="flex gap-4 mb-6 w-full justify-center md:justify-center">
              <router-link to="/products/create" class="flex items-center gap-2 bg-blue-100 hover:bg-blue-200 text-blue-900 font-semibold px-6 py-3 rounded-lg text-lg transition">
                <span>Добавить товар</span><span class="text-2xl">＋</span>
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
                  <th class="px-4 py-3 font-semibold text-left text-gray-900">Изображение</th>
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
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                      </div>
                    </div>
                  </td>
                  <td class="px-4 py-3">
                    <div class="font-medium text-gray-900">{{ product.name }}</div>
                    <div v-if="product.description" class="text-gray-500 text-xs mt-1 truncate max-w-xs">
                      {{ product.description }}
                    </div>
                  </td>
                  <td class="px-4 py-3 text-gray-900">{{ product.code || '-' }}</td>
                  <td class="px-4 py-3 text-gray-900">{{ product.article || '-' }}</td>
                  <td class="px-4 py-3">
                    <div class="text-gray-900">{{ product.category || '-' }}</div>
                    <div v-if="product.subcategory" class="text-gray-500 text-xs">{{ product.subcategory }}</div>
                  </td>
                  <td class="px-4 py-3 text-gray-900">{{ product.supplier || '-' }}</td>
                  <td class="px-4 py-3">
                    <div class="flex items-center space-x-2">
                      <!-- <router-link 
                        :to="`/products/edit/${product.id}`"
                        class="text-blue-600 hover:text-blue-800 hover:underline text-sm font-medium"
                      >
                        Редактировать
                      </router-link>
                      <button 
                        @click="deleteProduct(product.id)"
                        class="text-red-600 hover:text-red-800 hover:underline text-sm font-medium"
                      >
                        Удалить
                      </button> -->
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
                  class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  Назад
                </button>
                <button 
                  @click="changePage(pagination.current_page + 1)"
                  :disabled="pagination.current_page === pagination.last_page"
                  class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  Вперед
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
                      class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                      <span class="sr-only">Предыдущая</span>
                      <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                      </svg>
                    </button>
                    
                    <template v-for="page in visiblePages" :key="page">
                      <button 
                        v-if="page !== '...'"
                        @click="changePage(page)"
                        :class="[
                          page === pagination.current_page
                            ? 'z-10 bg-blue-50 border-blue-500 text-blue-600'
                            : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                          'relative inline-flex items-center px-4 py-2 border text-sm font-medium'
                        ]"
                      >
                        {{ page }}
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
                      class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                      <span class="sr-only">Следующая</span>
                      <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                      </svg>
                    </button>
                  </nav>
                </div>
              </div>
            </div>
          </div>
        </div>
      </template>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { apiRequest } from '@/config/api'
import ProductsMenu from './ProductsMenu.vue'
import toastr from 'toastr'

// Состояние
const products = ref([])
const loading = ref(false)
const error = ref('')
const searchQuery = ref('')
const pagination = ref(null)
const searchTimeout = ref(null)

// Загрузка товаров
async function loadProducts(page = 1) {
  loading.value = true
  error.value = ''
  
  try {
    const params = new URLSearchParams({
      page: page.toString(),
      per_page: '15'
    })
    
    if (searchQuery.value.trim()) {
      params.append('search', searchQuery.value.trim())
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
    loadProducts(1)
  }, 500)
}

// Смена страницы
function changePage(page) {
  if (page >= 1 && page <= pagination.value.last_page) {
    loadProducts(page)
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
      loadProducts(pagination.value.current_page)
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

onMounted(() => {
  document.title = 'B2B Storage - Товары'
  loadProducts()
})
</script> 