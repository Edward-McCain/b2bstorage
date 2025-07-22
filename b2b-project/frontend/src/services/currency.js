import { apiRequest } from '../config/api.js'

export const currencyService = {
  /**
   * Получить все курсы валют
   */
  async getRates() {
    try {
      const response = await apiRequest('/currencies', { method: 'GET' })
      return response.data
    } catch (error) {
      console.error('Error fetching currency rates:', error)
      throw error
    }
  },

  /**
   * Обновить курсы валют с внешнего API
   */
  async fetchAndSaveRates() {
    try {
      const response = await apiRequest('/currencies/fetch', { method: 'GET' })
      return response
    } catch (error) {
      console.error('Error fetching and saving currency rates:', error)
      throw error
    }
  },

  /**
   * Получить курс валюты по типу
   */
  async getRateByType(currencyType) {
    try {
      const response = await apiRequest(`/currencies/type/${currencyType}`, { method: 'GET' })
      return response.data
    } catch (error) {
      console.error('Error fetching currency rate:', error)
      throw error
    }
  },

  /**
   * Конвертировать сумму между валютами
   */
  async convert(amount, fromCurrency, toCurrency) {
    try {
      const response = await apiRequest('/currencies/convert', {
        method: 'POST',
        body: JSON.stringify({
          amount,
          from_currency: fromCurrency,
          to_currency: toCurrency
        })
      })
      return response.data
    } catch (error) {
      console.error('Error converting currency:', error)
      throw error
    }
  },

  /**
   * Получить валюту пользователя
   */
  async getUserCurrency() {
    try {
      const response = await apiRequest('/user/currency', { method: 'GET' })
      return response.data.currency
    } catch (error) {
      console.error('Error getting user currency:', error)
      throw error
    }
  },

  /**
   * Обновить валюту пользователя
   */
  async updateUserCurrency(currency) {
    try {
      const response = await apiRequest('/user/currency', {
        method: 'PUT',
        body: JSON.stringify({ currency })
      })
      return response.data
    } catch (error) {
      console.error('Error updating user currency:', error)
      throw error
    }
  }
} 