<script setup>
import Footer from './Footer.vue'
import HeroSection from './HeroSection.vue'
</script>

<template>
  <div class="min-h-screen bg-white transition-colors">
    <HeroSection />
    
    <!-- Возможности системы -->
    <section id="features" class="py-20 bg-white">
      <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-16">
          <h2 class="text-4xl font-bold mb-4 text-gray-800">Возможности системы</h2>
          <p class="text-xl text-gray-600">Все необходимое для эффективного управления складом</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <!-- Карточка 1 -->
          <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-shadow p-6 border border-gray-100">
            <div class="text-center">
              <div class="text-4xl mb-4">📦</div>
              <h3 class="text-xl font-semibold mb-4 text-gray-800">Управление товарами</h3>
              <p class="text-gray-600">Полный контроль над товарными запасами, категориями, характеристиками и остатками на складе</p>
            </div>
          </div>
          
          <!-- Карточка 2 -->
          <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-shadow p-6 border border-gray-100">
            <div class="text-center">
              <div class="text-4xl mb-4">📊</div>
              <h3 class="text-xl font-semibold mb-4 text-gray-800">Аналитика и отчеты</h3>
              <p class="text-gray-600">Детальная аналитика движения товаров, оборачиваемости и эффективности складских операций</p>
            </div>
          </div>
          
          <!-- Карточка 3 -->
          <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-shadow p-6 border border-gray-100">
            <div class="text-center">
              <div class="text-4xl mb-4">🔄</div>
              <h3 class="text-xl font-semibold mb-4 text-gray-800">Автоматизация процессов</h3>
              <p class="text-gray-600">Автоматизация приходов, расходов, инвентаризации и других складских операций</p>
            </div>
          </div>
          
          <!-- Карточка 4 -->
          <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-shadow p-6 border border-gray-100">
            <div class="text-center">
              <div class="text-4xl mb-4">👥</div>
              <h3 class="text-xl font-semibold mb-4 text-gray-800">Многопользовательский доступ</h3>
              <p class="text-gray-600">Гибкая система ролей и прав доступа для разных сотрудников и подразделений</p>
            </div>
          </div>
          
          <!-- Карточка 5 -->
          <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-shadow p-6 border border-gray-100">
            <div class="text-center">
              <div class="text-4xl mb-4">🔗</div>
              <h3 class="text-xl font-semibold mb-4 text-gray-800">Интеграции</h3>
              <p class="text-gray-600">Готовые интеграции с другими системами экосистемы B2B Global и внешними сервисами</p>
            </div>
          </div>
          
          <!-- Карточка 6 -->
          <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-shadow p-6 border border-gray-100">
            <div class="text-center">
              <div class="text-4xl mb-4">📱</div>
              <h3 class="text-xl font-semibold mb-4 text-gray-800">Мобильный доступ</h3>
              <p class="text-gray-600">Удобный доступ к системе с мобильных устройств для работы в любом месте</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Экосистема B2B Global -->
    <section id="ecosystem" class="py-20 bg-gray-50">
      <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-16">
          <h2 class="text-4xl font-bold mb-4 text-gray-800">Экосистема B2B Global</h2>
          <p class="text-xl text-gray-600">Комплексные решения для вашего бизнеса</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <!-- B2B Marketplace -->
          <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
            <div class="text-center">
              <div class="text-4xl mb-4">🛒</div>
              <h3 class="text-xl font-semibold mb-4 text-primary">B2B Marketplace</h3>
              <p class="mb-4 text-gray-600">Электронная торговая площадка для B2B взаимодействия между компаниями</p>
            </div>
          </div>
          
          <!-- B2B Storage -->
          <div class="bg-primary rounded-xl shadow-lg p-6">
            <div class="text-center">
              <div class="text-4xl mb-4">📦</div>
              <h3 class="text-xl font-semibold mb-4">B2B Storage</h3>
              <p class="mb-4 opacity-90">Система складского учета для эффективного управления товарными запасами</p>
            </div>
          </div>
          
          <!-- B2B Doc -->
          <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
            <div class="text-center">
              <div class="text-4xl mb-4">📄</div>
              <h3 class="text-xl font-semibold mb-4 text-primary">B2B Doc</h3>
              <p class="mb-4 text-gray-600">Система документооборота для автоматизации бизнес-процессов</p>
            </div>
          </div>
        </div>
        
        <div class="text-center mt-12">
          <p class="text-lg text-gray-600 mb-6 max-w-3xl mx-auto">
            Все системы экосистемы B2B Global работают в едином информационном пространстве, 
            обеспечивая полную интеграцию бизнес-процессов
          </p>
        </div>
      </div>
    </section>

    <!-- Преимущества -->
    <section class="py-20 bg-white">
      <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-16">
          <h2 class="text-4xl font-bold mb-4 text-gray-800">Почему выбирают B2B Storage?</h2>
          <p class="text-xl text-gray-600">Ключевые преимущества нашей системы</p>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
          <div>
            <div class="space-y-6">
              <div class="flex items-start gap-4">
                <div class="w-12 h-12 bg-primary/20 rounded-lg flex items-center justify-center flex-shrink-0">
                  <span class="text-2xl">⚡</span>
                </div>
                <div>
                  <h3 class="text-xl font-semibold mb-2 text-gray-800">Быстрый старт</h3>
                  <p class="text-gray-600">Настройка и запуск системы занимает минимум времени, не требует сложной подготовки</p>
                </div>
              </div>
              
              <div class="flex items-start gap-4">
                <div class="w-12 h-12 bg-primary/20 rounded-lg flex items-center justify-center flex-shrink-0">
                  <span class="text-2xl">🔒</span>
                </div>
                <div>
                  <h3 class="text-xl font-semibold mb-2 text-gray-800">Безопасность данных</h3>
                  <p class="text-gray-600">Многоуровневая защита информации и регулярное резервное копирование</p>
                </div>
              </div>
              
              <div class="flex items-start gap-4">
                <div class="w-12 h-12 bg-primary/20 rounded-lg flex items-center justify-center flex-shrink-0">
                  <span class="text-2xl">📈</span>
                </div>
                <div>
                  <h3 class="text-xl font-semibold mb-2 text-gray-800">Масштабируемость</h3>
                  <p class="text-gray-600">Система растет вместе с вашим бизнесом, поддерживая любые объемы данных</p>
                </div>
              </div>
            </div>
          </div>
          
          <div class="bg-gradient-to-br rounded-2xl p-8">
            <div class="text-center">
              <div class="text-6xl mb-4">📊</div>
              <h3 class="text-2xl font-bold mb-4 text-gray-800">Статистика использования</h3>
              <div class="grid grid-cols-2 gap-6">
                <div class="text-center">
                  <div class="text-3xl font-bold text-primary">10000+</div>
                  <div class="text-sm text-gray-600">Компаний</div>
                </div>
                <div class="text-center">
                  <div class="text-3xl font-bold text-primary">24/7</div>
                  <div class="text-sm text-gray-600">Поддержка</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Футер сайта -->
    <Footer />
  </div>
</template>

<style scoped>
/* Дополнительные стили для главной страницы */
</style> 