<template>
  <div>
    <SalesMenu />
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Продажи</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <!-- Заказы покупателей -->
          <div class="bg-blue-50 rounded-lg p-6 border border-blue-200">
            <div class="flex items-center mb-4">
              <div class="bg-blue-100 rounded-lg p-3">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
              </div>
              <h3 class="ml-3 text-lg font-semibold text-gray-900">Заказы покупателей</h3>
            </div>
            <p class="text-gray-600 mb-4">Управление заказами от покупателей</p>
            <router-link to="/sales/customer-orders" class="text-blue-600 hover:text-blue-800 font-medium">
              Перейти →
            </router-link>
          </div>

          <!-- Счета покупателям -->
          <div class="bg-green-50 rounded-lg p-6 border border-green-200">
            <div class="flex items-center mb-4">
              <div class="bg-green-100 rounded-lg p-3">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
              </div>
              <h3 class="ml-3 text-lg font-semibold text-gray-900">Счета покупателям</h3>
            </div>
            <p class="text-gray-600 mb-4">Выставление и управление счетами</p>
            <router-link to="/sales/customer-invoices" class="text-green-600 hover:text-green-800 font-medium">
              Перейти →
            </router-link>
          </div>

          <!-- Отгрузки -->
          <div class="bg-purple-50 rounded-lg p-6 border border-purple-200">
            <div class="flex items-center mb-4">
              <div class="bg-purple-100 rounded-lg p-3">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
              </div>
              <h3 class="ml-3 text-lg font-semibold text-gray-900">Отгрузки</h3>
            </div>
            <p class="text-gray-600 mb-4">Управление отгрузками товаров</p>
            <router-link to="/sales/shipments" class="text-purple-600 hover:text-purple-800 font-medium">
              Перейти →
            </router-link>
          </div>

          <!-- Возвраты покупателей -->
          <div class="bg-red-50 rounded-lg p-6 border border-red-200">
            <div class="flex items-center mb-4">
              <div class="bg-red-100 rounded-lg p-3">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                </svg>
              </div>
              <h3 class="ml-3 text-lg font-semibold text-gray-900">Возвраты покупателей</h3>
            </div>
            <p class="text-gray-600 mb-4">Обработка возвратов от покупателей</p>
            <router-link to="/sales/customer-returns" class="text-red-600 hover:text-red-800 font-medium">
              Перейти →
            </router-link>
          </div>

          <!-- Счета-фактуры выданные -->
          <div class="bg-indigo-50 rounded-lg p-6 border border-indigo-200">
            <div class="flex items-center mb-4">
              <div class="bg-indigo-100 rounded-lg p-3">
                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
              </div>
              <h3 class="ml-3 text-lg font-semibold text-gray-900">Счета-фактуры выданные</h3>
            </div>
            <p class="text-gray-600 mb-4">Учет выданных счетов-фактур</p>
            <router-link to="/sales/issued-invoices" class="text-indigo-600 hover:text-indigo-800 font-medium">
              Перейти →
            </router-link>
          </div>

          <!-- Прибыльность -->
          <div class="bg-emerald-50 rounded-lg p-6 border border-emerald-200">
            <div class="flex items-center mb-4">
              <div class="bg-emerald-100 rounded-lg p-3">
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
              </div>
              <h3 class="ml-3 text-lg font-semibold text-gray-900">Прибыльность</h3>
            </div>
            <p class="text-gray-600 mb-4">Анализ прибыльности продаж</p>
            <router-link to="/sales/profitability" class="text-emerald-600 hover:text-emerald-800 font-medium">
              Перейти →
            </router-link>
          </div>

          <!-- Товары на реализации -->
          <div class="bg-orange-50 rounded-lg p-6 border border-orange-200">
            <div class="flex items-center mb-4">
              <div class="bg-orange-100 rounded-lg p-3">
                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
              </div>
              <h3 class="ml-3 text-lg font-semibold text-gray-900">Товары на реализации</h3>
            </div>
            <p class="text-gray-600 mb-4">Учет товаров на комиссии</p>
            <router-link to="/sales/consignment-goods" class="text-orange-600 hover:text-orange-800 font-medium">
              Перейти →
            </router-link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import SalesMenu from './SalesMenu.vue'

// Устанавливаем заголовок страницы
document.title = 'Продажи - B2B Storage'
</script> 