<template>
  <div class="flex flex-col justify-center px-6 py-32 lg:px-8 bg-gray-50">
    <div class="sm:mx-auto sm:w-full sm:max-w-sm">
      <h2 class="mt-10 text-center text-2xl/9 font-bold tracking-tight text-gray-900">Войти в аккаунт</h2>
    </div>

    <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
      <form class="space-y-6" @submit.prevent="handleLogin">
        <div>
          <label for="email" class="block text-sm/6 font-medium text-gray-900">Email</label>
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
              placeholder="Введите ваш email"
            />
          </div>
          <p v-if="errors.email" class="mt-1 text-sm text-red-600">{{ errors.email[0] }}</p>
        </div>

        <div>
          <div class="flex items-center justify-between">
            <label for="password" class="block text-sm/6 font-medium text-gray-900">Пароль</label>
            <div class="text-sm">
              <a href="#" class="font-semibold text-primary hover:text-primary/80">Забыли пароль?</a>
            </div>
          </div>
          <div class="mt-2">
            <input 
              type="password" 
              name="password" 
              id="password" 
              v-model="form.password"
              autocomplete="current-password" 
              required 
              class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-primary sm:text-sm/6"
              :class="{ 'outline-red-500': errors.password }"
              placeholder="Введите ваш пароль"
            />
          </div>
          <p v-if="errors.password" class="mt-1 text-sm text-red-600">{{ errors.password[0] }}</p>
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
            {{ loading ? 'Вход...' : 'Войти' }}
          </button>
        </div>
      </form>

      <p class="mt-10 text-center text-sm/6 text-gray-500">
        Нет аккаунта?
        <button @click="$emit('switch-to-register')" class="font-semibold text-primary cursor-pointer text-blue-700 hover:text-primary/80">Зарегистрироваться</button>
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

export default {
  name: 'LoginForm',
  emits: ['switch-to-register', 'login-success'],
  data() {
    return {
      form: {
        email: '',
        password: '',
        remember: false
      },
      loading: false,
      error: '',
      errors: {}
    }
  },
  methods: {
    async handleLogin() {
      this.loading = true
      this.error = ''
      this.errors = {}

      try {
        const response = await apiRequest('/login', {
          method: 'POST',
          body: JSON.stringify({
            email: this.form.email,
            password: this.form.password
          })
        })

        if (response.ok) {
          // Сохраняем токен
          localStorage.setItem('auth_token', response.data.data.token)
          localStorage.setItem('user', JSON.stringify(response.data.data.user))
          
          this.$emit('login-success', response.data.data)
        } else {
          if (response.data.errors) {
            this.errors = response.data.errors
          } else {
            this.error = response.data.message || 'Ошибка входа'
          }
        }
      } catch (err) {
        this.error = 'Ошибка соединения с сервером'
        console.error('Login error:', err)
      } finally {
        this.loading = false
      }
    }
  }
}
</script> 