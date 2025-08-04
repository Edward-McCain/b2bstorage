import { currentLocale } from '../locales/index.js'

// Мапинг языков для локализации дат
const localeMapping = {
  ru: 'ru-RU',
  en: 'en-US', 
  uz: 'uz-UZ',
  china: 'zh-CN'
}

/**
 * Форматирует дату согласно текущему языку пользователя
 * @param {string|Date} date - Дата для форматирования
 * @param {object} options - Опции форматирования (необязательно)
 * @returns {string} Отформатированная дата
 */
export function formatDate(date, options = {}) {
  if (!date) return ''
  
  const locale = localeMapping[currentLocale.value] || 'ru-RU'
  return new Date(date).toLocaleString(locale, options)
}

/**
 * Форматирует дату и время полностью
 * @param {string|Date} date - Дата для форматирования  
 * @returns {string} Отформатированная дата и время
 */
export function formatDateTime(date) {
  return formatDate(date, {
    year: 'numeric',
    month: '2-digit', 
    day: '2-digit',
    hour: '2-digit',
    minute: '2-digit'
  })
}

/**
 * Форматирует только дату (без времени)
 * @param {string|Date} date - Дата для форматирования
 * @returns {string} Отформатированная дата
 */
export function formatDateOnly(date) {
  return formatDate(date, {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit'
  })
} 