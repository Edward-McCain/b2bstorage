<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Меню администратора -->
    <AdminMenu />
    
    <!-- Основной контент -->
    <main>
      <slot />
    </main>
  </div>
</template>

<script setup>
import AdminMenu from './AdminMenu.vue'
</script> 