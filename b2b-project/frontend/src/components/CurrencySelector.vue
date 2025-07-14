<template>
  <div class="relative">
    <!-- Кнопка выбора валюты -->
    <button
      @click="toggleCurrencyMenu"
      type="button"
      class="flex items-center gap-2 text-sm text-gray-700 hover:text-blue-600 transition-colors cursor-pointer"
    >
      <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
      </svg>
      <span>{{ selectedCurrency || 'USD' }}</span>
      <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path d="m6 9 6 6 6-6"/>
      </svg>
    </button>

    <!-- Выпадающее меню валют -->
    <div
      v-if="currencyMenuOpen"
      class="absolute right-0 z-[9999] mt-2 w-48 origin-top-right rounded-md bg-white shadow-lg focus:outline-none border border-gray-200"
      role="menu"
      aria-orientation="vertical"
      tabindex="-1"
    >
      <div class="py-1" role="none">
        <div class="px-3 py-2 text-xs font-medium text-gray-500 uppercase tracking-wider">
          Выберите валюту
        </div>
        
        <div v-if="loading" class="px-3 py-2 text-sm text-gray-500">
          Загрузка...
        </div>
        
        <div v-else-if="error" class="px-3 py-2 text-sm text-red-500">
          {{ error }}
        </div>
        
        <template v-else>
          <button
            v-for="currency in currencies"
            :key="currency.currency_type"
            @click="selectCurrency(currency.currency_type)"
            class="flex items-center justify-between w-full px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 cursor-pointer"
            :class="{ 'bg-blue-50 text-blue-700': selectedCurrency === currency.currency_type }"
            role="menuitem"
            tabindex="-1"
          >
            <span>{{ currency.currency_type }}</span>
            <span class="text-xs text-gray-500">{{ currency.full_name }}</span>
          </button>
        </template>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { currencyService } from '../services/currency.js'

const props = defineProps({
  currentCurrency: {
    type: String,
    default: 'USD'
  }
})

const emit = defineEmits(['currency-changed'])

const currencies = ref([])
const selectedCurrency = ref(props.currentCurrency)
const currencyMenuOpen = ref(false)
const loading = ref(false)
const error = ref('')

// Закрытие меню при клике вне его
const handleClickOutside = (event) => {
  if (!event.target.closest('.currency-selector')) {
    currencyMenuOpen.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
  loadCurrencies()
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})

const toggleCurrencyMenu = () => {
  currencyMenuOpen.value = !currencyMenuOpen.value
}

const loadCurrencies = async () => {
  loading.value = true
  error.value = ''
  
  try {
    // ЗАКОММЕНТИРОВАНО: API запросы временно отключены
    // // Сначала попробуем получить курсы из базы
    // currencies.value = await currencyService.getRates()
    
    // // Если курсов нет, попробуем загрузить с внешнего API
    // if (currencies.value.length === 0) {
    //   await currencyService.fetchAndSaveRates()
    //   currencies.value = await currencyService.getRates()
    // }
    
    // Временно используем только базовые валюты
    currencies.value = [
      { currency_type: 'USD', full_name: 'United States Dollar' },
      { currency_type: 'EUR', full_name: 'Euro' },
      { currency_type: 'RUB', full_name: 'Russian Ruble' },
      { currency_type: 'UZS', full_name: 'Uzbekistani Som' }
    ]
  } catch (err) {
    console.error('Error loading currencies:', err)
    error.value = 'Ошибка загрузки валют'
    
    // Fallback - базовые валюты
    currencies.value = [
      { currency_type: 'USD', full_name: 'United States Dollar' },
      { currency_type: 'EUR', full_name: 'Euro' },
      { currency_type: 'RUB', full_name: 'Russian Ruble' },
      { currency_type: 'UZS', full_name: 'Uzbekistani Som' }
    ]
  } finally {
    loading.value = false
  }
}

const selectCurrency = async (currencyType) => {
  try {
    // ЗАКОММЕНТИРОВАНО: API запрос временно отключен
    // await currencyService.updateUserCurrency(currencyType)
    
    selectedCurrency.value = currencyType
    currencyMenuOpen.value = false
    emit('currency-changed', currencyType)
    
    // Обновляем данные пользователя в localStorage
    const userData = localStorage.getItem('user')
    if (userData) {
      const user = JSON.parse(userData)
      user.currency = currencyType
      localStorage.setItem('user', JSON.stringify(user))
    }
  } catch (err) {
    console.error('Error updating currency:', err)
    error.value = 'Ошибка обновления валюты'
  }
}
</script>

<style scoped>
.currency-selector {
  position: relative;
}
</style> 