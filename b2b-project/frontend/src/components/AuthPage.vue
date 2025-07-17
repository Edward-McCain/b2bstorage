<template>
  <div class="bg-white">
    <Header />
    
    <main class="flex-1">
      <LoginForm 
        v-if="currentForm === 'login'"
        @switch-to-register="currentForm = 'register'"
        @login-success="handleAuthSuccess"
      />
      <RegisterForm 
        v-else
        @switch-to-login="currentForm = 'login'"
        @register-success="handleAuthSuccess"
      />
    </main>
  </div>
</template>

<script>
import LoginForm from './LoginForm.vue'
import RegisterForm from './RegisterForm.vue'
import Header from './Header.vue'
import { useRouter } from 'vue-router'

export default {
  name: 'AuthPage',
  components: {
    LoginForm,
    RegisterForm,
    Header
  },
  setup() {
    const router = useRouter()
    
    return {
      router
    }
  },
  data() {
    return {
      currentForm: 'login'
    }
  },
  methods: {
    handleAuthSuccess(data) {
      // Проверяем роль пользователя
      const user = data.user || JSON.parse(localStorage.getItem('user') || '{}')
      
      if (user.role === 1) {
        // Администратор - перенаправляем на админ панель
        window.location.href = '/admin'
      } else {
        // Обычный пользователь - перенаправляем на страницу товаров
        window.location.href = '/products'
      }
    }
  }
}
</script> 