<template>
  <div class="relative">
    <!-- Поле ввода даты -->
    <input
      :value="displayValue"
      @click="showCalendar = true"
      @input="handleInput"
      :placeholder="placeholder"
      :disabled="disabled"
      :readonly="true"
      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 transition shadow-sm cursor-pointer"
      :class="{
        'bg-gray-50 text-gray-500 cursor-not-allowed': disabled,
        'bg-white text-gray-700': !disabled
      }"
    />
    
    <!-- Иконка календаря -->
    <div class="absolute right-3 top-1/2 transform -translate-y-1/2 pointer-events-none">
      <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
      </svg>
    </div>

    <!-- Календарный попап -->
    <div
      v-if="showCalendar"
      class="absolute z-50 mt-1 bg-white border border-gray-200 rounded-lg shadow-lg p-4 min-w-[280px]"
      style="top: 100%;"
    >
      <!-- Заголовок календаря -->
      <div class="flex items-center justify-between mb-4">
        <button
          @click="previousMonth"
          class="p-1 hover:bg-gray-100 rounded transition-colors"
        >
          <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
          </svg>
        </button>
        
        <h3 class="text-sm font-medium text-gray-900">
          {{ currentMonthName }} {{ currentYear }}
        </h3>
        
        <button
          @click="nextMonth"
          class="p-1 hover:bg-gray-100 rounded transition-colors"
        >
          <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
          </svg>
        </button>
      </div>

      <!-- Дни недели -->
      <div class="grid grid-cols-7 gap-1 mb-2">
        <div
          v-for="day in weekDaysLocalized"
          :key="day"
          class="text-xs font-medium text-gray-500 text-center py-1"
        >
          {{ day }}
        </div>
      </div>

      <!-- Дни месяца -->
      <div class="grid grid-cols-7 gap-1">
        <div
          v-for="day in calendarDays"
          :key="day.date"
          @click="selectDate(day.date)"
          class="text-sm text-center py-2 px-1 rounded cursor-pointer transition-colors"
          :class="{
            'text-gray-400': !day.isCurrentMonth,
            'text-gray-700 hover:bg-gray-100': day.isCurrentMonth && !day.isSelected,
            'bg-blue-500 text-white hover:bg-blue-600': day.isSelected,
            'bg-blue-100 text-blue-700': day.isToday && !day.isSelected
          }"
        >
          {{ day.day }}
        </div>
      </div>

      <!-- Кнопки действий -->
      <div class="flex justify-between mt-4 pt-3 border-t border-gray-200">
        <button
          @click="clearDate"
          class="text-sm text-blue-600 hover:text-blue-700 transition-colors"
        >
          Удалить
        </button>
        <button
          @click="selectToday"
          class="text-sm text-blue-600 hover:text-blue-700 transition-colors"
        >
          Сегодня
        </button>
      </div>
    </div>

    <!-- Оверлей для закрытия календаря -->
    <div
      v-if="showCalendar"
      @click="showCalendar = false"
      class="fixed inset-0 z-40"
    ></div>
  </div>
</template>

<script>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue'
import { currentLocale } from '../locales/index.js'

