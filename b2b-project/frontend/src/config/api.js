// Универсальная конфигурация API
// Автоматически определяет домен и использует соответствующий API URL

// Функция для определения API URL на основе текущего домена
function getApiBaseUrl() {
  const hostname = window.location.hostname;
  
  // Продакшн домен
  if (hostname === 'b2bsklad.uz' || hostname === 'www.b2bsklad.uz') {
    return 'https://b2bsklad.uz/api';
  }
  
  // Тестовый домен
  if (hostname === 'b2bstorage.ru' || hostname === 'www.b2bstorage.ru' || hostname === 'api.b2bstorage.ru') {
    return 'https://api.b2bstorage.ru/api';
  }
  
  // Локальная разработка
  if (hostname === 'localhost' || hostname === '127.0.0.1') {
    return 'http://127.0.0.1:8000/api';
  }
  
  // По умолчанию для IP адресов (продакшн)
  if (hostname === '45.92.173.142') {
    return 'https://b2bsklad.uz/api';
  }
  
  // Fallback на продакшн
  return 'https://b2bsklad.uz/api';
}

// Функция для определения storage URL на основе текущего домена
function getStorageBaseUrl() {
  const hostname = window.location.hostname;
  
  // Продакшн домен
  if (hostname === 'b2bsklad.uz' || hostname === 'www.b2bsklad.uz') {
    return 'https://b2bsklad.uz';
  }
  
  // Тестовый домен
  if (hostname === 'b2bstorage.ru' || hostname === 'www.b2bstorage.ru' || hostname === 'api.b2bstorage.ru') {
    return 'https://api.b2bstorage.ru';
  }
  
  // Локальная разработка
  if (hostname === 'localhost' || hostname === '127.0.0.1') {
    return 'http://127.0.0.1:8000';
  }
  
  // По умолчанию для IP адресов (продакшн)
  if (hostname === '45.92.173.142') {
    return 'https://b2bsklad.uz';
  }
  
  // Fallback на продакшн
  return 'https://b2bsklad.uz';
}

// Конфигурация API
const API_CONFIG = {
  baseURL: getApiBaseUrl(),
  storageURL: getStorageBaseUrl(),
  timeout: 10000
}

// Экспортируем конфигурацию
export const apiConfig = API_CONFIG

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

// Для отладки - выводим информацию о текущем домене и API URL
console.log('Current hostname:', window.location.hostname);
console.log('API Base URL:', apiConfig.baseURL);
console.log('Storage Base URL:', apiConfig.storageURL);

const CATEGORIES_KEY = 'categories_cache';
const USER_CATEGORIES_KEY = 'user_categories_cache';
const CATEGORIES_TTL = 24 * 60 * 60 * 1000; // сутки

/**
 * Получить системные категории с кэшированием
 * @returns {Promise<Array>} массив системных категорий
 */
export async function getCategoriesWithCache() {
  const cached = localStorage.getItem(CATEGORIES_KEY);
  if (cached) {
    try {
      const { data, timestamp } = JSON.parse(cached);
      if (Date.now() - timestamp < CATEGORIES_TTL) {
        console.log('Системные категории загружены из кеша');
        return data;
      }
    } catch (e) {
      // ignore parse error
    }
  }
  // Если нет кеша или он устарел — делаем запрос
  console.log('Делаем запрос к API для получения системных категорий...');
  const response = await apiRequest('/categories');
  if (response.ok) {
    console.log('Системные категории получены с сервера:', response.data.data);
    console.log('Sample category from API:', response.data.data[0]);
    
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
  console.log('Ошибка получения системных категорий с сервера');
  return [];
}

// Импортируем утилиты для работы с кэшем
import { getUserCategoriesFromCache, saveUserCategoriesToCache, hasUserCategoriesCache, clearUserCategoriesCache } from '../utils/categoryCacheUtils.js'

/**
 * Получить пользовательские категории с кэшированием
 * @returns {Promise<Array>} массив пользовательских категорий
 */
export async function getUserCategoriesWithCache() {
  // Проверяем кэш из новой утилиты
  const cachedCategories = getUserCategoriesFromCache();
  if (cachedCategories) {
    console.log('Пользовательские категории загружены из кеша');
    return cachedCategories;
  }
  
  // Если нет кеша или он устарел — делаем запрос
  console.log('Делаем запрос к API для получения пользовательских категорий...');
  const response = await apiRequest('/user/categories');
  if (response.ok) {
    console.log('Пользовательские категории получены с сервера:', response.data.data);
    
    // Преобразуем данные для совместимости с системными категориями
    const processedData = response.data.data.map(cat => ({
      id: cat.id,
      category_id: cat.category_id,
      name: cat.name,
      name_ru: cat.name, // Для пользовательских категорий используем name как name_ru
      products_count: cat.products_count,
      subcategories: cat.subcategories || []
    }));
    
    // Сохраняем в кэш через новую утилиту
    saveUserCategoriesToCache(processedData);
    
    return processedData;
  }
  console.log('Ошибка получения пользовательских категорий с сервера');
  return [];
}

/**
 * Получить категории в зависимости от настроек пользователя
 * @returns {Promise<Array>} массив категорий (системных или пользовательских)
 */
export async function getCategoriesByUserSettings() {
  // Импортируем функцию здесь, чтобы избежать циклических зависимостей
  const { getUserCategoryType } = await import('../utils/categoryTypeUtils.js');
  
  const categoryType = getUserCategoryType();
  
  if (categoryType === 'user') {
    return await getUserCategoriesWithCache();
  } else {
    return await getCategoriesWithCache();
  }
}

/**
 * Очистить кэш категорий
 */
export function clearCategoriesCache() {
  localStorage.removeItem(CATEGORIES_KEY);
  localStorage.removeItem(USER_CATEGORIES_KEY);
  
  // Очищаем новый кэш пользовательских категорий
  clearUserCategoriesCache();
  
  console.log('Кэш категорий очищен');
} 