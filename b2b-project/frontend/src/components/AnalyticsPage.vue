<template>
  <div class="min-h-screen bg-gray-50" style="padding-top: 66px;">
    <!-- Второй уровень меню -->
    <div class="bg-white border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex space-x-8 overflow-x-auto">
          <router-link 
            to="/analytics" 
            class="border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap"
            :class="{ 'border-blue-700 text-blue-700 font-semibold router-link-active': $route.path === '/analytics' }"
          >
            Аналитика
          </router-link>
          <!-- <router-link 
            to="/analytics/sales" 
            class="border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap"
            :class="{ 'border-blue-700 text-blue-700 font-semibold router-link-active': $route.path === '/analytics/sales' }"
          >
            Продажи
          </router-link>
          <router-link 
            to="/analytics/money" 
            class="border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap"
            :class="{ 'border-blue-700 text-blue-700 font-semibold router-link-active': $route.path === '/analytics/money' }"
          >
            Деньги
          </router-link>
          <router-link 
            to="/analytics/overdue-orders" 
            class="border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap"
            :class="{ 'border-blue-700 text-blue-700 font-semibold router-link-active': $route.path === '/analytics/overdue-orders' }"
          >
            Просроченные заказы
          </router-link>
          <router-link 
            to="/analytics/overdue-invoices" 
            class="border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap"
            :class="{ 'border-blue-700 text-blue-700 font-semibold router-link-active': $route.path === '/analytics/overdue-invoices' }"
          >
            Просроченные счета
          </router-link> -->
        </nav>
      </div>
    </div>
    <!-- Основной контент -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Продажи -->
      <div class="flex flex-col md:flex-row md:gap-8">
        <div class="flex-1">
          <h2 class="text-xl font-semibold text-orange-600 mb-2">Продажи</h2>
          <div class="mb-4 flex gap-2">
            <button v-for="period in ['Неделя','Месяц','Год']" :key="period" class="border px-3 py-1 rounded bg-white text-gray-800 text-sm">{{ period }}</button>
          </div>
          <div class="mb-4">
            <div class="text-sm text-gray-700 mb-1">Сегодня, {{ todayStr }}</div>
            <div class="flex gap-8 mb-1">
              <div><span class="text-cyan-600 text-2xl font-bold">{{ salesToday.count }}</span><div class="text-xs text-gray-500">Продаж</div></div>
              <div><span class="text-cyan-600 text-2xl font-bold">{{ salesToday.amount }}</span><div class="text-xs text-gray-500">руб</div></div>
              <div><span class="text-cyan-600 text-2xl font-bold">{{ salesToday.avg }}</span><div class="text-xs text-gray-500">руб ({{ salesToday.percent }}%)</div></div>
            </div>
            <div class="text-xs text-gray-500">По сравнению с четвергом</div>
          </div>
          <div class="mb-6">
            <div class="text-sm text-gray-700 mb-1">На этой неделе</div>
            <div class="flex gap-8 mb-1">
              <div><span class="text-cyan-600 text-2xl font-bold">{{ salesWeek.count }}</span><div class="text-xs text-gray-500">Продаж</div></div>
              <div><span class="text-cyan-600 text-2xl font-bold">{{ salesWeek.amount }}</span><div class="text-xs text-gray-500">руб</div></div>
              <div><span class="text-cyan-600 text-2xl font-bold">{{ salesWeek.avg }}</span><div class="text-xs text-gray-500">руб ({{ salesWeek.percent }}%)</div></div>
            </div>
            <div class="text-xs text-gray-500">По сравнению с прошлой неделей</div>
          </div>
        </div>
        <div class="flex-1 flex items-center justify-center">
          <canvas id="salesChart" width="350" height="120"></canvas>
        </div>
      </div>
      <!-- Просроченные заказы и счета -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-8">
        <div>
          <h3 class="text-lg font-semibold text-orange-600 mb-2">Просроченные заказы</h3>
          <div class="flex gap-8 mb-2">
            <div><span class="text-cyan-600 text-xl font-bold">0</span><div class="text-xs text-gray-500">Заказов</div></div>
            <div><span class="text-cyan-600 text-xl font-bold">0</span><div class="text-xs text-gray-500">руб</div></div>
          </div>
          <table class="min-w-full text-sm border-t">
            <thead>
              <tr class="text-gray-600">
                <th class="py-1 px-2 text-left">Контрагент</th>
                <th class="py-1 px-2 text-left">Заказ</th>
                <th class="py-1 px-2 text-left">Сумма <span class="text-xs">руб</span></th>
                <th class="py-1 px-2 text-left">Срок <span class="text-xs">дни</span></th>
              </tr>
            </thead>
            <tbody>
              <tr><td colspan="4" class="text-center text-gray-400 py-2">-</td></tr>
            </tbody>
          </table>
        </div>
        <div>
          <div class="flex items-center gap-2 mb-2">
            <h3 class="text-lg font-semibold text-orange-600">Просроченные счета</h3>
            <span class="text-blue-400 cursor-pointer" title="Информация">?</span>
          </div>
          <div class="flex gap-8 mb-2">
            <div><span class="text-cyan-600 text-xl font-bold">0</span><div class="text-xs text-gray-500">Счетов</div></div>
            <div><span class="text-cyan-600 text-xl font-bold">0</span><div class="text-xs text-gray-500">руб</div></div>
          </div>
          <table class="min-w-full text-sm border-t">
            <thead>
              <tr class="text-gray-600">
                <th class="py-1 px-2 text-left">Контрагент</th>
                <th class="py-1 px-2 text-left">Счет</th>
                <th class="py-1 px-2 text-left">Сумма <span class="text-xs">руб</span></th>
                <th class="py-1 px-2 text-left">Срок <span class="text-xs">дни</span></th>
              </tr>
            </thead>
            <tbody>
              <tr><td colspan="4" class="text-center text-gray-400 py-2">-</td></tr>
            </tbody>
          </table>
        </div>
      </div>
      <!-- Деньги и график -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-8">
        <div>
          <h3 class="text-lg font-semibold text-orange-600 mb-2">Деньги</h3>
          <div class="flex gap-8 mb-2">
            <div><span class="text-cyan-600 text-xl font-bold">0</span><div class="text-xs text-gray-500">руб</div></div>
          </div>
          <table class="min-w-full text-sm border-t">
            <thead>
              <tr class="text-gray-600">
                <th class="py-1 px-2 text-left">Юр. лицо</th>
                <th class="py-1 px-2 text-left">Остаток</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td class="py-1 px-2">edmccain01</td>
                <td class="py-1 px-2">0</td>
              </tr>
            </tbody>
          </table>
          <div class="flex items-center justify-between mt-2 text-xs text-gray-600">
            <div>1-1 из 1</div>
            <div class="flex gap-2">
              <button class="px-2 py-1 border rounded" disabled>&lt;&lt;</button>
              <button class="px-2 py-1 border rounded" disabled>&lt;</button>
              <button class="px-2 py-1 border rounded" disabled>&gt;</button>
              <button class="px-2 py-1 border rounded" disabled>&gt;&gt;</button>
            </div>
          </div>
        </div>
        <div class="flex flex-col items-center justify-center">
          <canvas id="moneyChart" width="350" height="120"></canvas>
          <div class="flex gap-4 mt-2 text-xs">
            <div class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-green-400 inline-block"></span> Приход</div>
            <div class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-red-400 inline-block"></span> Расход</div>
            <div class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-cyan-400 inline-block"></span> Остаток</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'

