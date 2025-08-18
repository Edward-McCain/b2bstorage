<template>
  <div id="app">
    <!-- Header отображается везде, кроме административных страниц -->
    <Header v-if="!isAdminRoute" />
    
    <!-- Основной контент -->
    <main :class="{ 'admin-main': isAdminRoute }">
      <router-view />
    </main>
    
    <!-- Footer отображается везде, кроме административных страниц -->
    <Footer v-if="!isAdminRoute" />
    
    <!-- WebRTC Integration компонент -->
    <WebRTCIntegration />
  </div>
</template>

<script setup>
import { computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import Header from './components/Header.vue'
import Footer from './components/Footer.vue'
import WebRTCIntegration from './components/WebRTCIntegration.vue'
const route = useRoute()

// Проверяем, является ли текущий маршрут административным
const isAdminRoute = computed(() => {
  return route.path.startsWith('/admin')
})

// Инициализация приложения
onMounted(async () => {
  console.log('🚀 App.vue инициализировано')
})
</script>

<style>
/* Глобальные стили */
#app {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

main {
  flex: 1;
  padding-top: 80px; /* Отступ для фиксированного header */
}

.admin-main {
  padding-top: 0; /* Админ страницы без отступа */
}
</style>