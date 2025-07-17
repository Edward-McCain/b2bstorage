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

        <!-- Правая часть - выход -->
        <div class="flex items-center">
          <!-- Выход -->
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

    <!-- Мобильное меню -->
    <div v-if="mobileMenuOpen" class="sm:hidden">
      <div class="pt-2 pb-3 space-y-1">
        <router-link
          to="/admin"
          class="border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800 block pl-3 pr-4 py-2 border-l-4 text-base font-medium"
          :class="{ 'bg-blue-50 border-blue-500 text-blue-700': $route.path === '/admin' }"
        >
          Статистика
        </router-link>
        <router-link
          to="/admin/products"
          class="border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800 block pl-3 pr-4 py-2 border-l-4 text-base font-medium"
          :class="{ 'bg-blue-50 border-blue-500 text-blue-700': $route.path.startsWith('/admin/products') }"
        >
          Товары
        </router-link>
        <router-link
          to="/admin/users"
          class="border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800 block pl-3 pr-4 py-2 border-l-4 text-base font-medium"
          :class="{ 'bg-blue-50 border-blue-500 text-blue-700': $route.path.startsWith('/admin/users') }"
        >
          Пользователи
        </router-link>
        <router-link
          to="/admin/settings"
          class="border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800 block pl-3 pr-4 py-2 border-l-4 text-base font-medium"
          :class="{ 'bg-blue-50 border-blue-500 text-blue-700': $route.path.startsWith('/admin/settings') }"
        >
          Настройки
        </router-link>
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

// Выход из системы
const handleLogout = () => {
  localStorage.removeItem('auth_token')
  localStorage.removeItem('user')
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