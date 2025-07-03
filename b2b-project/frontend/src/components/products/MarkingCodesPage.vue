<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Внутреннее меню навигации -->
    <ProductsMenu />
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Заголовок страницы -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Коды маркировки</h1>
        <p class="mt-2 text-gray-600">Управление кодами маркировки</p>
      </div>
      
      <!-- Основной контент -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="text-center py-12">
          <div class="mx-auto h-12 w-12 text-gray-400">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
          </div>
          <h3 class="mt-2 text-sm font-medium text-gray-900">Коды маркировки</h3>
          <p class="mt-1 text-sm text-gray-500">Здесь будет список кодов маркировки</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import ProductsMenu from './ProductsMenu.vue'

onMounted(() => {
  // Устанавливаем заголовок страницы
  document.title = 'B2B Storage - Коды маркировки'
})
</script> 