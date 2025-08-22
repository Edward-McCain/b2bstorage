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
  padding-top: 0px; /* Отступ для фиксированного header */
}

.admin-main {
  padding-top: 0; /* Админ страницы без отступа */
}

/* Глобальные стили для multiselect */
.multiselect-custom {
  --ms-option-bg-selected: #3b82f6;
  --ms-option-color-selected: #ffffff;
  --ms-option-bg-selected-pointed: #2563eb;
  --ms-option-color-selected-pointed: #ffffff;
  --ms-option-bg-pointed: #f3f4f6;
  --ms-option-color-pointed: #374151;
  --ms-tag-bg: #3b82f6;
  --ms-tag-color: #ffffff;
  --ms-tag-remove-bg: #2563eb;
  --ms-tag-remove-color: #ffffff;
}

.multiselect-custom .multiselect-wrapper {
  font-size: 0.875rem;
}

.multiselect-custom .multiselect-option {
  font-size: 0.875rem;
}

.multiselect,
.multiselect.multiselect-custom {
  display: block !important;
  width: 100%;
  min-width: 200px;
}

.notification-toast {
  z-index: 2147483647 !important;
  top: 70px !important;
}

#toast-container > div {
  background: #fff !important;
  color: #222 !important;
  font-family: inherit !important;
  font-size: 0.95rem !important;
  border-radius: 0.5rem !important;
  box-shadow: 0 4px 16px 0 rgba(0,0,0,0.08) !important;
  border: 1px solid #e5e7eb !important;
  padding: 0.75rem 1.25rem !important;
}
#toast-container > div.toast-success {
  border-left: 4px solid #22c55e !important;
}
#toast-container > div.toast-error {
  border-left: 4px solid #ef4444 !important;
}
#toast-container > div.toast-info {
  border-left: 4px solid #3b82f6 !important;
}
#toast-container > div.toast-warning {
  border-left: 4px solid #f59e42 !important;
}

button.bg-gray-100 {
  border: none !important;
}

button, input, select, optgroup, textarea, ::file-selector-button {
  box-shadow: none !important;
}
*:not(.shadow-2xl):not(.fixed *) {box-shadow: none !important;}
.bg-white.border:not(input):not(textarea):not(.pro_nav_card) {border: none !important;}

body::-webkit-scrollbar {width: 5px;height: 5px;background: rgba(0, 0, 0, 0);}
body::-webkit-scrollbar-track {background: rgba(0,0,0,0);opacity:.7;}
body::-webkit-scrollbar-thumb {border-radius: 3px;background: #7696ff !important; cursor: pointer;}

button:not(.transition-colors), input, select, optgroup, textarea, ::file-selector-button, .dp__input {border-radius: 16px !important;min-height: 42px;}
.fixed button svg:not(.user_menu svg) {margin: auto !important;}
.fixed button {min-width: 42px;}
button {cursor: pointer;}
input:focus, textarea:focus {border-color: #7696ff !important;}
.multiselect, .multiselect-wrapper, .multiselect-wrapper input, .multiselect-dropdown, .dp__menu {border-radius: 16px !important;}
.rounded, .rounded-lg, .dropdown-menu-custom {border-radius: 16px;}

.multiselect-dropdown {transform: translateY(105%) !important;}
.multiselect-custom .multiselect-option {
    margin-top: 5px;
    margin-left: 4px;
    margin-bottom: 5px;
    border-radius: 16px;
}
multiselect, .multiselect.multiselect-custom {
  padding-bottom: 2px;
}

table img {border-radius: 5px !important;}

.flay_out_menu button {
  background: #fff;
  padding: 10px 20px;
  border-radius: 12px;
  transition: all .3s;
}
.flay_out_menu button:hover {
  background: #f1f5ff;
}

.max-w-7xl {max-width: 1400px !important;}
</style>