<template>
  <div class="space-y-4">
    <!-- Улучшенная область загрузки с интегрированными изображениями -->
    <div class="relative">
      <form 
        ref="dz" 
        class="dropzone-custom" 
        :action="uploadUrl" 
        :class="{ 'pointer-events-none opacity-50': disabled }"
      >
        <!-- Сообщение при отключенном состоянии -->
        <div v-if="disabled" class="dz-message text-center py-8">
          <div class="text-gray-400 mb-2">
            <svg class="w-12 h-12 mx-auto mb-2" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
            </svg>
          </div>
          <div class="text-sm text-gray-500">
            Сначала укажите наименование товара
          </div>
        </div>
        
        <!-- Сообщение при активном состоянии и нет изображений и нет загружающихся файлов -->
        <div v-else-if="images.length === 0 && uploadingFiles.length === 0" class="dz-message text-center py-8">
          <div class="text-blue-500 mb-2">
            <svg class="w-12 h-12 mx-auto mb-2" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
            </svg>
          </div>
          <div class="text-sm text-gray-700 font-medium mb-1">
            Перетащите изображения сюда или кликните для выбора
          </div>
          <div class="text-xs text-gray-500">
            Поддерживаются JPG, PNG, GIF, WEBP до 8 МБ
          </div>
        </div>
        
        <!-- Сетка загруженных изображений и загружающихся файлов внутри dropzone -->
        <div v-else class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 p-4">
          <!-- Загруженные изображения -->
          <div 
            v-for="image in images" 
            :key="image.id"
            :data-image-id="image.id"
            class="relative group aspect-square bg-gray-100 rounded-lg overflow-hidden border-2 border-gray-200 hover:border-blue-300 transition-all duration-300"
          >
            <!-- Изображение -->
            <img 
              :src="imageSrc(image)" 
              :alt="image.alt_text || 'Изображение товара'"
              class="w-full h-full object-cover"
              @error="handleImageError"
            />
            
            <!-- Overlay с кнопкой удаления при наведении -->
            <div class="absolute inset-0 bg-black/90 bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-200 flex items-center justify-center" style="opacity: 0.7;">
              <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                <!-- Кнопка удаления -->
                <button 
                  @click.stop.prevent="deleteImage(image.id)"
                  class="bg-red-500 hover:bg-red-600 text-white rounded-full w-8 h-8 flex items-center justify-center shadow-lg transition-colors"
                  title="Удалить изображение"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                  </svg>
                </button>
              </div>
            </div>
            
            <!-- Overlay для удаления с Loader2 -->
            <div v-if="deletingImages.includes(image.id)" class="absolute inset-0 bg-black bg-opacity-60 flex items-center justify-center">
              <div class="text-center text-white">
                <Loader2 class="w-8 h-8 mx-auto mb-2 animate-spin" />
                <div class="text-sm">
                  Удаление...
                </div>
              </div>
            </div>
            
            <!-- Alt текст -->
            <div v-if="image.alt_text" class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent text-white text-xs p-2 truncate">
              {{ image.alt_text }}
            </div>
          </div>
          
          <!-- Превью загружающихся файлов с прогрессом -->
          <div 
            v-for="upload in uploadingFiles" 
            :key="upload.id"
            class="aspect-square border-2 border-blue-300 rounded-lg bg-blue-50 relative overflow-hidden"
          >
            <!-- Превью изображения (если доступно) -->
            <img 
              v-if="upload.preview" 
              :src="upload.preview" 
              class="w-full h-full object-cover"
              alt="Превью загружаемого изображения"
            />
            
            <!-- Прогресс overlay -->
            <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center">
              <div class="text-center text-white">
                <Loader2 class="w-8 h-8 mx-auto mb-2 animate-spin" />
                <div class="text-sm font-medium mb-1">
                  {{ upload.progress }}%
                </div>
                <div class="text-xs opacity-90 truncate px-2 max-w-full">
                  {{ upload.name }}
                </div>
              </div>
            </div>
            
            <!-- Прогресс-бар внизу -->
            <div class="absolute bottom-0 left-0 w-full h-1 bg-black bg-opacity-20">
              <div 
                class="h-full bg-blue-500 transition-all duration-300"
                :style="{ width: upload.progress + '%' }"
              ></div>
            </div>
          </div>
          
          <!-- Плитка для добавления нового изображения -->
          <div 
            @click="triggerFileSelect"
            class="aspect-square border-2 border-dashed border-gray-300 hover:border-blue-400 rounded-lg flex items-center justify-center cursor-pointer transition-colors bg-gray-50 hover:bg-blue-50"
          >
            <div class="text-center">
              <svg class="w-8 h-8 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
              </svg>
              <div class="text-xs text-gray-500">
                Добавить еще
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch, computed } from 'vue'
import Dropzone from 'dropzone'
import 'dropzone/dist/dropzone.css'
import { apiConfig } from '@/config/api'
import { Loader2 } from 'lucide-vue-next'

