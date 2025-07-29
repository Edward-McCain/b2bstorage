/**
 * Проверяет, включены ли категории товаров для пользователя
 * @returns {boolean} true если категории включены, false если выключены
 */
export function areCategoriesEnabled() {
  try {
    const productFieldsVisibility = JSON.parse(localStorage.getItem('product_fields_visibility') || '{}')
    // Проверяем явно на false, так как undefined/null должны возвращать true
    return productFieldsVisibility.categories !== false
  } catch (error) {
    console.error('Ошибка при проверке настроек категорий:', error)
    return true
  }
}

/**
 * Проверяет, является ли поле обязательным для пользователя
 * @param {string} fieldKey - Ключ поля (например, 'category', 'subcategory', 'price')
 * @returns {boolean} true если поле обязательное, false если необязательное
 */
export function isFieldRequired(fieldKey) {
  // Категории и подкатегории управляются одним переключателем
  if (fieldKey === 'category' || fieldKey === 'subcategory') {
    return areCategoriesEnabled()
  }
  
  // Для остальных полей проверяем настройки видимости
  try {
    const productFieldsVisibility = JSON.parse(localStorage.getItem('product_fields_visibility') || '{}')
    // Проверяем явно на false, так как undefined/null должны возвращать true
    return productFieldsVisibility[fieldKey] !== false
  } catch (error) {
    console.error('Ошибка при проверке обязательности поля:', error)
    return true
  }
}

/**
 * Получает настройки видимости полей товаров
 * @returns {Object} Объект с настройками видимости полей
 */
export function getProductFieldsVisibility() {
  try {
    const savedVisibility = localStorage.getItem('product_fields_visibility')
    if (savedVisibility) {
      const visibility = JSON.parse(savedVisibility)
      
      // Применяем настройки категорий
      const categoriesEnabled = areCategoriesEnabled()
      visibility.category = categoriesEnabled
      visibility.subcategory = categoriesEnabled
      
      return visibility
    }
    
    // Возвращаем настройки по умолчанию
    return {
      categories: true,
      category: true,
      subcategory: true,
      description: true,
      country: true,
      supplier: true,
      article: true,
      code: true,
      external_code: true,
      weight: true,
      volume: true,
      vat: true,
      min_stock: true,
      stock_type: true,
      packing: true,
      accounting_type: true,
      traceable: true,
      marking: true,
      product_type: true,
      barcode_type: true,
      barcode: true,
      cash_register_tax: true,
      cash_register_type: true,
      price: true
    }
  } catch (error) {
    console.error('Ошибка при получении настроек полей товаров:', error)
    return {}
  }
} 