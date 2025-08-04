import './assets/main.css'
import 'toastr/build/toastr.min.css'
import 'vue-multiselect/dist/vue-multiselect.css'

import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import toastr from 'toastr'
import VueApexCharts from 'vue3-apexcharts'
import { initLocaleFromUserData } from './locales/index.js'

// Импортируем Vue Datepicker
import VueDatePicker from '@vuepic/vue-datepicker'
import '@vuepic/vue-datepicker/dist/main.css'
import LocalizedDatePicker from './components/LocalizedDatePicker.vue'

// Настройка toastr
toastr.options = {
  closeButton: true,
  progressBar: true,
  timeOut: 5000, // 5 секунд
  extendedTimeOut: 1000,
  preventDuplicates: true,
  newestOnTop: true,
  positionClass: 'toast-top-right'
}

// Делаем toastr доступным глобально
window.toastr = toastr

const app = createApp(App)

// Инициализация локализации после создания приложения Vue
try {
  initLocaleFromUserData()
} catch (error) {
  console.error('Error initializing locale:', error)
}
app.use(router)
app.component('apexchart', VueApexCharts)
app.component('VueDatePicker', VueDatePicker)
app.component('LocalizedDatePicker', LocalizedDatePicker)
app.mount('#app')
