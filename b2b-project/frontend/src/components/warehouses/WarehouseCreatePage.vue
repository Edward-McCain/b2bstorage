<template>
  <div class="min-h-screen bg-gray-50" style="padding-top: 66px;">
    <ProductsMenu />
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="flex items-center justify-between mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Создать склад</h1>
        <router-link
          to="/warehouses"
          class="flex items-center gap-2 text-gray-600 hover:text-gray-900 font-medium px-4 py-2 rounded text-sm hover:bg-gray-100 transition-colors"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
          </svg>
        </router-link>
      </div>
      
      <form @submit.prevent="handleSubmit" class="space-y-6">
        <div class="bg-white rounded-xl shadow p-6">
          <div class="space-y-4">
            <div>
              <label class="block text-sm text-gray-700 mb-1">Название склада *</label>
              <input 
                v-model="form.name" 
                type="text" 
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm" 
                :class="{'border-red-400': errors.name}"
                placeholder="Введите название склада"
              />
              <div v-if="errors.name" class="text-sm text-red-500 mt-1">{{ errors.name }}</div>
            </div>
            
            <div>
              <label class="block text-sm text-gray-700 mb-1">Адрес склада</label>
              <textarea 
                v-model="form.address" 
                rows="3" 
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm"
                placeholder="Введите адрес склада"
              ></textarea>
            </div>
          </div>
        </div>

        <!-- Сообщения об ошибках -->
        <div v-if="serverError" class="bg-red-50 border border-red-200 rounded-lg p-4">
          <div class="text-sm text-red-700">{{ serverError }}</div>
        </div>

        <!-- Сообщения об успехе -->
        <div v-if="successMessage" class="bg-green-50 border border-green-200 rounded-lg p-4">
          <div class="text-sm text-green-700">{{ successMessage }}</div>
        </div>

        <!-- Кнопки -->
        <div class="flex justify-end gap-2 mt-6">
          <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded-lg shadow transition text-sm flex items-center gap-2" :disabled="saving">
            <Loader2 v-if="saving" class="animate-spin h-4 w-4" />
            <span v-if="saving">Создание...</span>
            <span v-else>Создать склад</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import ProductsMenu from '../products/ProductsMenu.vue'
import { useRouter } from 'vue-router'
import { apiRequest } from '@/config/api'
import { Loader2 } from 'lucide-vue-next'
import toastr from 'toastr'

// Устанавливаем заголовок страницы
document.title = 'B2B SKLAD - Склады'

const router = useRouter()

const form = ref({
  name: '',
  address: ''
})

const errors = ref({})
const serverError = ref('')
const successMessage = ref('')
const saving = ref(false)

function goBack() {
  router.push('/warehouses')
}

function validate() {
  let isValid = true
  errors.value = {}
  
  if (!form.value.name.trim()) {
    errors.value.name = 'Название склада обязательно'
    isValid = false
  }
  
  return isValid
}

async function handleSubmit() {
  if (!validate()) return
  
  saving.value = true
  serverError.value = ''
  successMessage.value = ''
  
  try {
    const response = await apiRequest('/warehouses', {
      method: 'POST',
      body: JSON.stringify(form.value)
    })
    
    if (response.ok && response.data.success) {
      successMessage.value = 'Склад успешно создан!'
      toastr.success('Склад успешно создан')
      setTimeout(() => {
        router.push('/warehouses')
      }, 1000)
    } else {
      serverError.value = response.data.message || 'Произошла ошибка при создании склада'
    }
  } catch (error) {
    console.error('Ошибка при создании склада:', error)
    serverError.value = 'Произошла ошибка при создании склада'
  } finally {
    saving.value = false
  }
}
</script> 