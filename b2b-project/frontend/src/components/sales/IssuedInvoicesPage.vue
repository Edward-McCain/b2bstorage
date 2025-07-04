<template>
  <div class="min-h-screen bg-gray-50" style="padding-top: 66px;">
    <SalesMenu />
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Счета-фактуры выданные</h1>
        <p class="text-gray-700 mb-4">Здесь отображаются все выданные счета-фактуры покупателям. Вы можете просматривать, фильтровать и скачивать документы.</p>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Номер</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Покупатель</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Дата</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Сумма</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Действия</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">СФ-2024-001</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">ООО "Покупатель 1"</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">21.01.2024</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">125,000 ₽</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <button class="text-blue-600 hover:text-blue-900 mr-3">Скачать</button>
                  <button class="text-gray-600 hover:text-gray-900">Просмотр</button>
                </td>
              </tr>
              <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">СФ-2024-002</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">ИП Петров</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">22.01.2024</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">89,500 ₽</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <button class="text-blue-600 hover:text-blue-900 mr-3">Скачать</button>
                  <button class="text-gray-600 hover:text-gray-900">Просмотр</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup>
import SalesMenu from './SalesMenu.vue'
document.title = 'Счета-фактуры выданные - B2B Storage'
</script> 