<template>
  <nav class="bg-white shadow-sm border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-16">
        <!-- Логотип и основная навигация -->
        <div class="flex">
          <!-- Логотип -->
          <div class="flex-shrink-0 flex items-center">
            <a href="/admin" class="flex items-center gap-2">
              <img class="h-8 w-auto" src="../../assets/skladlogo.png" alt="" />
            </a>
          </div>

          <!-- Навигационные ссылки -->
          <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
            <!-- Статистика -->
            <router-link
              to="/admin"
              class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
              :class="{ 'border-blue-500 text-blue-600': $route.path === '/admin' }"
            >
              <BarChart3 class="w-5 h-5 mr-1" />
              Статистика
            </router-link>

            <!-- Пользователи -->
            <router-link
              to="/admin/users"
              class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
              :class="{ 'border-blue-500 text-blue-600': $route.path.startsWith('/admin/users') }"
            >
              <Users class="w-5 h-5 mr-1" />
              Пользователи
            </router-link>

            <!-- Товары -->
            <div class="relative flex items-center" data-menu>
              <button
                @click="toggleProductsMenu"
                class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
                :class="{ 'border-blue-500 text-blue-600': $route.path.startsWith('/admin/products') }"
              >
                <Package class="w-5 h-5 mr-1" />
                Товары
                <ChevronDown class="ml-1 w-4 h-4" />
              </button>

              <!-- Выпадающее меню товаров -->
              <div
                v-if="productsMenuOpen"
                class="absolute z-10 left-0 top-full mt-1 w-48 rounded-md shadow-lg bg-white"
              >
                <div class="py-1">
                  <router-link
                    to="/admin/products"
                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                    @click="productsMenuOpen = false"
                  >
                    Товары
                  </router-link>
                  <router-link
                    to="/admin/products/receipts"
                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                    @click="productsMenuOpen = false"
                  >
                    Оприходования
                  </router-link>
                  <router-link
                    to="/admin/products/write-offs"
                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                    @click="productsMenuOpen = false"
                  >
                    Списания
                  </router-link>
                  <router-link
                    to="/admin/products/inventory"
                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                    @click="productsMenuOpen = false"
                  >
                    Инвентаризации
                  </router-link>
                  <router-link
                    to="/admin/products/transfers"
                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                    @click="productsMenuOpen = false"
                  >
                    Перемещения
                  </router-link>
                  <router-link
                    to="/admin/products/balances"
                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                    @click="productsMenuOpen = false"
                  >
                    Остатки
                  </router-link>
                  <router-link
                    to="/admin/warehouses"
                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                    @click="productsMenuOpen = false"
                  >
                    Склады
                  </router-link>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Правая часть - выход и мобильная кнопка -->
        <div class="flex items-center">
          <!-- Мобильная кнопка меню -->
          <div class="flex sm:hidden">
            <button 
              @click="toggleMobileMenu"
              class="text-gray-700 hover:text-gray-900 p-2"
            >
              <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
              </svg>
            </button>
          </div>

          <!-- Выход (только для десктопа) -->
          <div class="hidden sm:flex">
            <button
              @click="handleLogout"
              class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
            >
              <LogOut class="w-4 h-4 mr-2" />
              Выход
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Мобильное меню -->
    <div v-if="mobileMenuOpen" class="sm:hidden" style="height: 100vh;">
      <!-- Темная подложка на всю высоту -->
      <div class="fixed inset-0 z-[9999] bg-gray-900/50" @click="toggleMobileMenu"></div>
      <!-- Блок меню с белым фоном -->
      <div class="fixed inset-y-0 right-0 z-[9999] w-full max-w-xs bg-white shadow-xl flex flex-col">
        <!-- Заголовок меню -->
        <div class="flex items-center justify-between p-4 border-b border-gray-200 bg-white">
          <h2 class="text-lg font-semibold text-gray-900">Админ панель</h2>
          <button
            @click="toggleMobileMenu"
            class="text-gray-400 hover:text-gray-600"
          >
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
        
        <!-- Скроллируемый контент меню -->
        <div class="flex-1 overflow-y-auto bg-white" style="padding-bottom: 100px;">
          <div class="px-4 py-6 space-y-6">
            <!-- Статистика -->
            <div>
              <h3 class="text-sm font-semibold text-gray-900 mb-3">Основное</h3>
              <div class="space-y-2">
                <router-link
                  to="/admin"
                  class="flex items-center gap-3 text-sm text-gray-700 hover:text-blue-600 py-2 pl-4"
                  @click="toggleMobileMenu"
                >
                  <BarChart3 class="w-5 h-5" />
                  Статистика
                </router-link>
                <router-link
                  to="/admin/users"
                  class="flex items-center gap-3 text-sm text-gray-700 hover:text-blue-600 py-2 pl-4"
                  @click="toggleMobileMenu"
                >
                  <Users class="w-5 h-5" />
                  Пользователи
                </router-link>
              </div>
            </div>

            <!-- Товары -->
            <div>
              <h3 class="text-sm font-semibold text-gray-900 mb-3">Товары</h3>
              <div class="space-y-2">
                <router-link
                  to="/admin/products"
                  class="block text-sm text-gray-700 hover:text-blue-600 py-2 pl-4"
                  @click="toggleMobileMenu"
                >
                  Товары
                </router-link>
                <router-link
                  to="/admin/products/receipts"
                  class="block text-sm text-gray-700 hover:text-blue-600 py-2 pl-4"
                  @click="toggleMobileMenu"
                >
                  Оприходования
                </router-link>
                <router-link
                  to="/admin/products/write-offs"
                  class="block text-sm text-gray-700 hover:text-blue-600 py-2 pl-4"
                  @click="toggleMobileMenu"
                >
                  Списания
                </router-link>
                <router-link
                  to="/admin/products/inventory"
                  class="block text-sm text-gray-700 hover:text-blue-600 py-2 pl-4"
                  @click="toggleMobileMenu"
                >
                  Инвентаризации
                </router-link>
                <router-link
                  to="/admin/products/transfers"
                  class="block text-sm text-gray-700 hover:text-blue-600 py-2 pl-4"
                  @click="toggleMobileMenu"
                >
                  Перемещения
                </router-link>
                <router-link
                  to="/admin/products/balances"
                  class="block text-sm text-gray-700 hover:text-blue-600 py-2 pl-4"
                  @click="toggleMobileMenu"
                >
                  Остатки
                </router-link>
                <router-link
                  to="/admin/warehouses"
                  class="block text-sm text-gray-700 hover:text-blue-600 py-2 pl-4"
                  @click="toggleMobileMenu"
                >
                  Склады
                </router-link>
              </div>
            </div>

            <!-- Выход -->
            <div class="border-t border-gray-200 pt-4">
              <button
                @click="handleLogout"
                class="flex items-center gap-3 text-gray-700 py-2 w-full text-left"
              >
                <LogOut class="w-5 h-5 text-red-600" />
                <span class="text-red-600">Выйти</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { BarChart3, Package, ChevronDown, Users, Settings, LogOut } from 'lucide-vue-next'

const router = useRouter()
const productsMenuOpen = ref(false)
const mobileMenuOpen = ref(false)

// Переключение меню товаров
const toggleProductsMenu = () => {
  productsMenuOpen.value = !productsMenuOpen.value
}

// Переключение мобильного меню
const toggleMobileMenu = () => {
  mobileMenuOpen.value = !mobileMenuOpen.value
  
  // Управляем скроллом body
  if (mobileMenuOpen.value) {
    document.body.style.overflow = 'hidden'
  } else {
    document.body.style.overflow = ''
  }
}

// Выход из системы
const handleLogout = () => {
  localStorage.removeItem('auth_token')
  localStorage.removeItem('user')
  mobileMenuOpen.value = false
  // Восстанавливаем скролл body
  document.body.style.overflow = ''
  router.push('/auth')
}

// Закрытие меню при клике вне его
const handleClickOutside = (event) => {
  if (!event.target.closest('[data-menu]')) {
    productsMenuOpen.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script> 