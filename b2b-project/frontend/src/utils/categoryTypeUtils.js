/**
 * Утилиты для работы с типами категорий (системные/пользовательские)
 */

/**
 * Получить тип категорий пользователя из localStorage
 * @returns {string} 'system' или 'user', по умолчанию 'system'
 */
export function getUserCategoryType() {
  try {
    const catsType = localStorage.getItem('user_cats_type')
    return catsType || 'system'
  } catch (error) {
    console.error('Ошибка при получении типа категорий:', error)
    return 'system'
  }
}

/**
 * Проверить, использует ли пользователь пользовательские категории
 * @returns {boolean} true если используются пользовательские категории
 */
export function isUserCategoriesEnabled() {
  return getUserCategoryType() === 'user'
}

/**
 * Проверить, использует ли пользователь системные категории
 * @returns {boolean} true если используются системные категории
 */
export function isSystemCategoriesEnabled() {
  return getUserCategoryType() === 'system'
}

/**
 * Получить правильный API endpoint для категорий в зависимости от типа
 * @param {string} endpoint - базовый endpoint (например, '/categories')
 * @returns {string} полный endpoint с учетом типа категорий
 */
export function getCategoryApiEndpoint(endpoint) {
  const categoryType = getUserCategoryType()
  
  if (categoryType === 'user') {
    // Для пользовательских категорий используем /user/categories
    if (endpoint === '/categories') {
      return '/user/categories'
    }
    if (endpoint.includes('/subcategories')) {
      // Заменяем /categories/{id}/subcategories на /user/categories/{id}/subcategories
      return endpoint.replace('/categories/', '/user/categories/')
    }
  }
  
  // Для системных категорий возвращаем как есть
  return endpoint
}

/**
 * Получить правильный API endpoint для подкатегорий
 * @param {string} categoryId - ID категории
 * @returns {string} полный endpoint для подкатегорий
 */
export function getSubcategoriesApiEndpoint(categoryId) {
  const categoryType = getUserCategoryType()
  
  if (categoryType === 'user') {
    return `/user/categories/${categoryId}/subcategories`
  }
  
  return `/categories/${categoryId}/subcategories`
}

/**
 * Сохранить тип категорий в localStorage
 * @param {string} categoryType - 'system' или 'user'
 */
export function setUserCategoryType(categoryType) {
  try {
    if (categoryType === 'system' || categoryType === 'user') {
      localStorage.setItem('user_cats_type', categoryType)
      console.log('Тип категорий сохранен:', categoryType)
    } else {
      console.error('Неверный тип категорий:', categoryType)
    }
  } catch (error) {
    console.error('Ошибка при сохранении типа категорий:', error)
  }
}

/**
 * Получить название типа категорий для отображения
 * @returns {string} 'Системные категории' или 'Пользовательские категории'
 */
export function getCategoryTypeDisplayName() {
  const categoryType = getUserCategoryType()
  return categoryType === 'user' ? 'Пользовательские категории' : 'Системные категории'
}

/**
 * Проверить, нужно ли показывать категории вообще
 * @returns {boolean} true если категории включены в настройках
 */
export function areCategoriesVisible() {
  try {
    const productFieldsVisibility = JSON.parse(localStorage.getItem('product_fields_visibility') || '{}')
    return productFieldsVisibility.categories !== false
  } catch (error) {
    console.error('Ошибка при проверке видимости категорий:', error)
    return true
  }
}

/**
 * Получить полную информацию о настройках категорий
 * @returns {Object} объект с информацией о настройках категорий
 */
export function getCategorySettings() {
  return {
    type: getUserCategoryType(),
    isUser: isUserCategoriesEnabled(),
    isSystem: isSystemCategoriesEnabled(),
    isVisible: areCategoriesVisible(),
    displayName: getCategoryTypeDisplayName()
  }
} 