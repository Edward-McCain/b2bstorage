<template>
  <div class="bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" style="padding-top: 10px;">
      <nav class="flex space-x-8 overflow-x-auto">
        <router-link 
          to="/products" 
          class="border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap"
          :class="{ 'border-blue-700 text-blue-700': route.path === '/products' }"
        >
          {{ t('ProductsMenu_1') }} <!-- Главная -->
        </router-link>
        <router-link 
          to="/products/receipts" 
          class="border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap"
          :class="{ 'border-blue-700 text-blue-700 font-semibold router-link-active': isActive('/products/receipts') }"
        >
          {{ t('ProductsMenu_2') }} <!-- Оприходования -->
        </router-link>
        <router-link 
          to="/products/write-offs" 
          class="border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap"
          :class="{ 'border-blue-700 text-blue-700 font-semibold router-link-active': isWriteOffActive }"
        >
          {{ t('ProductsMenu_3') }} <!-- Списания -->
        </router-link>
        <router-link 
          to="/products/inventory" 
          class="border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap"
          :class="{ 'border-blue-700 text-blue-700 font-semibold router-link-active': isInventoryActive }"
        >
          {{ t('ProductsMenu_4') }} <!-- Инвентаризации -->
        </router-link>
        <!-- <router-link 
          to="/products/internal-orders" 
          class="border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap"
          :class="{ 'border-blue-700 text-blue-700': route.path === '/products/internal-orders' }"
        >
          Внутренние заказы
        </router-link> --> <!-- Внутренние заказы -->
        <router-link 
          to="/products/transfers" 
          class="border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap"
          :class="{ 'border-blue-700 text-blue-700': route.path === '/products/transfers' }"
        >
          {{ t('ProductsMenu_6') }} <!-- Перемещения -->
        </router-link>
        <!-- <router-link 
          to="/products/price-lists" 
          class="border-b-2 border-transparent py-4 px-1 text-sm font-medium text-red-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap"
          :class="{ 'border-blue-700 text-blue-700': route.path === '/products/price-lists' }"
        >
          Прайс-листы
        </router-link> -->
        <router-link 
          to="/products/balances" 
          class="border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap"
          :class="{ 'border-blue-700 text-blue-700': route.path === '/products/balances' }"
        >
          {{ t('ProductsMenu_8') }} <!-- Остатки -->
        </router-link>
        <router-link 
          to="/warehouses" 
          class="border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap"
          :class="{ 'border-blue-700 text-blue-700': isWarehouseActive }"
        >
          {{ t('ProductsMenu_9') }} <!-- Склады -->
        </router-link>
        <router-link 
          to="/products/logs" 
          class="border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap"
          :class="{ 'border-blue-700 text-blue-700': route.path === '/products/logs' }"
        >
          {{ t('ProductsMenu_10') }} <!-- Логи -->
        </router-link>
        <!-- <router-link 
          to="/products/turnovers" 
          class="border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap"
          :class="{ 'border-blue-700 text-blue-700': route.path === '/products/turnovers' }"
        >
          Обороты
        </router-link>
        <router-link 
          to="/products/serial-numbers" 
          class="border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap"
          :class="{ 'border-blue-700 text-blue-700': route.path === '/products/serial-numbers' }"
        >
          Сер. номера
        </router-link>
        <router-link 
          to="/products/marking-codes" 
          class="border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap"
          :class="{ 'border-blue-700 text-blue-700': route.path === '/products/marking-codes' }"
        >
          Коды маркировки
        </router-link>
        <router-link 
          to="/products/marking" 
          class="border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap"
          :class="{ 'border-blue-700 text-blue-700': route.path === '/products/marking' }"
        >
          Маркировка
        </router-link> -->
      </nav>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import { t } from '@/locales'

const route = useRoute()

// Проверяем активные состояния
const isActive = (path) => {
  return route.path === path
}

// Проверяем активное состояние для складов
const isWarehouseActive = computed(() => {
  return route.path === '/warehouses' || 
         route.path === '/warehouses/create' || 
         route.path.startsWith('/warehouses/edit/')
})

// Проверяем активное состояние для списаний
const isWriteOffActive = computed(() => {
  return route.path === '/products/write-offs' || 
         route.path === '/products/write-offs/create' || 
         route.path.startsWith('/products/write-offs/') && 
         !route.path.startsWith('/products/write-offs/edit/')
})

// Проверяем активное состояние для инвентаризаций
const isInventoryActive = computed(() => {
  return route.path === '/products/inventory' || 
         route.path === '/products/inventory/create' || 
         route.path.startsWith('/products/inventory/')
})
</script>

<style scoped>
.router-link-active {
  border-bottom-color: #1d4ed8 !important;
  color: #1d4ed8 !important;
  font-weight: 600 !important;
}
</style> 