const props = defineProps({
  productId: { type: [String, Number, null], required: false, default: null },
  images: { type: Array, required: true },
  disabled: { type: Boolean, default: false },
})
const emit = defineEmits(['uploaded', 'deleted'])

const dz = ref(null)
const uploadingFiles = ref([])
const deletingImages = ref([]) // Массив ID изображений в процессе удаления

const uploadUrl = computed(() => {
  if (!props.productId || props.productId === 'null' || props.disabled) return ''
  return `${apiConfig.baseURL}/products/${props.productId}/images`
})

function imageSrc(img) {
  console.log('Processing image:', img)
  
  // Если URL уже полный (начинается с http), возвращаем как есть
  if (img.image_url && (img.image_url.startsWith('http://') || img.image_url.startsWith('https://'))) {
    console.log('Full URL found:', img.image_url)
    return img.image_url
  }
  
  // Если image_url не указан, используем image_path
  const imagePath = img.image_url || img.image_path
  if (!imagePath) {
    console.log('No image path found')
    return ''
  }
  
  // Убираем ведущий слеш если есть
  let cleanPath = imagePath.startsWith('/') ? imagePath.substring(1) : imagePath
  
  // Формируем полный URL
  let fullUrl
  if (cleanPath.startsWith('storage/')) {
    // Путь уже начинается с storage/
    fullUrl = `${window.location.origin}/${cleanPath}`
  } else {
    // Добавляем storage/ в начало
    fullUrl = `${window.location.origin}/storage/${cleanPath}`
  }
  
  console.log('Generated URL:', fullUrl)
  return fullUrl
}

function handleImageError(event) {
  console.warn('Image failed to load:', event.target.src)
  
  // Заменяем изображение на SVG placeholder при ошибке загрузки
  event.target.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgdmlld0JveD0iMCAwIDIwMCAyMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIyMDAiIGhlaWdodD0iMjAwIiBmaWxsPSIjRjNGNEY2Ii8+CjxwYXRoIGQ9Ik0xMDAgMTMwQzExMC40NTcgMTMwIDExOSAxMjEuNDU3IDExOSAxMTFDMTE5IDEwMC41NDMgMTEwLjQ1NyA5MiAxMDAgOTJDODkuNTQzIDkyIDgxIDEwMC41NDMgODEgMTExQzgxIDEyMS40NTcgODkuNTQzIDEzMCAxMDAgMTMwWiIgZmlsbD0iIzlDQTNBRiIvPgo8cGF0aCBkPSJNMTc1IDEzNUw1OSAxNTVMNDcuNSAxNDAuNUw2MS41IDEyNS41TDg0IDEzNUwxMzMuNSA5Ni41TDE2MCAxMTVMMTc1IDEzNVoiIGZpbGw9IiM5Q0EzQUYiLz4KPC9zdmc+Cg=='
  event.target.parentElement.style.backgroundColor = '#f3f4f6'
  
  // Добавляем индикатор ошибки
  const parent = event.target.parentElement
  if (!parent.querySelector('.error-indicator')) {
    const errorDiv = document.createElement('div')
    errorDiv.className = 'error-indicator absolute top-2 left-2 bg-red-500 text-white text-xs px-2 py-1 rounded'
    errorDiv.textContent = 'Ошибка загрузки'
    parent.appendChild(errorDiv)
  }
}

