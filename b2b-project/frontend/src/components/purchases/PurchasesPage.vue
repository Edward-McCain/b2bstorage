<template>
  <div>
    <PurchasesMenu />
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Закупки</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <!-- Заказы поставщикам -->
          <div class="bg-blue-50 rounded-lg p-6 border border-blue-200">
            <div class="flex items-center mb-4">
              <div class="bg-blue-100 rounded-lg p-3">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
              </div>
              <h3 class="ml-3 text-lg font-semibold text-gray-900">Заказы поставщикам</h3>
            </div>
            <p class="text-gray-600 mb-4">Управление заказами товаров у поставщиков</p>
            <router-link to="/purchases/supplier-orders" class="text-blue-600 hover:text-blue-800 font-medium">
              Перейти →
            </router-link>
          </div>

          <!-- Счета поставщиков -->
          <div class="bg-green-50 rounded-lg p-6 border border-green-200">
            <div class="flex items-center mb-4">
              <div class="bg-green-100 rounded-lg p-3">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
              </div>
              <h3 class="ml-3 text-lg font-semibold text-gray-900">Счета поставщиков</h3>
            </div>
            <p class="text-gray-600 mb-4">Обработка и оплата счетов от поставщиков</p>
            <router-link to="/purchases/supplier-invoices" class="text-green-600 hover:text-green-800 font-medium">
              Перейти →
            </router-link>
          </div>

          <!-- Приемки -->
          <div class="bg-purple-50 rounded-lg p-6 border border-purple-200">
            <div class="flex items-center mb-4">
              <div class="bg-purple-100 rounded-lg p-3">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
              </div>
              <h3 class="ml-3 text-lg font-semibold text-gray-900">Приемки</h3>
            </div>
            <p class="text-gray-600 mb-4">Прием товаров на склад</p>
            <router-link to="/purchases/receipts" class="text-purple-600 hover:text-purple-800 font-medium">
              Перейти →
            </router-link>
          </div>

          <!-- Возвраты поставщикам -->
          <div class="bg-red-50 rounded-lg p-6 border border-red-200">
            <div class="flex items-center mb-4">
              <div class="bg-red-100 rounded-lg p-3">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                </svg>
              </div>
              <h3 class="ml-3 text-lg font-semibold text-gray-900">Возвраты поставщикам</h3>
            </div>
            <p class="text-gray-600 mb-4">Возврат товаров поставщикам</p>
            <router-link to="/purchases/supplier-returns" class="text-red-600 hover:text-red-800 font-medium">
              Перейти →
            </router-link>
          </div>

          <!-- Счета-фактуры полученные -->
          <div class="bg-yellow-50 rounded-lg p-6 border border-yellow-200">
            <div class="flex items-center mb-4">
              <div class="bg-yellow-100 rounded-lg p-3">
                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
              </div>
              <h3 class="ml-3 text-lg font-semibold text-gray-900">Счета-фактуры полученные</h3>
            </div>
            <p class="text-gray-600 mb-4">Учет полученных счетов-фактур</p>
            <router-link to="/purchases/received-invoices" class="text-yellow-600 hover:text-yellow-800 font-medium">
              Перейти →
            </router-link>
          </div>

          <!-- Управление закупками -->
          <div class="bg-indigo-50 rounded-lg p-6 border border-indigo-200">
            <div class="flex items-center mb-4">
              <div class="bg-indigo-100 rounded-lg p-3">
                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
              </div>
              <h3 class="ml-3 text-lg font-semibold text-gray-900">Управление закупками</h3>
            </div>
            <p class="text-gray-600 mb-4">Настройки и управление процессом закупок</p>
            <router-link to="/purchases/purchase-management" class="text-indigo-600 hover:text-indigo-800 font-medium">
              Перейти →
            </router-link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import PurchasesMenu from './PurchasesMenu.vue'

// Устанавливаем заголовок страницы
document.title = 'Закупки - B2B Storage'
</script> 