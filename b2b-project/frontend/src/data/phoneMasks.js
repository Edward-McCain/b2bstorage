// Маски телефонных номеров для разных стран
export const phoneMasks = {
  // Россия
  '7': {
    mask: '+7 (###) ###-##-##',
    placeholder: '+7 (___) ___-__-__',
    pattern: /^\+7\s?\(?(\d{3})\)?\s?(\d{3})-?(\d{2})-?(\d{2})$/
  },
  
  // Узбекистан
  '998': {
    mask: '+998 ## ### ## ##',
    placeholder: '+998 __ ___ __ __',
    pattern: /^\+998\s?(\d{2})\s?(\d{3})\s?(\d{2})\s?(\d{2})$/
  },
  
  // Украина
  '380': {
    mask: '+380 ## ### ## ##',
    placeholder: '+380 __ ___ __ __',
    pattern: /^\+380\s?(\d{2})\s?(\d{3})\s?(\d{2})\s?(\d{2})$/
  },
  
  // Беларусь
  '375': {
    mask: '+375 ## ### ## ##',
    placeholder: '+375 __ ___ __ __',
    pattern: /^\+375\s?(\d{2})\s?(\d{3})\s?(\d{2})\s?(\d{2})$/
  },
  
  // Кыргызстан
  '996': {
    mask: '+996 ### ### ###',
    placeholder: '+996 ___ ___ ___',
    pattern: /^\+996\s?(\d{3})\s?(\d{3})\s?(\d{3})$/
  },
  
  // Таджикистан
  '992': {
    mask: '+992 ### ### ###',
    placeholder: '+992 ___ ___ ___',
    pattern: /^\+992\s?(\d{3})\s?(\d{3})\s?(\d{3})$/
  },
  
  // Туркменистан
  '993': {
    mask: '+993 ## ### ###',
    placeholder: '+993 __ ___ ___',
    pattern: /^\+993\s?(\d{2})\s?(\d{3})\s?(\d{3})$/
  },
  
  // Азербайджан
  '994': {
    mask: '+994 ## ### ## ##',
    placeholder: '+994 __ ___ __ __',
    pattern: /^\+994\s?(\d{2})\s?(\d{3})\s?(\d{2})\s?(\d{2})$/
  },
  
  // Армения
  '374': {
    mask: '+374 ## ### ###',
    placeholder: '+374 __ ___ ___',
    pattern: /^\+374\s?(\d{2})\s?(\d{3})\s?(\d{3})$/
  },
  
  // Грузия
  '995': {
    mask: '+995 ### ### ###',
    placeholder: '+995 ___ ___ ___',
    pattern: /^\+995\s?(\d{3})\s?(\d{3})\s?(\d{3})$/
  },
  
  // Молдова
  '373': {
    mask: '+373 ## ### ###',
    placeholder: '+373 __ ___ ___',
    pattern: /^\+373\s?(\d{2})\s?(\d{3})\s?(\d{3})$/
  },
  
  // США и Канада
  '1': {
    mask: '+1 (###) ###-####',
    placeholder: '+1 (___) ___-____',
    pattern: /^\+1\s?\(?(\d{3})\)?\s?(\d{3})-?(\d{4})$/
  },
  
  // Германия
  '49': {
    mask: '+49 ### ### ####',
    placeholder: '+49 ___ ___ ____',
    pattern: /^\+49\s?(\d{3})\s?(\d{3})\s?(\d{4})$/
  },
  
  // Франция
  '33': {
    mask: '+33 # ## ## ## ##',
    placeholder: '+33 _ __ __ __ __',
    pattern: /^\+33\s?(\d{1})\s?(\d{2})\s?(\d{2})\s?(\d{2})\s?(\d{2})$/
  },
  
  // Великобритания
  '44': {
    mask: '+44 #### ######',
    placeholder: '+44 ____ ______',
    pattern: /^\+44\s?(\d{4})\s?(\d{6})$/
  },
  
  // Италия
  '39': {
    mask: '+39 ### ### ####',
    placeholder: '+39 ___ ___ ____',
    pattern: /^\+39\s?(\d{3})\s?(\d{3})\s?(\d{4})$/
  },
  
  // Испания
  '34': {
    mask: '+34 ### ### ###',
    placeholder: '+34 ___ ___ ___',
    pattern: /^\+34\s?(\d{3})\s?(\d{3})\s?(\d{3})$/
  },
  
  // Китай
  '86': {
    mask: '+86 ### #### ####',
    placeholder: '+86 ___ ____ ____',
    pattern: /^\+86\s?(\d{3})\s?(\d{4})\s?(\d{4})$/
  },
  
  // Япония
  '81': {
    mask: '+81 ## #### ####',
    placeholder: '+81 __ ____ ____',
    pattern: /^\+81\s?(\d{2})\s?(\d{4})\s?(\d{4})$/
  },
  
  // Южная Корея
  '82': {
    mask: '+82 ## #### ####',
    placeholder: '+82 __ ____ ____',
    pattern: /^\+82\s?(\d{2})\s?(\d{4})\s?(\d{4})$/
  },
  
  // Индия
  '91': {
    mask: '+91 ##### #####',
    placeholder: '+91 _____ _____',
    pattern: /^\+91\s?(\d{5})\s?(\d{5})$/
  },
  
  // Бразилия
  '55': {
    mask: '+55 ## ##### ####',
    placeholder: '+55 __ _____ ____',
    pattern: /^\+55\s?(\d{2})\s?(\d{5})\s?(\d{4})$/
  },
  
  // Турция
  '90': {
    mask: '+90 ### ### ####',
    placeholder: '+90 ___ ___ ____',
    pattern: /^\+90\s?(\d{3})\s?(\d{3})\s?(\d{4})$/
  }
}

