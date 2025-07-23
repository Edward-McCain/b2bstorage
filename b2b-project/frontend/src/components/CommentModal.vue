<template>
  <div v-if="isVisible" class="fixed inset-0 bg-white/90 bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
      <!-- Заголовок -->
      <div class="flex items-center justify-between p-6 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">
          Комментарий к товару
        </h3>
        <button 
          @click="handleClose" 
          class="text-gray-400 hover:text-gray-600 transition-colors"
        >
          <X class="w-5 h-5" />
        </button>
      </div>

      <!-- Содержимое -->
      <div class="p-6">
        <!-- Информация о товаре -->
        <div class="mb-4 p-3 bg-gray-50 rounded-lg">
          <div class="text-sm font-medium text-gray-900">{{ productName }}</div>
          <div class="text-xs text-gray-500 mt-1">
            <span v-if="productArticle">Артикул: {{ productArticle }}</span>
            <span v-if="productArticle && differenceText"> • </span>
            <span :class="differenceClass">{{ differenceText }}</span>
          </div>
        </div>

        <!-- Поле для комментария -->
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-2">
            Комментарий о расхождении
          </label>
          <textarea
            ref="commentInput"
            v-model="tempComment"
            rows="4"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm resize-none"
            placeholder="Опишите причину недостачи или избытка..."
          ></textarea>
        </div>

        <!-- Подсказка -->
        <div class="text-xs text-gray-500 mb-6">
          Комментарий поможет объяснить причину расхождения между расчетным и фактическим остатком.
        </div>
      </div>

      <!-- Кнопки -->
      <div class="flex justify-end gap-3 p-6 border-t border-gray-200">
        <button 
          @click="handleClose" 
          class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors"
        >
          Отмена
        </button>
        <button 
          @click="handleSave" 
          class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors"
        >
          Сохранить
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, nextTick } from 'vue'
import { X } from 'lucide-vue-next'

const props = defineProps({
  isVisible: {
    type: Boolean,
    required: true
  },
  productName: {
    type: String,
    default: ''
  },
  productArticle: {
    type: String,
    default: ''
  },
  differenceText: {
    type: String,
    default: ''
  },
  differenceClass: {
    type: String,
    default: 'text-gray-600'
  },
  initialComment: {
    type: String,
    default: ''
  }
})

const emit = defineEmits(['close', 'save'])

const tempComment = ref('')
const commentInput = ref(null)

// Инициализируем комментарий когда модальное окно открывается
watch(() => props.isVisible, (newValue) => {
  if (newValue) {
    tempComment.value = props.initialComment || ''
    // Фокусируемся на поле ввода
    nextTick(() => {
      if (commentInput.value) {
        commentInput.value.focus()
      }
    })
  }
})

// Инициализируем комментарий при изменении initialComment
watch(() => props.initialComment, (newValue) => {
  if (props.isVisible) {
    tempComment.value = newValue || ''
  }
})

function handleClose() {
  emit('close')
}

function handleSave() {
  emit('save', tempComment.value)
}

// Закрытие по ESC
document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape' && props.isVisible) {
    handleClose()
  }
})
</script>

<style scoped>
/* Стили для плавной анимации */
.fixed {
  animation: fadeIn 0.2s ease-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

.bg-white {
  animation: slideIn 0.2s ease-out;
}

@keyframes slideIn {
  from {
    transform: translateY(-20px);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}
</style> 