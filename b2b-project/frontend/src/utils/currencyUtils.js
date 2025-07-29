import { apiRequest } from '@/config/api'

/**
 * Получает валюту пользователя из localStorage или API
 * @returns {Promise<string>} Валюта пользователя
 */
export async function getUserCurrency() {
  // Сначала пробуем получить из localStorage
  const savedCurrency = localStorage.getItem('userCurrency')
  
  if (savedCurrency) {
    return savedCurrency
  }
  
  // Если в localStorage нет, запрашиваем с сервера
  try {
    const response = await apiRequest('/me', { method: 'GET' })
    if (response.ok && response.data && response.data.data && response.data.data.currency) {
      const currency = response.data.data.currency
      // Сохраняем в localStorage
      localStorage.setItem('userCurrency', currency)
      return currency
    }
  } catch (error) {
    console.error('Ошибка получения валюты пользователя:', error)
  }
  
  // Возвращаем значение по умолчанию
  return 'UZS'
}

/**
 * Обновляет валюту пользователя с сервера
 * @returns {Promise<string>} Обновленная валюта пользователя
 */
export async function updateUserCurrency() {
  try {
    const response = await apiRequest('/me', { method: 'GET' })
    if (response.ok && response.data && response.data.data && response.data.data.currency) {
      const currency = response.data.data.currency
      // Сохраняем в localStorage
      localStorage.setItem('userCurrency', currency)
      return currency
    }
  } catch (error) {
    console.error('Ошибка обновления валюты пользователя:', error)
  }
  
  // Возвращаем значение по умолчанию
  return 'UZS'
}

/**
 * Получает валюту пользователя из localStorage (синхронно)
 * @returns {string} Валюта пользователя
 */
export function getStoredUserCurrency() {
  return localStorage.getItem('userCurrency') || 'UZS'
} 