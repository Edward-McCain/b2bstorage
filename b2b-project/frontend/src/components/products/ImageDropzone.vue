<template>
  <div class="relative" @mouseenter="showTooltip = true" @mouseleave="showTooltip = false">
    <form ref="dz" class="dropzone" :action="uploadUrl" style="border: 2px solid rgb(142 177 255);" :class="{ 'pointer-events-none opacity-50': disabled }" />
    <div v-if="disabled && showTooltip" class="absolute inset-0 flex items-center justify-center z-10 pointer-events-none">
      <div class="bg-black/70 text-white text-xs rounded px-4 py-2 shadow-lg">
        Сначала укажите наименование товара
      </div>
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
  return `/storage/${img.image_url}`
}

function deleteImage(id) {
  emit('deleted', id)
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
  initDropzone()
})
</script> 