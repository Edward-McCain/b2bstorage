<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Внутреннее меню навигации -->
    <ProductsMenu />
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Заголовок страницы -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Перемещения</h1>
        <p class="mt-2 text-gray-600">Управление перемещениями товаров</p>
      </div>
      
      <!-- Основной контент -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="text-center py-12">
          <div class="mx-auto h-12 w-12 text-gray-400">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
            </svg>
          </div>
          <h3 class="mt-2 text-sm font-medium text-gray-900">Перемещения</h3>
          <p class="mt-1 text-sm text-gray-500">Здесь будет список перемещений</p>
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
  document.title = 'B2B Storage - Перемещения'
})
</script> 