<template>
  <AdminLayout>
    <!-- Заголовок страницы -->
    <div class="bg-white shadow-sm border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="py-6">
          <h1 class="text-3xl font-bold text-gray-900">Управление пользователями</h1>
          <p class="mt-2 text-sm text-gray-600">Администрирование пользователей системы</p>
        </div>
      </div>
    </div>

    <!-- Основной контент -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

      <!-- Фильтры и поиск -->
      <div class="bg-white shadow rounded-lg mb-8">
        <div class="px-4 py-5 sm:p-6">
          <div class="">
            <!-- Поиск -->
            <div class="md:col-span-2">
              <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Поиск пользователей</label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <Search class="h-5 w-5 text-gray-400" />
                </div>
                <input
                  type="text"
                  id="search"
                  v-model="searchQuery"
                  placeholder="Поиск по имени, email, компании, ИНН..."
                  class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                />
              </div>
            </div>

          </div>
        </div>
      </div>

      <!-- Таблица пользователей -->
      <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
          <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Список пользователей</h3>
          <div class="overflow-x-auto">
            <!-- Индикатор загрузки -->
            <div v-if="isLoading" class="flex justify-center items-center py-12">
              <Loader2 class="h-8 w-8 text-blue-500 animate-spin" />
              <span class="ml-3 text-gray-600">Загрузка пользователей...</span>
            </div>
            
            <!-- Таблица -->
            <table v-else class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-sm font-medium text-gray-500">
                    Пользователь
                  </th>
                  <th class="px-6 py-3 text-left text-sm font-medium text-gray-500">
                    Роль
                  </th>
                  <th class="px-6 py-3 text-left text-sm font-medium text-gray-500">
                    Статус
                  </th>
                  <th class="px-6 py-3 text-left text-sm font-medium text-gray-500">
                    Дата регистрации
                  </th>
                  <th class="px-6 py-3 text-left text-sm font-medium text-gray-500">
                    Последний вход
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="user in filteredUsers" :key="user.id">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="flex-shrink-0 h-10 w-10">
                        <img v-if="user.avatar_url" :src="user.avatar_url" alt="" class="h-10 w-10 rounded-full">
                        <div v-else class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                          <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                          </svg>
                        </div>
                      </div>
                      <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900">{{ user.user_name }}</div>
                        <div class="text-sm text-gray-500">{{ user.email }}</div>
                        <div v-if="user.company_name" class="text-sm text-gray-500">{{ user.company_name }}</div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span v-if="user.role === 1" class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                      Администратор
                    </span>
                    <span v-else class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                      Пользователь
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span v-if="user.banned" class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                      Заблокирован
                    </span>
                    <span v-else-if="user.is_online" class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                      Онлайн
                    </span>
                    <span v-else class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                      Офлайн
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ formatDate(user.created_at) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ formatDate(user.last_logged_in) }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import AdminLayout from './AdminLayout.vue'
import { Users, UserCheck, Shield, UserX, Search, Loader2 } from 'lucide-vue-next'
import api from '../../config/api.js'

// Данные статистики
const stats = ref({
  totalUsers: 0,
  activeUsers: 0,
  adminUsers: 0,
  bannedUsers: 0
})

// Пользователи
const users = ref([])
const searchQuery = ref('')
const roleFilter = ref('')
const statusFilter = ref('')
const isLoading = ref(false)

// Форматирование даты
const formatDate = (dateString) => {
  if (!dateString) return 'Не указано'
  const date = new Date(dateString)
  return date.toLocaleDateString('ru-RU', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

// Фильтрация пользователей
const filteredUsers = computed(() => {
  let filtered = users.value

  // Поиск
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(user => 
      user.user_name?.toLowerCase().includes(query) ||
      user.first_name?.toLowerCase().includes(query) ||
      user.email?.toLowerCase().includes(query) ||
      user.company_name?.toLowerCase().includes(query) ||
      user.inn?.toLowerCase().includes(query)
    )
  }

  // Фильтр по роли
  if (roleFilter.value !== '') {
    filtered = filtered.filter(user => user.role === parseInt(roleFilter.value))
  }

  // Фильтр по статусу
  if (statusFilter.value) {
    switch (statusFilter.value) {
      case 'active':
        filtered = filtered.filter(user => !user.banned && user.is_active)
        break
      case 'banned':
        filtered = filtered.filter(user => user.banned)
        break
      case 'inactive':
        filtered = filtered.filter(user => !user.is_active)
        break
    }
  }

  return filtered
})

// Загрузка данных - статистика теперь приходит вместе с пользователями

const loadUsers = async () => {
  try {
    isLoading.value = true
    const params = new URLSearchParams()
    if (searchQuery.value) {
      params.append('search', searchQuery.value)
    }
    if (roleFilter.value) {
      params.append('role', roleFilter.value)
    }
    if (statusFilter.value) {
      params.append('status', statusFilter.value)
    }
    
    const response = await api.get(`/admin/users?${params.toString()}`)
    if (response.data.success) {
      users.value = response.data.data.users
      // Обновляем статистику из ответа
      if (response.data.data.stats) {
        stats.value = {
          totalUsers: response.data.data.stats.total_users,
          activeUsers: response.data.data.stats.active_users,
          adminUsers: response.data.data.stats.admin_users,
          bannedUsers: response.data.data.stats.banned_users
        }
      }
    }
  } catch (error) {
    console.error('Ошибка загрузки пользователей:', error)
  } finally {
    isLoading.value = false
  }
}



// Автоматическая перезагрузка при изменении поиска
watch(searchQuery, () => {
  loadUsers()
}, { debounce: 300 })

onMounted(() => {
  loadUsers()
})
</script> 