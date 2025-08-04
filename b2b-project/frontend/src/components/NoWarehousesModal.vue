<template>
  <div v-if="isVisible" class="fixed inset-0 bg-white/90 bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4 shadow-xl">
      <div class="text-center">
        <div class="mb-4">
          <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <Package class="w-8 h-8 text-blue-600" />
          </div>
          <!-- Необходимо добавить склад -->
          <h3 class="text-lg font-semibold text-gray-900 mb-2">
            {{ t('NoWarehousesModal_1') }}
          </h3>
          <!-- Для работы с разделами сначала добавьте ваш склад -->
          <p class="text-gray-600 mb-6">
            {{ t('NoWarehousesModal_2') }}
          </p>
        </div>
        
        <div class="flex flex-col sm:flex-row gap-3">
          <button
            @click="closeModal"
            class="flex-1 bg-gray-200 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-300 transition-colors font-medium"
          >
            <!-- Отмена -->
            {{ t('NoWarehousesModal_3') }}
          </button>
          <button
            @click="goToCreateWarehouse"
            class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors font-medium"
          >
            <!-- Добавить склад -->
            {{ t('NoWarehousesModal_4') }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { Package } from 'lucide-vue-next'
import { t } from '../locales/index.js'

const router = useRouter()

defineProps({
  isVisible: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['close'])

function closeModal() {
  emit('close')
}

function goToCreateWarehouse() {
  emit('close')
  router.push('/warehouses/create')
}
</script> 