// Функция для создания превью изображения
function createImagePreview(file) {
  return new Promise((resolve) => {
    if (file.type.startsWith('image/')) {
      const reader = new FileReader()
      reader.onload = (e) => resolve(e.target.result)
      reader.onerror = () => resolve(null)
      reader.readAsDataURL(file)
    } else {
      resolve(null)
    }
  })
}

// Функция для программного открытия диалога выбора файлов
function triggerFileSelect() {
  if (dzInstance && !props.disabled) {
    // Программно открываем диалог выбора файлов
    const hiddenFileInput = dzInstance.hiddenFileInput || dzInstance.element.querySelector('input[type="file"]')
    if (hiddenFileInput) {
      hiddenFileInput.click()
    } else {
      // Если скрытый input не найден, используем метод Dropzone
      if (dzInstance.click) {
        dzInstance.click()
      }
    }
  }
}

async function deleteImage(id) {
  console.log('deleteImage called with ID:', id)
  
  // Проверяем, не удаляется ли уже это изображение
  if (deletingImages.value.includes(id)) {
    console.log('Image with ID', id, 'is already being deleted, skipping')
    return
  }
  
  if (!confirm('Вы уверены, что хотите удалить это изображение?')) {
    return
  }
  
  // Добавляем ID в массив удаляемых изображений
  deletingImages.value.push(id)
  console.log('Added image ID to deleting array:', id, 'Array:', deletingImages.value)
  
  const token = localStorage.getItem('auth_token')
  console.log('Starting DELETE request for image ID:', id, 'Token present:', token ? 'yes' : 'no')
  
  try {
    const response = await fetch(`${apiConfig.baseURL}/products/images/${id}`, {
      method: 'DELETE',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    
    console.log('Delete response status:', response.status)
    
    if (response.ok) {
      console.log('Image deleted successfully')
      // Убираем ID из массива удаляемых изображений
      deletingImages.value = deletingImages.value.filter(imageId => imageId !== id)
      // Эмитим событие для удаления из родительского компонента
      emit('deleted', id)
    } else {
      // Убираем ID из массива удаляемых изображений при ошибке
      deletingImages.value = deletingImages.value.filter(imageId => imageId !== id)
      
      const errorData = await response.json()
      console.error('Delete error response:', errorData)
      
      let message = 'Ошибка удаления изображения'
      if (response.status === 401) {
        message = 'Ошибка авторизации. Пожалуйста, войдите в систему заново.'
      } else if (response.status === 404) {
        message = 'Изображение не найдено'
      } else if (errorData.message || errorData.error) {
        message = errorData.message || errorData.error
      }
      
      alert(message)
    }
  } catch (error) {
    // Убираем ID из массива удаляемых изображений при ошибке
    deletingImages.value = deletingImages.value.filter(imageId => imageId !== id)
    
    console.error('Error deleting image:', error)
    alert('Ошибка удаления изображения: ' + error.message)
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
  
  const token = localStorage.getItem('auth_token')
  console.log('Initializing Dropzone with token:', token ? 'present' : 'missing')
  console.log('Upload URL:', uploadUrl.value)
  
  dzInstance = new Dropzone(dz.value, {
    url: uploadUrl.value,
    paramName: 'image',
    maxFilesize: 8, // MB
    acceptedFiles: 'image/*',
    headers: token ? {
      'Authorization': `Bearer ${token}`,
      'X-Requested-With': 'XMLHttpRequest'
    } : {
      'X-Requested-With': 'XMLHttpRequest'
    },
    addRemoveLinks: false,
    createImageThumbnails: false, // Отключаем стандартные превью
    previewsContainer: false, // Отключаем контейнер превью
    parallelUploads: 5,
    uploadMultiple: false,
    autoProcessQueue: true,
    
    // События загрузки
    sending: async function(file, xhr, formData) {
      console.log('Начинается загрузка файла:', file.name, 'Size:', file.size)
      
      // Создаем превью изображения
      const preview = await createImagePreview(file)
      
      const uploadItem = {
        id: file.name + '_' + Date.now(),
        name: file.name.length > 20 ? file.name.substring(0, 17) + '...' : file.name,
        progress: 0,
        fileName: file.name, // Сохраняем оригинальное имя для поиска
        preview: preview // Добавляем превью
      }
      uploadingFiles.value.push(uploadItem)
      console.log('Added upload item:', uploadItem)
    },
    
    uploadprogress: function(file, progress, bytesSent) {
      console.log('Прогресс загрузки:', file.name, Math.round(progress) + '%')
      const uploadItem = uploadingFiles.value.find(item => item.fileName === file.name)
      if (uploadItem) {
        uploadItem.progress = Math.round(progress)
      } else {
        console.warn('Upload item not found for:', file.name)
      }
    },
    
    success: function(file, response) {
      console.log('Upload success:', response)
      // Удаляем элемент прогресса
      uploadingFiles.value = uploadingFiles.value.filter(item => item.fileName !== file.name)
      
      if (response && response.image) {
        console.log('Emitting uploaded image:', response.image)
        emit('uploaded', response.image)
      } else {
        console.error('Invalid response format:', response)
      }
    },
    
    error: function(file, errorMessage, xhr) {
      console.error('Upload error:', errorMessage, xhr)
      // Удаляем элемент прогресса
      uploadingFiles.value = uploadingFiles.value.filter(item => item.fileName !== file.name)
      
      // Более детальная обработка ошибок
      let message = 'Ошибка загрузки изображения'
      if (xhr && xhr.status === 401) {
        message = 'Ошибка авторизации. Пожалуйста, войдите в систему заново.'
      } else if (xhr && xhr.status === 413) {
        message = 'Файл слишком большой. Максимальный размер: 8 МБ'
      } else if (xhr && xhr.status === 422) {
        message = 'Неподдерживаемый формат файла. Используйте JPG, PNG, GIF или WEBP'
      } else if (errorMessage?.message) {
        message = errorMessage.message
      } else if (typeof errorMessage === 'string') {
        message = errorMessage
      }
      
      alert(message)
    },
    
    complete: function(file) {
      console.log('Upload completed for:', file.name)
      // Удаляем элемент прогресса (на всякий случай)
      uploadingFiles.value = uploadingFiles.value.filter(item => item.fileName !== file.name)
      // Удаляем файл из очереди Dropzone
      dzInstance.removeFile(file)
    }
  })
}

onMounted(() => {
  initDropzone()
})

watch([() => props.productId, () => props.disabled], () => {
  setTimeout(() => {
    initDropzone()
  }, 100)
})
</script>

<style scoped>
/* Кастомные стили для dropzone */
.dropzone-custom {
  min-height: 200px;
  border: 2px dashed #3b82f6;
  border-radius: 12px;
  background: #f8fafc;
  transition: all 0.3s ease;
}

.dropzone-custom:hover {
  border-color: #1d4ed8;
  background: #eff6ff;
}

.dropzone-custom.dz-drag-hover {
  border-color: #1d4ed8;
  background: #dbeafe;
  transform: scale(1.02);
}

/* Скрываем стандартные элементы dropzone */
.dropzone-custom .dz-preview {
  display: none !important;
}

/* Стили для плитки загруженных изображений */
.aspect-square {
  aspect-ratio: 1 / 1;
}

/* Анимация для overlay */
.group:hover .group-hover\:bg-opacity-40 {
  transition: background-color 0.2s ease-in-out;
}



/* Анимации для изображений */
@keyframes fadeInScale {
  0% {
    opacity: 0;
    transform: scale(0.8);
  }
  100% {
    opacity: 1;
    transform: scale(1);
  }
}

.aspect-square {
  animation: fadeInScale 0.3s ease-out;
}


</style> 