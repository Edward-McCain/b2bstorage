import { ref } from 'vue'
import { apiRequest } from '../config/api'

export function useAI() {
  const loading = ref(false)
  const error = ref(null)

  // Methods
  const analyzeStockLevels = async () => {
    loading.value = true
    error.value = null
    
    try {
      const response = await apiRequest('/ai/analyze-stock', {
        method: 'POST'
      })
      
      if (response.success) {
        return response.data
      } else {
        error.value = response.message || 'Ошибка при анализе остатков'
        return null
      }
    } catch (err) {
      error.value = 'Ошибка при анализе остатков'
      console.error('Ошибка при анализе остатков:', err)
      return null
    } finally {
      loading.value = false
    }
  }

  const analyzeDocuments = async () => {
    loading.value = true
    error.value = null
    
    try {
      const response = await apiRequest('/ai/analyze-documents', {
        method: 'POST'
      })
      
      if (response.success) {
        return response.data
      } else {
        error.value = response.message || 'Ошибка при анализе документов'
        return null
      }
    } catch (err) {
      error.value = 'Ошибка при анализе документов'
      console.error('Ошибка при анализе документов:', err)
      return null
    } finally {
      loading.value = false
    }
  }

  const smartSearch = async (query) => {
    loading.value = true
    error.value = null
    
    try {
      const response = await apiRequest('/ai/smart-search', {
        method: 'POST',
        body: JSON.stringify({ query })
      })
      
      if (response.success) {
        return response.data
      } else {
        error.value = response.message || 'Ошибка при умном поиске'
        return null
      }
    } catch (err) {
      error.value = 'Ошибка при умном поиске'
      console.error('Ошибка при умном поиске:', err)
      return null
    } finally {
      loading.value = false
    }
  }

  const forecastStock = async (productId) => {
    loading.value = true
    error.value = null
    
    try {
      const response = await apiRequest('/ai/forecast-stock', {
        method: 'POST',
        body: JSON.stringify({ product_id: productId })
      })
      
      if (response.success) {
        return response.data
      } else {
        error.value = response.message || 'Ошибка при прогнозировании остатков'
        return null
      }
    } catch (err) {
      error.value = 'Ошибка при прогнозировании остатков'
      console.error('Ошибка при прогнозировании остатков:', err)
      return null
    } finally {
      loading.value = false
    }
  }

  const generateRecommendations = async () => {
    loading.value = true
    error.value = null
    
    try {
      const response = await apiRequest('/ai/generate-recommendations', {
        method: 'POST'
      })
      
      if (response.success) {
        return response.data
      } else {
        error.value = response.message || 'Ошибка при генерации рекомендаций'
        return null
      }
    } catch (err) {
      error.value = 'Ошибка при генерации рекомендаций'
      console.error('Ошибка при генерации рекомендаций:', err)
      return null
    } finally {
      loading.value = false
    }
  }

  const comprehensiveAnalysis = async () => {
    loading.value = true
    error.value = null
    
    try {
      const response = await apiRequest('/ai/comprehensive-analysis', {
        method: 'POST'
      })
      
      if (response.success) {
        return response.data
      } else {
        error.value = response.message || 'Ошибка при комплексном анализе'
        return null
      }
    } catch (err) {
      error.value = 'Ошибка при комплексном анализе'
      console.error('Ошибка при комплексном анализе:', err)
      return null
    } finally {
      loading.value = false
    }
  }

  const clearError = () => {
    error.value = null
  }

  return {
    // State
    loading,
    error,
    
    // Methods
    analyzeStockLevels,
    analyzeDocuments,
    smartSearch,
    forecastStock,
    generateRecommendations,
    comprehensiveAnalysis,
    clearError
  }
} 