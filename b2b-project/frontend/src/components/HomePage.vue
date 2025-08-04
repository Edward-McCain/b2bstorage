<script setup>
import Footer from './Footer.vue'
import HeroSection from './HeroSection.vue'
import { t } from '../locales/index.js'
import { Package, FileText, BarChart3, Search, Smartphone, Shield, Zap, CheckCircle, TrendingUp, Heart, ShoppingCart, FolderOpen, ArrowRight, Star, Clock, Globe } from 'lucide-vue-next'
</script>

<template>
  <div class="min-h-screen bg-white">
    <HeroSection />
    
    <!-- Основные возможности с современным дизайном -->
    <section class="py-24 bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-20">
          <div class="inline-flex items-center rounded-full bg-blue-50 px-4 py-2 text-sm font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10 mb-6">
            <Star class="w-4 h-4 mr-2" />
            <!-- Профессиональное решение -->
            {{ t('HomePage_1') }}
          </div>
          <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">
            <!-- Полный контроль над складскими операциями -->
            {{ t('HomePage_2') }}
          </h2>
          <p class="text-xl text-gray-600 max-w-3xl mx-auto">
            <!-- Профессиональная система складского учета с автоматизацией всех процессов и детальной аналитикой -->
            {{ t('HomePage_3') }}
          </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <!-- Управление товарами -->
          <div class="group relative bg-white/70 backdrop-blur-sm rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-500 p-8 border border-white/20 hover:border-blue-200/50 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-50/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
            <div class="relative text-center">
              <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                <Package class="w-8 h-8 text-white" />
              </div>
              <!-- Управление товарами -->
              <h3 class="text-2xl font-bold mb-4 text-gray-900">{{ t('HomePage_4') }}</h3>
              <p class="text-gray-600 leading-relaxed">
                <!-- Создавайте товары с детальными характеристиками, категориями и изображениями. Отслеживайте остатки на складах в реальном времени с автоматическим расчетом движений. -->
                {{ t('HomePage_5') }}
              </p>
            </div>
          </div>
          
          <!-- Документооборот -->
          <div class="group relative bg-white/70 backdrop-blur-sm rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-500 p-8 border border-white/20 hover:border-green-200/50 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-green-50/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
            <div class="relative text-center">
              <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                <FileText class="w-8 h-8 text-white" />
              </div>
              <!-- Документооборот -->
              <h3 class="text-2xl font-bold mb-4 text-gray-900">{{ t('HomePage_6') }}</h3>
              <p class="text-gray-600 leading-relaxed">
                <!-- Автоматизируйте оприходования, списания, инвентаризации и перемещения товаров. Все документы создаются с проверкой остатков и валидацией данных. -->
                {{ t('HomePage_7') }}
              </p>
            </div>
          </div>
          
          <!-- Аналитика и отчеты -->
          <div class="group relative bg-white/70 backdrop-blur-sm rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-500 p-8 border border-white/20 hover:border-purple-200/50 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-purple-50/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
            <div class="relative text-center">
              <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                <BarChart3 class="w-8 h-8 text-white" />
              </div>
              <!-- Аналитика и отчеты -->
              <h3 class="text-2xl font-bold mb-4 text-gray-900">{{ t('HomePage_8') }}</h3>
              <p class="text-gray-600 leading-relaxed">
                <!-- Детальная аналитика движения товаров, оборачиваемости и эффективности операций. Визуальные графики и экспорт данных в Excel. -->
                {{ t('HomePage_9') }}
              </p>
            </div>
          </div>
          
          <!-- Многопользовательский доступ -->
          <div class="group relative bg-white/70 backdrop-blur-sm rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-500 p-8 border border-white/20 hover:border-orange-200/50 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-orange-50/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
            <div class="relative text-center">
              <div class="w-16 h-16 bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                <Search class="w-8 h-8 text-white" />
              </div>
              <!-- Быстрый поиск -->
              <h3 class="text-2xl font-bold mb-4 text-gray-900">{{ t('HomePage_10') }}</h3>
              <p class="text-gray-600 leading-relaxed">
                <!-- Мгновенный поиск товаров по названию, артикулу или категории. Фильтрация по складам, остаткам и датам с сохранением настроек. -->
                {{ t('HomePage_11') }}
              </p>
            </div>
          </div>
          
          <!-- Мобильный доступ -->
          <div class="group relative bg-white/70 backdrop-blur-sm rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-500 p-8 border border-white/20 hover:border-indigo-200/50 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-indigo-50/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
            <div class="relative text-center">
              <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                <Smartphone class="w-8 h-8 text-white" />
              </div>
              <!-- Мобильный доступ -->
              <h3 class="text-2xl font-bold mb-4 text-gray-900">{{ t('HomePage_12') }}</h3>
              <p class="text-gray-600 leading-relaxed">
                <!-- Адаптивный интерфейс для работы с любого устройства. Удобный доступ к системе с планшетов и смартфонов. -->
                {{ t('HomePage_13') }}
              </p>
            </div>
          </div>
          
          <!-- Безопасность -->
          <div class="group relative bg-white/70 backdrop-blur-sm rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-500 p-8 border border-white/20 hover:border-red-200/50 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-red-50/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
            <div class="relative text-center">
              <div class="w-16 h-16 bg-gradient-to-br from-red-500 to-red-600 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                <Shield class="w-8 h-8 text-white" />
              </div>
              <!-- Безопасность данных -->
              <h3 class="text-2xl font-bold mb-4 text-gray-900">{{ t('HomePage_14') }}</h3>
              <p class="text-gray-600 leading-relaxed">
                <!-- Многоуровневая защита информации, регулярное резервное копирование и полная изоляция данных между пользователями. -->
                {{ t('HomePage_15') }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Статистика с современным дизайном -->
    <section class="py-24 bg-gradient-to-br from-gray-900 via-blue-900 to-indigo-900 relative overflow-hidden">
      <div class="absolute inset-0 bg-gradient-to-br from-white/5 to-transparent opacity-20"></div>
      <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
          <div class="inline-flex items-center rounded-full bg-white/10 px-4 py-2 text-sm font-medium text-white ring-1 ring-inset ring-white/20 mb-6">
            <Globe class="w-4 h-4 mr-2" />
            <!-- Доверяют тысячи компаний -->
            {{ t('HomePage_16') }}
          </div>
          <h2 class="text-4xl md:text-5xl font-bold text-white mb-6">
            <!-- Наша система помогает бизнесу расти и развиваться -->
            {{ t('HomePage_17') }}
          </h2>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
          <div class="text-center group">
            <div class="relative">
              <div class="absolute inset-0 bg-gradient-to-r from-blue-400 to-purple-400 rounded-full blur-xl opacity-20 group-hover:opacity-40 transition-opacity duration-300"></div>
              <div class="relative bg-white/10 backdrop-blur-sm rounded-2xl p-6 border border-white/20">
                <div class="text-4xl md:text-5xl font-bold text-blue-400 mb-2">10,000+</div>
                <!-- Активных компаний -->
                <div class="text-gray-300 text-lg">{{ t('HomePage_18') }}</div>
              </div>
            </div>
          </div>
          <div class="text-center group">
            <div class="relative">
              <div class="absolute inset-0 bg-gradient-to-r from-green-400 to-blue-400 rounded-full blur-xl opacity-20 group-hover:opacity-40 transition-opacity duration-300"></div>
              <div class="relative bg-white/10 backdrop-blur-sm rounded-2xl p-6 border border-white/20">
                <div class="text-4xl md:text-5xl font-bold text-green-400 mb-2">1M+</div>
                <!-- Товаров в системе -->
                <div class="text-gray-300 text-lg">{{ t('HomePage_19') }}</div>
              </div>
            </div>
          </div>
          <div class="text-center group">
            <div class="relative">
              <div class="absolute inset-0 bg-gradient-to-r from-purple-400 to-pink-400 rounded-full blur-xl opacity-20 group-hover:opacity-40 transition-opacity duration-300"></div>
              <div class="relative bg-white/10 backdrop-blur-sm rounded-2xl p-6 border border-white/20">
                <div class="text-4xl md:text-5xl font-bold text-purple-400 mb-2">12000+</div>
                <!-- Складов -->
                <div class="text-gray-300 text-lg">{{ t('HomePage_20') }}</div>
              </div>
            </div>
          </div>
          <div class="text-center group">
            <div class="relative">
              <div class="absolute inset-0 bg-gradient-to-r from-orange-400 to-red-400 rounded-full blur-xl opacity-20 group-hover:opacity-40 transition-opacity duration-300"></div>
              <div class="relative bg-white/10 backdrop-blur-sm rounded-2xl p-6 border border-white/20">
                <div class="text-4xl md:text-5xl font-bold text-orange-400 mb-2">24/7</div>
                <!-- Техподдержка -->
                <div class="text-gray-300 text-lg">{{ t('HomePage_21') }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Преимущества с современным дизайном -->
    <section class="py-24 bg-gradient-to-br from-white via-gray-50 to-blue-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-20">
          <div class="inline-flex items-center rounded-full bg-blue-50 px-4 py-2 text-sm font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10 mb-6">
            <CheckCircle class="w-4 h-4 mr-2" />
            <!-- Ключевые преимущества -->
            {{ t('HomePage_22') }}
          </div>
          <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">
            <!-- Почему выбирают B2B SKLAD? -->
            {{ t('HomePage_23') }}
          </h2>
          <p class="text-xl text-gray-600 max-w-3xl mx-auto">
            <!-- Ключевые преимущества, которые делают нашу систему незаменимой для вашего бизнеса -->
            {{ t('HomePage_24') }}
          </p>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
          <div class="space-y-8">
            <div class="group flex items-start gap-6 p-6 rounded-2xl hover:bg-white/50 transition-all duration-300">
              <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                <Zap class="w-6 h-6 text-white" />
              </div>
              <div>
                <!-- Мгновенный старт -->
                <h3 class="text-xl font-bold mb-3 text-gray-900">{{ t('HomePage_25') }}</h3>
                <p class="text-gray-600 leading-relaxed">
                  <!-- Настройка и запуск системы занимает всего несколько минут. Не требует сложной подготовки или обучения персонала. -->
                  {{ t('HomePage_26') }}
                </p>
              </div>
            </div>
            
            <div class="group flex items-start gap-6 p-6 rounded-2xl hover:bg-white/50 transition-all duration-300">
              <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                <CheckCircle class="w-6 h-6 text-white" />
              </div>
              <div>
                <!-- Автоматизация процессов -->
                <h3 class="text-xl font-bold mb-3 text-gray-900">{{ t('HomePage_27') }}</h3>
                <p class="text-gray-600 leading-relaxed">
                  <!-- Автоматический расчет остатков, проверка достаточности товаров и предотвращение ошибок в документах. -->
                  {{ t('HomePage_28') }}
                </p>
              </div>
            </div>
            
            <div class="group flex items-start gap-6 p-6 rounded-2xl hover:bg-white/50 transition-all duration-300">
              <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                <TrendingUp class="w-6 h-6 text-white" />
              </div>
              <div>
                <!-- Масштабируемость -->
                <h3 class="text-xl font-bold mb-3 text-gray-900">{{ t('HomePage_29') }}</h3>
                <p class="text-gray-600 leading-relaxed">
                  <!-- Система растет вместе с вашим бизнесом. Поддерживает неограниченное количество товаров, складов и пользователей. -->
                  {{ t('HomePage_30') }}
                </p>
              </div>
            </div>
            
            <div class="group flex items-start gap-6 p-6 rounded-2xl hover:bg-white/50 transition-all duration-300">
              <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                <Heart class="w-6 h-6 text-white" />
              </div>
              <div>
                <!-- Интуитивный интерфейс -->
                <h3 class="text-xl font-bold mb-3 text-gray-900">{{ t('HomePage_31') }}</h3>
                <p class="text-gray-600 leading-relaxed">
                  <!-- Современный и понятный интерфейс, который не требует специального обучения. Работать в системе может любой сотрудник. -->
                  {{ t('HomePage_32') }}
                </p>
              </div>
            </div>
          </div>
          
          <div class="relative">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-500 to-purple-600 rounded-3xl blur-xl opacity-20"></div>
            <div class="relative bg-gradient-to-br from-blue-500 to-purple-600 rounded-3xl p-8 text-white shadow-2xl">
              <div class="text-center">
                <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-6 backdrop-blur-sm">
                  <BarChart3 class="w-8 h-8 text-white" />
                </div>
                <!-- Реальная экономия -->
                <h3 class="text-2xl font-bold mb-4">{{ t('HomePage_33') }}</h3>
                <div class="space-y-4 text-lg">
                  <div class="flex justify-between items-center p-3 bg-white/10 rounded-xl backdrop-blur-sm">
                    <!-- Время на создание товара -->
                    <span>{{ t('HomePage_34') }}</span>
                    <span class="font-bold text-green-300">-70%</span>
                  </div>
                  <div class="flex justify-between items-center p-3 bg-white/10 rounded-xl backdrop-blur-sm">
                    <!-- Ошибки в документах -->
                    <span>{{ t('HomePage_35') }}</span>
                    <span class="font-bold text-green-300">-90%</span>
                  </div>
                  <div class="flex justify-between items-center p-3 bg-white/10 rounded-xl backdrop-blur-sm">
                    <!-- Время на инвентаризацию -->
                    <span>{{ t('HomePage_36') }}</span>
                    <span class="font-bold text-green-300">-60%</span>
                  </div>
                  <div class="flex justify-between items-center p-3 bg-white/10 rounded-xl backdrop-blur-sm">
                    <!-- Потери товаров -->
                    <span>{{ t('HomePage_37') }}</span>
                    <span class="font-bold text-green-300">-50%</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Экосистема B2B Global с современным дизайном -->
    <section class="py-24 bg-gradient-to-br from-gray-50 via-white to-blue-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-20">
          <div class="inline-flex items-center rounded-full bg-blue-50 px-4 py-2 text-sm font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10 mb-6">
            <Globe class="w-4 h-4 mr-2" />
            <!-- Единая экосистема -->
            {{ t('HomePage_38') }}
          </div>
          <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">
            <!-- Экосистема B2B Global -->
            {{ t('HomePage_39') }}
          </h2>
          <p class="text-xl text-gray-600 max-w-3xl mx-auto">
            <!-- Комплексные решения для автоматизации всех бизнес-процессов вашей компании -->
            {{ t('HomePage_40') }}
          </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <!-- B2B Marketplace -->
          <div class="group relative bg-white/70 backdrop-blur-sm rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-500 p-8 border border-white/20 hover:border-blue-200/50 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-50/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
            <div class="relative text-center">
              <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                <ShoppingCart class="w-8 h-8 text-white" />
              </div>
              <!-- B2B Marketplace -->
              <h3 class="text-2xl font-bold mb-4 text-gray-900">{{ t('HomePage_41') }}</h3>
              <p class="text-gray-600 mb-6 leading-relaxed">
                <!-- Электронная торговая площадка для B2B взаимодействия между компаниями. Умный поиск товаров, автоматические заказы и интеграция с системами учета. -->
                {{ t('HomePage_42') }}
              </p>
              <div class="inline-flex items-center text-sm text-blue-600 font-medium bg-blue-50 px-3 py-1 rounded-full">
                <Star class="w-4 h-4 mr-1" />
                <!-- Работает на ИИ -->
                {{ t('HomePage_43') }}
              </div>
            </div>
          </div>
          
          <!-- B2B SKLAD -->
          <div class="group relative bg-gradient-to-br from-blue-500 to-blue-600 rounded-3xl shadow-2xl hover:shadow-3xl transition-all duration-500 p-8 text-white overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-white/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
            <div class="relative text-center">
              <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-300 backdrop-blur-sm">
                <Package class="w-8 h-8 text-white" />
              </div>
              <!-- B2B SKLAD -->
              <h3 class="text-2xl font-bold mb-4">{{ t('HomePage_44') }}</h3>
              <p class="mb-6 leading-relaxed opacity-90">
                <!-- Профессиональная система складского учета с полной автоматизацией процессов. Детальная аналитика и контроль остатков в реальном времени. -->
                {{ t('HomePage_45') }}
              </p>
              <div class="inline-flex items-center text-sm font-medium bg-white/20 px-3 py-1 rounded-full backdrop-blur-sm">
                <Clock class="w-4 h-4 mr-1" />
                <!-- Текущая система -->
                {{ t('HomePage_46') }}
              </div>
            </div>
          </div>
          
          <!-- B2B Doc -->
          <div class="group relative bg-white/70 backdrop-blur-sm rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-500 p-8 border border-white/20 hover:border-green-200/50 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-green-50/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
            <div class="relative text-center">
              <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                <FolderOpen class="w-8 h-8 text-white" />
              </div>
              <!-- B2B Doc -->
              <h3 class="text-2xl font-bold mb-4 text-gray-900">{{ t('HomePage_47') }}</h3>
              <p class="text-gray-600 mb-6 leading-relaxed">
                <!-- Система документооборота с автоматическим распознаванием документов и умной обработкой контрактов, счетов и накладных. -->
                {{ t('HomePage_48') }}
              </p>
              <div class="inline-flex items-center text-sm text-green-600 font-medium bg-green-50 px-3 py-1 rounded-full">
                <Star class="w-4 h-4 mr-1" />
                <!-- Работает на ИИ -->
                {{ t('HomePage_43') }}
              </div>
            </div>
          </div>
        </div>
        
        <div class="text-center mt-16">
          <div class="bg-white/70 backdrop-blur-sm rounded-3xl shadow-xl p-8 max-w-4xl mx-auto border border-white/20">
            <!-- Единая экосистема -->
            <h3 class="text-2xl font-bold mb-4 text-gray-900">{{ t('HomePage_38') }}</h3>
            <p class="text-lg text-gray-600 leading-relaxed">
              <!-- Все системы экосистемы B2B Global работают в едином информационном пространстве, обеспечивая полную интеграцию бизнес-процессов от закупок до продаж. Искусственный интеллект автоматизирует рутинные задачи и повышает эффективность работы. -->
              {{ t('HomePage_49') }}
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA секция с современным дизайном -->
    <section class="py-24 bg-gradient-to-br from-blue-600 via-purple-600 to-indigo-600 relative overflow-hidden">
      <div class="absolute inset-0 bg-gradient-to-br from-white/10 to-transparent opacity-30"></div>
      <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="inline-flex items-center rounded-full bg-white/10 px-4 py-2 text-sm font-medium text-white ring-1 ring-inset ring-white/20 mb-6">
          <Zap class="w-4 h-4 mr-2" />
          <!-- Готовы к оптимизации -->
          {{ t('HomePage_50') }}
        </div>
        <h2 class="text-4xl md:text-5xl font-bold text-white mb-6">
          <!-- Готовы оптимизировать складские процессы? -->
          {{ t('HomePage_51') }}
        </h2>
        <p class="text-xl text-blue-100 mb-8 max-w-2xl mx-auto">
          <!-- Присоединяйтесь к тысячам компаний, которые уже используют B2B SKLAD для эффективного управления складом -->
          {{ t('HomePage_52') }}
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
          <a href="/auth" class="group inline-flex items-center justify-center px-8 py-4 bg-white text-blue-600 font-semibold rounded-xl hover:bg-gray-100 transition-all duration-300 shadow-lg hover:shadow-xl">
            <!-- Начать бесплатно -->
            {{ t('HeroSection_4') }}
            <ArrowRight class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform duration-300" />
          </a>
        </div>
      </div>
    </section>

    <!-- <Footer /> -->
  </div>
</template>

<style scoped>
/* Дополнительные стили для главной страницы */
</style> 