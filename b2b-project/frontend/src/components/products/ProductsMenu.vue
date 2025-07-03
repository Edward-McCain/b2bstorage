<template>
  <div class="bg-white border-b border-gray-200 pt-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <nav class="flex space-x-8 overflow-x-auto">
        <router-link 
          to="/products" 
          class="border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap"
          :class="{ 'border-blue-700 text-blue-700': route.path === '/products' }"
        >
          Товары
        </router-link>
        <router-link 
          to="/products/receipts" 
          class="border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap"
          :class="{ 'border-blue-700 text-blue-700 font-semibold router-link-active': isActive('/products/receipts') }"
        >
          Оприходования
        </router-link>
        <router-link 
          to="/products/write-offs" 
          class="border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap"
          :class="{ 'border-blue-700 text-blue-700': route.path === '/products/write-offs' }"
        >
          Списания
        </router-link>
        <router-link 
          to="/products/inventory" 
          class="border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap"
          :class="{ 'border-blue-700 text-blue-700': route.path === '/products/inventory' }"
        >
          Инвентаризации
        </router-link>
        <router-link 
          to="/products/internal-orders" 
          class="border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap"
          :class="{ 'border-blue-700 text-blue-700': route.path === '/products/internal-orders' }"
        >
          Внутренние заказы
        </router-link>
        <router-link 
          to="/products/transfers" 
          class="border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap"
          :class="{ 'border-blue-700 text-blue-700': route.path === '/products/transfers' }"
        >
          Перемещения
        </router-link>
        <router-link 
          to="/products/price-lists" 
          class="border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap"
          :class="{ 'border-blue-700 text-blue-700': route.path === '/products/price-lists' }"
        >
          Прайс-листы
        </router-link>
        <router-link 
          to="/products/balances" 
          class="border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap"
          :class="{ 'border-blue-700 text-blue-700': route.path === '/products/balances' }"
        >
          Остатки
        </router-link>
        <router-link 
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
        </router-link>
      </nav>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()

// Отладочная информация
console.log('Current route path:', route.path)

// Проверяем активные состояния
const isActive = (path) => {
  const isActivePath = route.path === path
  console.log(`Path ${path} is active:`, isActivePath)
  return isActivePath
}
</script>

<style scoped>
.router-link-active {
  border-bottom-color: #1d4ed8 !important;
  color: #1d4ed8 !important;
  font-weight: 600 !important;
}
</style> 