<template>
  <div class="bg-white border-b border-gray-200 pt-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <nav class="flex space-x-8 overflow-x-auto">
        <router-link 
          to="/purchases" 
          class="border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap"
          :class="{ 'border-blue-700 text-blue-700': route.path === '/purchases' }"
        >
          Закупки
        </router-link>
        <router-link 
          to="/purchases/supplier-orders" 
          class="border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap"
          :class="{ 'border-blue-700 text-blue-700': route.path === '/purchases/supplier-orders' }"
        >
          Заказы поставщикам
        </router-link>
        <router-link 
          to="/purchases/supplier-invoices" 
          class="border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap"
          :class="{ 'border-blue-700 text-blue-700': route.path === '/purchases/supplier-invoices' }"
        >
          Счета поставщиков
        </router-link>
        <router-link 
          to="/purchases/receipts" 
          class="border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap"
          :class="{ 'border-blue-700 text-blue-700': route.path === '/purchases/receipts' }"
        >
          Приемки
        </router-link>
        <router-link 
          to="/purchases/supplier-returns" 
          class="border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap"
          :class="{ 'border-blue-700 text-blue-700': route.path === '/purchases/supplier-returns' }"
        >
          Возвраты поставщикам
        </router-link>
        <router-link 
          to="/purchases/received-invoices" 
          class="border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap"
          :class="{ 'border-blue-700 text-blue-700': route.path === '/purchases/received-invoices' }"
        >
          Счета-фактуры полученные
        </router-link>
        <router-link 
          to="/purchases/purchase-management" 
          class="border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap"
          :class="{ 'border-blue-700 text-blue-700': route.path === '/purchases/purchase-management' }"
        >
          Управление закупками
        </router-link>
      </nav>
    </div>
  </div>
</template>

<script setup>
import { useRoute } from 'vue-router'

const route = useRoute()

// Отладочная информация
console.log('Current purchases route path:', route.path)

// Проверяем активные состояния
const isActive = (path) => {
  const isActivePath = route.path === path
  console.log(`Purchases path ${path} is active:`, isActivePath)
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