<template>
  <div class="min-h-screen bg-white">
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
    
    <Footer />
  </div>
</template>

<script>
import LoginForm from './LoginForm.vue'
import RegisterForm from './RegisterForm.vue'
import Header from './Header.vue'
import Footer from './Footer.vue'
import { useRouter } from 'vue-router'

export default {
  name: 'AuthPage',
  components: {
    LoginForm,
    RegisterForm,
    Header,
    Footer
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
      // Перенаправляем на главную страницу после успешной авторизации
      this.router.push('/')
    }
  }
}
</script> 