<template>
  <div class="min-h-screen bg-gray-50" style="padding-top: 66px;">
    <!-- Внутреннее меню навигации -->
    <ProductsMenu />

    <!-- Верхнее меню и фильтры -->
    <div class="bg-white border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-wrap items-center gap-2 py-4">
        <router-link to="/products/create" class="flex items-center gap-1 bg-blue-50 border border-blue-200 text-blue-700 font-medium px-3 py-1.5 rounded text-sm">
          <span class="text-lg">＋</span>Товар
        </router-link>
        <button class="bg-gray-100 border border-gray-200 text-gray-700 font-medium px-3 py-1.5 rounded text-sm">Фильтр</button>
        <input type="text" placeholder="Наименование, код или артикул" class="border border-gray-300 rounded px-3 py-1.5 text-sm w-64" />
        <button class="bg-white border border-gray-300 px-3 py-1.5 rounded font-medium text-sm">Импорт <svg class="h-4 w-4 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg></button>
        <button class="bg-white border border-gray-300 px-3 py-1.5 rounded font-medium text-sm">Экспорт</button>
      </div>
    </div>

    <!-- Центральный контент -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col items-center justify-center min-h-[60vh]">
      <template v-if="products.length === 0">
        <div class="flex flex-col md:flex-row items-center justify-center w-full mt-12 gap-8">
          <div class="flex flex-col items-center md:items-start">
            <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-6 text-center md:text-left">Здесь будут все ваши товары</h1>
            <div class="flex gap-4 mb-6 w-full justify-center md:justify-center">
              <router-link to="/products/create" class="flex items-center gap-2 bg-blue-100 hover:bg-blue-200 text-blue-900 font-semibold px-6 py-3 rounded-lg text-lg transition">
                <span>Добавить товар</span><span class="text-2xl">＋</span>
              </router-link>
            </div>
            <div class="text-gray-600 mb-4 text-center md:text-left">
              Если у вас уже есть каталог товаров, загрузите его из <a href="#" class="text-blue-600 hover:underline">документа Excel</a>.
            </div>
          </div>
        </div>
      </template>
      <template v-else>
        <!-- Таблица товаров -->
        <div class="w-full bg-white rounded-lg shadow-sm border border-gray-200 p-4">
          <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-2 py-2 font-semibold text-left">Наименование</th>
                <th class="px-2 py-2 font-semibold text-left">Код</th>
                <th class="px-2 py-2 font-semibold text-left">Артикул</th>
                <th class="px-2 py-2 font-semibold text-left">Тип</th>
                <th class="px-2 py-2 font-semibold text-left">Цена</th>
                <th class="px-2 py-2 font-semibold text-left">Остаток</th>
                <th class="px-2 py-2 font-semibold text-left">Действия</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="product in products" :key="product.id">
                <td class="px-2 py-2">{{ product.name }}</td>
                <td class="px-2 py-2">{{ product.code }}</td>
                <td class="px-2 py-2">{{ product.sku }}</td>
                <td class="px-2 py-2">{{ product.type }}</td>
                <td class="px-2 py-2">{{ product.price }}</td>
                <td class="px-2 py-2">{{ product.stock }}</td>
                <td class="px-2 py-2">
                  <button class="text-blue-600 hover:underline mr-2">Редактировать</button>
                  <button class="text-red-600 hover:underline">Удалить</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </template>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import ProductsMenu from './ProductsMenu.vue'

// Пример: массив товаров (заменить на реальные данные)
const products = ref([])
// Для теста: раскомментируйте следующую строку чтобы увидеть таблицу
// products.value = [{id:1, name:'Товар 1', code:'001', sku:'A-001', type:'Товар', price:'1000', stock:10}]

onMounted(() => {
  document.title = 'B2B Storage - Товары'
})
</script> 