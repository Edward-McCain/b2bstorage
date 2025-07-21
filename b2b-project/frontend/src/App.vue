<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import Header from './components/Header.vue'
import Footer from './components/Footer.vue'

const route = useRoute()

// Проверяем, является ли текущий маршрут административным
const isAdminRoute = computed(() => {
  return route.path.startsWith('/admin')
})
</script>

<template>
  <div>
    <!-- Показываем Header и Footer только для не-административных страниц -->
    <Header v-if="!isAdminRoute" />
    <router-view />
    <!-- <Footer v-if="!isAdminRoute" /> -->
  </div>
</template>

<style scoped>
/* Дополнительные стили для главной страницы */
</style>

<style>
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
</style>
