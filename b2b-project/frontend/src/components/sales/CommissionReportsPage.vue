<template>
  <div>
    <SalesMenu />
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Отчеты комиссионера</h1>
        <p class="text-gray-700 mb-4">Аналитика и отчеты по продажам. Здесь вы можете анализировать эффективность продаж, комиссии и доходы.</p>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
          <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Общая выручка</h3>
            <p class="text-2xl font-bold text-blue-600">2.4M ₽</p>
            <p class="text-sm text-gray-600">За текущий месяц</p>
          </div>
          <div class="bg-green-50 rounded-lg p-4 border border-green-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Комиссия</h3>
            <p class="text-2xl font-bold text-green-600">180K ₽</p>
            <p class="text-sm text-gray-600">7.5% от выручки</p>
          </div>
          <div class="bg-purple-50 rounded-lg p-4 border border-purple-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Количество сделок</h3>
            <p class="text-2xl font-bold text-purple-600">156</p>
            <p class="text-sm text-gray-600">Средний чек: 15.4K ₽</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import SalesMenu from './SalesMenu.vue'
document.title = 'Отчеты комиссионера - B2B Storage'
</script> 