import { ref, computed } from 'vue'
import { apiRequest } from '../config/api'

export function useNotifications() {
  const notifications = ref([])
  const loading = ref(false)
  const unreadCount = ref(0)
  const filters = ref({
    type: '',
    isRead: ''
  })

  // Computed
  const hasUnreadNotifications = computed(() => unreadCount.value > 0)
  const filteredNotifications = computed(() => {
    let filtered = notifications.value

    if (filters.value.type) {
      filtered = filtered.filter(n => n.type === filters.value.type)
    }

    if (filters.value.isRead !== '') {
      const isRead = filters.value.isRead === 'true'
      filtered = filtered.filter(n => n.is_read === isRead)
    }

    return filtered
  })

  // Methods
  const loadNotifications = async () => {
    loading.value = true
    try {
      const params = new URLSearchParams()
      if (filters.value.type) params.append('type', filters.value.type)
      if (filters.value.isRead !== '') params.append('is_read', filters.value.isRead)
      
      const response = await apiRequest(`/notifications?${params.toString()}`)
      if (response.success) {
        notifications.value = response.data
        unreadCount.value = response.unread_count || 0
      }
    } catch (error) {
      console.error('Ошибка при загрузке уведомлений:', error)
    } finally {
      loading.value = false
    }
  }

  const loadUnreadNotifications = async () => {
    try {
      const response = await apiRequest('/notifications/unread')
      if (response.success) {
        return response.data
      }
    } catch (error) {
      console.error('Ошибка при загрузке непрочитанных уведомлений:', error)
    }
    return []
  }

  const markAsRead = async (id) => {
    try {
      const response = await apiRequest(`/notifications/${id}/mark-read`, {
        method: 'PUT'
      })
      if (response.success) {
        const notification = notifications.value.find(n => n.id === id)
        if (notification) {
          notification.is_read = true
          unreadCount.value = Math.max(0, unreadCount.value - 1)
        }
        return true
      }
    } catch (error) {
      console.error('Ошибка при отметке уведомления:', error)
    }
    return false
  }

  const markAllAsRead = async () => {
    loading.value = true
    try {
      const response = await apiRequest('/notifications/mark-all-read', {
        method: 'PUT'
      })
      if (response.success) {
        notifications.value.forEach(n => n.is_read = true)
        unreadCount.value = 0
        return true
      }
    } catch (error) {
      console.error('Ошибка при отметке всех уведомлений:', error)
    } finally {
      loading.value = false
    }
    return false
  }

  const deleteNotification = async (id) => {
    try {
      const response = await apiRequest(`/notifications/${id}`, {
        method: 'DELETE'
      })
      if (response.success) {
        const index = notifications.value.findIndex(n => n.id === id)
        if (index > -1) {
          const notification = notifications.value[index]
          notifications.value.splice(index, 1)
          if (!notification.is_read) {
            unreadCount.value = Math.max(0, unreadCount.value - 1)
          }
        }
        return true
      }
    } catch (error) {
      console.error('Ошибка при удалении уведомления:', error)
    }
    return false
  }

  const getUnreadCount = async () => {
    try {
      const response = await apiRequest('/notifications/unread-count')
      if (response.success) {
        unreadCount.value = response.count
        return response.count
      }
    } catch (error) {
      console.error('Ошибка при получении количества непрочитанных уведомлений:', error)
    }
    return 0
  }

  const setFilters = (newFilters) => {
    filters.value = { ...filters.value, ...newFilters }
  }

  const clearFilters = () => {
    filters.value = {
      type: '',
      isRead: ''
    }
  }

  return {
    // State
    notifications,
    loading,
    unreadCount,
    filters,
    
    // Computed
    hasUnreadNotifications,
    filteredNotifications,
    
    // Methods
    loadNotifications,
    loadUnreadNotifications,
    markAsRead,
    markAllAsRead,
    deleteNotification,
    getUnreadCount,
    setFilters,
    clearFilters
  }
} 