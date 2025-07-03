<template>
  <div>
    <PurchasesMenu />
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Управление закупками</h1>
        <p class="text-gray-700 mb-4">Настройте параметры и процессы закупок для вашей компании. Здесь вы можете управлять поставщиками, условиями оплаты, автоматизацией и уведомлениями.</p>
        <ul class="list-disc pl-6 text-gray-700 space-y-2">
          <li>Добавление и редактирование поставщиков</li>
          <li>Настройка условий оплаты и доставки</li>
          <li>Автоматизация согласования заказов</li>
          <li>Уведомления о статусах закупок</li>
          <li>Интеграция с бухгалтерией</li>
        </ul>
      </div>
    </div>
  </div>
</template>
<script setup>
import PurchasesMenu from './PurchasesMenu.vue'
document.title = 'Управление закупками - B2B Storage'
</script> 