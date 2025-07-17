<template>
  <AdminLayout>
    <!-- Заголовок страницы -->
    <div class="bg-white shadow-sm border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="py-6">
          <h1 class="text-3xl font-bold text-gray-900">Панель администратора</h1>
          <p class="mt-2 text-sm text-gray-600">Управление системой и мониторинг статистики</p>
        </div>
      </div>
    </div>

    <!-- Основной контент -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Статистические карточки -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Общее количество пользователей -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                  <Users class="w-5 h-5 text-white" />
                </div>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Всего пользователей</dt>
                  <dd v-if="loadingStats" class="flex items-center">
                    <Loader2 class="h-5 w-5 text-blue-500 animate-spin mr-2" />
                    <span class="text-sm text-gray-500">Загрузка...</span>
                  </dd>
                  <dd v-else class="text-lg font-medium text-gray-900">{{ stats.totalUsers || 0 }}</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <!-- Пользователи в сети -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                  <UserCheck class="w-5 h-5 text-white" />
                </div>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Пользователи в сети</dt>
                  <dd v-if="loadingStats" class="flex items-center">
                    <Loader2 class="h-5 w-5 text-green-500 animate-spin mr-2" />
                    <span class="text-sm text-gray-500">Загрузка...</span>
                  </dd>
                  <dd v-else class="text-lg font-medium text-gray-900">{{ stats.onlineUsers || 0 }}</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <!-- Общее количество товаров -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                  <Package class="w-5 h-5 text-white" />
                </div>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Всего товаров</dt>
                  <dd v-if="loadingStats" class="flex items-center">
                    <Loader2 class="h-5 w-5 text-yellow-500 animate-spin mr-2" />
                    <span class="text-sm text-gray-500">Загрузка...</span>
                  </dd>
                  <dd v-else class="text-lg font-medium text-gray-900">{{ stats.totalProducts || 0 }}</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <!-- Склады -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                  <Building2 class="w-5 h-5 text-white" />
                </div>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Склады</dt>
                  <dd v-if="loadingStats" class="flex items-center">
                    <Loader2 class="h-5 w-5 text-purple-500 animate-spin mr-2" />
                    <span class="text-sm text-gray-500">Загрузка...</span>
                  </dd>
                  <dd v-else class="text-lg font-medium text-gray-900">{{ stats.totalWarehouses || 0 }}</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Графики и дополнительные данные -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Последние регистрации -->
        <div class="bg-white shadow rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Последние регистрации</h3>
            
            <!-- Индикатор загрузки -->
            <div v-if="loadingUsers" class="flex justify-center items-center py-8">
              <Loader2 class="h-8 w-8 text-blue-500 animate-spin" />
              <span class="ml-3 text-gray-600">Загрузка пользователей...</span>
            </div>
            
            <!-- Список пользователей -->
            <div v-else class="space-y-4">
              <div v-for="user in recentUsers" :key="user.id" class="flex items-center space-x-3">
                <div class="flex-shrink-0">
                  <img v-if="user.avatar_url" :src="user.avatar_url" alt="" class="h-8 w-8 rounded-full">
                  <div v-else class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                    <User class="w-4 h-4 text-gray-600" />
                  </div>
                </div>
                <div class="flex-1 min-w-0">
                  <p class="text-sm font-medium text-gray-900 truncate">{{ user.user_name }}</p>
                  <p class="text-sm text-gray-500">{{ user.email }}</p>
                </div>
                <div class="text-sm text-gray-500">
                  {{ formatDate(user.created_at) }}
                </div>
              </div>
              
              <!-- Сообщение если нет пользователей -->
              <div v-if="recentUsers.length === 0" class="text-center py-4 text-gray-500">
                Нет данных о последних регистрациях
              </div>
            </div>
          </div>
        </div>

        <!-- Системная информация -->
        <div class="bg-white shadow rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Системная информация</h3>
            <dl class="space-y-4">
              <div class="flex justify-between">
                <dt class="text-sm font-medium text-gray-500">Версия системы</dt>
                <dd class="text-sm text-gray-900">1.0.0</dd>
              </div>
              <div class="flex justify-between">
                <dt class="text-sm font-medium text-gray-500">Последнее обновление</dt>
                <dd class="text-sm text-gray-900">{{ formatDate(new Date()) }}</dd>
              </div>
              <div class="flex justify-between">
                <dt class="text-sm font-medium text-gray-500">Статус системы</dt>
                <dd class="text-sm text-green-600 font-medium">Работает</dd>
              </div>
            </dl>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import AdminLayout from './AdminLayout.vue'
import api from '../../config/api.js'
import { Loader2, Users, UserCheck, Package, Building2, User } from 'lucide-vue-next'

// Данные статистики
const stats = ref({
  totalUsers: 0,
  onlineUsers: 0,
  totalProducts: 0,
  totalWarehouses: 0
})

// Последние пользователи
const recentUsers = ref([])

// Состояние загрузки
const loadingStats = ref(true)
const loadingUsers = ref(true)

// Форматирование даты
const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('ru-RU', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

// Загрузка данных
const loadStats = async () => {
  try {
    loadingStats.value = true
    const response = await api.get('/admin/stats')
    if (response.data.success) {
      stats.value = {
        totalUsers: response.data.data.total_users,
        onlineUsers: response.data.data.online_users,
        totalProducts: response.data.data.total_products,
        totalWarehouses: response.data.data.total_warehouses
      }
    }
  } catch (error) {
    console.error('Ошибка загрузки статистики:', error)
  } finally {
    loadingStats.value = false
  }
}

const loadRecentUsers = async () => {
  try {
    loadingUsers.value = true
    const response = await api.get('/admin/recent-users')
    if (response.data.success) {
      recentUsers.value = response.data.data
    }
  } catch (error) {
    console.error('Ошибка загрузки пользователей:', error)
  } finally {
    loadingUsers.value = false
  }
}

onMounted(async () => {
  // Загружаем данные параллельно
  await Promise.all([
    loadStats(),
    loadRecentUsers()
  ])
})
</script> 