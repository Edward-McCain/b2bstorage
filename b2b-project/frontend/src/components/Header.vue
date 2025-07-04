<script setup>
// Компонент шапки сайта
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const isAuthenticated = ref(false)
const user = ref(null)
const mobileMenuOpen = ref(false)
const userMenuOpen = ref(false)
const productsMenuOpen = ref(false)
const purchasesMenuOpen = ref(false)
const salesMenuOpen = ref(false)

onMounted(() => {
  const token = localStorage.getItem('auth_token')
  const userData = localStorage.getItem('user')
  
  if (token && userData) {
    isAuthenticated.value = true
    user.value = JSON.parse(userData)
  }
  
  // Закрытие меню при клике вне его
  document.addEventListener('click', (event) => {
    const userMenu = document.getElementById('user-menu-button')
    const productsMenu = document.getElementById('products-menu-button')
    const purchasesMenu = document.getElementById('purchases-menu-button')
    const salesMenu = document.getElementById('sales-menu-button')
    
    if (userMenu && !userMenu.contains(event.target)) {
      userMenuOpen.value = false
    }
    
    if (productsMenu && !productsMenu.contains(event.target)) {
      productsMenuOpen.value = false
    }
    
    if (purchasesMenu && !purchasesMenu.contains(event.target)) {
      purchasesMenuOpen.value = false
    }
    
    if (salesMenu && !salesMenu.contains(event.target)) {
      salesMenuOpen.value = false
    }
  })
})

const handleLogout = () => {
  localStorage.removeItem('auth_token')
  localStorage.removeItem('user')
  isAuthenticated.value = false
  user.value = null
  mobileMenuOpen.value = false
  userMenuOpen.value = false
  productsMenuOpen.value = false
  purchasesMenuOpen.value = false
  salesMenuOpen.value = false
  // Обновляем страницу после выхода
  window.location.reload()
}

const handleLogin = () => {
  router.push('/auth')
}

const toggleMobileMenu = () => {
  mobileMenuOpen.value = !mobileMenuOpen.value
}

const toggleUserMenu = () => {
  userMenuOpen.value = !userMenuOpen.value
  // Закрываем другие меню
  productsMenuOpen.value = false
  purchasesMenuOpen.value = false
  salesMenuOpen.value = false
}

const closeUserMenu = () => {
  userMenuOpen.value = false
}

const goToAccountSettings = () => {
  router.push('/account-settings')
  userMenuOpen.value = false
}

const openSupport = () => {
  // Здесь можно добавить логику для открытия поддержки
  window.open('mailto:support@b2bstorage.ru', '_blank')
  userMenuOpen.value = false
}

const toggleProductsMenu = () => {
  productsMenuOpen.value = !productsMenuOpen.value
  // Закрываем другие меню
  userMenuOpen.value = false
  purchasesMenuOpen.value = false
  salesMenuOpen.value = false
}

const closeProductsMenu = () => {
  productsMenuOpen.value = false
}

const togglePurchasesMenu = () => {
  purchasesMenuOpen.value = !purchasesMenuOpen.value
  // Закрываем другие меню
  userMenuOpen.value = false
  productsMenuOpen.value = false
  salesMenuOpen.value = false
}

const closePurchasesMenu = () => {
  purchasesMenuOpen.value = false
}

const toggleSalesMenu = () => {
  salesMenuOpen.value = !salesMenuOpen.value
  // Закрываем другие меню
  userMenuOpen.value = false
  productsMenuOpen.value = false
  purchasesMenuOpen.value = false
}

const closeSalesMenu = () => {
  salesMenuOpen.value = false
}
</script>

