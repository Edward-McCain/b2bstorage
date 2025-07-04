// Конфигурация API
const API_CONFIG = {
  // Локальная разработка
  development: {
    baseURL: 'https://api.b2bstorage.ru/api',
    timeout: 10000
  },
  // Продакшн
  production: {
    baseURL: 'https://api.b2bstorage.ru/api',
    timeout: 10000
  }
}

// Определяем окружение
const isDevelopment = import.meta.env.DEV || window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1'

// Экспортируем конфигурацию для текущего окружения
export const apiConfig = isDevelopment ? API_CONFIG.development : API_CONFIG.production

// Функция для получения полного URL
export const getApiUrl = (endpoint) => {
  return `${apiConfig.baseURL}${endpoint}`
}

// Функция для выполнения API запросов
export const apiRequest = async (endpoint, options = {}) => {
  const url = getApiUrl(endpoint)
  
  const defaultOptions = {
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      ...options.headers
    },
    timeout: apiConfig.timeout,
    ...options
  }

  // Добавляем токен авторизации, если он есть
  const token = localStorage.getItem('auth_token')
  if (token) {
    defaultOptions.headers['Authorization'] = `Bearer ${token}`
  }

  try {
    const response = await fetch(url, defaultOptions)
    const data = await response.json()
    
    return {
      ok: response.ok,
      status: response.status,
      data,
      headers: response.headers
    }
  } catch (error) {
    console.error('API request error:', error)
    throw error
  }
} 