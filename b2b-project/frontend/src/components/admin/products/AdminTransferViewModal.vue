<template>
  <div class="fixed inset-0 bg-white/90 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
      <div class="mt-3">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-medium text-gray-900">Просмотр перемещения #{{ transfer?.id }}</h3>
          <button
            @click="$emit('close')"
            class="text-gray-400 hover:text-gray-600"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <!-- Индикатор загрузки -->
        <div v-if="loading" class="flex items-center justify-center py-12">
          <div class="text-center">
            <Loader2 class="animate-spin h-8 w-8 text-blue-600 mx-auto mb-4" />
            <p class="text-gray-600 text-sm">Загрузка данных перемещения...</p>
          </div>
        </div>

        <!-- Контент -->
        <div v-else-if="transfer" class="space-y-4">
          <!-- Основная информация -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">От склада</label>
              <div class="text-sm text-gray-900">
                {{ transfer.from_warehouse_name }}
                <div v-if="transfer.from_warehouse_address" class="text-xs text-gray-500">
                  {{ transfer.from_warehouse_address }}
                </div>
              </div>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">В склад</label>
              <div class="text-sm text-gray-900">
                {{ transfer.to_warehouse_name }}
                <div v-if="transfer.to_warehouse_address" class="text-xs text-gray-500">
                  {{ transfer.to_warehouse_address }}
                </div>
              </div>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Дата</label>
              <div class="text-sm text-gray-900">{{ formatDate(transfer.transfer_date) }}</div>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Статус</label>
              <span
                :class="getStatusClass(transfer.status)"
                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
              >
                {{ transfer.status_text }}
              </span>
            </div>
          </div>

          <!-- Примечания -->
          <div v-if="transfer.notes">
            <label class="block text-sm font-medium text-gray-700 mb-1">Примечания</label>
            <div class="text-sm text-gray-900">{{ transfer.notes }}</div>
          </div>

          <!-- Позиции -->
          <div>
            <h4 class="text-md font-medium text-gray-900 mb-2">Товары для перемещения</h4>
            <div class="space-y-2">
              <div
                v-for="position in transfer.positions"
                :key="position.id"
                class="border border-gray-200 rounded-lg p-3"
              >
                <div class="flex justify-between items-start">
                  <div>
                    <div class="font-medium text-gray-900">{{ position.product_name }}</div>
                    <div class="text-sm text-gray-500">
                      Артикул: {{ position.product_sku }}
                    </div>
                    <div class="text-sm text-gray-500">
                      Количество: {{ parseFloat(position.quantity) }}
                      <span v-if="position.actual_quantity !== null">
                        (фактически: {{ parseFloat(position.actual_quantity) }})
                      </span>
                    </div>
                    <div v-if="position.notes" class="text-sm text-gray-500 mt-1">
                      {{ position.notes }}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Информация о пользователях -->
          <div class="border-t pt-4">
            <div class="text-sm text-gray-500">
              Создал: {{ transfer.user?.full_name || transfer.created_by }} {{ formatDateTime(transfer.created_at) }}
            </div>
            <div v-if="transfer.completed_by" class="text-sm text-gray-500 mt-1">
              Выполнил: {{ transfer.completed_by }} {{ formatDateTime(transfer.completed_at) }}
            </div>
            
            <!-- Дополнительная информация о пользователе -->
            <div v-if="transfer.user" class="mt-2 p-3 bg-gray-50 rounded-lg">
              <div class="text-sm font-medium text-gray-700 mb-1">Информация о пользователе:</div>
              <div class="text-xs text-gray-600 space-y-1">
                <div v-if="transfer.user.email">Email: {{ transfer.user.email }}</div>
                <div v-if="transfer.user.company_name">Компания: {{ transfer.user.company_name }}</div>
                <div v-if="transfer.user.inn">ИНН: {{ transfer.user.inn }}</div>
                <div v-if="transfer.user.phone_number">Телефон: {{ transfer.user.phone_number }}</div>
              </div>
            </div>
          </div>
        </div>

        <div class="flex justify-end mt-6">
          <button
            @click="$emit('close')"
            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 text-sm"
          >
            Закрыть
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { defineProps, defineEmits, ref, watch } from 'vue'
import { Loader2 } from 'lucide-vue-next'

const props = defineProps({
  transfer: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['close'])

const loading = ref(true)

// Следим за изменением transfer и сбрасываем loading
watch(() => props.transfer, (newTransfer) => {
  if (newTransfer && Object.keys(newTransfer).length > 0) {
    loading.value = false
  } else {
    loading.value = true
  }
}, { immediate: true })

function formatDate(date) {
  if (!date) return ''
  return new Date(date).toLocaleString('ru-RU', {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const formatDateTime = (date) => {
  if (!date) return ''
  return new Date(date).toLocaleString('ru-RU')
}

const getStatusClass = (status) => {
  const classes = {
    draft: 'bg-gray-100 text-gray-800',
    confirmed: 'bg-blue-100 text-blue-800',
    completed: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}
</script> 