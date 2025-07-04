<template>
  <div class="min-h-screen bg-gray-50" style="padding-top: 66px;">
    <SalesMenu />
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Товары на реализации</h1>
        <p class="text-gray-700 mb-4">Учет товаров, переданных на реализацию. Здесь вы можете отслеживать остатки, продажи и возвраты по комиссионным товарам.</p>
        <ul class="list-disc pl-6 text-gray-700 space-y-2">
          <li>Всего на реализации: <span class="font-bold">320 позиций</span></li>
          <li>Продано за месяц: <span class="font-bold">48 позиций</span></li>
          <li>Возвращено: <span class="font-bold">3 позиции</span></li>
        </ul>
      </div>
    </div>
  </div>
</template>
<script setup>
import SalesMenu from './SalesMenu.vue'
document.title = 'Товары на реализации - B2B Storage'
</script> 