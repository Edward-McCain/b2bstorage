import './assets/main.css'
import 'toastr/build/toastr.min.css'
import 'vue-multiselect/dist/vue-multiselect.css'

import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import toastr from 'toastr'
import VueApexCharts from 'vue3-apexcharts'

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
app.use(router)
app.component('apexchart', VueApexCharts)
app.mount('#app')
