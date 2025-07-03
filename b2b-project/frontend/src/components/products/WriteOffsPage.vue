<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Внутреннее меню навигации -->
    <ProductsMenu />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Заголовок страницы -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Списания</h1>
        <p class="mt-2 text-gray-600">Управление списаниями товаров</p>
      </div>
      
      <!-- Основной контент -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="text-center py-12">
          <div class="mx-auto h-12 w-12 text-gray-400">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
          </div>
          <h3 class="mt-2 text-sm font-medium text-gray-900">Списания</h3>
          <p class="mt-1 text-sm text-gray-500">Здесь будет список списаний</p>
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
  document.title = 'B2B Storage - Списания'
})
</script> 