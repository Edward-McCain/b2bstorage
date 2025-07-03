<template>
  <div>
    <SalesMenu />
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex justify-between items-center mb-6">
          <h1 class="text-3xl font-bold text-gray-900">Счета покупателям</h1>
          <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium">
            Создать счет
          </button>
        </div>

        <!-- Статистика -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
          <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
            <div class="flex items-center">
              <div class="bg-blue-100 rounded-lg p-2">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
              </div>
              <div class="ml-3">
                <p class="text-sm font-medium text-gray-600">Всего счетов</p>
                <p class="text-2xl font-bold text-gray-900">89</p>
              </div>
            </div>
          </div>
          <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-200">
            <div class="flex items-center">
              <div class="bg-yellow-100 rounded-lg p-2">
                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <div class="ml-3">
                <p class="text-sm font-medium text-gray-600">Ожидают оплаты</p>
                <p class="text-2xl font-bold text-gray-900">15</p>
              </div>
            </div>
          </div>
          <div class="bg-green-50 rounded-lg p-4 border border-green-200">
            <div class="flex items-center">
              <div class="bg-green-100 rounded-lg p-2">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
              </div>
              <div class="ml-3">
                <p class="text-sm font-medium text-gray-600">Оплачено</p>
                <p class="text-2xl font-bold text-gray-900">74</p>
              </div>
            </div>
          </div>
          <div class="bg-purple-50 rounded-lg p-4 border border-purple-200">
            <div class="flex items-center">
              <div class="bg-purple-100 rounded-lg p-2">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                </svg>
              </div>
              <div class="ml-3">
                <p class="text-sm font-medium text-gray-600">Сумма счетов</p>
                <p class="text-2xl font-bold text-gray-900">1.8M ₽</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Таблица счетов -->
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Номер счета</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Покупатель</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Дата выставления</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Срок оплаты</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Сумма</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Статус</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Действия</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#INV-2024-001</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">ООО "Покупатель 1"</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">18.01.2024</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">28.01.2024</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">125,000 ₽</td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Оплачено</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <button class="text-blue-600 hover:text-blue-900 mr-3">Просмотр</button>
                  <button class="text-gray-600 hover:text-gray-900">Скачать</button>
                </td>
              </tr>
              <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#INV-2024-002</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">ИП Петров</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">19.01.2024</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">29.01.2024</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">89,500 ₽</td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Ожидает оплаты</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <button class="text-blue-600 hover:text-blue-900 mr-3">Просмотр</button>
                  <button class="text-green-600 hover:text-green-900">Отправить</button>
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
document.title = 'Счета покупателям - B2B Storage'
</script> 