<template>
  <div class="min-h-screen bg-gray-50 pt-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      
      <!-- Основной контент -->
      <div class="flex gap-8">
        <!-- Левая колонка - навигация (только для ПК) -->
        <div class="hidden lg:block w-64 flex-shrink-0">
          <div class="sticky top-24">
            <nav class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
              <h3 class="text-sm font-semibold text-gray-900 mb-4">Навигация</h3>
              <ul class="space-y-2">
                <li>
                  <button 
                    @click="scrollToSection('personal')"
                    class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:text-gray-900 hover:bg-gray-50 rounded-md transition-colors flex items-center gap-2"
                    :class="{ 'bg-blue-50 text-blue-700': activeSection === 'personal' }"
                  >
                    <User class="w-4 h-4" />
                    Личные данные
                  </button>
                </li>
                <li>
                  <button 
                    @click="scrollToSection('company')"
                    class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:text-gray-900 hover:bg-gray-50 rounded-md transition-colors flex items-center gap-2"
                    :class="{ 'bg-blue-50 text-blue-700': activeSection === 'company' }"
                  >
                    <Building class="w-4 h-4" />
                    Данные компании
                  </button>
                </li>
                <li>
                  <button 
                    @click="scrollToSection('password')"
                    class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:text-gray-900 hover:bg-gray-50 rounded-md transition-colors flex items-center gap-2"
                    :class="{ 'bg-blue-50 text-blue-700': activeSection === 'password' }"
                  >
                    <Lock class="w-4 h-4" />
                    Смена пароля
                  </button>
                </li>
              </ul>
            </nav>
          </div>
        </div>

        <!-- Правая колонка - контент -->
        <div class="flex-1 space-y-8">
          <!-- Личные данные -->
          <section id="personal" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center gap-3 mb-6">
              <User class="w-5 h-5 text-blue-600" />
              <h2 class="text-xl font-semibold text-gray-900">Личные данные</h2>
              <!-- <button 
                @click="detectLocation"
                :disabled="isDetectingLocation"
                class="ml-auto bg-green-600 hover:bg-green-700 disabled:bg-green-400 text-white font-medium px-4 py-2 rounded-lg transition-colors flex items-center gap-2 text-sm"
              >
                <Loader2 v-if="isDetectingLocation" class="animate-spin h-4 w-4" />
                {{ isDetectingLocation ? 'Определение...' : (locationDetected ? 'Обновить' : 'Автоопределение') }}
              </button> -->
            </div>
            
            <form @submit.prevent="savePersonalData" class="space-y-6">
              <!-- Блок аватара -->
              <div class="mb-8">
                <label class="block text-sm font-medium text-gray-700 mb-4">Фото профиля</label>
                <div class="flex items-center gap-6">
                  <!-- Аватар -->
                  <div class="relative group">
                    <div class="w-24 h-24 rounded-full overflow-hidden bg-gray-100 border-2 border-gray-200">
                      <img 
                        v-if="avatarDisplayUrl" 
                        :src="avatarDisplayUrl" 
                        alt="Аватар" 
                        class="w-full h-full object-cover"
                      />
                      <div v-else class="w-full h-full flex items-center justify-center">
                        <User class="w-8 h-8 text-gray-400" />
                      </div>
                    </div>
                    <!-- Оверлей при наведении -->
                    <div class="absolute inset-0 bg-black bg-opacity-50 rounded-full opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center cursor-pointer text-center" @click="openAvatarUpload">
                      <span class="text-white text-xs font-medium">Обновить аватар</span>
                    </div>
                  </div>
                  
                  <!-- Кнопка загрузки -->
                  <div>
                    <button 
                      type="button"
                      @click="openAvatarUpload"
                      class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg transition-colors text-sm"
                    >
                      Загрузить фото
                    </button>
                    <p class="text-xs text-gray-500 mt-1">Рекомендуемый размер: 400x400px</p>
                  </div>
                </div>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Имя -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Имя *</label>
                  <input 
                    v-model="personalData.firstName" 
                    type="text" 
                    required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent"
                    placeholder="Введите ваше имя"
                  />
                </div>

                <!-- Должность -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Должность</label>
                  <input 
                    v-model="personalData.position" 
                    type="text" 
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent"
                    placeholder="Введите должность"
                  />
                </div>

                <!-- Телефон -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Телефон *</label>
                  <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                      <button 
                        type="button"
                        @click="showCountrySelect = !showCountrySelect"
                        class="country-select flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700"
                      >
                        <img 
                          :src="selectedCountry.flag" 
                          :alt="selectedCountry.name"
                          class="w-5 h-5 rounded"
                        />
                        <span>{{ selectedCountry.code }}</span>
                        <ChevronDown class="w-4 h-4" />
                      </button>
                    </div>
                    <input 
                      v-model="personalData.phone" 
                      type="tel" 
                      required
                      @input="formatPhoneNumber"
                      @focus="showCountrySelect = false"
                      class="w-full border border-gray-300 rounded-lg pl-20 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent"
                      :placeholder="getPhoneMask(selectedCountry.phone_code).placeholder"
                    />
                  </div>
                  <!-- Выпадающий список стран -->
                  <div v-if="showCountrySelect" class="country-select absolute z-10 mt-1 w-64 bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                    <div class="p-2">
                      <input 
                        v-model="countrySearch" 
                        type="text" 
                        placeholder="Поиск страны..."
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm mb-2"
                      />
                      <div class="space-y-1">
                        <button 
                          v-for="country in filteredCountries" 
                          :key="country.id"
                          @click="selectCountry(country)"
                          class="w-full text-left px-3 py-2 text-sm hover:bg-gray-50 rounded flex items-center gap-2"
                        >
                          <img :src="country.flag" :alt="country.name" class="w-4 h-4 rounded" />
                          <span>{{ country.name }}</span>
                          <span class="text-gray-500 text-xs">+{{ country.phone_code }}</span>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Email -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                  <input 
                    v-model="personalData.email" 
                    type="email" 
                    required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent"
                    placeholder="Введите email"
                  />
                </div>

                <!-- Страна -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Страна</label>
                  <select 
                    v-model="personalData.country" 
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent"
                  >
                    <option value="">Выберите страну</option>
                    <option v-for="country in countries" :key="country.id" :value="country.id">
                      {{ country.name }}
                    </option>
                  </select>
                </div>

                <!-- Город -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Город</label>
                  <input 
                    v-model="personalData.city" 
                    type="text" 
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent"
                    placeholder="Введите город"
                  />
                </div>

                <!-- Часовой пояс -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Часовой пояс</label>
                  <select 
                    v-model="personalData.timezone" 
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent"
                  >
                    <option value="">Выберите часовой пояс</option>
                    <option v-for="timezone in timezones" :key="timezone.value" :value="timezone.value">
                      {{ timezone.label }}
                    </option>
                  </select>
                </div>
              </div>

              <div class="flex justify-end">
                <button 
                  type="submit" 
                  :disabled="savingPersonal"
                  class="bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white font-semibold px-6 py-2 rounded-lg transition-colors flex items-center gap-2 text-sm"
                >
                  <Loader2 v-if="savingPersonal" class="animate-spin h-4 w-4" />
                  {{ savingPersonal ? 'Сохранение...' : 'Сохранить' }}
                </button>
              </div>
            </form>
          </section>

          <!-- Данные компании -->
          <section id="company" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center gap-3 mb-6">
              <Building class="w-5 h-5 text-blue-600" />
              <h2 class="text-xl font-semibold text-gray-900">Данные компании</h2>
            </div>
            
            <form @submit.prevent="saveCompanyData" class="space-y-6">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Наименование компании -->
                <div class="md:col-span-2">
                  <label class="block text-sm font-medium text-gray-700 mb-2">Наименование компании</label>
                  <input 
                    v-model="companyData.name" 
                    type="text" 
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent"
                    placeholder="Введите наименование компании"
                  />
                </div>

                <!-- ИНН или ПИНФЛ -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">ИНН или ПИНФЛ</label>
                  <input 
                    v-model="companyData.inn" 
                    type="text" 
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent"
                    placeholder="Введите ИНН или ПИНФЛ"
                  />
                </div>
              </div>

              <div class="flex justify-end">
                <button 
                  type="submit" 
                  :disabled="savingCompany"
                  class="bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white font-semibold px-6 py-2 rounded-lg transition-colors flex items-center gap-2 text-sm"
                >
                  <Loader2 v-if="savingCompany" class="animate-spin h-4 w-4" />
                  {{ savingCompany ? 'Сохранение...' : 'Сохранить' }}
                </button>
              </div>
            </form>
          </section>

          <!-- Смена пароля -->
          <section id="password" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center gap-3 mb-6">
              <Lock class="w-5 h-5 text-blue-600" />
              <h2 class="text-xl font-semibold text-gray-900">Смена пароля</h2>
            </div>
            
            <form @submit.prevent="changePassword" class="space-y-6">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Старый пароль -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Старый пароль *</label>
                  <div class="relative">
                    <input 
                      v-model="passwordData.oldPassword" 
                      :type="showOldPassword ? 'text' : 'password'" 
                      required
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent"
                      placeholder="Введите старый пароль"
                    />
                    <button 
                      type="button"
                      @click="showOldPassword = !showOldPassword"
                      class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600"
                    >
                      <Eye v-if="showOldPassword" class="w-4 h-4" />
                      <EyeOff v-else class="w-4 h-4" />
                    </button>
                  </div>
                </div>

                <!-- Новый пароль -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Новый пароль *</label>
                  <div class="relative">
                    <input 
                      v-model="passwordData.newPassword" 
                      :type="showNewPassword ? 'text' : 'password'" 
                      required
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent"
                      placeholder="Введите новый пароль"
                    />
                    <button 
                      type="button"
                      @click="showNewPassword = !showNewPassword"
                      class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600"
                    >
                      <Eye v-if="showNewPassword" class="w-4 h-4" />
                      <EyeOff v-else class="w-4 h-4" />
                    </button>
                  </div>
                </div>

                <!-- Повторить новый пароль -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Повторить новый пароль *</label>
                  <div class="relative">
                    <input 
                      v-model="passwordData.confirmPassword" 
                      :type="showConfirmPassword ? 'text' : 'password'" 
                      required
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent"
                      placeholder="Повторите новый пароль"
                    />
                    <button 
                      type="button"
                      @click="showConfirmPassword = !showConfirmPassword"
                      class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600"
                    >
                      <Eye v-if="showConfirmPassword" class="w-4 h-4" />
                      <EyeOff v-else class="w-4 h-4" />
                    </button>
                  </div>
                </div>
              </div>

              <div class="flex justify-end">
                <button 
                  type="submit" 
                  :disabled="changingPassword"
                  class="bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white font-semibold px-6 py-2 rounded-lg transition-colors flex items-center gap-2 text-sm"
                >
                  <Loader2 v-if="changingPassword" class="animate-spin h-4 w-4" />
                  {{ changingPassword ? 'Смена пароля...' : 'Сменить пароль' }}
                </button>
              </div>
            </form>
          </section>
        </div>
      </div>
    </div>

    <!-- Скрытый input для загрузки файла -->
    <input 
      ref="fileInput"
      type="file" 
      accept="image/*" 
      @change="handleFileSelect"
      class="hidden"
    />
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, onUnmounted, computed } from 'vue'
import { User, Building, Lock, Eye, EyeOff, ChevronDown, Loader2, X } from 'lucide-vue-next'
import countriesData from '@/data/countries.json'
import timezonesData from '@/data/timezones.json'
import { apiRequest, getFileUrl } from '@/config/api'
import { autoDetectLocation, findCountryInList, findTimezoneInList } from '@/services/geolocation'
import { getPhoneMask, applyPhoneMask, extractDigits, validatePhone, formatPhoneInput } from '@/data/phoneMasks.js'