// Функция для получения маски по коду страны
export function getPhoneMask(phoneCode) {
  return phoneMasks[phoneCode] || {
    mask: `+${phoneCode} ##########`,
    placeholder: `+${phoneCode} __________`,
    pattern: new RegExp(`^\\+${phoneCode}\\s?(\\d{10})$`)
  }
}

// Функция для применения маски к номеру
export function applyPhoneMask(phone, phoneCode) {
  const mask = getPhoneMask(phoneCode)
  const digits = phone.replace(/\D/g, '')
  const code = phoneCode.toString()
  
  // Убираем код страны из цифр, если он есть
  let numberDigits = digits
  if (digits.startsWith(code)) {
    numberDigits = digits.slice(code.length)
  }
  
  // Ограничиваем количество цифр (максимум 15 для номера)
  if (numberDigits.length > 15) {
    numberDigits = numberDigits.slice(0, 15)
  }
  
  let result = mask.mask
  let digitIndex = 0
  
  // Заменяем символы # на цифры
  for (let i = 0; i < result.length && digitIndex < numberDigits.length; i++) {
    if (result[i] === '#') {
      result = result.slice(0, i) + numberDigits[digitIndex] + result.slice(i + 1)
      digitIndex++
    }
  }
  
  // Заменяем оставшиеся # на _
  result = result.replace(/#/g, '_')
  
  return result
}

// Функция для извлечения только цифр из номера
export function extractDigits(phone) {
  return phone.replace(/\D/g, '')
}

// Функция для валидации номера телефона
export function validatePhone(phone, phoneCode) {
  const mask = getPhoneMask(phoneCode)
  return mask.pattern.test(phone)
}

// Функция для форматирования номера при вводе
export function formatPhoneInput(input, phoneCode) {
  // Убираем все нецифровые символы
  const digits = input.replace(/\D/g, '')
  
  // Если нет цифр, возвращаем пустую строку
  if (!digits) {
    return ''
  }
  
  // Убираем код страны из начала, если он есть
  const code = phoneCode.toString()
  let numberDigits = digits
  if (digits.startsWith(code)) {
    numberDigits = digits.slice(code.length)
  }
  
  // Ограничиваем количество цифр
  if (numberDigits.length > 15) {
    numberDigits = numberDigits.slice(0, 15)
  }
  
  // Применяем маску
  return applyPhoneMask('+' + code + numberDigits, phoneCode)
} 