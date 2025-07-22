<template>
  <div class="currency-selector">
    <!-- Обычный HTML select -->
    <select 
      v-model="selectedCurrency" 
      @change="selectCurrency($event.target.value)"
      class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm bg-white"
    >
      <option 
        v-for="currency in currencies" 
        :key="currency.currency_type" 
        :value="currency.currency_type"
      >
        {{ currency.currency_type }} - {{ currency.full_name }} ({{ currency.rate }})
      </option>
    </select>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import { currencyService } from '../services/currency.js'

const props = defineProps({
  currentCurrency: {
    type: String,
    default: 'USD'
  }
})

const emit = defineEmits(['currency-changed'])

// Статический список валют
const currencies = ref([
  { currency_type: 'AUD', full_name: 'Australian Dollar', rate: '1.54' },
  { currency_type: 'CAD', full_name: 'Canadian Dollar', rate: '1.37' },
  { currency_type: 'CHF', full_name: 'Swiss Franc', rate: '0.80' },
  { currency_type: 'CNY', full_name: 'Chinese Yuan', rate: '7.18' },
  { currency_type: 'EUR', full_name: 'Euro', rate: '0.86' },
  { currency_type: 'GBP', full_name: 'British Pound Sterling', rate: '0.74' },
  { currency_type: 'HKD', full_name: 'Hong Kong Dollar', rate: '7.85' },
  { currency_type: 'JPY', full_name: 'Japanese Yen', rate: '147.79' },
  { currency_type: 'NZD', full_name: 'New Zealand Dollar', rate: '1.68' },
  { currency_type: 'RUB', full_name: 'Russian Ruble', rate: '78.19' },
  { currency_type: 'USD', full_name: 'United States Dollar', rate: '1.00' },
  { currency_type: 'UZS', full_name: 'Uzbekistani Som', rate: '12531.39' }
])

const selectedCurrency = ref(props.currentCurrency)

// Следим за изменением currentCurrency от родителя
watch(() => props.currentCurrency, (newCurrency) => {
  selectedCurrency.value = newCurrency
})

const selectCurrency = (currencyType) => {
  console.log('Selecting currency:', currencyType)
  
  // Уведомляем родительский компонент
  emit('currency-changed', currencyType)
  
  // Обновляем данные пользователя в localStorage
  const userData = localStorage.getItem('user')
  if (userData) {
    try {
      const user = JSON.parse(userData)
      user.currency = currencyType
      localStorage.setItem('user', JSON.stringify(user))
    } catch (error) {
      console.error('Error updating localStorage:', error)
    }
  }
  
  // Показываем уведомление
  if (window.toastr) {
    window.toastr.success(`Валюта изменена на ${currencyType}`)
  }
  
  // Обновляем валюту пользователя через API (в фоне)
  currencyService.updateUserCurrency(currencyType).catch(error => {
    console.error('Error updating user currency:', error)
    if (window.toastr) {
      window.toastr.error('Ошибка при сохранении валюты на сервере')
    }
  })
}
</script>

<style scoped>
.currency-selector select {
  appearance: auto;
}
</style> 