<template>
  <!-- Этот компонент только загружает embed скрипт -->
  <!-- Контейнер создается автоматически embed скриптом -->
  <div></div>
</template>

<script setup>
import { onMounted } from 'vue'

onMounted(() => {
  //console.log('newRTC:: WebRTC Integration component mounted')
  
  // Получаем данные пользователя из localStorage
  const userDataStr = localStorage.getItem('user')
  const token = localStorage.getItem('auth_token')
  
  if (!userDataStr) {
    console.error('newRTC:: No user data available in localStorage')
    return
  }
  
  let userData
  try {
    userData = JSON.parse(userDataStr)
    //console.log('newRTC:: User data from localStorage:', userData)
  } catch (error) {
    console.error('newRTC:: Error parsing user data:', error)
    return
  }

  // Загружаем API-First embed скрипт - ВСЯ ЛОГИКА ТЕПЕРЬ В НЕМ!
                    const script = document.createElement('script')
            const version = '20250818-010' // ФИНАЛЬНАЯ ВЕРСИЯ С EMBED РЕЖИМОМ
            script.src = `https://webrtc.b2bsklad.uz/embed/webrtc-embed.js?v=${version}`
  // Определяем userId из разных возможных ключей
  const userId = userData.user_id || userData.id || userData.uuid || null
  const userName = userData.name || (userData.first_name + ' ' + (userData.last_name || ''))
  
  //console.log('newRTC:: Resolved userId:', userId)
  //console.log('newRTC:: Resolved userName:', userName)
  
  if (!userId) {
    console.error('newRTC:: No valid userId found in userData:', userData)
    return
  }
  
  script.setAttribute('data-user-name', userName)
  script.setAttribute('data-user-id', userId)
  script.setAttribute('data-user-avatar', userData.avatar_url ? `https://b2bstorage.ru${userData.avatar_url}` : '')
  script.setAttribute('data-user-token', token || '')
  
  script.onload = () => {
    //console.log('newRTC:: API-First embed script loaded!')
  }

  script.onerror = (error) => {
    console.error('newRTC:: Failed to load API-First embed script:', error)
  }

  document.head.appendChild(script)
})
</script>

<style scoped>
/* Контейнер создается автоматически embed скриптом */
</style>