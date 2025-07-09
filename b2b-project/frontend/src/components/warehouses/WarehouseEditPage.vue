<template>
  <div class="min-h-screen bg-gray-50" style="padding-top: 66px;">
    <ProductsMenu />
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="flex items-center justify-between mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Редактировать склад</h1>
        <button @click="goBack" class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold px-6 py-2 rounded-lg border shadow transition text-sm">
          Назад
        </button>
      </div>
      
      <div v-if="loading" class="flex items-center justify-center py-8">
        <Loader2 class="animate-spin h-6 w-6 text-blue-600 mr-2" />
        <span class="text-sm text-gray-600">Загрузка данных склада...</span>
      </div>
      
      <form v-else @submit.prevent="handleSubmit" class="space-y-6">
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
            <span v-if="saving">Сохранение...</span>
            <span v-else>Сохранить</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import ProductsMenu from '../products/ProductsMenu.vue'
import { useRouter, useRoute } from 'vue-router'
import { apiRequest } from '@/config/api'
import { Loader2 } from 'lucide-vue-next'
import toastr from 'toastr'

// Устанавливаем заголовок страницы
document.title = 'B2B SKLAD - Склады'

const router = useRouter()
const route = useRoute()

const form = ref({
  name: '',
  address: ''
})

const errors = ref({})
const serverError = ref('')
const successMessage = ref('')
const saving = ref(false)
const loading = ref(true)

function goBack() {
  router.push('/warehouses')
}

async function fetchWarehouse() {
  const id = route.params.id
  try {
    const res = await apiRequest(`/warehouses/${id}`, { method: 'GET' })
    if (res.ok && res.data.success) {
      const warehouse = res.data.data
      form.value = {
        name: warehouse.name || '',
        address: warehouse.address || ''
      }
    } else {
      router.push('/warehouses')
    }
  } catch (error) {
    console.error('Ошибка загрузки склада:', error)
    router.push('/warehouses')
  } finally {
    loading.value = false
  }
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
    const id = route.params.id
    const response = await apiRequest(`/warehouses/${id}`, {
      method: 'PUT',
      body: JSON.stringify(form.value)
    })
    
    if (response.ok && response.data.success) {
      successMessage.value = 'Склад успешно обновлен!'
      toastr.success('Склад успешно обновлен')
      setTimeout(() => {
        router.push('/warehouses')
      }, 1000)
    } else {
      serverError.value = response.data.message || 'Произошла ошибка при обновлении склада'
    }
  } catch (error) {
    console.error('Ошибка при обновлении склада:', error)
    serverError.value = 'Произошла ошибка при обновлении склада'
  } finally {
    saving.value = false
  }
}

onMounted(fetchWarehouse)
</script> 