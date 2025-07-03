<template>
  <div>
    <SalesMenu />
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Прибыльность</h1>
        <p class="text-gray-700 mb-4">Анализируйте прибыльность продаж по различным направлениям, товарам и клиентам.</p>
        <ul class="list-disc pl-6 text-gray-700 space-y-2">
          <li>Общая прибыль: <span class="font-bold">1.2M ₽</span></li>
          <li>Средняя маржа: <span class="font-bold">32%</span></li>
          <li>Топ-5 прибыльных товаров</li>
          <li>Топ-5 прибыльных клиентов</li>
        </ul>
      </div>
    </div>
  </div>
</template>
<script setup>
import SalesMenu from './SalesMenu.vue'
document.title = 'Прибыльность - B2B Storage'
</script> 