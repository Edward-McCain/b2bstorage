import { ref, computed } from 'vue'
import ru from './ru.js'
import en from './en.js'
import uz from './uz.js'
import china from './china.js'

// Основной файл локализации
export const locales = {
  ru,
  en,
  uz,
  china
}

// Реактивный текущий язык (по умолчанию русский)
export const currentLocale = ref('ru')

// Функция для получения перевода (реактивная)
export function t(key) {
  return locales[currentLocale.value]?.[key] || key
}

// Функция для смены языка (реактивная)
export function setLocale(locale) {
  console.log('=== setLocale called ===')
  console.log('Setting locale to:', locale)
  console.log('Available locales:', Object.keys(locales))
  if (locales[locale]) {
    currentLocale.value = locale
    console.log('Locale set successfully to:', locale)
    console.log('currentLocale.value is now:', currentLocale.value)
  } else {
    console.error('Locale not found:', locale)
  }
}

// Функция для получения текущего языка
export function getCurrentLocale() {
  return currentLocale.value
}

// Функция для инициализации языка из пользовательских данных (используется только в main.js для резервной инициализации)
export function initLocaleFromUserData() {
  const userData = localStorage.getItem('user')
  if (userData) {
    try {
      const user = JSON.parse(userData)
      if (user.language && locales[user.language]) {
        console.log('Initializing locale from user data:', user.language)
        setLocale(user.language)
      } else {
        // Если нет языка в данных пользователя, устанавливаем русский по умолчанию
        setLocale('ru')
      }
    } catch (error) {
      console.error('Error parsing user data for locale:', error)
      setLocale('ru')
    }
  } else {
    // Если нет данных пользователя, устанавливаем русский по умолчанию
    setLocale('ru')
  }
} 