const emit = defineEmits(['avatar-updated'])

// Состояние навигации
const activeSection = ref('personal')

// Состояние аватара
const avatarUrl = ref('')
const fileInput = ref(null)

// Computed свойство для правильного URL аватара
const avatarDisplayUrl = computed(() => {
  if (!avatarUrl.value) return null
  return getFileUrl(avatarUrl.value)
})

// Состояние форм
const personalData = reactive({
  firstName: '',
  position: '',
  phone: '',
  email: '',
  country: '',
  city: '',
  timezone: 'UTC+5'
})

const companyData = reactive({
  name: '',
  inn: ''
})

const passwordData = reactive({
  oldPassword: '',
  newPassword: '',
  confirmPassword: ''
})

// Состояние загрузки
const savingPersonal = ref(false)
const savingCompany = ref(false)
const changingPassword = ref(false)

// Состояние отображения паролей
const showOldPassword = ref(false)
const showNewPassword = ref(false)
const showConfirmPassword = ref(false)

// Состояние выбора страны для телефона
const showCountrySelect = ref(false)
const countrySearch = ref('')
const selectedCountry = ref({
  id: 860,
  name: 'Uzbekistan',
  code: 'UZ',
  phone_code: '998',
  flag: 'https://flagcdn.com/w40/uz.png'
})

