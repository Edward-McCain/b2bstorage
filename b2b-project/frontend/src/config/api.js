// Конфигурация API
const API_CONFIG = {
  // Локальная разработка
  development: {
    baseURL: 'http://127.0.0.1:8000/api',
    storageURL: 'http://127.0.0.1:8000',
    timeout: 10000
  },
  // Продакшн
  production: {
    baseURL: 'https://api.b2bstorage.ru/api',
    storageURL: 'https://api.b2bstorage.ru',
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

// Функция для получения URL файла (аватара, изображений и т.д.)
export const getFileUrl = (filePath) => {
  // console.log('getFileUrl вызвана с:', filePath)
  
  if (!filePath) {
    // console.log('filePath пустой, возвращаем null')
    return null
  }
  
  // Если уже полный URL, возвращаем как есть
  if (filePath.startsWith('http://') || filePath.startsWith('https://')) {
    // console.log('Полный URL, возвращаем как есть:', filePath)
    return filePath
  }
  
  // Если относительный путь, добавляем базовый URL
  if (filePath.startsWith('/')) {
    const result = `${apiConfig.storageURL}${filePath}`
    // console.log('Относительный путь, результат:', result)
    return result
  }
  
  // Если путь без слеша, добавляем /storage/
  const result = `${apiConfig.storageURL}/storage/${filePath}`
  // console.log('Путь без слеша, результат:', result)
  return result
}

// Функция для выполнения API запросов
export const apiRequest = async (endpoint, options = {}) => {
  const url = getApiUrl(endpoint)
  
  const defaultOptions = {
    headers: {
      'Accept': 'application/json',
      ...options.headers
    },
    timeout: apiConfig.timeout,
    ...options
  }

  // Добавляем Content-Type только если это не FormData
  if (!(options.body instanceof FormData)) {
    defaultOptions.headers['Content-Type'] = 'application/json'
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

// Создаем объект API с методами для удобства
const api = {
  async get(endpoint, options = {}) {
    const response = await apiRequest(endpoint, { ...options, method: 'GET' })
    if (!response.ok) {
      throw new Error(`HTTP ${response.status}: ${response.data.message || 'Request failed'}`)
    }
    return response
  },

  async post(endpoint, data = {}, options = {}) {
    const response = await apiRequest(endpoint, {
      ...options,
      method: 'POST',
      body: JSON.stringify(data)
    })
    if (!response.ok) {
      throw new Error(`HTTP ${response.status}: ${response.data.message || 'Request failed'}`)
    }
    return response
  },

  async put(endpoint, data = {}, options = {}) {
    const response = await apiRequest(endpoint, {
      ...options,
      method: 'PUT',
      body: JSON.stringify(data)
    })
    if (!response.ok) {
      throw new Error(`HTTP ${response.status}: ${response.data.message || 'Request failed'}`)
    }
    return response
  },

  async delete(endpoint, options = {}) {
    const response = await apiRequest(endpoint, { ...options, method: 'DELETE' })
    if (!response.ok) {
      throw new Error(`HTTP ${response.status}: ${response.data.message || 'Request failed'}`)
    }
    return response
  }
}

// Экспортируем по умолчанию
export default api

const CATEGORIES_KEY = 'categories_cache';
const CATEGORIES_TTL = 24 * 60 * 60 * 1000; // сутки

export async function getCategoriesWithCache() {
  const cached = localStorage.getItem(CATEGORIES_KEY);
  if (cached) {
    try {
      const { data, timestamp } = JSON.parse(cached);
      if (Date.now() - timestamp < CATEGORIES_TTL) {
        console.log('Категории загружены из кеша');
        return data;
      }
    } catch (e) {
      // ignore parse error
    }
  }
  // Если нет кеша или он устарел — делаем запрос
  console.log('Делаем запрос к API для получения категорий...');
  const response = await apiRequest('/categories');
  if (response.ok) {
    console.log('Категории получены с сервера:', response.data.data);
    
    // Преобразуем данные для совместимости
    const processedData = response.data.data.map(cat => ({
      ...cat,
      name_ru: cat.name || cat.name_ru // Используем name как name_ru для совместимости
    }));
    
    localStorage.setItem(CATEGORIES_KEY, JSON.stringify({
      data: processedData,
      timestamp: Date.now()
    }));
    return processedData;
  }
  console.log('Ошибка получения категорий с сервера');
  return [];
} 