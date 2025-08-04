<template>
  <div class="bg-white rounded-lg shadow p-4 mb-4 sm:mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center gap-4">
      <div class="flex-1 min-w-0">
        <!-- Тип уведомления -->
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ t('NotificationFilters_1') }}</label>
        <Multiselect
          v-model="localFilters.type"
          :options="[
            { label: t('NotificationFilters_2'), value: '' },
            { label: t('NotificationFilters_3'), value: 'info' },
            { label: t('NotificationFilters_4'), value: 'warning' },
            { label: t('NotificationFilters_5'), value: 'recommendation' },
            { label: t('NotificationFilters_6'), value: 'low_stock' },
            { label: t('NotificationFilters_7'), value: 'overdue' }
          ]"
          label="label"
          value="value"
          :object="true"
          :placeholder="t('NotificationFilters_2')"
          :max-height="400"
          class="w-full text-sm multiselect-custom"
          @change="updateFilters"
        />
      </div>
      
      <div class="flex-1 min-w-0">
        <!-- Статус -->
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ t('NotificationFilters_8') }}</label>
        <Multiselect
          v-model="localFilters.isRead"
          :options="[
            { label: t('NotificationFilters_9'), value: '' },
            { label: t('NotificationFilters_10'), value: 'false' },
            { label: t('NotificationFilters_11'), value: 'true' }
          ]"
          label="label"
          value="value"
          :object="true"
          :placeholder="t('NotificationFilters_9')"
          :max-height="400"
          class="w-full text-sm multiselect-custom"
          @change="updateFilters"
        />
      </div>
      
      <div class="flex-1 min-w-0">
        <!-- Лимит -->
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ t('NotificationFilters_12') }}</label>
        <input
          v-model="localFilters.limit"
          type="number"
          min="1"
          max="100"
          @change="updateFilters"
          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
          placeholder="50"
        >
      </div>
      
      <div class="flex items-center justify-between sm:justify-end gap-3">
        <span v-if="totalCount > 0" class="text-sm text-gray-500">
          <!-- Всего: / Непрочитанных: -->
          {{ t('NotificationFilters_13') }} {{ totalCount }} | {{ t('NotificationFilters_14') }} {{ unreadCount }}
        </span>
        <button
          @click="clearFilters"
          class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
          <X class="h-4 w-4 mr-2" />
          <!-- Очистить -->
          <span class="hidden sm:inline">{{ t('NotificationFilters_15') }}</span>
          <span class="sm:hidden">{{ t('NotificationFilters_15') }}</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import { X } from 'lucide-vue-next'
import { t } from '../locales/index.js'
import Multiselect from '@vueform/multiselect'

// Props
const props = defineProps({
  filters: {
    type: Object,
    required: true
  },
  totalCount: {
    type: Number,
    default: 0
  },
  unreadCount: {
    type: Number,
    default: 0
  }
})

// Emits
const emit = defineEmits(['update:filters'])

// Local state
const localFilters = ref({
  type: props.filters.type || '',
  isRead: props.filters.isRead || '',
  limit: props.filters.limit || 50
})

// Watch for prop changes
watch(() => props.filters, (newFilters) => {
  localFilters.value = {
    type: newFilters.type || '',
    isRead: newFilters.isRead || '',
    limit: newFilters.limit || 50
  }
}, { deep: true })

// Methods
const updateFilters = () => {
  emit('update:filters', { ...localFilters.value })
}

const clearFilters = () => {
  localFilters.value = {
    type: '',
    isRead: '',
    limit: 50
  }
  updateFilters()
}
</script> 