// Данные стран
const countries = ref(countriesData)

// Данные часовых поясов
const timezones = ref(timezonesData)

// Состояние автоопределения
const isDetectingLocation = ref(false)
const locationDetected = ref(false)

// Фильтрованные страны для поиска
const filteredCountries = computed(() => {
  if (!countrySearch.value) return countries.value
  return countries.value.filter(country => 
    country.name.toLowerCase().includes(countrySearch.value.toLowerCase()) ||
    country.code.toLowerCase().includes(countrySearch.value.toLowerCase())
  )
})

// Функции для работы с аватаром
const openAvatarUpload = () => {
  fileInput.value?.click()
}

const handleFileSelect = (event) => {
  const file = event.target.files[0]
  if (file) {
    const reader = new FileReader()
    reader.onload = (e) => {
      cropAndSetAvatar(e.target.result)
    }
    reader.readAsDataURL(file)
  }
  event.target.value = ''
}

async function cropAndSetAvatar(imageSrc) {
  const img = new window.Image()
  img.onload = async () => {
    const size = Math.min(img.width, img.height)
    const sx = (img.width - size) / 2
    const sy = (img.height - size) / 2
    const canvas = document.createElement('canvas')
    canvas.width = 400
    canvas.height = 400
    const ctx = canvas.getContext('2d')
    ctx.drawImage(img, sx, sy, size, size, 0, 0, 400, 400)
    
    const base64Image = canvas.toDataURL('image/jpeg', 0.8)
    
    try {
      // Отправляем аватар на сервер
      const response = await apiRequest('/user/avatar', {
        method: 'POST',
        body: JSON.stringify({
          avatar: base64Image
        })
      })
      
      if (response.ok && response.data.success) {
        console.log('Ответ сервера:', response.data)
        avatarUrl.value = response.data.data.avatar_url
        console.log('avatarUrl обновлен на:', avatarUrl.value)
        
        // Обновляем аватар в шапке через emit
        emit('avatar-updated', response.data.data.avatar_url)
        // Отправляем глобальное событие
        window.dispatchEvent(new CustomEvent('avatar-updated', {
          detail: response.data.data.avatar_url
        }))
        console.log('Аватар успешно загружен:', response.data.data.avatar_url)
      } else {
        console.error('Ошибка загрузки аватара:', response.data.message)
      }
    } catch (error) {
      console.error('Ошибка при загрузке аватара:', error)
    }
  }
  img.src = imageSrc
}

