/**
 * Утилиты для работы с кэшем пользовательских категорий в localStorage
 */

/**
 * Сохранить пользовательские категории в кэш
 * @param {Array} categories - массив категорий
 */
export function saveUserCategoriesToCache(categories) {
  try {
    const cacheData = {
      categories: categories,
      timestamp: Date.now()
    }
    localStorage.setItem('user_categories_cache', JSON.stringify(cacheData))
    console.log('Пользовательские категории сохранены в кэш:', categories.length)
  } catch (error) {
    console.error('Ошибка сохранения категорий в кэш:', error)
  }
}

/**
 * Получить пользовательские категории из кэша
 * @returns {Array|null} массив категорий или null
 */
export function getUserCategoriesFromCache() {
  try {
    const cached = localStorage.getItem('user_categories_cache')
    if (!cached) return null
    
    const cacheData = JSON.parse(cached)
    
    // Проверяем, что кэш не устарел (24 часа)
    const cacheAge = Date.now() - cacheData.timestamp
    const maxAge = 24 * 60 * 60 * 1000 // 24 часа
    
    if (cacheAge > maxAge) {
      console.log('Кэш категорий устарел, очищаем')
      localStorage.removeItem('user_categories_cache')
      return null
    }
    
    return cacheData.categories
  } catch (error) {
    console.error('Ошибка получения категорий из кэша:', error)
    return null
  }
}

/**
 * Очистить кэш пользовательских категорий
 */
export function clearUserCategoriesCache() {
  try {
    localStorage.removeItem('user_categories_cache')
    console.log('Кэш пользовательских категорий очищен')
  } catch (error) {
    console.error('Ошибка очистки кэша категорий:', error)
  }
}

/**
 * Обновить кэш пользовательских категорий
 * @param {Array} categories - новый массив категорий
 */
export function updateUserCategoriesCache(categories) {
  saveUserCategoriesToCache(categories)
}

/**
 * Проверить, есть ли кэш пользовательских категорий
 * @returns {boolean}
 */
export function hasUserCategoriesCache() {
  try {
    const cached = localStorage.getItem('user_categories_cache')
    if (!cached) return false
    
    const cacheData = JSON.parse(cached)
    const cacheAge = Date.now() - cacheData.timestamp
    const maxAge = 24 * 60 * 60 * 1000 // 24 часа
    
    return cacheAge <= maxAge
  } catch (error) {
    return false
  }
} 