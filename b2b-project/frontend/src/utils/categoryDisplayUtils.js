import { currentLocale } from '../locales/index.js'
import { computed } from 'vue'

/**
 * Маппинг языков к полям в базе данных
 */
const languageFieldMapping = {
  'ru': 'name',
  'en': 'name_en', 
  'uz': 'name_uz',
  'china': 'name_china',
  'zh-CN': 'name_china' // Добавляем поддержку zh-CN для совместимости
}



/**
 * Получить правильное название категории в зависимости от текущего языка
 * @param {Object} category - объект категории
 * @returns {string} название категории на нужном языке
 */
export function getCategoryDisplayName(category) {
  if (!category) return ''
  
  console.log('=== Category Display Debug ===')
  console.log('Current locale:', currentLocale.value)
  console.log('Category object:', category)
  console.log('Available fields:', Object.keys(category))
  console.log('Language field mapping keys:', Object.keys(languageFieldMapping))
  
  // Получаем правильное поле для текущего языка
  const languageField = languageFieldMapping[currentLocale.value] || 'name'
  console.log('Selected language field:', languageField)
  console.log('Field value:', category[languageField])
  console.log('Original name field:', category.name)
  
  // Проверяем, есть ли поле для данного языка
  if (category[languageField] && category[languageField].trim()) {
    console.log('Using translated field:', category[languageField])
    return category[languageField]
  }
  
  // Если нет перевода для выбранного языка, возвращаем базовое название
  console.log('Using fallback name:', category.name)
  return category.name || ''
}

/**
 * Получить правильное название подкатегории в зависимости от текущего языка
 * @param {Object} subcategory - объект подкатегории
 * @returns {string} название подкатегории на нужном языке
 */
export function getSubcategoryDisplayName(subcategory) {
  if (!subcategory) return ''
  
  // Получаем правильное поле для текущего языка
  const languageField = languageFieldMapping[currentLocale.value] || 'name'
  
  // Проверяем, есть ли поле для данного языка
  if (subcategory[languageField] && subcategory[languageField].trim()) {
    return subcategory[languageField]
  }
  
  // Если нет перевода для выбранного языка, возвращаем базовое название
  return subcategory.name || ''
}

/**
 * Преобразовать массив категорий в опции для селекта с правильными названиями
 * @param {Array} categories - массив категорий
 * @returns {Array} массив опций для селекта
 */
export function transformCategoriesToOptions(categories) {
  if (!Array.isArray(categories)) return []
  
  return categories.map(category => ({
    label: getCategoryDisplayName(category),
    value: category.category_id,
    raw: category
  }))
}

/**
 * Преобразовать массив подкатегорий в опции для селекта с правильными названиями
 * @param {Array} subcategories - массив подкатегорий
 * @returns {Array} массив опций для селекта
 */
export function transformSubcategoriesToOptions(subcategories) {
  if (!Array.isArray(subcategories)) return []
  
  return subcategories.map(subcategory => ({
    label: getSubcategoryDisplayName(subcategory),
    value: subcategory.subcategory_id,
    raw: subcategory
  }))
}

/**
 * Создать реактивную функцию для преобразования категорий в опции
 * @param {Array} categories - массив категорий
 * @returns {ComputedRef} реактивная функция
 */
export function createReactiveCategoryOptions(categories) {
  return computed(() => transformCategoriesToOptions(categories.value || []))
}

/**
 * Создать реактивную функцию для преобразования подкатегорий в опции
 * @param {Array} subcategories - массив подкатегорий
 * @returns {ComputedRef} реактивная функция
 */
export function createReactiveSubcategoryOptions(subcategories) {
  return computed(() => transformSubcategoriesToOptions(subcategories.value || []))
} 