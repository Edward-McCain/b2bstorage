<template>
  <div class="bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <nav class="flex space-x-8 overflow-x-auto">
        <router-link 
          to="/sales" 
          class="border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap"
          :class="{ 'border-blue-700 text-blue-700': route.path === '/sales' }"
        >
          Продажи
        </router-link>
        <!-- <router-link 
          to="/sales/customer-orders" 
          class="border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap"
          :class="{ 'border-blue-700 text-blue-700': route.path === '/sales/customer-orders' }"
        >
          Заказы
        </router-link> -->
        <router-link 
          to="/sales/customer-invoices" 
          class="border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap"
          :class="{ 'border-blue-700 text-blue-700': route.path === '/sales/customer-invoices' }"
        >
          Счета
        </router-link>
        <router-link 
          to="/sales/shipments" 
          class="border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap"
          :class="{ 'border-blue-700 text-blue-700': route.path === '/sales/shipments' }"
        >
          Отгрузки
        </router-link>
        <router-link 
          to="/sales/customer-returns" 
          class="border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap"
          :class="{ 'border-blue-700 text-blue-700': route.path === '/sales/customer-returns' }"
        >
          Возвраты
        </router-link>
        <!-- <router-link 
          to="/sales/issued-invoices" 
          class="border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap"
          :class="{ 'border-blue-700 text-blue-700': route.path === '/sales/issued-invoices' }"
        >
          Счета-фактуры
        </router-link> -->
        <!-- <router-link 
          to="/sales/profitability" 
          class="border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap"
          :class="{ 'border-blue-700 text-blue-700': route.path === '/sales/profitability' }"
        >
          Прибыльность
        </router-link>
        <router-link 
          to="/sales/consignment-goods" 
          class="border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap"
          :class="{ 'border-blue-700 text-blue-700': route.path === '/sales/consignment-goods' }"
        >
          Товары на реализации
        </router-link> -->
      </nav>
    </div>
  </div>
</template>

<script setup>
import { useRoute } from 'vue-router'

const route = useRoute()

// Отладочная информация
console.log('Current sales route path:', route.path)

// Проверяем активные состояния
const isActive = (path) => {
  const isActivePath = route.path === path
  console.log(`Sales path ${path} is active:`, isActivePath)
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