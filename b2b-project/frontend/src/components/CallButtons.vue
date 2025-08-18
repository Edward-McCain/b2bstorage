<template>
  <div class="call-buttons">
    <button 
      @click="startVideoCall" 
      :disabled="!isUserOnline || !isWebRTCLoaded"
      class="btn btn-video"
      title="Видео звонок"
    >
      📹
    </button>
    <button 
      @click="startAudioCall" 
      :disabled="!isUserOnline || !isWebRTCLoaded"
      class="btn btn-audio"
      title="Аудио звонок"
    >
      📞
    </button>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const props = defineProps({
  userId: {
    type: String,
    required: true
  }
})

const isUserOnline = ref(false)
const isWebRTCLoaded = ref(false)

const startVideoCall = async () => {
  try {
    await window.WebRTC.videoCall(props.userId)
    console.log('📹 Видео звонок начат')
  } catch (error) {
    console.error('Ошибка видео звонка:', error)
    alert(`Ошибка: ${error.message}`)
  }
}

const startAudioCall = async () => {
  try {
    await window.WebRTC.audioCall(props.userId)
    console.log('📞 Аудио звонок начат')
  } catch (error) {
    console.error('Ошибка аудио звонка:', error)
    alert(`Ошибка: ${error.message}`)
  }
}

onMounted(async () => {
  // Проверяем загрузку WebRTC с таймаутом
  let attempts = 0;
  const maxAttempts = 50; // 5 секунд максимум
  
  const checkWebRTC = () => {
    attempts++;
    
    if (window.WebRTC && typeof window.WebRTC.videoCall === 'function') {
      isWebRTCLoaded.value = true
      checkUserOnline()
      return;
    }
    
    if (attempts >= maxAttempts) {
      console.error('❌ WebRTC SDK не загрузился для CallButtons')
      return;
    }
    
    setTimeout(checkWebRTC, 100)
  }
  
  checkWebRTC()
})

const checkUserOnline = async () => {
  try {
    isUserOnline.value = await window.WebRTC.checkUserOnline(props.userId)
  } catch (error) {
    console.error('Ошибка проверки онлайн статуса:', error)
    isUserOnline.value = false
  }
}
</script>

<style scoped>
.call-buttons {
  display: flex;
  gap: 8px;
}

.btn {
  padding: 8px 12px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 14px;
  font-weight: bold;
  transition: all 0.3s ease;
  min-width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.btn-video {
  background: #ff9800;
  color: white;
}

.btn-video:hover:not(:disabled) {
  background: #f57c00;
}

.btn-audio {
  background: #9c27b0;
  color: white;
}

.btn-audio:hover:not(:disabled) {
  background: #7b1fa2;
}
</style>
