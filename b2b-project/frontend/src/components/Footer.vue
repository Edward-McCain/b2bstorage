<script setup>
// Компонент футера сайта
import { t } from '../locales/index.js'
</script>

<template>
  <footer class="bg-gray-100 py-12 border-t border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center">
        <!-- Логотип и описание -->
        <div class="mb-8">
          <div class="flex items-center justify-center gap-3 mb-4">
            <!-- <span class="text-2xl font-bold text-gray-800">B2B SKLAD</span> -->
            <img src="../assets/skladlogo.png" alt="B2B SKLAD" class="h-8">
          </div>
          <p class="text-gray-600 text-sm max-w-md mx-auto">
            <!-- Профессиональная система складского учета -->
            {{ t('Footer_1') }}<br>
            <!-- Часть экосистемы B2B Global -->
            {{ t('Footer_2') }}
          </p>
        </div>
        
        <!-- Копирайт -->
        <div class="border-t border-gray-200 pt-8">
          <!-- Copyright © 2025 - Все права защищены B2B Global. -->
          <p class="text-gray-500 text-sm">{{ t('Footer_3') }}</p>
        </div>
      </div>
    </div>
  </footer>
</template>

<style scoped>
/* Дополнительные стили для футера */
</style> 