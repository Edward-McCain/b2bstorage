<script setup>
// Компонент шапки сайта
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const isAuthenticated = ref(false)
const user = ref(null)

onMounted(() => {
  const token = localStorage.getItem('auth_token')
  const userData = localStorage.getItem('user')
  
  if (token && userData) {
    isAuthenticated.value = true
    user.value = JSON.parse(userData)
  }
})

const handleLogout = () => {
  localStorage.removeItem('auth_token')
  localStorage.removeItem('user')
  isAuthenticated.value = false
  user.value = null
  window.location.reload()
}

const handleLogin = () => {
  router.push('/auth')
}
</script>

<template>
  <header class="fixed inset-x-0 top-0 z-50 bg-white/90 backdrop-blur border-b border-gray-200">
    <nav class="flex items-center justify-between p-4 lg:px-8" aria-label="Global">
      <!-- Логотип -->
      <div class="flex lg:flex-1">
        <a href="/" class="-m-1.5 p-1.5 flex items-center gap-2">
          <img class="h-8 w-auto" src="../assets/skladlogo.png" alt="" />
        </a>
      </div>
      <!-- Мобильная кнопка входа -->
      <div class="flex lg:hidden">
        <!-- Если пользователь авторизован -->
        <div v-if="isAuthenticated" class="flex items-center gap-2">
          <span class="text-sm text-gray-700">{{ user?.user_name || 'Пользователь' }}</span>
          <button 
            @click="handleLogout"
            class="text-sm font-semibold text-white bg-blue-700 border-blue-700 px-3 py-1.5 rounded-lg font-semibold text-sm transition-colors cursor-pointer"
          >
            Выйти
          </button>
        </div>
        <!-- Если пользователь не авторизован -->
        <button 
          v-else
          @click="handleLogin"
          class="border-2 text-white bg-blue-700 border-blue-700 px-3 py-1.5 rounded-lg font-semibold text-sm transition-colors cursor-pointer"
        >
          Войти
        </button>
      </div>
      <!-- Навигация (десктоп) -->
      <div class="hidden lg:flex lg:gap-x-12">
        <!-- <a href="#home" class="text-sm font-semibold text-gray-900 hover:text-primary">Главная</a> -->
        <a href="#features" class="text-sm font-semibold text-gray-900 hover:text-primary">Возможности</a>
        <a href="#ecosystem" class="text-sm font-semibold text-gray-900 hover:text-primary">Экосистема</a>
      </div>
      <!-- Справа: кнопки авторизации -->
      <div class="hidden lg:flex lg:flex-1 lg:justify-end items-center gap-4">
        <!-- Если пользователь авторизован -->
        <div v-if="isAuthenticated" class="flex items-center gap-4">
          <span class="text-sm text-gray-700">Привет, {{ user?.user_name || 'Пользователь' }}!</span>
          <button 
            @click="handleLogout"
            class="text-sm font-semibold border-2 border-blue-700 text-white bg-blue-700 px-4 py-2 rounded-lg font-semibold text-sm transition-colors cursor-pointer"
          >
            Выйти
          </button>
        </div>
        <!-- Если пользователь не авторизован -->
        <button 
          v-else
          @click="handleLogin"
          class="border-2 border-blue-700 text-white bg-blue-700 px-4 py-2 rounded-lg font-semibold text-sm transition-colors cursor-pointer"
        >
          Войти
        </button>
      </div>
    </nav>
  </header>
</template>

<style scoped>
/* Дополнительные стили для шапки */
</style> 