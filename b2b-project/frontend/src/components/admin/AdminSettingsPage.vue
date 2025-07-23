<template>
  <AdminLayout>
    <!-- Заголовок страницы -->
    <div class="bg-white shadow-sm border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="py-6">
          <h1 class="text-3xl font-bold text-gray-900">Настройки системы</h1>
          <p class="mt-2 text-sm text-gray-600">Управление настройками и конфигурацией системы</p>
        </div>
      </div>
    </div>

    <!-- Основной контент -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Боковая навигация -->
        <div class="lg:col-span-1">
          <nav class="space-y-1">
            <button
              v-for="section in sections"
              :key="section.id"
              @click="activeSection = section.id"
              class="w-full text-left px-3 py-2 text-sm font-medium rounded-md"
              :class="activeSection === section.id ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'"
            >
              <div class="flex items-center">
                <component :is="section.icon" class="w-5 h-5 mr-3" />
                {{ section.name }}
              </div>
            </button>
          </nav>
        </div>

        <!-- Основной контент -->
        <div class="lg:col-span-2">
          <!-- Общие настройки -->
          <div v-if="activeSection === 'general'" class="space-y-6">
            <div class="bg-white shadow rounded-lg">
              <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Общие настройки</h3>
                <div class="space-y-4">
                  <div>
                    <label for="site-name" class="block text-sm font-medium text-gray-700">Название сайта</label>
                    <input
                      type="text"
                      id="site-name"
                      v-model="settings.siteName"
                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    />
                  </div>
                  <div>
                    <label for="site-description" class="block text-sm font-medium text-gray-700">Описание сайта</label>
                    <textarea
                      id="site-description"
                      v-model="settings.siteDescription"
                      rows="3"
                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    ></textarea>
                  </div>
                  <div>
                    <label for="default-currency" class="block text-sm font-medium text-gray-700">Валюта по умолчанию</label>
                    <select
                      id="default-currency"
                      v-model="settings.defaultCurrency"
                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    >
                      <option value="RUB">Рубль (RUB)</option>
                      <option value="USD">Доллар (USD)</option>
                      <option value="EUR">Евро (EUR)</option>
                      <option value="UZS">Сум (UZS)</option>
                    </select>
                  </div>
                  <div>
                    <label for="timezone" class="block text-sm font-medium text-gray-700">Часовой пояс по умолчанию</label>
                    <select
                      id="timezone"
                      v-model="settings.defaultTimezone"
                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    >
                      <option value="UTC+3">Москва (UTC+3)</option>
                      <option value="UTC+5">Ташкент (UTC+5)</option>
                      <option value="UTC+0">UTC</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Настройки безопасности -->
          <div v-if="activeSection === 'security'" class="space-y-6">
            <div class="bg-white shadow rounded-lg">
              <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Настройки безопасности</h3>
                <div class="space-y-4">
                  <div>
                    <label for="session-timeout" class="block text-sm font-medium text-gray-700">Таймаут сессии (минуты)</label>
                    <input
                      type="number"
                      id="session-timeout"
                      v-model="settings.sessionTimeout"
                      min="5"
                      max="1440"
                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    />
                  </div>
                  <div>
                    <label for="max-login-attempts" class="block text-sm font-medium text-gray-700">Максимум попыток входа</label>
                    <input
                      type="number"
                      id="max-login-attempts"
                      v-model="settings.maxLoginAttempts"
                      min="3"
                      max="10"
                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    />
                  </div>
                  <div class="flex items-center">
                    <input
                      id="require-email-verification"
                      type="checkbox"
                      v-model="settings.requireEmailVerification"
                      class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                    />
                    <label for="require-email-verification" class="ml-2 block text-sm text-gray-900">
                      Требовать подтверждение email при регистрации
                    </label>
                  </div>
                  <div class="flex items-center">
                    <input
                      id="enable-two-factor"
                      type="checkbox"
                      v-model="settings.enableTwoFactor"
                      class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                    />
                    <label for="enable-two-factor" class="ml-2 block text-sm text-gray-900">
                      Включить двухфакторную аутентификацию
                    </label>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Настройки уведомлений -->
          <div v-if="activeSection === 'notifications'" class="space-y-6">
            <div class="bg-white shadow rounded-lg">
              <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Настройки уведомлений</h3>
                <div class="space-y-4">
                  <div class="flex items-center">
                    <input
                      id="email-notifications"
                      type="checkbox"
                      v-model="settings.emailNotifications"
                      class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                    />
                    <label for="email-notifications" class="ml-2 block text-sm text-gray-900">
                      Email уведомления
                    </label>
                  </div>
                  <div class="flex items-center">
                    <input
                      id="sms-notifications"
                      type="checkbox"
                      v-model="settings.smsNotifications"
                      class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                    />
                    <label for="sms-notifications" class="ml-2 block text-sm text-gray-900">
                      SMS уведомления
                    </label>
                  </div>
                  <div class="flex items-center">
                    <input
                      id="push-notifications"
                      type="checkbox"
                      v-model="settings.pushNotifications"
                      class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                    />
                    <label for="push-notifications" class="ml-2 block text-sm text-gray-900">
                      Push уведомления
                    </label>
                  </div>
                  <div>
                    <label for="notification-email" class="block text-sm font-medium text-gray-700">Email для системных уведомлений</label>
                    <input
                      type="email"
                      id="notification-email"
                      v-model="settings.notificationEmail"
                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    />
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Настройки интеграций -->
          <div v-if="activeSection === 'integrations'" class="space-y-6">
            <div class="bg-white shadow rounded-lg">
              <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Интеграции</h3>
                <div class="space-y-4">
                  <div>
                    <label for="api-key" class="block text-sm font-medium text-gray-700">API ключ</label>
                    <div class="mt-1 flex rounded-md shadow-sm">
                      <input
                        type="text"
                        id="api-key"
                        v-model="settings.apiKey"
                        readonly
                        class="flex-1 px-3 py-2 border border-gray-300 rounded-l-md bg-gray-50 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                      />
                      <button
                        @click="generateApiKey"
                        class="inline-flex items-center px-3 py-2 border border-l-0 border-gray-300 rounded-r-md bg-gray-50 text-sm font-medium text-gray-700 hover:bg-gray-100"
                      >
                        Сгенерировать
                      </button>
                    </div>
                  </div>
                  <div class="flex items-center">
                    <input
                      id="enable-api"
                      type="checkbox"
                      v-model="settings.enableApi"
                      class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                    />
                    <label for="enable-api" class="ml-2 block text-sm text-gray-900">
                      Включить API доступ
                    </label>
                  </div>
                  <div class="flex items-center">
                    <input
                      id="enable-webhooks"
                      type="checkbox"
                      v-model="settings.enableWebhooks"
                      class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                    />
                    <label for="enable-webhooks" class="ml-2 block text-sm text-gray-900">
                      Включить webhooks
                    </label>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Резервное копирование -->
          <div v-if="activeSection === 'backup'" class="space-y-6">
            <div class="bg-white shadow rounded-lg">
              <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Резервное копирование</h3>
                <div class="space-y-4">
                  <div class="flex items-center justify-between">
                    <div>
                      <h4 class="text-sm font-medium text-gray-900">Автоматическое резервное копирование</h4>
                      <p class="text-sm text-gray-500">Создавать резервные копии каждый день в 02:00</p>
                    </div>
                    <button
                      @click="createBackup"
                      class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                      Создать резервную копию
                    </button>
                  </div>
                  <div class="flex items-center">
                    <input
                      id="auto-backup"
                      type="checkbox"
                      v-model="settings.autoBackup"
                      class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                    />
                    <label for="auto-backup" class="ml-2 block text-sm text-gray-900">
                      Включить автоматическое резервное копирование
                    </label>
                  </div>
                  <div>
                    <label for="backup-retention" class="block text-sm font-medium text-gray-700">Хранить резервные копии (дней)</label>
                    <input
                      type="number"
                      id="backup-retention"
                      v-model="settings.backupRetention"
                      min="1"
                      max="365"
                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    />
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Кнопки действий -->
          <div class="flex justify-end space-x-3 pt-6">
            <button
              @click="resetSettings"
              class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
              Сбросить
            </button>
            <button
              @click="saveSettings"
              class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
              Сохранить настройки
            </button>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import AdminLayout from './AdminLayout.vue'

