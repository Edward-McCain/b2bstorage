<template>
  <div class="min-h-screen bg-gray-50" style="padding-top: 66px;">
    <SalesMenu />
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-6">Отгрузки</h1>
        <p class="text-gray-700 mb-4">Управление отгрузками товаров покупателям. Здесь вы можете создавать отгрузки, отслеживать их статус и печатать документы.</p>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Номер отгрузки</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Покупатель</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Дата отгрузки</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Способ доставки</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Статус</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Действия</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#SH-2024-001</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">ООО "Покупатель 1"</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">18.01.2024</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Самовывоз</td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Отгружено</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <button class="text-blue-600 hover:text-blue-900 mr-3">Просмотр</button>
                  <button class="text-gray-600 hover:text-gray-900">Печать</button>
                </td>
              </tr>
              <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#SH-2024-002</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">ИП Петров</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">19.01.2024</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Курьер</td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">В пути</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <button class="text-blue-600 hover:text-blue-900 mr-3">Просмотр</button>
                  <button class="text-green-600 hover:text-green-900">Отследить</button>
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
document.title = 'Отгрузки - B2B SKLAD'
</script> 