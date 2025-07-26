<template>
  <div class="bg-white rounded-lg shadow p-4 mb-4 sm:mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center gap-4">
      <div class="flex-1 min-w-0">
        <label class="block text-sm font-medium text-gray-700 mb-1">Тип уведомления</label>
        <Multiselect
          v-model="localFilters.type"
          :options="[
            { label: 'Все типы', value: '' },
            { label: 'Информация', value: 'info' },
            { label: 'Предупреждение', value: 'warning' },
            { label: 'Рекомендация', value: 'recommendation' },
            { label: 'Низкие остатки', value: 'low_stock' },
            { label: 'Просроченные документы', value: 'overdue' }
          ]"
          label="label"
          value="value"
          :object="true"
          placeholder="Все типы"
          :max-height="400"
          class="w-full text-sm multiselect-custom"
          @change="updateFilters"
        />
      </div>
      
      <div class="flex-1 min-w-0">
        <label class="block text-sm font-medium text-gray-700 mb-1">Статус</label>
        <Multiselect
          v-model="localFilters.isRead"
          :options="[
            { label: 'Все', value: '' },
            { label: 'Непрочитанные', value: 'false' },
            { label: 'Прочитанные', value: 'true' }
          ]"
          label="label"
          value="value"
          :object="true"
          placeholder="Все"
          :max-height="400"
          class="w-full text-sm multiselect-custom"
          @change="updateFilters"
        />
      </div>
      
      <div class="flex-1 min-w-0">
        <label class="block text-sm font-medium text-gray-700 mb-1">Лимит</label>
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
          Всего: {{ totalCount }} | Непрочитанных: {{ unreadCount }}
        </span>
        <button
          @click="clearFilters"
          class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
          <X class="h-4 w-4 mr-2" />
          <span class="hidden sm:inline">Очистить</span>
          <span class="sm:hidden">Очистить</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import { X } from 'lucide-vue-next'
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