// Функции навигации
const scrollToSection = (sectionId) => {
  const element = document.getElementById(sectionId)
  if (element) {
    const rect = element.getBoundingClientRect()
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop
    const targetPosition = scrollTop + rect.top - 120 // Учитываем высоту шапки и отступ
    
    window.scrollTo({
      top: targetPosition,
      behavior: 'smooth'
    })
    activeSection.value = sectionId
  }
}

const selectCountry = (country) => {
  selectedCountry.value = country
  showCountrySelect.value = false
  countrySearch.value = ''
  
  // Применяем маску для новой страны
  const phoneMask = getPhoneMask(country.phone_code)
  
  // Если поле пустое или содержит только placeholder, устанавливаем новый placeholder
  if (!personalData.phone || personalData.phone.includes('_')) {
    personalData.phone = phoneMask.placeholder
  } else {
    // Если поле не пустое, применяем маску к существующему номеру
    const digits = extractDigits(personalData.phone)
    if (digits) {
      personalData.phone = applyPhoneMask('+' + country.phone_code + digits, country.phone_code)
    } else {
      personalData.phone = phoneMask.placeholder
    }
  }
}

// Функция форматирования номера телефона с маской
const formatPhoneNumber = () => {
  // Используем новую функцию форматирования
  const formatted = formatPhoneInput(personalData.phone, selectedCountry.value.phone_code)
  
  // Если результат пустой, устанавливаем placeholder
  if (!formatted) {
    const phoneMask = getPhoneMask(selectedCountry.value.phone_code)
    personalData.phone = phoneMask.placeholder
  } else {
    personalData.phone = formatted
  }
}

