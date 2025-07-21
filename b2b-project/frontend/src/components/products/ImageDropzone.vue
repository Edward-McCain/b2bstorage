<template>
  <div class="space-y-4">
    <!-- Область загрузки -->
    <div class="relative" @mouseenter="showTooltip = true" @mouseleave="showTooltip = false">
      <form ref="dz" class="dropzone" :action="uploadUrl" style="border: 2px solid rgb(142 177 255);" :class="{ 'pointer-events-none opacity-50': disabled }" />
      <div v-if="disabled && showTooltip" class="absolute inset-0 flex items-center justify-center z-10 pointer-events-none">
        <div class="bg-black/70 text-white text-xs rounded px-4 py-2 shadow-lg">
          Сначала укажите наименование товара
        </div>
      </div>
    </div>

    <!-- Существующие изображения -->
    <div v-if="images.length > 0" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
      <div 
        v-for="image in images" 
        :key="image.id" 
        class="relative group aspect-square bg-gray-100 rounded-lg overflow-hidden border border-gray-200"
      >
        <img 
          :src="imageSrc(image)" 
          :alt="image.alt_text || 'Изображение товара'"
          class="w-full h-full object-cover"
          @error="handleImageError"
        />
        
        <!-- Кнопка удаления -->
        <button 
          @click="deleteImage(image.id)"
          class="absolute top-2 right-2 bg-red-500 hover:bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200"
          title="Удалить изображение"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
        
        <!-- Alt текст -->
        <div v-if="image.alt_text" class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-50 text-white text-xs p-1 truncate">
          {{ image.alt_text }}
        </div>
      </div>
    </div>

    <!-- Сообщение если нет изображений -->
    <div v-else-if="!disabled" class="text-center text-gray-500 text-sm py-4">
      Нет загруженных изображений
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch, computed } from 'vue'
import Dropzone from 'dropzone'
import 'dropzone/dist/dropzone.css'
import { apiConfig } from '@/config/api'

const props = defineProps({
  productId: { type: [String, Number, null], required: false, default: null },
  images: { type: Array, required: true },
  disabled: { type: Boolean, default: false },
})
const emit = defineEmits(['uploaded', 'deleted'])

const dz = ref(null)
const showTooltip = ref(false)

const uploadUrl = computed(() => {
  if (!props.productId || props.productId === 'null' || props.disabled) return ''
  return `${apiConfig.baseURL}/products/${props.productId}/images`
})

function imageSrc(img) {
  // Если URL уже полный (начинается с http), возвращаем как есть
  if (img.image_url && (img.image_url.startsWith('http://') || img.image_url.startsWith('https://'))) {
    return img.image_url
  }
  // Иначе добавляем /storage/
  return `/storage/${img.image_url}`
}

function handleImageError(event) {
  // Заменяем изображение на placeholder при ошибке загрузки
  event.target.src = '/placeholder-image.png'
}

async function deleteImage(id) {
  if (!confirm('Вы уверены, что хотите удалить это изображение?')) {
    return
  }
  
  try {
    const response = await fetch(`${apiConfig.baseURL}/products/images/${id}`, {
      method: 'DELETE',
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
        'Content-Type': 'application/json'
      }
    })
    
    if (response.ok) {
      emit('deleted', id)
    } else {
      const errorData = await response.json()
      alert(errorData.message || 'Ошибка удаления изображения')
    }
  } catch (error) {
    console.error('Error deleting image:', error)
    alert('Ошибка удаления изображения')
  }
}

let dzInstance = null
function initDropzone() {
  if (!dz.value || !uploadUrl.value) return
  Dropzone.autoDiscover = false
  if (dzInstance) {
    dzInstance.destroy()
    dzInstance = null
  }
  dzInstance = new Dropzone(dz.value, {
    url: uploadUrl.value,
    paramName: 'image',
    maxFilesize: 8, // MB
    acceptedFiles: 'image/*',
    headers: {
      Authorization: `Bearer ${localStorage.getItem('auth_token')}`
    },
    addRemoveLinks: false,
    dictDefaultMessage: 'Перетащите изображения сюда или кликните',
    parallelUploads: 5,
    uploadMultiple: false,
    autoProcessQueue: true,
    success: function(file, response) {
      emit('uploaded', response.image)
    },
    error: function(file, errorMessage) {
      dzInstance.removeFile(file)
      alert(errorMessage?.message || 'Ошибка загрузки')
    }
  })
}

onMounted(() => {
  initDropzone()
})

watch([() => props.productId, () => props.disabled], () => {
  // Добавляем небольшую задержку, чтобы избежать множественных вызовов
  setTimeout(() => {
    initDropzone()
  }, 100)
})
</script> 