export default {
  name: 'CustomDatePicker',
  props: {
    modelValue: {
      type: String,
      default: ''
    },
    placeholder: {
      type: String,
      default: 'Выберите дату'
    },
    disabled: {
      type: Boolean,
      default: false
    },
    format: {
      type: String,
      default: 'YYYY-MM-DD'
    }
  },
  emits: ['update:modelValue'],
  setup(props, { emit }) {
    const showCalendar = ref(false)
    const currentDate = ref(new Date())
    const selectedDate = ref(props.modelValue ? new Date(props.modelValue) : null)

    // Локализованные дни недели
    const weekDaysLocalized = computed(() => {
      const weekDaysMap = {
        ru: ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'],
        en: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        uz: ['Dush', 'Sesh', 'Chor', 'Pay', 'Jum', 'Shan', 'Yak'],
        china: ['周一', '周二', '周三', '周四', '周五', '周六', '周日']
      }
      return weekDaysMap[currentLocale.value] || weekDaysMap.ru
    })

    // Локализованные названия месяцев
    const monthNamesLocalized = computed(() => {
      const monthNamesMap = {
        ru: ['январь', 'февраль', 'март', 'апрель', 'май', 'июнь', 'июль', 'август', 'сентябрь', 'октябрь', 'ноябрь', 'декабрь'],
        en: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
        uz: ['Yanvar', 'Fevral', 'Mart', 'Aprel', 'May', 'Iyun', 'Iyul', 'Avgust', 'Sentyabr', 'Oktyabr', 'Noyabr', 'Dekabr'],
        china: ['一月', '二月', '三月', '四月', '五月', '六月', '七月', '八月', '九月', '十月', '十一月', '十二月']
      }
      return monthNamesMap[currentLocale.value] || monthNamesMap.ru
    })

    // Локализованные тексты кнопок
    const buttonTextsLocalized = computed(() => {
      const buttonTextsMap = {
        ru: { clear: 'Удалить', today: 'Сегодня', placeholder: 'Выберите дату' },
        en: { clear: 'Clear', today: 'Today', placeholder: 'Select date' },
        uz: { clear: 'Tozalash', today: 'Bugun', placeholder: 'Sanani tanlang' },
        china: { clear: '清除', today: '今天', placeholder: '选择日期' }
      }
      return buttonTextsMap[currentLocale.value] || buttonTextsMap.ru
    })

    // Вычисляемые свойства
    const currentYear = computed(() => currentDate.value.getFullYear())
    const currentMonth = computed(() => currentDate.value.getMonth())
    const currentMonthName = computed(() => monthNamesLocalized.value[currentMonth.value])

    const displayValue = computed(() => {
      if (!selectedDate.value) return ''
      return formatDate(selectedDate.value, props.format)
    })

    const calendarDays = computed(() => {
      const year = currentYear.value
      const month = currentMonth.value
      
      // Первый день месяца
      const firstDay = new Date(year, month, 1)
      // Последний день месяца
      const lastDay = new Date(year, month + 1, 0)
      
      // День недели первого дня (0 = воскресенье, 1 = понедельник, ...)
      const firstDayOfWeek = firstDay.getDay()
      // Корректируем для начала недели с понедельника
      const startOffset = firstDayOfWeek === 0 ? 6 : firstDayOfWeek - 1
      
      const days = []
      
      // Добавляем дни предыдущего месяца
      for (let i = startOffset - 1; i >= 0; i--) {
        const date = new Date(year, month, -i)
        days.push({
          date: date,
          day: date.getDate(),
          isCurrentMonth: false,
          isSelected: selectedDate.value && isSameDate(date, selectedDate.value),
          isToday: isSameDate(date, new Date())
        })
      }
      
      // Добавляем дни текущего месяца
      for (let i = 1; i <= lastDay.getDate(); i++) {
        const date = new Date(year, month, i)
        days.push({
          date: date,
          day: i,
          isCurrentMonth: true,
          isSelected: selectedDate.value && isSameDate(date, selectedDate.value),
          isToday: isSameDate(date, new Date())
        })
      }
      
      // Добавляем дни следующего месяца
      const remainingDays = 42 - days.length // 6 недель * 7 дней
      for (let i = 1; i <= remainingDays; i++) {
        const date = new Date(year, month + 1, i)
        days.push({
          date: date,
          day: date.getDate(),
          isCurrentMonth: false,
          isSelected: selectedDate.value && isSameDate(date, selectedDate.value),
          isToday: isSameDate(date, new Date())
        })
      }
      
      return days
    })

    // Методы
    const formatDate = (date, format) => {
      const year = date.getFullYear()
      const month = String(date.getMonth() + 1).padStart(2, '0')
      const day = String(date.getDate()).padStart(2, '0')
      
      return format
        .replace('YYYY', year)
        .replace('MM', month)
        .replace('DD', day)
    }

    const isSameDate = (date1, date2) => {
      return date1.getFullYear() === date2.getFullYear() &&
             date1.getMonth() === date2.getMonth() &&
             date1.getDate() === date2.getDate()
    }

    const selectDate = (date) => {
      selectedDate.value = date
      emit('update:modelValue', formatDate(date, props.format))
      showCalendar.value = false
    }

    const selectToday = () => {
      const today = new Date()
      selectDate(today)
    }

    const clearDate = () => {
      selectedDate.value = null
      emit('update:modelValue', '')
      showCalendar.value = false
    }

    const previousMonth = () => {
      currentDate.value = new Date(currentYear.value, currentMonth.value - 1, 1)
    }

    const nextMonth = () => {
      currentDate.value = new Date(currentYear.value, currentMonth.value + 1, 1)
    }

    const handleInput = () => {
      // Обработка ввода не нужна, так как поле readonly
    }

    // Обработка клика вне календаря
    const handleClickOutside = (event) => {
      if (!event.target.closest('.custom-date-picker')) {
        showCalendar.value = false
      }
    }

    // Слушатели
    watch(() => props.modelValue, (newValue) => {
      if (newValue) {
        selectedDate.value = new Date(newValue)
      } else {
        selectedDate.value = null
      }
    })

    onMounted(() => {
      document.addEventListener('click', handleClickOutside)
    })

    onUnmounted(() => {
      document.removeEventListener('click', handleClickOutside)
    })

    return {
      showCalendar,
      currentDate,
      selectedDate,
      weekDays,
      currentYear,
      currentMonth,
      currentMonthName,
      displayValue,
      calendarDays,
      selectDate,
      selectToday,
      clearDate,
      previousMonth,
      nextMonth,
      handleInput
    }
  }
}
</script>

<style scoped>
/* Дополнительные стили для кастомного календаря */
.custom-date-picker {
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

/* Анимация появления календаря */
.calendar-enter-active,
.calendar-leave-active {
  transition: opacity 0.2s ease-in-out, transform 0.2s ease-in-out;
}

.calendar-enter-from,
.calendar-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}
</style> 