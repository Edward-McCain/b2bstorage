<template>
  <div class="flex flex-col justify-center px-6 py-20 lg:px-8 bg-gray-50">
    <div class="sm:mx-auto sm:w-full sm:max-w-sm">
      <!-- Создать аккаунт -->
      <h2 class="mt-10 text-center text-2xl/9 font-bold tracking-tight text-gray-900">{{ t('RegisterForm_1') }}</h2>
    </div>

    <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
      <form class="space-y-6" @submit.prevent="handleRegister">
        <div>
          <!-- Email -->
          <label for="email" class="block text-sm/6 font-medium text-gray-900">{{ t('RegisterForm_2') }}</label>
          <div class="mt-2">
            <input 
              type="email" 
              name="email" 
              id="email" 
              v-model="form.email"
              autocomplete="email" 
              required 
              class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-primary sm:text-sm/6"
              :class="{ 'outline-red-500': errors.email }"
              :placeholder="t('RegisterForm_3')"
            />
          </div>
          <p v-if="errors.email" class="mt-1 text-sm text-red-600">{{ errors.email[0] }}</p>
        </div>

        <div>
          <!-- Логин -->
          <label for="user_name" class="block text-sm/6 font-medium text-gray-900">{{ t('RegisterForm_4') }}</label>
          <div class="mt-2">
            <input 
              type="text" 
              name="user_name" 
              id="user_name" 
              v-model="form.user_name"
              autocomplete="username" 
              required 
              class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-primary sm:text-sm/6"
              :class="{ 'outline-red-500': errors.user_name }"
              :placeholder="t('RegisterForm_5')"
            />
          </div>
          <p v-if="errors.user_name" class="mt-1 text-sm text-red-600">{{ errors.user_name[0] }}</p>
        </div>

        <div>
          <!-- Пароль -->
          <label for="password" class="block text-sm/6 font-medium text-gray-900">{{ t('RegisterForm_6') }}</label>
          <div class="mt-2">
            <input 
              type="password" 
              name="password" 
              id="password" 
              v-model="form.password"
              autocomplete="new-password" 
              required 
              class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-primary sm:text-sm/6"
              :class="{ 'outline-red-500': errors.password }"
              :placeholder="t('RegisterForm_7')"
            />
          </div>
          <p v-if="errors.password" class="mt-1 text-sm text-red-600">{{ errors.password[0] }}</p>
        </div>

        <div>
          <!-- Повторите пароль -->
          <label for="password_confirmation" class="block text-sm/6 font-medium text-gray-900">{{ t('RegisterForm_8') }}</label>
          <div class="mt-2">
            <input 
              type="password" 
              name="password_confirmation" 
              id="password_confirmation" 
              v-model="form.password_confirmation"
              autocomplete="new-password" 
              required 
              class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-primary sm:text-sm/6"
              :class="{ 'outline-red-500': errors.password_confirmation }"
              :placeholder="t('RegisterForm_8')"
            />
          </div>
          <p v-if="errors.password_confirmation" class="mt-1 text-sm text-red-600">{{ errors.password_confirmation[0] }}</p>
        </div>

        <div>
          <button 
            type="submit" 
            :disabled="loading"
            class="flex w-full justify-center rounded-md border-2 bg-blue-700 border-blue-700 px-3 py-1.5 text-sm/6 font-semibold text-white cursor-pointer shadow-xs focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <span v-if="loading" class="mr-2">
              <svg class="animate-spin h-4 w-4 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
            </span>
            <!-- Регистрация... / Зарегистрироваться -->
            {{ loading ? t('RegisterForm_9') : t('RegisterForm_10') }}
          </button>
        </div>
      </form>

      <p class="mt-10 text-center text-sm/6 text-gray-500">
        <!-- Уже есть аккаунт? -->
        {{ t('RegisterForm_11') }}
        <!-- Войти -->
        <button @click="$emit('switch-to-login')" class="font-semibold text-primary cursor-pointer text-blue-700 hover:text-primary/80">{{ t('RegisterForm_12') }}</button>
      </p>

      <div v-if="error" class="mt-6 rounded-md bg-red-50 p-4">
        <div class="flex">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
            </svg>
          </div>
          <div class="ml-3">
            <p class="text-sm text-red-800">{{ error }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { apiRequest } from '@/config/api'
import { t } from '../locales/index.js'

export default {
  name: 'RegisterForm',
  emits: ['switch-to-login', 'register-success'],
  data() {
    return {
      form: {
        email: '',
        user_name: '',
        password: '',
        password_confirmation: ''
      },
      loading: false,
      error: '',
      errors: {}
    }
  },
  methods: {
    t,
    async handleRegister() {
      this.loading = true
      this.error = ''
      this.errors = {}

      try {
        const response = await apiRequest('/register', {
          method: 'POST',
          body: JSON.stringify({
            email: this.form.email,
            user_name: this.form.user_name,
            password: this.form.password,
            password_confirmation: this.form.password_confirmation,
            // Добавляем обязательные поля для backend
            first_name: this.form.user_name,
            last_name: '',
            phone_number: '',
            position: 'Пользователь',
            country: 'Россия',
            city: 'Москва'
          })
        })

        if (response.ok) {
          // Сохраняем токен
          localStorage.setItem('auth_token', response.data.data.token)
          localStorage.setItem('user', JSON.stringify(response.data.data.user))
          
          this.$emit('register-success', response.data.data)
        } else {
          if (response.data.errors) {
            this.errors = response.data.errors
          } else {
            this.error = response.data.message || this.t('RegisterForm_13')
          }
        }
      } catch (err) {
        this.error = this.t('RegisterForm_14')
        console.error('Register error:', err)
      } finally {
        this.loading = false
      }
    }
  }
}
</script> 