// Функция автоопределения местоположения
const detectLocation = async () => {
  if (isDetectingLocation.value) return
  
  isDetectingLocation.value = true
  
  try {
    const location = await autoDetectLocation()
    
    if (location) {
      console.log('Полученные данные о местоположении:', location)
      
      // Находим страну в нашем списке
      const detectedCountry = findCountryInList(location, countries.value)
      if (detectedCountry) {
        console.log('Найдена страна:', detectedCountry)
        selectedCountry.value = detectedCountry
        personalData.country = detectedCountry.id
        
        // Автоматически применяем маску для телефона при автоопределении
        const phoneMask = getPhoneMask(detectedCountry.phone_code)
        if (!personalData.phone || personalData.phone.includes('_')) {
          personalData.phone = phoneMask.placeholder
        } else {
          const digits = extractDigits(personalData.phone)
          if (digits) {
            personalData.phone = applyPhoneMask('+' + detectedCountry.phone_code + digits, detectedCountry.phone_code)
          } else {
            personalData.phone = phoneMask.placeholder
          }
        }
      } else {
        console.log('Страна не найдена в списке для:', location.country)
      }
      
      // Устанавливаем город
      if (location.city) {
        console.log('Устанавливаем город:', location.city)
        personalData.city = location.city
      }
      
      // Находим часовой пояс
      const detectedTimezone = findTimezoneInList(location.timezone, timezones.value)
      if (detectedTimezone) {
        console.log('Найден часовой пояс:', detectedTimezone)
        personalData.timezone = detectedTimezone.value
      } else {
        console.log('Часовой пояс не найден для:', location.timezone)
      }
      
      locationDetected.value = true
      console.log('Местоположение определено:', location)
    } else {
      console.log('Не удалось определить местоположение')
    }
  } catch (error) {
    console.error('Ошибка автоопределения местоположения:', error)
  } finally {
    isDetectingLocation.value = false
  }
}

