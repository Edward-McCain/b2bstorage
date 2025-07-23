import { ref, computed } from 'vue'
import { apiRequest } from '@/config/api'

export function useWarehouseCheck() {
  const warehouses = ref([])
  const loadingWarehouses = ref(false)
  const showNoWarehousesModal = ref(false)

  // Computed для проверки наличия складов
  const hasWarehouses = computed(() => {
    return Array.isArray(warehouses.value) && warehouses.value.length > 0
  })

  // Загрузка складов
  const loadWarehouses = async () => {
    try {
      loadingWarehouses.value = true
      const response = await apiRequest('/warehouses', { method: 'GET' })
      if (response.ok && response.data.success) {
        warehouses.value = response.data.data || []
        return warehouses.value
      } else {
        warehouses.value = []
        return []
      }
    } catch (error) {
      console.error('Ошибка загрузки складов:', error)
      warehouses.value = []
      return []
    } finally {
      loadingWarehouses.value = false
    }
  }

  // Проверка и показ модального окна если нет складов
  const checkWarehousesAndShowModal = async () => {
    const warehousesList = await loadWarehouses()
    if (warehousesList.length === 0) {
      showNoWarehousesModal.value = true
      return false // нет складов
    }
    return true // есть склады
  }

  // Закрытие модального окна
  const closeNoWarehousesModal = () => {
    showNoWarehousesModal.value = false
  }

  // Опции для селектов
  const warehouseOptions = computed(() => {
    return warehouses.value.map(w => ({
      label: w.name,
      value: w.id
    }))
  })

  return {
    warehouses,
    loadingWarehouses,
    showNoWarehousesModal,
    hasWarehouses,
    warehouseOptions,
    loadWarehouses,
    checkWarehousesAndShowModal,
    closeNoWarehousesModal
  }
} 