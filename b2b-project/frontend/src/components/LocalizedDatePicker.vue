<template>
  <VueDatePicker 
    :model-value="modelValue"
    :locale="localeCode"
    :enable-time-picker="enableTimePicker"
    :is24="is24"
    :format="dateFormat"
    :preview-format="previewFormat"
    :placeholder="placeholderText"
    :clearable="clearable"
    :auto-apply="autoApply"
    :close-on-scroll="closeOnScroll"
    :close-on-auto-apply="closeOnAutoApply"
    :teleport="true"
    v-bind="$attrs"
    @update:model-value="$emit('update:modelValue', $event)"
    @open="$emit('open')"  
    @close="$emit('close')"
    @focus="$emit('focus')"
    @blur="$emit('blur')"
  />
</template>

<script setup>
import { computed } from 'vue'
import { currentLocale } from '../locales/index.js'
import VueDatePicker from '@vuepic/vue-datepicker'
import '@vuepic/vue-datepicker/dist/main.css'

// Props
const props = defineProps({
  modelValue: {
    type: [String, Date, Array, Object, Number],
    default: null
  },
  enableTimePicker: {
    type: Boolean,
    default: true
  },
  is24: {
    type: Boolean,
    default: true
  },
  clearable: {
    type: Boolean,
    default: true
  },
  autoApply: {
    type: Boolean,
    default: false
  },
  closeOnScroll: {
    type: Boolean,
    default: true
  },
  closeOnAutoApply: {
    type: Boolean,
    default: true
  },
  format: {
    type: String,
    default: null
  },
  previewFormat: {
    type: String,
    default: null
  },
  placeholder: {
    type: String,
    default: null
  }
})

// Events
defineEmits(['update:modelValue', 'open', 'close', 'focus', 'blur'])

// Мапинг языков для Vue Datepicker
const localeMapping = {
  ru: 'ru',
  en: 'en-Us', 
  uz: 'ru', // Для узбекского используем русский как базу
  china: 'zh-CN'
}

// Локализованные тексты
const localizedTexts = computed(() => {
  const texts = {
    ru: {
      placeholder: 'Выберите дату',
      format: 'dd.MM.yyyy HH:mm',
      previewFormat: 'dd.MM.yyyy HH:mm'
    },
    en: {
      placeholder: 'Select date',
      format: 'MM/dd/yyyy hh:mm aa',
      previewFormat: 'MM/dd/yyyy hh:mm aa'
    },
    uz: {
      placeholder: 'Sanani tanlang',
      format: 'dd.MM.yyyy HH:mm',
      previewFormat: 'dd.MM.yyyy HH:mm'
    },
    china: {
      placeholder: '选择日期',
      format: 'yyyy/MM/dd HH:mm',
      previewFormat: 'yyyy/MM/dd HH:mm'
    }
  }
  return texts[currentLocale.value] || texts.ru
})

// Computed свойства
const localeCode = computed(() => {
  return localeMapping[currentLocale.value] || 'ru'
})

const placeholderText = computed(() => {
  return props.placeholder || localizedTexts.value.placeholder
})

const dateFormat = computed(() => {
  if (props.format) return props.format
  return props.enableTimePicker ? localizedTexts.value.format : localizedTexts.value.format.split(' ')[0]
})

const previewFormat = computed(() => {
  if (props.previewFormat) return props.previewFormat
  return props.enableTimePicker ? localizedTexts.value.previewFormat : localizedTexts.value.previewFormat.split(' ')[0]
})
</script>

<style>
/* Глобальные стили для Vue Datepicker с Tailwind */
.dp__input {
  width: 100% !important;
  border: 1px solid #d1d5db !important;
  /* border-radius: 0.5rem !important; */
  padding: 0.5rem 0.75rem !important;
  font-size: 0.875rem !important;
  background-color: white !important;
  color: #374151 !important;
  transition: all 0.2s ease-in-out !important;
  box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
  padding-left: 32px !important;
}

.dp__input:focus {
  outline: none !important;
  border-color: #60a5fa !important;
  box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.1) !important;
}

.dp__input:disabled {
  background-color: #f9fafb !important;
  color: #9ca3af !important;
  cursor: not-allowed !important;
}

.dp__menu {
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
  border: 1px solid #e5e7eb !important;
  /* border-radius: 0.5rem !important; */
}

.dp__menu_wrap {
  z-index: 9999 !important;
}

/* Кнопки в календаре */
.dp__action_buttons {
  gap: 0.5rem !important;
}

.dp__action_button {
  padding: 0.375rem 0.75rem !important;
  font-size: 0.875rem !important;
  border-radius: 0.375rem !important;
  transition: all 0.2s ease-in-out !important;
}

.dp__action_select {
  background-color: #3b82f6 !important;
  color: white !important;
}

.dp__action_select:hover {
  background-color: #2563eb !important;
}

.dp__action_cancel {
  background-color: #f3f4f6 !important;
  color: #374151 !important;
}

.dp__action_cancel:hover {
  background-color: #e5e7eb !important;
}
.dp__range_end, .dp__range_start, .dp__active_date {
  background: #1447e6;
}
</style> 