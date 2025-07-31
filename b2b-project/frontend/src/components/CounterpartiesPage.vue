<template>
  <div class="min-h-screen bg-gray-50" style="padding-top: 66px;">
    <!-- Второй уровень меню -->
    <div class="bg-white border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex space-x-8 overflow-x-auto">
          <router-link 
            to="/counterparties" 
            class="border-b-2 py-4 px-1 text-sm font-medium hover:text-gray-700 whitespace-nowrap border-blue-700 text-blue-700 font-semibold router-link-active"
            :class="{ 'border-blue-700 text-blue-700 font-semibold router-link-active': $route.path === '/counterparties' }"
          >
            Контрагенты
          </router-link>
          <!-- <router-link 
            to="/counterparties/buyers" 
            class="border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap"
            :class="{ 'border-blue-700 text-blue-700 font-semibold router-link-active': $route.path === '/counterparties/buyers' }"
          >
            Покупатели
          </router-link>
          <router-link 
            to="/counterparties/suppliers" 
            class="border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap"
            :class="{ 'border-blue-700 text-blue-700 font-semibold router-link-active': $route.path === '/counterparties/suppliers' }"
          >
            Поставщики
          </router-link>
          <router-link 
            to="/counterparties/groups" 
            class="border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap"
            :class="{ 'border-blue-700 text-blue-700 font-semibold router-link-active': $route.path === '/counterparties/groups' }"
          >
            Группы
          </router-link> -->
        </nav>
      </div>
    </div>
    <!-- Основной контент -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex items-center gap-4">
          <!-- <h1 class="text-3xl font-bold text-gray-900">Контрагенты</h1> -->
          <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium flex items-center gap-2">
            <span>+ Контрагент</span>
          </button>
          <button class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-4 py-2 rounded-lg font-medium flex items-center gap-2">
            <span>Фильтр</span>
          </button>
          <input type="text" placeholder="Наим, тел, email, событ, коммент, код..." class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-64" />
        </div>
        <div class="flex items-center gap-2 flex-wrap">
          <button class="bg-white border border-gray-300 px-3 py-1.5 rounded font-medium text-sm">Импорт</button>
          <button class="bg-white border border-gray-300 px-3 py-1.5 rounded font-medium text-sm">Экспорт</button>
          <button class="bg-white border border-gray-300 px-3 py-1.5 rounded font-medium text-sm">
            <svg class="h-5 w-5 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
          </button>
        </div>
      </div>
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 overflow-x-auto">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 text-sm">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-2 py-2"><input type="checkbox" /></th>
              <th class="px-2 py-2 font-semibold text-left">Наименование</th>
              <th class="px-2 py-2 font-semibold text-left">Код</th>
              <th class="px-2 py-2 font-semibold text-left">Создан</th>
              <th class="px-2 py-2 font-semibold text-left">Телефон</th>
              <th class="px-2 py-2 font-semibold text-left">Факс</th>
              <th class="px-2 py-2 font-semibold text-left">E-mail</th>
              <th class="px-2 py-2 font-semibold text-left">Статус</th>
              <th class="px-2 py-2 font-semibold text-left">Фактический адрес</th>
              <th class="px-2 py-2 font-semibold text-left">Комментарий</th>
              <th class="px-2 py-2 font-semibold text-left">Группы</th>
              <th class="px-2 py-2 font-semibold text-left">Последняя продажа</th>
              <th class="px-2 py-2 font-semibold text-left">Кол-во продаж</th>
              <th class="px-2 py-2 font-semibold text-left">Средний чек</th>
              <th class="px-2 py-2 font-semibold text-left">Баланс</th>
              <th class="px-2 py-2 font-semibold text-left">Дата события</th>
              <th class="px-2 py-2 font-semibold text-left">Текст события</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr>
              <td class="px-2 py-2"><input type="checkbox" /></td>
              <td class="px-2 py-2">ООО "Покупатель"</td>
              <td class="px-2 py-2">-</td>
              <td class="px-2 py-2">03.07.2025 11:13</td>
              <td class="px-2 py-2">-</td>
              <td class="px-2 py-2">-</td>
              <td class="px-2 py-2">-</td>
              <td class="px-2 py-2">-</td>
              <td class="px-2 py-2">-</td>
              <td class="px-2 py-2">-</td>
              <td class="px-2 py-2">-</td>
              <td class="px-2 py-2">-</td>
              <td class="px-2 py-2">0</td>
              <td class="px-2 py-2">0,00</td>
              <td class="px-2 py-2">0,00</td>
              <td class="px-2 py-2">-</td>
              <td class="px-2 py-2">-</td>
            </tr>
            <tr>
              <td class="px-2 py-2"><input type="checkbox" /></td>
              <td class="px-2 py-2">ООО "Поставщик"</td>
              <td class="px-2 py-2">-</td>
              <td class="px-2 py-2">03.07.2025 11:13</td>
              <td class="px-2 py-2">-</td>
              <td class="px-2 py-2">-</td>
              <td class="px-2 py-2">-</td>
              <td class="px-2 py-2">-</td>
              <td class="px-2 py-2">-</td>
              <td class="px-2 py-2">-</td>
              <td class="px-2 py-2">-</td>
              <td class="px-2 py-2">-</td>
              <td class="px-2 py-2">0</td>
              <td class="px-2 py-2">0,00</td>
              <td class="px-2 py-2">0,00</td>
              <td class="px-2 py-2">-</td>
              <td class="px-2 py-2">-</td>
            </tr>
            <tr>
              <td class="px-2 py-2"><input type="checkbox" /></td>
              <td class="px-2 py-2">Розничный покупатель</td>
              <td class="px-2 py-2">-</td>
              <td class="px-2 py-2">03.07.2025 11:13</td>
              <td class="px-2 py-2">-</td>
              <td class="px-2 py-2">-</td>
              <td class="px-2 py-2">-</td>
              <td class="px-2 py-2">-</td>
              <td class="px-2 py-2">-</td>
              <td class="px-2 py-2">-</td>
              <td class="px-2 py-2">-</td>
              <td class="px-2 py-2">-</td>
              <td class="px-2 py-2">0</td>
              <td class="px-2 py-2">0,00</td>
              <td class="px-2 py-2">0,00</td>
              <td class="px-2 py-2">-</td>
              <td class="px-2 py-2">-</td>
            </tr>
          </tbody>
        </table>
        </div>
        <div class="flex items-center justify-between mt-4 text-sm text-gray-600">
          <div>1-3 из 3</div>
          <div class="flex gap-2">
            <button class="px-2 py-1 border rounded" disabled>&lt;&lt;</button>
            <button class="px-2 py-1 border rounded" disabled>&lt;</button>
            <button class="px-2 py-1 border rounded" disabled>&gt;</button>
            <button class="px-2 py-1 border rounded" disabled>&gt;&gt;</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
    document.title = 'Контрагенты - B2B SKLAD'
</script> 