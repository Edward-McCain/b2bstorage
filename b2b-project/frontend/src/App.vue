<script setup>
import Header from './components/Header.vue'
import Footer from './components/Footer.vue'
</script>

<template>
  <div>
    <Header />
    <router-view />
    <Footer />
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
</style>