// Случайные данные для графиков и статистики
const todayStr = new Date().toLocaleDateString('ru-RU', { weekday: 'long', day: 'numeric', month: 'long' })
const salesToday = {
  count: Math.floor(Math.random()*3),
  amount: Math.floor(Math.random()*10000),
  avg: 0,
  percent: 0
}
salesToday.avg = salesToday.amount
salesToday.percent = Math.floor(Math.random()*10)
const salesWeek = {
  count: Math.floor(Math.random()*10),
  amount: Math.floor(Math.random()*50000),
  avg: 0,
  percent: 0
}
salesWeek.avg = salesWeek.amount
salesWeek.percent = Math.floor(Math.random()*10)

onMounted(() => {
  // Chart.js через CDN
  if (!window.Chart) {
    const script = document.createElement('script')
    script.src = 'https://cdn.jsdelivr.net/npm/chart.js'
    script.onload = () => renderCharts()
    document.head.appendChild(script)
  } else {
    renderCharts()
  }
})

function renderCharts() {
  // Продажи по дням недели
  const ctx1 = document.getElementById('salesChart').getContext('2d')
  new window.Chart(ctx1, {
    type: 'line',
    data: {
      labels: ['пн','вт','ср','чт','пт','сб','вс'],
      datasets: [{
        label: 'Продажи',
        data: Array.from({length: 7}, () => Math.floor(Math.random()*10)),
        borderColor: '#06b6d4',
        backgroundColor: 'rgba(6,182,212,0.1)',
        tension: 0.3,
        fill: true,
        pointRadius: 2
      }]
    },
    options: {
      plugins: { legend: { display: false } },
      scales: {
        y: { min: -1, max: 10, ticks: { stepSize: 1 } }
      }
    }
  })
  // Деньги по месяцам
  const ctx2 = document.getElementById('moneyChart').getContext('2d')
  new window.Chart(ctx2, {
    type: 'line',
    data: {
      labels: ['Февр.','Март','Апр.','Май','Июнь','Июль'],
      datasets: [
        {
          label: 'Приход',
          data: Array.from({length: 6}, () => Math.floor(Math.random()*10)),
          borderColor: '#4ade80',
          backgroundColor: 'rgba(74,222,128,0.1)',
          tension: 0.3,
          fill: false,
          pointRadius: 2
        },
        {
          label: 'Расход',
          data: Array.from({length: 6}, () => Math.floor(Math.random()*10)),
          borderColor: '#f87171',
          backgroundColor: 'rgba(248,113,113,0.1)',
          tension: 0.3,
          fill: false,
          pointRadius: 2
        },
        {
          label: 'Остаток',
          data: Array.from({length: 6}, () => Math.floor(Math.random()*10)),
          borderColor: '#22d3ee',
          backgroundColor: 'rgba(34,211,238,0.1)',
          tension: 0.3,
          fill: false,
          pointRadius: 2
        }
      ]
    },
    options: {
      plugins: { legend: { display: false } },
      scales: {
        y: { min: -1, max: 10, ticks: { stepSize: 1 } }
      }
    }
  })
}
document.title = 'Аналитика - B2B Storage'
</script> 