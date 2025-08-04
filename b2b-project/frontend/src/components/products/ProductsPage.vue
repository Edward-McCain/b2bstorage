<template>
  <div class="min-h-screen bg-gray-50" style="padding-top: 66px;">
    <!-- Карточки навигации -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <!-- Создать товар -->
        <router-link 
          to="/products" 
          class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover:shadow-md transition-all duration-200 cursor-pointer group relative" style="background: linear-gradient(45deg, #d0dfff, #ddfcef);"
        >
          <div class="flex items-center justify-center h-full">
            <span class="text-sm font-bold text-gray-900 text-center">
              <!-- Добро пожаловать, -->
              {{ t('ProductsPage_1') }} <br> {{ userName || userEmail }}
            </span>
          </div>
        </router-link>

        <!-- Оприходования -->
        <router-link 
          to="/products/receipts" 
          class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover:shadow-md transition-all duration-200 cursor-pointer group relative"
        >
          <div class="flex items-center justify-between mb-4">
            <PackagePlus class="w-6 h-6 text-blue-600" />
            <div class="bg-gray-100 text-gray-600 text-xs font-medium rounded-full w-6 h-6 flex items-center justify-center">
              <Loader2 v-if="loadingCounts" class="w-3 h-3 animate-spin" />
              <span v-else-if="errorCounts">0</span>
              <span v-else>{{ counts.receipts || 0 }}</span>
            </div>
          </div>
          <div class="pt-6">
            <!-- Оприходования -->
            <span class="text-sm font-medium text-gray-900">{{ t('ProductsPage_2') }}</span>
          </div>
        </router-link>

        <!-- Списания -->
        <router-link 
          to="/products/write-offs" 
          class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover:shadow-md transition-all duration-200 cursor-pointer group relative"
        >
          <div class="flex items-center justify-between mb-4">
            <PackageMinus class="w-6 h-6 text-blue-600" />
            <div class="bg-gray-100 text-gray-600 text-xs font-medium rounded-full w-6 h-6 flex items-center justify-center">
              <Loader2 v-if="loadingCounts" class="w-3 h-3 animate-spin" />
              <span v-else-if="errorCounts">0</span>
              <span v-else>{{ counts.writeOffs || 0 }}</span>
            </div>
          </div>
          <div class="pt-6">
            <!-- Списания -->
            <span class="text-sm font-medium text-gray-900">{{ t('ProductsPage_3') }}</span>
          </div>
        </router-link>

        <!-- Инвентаризации -->
        <router-link 
          to="/products/inventory" 
          class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover:shadow-md transition-all duration-200 cursor-pointer group relative"
        >
          <div class="flex items-center justify-between mb-4">
            <ClipboardList class="w-6 h-6 text-blue-600" />
            <div class="bg-gray-100 text-gray-600 text-xs font-medium rounded-full w-6 h-6 flex items-center justify-center">
              <Loader2 v-if="loadingCounts" class="w-3 h-3 animate-spin" />
              <span v-else-if="errorCounts">0</span>
              <span v-else>{{ counts.inventory || 0 }}</span>
            </div>
          </div>
          <div class="pt-6">
            <!-- Инвентаризации -->
            <span class="text-sm font-medium text-gray-900">{{ t('ProductsPage_4') }}</span>
          </div>
        </router-link>

        <!-- Перемещения -->
        <router-link 
          to="/products/transfers" 
          class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover:shadow-md transition-all duration-200 cursor-pointer group relative"
        >
          <div class="flex items-center justify-between mb-4">
            <ArrowRightLeft class="w-6 h-6 text-blue-600" />
            <div class="bg-gray-100 text-gray-600 text-xs font-medium rounded-full w-6 h-6 flex items-center justify-center">
              <Loader2 v-if="loadingCounts" class="w-3 h-3 animate-spin" />
              <span v-else-if="errorCounts">0</span>
              <span v-else>{{ counts.transfers || 0 }}</span>
            </div>
          </div>
          <div class="pt-6">
            <!-- Перемещения -->
            <span class="text-sm font-medium text-gray-900">{{ t('ProductsPage_5') }}</span>
          </div>
        </router-link>

        <!-- Остатки -->
        <router-link 
          to="/products/balances" 
          class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover:shadow-md transition-all duration-200 cursor-pointer group relative"
        >
          <div class="flex items-center justify-between mb-4">
            <Package class="w-6 h-6 text-blue-600" />
            <div class="bg-gray-100 text-gray-600 text-xs font-medium rounded-full w-6 h-6 flex items-center justify-center">
              <Loader2 v-if="loadingCounts" class="w-3 h-3 animate-spin" />
              <span v-else-if="errorCounts">0</span>
              <span v-else>{{ counts.balances || 0 }}</span>
            </div>
          </div>
          <div class="pt-6">
            <!-- Остатки -->
            <span class="text-sm font-medium text-gray-900">{{ t('ProductsPage_6') }}</span>
          </div>
        </router-link>

        <!-- Склады -->
        <router-link 
          to="/warehouses" 
          class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover:shadow-md transition-all duration-200 cursor-pointer group relative"
        >
          <div class="flex items-center justify-between mb-4">
            <Warehouse class="w-6 h-6 text-blue-600" />
            <div class="bg-gray-100 text-gray-600 text-xs font-medium rounded-full w-6 h-6 flex items-center justify-center">
              <Loader2 v-if="loadingCounts" class="w-3 h-3 animate-spin" />
              <span v-else-if="errorCounts">0</span>
              <span v-else>{{ counts.warehouses || 0 }}</span>
            </div>
          </div>
          <div class="pt-6">
            <!-- Склады -->
            <span class="text-sm font-medium text-gray-900">{{ t('ProductsPage_7') }}</span>
          </div>
        </router-link>

        <!-- Логи -->
        <router-link 
          to="/products/logs" 
          class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover:shadow-md transition-all duration-200 cursor-pointer group relative"
        >
          <div class="flex items-center justify-between mb-4">
            <FileText class="w-6 h-6 text-blue-600" />
          </div>
          <div class="pt-6">
            <!-- Логи -->
            <span class="text-sm font-medium text-gray-900">{{ t('ProductsPage_8') }}</span>
          </div>
        </router-link>
      </div>

      <!-- График статистики -->
      <div class="mt-8">
        <ProductsChart />
      </div>
    </div>
  </div>
</template>

<script setup>
import { PackagePlus, PackageMinus, ClipboardList, ArrowRightLeft, Package, Warehouse, FileText, Plus, Loader2 } from 'lucide-vue-next'
import { ref, onMounted, computed } from 'vue'
import { apiRequest } from '@/config/api'
import { t } from '../../locales/index.js'
import ProductsChart from '../ProductsChart.vue'

// Устанавливаем заголовок страницы
document.title = 'B2B SKLAD - Товары'

// Данные пользователя из localStorage
const user = ref(null)
const userName = computed(() => user.value?.first_name || '')
const userEmail = computed(() => user.value?.email || '')

// Количества для карточек
const counts = ref({
  receipts: 0,
  writeOffs: 0,
  inventory: 0,
  transfers: 0,
  balances: 0,
  warehouses: 0
})
const loadingCounts = ref(false)
const errorCounts = ref(false)
let countsLoaded = false

// Загружаем данные пользователя из localStorage
onMounted(() => {
  const userData = localStorage.getItem('user')
  if (userData) {
    user.value = JSON.parse(userData)
  }
  
  // Загружаем количества для карточек
  loadCardCounts()
})

// Функция для загрузки количеств
const loadCardCounts = async () => {
  if (countsLoaded) return
  countsLoaded = true
  loadingCounts.value = true
  errorCounts.value = false
  try {
    const res = await apiRequest('/card-counts/all', { method: 'GET' })
    if (res.ok && res.data.success && res.data.counts) {
      counts.value = res.data.counts
    } else {
      errorCounts.value = true
    }
  } catch (e) {
    errorCounts.value = true
  } finally {
    loadingCounts.value = false
  }
}
</script>

<style scoped>
/* Эффект приподнятия карточки при наведении */
.group:hover {
  transform: translateY(-4px);
}

.group:hover .text-blue-600 {
  color: #2563eb;
}
</style> 