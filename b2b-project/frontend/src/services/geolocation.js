// Сервис для автоопределения геолокации
import { apiRequest } from '@/config/api'

/**
 * Преобразовать timezone в UTC формат
 */
function convertTimezoneToUTC(timezone) {
  if (!timezone) return 'UTC'
  
  // Словарь преобразований timezone в UTC
  const timezoneMap = {
    'Asia/Ho_Chi_Minh': 'UTC+7',
    'Asia/Bangkok': 'UTC+7',
    'Asia/Jakarta': 'UTC+7',
    'Asia/Krasnoyarsk': 'UTC+7',
    'Asia/Novosibirsk': 'UTC+7',
    'Asia/Almaty': 'UTC+6',
    'Asia/Tashkent': 'UTC+5',
    'Asia/Yekaterinburg': 'UTC+5',
    'Asia/Baku': 'UTC+4',
    'Asia/Tbilisi': 'UTC+4',
    'Asia/Yerevan': 'UTC+4',
    'Europe/Moscow': 'UTC+3',
    'Asia/Istanbul': 'UTC+3',
    'Asia/Baghdad': 'UTC+3',
    'Europe/Kiev': 'UTC+2',
    'Europe/Sofia': 'UTC+2',
    'Europe/Athens': 'UTC+2',
    'Europe/Paris': 'UTC+1',
    'Europe/Berlin': 'UTC+1',
    'Europe/Rome': 'UTC+1',
    'Europe/London': 'UTC',
    'Europe/Lisbon': 'UTC',
    'America/New_York': 'UTC-4',
    'America/Toronto': 'UTC-4',
    'America/Chicago': 'UTC-5',
    'America/Mexico_City': 'UTC-5',
    'America/Lima': 'UTC-5',
    'America/Denver': 'UTC-6',
    'America/Guatemala': 'UTC-6',
    'America/Los_Angeles': 'UTC-7',
    'America/Vancouver': 'UTC-7',
    'America/San_Francisco': 'UTC-8',
    'America/Seattle': 'UTC-8',
    'America/Anchorage': 'UTC-9',
    'Pacific/Honolulu': 'UTC-10',
    'Pacific/Samoa': 'UTC-11',
    'Pacific/Auckland': 'UTC+12',
    'Pacific/Fiji': 'UTC+12',
    'Asia/Kamchatka': 'UTC+12',
    'Asia/Vladivostok': 'UTC+10',
    'Australia/Sydney': 'UTC+10',
    'Australia/Melbourne': 'UTC+10',
    'Asia/Tokyo': 'UTC+9',
    'Asia/Seoul': 'UTC+9',
    'Asia/Pyongyang': 'UTC+9',
    'Asia/Shanghai': 'UTC+8',
    'Asia/Hong_Kong': 'UTC+8',
    'Asia/Singapore': 'UTC+8',
    'Asia/Bangkok': 'UTC+7',
    'Asia/Jakarta': 'UTC+7',
    'Asia/Krasnoyarsk': 'UTC+7',
    'Asia/Novosibirsk': 'UTC+7',
    'Asia/Almaty': 'UTC+6',
    'Asia/Tashkent': 'UTC+5',
    'Asia/Yekaterinburg': 'UTC+5',
    'Asia/Baku': 'UTC+4',
    'Asia/Tbilisi': 'UTC+4',
    'Asia/Yerevan': 'UTC+4',
    'Europe/Moscow': 'UTC+3',
    'Asia/Istanbul': 'UTC+3',
    'Asia/Baghdad': 'UTC+3',
    'Europe/Kiev': 'UTC+2',
    'Europe/Sofia': 'UTC+2',
    'Europe/Athens': 'UTC+2',
    'Europe/Paris': 'UTC+1',
    'Europe/Berlin': 'UTC+1',
    'Europe/Rome': 'UTC+1',
    'Europe/London': 'UTC',
    'Europe/Lisbon': 'UTC',
    'America/New_York': 'UTC-4',
    'America/Toronto': 'UTC-4',
    'America/Chicago': 'UTC-5',
    'America/Mexico_City': 'UTC-5',
    'America/Lima': 'UTC-5',
    'America/Denver': 'UTC-6',
    'America/Guatemala': 'UTC-6',
    'America/Los_Angeles': 'UTC-7',
    'America/Vancouver': 'UTC-7',
    'America/San_Francisco': 'UTC-8',
    'America/Seattle': 'UTC-8',
    'America/Anchorage': 'UTC-9',
    'Pacific/Honolulu': 'UTC-10',
    'Pacific/Samoa': 'UTC-11',
    'Pacific/Auckland': 'UTC+12',
    'Pacific/Fiji': 'UTC+12',
    'Asia/Kamchatka': 'UTC+12',
    'Asia/Vladivostok': 'UTC+10',
    'Australia/Sydney': 'UTC+10',
    'Australia/Melbourne': 'UTC+10',
    'Asia/Tokyo': 'UTC+9',
    'Asia/Seoul': 'UTC+9',
    'Asia/Pyongyang': 'UTC+9',
    'Asia/Shanghai': 'UTC+8',
    'Asia/Hong_Kong': 'UTC+8',
    'Asia/Singapore': 'UTC+8'
  }
  
  // Ищем точное совпадение
  if (timezoneMap[timezone]) {
    return timezoneMap[timezone]
  }
  
  // Пробуем найти по частичному совпадению
  for (const [tz, utc] of Object.entries(timezoneMap)) {
    if (timezone.includes(tz.split('/')[1]) || tz.includes(timezone.split('/')[1])) {
      return utc
    }
  }
  
  // Если не найдено, пробуем определить по смещению
  try {
    const date = new Date()
    const utcOffset = date.getTimezoneOffset()
    const hours = Math.abs(Math.floor(utcOffset / 60))
    const sign = utcOffset > 0 ? '-' : '+'
    return `UTC${sign}${hours}`
  } catch (error) {
    console.warn('Не удалось определить UTC смещение для:', timezone)
    return 'UTC'
  }
}