// Активная секция
const activeSection = ref('general')

// Настройки
const settings = ref({
  siteName: 'B2B Storage',
  siteDescription: 'Система управления складом и товарами',
  defaultCurrency: 'UZS',
  defaultTimezone: 'UTC+5',
  sessionTimeout: 30,
  maxLoginAttempts: 5,
  requireEmailVerification: true,
  enableTwoFactor: false,
  emailNotifications: true,
  smsNotifications: false,
  pushNotifications: true,
  notificationEmail: 'admin@b2bstorage.ru',
  apiKey: 'sk-1234567890abcdef',
  enableApi: true,
  enableWebhooks: false,
  autoBackup: true,
  backupRetention: 30
})

// Секции настроек
const sections = [
  {
    id: 'general',
    name: 'Общие настройки',
    icon: 'SettingsIcon'
  },
  {
    id: 'security',
    name: 'Безопасность',
    icon: 'ShieldIcon'
  },
  {
    id: 'notifications',
    name: 'Уведомления',
    icon: 'BellIcon'
  },
  {
    id: 'integrations',
    name: 'Интеграции',
    icon: 'LinkIcon'
  },
  {
    id: 'backup',
    name: 'Резервное копирование',
    icon: 'DatabaseIcon'
  }
]

// Иконки
const SettingsIcon = {
  template: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
  </svg>`
}

const ShieldIcon = {
  template: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
  </svg>`
}

const BellIcon = {
  template: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
  </svg>`
}

const LinkIcon = {
  template: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
  </svg>`
}

const DatabaseIcon = {
  template: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
  </svg>`
}

// Методы
const generateApiKey = () => {
  const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'
  let result = 'sk-'
  for (let i = 0; i < 32; i++) {
    result += chars.charAt(Math.floor(Math.random() * chars.length))
  }
  settings.value.apiKey = result
}

const createBackup = () => {
  console.log('Создание резервной копии...')
  // Здесь будет логика создания резервной копии
}

const saveSettings = () => {
  console.log('Сохранение настроек:', settings.value)
  // Здесь будет логика сохранения настроек
}

const resetSettings = () => {
  console.log('Сброс настроек')
  // Здесь будет логика сброса настроек
}

onMounted(() => {
  // Загрузка настроек при монтировании компонента
  console.log('Загрузка настроек...')
})
</script> 