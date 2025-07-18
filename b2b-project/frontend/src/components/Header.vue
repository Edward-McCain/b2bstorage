<script setup>
// Компонент шапки сайта
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { getFileUrl } from '../config/api.js'
import CurrencySelector from './CurrencySelector.vue'

const router = useRouter()
const isAuthenticated = ref(false)
const user = ref(null)
const mobileMenuOpen = ref(false)
const userMenuOpen = ref(false)
const productsMenuOpen = ref(false)
const purchasesMenuOpen = ref(false)
const salesMenuOpen = ref(false)

// Computed свойство для правильного URL аватара
const avatarUrl = computed(() => {
  if (!user.value?.avatar_url) return null
  return getFileUrl(user.value.avatar_url)
})

// Проверка роли пользователя
const isAdmin = computed(() => {
  return user.value?.role === 1
})

onMounted(() => {
  const token = localStorage.getItem('auth_token')
  const userData = localStorage.getItem('user')
  
  if (token && userData) {
    isAuthenticated.value = true
    user.value = JSON.parse(userData)
  }
  
  // Добавляем глобальный обработчик события обновления аватара
  window.addEventListener('avatar-updated', (event) => {
    handleAvatarUpdated(event.detail)
  })
  
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
  // Восстанавливаем скролл body
  document.body.style.overflow = ''
  // Перенаправляем на главную страницу после выхода
  router.push('/')
}

const handleLogin = () => {
  router.push('/auth')
}

const toggleMobileMenu = () => {
  mobileMenuOpen.value = !mobileMenuOpen.value
  
  // Управляем скроллом body
  if (mobileMenuOpen.value) {
    document.body.style.overflow = 'hidden'
  } else {
    document.body.style.overflow = ''
  }
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

const goToAdminPanel = () => {
  router.push('/admin')
  userMenuOpen.value = false
}

const openSupport = () => {
  // Здесь можно добавить логику для открытия поддержки
  window.open('mailto:support@b2bstorage.ru', '_blank')
  userMenuOpen.value = false
}

const handleCurrencyChanged = (newCurrency) => {
  console.log('Currency changed to:', newCurrency)
  // Обновляем данные пользователя в компоненте
  if (user.value) {
    user.value.currency = newCurrency
  }
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

// Обработчик обновления аватара
const handleAvatarUpdated = (newAvatarUrl) => {
  console.log('Header: получено событие avatar-updated:', newAvatarUrl)
  if (user.value) {
    console.log('Header: обновляем аватар пользователя с', user.value.avatar_url, 'на', newAvatarUrl)
    user.value.avatar_url = newAvatarUrl
    // Обновляем данные в localStorage
    localStorage.setItem('user', JSON.stringify(user.value))
    console.log('Header: localStorage обновлен')
  } else {
    console.log('Header: user.value не найден')
  }
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
              </router-link> -->
              <router-link
                to="/products/transfers"
                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                role="menuitem"
                tabindex="-1"
                @click="closeProductsMenu"
              >
                Перемещения
              </router-link>
              <!-- <router-link
                to="/products/price-lists"
                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                role="menuitem"
                tabindex="-1"
                @click="closeProductsMenu"
              >
                Прайс-листы
              </router-link> -->
              <router-link
                to="/products/balances"
                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                role="menuitem"
                tabindex="-1"
                @click="closeProductsMenu"
              >
                Остатки
              </router-link>
              <router-link
                to="/warehouses"
                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                role="menuitem"
                tabindex="-1"
                @click="closeProductsMenu"
              >
                Склады
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
        <div class="relative" style="display: none;">
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
        <div class="relative" style="display: none;">
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
          class="text-sm font-semibold text-gray-900 hover:text-blue-600 transition-colors flex items-center gap-1" style="display: none;"
        >
          Аналитика
        </router-link>
        <router-link
          to="/counterparties"
          class="text-sm font-semibold text-gray-900 hover:text-blue-600 transition-colors flex items-center gap-1" style="display: none;"
        >
          Контрагенты
        </router-link>
        <router-link
          to="/api-docs"
          class="text-sm font-semibold text-gray-900 hover:text-blue-600 transition-colors flex items-center gap-1"
        >
          API Документация
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
              <span class="text-sm text-gray-700">{{ user?.first_name || (user?.first_name === '' ? 'Пользователь' : user?.first_name) }}</span>
              <!-- Аватар -->
              <div v-if="avatarUrl" class="h-8 w-8 rounded-full overflow-hidden">
                <img 
                  :src="avatarUrl" 
                  :alt="user?.user_name || 'Аватар'"
                  class="h-full w-full object-cover"
                  @error="console.error('Header: ошибка загрузки аватара:', avatarUrl)"
                />
              </div>
              <div v-else class="h-8 w-8 rounded-full bg-blue-700 flex items-center justify-center text-white font-medium text-sm">
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
                <!-- Выбор валюты -->
                <div class="px-4 py-2 border-b border-gray-100 hidden">
                  <CurrencySelector 
                    :current-currency="user?.currency || 'USD'"
                    @currency-changed="handleCurrencyChanged"
                  />
                </div>
                
                <!-- Админ панель (только для администраторов) -->
                <button
                  v-if="isAdmin"
                  @click="goToAdminPanel"
                  class="flex items-center gap-3 text-gray-700 block px-4 py-2 text-sm hover:bg-gray-100 w-full text-left cursor-pointer"
                  role="menuitem"
                  tabindex="-1"
                >
                  <svg class="h-5 w-5 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                  </svg>
                  Админ панель
                </button>
                
                <!-- Настройки аккаунта -->
                <button
                  @click="goToAccountSettings"
                  class="flex items-center gap-3 text-gray-700 block px-4 py-2 text-sm hover:bg-gray-100 w-full text-left cursor-pointer"
                  role="menuitem"
                  tabindex="-1"
                >
                  <svg class="h-5 w-5 text-blue-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/>
                    <circle cx="12" cy="12" r="3"/>
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
                  <svg class="h-5 w-5 text-blue-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path d="M22 10.5V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v10c0 1.1.9 2 2 2h6l5 4v-4H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v4.5"/>
                    <path d="M10 9.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z"/>
                    <path d="M14.5 8a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3Z"/>
                    <path d="M10 12.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z"/>
                    <path d="M14.5 11a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3Z"/>
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
                  <svg class="h-5 w-5 text-blue-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                    <polyline points="16,17 21,12 16,7"/>
                    <line x1="21" x2="9" y1="12" y2="12"/>
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

    <!-- Мобильное меню -->
    <div v-if="mobileMenuOpen" class="lg:hidden" style="height: 100vh;">
      <!-- Темная подложка на всю высоту -->
      <div class="fixed inset-0 z-[9999] bg-gray-900/50" @click="toggleMobileMenu"></div>
      <!-- Блок меню с белым фоном -->
      <div class="fixed inset-y-0 right-0 z-[9999] w-full max-w-xs bg-white shadow-xl flex flex-col">
        <!-- Заголовок меню -->
        <div class="flex items-center justify-between p-4 border-b border-gray-200 bg-white">
          <h2 class="text-lg font-semibold text-gray-900">Меню</h2>
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
          <!-- Товары -->
          <div>
            <h3 class="text-sm font-semibold text-gray-900 mb-3">Товары</h3>
            <div class="space-y-2">
              <router-link
                to="/products"
                class="block text-sm text-gray-700 hover:text-blue-600 py-2 pl-4"
                @click="toggleMobileMenu"
              >
                Товары
              </router-link>
              <router-link
                to="/products/receipts"
                class="block text-sm text-gray-700 hover:text-blue-600 py-2 pl-4"
                @click="toggleMobileMenu"
              >
                Оприходования
              </router-link>
              <router-link
                to="/products/write-offs"
                class="block text-sm text-gray-700 hover:text-blue-600 py-2 pl-4"
                @click="toggleMobileMenu"
              >
                Списания
              </router-link>
              <router-link
                to="/products/inventory"
                class="block text-sm text-gray-700 hover:text-blue-600 py-2 pl-4"
                @click="toggleMobileMenu"
              >
                Инвентаризации
              </router-link>
              <router-link
                to="/products/transfers"
                class="block text-sm text-gray-700 hover:text-blue-600 py-2 pl-4"
                @click="toggleMobileMenu"
              >
                Перемещения
              </router-link>
              <!-- <router-link
                to="/products/price-lists"
                class="block text-sm text-gray-700 hover:text-blue-600 py-2 pl-4"
                @click="toggleMobileMenu"
              >
                Прайс-листы
              </router-link> -->
              <router-link
                to="/products/balances"
                class="block text-sm text-gray-700 hover:text-blue-600 py-2 pl-4"
                @click="toggleMobileMenu"
              >
                Остатки
              </router-link>
              <router-link
                to="/warehouses"
                class="block text-sm text-gray-700 hover:text-blue-600 py-2 pl-4"
                @click="toggleMobileMenu"
              >
                Склады
              </router-link>
            </div>
          </div>

          <!-- Закупки -->
          <div style="display: none;">
            <h3 class="text-sm font-semibold text-gray-900 mb-3">Закупки</h3>
            <div class="space-y-2">
              <router-link
                to="/purchases"
                class="block text-sm text-gray-700 hover:text-blue-600 py-2 pl-4"
                @click="toggleMobileMenu"
              >
                Закупки
              </router-link>
              <router-link
                to="/purchases/supplier-orders"
                class="block text-sm text-gray-700 hover:text-blue-600 py-2 pl-4"
                @click="toggleMobileMenu"
              >
                Заказы поставщикам
              </router-link>
              <router-link
                to="/purchases/supplier-invoices"
                class="block text-sm text-gray-700 hover:text-blue-600 py-2 pl-4"
                @click="toggleMobileMenu"
              >
                Счета поставщиков
              </router-link>
              <router-link
                to="/purchases/received-invoices"
                class="block text-sm text-gray-700 hover:text-blue-600 py-2 pl-4"
                @click="toggleMobileMenu"
              >
                Полученные счета
              </router-link>
              <router-link
                to="/purchases/receipts"
                class="block text-sm text-gray-700 hover:text-blue-600 py-2 pl-4"
                @click="toggleMobileMenu"
              >
                Приемки
              </router-link>
              <router-link
                to="/purchases/supplier-returns"
                class="block text-sm text-gray-700 hover:text-blue-600 py-2 pl-4"
                @click="toggleMobileMenu"
              >
                Возвраты поставщикам
              </router-link>
              <router-link
                to="/purchases/purchase-management"
                class="block text-sm text-gray-700 hover:text-blue-600 py-2 pl-4"
                @click="toggleMobileMenu"
              >
                Управление закупками
              </router-link>
            </div>
          </div>

          <!-- Продажи -->
          <div style="display: none;" >
            <h3 class="text-sm font-semibold text-gray-900 mb-3">Продажи</h3>
            <div class="space-y-2">
              <router-link
                to="/sales"
                class="block text-sm text-gray-700 hover:text-blue-600 py-2 pl-4"
                @click="toggleMobileMenu"
              >
                Продажи
              </router-link>
              <router-link
                to="/sales/customer-invoices"
                class="block text-sm text-gray-700 hover:text-blue-600 py-2 pl-4"
                @click="toggleMobileMenu"
              >
                Счета покупателям
              </router-link>
              <router-link
                to="/sales/shipments"
                class="block text-sm text-gray-700 hover:text-blue-600 py-2 pl-4"
                @click="toggleMobileMenu"
              >
                Отгрузки
              </router-link>
              <router-link
                to="/sales/customer-returns"
                class="block text-sm text-gray-700 hover:text-blue-600 py-2 pl-4"
                @click="toggleMobileMenu"
              >
                Возвраты покупателей
              </router-link>
            </div>
          </div>

          <!-- Аналитика и Контрагенты -->
          <div class="space-y-2" style="display: none;">
            <router-link
              to="/analytics"
              class="block text-sm text-gray-700 hover:text-blue-600 py-2"
              @click="toggleMobileMenu"
            >
              Аналитика
            </router-link>
            <router-link
              to="/counterparties"
              class="block text-sm text-gray-700 hover:text-blue-600 py-2"
              @click="toggleMobileMenu"
            >
              Контрагенты
            </router-link>
          </div>

          <!-- API Документация -->
          <!-- <div class="space-y-2">
            <router-link
              to="/api-docs"
              class="block text-sm text-gray-700 hover:text-blue-600 py-2"
              @click="toggleMobileMenu"
            >
              API Документация
            </router-link>
          </div> -->

          <!-- Выбор валюты -->
          <div class="border-t border-gray-200 pt-4 hidden">
            <div class="px-4 py-2">
              <CurrencySelector 
                :current-currency="user?.currency || 'USD'"
                @currency-changed="handleCurrencyChanged"
              />
            </div>
          </div>
          
          <!-- Настройки аккаунта -->
          <div class="border-t border-gray-200 pt-4">
            <router-link
              to="/account-settings"
              class="flex items-center gap-3 text-gray-700 py-2"
              @click="toggleMobileMenu"
            >
              <svg class="h-5 w-5 text-blue-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/>
                <circle cx="12" cy="12" r="3"/>
              </svg>
              Настройки аккаунта
            </router-link>
            
            <button
              @click="openSupport"
              class="flex items-center gap-3 text-gray-700 py-2 w-full text-left"
            >
              <svg class="h-5 w-5 text-blue-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path d="M22 10.5V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v10c0 1.1.9 2 2 2h6l5 4v-4H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v4.5"/>
                <path d="M10 9.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z"/>
                <path d="M14.5 8a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3Z"/>
                <path d="M10 12.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z"/>
                <path d="M14.5 11a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3Z"/>
              </svg>
              Техническая поддержка
            </button>
            
            <button
              @click="handleLogout"
              class="flex items-center gap-3 text-gray-700 py-2 w-full text-left"
            >
              <svg class="h-5 w-5 text-blue-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                <polyline points="16,17 21,12 16,7"/>
                <line x1="21" x2="9" y1="12" y2="12"/>
              </svg>
              Выйти
            </button>
          </div>
        </div>
        </div>
      </div>
    </div>
  </header>
</template>

<style scoped>
/* Дополнительные стили для шапки */
</style> 