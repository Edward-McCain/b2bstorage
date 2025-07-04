<template>
  <div class="min-h-screen bg-gray-50" style="padding-top: 66px;">
    <PurchasesMenu />
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex justify-between items-center mb-6">
          <h1 class="text-3xl font-bold text-gray-900">Заказы поставщикам</h1>
          <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium">
            Создать заказ
          </button>
        </div>

        <!-- Фильтры -->
        <div class="bg-gray-50 rounded-lg p-4 mb-6">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Поставщик</label>
              <select class="w-full border border-gray-300 rounded-lg px-3 py-2">
                <option>Все поставщики</option>
                <option>ООО "Поставщик 1"</option>
                <option>ИП Иванов</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Статус</label>
              <select class="w-full border border-gray-300 rounded-lg px-3 py-2">
                <option>Все статусы</option>
                <option>Новый</option>
                <option>Подтвержден</option>
                <option>В доставке</option>
                <option>Получен</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Дата с</label>
              <input type="date" class="w-full border border-gray-300 rounded-lg px-3 py-2">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Дата по</label>
              <input type="date" class="w-full border border-gray-300 rounded-lg px-3 py-2">
            </div>
          </div>
        </div>

        <!-- Таблица заказов -->
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Номер заказа
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Поставщик
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Дата заказа
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Сумма
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Статус
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Действия
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                  #PO-2024-001
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  ООО "Поставщик 1"
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  15.01.2024
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  125,000 ₽
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                    Подтвержден
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <button class="text-blue-600 hover:text-blue-900 mr-3">Просмотр</button>
                  <button class="text-gray-600 hover:text-gray-900">Редактировать</button>
                </td>
              </tr>
              <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                  #PO-2024-002
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  ИП Иванов
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  16.01.2024
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  89,500 ₽
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                    В доставке
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <button class="text-blue-600 hover:text-blue-900 mr-3">Просмотр</button>
                  <button class="text-gray-600 hover:text-gray-900">Редактировать</button>
                </td>
              </tr>
              <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                  #PO-2024-003
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  ООО "Поставщик 1"
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  17.01.2024
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  210,000 ₽
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                    Новый
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <button class="text-blue-600 hover:text-blue-900 mr-3">Просмотр</button>
                  <button class="text-gray-600 hover:text-gray-900">Редактировать</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Пагинация -->
        <div class="flex items-center justify-between mt-6">
          <div class="text-sm text-gray-700">
            Показано 1-3 из 3 результатов
          </div>
          <div class="flex space-x-2">
            <button class="px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50">
              Предыдущая
            </button>
            <button class="px-3 py-2 bg-blue-600 text-white rounded-lg text-sm">
              1
            </button>
            <button class="px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50">
              Следующая
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import PurchasesMenu from './PurchasesMenu.vue'

// Устанавливаем заголовок страницы
document.title = 'Заказы поставщикам - B2B Storage'
</script> 