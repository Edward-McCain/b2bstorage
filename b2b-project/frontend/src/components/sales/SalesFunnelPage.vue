<template>
  <div class="min-h-screen bg-gray-50" style="padding-top: 66px;">
    <SalesMenu />
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Воронка продаж</h1>
        <p class="text-gray-700 mb-4">Анализируйте этапы воронки продаж, конверсию и эффективность работы отдела продаж.</p>
        <ul class="list-disc pl-6 text-gray-700 space-y-2">
          <li>Лиды: <span class="font-bold">120</span></li>
          <li>В работе: <span class="font-bold">45</span></li>
          <li>Сделки: <span class="font-bold">32</span></li>
          <li>Конверсия: <span class="font-bold">26.7%</span></li>
        </ul>
      </div>
    </div>
  </div>
</template>
<script setup>
import SalesMenu from './SalesMenu.vue'
document.title = 'Воронка продаж - B2B SKLAD'
</script> 