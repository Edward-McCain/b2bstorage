<template>
  <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-2 lg:p-6">
    <div class="flex items-center justify-between mb-6">
      <div class="flex items-center justify-between space-x-4 w-full">
        <!-- Переключатель периодов -->
        <div class="flex bg-gray-100 rounded-lg p-1">
          <button
            v-for="period in availablePeriodsList"
            :key="period.value"
            @click="changePeriod(period.value)"
            :class="[
              'px-3 py-1 text-sm font-medium rounded-md transition-colors',
              selectedPeriod === period.value
                ? 'bg-white text-blue-600 shadow-sm'
                : 'text-gray-600 hover:text-gray-900'
            ]"
          >
            {{ period.label }}
          </button>
        </div>
        <div class="text-right">
          <div class="text-3xl font-bold text-gray-900 hidden">{{ totalOperations }}</div>
          <div class="text-sm text-green-600 font-medium hidden">+{{ growthPercentage }}% ↑</div>
        </div>
      </div>
    </div>
    
    <div class="w-full">
      <!-- Лоадер -->
      <div v-if="loading" class="flex items-center justify-center h-64">
        <div class="flex items-center space-x-2">
          <Loader2 class="w-6 h-6 animate-spin text-blue-600" />
          <!-- Загрузка данных... -->
          <span class="text-gray-600">{{ t('ProductsChart_4') }}</span>
        </div>
      </div>
      
      <!-- Сообщение об отсутствии данных -->
      <div v-else-if="!hasData" class="flex items-center justify-center h-64">
        <div class="text-center">
          <div class="text-gray-400 mb-2">
            <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
          </div>
          <!-- Нет данных -->
          <h3 class="text-lg font-medium text-gray-900 mb-1">{{ t('ProductsChart_5') }}</h3>
          <!-- За выбранный период нет операций -->
          <p class="text-sm text-gray-500">{{ t('ProductsChart_6') }}</p>
        </div>
      </div>
      
      <!-- График -->
      <apexchart
        v-else
        type="area"
        height="350"
        :options="chartOptions"
        :series="series"
      />
    </div>
    
    <!-- Адаптивные лейблы -->
    <div class="mt-6">
      <!-- Мобильная версия (вертикальное расположение) -->
      <div class="md:hidden space-y-3">
        <div class="flex items-center space-x-2">
          <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
          <!-- Оприходования -->
          <span class="text-sm text-gray-600">{{ t('ProductsChart_7') }}</span>
        </div>
        <div class="flex items-center space-x-2">
          <div class="w-3 h-3 bg-purple-500 rounded-full"></div>
          <!-- Списания -->
          <span class="text-sm text-gray-600">{{ t('ProductsChart_8') }}</span>
        </div>
        <div class="flex items-center space-x-2">
          <div class="w-3 h-3 bg-green-500 rounded-full"></div>
          <!-- Перемещения -->
          <span class="text-sm text-gray-600">{{ t('ProductsChart_9') }}</span>
        </div>
      </div>
      
      <!-- Десктопная версия (горизонтальное расположение) -->
      <div class="hidden md:flex items-center justify-between">
        <div class="flex items-center space-x-4">
          <div class="flex items-center space-x-2">
            <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
            <!-- Оприходования -->
            <span class="text-sm text-gray-600">{{ t('ProductsChart_7') }}</span>
          </div>
          <div class="flex items-center space-x-2">
            <div class="w-3 h-3 bg-purple-500 rounded-full"></div>
            <!-- Списания -->
            <span class="text-sm text-gray-600">{{ t('ProductsChart_8') }}</span>
          </div>
          <div class="flex items-center space-x-2">
            <div class="w-3 h-3 bg-green-500 rounded-full"></div>
            <!-- Перемещения -->
            <span class="text-sm text-gray-600">{{ t('ProductsChart_9') }}</span>
          </div>
        </div>
        <div class="text-sm text-gray-500">{{ periodLabel }}</div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import { apiRequest } from '@/config/api'
import { t } from '../locales/index.js'
import { Loader2 } from 'lucide-vue-next'

// Периоды для переключения
const periods = [
  { value: 'week', label: t('ProductsChart_1') },
  { value: 'month', label: t('ProductsChart_2') },
  { value: 'year', label: t('ProductsChart_3') }
]

// Состояние
const selectedPeriod = ref('week')
const loading = ref(false)
const availablePeriods = ref(['week']) // По умолчанию доступна только неделя
const statisticsData = ref({
  receipts: [],
  writeOffs: [],
  transfers: []
})

// Данные для графика
const series = ref([
  {
    name: t('ProductsChart_7'),
    data: []
  },
  {
    name: t('ProductsChart_8'),
    data: []
  },
  {
    name: t('ProductsChart_9'),
    data: []
  }
])

// Вычисляемые свойства
const availablePeriodsList = computed(() => {
  return periods.filter(period => availablePeriods.value.includes(period.value))
})

const totalOperations = computed(() => {
  const receipts = statisticsData.value.receipts.reduce((sum, item) => sum + item.count, 0)
  const writeOffs = statisticsData.value.writeOffs.reduce((sum, item) => sum + item.count, 0)
  const transfers = statisticsData.value.transfers.reduce((sum, item) => sum + item.count, 0)
  return receipts + writeOffs + transfers
})

const growthPercentage = computed(() => {
  // Простая логика роста - можно улучшить
  return Math.floor(Math.random() * 30) + 5
})

const periodLabel = computed(() => {
  const period = periods.find(p => p.value === selectedPeriod.value)
  return period ? period.label : t('ProductsChart_3')
})