const savePersonalData = async () => {
  // Валидация телефона
  const phoneDigits = extractDigits(personalData.phone)
  if (phoneDigits && !validatePhone(personalData.phone, selectedCountry.value.phone_code)) {
    alert('Пожалуйста, введите корректный номер телефона')
    return
  }
  
  savingPersonal.value = true
  try {
    // Здесь будет API вызов для сохранения личных данных
    await new Promise(resolve => setTimeout(resolve, 1000)) // Имитация API
    console.log('Личные данные сохранены:', personalData)
  } catch (error) {
    console.error('Ошибка сохранения личных данных:', error)
  } finally {
    savingPersonal.value = false
  }
}

const saveCompanyData = async () => {
  savingCompany.value = true
  try {
    // Здесь будет API вызов для сохранения данных компании
    await new Promise(resolve => setTimeout(resolve, 1000)) // Имитация API
    console.log('Данные компании сохранены:', companyData)
  } catch (error) {
    console.error('Ошибка сохранения данных компании:', error)
  } finally {
    savingCompany.value = false
  }
}

const changePassword = async () => {
  if (passwordData.newPassword !== passwordData.confirmPassword) {
    alert('Пароли не совпадают')
    return
  }
  
  changingPassword.value = true
  try {
    // Здесь будет API вызов для смены пароля
    await new Promise(resolve => setTimeout(resolve, 1000)) // Имитация API
    console.log('Пароль изменен')
    
    // Очищаем поля паролей
    passwordData.oldPassword = ''
    passwordData.newPassword = ''
    passwordData.confirmPassword = ''
  } catch (error) {
    console.error('Ошибка смены пароля:', error)
  } finally {
    changingPassword.value = false
  }
}

// Отслеживание активной секции при скролле
const handleScroll = () => {
  const sections = ['personal', 'company', 'password']
  const scrollPosition = window.scrollY + 0 // Учитываем высоту шапки и отступы

  for (const section of sections) {
    const element = document.getElementById(section)
    if (element) {
      const rect = element.getBoundingClientRect()
      const elementTop = rect.top + window.scrollY
      const elementBottom = elementTop + rect.height
      
      // Проверяем, находится ли текущая позиция скролла в пределах секции
      if (scrollPosition >= elementTop - 100 && scrollPosition < elementBottom - 100) {
        activeSection.value = section
        break
      }
    }
  }
}

// Обработчик кликов вне выпадающего списка стран
const handleClickOutside = (event) => {
  const countrySelect = event.target.closest('.country-select')
  if (!countrySelect && showCountrySelect.value) {
    showCountrySelect.value = false
    countrySearch.value = ''
  }
}

onMounted(() => {
  // Устанавливаем заголовок страницы
  document.title = 'B2B Storage - Настройки аккаунта'
  
  // Загружаем текущий аватар пользователя
  const userData = localStorage.getItem('user')
  if (userData) {
    const user = JSON.parse(userData)
    if (user.avatar_url) {
      avatarUrl.value = user.avatar_url
      console.log('Загружен текущий аватар:', user.avatar_url)
    }
  }
  
  // Добавляем обработчик скролла
  window.addEventListener('scroll', handleScroll)
  
  // Добавляем обработчик кликов вне выпадающего списка стран
  document.addEventListener('click', handleClickOutside)
  
  // Устанавливаем первую секцию как активную по умолчанию
  activeSection.value = 'personal'
  
  // Инициализируем маску телефона для выбранной страны
  if (!personalData.phone || personalData.phone.includes('_')) {
    const phoneMask = getPhoneMask(selectedCountry.value.phone_code)
    personalData.phone = phoneMask.placeholder
  }
  
  // Автоматически определяем местоположение при загрузке
  detectLocation()
})

onUnmounted(() => {
  // Удаляем обработчик скролла
  window.removeEventListener('scroll', handleScroll)
  
  // Удаляем обработчик кликов
  document.removeEventListener('click', handleClickOutside)
})
</script> 