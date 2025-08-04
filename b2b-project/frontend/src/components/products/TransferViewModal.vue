<template>
  <div class="fixed inset-0 bg-white/90 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
      <div class="mt-3">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-medium text-gray-900">{{ t('TransferViewModal_1') }}{{ transfer?.id }}</h3> <!-- Просмотр перемещения # -->
          <button
            @click="$emit('close')"
            class="text-gray-400 hover:text-gray-600"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <div v-if="transfer" class="space-y-4">
          <!-- Основная информация -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">{{ t('TransferViewModal_2') }}</label> <!-- От склада -->
              <div class="text-sm text-gray-900">{{ transfer.from_warehouse?.name }}</div>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">{{ t('TransferViewModal_3') }}</label> <!-- В склад -->
              <div class="text-sm text-gray-900">{{ transfer.to_warehouse?.name }}</div>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">{{ t('TransferViewModal_4') }}</label> <!-- Дата -->
              <div class="text-sm text-gray-900">{{ formatDate(transfer.transfer_date) }}</div>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">{{ t('TransferViewModal_5') }}</label> <!-- Статус -->
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
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ t('TransferViewModal_6') }}</label> <!-- Примечания -->
            <div class="text-sm text-gray-900">{{ transfer.notes }}</div>
          </div>

          <!-- Позиции -->
          <div>
            <h4 class="text-md font-medium text-gray-900 mb-2">{{ t('TransferViewModal_7') }}</h4> <!-- Товары для перемещения -->
            <div class="space-y-2">
              <div
                v-for="position in transfer.positions"
                :key="position.id"
                class="border border-gray-200 rounded-lg p-3"
              >
                <div class="flex justify-between items-start">
                  <div>
                    <div class="font-medium text-gray-900">{{ position.product?.name }}</div>
                    <div class="text-sm text-gray-500">
                      {{ t('TransferViewModal_8') }} {{ parseFloat(position.quantity) }} <!-- Количество: -->
                      <span v-if="position.actual_quantity !== null">
                        ({{ t('TransferViewModal_9') }} {{ parseFloat(position.actual_quantity) }}) <!-- фактически: -->
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

          <!-- Информация о создании -->
          <div class="border-t pt-4">
            <div class="text-sm text-gray-500">
              {{ t('TransferViewModal_10') }} {{ transfer.created_by_user?.name }} {{ formatDateTime(transfer.created_at) }} <!-- Создал: -->
            </div>
            <div v-if="transfer.completed_by_user" class="text-sm text-gray-500 mt-1">
              {{ t('TransferViewModal_11') }} {{ transfer.completed_by_user?.name }} {{ formatDateTime(transfer.completed_at) }} <!-- Выполнил: -->
            </div>
          </div>
        </div>

        <div class="flex justify-end mt-6">
          <button
            @click="$emit('close')"
            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 text-sm"
          >
            {{ t('TransferViewModal_12') }} <!-- Закрыть -->
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { t } from '@/locales'

export default {
  name: 'TransferViewModal',
  props: {
    transfer: {
      type: Object,
      required: true
    }
  },
  emits: ['close'],
  setup() {
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

    return {
      formatDate,
      formatDateTime,
      getStatusClass,
      t
    }
  }
}
</script> 