/**
 * Получить информацию о местоположении по IP
 */
export async function getLocationByIP() {
  try {
    // Пробуем несколько бесплатных сервисов
    const services = [
      'https://ipapi.co/json/',
      'https://ipinfo.io/json',
      'https://api.ipify.org?format=json'
    ]
    
    for (const service of services) {
      try {
        const response = await fetch(service, { 
          timeout: 5000,
          headers: {
            'Accept': 'application/json'
          }
        })
        
        if (response.ok) {
          const data = await response.json()
          
          // Обрабатываем разные форматы ответов
          if (data.country_name) {
            // ipapi.co формат
            return {
              country: data.country_name,
              countryCode: data.country_code,
              city: data.city,
              timezone: convertTimezoneToUTC(data.timezone),
              latitude: data.latitude,
              longitude: data.longitude
            }
          } else if (data.country) {
            // ipinfo.io формат
            return {
              country: data.country,
              countryCode: data.country,
              city: data.city,
              timezone: convertTimezoneToUTC(data.timezone),
              latitude: data.loc?.split(',')[0],
              longitude: data.loc?.split(',')[1]
            }
          }
        }
      } catch (error) {
        console.warn(`Ошибка сервиса ${service}:`, error)
        continue
      }
    }
    
    return null
  } catch (error) {
    console.error('Ошибка получения геолокации по IP:', error)
    return null
  }
}

/**
 * Получить местоположение через браузерное API
 */
export async function getLocationByBrowser() {
  return new Promise((resolve, reject) => {
    if (!navigator.geolocation) {
      reject(new Error('Геолокация не поддерживается браузером'))
      return
    }

    navigator.geolocation.getCurrentPosition(
      async (position) => {
        try {
          const { latitude, longitude } = position.coords
          
          // Пробуем несколько сервисов для получения информации по координатам
          const services = [
            `https://api.bigdatacloud.net/data/reverse-geocode-client?latitude=${latitude}&longitude=${longitude}&localityLanguage=ru`,
            `https://nominatim.openstreetmap.org/reverse?format=json&lat=${latitude}&lon=${longitude}&accept-language=ru`
          ]
          
          for (const service of services) {
            try {
              const response = await fetch(service, { timeout: 5000 })
              if (response.ok) {
                const data = await response.json()
                
                if (data.countryName) {
                  // bigdatacloud формат
                  return resolve({
                    country: data.countryName,
                    countryCode: data.countryCode,
                    city: data.city || data.locality,
                    timezone: convertTimezoneToUTC(data.timezone?.name || 'UTC'),
                    latitude,
                    longitude
                  })
                } else if (data.address) {
                  // nominatim формат
                  return resolve({
                    country: data.address.country,
                    countryCode: data.address.country_code?.toUpperCase(),
                    city: data.address.city || data.address.town || data.address.village,
                    timezone: 'UTC', // nominatim не предоставляет timezone
                    latitude,
                    longitude
                  })
                }
              }
            } catch (error) {
              console.warn(`Ошибка сервиса координат ${service}:`, error)
              continue
            }
          }
          
          reject(new Error('Не удалось получить информацию по координатам'))
        } catch (error) {
          console.error('Ошибка получения информации по координатам:', error)
          reject(error)
        }
      },
      (error) => {
        console.error('Ошибка получения геолокации:', error)
        reject(error)
      },
      {
        enableHighAccuracy: false,
        timeout: 10000,
        maximumAge: 300000 // 5 минут
      }
    )
  })
}

/**
 * Автоопределение местоположения пользователя
 */
export async function autoDetectLocation() {
  try {
    // Сначала пробуем получить по IP (быстрее)
    const ipLocation = await getLocationByIP()
    if (ipLocation) {
      return ipLocation
    }
    
    // Если не получилось, пробуем браузерное API
    const browserLocation = await getLocationByBrowser()
    if (browserLocation) {
      return browserLocation
    }
    
    return null
  } catch (error) {
    console.error('Ошибка автоопределения местоположения:', error)
    return null
  }
}

/**
 * Найти страну в нашем списке по коду или названию
 */
export function findCountryInList(location, countriesList) {
  if (!location) return null
  
  // Сначала ищем по коду страны
  if (location.countryCode) {
    const countryByCode = countriesList.find(
      country => country.code.toLowerCase() === location.countryCode.toLowerCase()
    )
    if (countryByCode) return countryByCode
  }
  
  // Затем ищем по названию
  if (location.country) {
    const countryByName = countriesList.find(
      country => country.name.toLowerCase().includes(location.country.toLowerCase()) ||
                 location.country.toLowerCase().includes(country.name.toLowerCase())
    )
    if (countryByName) return countryByName
  }
  
  return null
}

/**
 * Найти часовой пояс в нашем списке
 */
export function findTimezoneInList(timezone, timezonesList) {
  if (!timezone || !timezonesList) return null
  
  // Ищем точное совпадение
  const exactMatch = timezonesList.find(tz => tz.value === timezone)
  if (exactMatch) return exactMatch
  
  // Ищем по частичному совпадению
  const partialMatch = timezonesList.find(tz => 
    timezone.includes(tz.value) || tz.value.includes(timezone)
  )
  if (partialMatch) return partialMatch
  
  // Возвращаем UTC по умолчанию
  return timezonesList.find(tz => tz.value === 'UTC') || null
} 