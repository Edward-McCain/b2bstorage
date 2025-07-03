<template>
  <div>
    <SalesMenu />
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Юнит-экономика</h1>
        <p class="text-gray-700 mb-4">Анализируйте показатели юнит-экономики по товарам, клиентам и каналам продаж.</p>
        <ul class="list-disc pl-6 text-gray-700 space-y-2">
          <li>Средний доход на единицу: <span class="font-bold">5,200 ₽</span></li>
          <li>Средняя себестоимость: <span class="font-bold">3,500 ₽</span></li>
          <li>Средняя маржа: <span class="font-bold">32.7%</span></li>
          <li>Топ-3 прибыльных канала продаж</li>
        </ul>
      </div>
    </div>
  </div>
</template>
<script setup>
import SalesMenu from './SalesMenu.vue'
document.title = 'Юнит-экономика - B2B Storage'
</script> 