const hasData = computed(() => {
  const receipts = statisticsData.value.receipts.reduce((sum, item) => sum + item.count, 0)
  const writeOffs = statisticsData.value.writeOffs.reduce((sum, item) => sum + item.count, 0)
  const transfers = statisticsData.value.transfers.reduce((sum, item) => sum + item.count, 0)
  return receipts > 0 || writeOffs > 0 || transfers > 0
})

// Настройки графика
const chartOptions = ref({
  chart: {
    height: 350,
    type: 'area',
    zoom: {
      enabled: false
    },
    toolbar: {
      show: false
    },
    animations: {
      enabled: true,
      easing: 'easeinout',
      speed: 800,
      animateGradually: {
        enabled: true,
        delay: 150
      },
      dynamicAnimation: {
        enabled: true,
        speed: 350
      }
    }
  },
  dataLabels: {
    enabled: false
  },
  stroke: {
    curve: 'smooth',
    width: 3
  },
  fill: {
    type: 'gradient',
    gradient: {
      shadeIntensity: 1,
      opacityFrom: 0.7,
      opacityTo: 0.1,
      stops: [0, 90, 100]
    }
  },
  grid: {
    show: true,
    borderColor: '#f1f5f9',
    strokeDashArray: 5,
    position: 'back'
  },
  xaxis: {
    categories: [],
    labels: {
      style: {
        colors: '#64748b',
        fontSize: '12px',
        fontFamily: 'Inter, sans-serif'
      }
    },
    axisBorder: {
      show: false
    },
    axisTicks: {
      show: false
    }
  },
  yaxis: {
    labels: {
      style: {
        colors: '#64748b',
        fontSize: '12px',
        fontFamily: 'Inter, sans-serif'
      }
    },
    axisBorder: {
      show: false
    },
    axisTicks: {
      show: false
    }
  },
  colors: ['#3b82f6', '#8b5cf6', '#10b981'],
  legend: {
    show: false
  },
  markers: {
    size: 0,
    hover: {
      size: 6,
      sizeOffset: 3
    }
  },
  tooltip: {
    theme: 'light',
    style: {
      fontSize: '12px',
      fontFamily: 'Inter, sans-serif'
    },
    x: {
      show: true
    },
    y: {
      title: {
        formatter: function (seriesName) {
          return seriesName + ': '
        }
      }
    }
  },
  states: {
    hover: {
      filter: {
        type: 'lighten',
        value: 0.1
      }
    },
    active: {
      filter: {
        type: 'darken',
        value: 0.1
      }
    }
  },
  responsive: [
    {
      breakpoint: 768,
      options: {
        chart: {
          height: 300
        },
        xaxis: {
          labels: {
            style: {
              fontSize: '10px'
            }
          }
        }
      }
    }
  ]
})

// Функции
const loadStatistics = async () => {
  loading.value = true
  try {
    const response = await apiRequest(`/statistics/operations?period=${selectedPeriod.value}`, {
      method: 'GET'
    })
    
    if (response.ok && response.data.success) {
      statisticsData.value = response.data.data
      // Обновляем доступные периоды из ответа API
      if (response.data.data.availablePeriods) {
        availablePeriods.value = response.data.data.availablePeriods
      }
      updateChartData()
    } else {
      console.error('Ошибка загрузки статистики:', response.data.message)
    }
  } catch (error) {
    console.error('Ошибка при загрузке статистики:', error)
  } finally {
    loading.value = false
  }
}

const updateChartData = () => {
  // Получаем все уникальные даты
  const allDates = new Set()
  statisticsData.value.receipts.forEach(item => allDates.add(item.date))
  statisticsData.value.writeOffs.forEach(item => allDates.add(item.date))
  statisticsData.value.transfers.forEach(item => allDates.add(item.date))
  
  const sortedDates = Array.from(allDates).sort()
  
  // Обновляем категории X
  chartOptions.value.xaxis.categories = sortedDates
  
  // Обновляем данные серий
  series.value[0].data = sortedDates.map(date => {
    const item = statisticsData.value.receipts.find(r => r.date === date)
    return item ? item.count : 0
  })
  
  series.value[1].data = sortedDates.map(date => {
    const item = statisticsData.value.writeOffs.find(w => w.date === date)
    return item ? item.count : 0
  })
  
  series.value[2].data = sortedDates.map(date => {
    const item = statisticsData.value.transfers.find(t => t.date === date)
    return item ? item.count : 0
  })
}

const changePeriod = (period) => {
  selectedPeriod.value = period
  loadStatistics()
}

// Наблюдатели - убираем watch чтобы избежать двойных запросов
// watch(selectedPeriod, () => {
//   loadStatistics()
// })

// Инициализация
onMounted(() => {
  loadStatistics()
})
</script>

<style scoped>
/* Дополнительные стили для графика */
:deep(.apexcharts-canvas) {
  margin: 0 auto;
}

:deep(.apexcharts-tooltip) {
  background: #ffffff !important;
  border: 1px solid #e2e8f0 !important;
  border-radius: 8px !important;
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
  color: #374151 !important;
}

:deep(.apexcharts-tooltip-title) {
  background: #f8fafc !important;
  border-bottom: 1px solid #e2e8f0 !important;
  color: #64748b !important;
  font-weight: 600 !important;
}

:deep(.apexcharts-area-series) {
  opacity: 0.8;
}

:deep(.apexcharts-series) {
  transition: opacity 0.2s ease;
}

:deep(.apexcharts-series:hover) {
  opacity: 1;
}

:deep(.apexcharts-gridline) {
  stroke: #f1f5f9;
  stroke-width: 1;
}

:deep(.apexcharts-xaxis-line) {
  stroke: #e2e8f0;
  stroke-width: 1;
}

:deep(.apexcharts-yaxis-line) {
  stroke: #e2e8f0;
  stroke-width: 1;
}
</style> 