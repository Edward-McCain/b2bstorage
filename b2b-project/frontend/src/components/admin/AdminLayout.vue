<template>
  <div class="min-h-screen bg-gray-50">
    <!-- WebRTC интеграция отключена - используется внешний SDK -->
    
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
// WebRTCIntegration отключен - используется внешний SDK
</script> 