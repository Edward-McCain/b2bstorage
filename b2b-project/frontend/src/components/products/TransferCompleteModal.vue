<template>
  <div class="fixed inset-0 bg-white/90 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
      <div class="mt-3">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-medium text-gray-900">{{ t('TransferCompleteModal_1') }}{{ transfer?.id }}</h3> <!-- Выполнение перемещения # -->
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
          <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="text-sm text-blue-800">
              <strong>{{ t('TransferCompleteModal_2') }}</strong> {{ t('TransferCompleteModal_3') }} <!-- Внимание! При выполнении перемещения товары будут физически перемещены между складами. Убедитесь, что все товары готовы к перемещению. -->
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">{{ t('TransferCompleteModal_4') }}</label> <!-- От склада -->
              <div class="text-sm text-gray-900">{{ transfer.from_warehouse?.name }}</div>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">{{ t('TransferCompleteModal_5') }}</label> <!-- В склад -->
              <div class="text-sm text-gray-900">{{ transfer.to_warehouse?.name }}</div>
            </div>
          </div>

          <!-- Позиции для выполнения -->
          <div>
            <h4 class="text-md font-medium text-gray-900 mb-2">{{ t('TransferCompleteModal_6') }}</h4> <!-- Фактические количества -->
            <div class="space-y-3">
              <div
                v-for="position in transfer.positions"
                :key="position.id"
                class="border border-gray-200 rounded-lg p-3"
              >
                <div class="flex justify-between items-center mb-2">
                  <div>
                    <div class="font-medium text-gray-900">{{ position.product?.name }}</div>
                    <div class="text-sm text-gray-500">
                      {{ t('TransferCompleteModal_7') }} {{ position.quantity }} <!-- Планируемое количество: -->
                    </div>
                  </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                      {{ t('TransferCompleteModal_8') }} <!-- Фактическое количество * -->
                    </label>
                    <input
                      v-model.number="position.actual_quantity"
                      type="number"
                      min="0"
                      :max="position.quantity"
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
                      required
                    />
                    <div class="text-xs text-gray-500 mt-1">
                      {{ t('TransferCompleteModal_9') }} {{ position.quantity }} <!-- Максимум: -->
                    </div>
                  </div>
                  <div class="flex items-end">
                    <div class="text-sm text-gray-600">
                      <div v-if="position.actual_quantity !== null">
                        {{ t('TransferCompleteModal_10') }} {{ position.actual_quantity - position.quantity }} <!-- Разница: -->
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="flex justify-end gap-3">
            <button
              @click="$emit('close')"
              class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 text-sm"
            >
              {{ t('TransferCompleteModal_11') }} <!-- Отмена -->
            </button>
            <button
              @click="completeTransfer"
              :disabled="loading || !isFormValid"
              class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg disabled:opacity-50 text-sm"
            >
              {{ loading ? t('TransferCompleteModal_12') : t('TransferCompleteModal_13') }} <!-- Выполнение... : Выполнить перемещение -->
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed } from 'vue'
import api from '@/config/api'
import { t } from '@/locales'

export default {
  name: 'TransferCompleteModal',
  props: {
    transfer: {
      type: Object,
      required: true
    }
  },
  emits: ['close', 'completed'],
  setup(props, { emit }) {
    const loading = ref(false)

    const isFormValid = computed(() => {
      return props.transfer?.positions?.every(position => 
        position.actual_quantity !== null && position.actual_quantity >= 0
      )
    })

    const completeTransfer = async () => {
      if (!isFormValid.value) {
        alert(t('TransferCompleteModal_14')) // Пожалуйста, заполните все поля с фактическими количествами
        return
      }

      loading.value = true
      try {
        const positions = props.transfer.positions.map(pos => ({
          id: pos.id,
          actual_quantity: pos.actual_quantity
        }))

        await api.post(`/transfers/${props.transfer.id}/complete`, {
          positions
        })

        emit('completed')
      } catch (error) {
        console.error(t('TransferCompleteModal_15') + error) // Ошибка выполнения перемещения:
        alert(t('TransferCompleteModal_16')) // Ошибка выполнения перемещения
      } finally {
        loading.value = false
      }
    }

    return {
      loading,
      isFormValid,
      completeTransfer
    }
  }
}
</script> 