<template>
  <header class="fixed inset-x-0 top-0 z-[9998] bg-white/90 backdrop-blur border-b border-gray-200">
    <nav class="flex items-center justify-between p-4 lg:px-8" aria-label="Global">
      <!-- Логотип -->
      <div class="flex lg:flex-1">
        <a href="/" class="-m-1.5 p-1.5 flex items-center gap-2">
          <img class="h-8 w-auto" src="../assets/skladlogo.png" alt="" />
        </a>
      </div>
      <!-- Мобильная навигация -->
      <div class="flex lg:hidden">
        <!-- Если пользователь авторизован -->
        <div v-if="isAuthenticated" class="flex items-center gap-2">
          <button 
            @click="toggleMobileMenu"
            class="text-gray-700 hover:text-gray-900 p-2"
          >
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
          </button>
        </div>
        <!-- Если пользователь не авторизован -->
        <button 
          v-else
          @click="handleLogin"
          class="border-2 text-white bg-blue-700 border-blue-700 px-3 py-1.5 rounded-lg font-semibold text-sm transition-colors cursor-pointer"
        >
          Войти
        </button>
      </div>
      <!-- Навигация (десктоп) - только для авторизованных пользователей -->
      <div v-if="isAuthenticated" class="hidden lg:flex lg:gap-x-12">
        <!-- Flyout Menu для Товары -->
        <div class="relative">
          <button
            @click="toggleProductsMenu"
            type="button"
            class="text-sm font-semibold text-gray-900 hover:text-blue-600 transition-colors flex items-center gap-1"
            id="products-menu-button"
            aria-expanded="false"
            aria-haspopup="true"
          >
            Товары
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
          </button>

          <!-- Dropdown menu для товаров -->
          <div
            v-if="productsMenuOpen"
            class="absolute left-0 z-[9999] mt-2 w-56 origin-top-left rounded-md bg-white shadow-lg focus:outline-none border border-gray-200"
            role="menu"
            aria-orientation="vertical"
            aria-labelledby="products-menu-button"
            tabindex="-1"
          >
            <div class="py-1" role="none">
              <router-link
                to="/products"
                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                role="menuitem"
                tabindex="-1"
                @click="closeProductsMenu"
              >
                Товары
              </router-link>
              <router-link
                to="/products/receipts"
                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                role="menuitem"
                tabindex="-1"
                @click="closeProductsMenu"
              >
                Оприходования
              </router-link>
              <router-link
                to="/products/write-offs"
                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                role="menuitem"
                tabindex="-1"
                @click="closeProductsMenu"
              >
                Списания
              </router-link>
              <router-link
                to="/products/inventory"
                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                role="menuitem"
                tabindex="-1"
                @click="closeProductsMenu"
              >
                Инвентаризации
              </router-link>
              <!-- <router-link
                to="/products/internal-orders"
                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                role="menuitem"
                tabindex="-1"
                @click="closeProductsMenu"
              >
                Внутренние заказы
              </router-link>
              <router-link
                to="/products/transfers"
                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                role="menuitem"
                tabindex="-1"
                @click="closeProductsMenu"
              >
                Перемещения
              </router-link> -->
              <router-link
                to="/products/price-lists"
                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                role="menuitem"
                tabindex="-1"
                @click="closeProductsMenu"
              >
                Прайс-листы
              </router-link>
              <router-link
                to="/products/balances"
                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                role="menuitem"
                tabindex="-1"
                @click="closeProductsMenu"
              >
                Остатки
              </router-link>
              <!-- <router-link
                to="/products/turnovers"
                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                role="menuitem"
                tabindex="-1"
                @click="closeProductsMenu"
              >
                Обороты
              </router-link>
              <router-link
                to="/products/serial-numbers"
                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                role="menuitem"
                tabindex="-1"
                @click="closeProductsMenu"
              >
                Сер. номера
              </router-link>
              <router-link
                to="/products/marking-codes"
                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                role="menuitem"
                tabindex="-1"
                @click="closeProductsMenu"
              >
                Коды маркировки
              </router-link>
              <router-link
                to="/products/marking"
                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                role="menuitem"
                tabindex="-1"
                @click="closeProductsMenu"
              >
                Маркировка
              </router-link> -->
            </div>
          </div>
        </div>
        <!-- Закупки -->
        <div class="relative">
          <button
            @click="togglePurchasesMenu"
            type="button"
            class="text-sm font-semibold text-gray-900 hover:text-blue-600 transition-colors flex items-center gap-1"
            id="purchases-menu-button"
            aria-expanded="false"
            aria-haspopup="true"
          >
            Закупки
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
          </button>
          <div
            v-if="purchasesMenuOpen"
            class="absolute left-0 z-[9999] mt-2 w-72 origin-top-left rounded-md bg-white shadow-lg focus:outline-none border border-gray-200"
            role="menu"
            aria-orientation="vertical"
            aria-labelledby="purchases-menu-button"
            tabindex="-1"
          >
            <div class="py-1" role="none">
              <router-link
                to="/purchases"
                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                role="menuitem"
                tabindex="-1"
                @click="closePurchasesMenu"
              >
                Закупки
              </router-link>
              <router-link
                to="/purchases/supplier-orders"
                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                role="menuitem"
                tabindex="-1"
                @click="closePurchasesMenu"
              >
                Заказы поставщикам
              </router-link>
              <router-link
                to="/purchases/supplier-invoices"
                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                role="menuitem"
                tabindex="-1"
                @click="closePurchasesMenu"
              >
                Счета поставщиков
              </router-link>
              <router-link
                to="/purchases/received-invoices"
                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                role="menuitem"
                tabindex="-1"
                @click="closePurchasesMenu"
              >
                Полученные счета
              </router-link>
              <router-link
                to="/purchases/receipts"
                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                role="menuitem"
                tabindex="-1"
                @click="closePurchasesMenu"
              >
                Приемки
              </router-link>
              <router-link
                to="/purchases/supplier-returns"
                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                role="menuitem"
                tabindex="-1"
                @click="closePurchasesMenu"
              >
                Возвраты поставщикам
              </router-link>
              <router-link
                to="/purchases/purchase-management"
                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                role="menuitem"
                tabindex="-1"
                @click="closePurchasesMenu"
              >
                Управление закупками
              </router-link>
            </div>
          </div>
        </div>
        <!-- Продажи -->
        <div class="relative">
          <button
            @click="toggleSalesMenu"
            type="button"
            class="text-sm font-semibold text-gray-900 hover:text-blue-600 transition-colors flex items-center gap-1"
            id="sales-menu-button"
            aria-expanded="false"
            aria-haspopup="true"
          >
            Продажи
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
          </button> 
          <div
            v-if="salesMenuOpen"
            class="absolute left-0 z-[9999] mt-2 w-72 origin-top-left rounded-md bg-white shadow-lg focus:outline-none border border-gray-200"
            role="menu"
            aria-orientation="vertical"
            aria-labelledby="sales-menu-button"
            tabindex="-1"
          >
            <div class="py-1" role="none">
              <router-link
                to="/sales"
                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                role="menuitem"
                tabindex="-1"
                @click="closeSalesMenu"
              >
                Продажи
              </router-link>
              <router-link
                to="/sales/customer-invoices"
                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                role="menuitem"
                tabindex="-1"
                @click="closeSalesMenu"
              >
                Счета покупателям
              </router-link>
              <router-link
                to="/sales/shipments"
                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                role="menuitem"
                tabindex="-1"
                @click="closeSalesMenu"
              >
                Отгрузки
              </router-link>
              <!-- <router-link
                to="/sales/commission-reports"
                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                role="menuitem"
                tabindex="-1"
                @click="closeSalesMenu"
              >
                Отчеты комиссионера
              </router-link> -->
              <router-link
                to="/sales/customer-returns"
                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                role="menuitem"
                tabindex="-1"
                @click="closeSalesMenu"
              >
                Возвраты покупателей
              </router-link>
              <!-- <router-link
                to="/sales/issued-invoices"
                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                role="menuitem"
                tabindex="-1"
                @click="closeSalesMenu"
              >
                Счета-фактуры выданные
              </router-link>
              <router-link
                to="/sales/profitability"
                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                role="menuitem"
                tabindex="-1"
                @click="closeSalesMenu"
              >
                Прибыльность
              </router-link>
              <router-link
                to="/sales/consignment-goods"
                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                role="menuitem"
                tabindex="-1"
                @click="closeSalesMenu"
              >
                Товары на реализации
              </router-link>
              <router-link
                to="/sales/sales-funnel"
                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                role="menuitem"
                tabindex="-1"
                @click="closeSalesMenu"
              >
                Воронка продаж
              </router-link>
              <router-link
                to="/sales/unit-economics"
                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                role="menuitem"
                tabindex="-1"
                @click="closeSalesMenu"
              >
                Юнит-экономика
              </router-link> -->
            </div>
          </div>
        </div>
        <router-link
          to="/analytics"
          class="text-sm font-semibold text-gray-900 hover:text-blue-600 transition-colors flex items-center gap-1"
        >
          Аналитика
        </router-link>
        <router-link
          to="/counterparties"
          class="text-sm font-semibold text-gray-900 hover:text-blue-600 transition-colors flex items-center gap-1"
        >
          Контрагенты
        </router-link>
      </div>
      <!-- Справа: кнопки авторизации -->
      <div class="hidden lg:flex lg:flex-1 lg:justify-end items-center gap-4">
        <!-- Если пользователь авторизован -->
        <div v-if="isAuthenticated" class="relative">
          <!-- Flyout Menu -->
          <div class="relative">
            <button
              @click="toggleUserMenu"
              type="button"
              class="flex items-center gap-3 text-sm rounded-full"
              id="user-menu-button"
              aria-expanded="false"
              aria-haspopup="true"
            >
              <span class="text-sm text-gray-700">{{ user?.user_name || 'Пользователь' }}</span>
              <!-- Аватар -->
              <div class="h-8 w-8 rounded-full bg-blue-700 flex items-center justify-center text-white font-medium text-sm">
                {{ (user?.user_name || 'П').charAt(0).toUpperCase() }}
              </div>
            </button>

            <!-- Dropdown menu -->
            <div
              v-if="userMenuOpen"
              class="absolute right-0 z-[9999] mt-2 w-56 origin-top-right rounded-md bg-white shadow-lg focus:outline-none border border-gray-200"
              role="menu"
              aria-orientation="vertical"
              aria-labelledby="user-menu-button"
              tabindex="-1"
            >
              <div class="py-1" role="none">
                <!-- Настройки аккаунта -->
                <button
                  @click="goToAccountSettings"
                  class="flex items-center gap-3 text-gray-700 block px-4 py-2 text-sm hover:bg-gray-100 w-full text-left cursor-pointer"
                  role="menuitem"
                  tabindex="-1"
                >
                  <svg class="h-5 w-5 text-blue-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  </svg>
                  Настройки аккаунта
                </button>
                
                <!-- Техническая поддержка -->
                <button
                  @click="openSupport"
                  class="flex items-center gap-3 text-gray-700 block px-4 py-2 text-sm hover:bg-gray-100 w-full text-left cursor-pointer"
                  role="menuitem"
                  tabindex="-1"
                >
                  <svg class="h-5 w-5 text-blue-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M12 2.25a9.75 9.75 0 100 19.5 9.75 9.75 0 000-19.5z" />
                  </svg>
                  Техническая поддержка
                </button>
                
                <!-- Разделитель -->
                <div class="border-t border-gray-100 my-1"></div>
                
                <!-- Выйти -->
                <button
                  @click="handleLogout"
                  class="flex items-center gap-3 text-gray-700 block px-4 py-2 text-sm hover:bg-gray-100 w-full text-left cursor-pointer"
                  role="menuitem"
                  tabindex="-1"
                >
                  <svg class="h-5 w-5 text-blue-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                  </svg>
                  Выйти
                </button>
              </div>
            </div>
          </div>
        </div>
        <!-- Если пользователь не авторизован -->
        <button 
          v-else
          @click="handleLogin"
          class="border-2 border-blue-700 text-white bg-blue-700 px-4 py-2 rounded-lg font-semibold text-sm transition-colors cursor-pointer"
        >
          Войти
        </button>
      </div>
    </nav>
  </header>
</template>

<style scoped>
/* Дополнительные стили для шапки */
</style> 