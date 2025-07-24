<template>
  <div class="min-h-screen bg-gray-50 pt-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      
      <!-- Основной контент -->
      <div class="flex gap-8">
        <!-- Левая колонка - навигация (только для ПК) -->
        <div class="hidden lg:block w-64 flex-shrink-0">
          <div class="sticky top-24">
            <nav class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
              <h3 class="text-sm font-semibold text-gray-900 mb-4">Навигация</h3>
              <ul class="space-y-2">
                <li>
                  <button 
                    @click="scrollToSection('personal')"
                    class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:text-gray-900 hover:bg-gray-50 rounded-md transition-colors flex items-center gap-2"
                    :class="{ 'bg-blue-50 text-blue-700': activeSection === 'personal' }"
                  >
                    <User class="w-4 h-4" />
                    Личные данные
                  </button>
                </li>
                <li>
                  <button 
                    @click="scrollToSection('company')"
                    class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:text-gray-900 hover:bg-gray-50 rounded-md transition-colors flex items-center gap-2"
                    :class="{ 'bg-blue-50 text-blue-700': activeSection === 'company' }"
                  >
                    <Building class="w-4 h-4" />
                    Данные компании
                  </button>
                </li>
                <li>
                  <button 
                    @click="scrollToSection('password')"
                    class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:text-gray-900 hover:bg-gray-50 rounded-md transition-colors flex items-center gap-2"
                    :class="{ 'bg-blue-50 text-blue-700': activeSection === 'password' }"
                  >
                    <Lock class="w-4 h-4" />
                    Смена пароля
                  </button>
                </li>
                <li>
                  <button 
                    @click="scrollToSection('custom-product-fields')"
                    class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:text-gray-900 hover:bg-gray-50 rounded-md transition-colors flex items-center gap-2"
                    :class="{ 'bg-blue-50 text-blue-700': activeSection === 'custom-product-fields' }"
                  >
                    <List class="w-4 h-4" />
                    Поля товаров
                  </button>
                </li>
              </ul>
            </nav>
          </div>
        </div>

        <!-- Правая колонка - контент -->
        <div class="flex-1 space-y-8">
          <!-- Личные данные -->
          <section id="personal" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center gap-3 mb-6">
              <User class="w-5 h-5 text-blue-600" />
              <h2 class="text-xl font-semibold text-gray-900">Личные данные</h2>
              <button 
                v-if="!loadingSettings"
                @click="detectLocation"
                :disabled="isDetectingLocation"
                class="ml-auto bg-green-600 hover:bg-green-700 disabled:bg-green-400 text-white font-medium px-4 py-2 rounded-lg transition-colors flex items-center gap-2 text-sm" style="display: none" 
              >
                <Loader2 v-if="isDetectingLocation" class="animate-spin h-4 w-4" />
                {{ isDetectingLocation ? 'Определение...' : (locationDetected ? 'Обновить' : 'Автоопределение') }}
              </button>
            </div>
            
            <!-- Прелоадер для личных данных -->
            <div v-if="loadingSettings" class="flex items-center justify-center py-12">
              <div class="text-center">
                <Loader2 class="animate-spin h-8 w-8 text-blue-600 mx-auto mb-4" />
                <p class="text-gray-600 text-sm">Загрузка личных данных...</p>
              </div>
            </div>
            
            <!-- Основная форма -->
            <form v-else @submit.prevent="savePersonalData" class="space-y-6">
              <!-- Блок аватара -->
              <div class="mb-8">
                <label class="block text-sm font-medium text-gray-700 mb-4">Фото профиля</label>
                <div class="flex items-center gap-6">
                  <!-- Аватар -->
                  <div class="relative group">
                    <div class="w-24 h-24 rounded-full overflow-hidden bg-gray-100 border-2 border-gray-200">
                      <img 
                        v-if="avatarUrl" 
                        :src="getFileUrl(avatarUrl)" 
                        alt="Аватар" 
                        class="w-full h-full object-cover"
                        @error="handleAvatarError"
                        @load="handleAvatarLoad"
                      />
                      <div v-else class="w-full h-full flex items-center justify-center">
                        <User class="w-8 h-8 text-gray-400" />
                      </div>
                    </div>
                    <!-- Оверлей при наведении -->
                    <div class="absolute inset-0 bg-black bg-opacity-50 rounded-full opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center cursor-pointer text-center" @click="openAvatarUpload">
                      <span class="text-white text-xs font-medium">Обновить аватар</span>
                    </div>
                  </div>
                  
                  <!-- Кнопка загрузки -->
                  <div>
                    <button 
                      type="button"
                      @click="openAvatarUpload"
                      class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg transition-colors text-sm"
                    >
                      Загрузить фото
                    </button>
                    <p class="text-xs text-gray-500 mt-1">Рекомендуемый размер: 400x400px</p>
                  </div>
                </div>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Имя -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Имя *</label>
                  <input 
                    v-model="personalData.firstName" 
                    type="text" 
                    required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent"
                    placeholder="Введите ваше имя"
                  />
                </div>

                <!-- Должность -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Должность</label>
                  <input 
                    v-model="personalData.position" 
                    type="text" 
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent"
                    placeholder="Введите должность"
                  />
                </div>

                <!-- Телефон -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Телефон *</label>
                  <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                      <button 
                        type="button"
                        @click="showCountrySelect = !showCountrySelect"
                        class="country-select flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700"
                      >
                        <img 
                          :src="selectedCountry.flag" 
                          :alt="selectedCountry.name"
                          class="w-5 h-5 rounded"
                        />
                        <span>{{ selectedCountry.code }}</span>
                        <ChevronDown class="w-4 h-4" />
                      </button>
                    </div>
                    <input 
                      v-model="personalData.phone" 
                      type="tel" 
                      required
                      @input="formatPhoneNumber"
                      @focus="showCountrySelect = false"
                      class="w-full border border-gray-300 rounded-lg pl-20 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent"
                      :placeholder="getPhoneMask(selectedCountry.phone_code).placeholder"
                    />
                  </div>
                  <!-- Выпадающий список стран -->
                  <div v-if="showCountrySelect" class="country-select absolute z-10 mt-1 w-64 bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                    <div class="p-2">
                      <input 
                        v-model="countrySearch" 
                        type="text" 
                        placeholder="Поиск страны..."
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm mb-2"
                      />
                      <div class="space-y-1">
                        <button 
                          v-for="country in filteredCountries" 
                          :key="country.id"
                          @click="selectCountry(country)"
                          class="w-full text-left px-3 py-2 text-sm hover:bg-gray-50 rounded flex items-center gap-2"
                        >
                          <img :src="country.flag" :alt="country.name" class="w-4 h-4 rounded" />
                          <span>{{ country.name }}</span>
                          <span class="text-gray-500 text-xs">+{{ country.phone_code }}</span>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Email -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                  <input 
                    v-model="personalData.email" 
                    type="email" 
                    required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent"
                    placeholder="Введите email"
                  />
                </div>

                <!-- Страна -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Страна</label>
                  <Multiselect
                    v-model="personalData.country"
                    :options="countryOptions"
                    label="label"
                    value="value"
                    :object="true"
                    placeholder="Выберите страну"
                    searchable
                    :search-placeholder="'Поиск страны'"
                    :max-height="400"
                    class="w-full text-sm multiselect-custom"
                  />
                </div>

                <!-- Город -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Город</label>
                  <input 
                    v-model="personalData.city" 
                    type="text" 
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent"
                    placeholder="Введите город"
                  />
                </div>

                <!-- Часовой пояс -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Часовой пояс</label>
                  <Multiselect
                    v-model="personalData.timezone"
                    :options="timezoneOptions"
                    label="label"
                    value="value"
                    :object="true"
                    placeholder="Выберите часовой пояс"
                    searchable
                    :search-placeholder="'Поиск часового пояса'"
                    :max-height="400"
                    class="w-full text-sm multiselect-custom"
                  />
                </div>

                <!-- Валюта -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Валюта</label>
                  <Multiselect
                    v-model="personalData.currency"
                    :options="currencyOptions"
                    label="label"
                    value="value"
                    :object="false"
                    placeholder="Выберите валюту"
                    searchable
                    :search-placeholder="'Поиск валюты'"
                    :max-height="400"
                    class="w-full text-sm multiselect-custom"
                  />
                </div>
              </div>

              <div class="flex justify-end">
                <button 
                  type="submit" 
                  :disabled="savingPersonal"
                  class="bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white font-semibold px-6 py-2 rounded-lg transition-colors flex items-center gap-2 text-sm"
                >
                  <Loader2 v-if="savingPersonal" class="animate-spin h-4 w-4" />
                  {{ savingPersonal ? 'Сохранение...' : 'Сохранить' }}
                </button>
              </div>
            </form>
          </section>

          <!-- Данные компании -->
          <section id="company" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center gap-3 mb-6">
              <Building class="w-5 h-5 text-blue-600" />
              <h2 class="text-xl font-semibold text-gray-900">Данные компании</h2>
            </div>
            
            <!-- Прелоадер для данных компании -->
            <div v-if="loadingSettings" class="flex items-center justify-center py-12">
              <div class="text-center">
                <Loader2 class="animate-spin h-8 w-8 text-blue-600 mx-auto mb-4" />
                <p class="text-gray-600 text-sm">Загрузка данных компании...</p>
              </div>
            </div>
            
            <!-- Основная форма -->
            <form v-else @submit.prevent="saveCompanyData" class="space-y-6">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Наименование компании -->
                <div class="md:col-span-2">
                  <label class="block text-sm font-medium text-gray-700 mb-2">Наименование компании</label>
                  <input 
                    v-model="companyData.name" 
                    type="text" 
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent"
                    placeholder="Введите наименование компании"
                  />
                </div>

                <!-- ИНН или ПИНФЛ -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">ИНН или ПИНФЛ</label>
                  <input 
                    v-model="companyData.inn" 
                    type="text" 
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent"
                    placeholder="Введите ИНН или ПИНФЛ"
                  />
                </div>
              </div>

              <div class="flex justify-end">
                <button 
                  type="submit" 
                  :disabled="savingCompany"
                  class="bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white font-semibold px-6 py-2 rounded-lg transition-colors flex items-center gap-2 text-sm"
                >
                  <Loader2 v-if="savingCompany" class="animate-spin h-4 w-4" />
                  {{ savingCompany ? 'Сохранение...' : 'Сохранить' }}
                </button>
              </div>
            </form>
          </section>

          <!-- Смена пароля -->
          <section id="password" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center gap-3 mb-6">
              <Lock class="w-5 h-5 text-blue-600" />
              <h2 class="text-xl font-semibold text-gray-900">Смена пароля</h2>
            </div>
            
            <!-- Прелоадер для смены пароля -->
            <div v-if="loadingSettings" class="flex items-center justify-center py-12">
              <div class="text-center">
                <Loader2 class="animate-spin h-8 w-8 text-blue-600 mx-auto mb-4" />
                <p class="text-gray-600 text-sm">Загрузка настроек пароля...</p>
              </div>
            </div>
            
            <!-- Основная форма -->
            <form v-else @submit.prevent="changePassword" class="space-y-6">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Старый пароль -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Старый пароль *</label>
                  <div class="relative">
                    <input 
                      v-model="passwordData.oldPassword" 
                      :type="showOldPassword ? 'text' : 'password'" 
                      required
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent"
                      placeholder="Введите старый пароль"
                    />
                    <button 
                      type="button"
                      @click="showOldPassword = !showOldPassword"
                      class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600"
                    >
                      <Eye v-if="showOldPassword" class="w-4 h-4" />
                      <EyeOff v-else class="w-4 h-4" />
                    </button>
                  </div>
                </div>

                <!-- Новый пароль -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Новый пароль *</label>
                  <div class="relative">
                    <input 
                      v-model="passwordData.newPassword" 
                      :type="showNewPassword ? 'text' : 'password'" 
                      required
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent"
                      placeholder="Введите новый пароль"
                    />
                    <button 
                      type="button"
                      @click="showNewPassword = !showNewPassword"
                      class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600"
                    >
                      <Eye v-if="showNewPassword" class="w-4 h-4" />
                      <EyeOff v-else class="w-4 h-4" />
                    </button>
                  </div>
                </div>

                <!-- Повторить новый пароль -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Повторить новый пароль *</label>
                  <div class="relative">
                    <input 
                      v-model="passwordData.confirmPassword" 
                      :type="showConfirmPassword ? 'text' : 'password'" 
                      required
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent"
                      placeholder="Повторите новый пароль"
                    />
                    <button 
                      type="button"
                      @click="showConfirmPassword = !showConfirmPassword"
                      class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600"
                    >
                      <Eye v-if="showConfirmPassword" class="w-4 h-4" />
                      <EyeOff v-else class="w-4 h-4" />
                    </button>
                  </div>
                </div>
              </div>

              <div class="flex justify-end">
                <button 
                  type="submit" 
                  :disabled="changingPassword"
                  class="bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white font-semibold px-6 py-2 rounded-lg transition-colors flex items-center gap-2 text-sm"
                >
                  <Loader2 v-if="changingPassword" class="animate-spin h-4 w-4" />
                  {{ changingPassword ? 'Смена пароля...' : 'Сменить пароль' }}
                </button>
              </div>
            </form>
          </section>

          <!-- Кастомные поля товаров -->
          <section id="custom-product-fields" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center gap-3 mb-6">
              <List class="w-5 h-5 text-blue-600" />
              <h2 class="text-xl font-semibold text-gray-900">Кастомные поля товаров</h2>
            </div>

            <div class="mb-6">
              <h3 class="text-lg font-semibold mb-2">Стандартные поля товаров</h3>
              <div v-if="loadingVisibility || Object.keys(productFieldsVisibility).length === 0" class="flex items-center gap-2 text-blue-600"><Loader2 class="animate-spin w-4 h-4" /> Загрузка...</div>
              <div v-else>
                <div v-if="errorVisibility" class="text-red-600 text-sm mb-2">{{ errorVisibility }}</div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                  <div v-for="field in standardProductFields" :key="field.key" class="flex items-center gap-3 p-2 bg-gray-50 rounded">
                    <label class="flex-1 text-gray-800 text-sm">{{ field.label }}</label>
                    <button @click="toggleFieldVisibility(field.key)"
                      :class="productFieldsVisibility[field.key] === true ? 'bg-blue-600' : 'bg-gray-300'"
                      class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none">
                      <span :class="productFieldsVisibility[field.key] === true ? 'translate-x-6 bg-white' : 'translate-x-1 bg-white'"
                        class="inline-block h-4 w-4 transform rounded-full transition-transform" />
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- CRUD кастомных полей ниже -->
            <div class="border-t border-gray-200 pt-6 mt-6">
              <div class="flex items-center justify-between mb-2">
                <h3 class="text-lg font-semibold">Пользовательские поля</h3>
                <button 
                  @click="openAddFieldModal"
                  class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2 rounded-lg transition-colors flex items-center gap-2 text-sm"
                >
                  <Plus class="w-4 h-4" /> Добавить поле
                </button>
              </div>
              <div v-if="loadingFields" class="flex items-center justify-center py-8"><Loader2 class="animate-spin w-6 h-6 text-blue-600" /></div>
              <div v-else>
                <div v-if="errorFields" class="text-red-600 text-sm mb-2">{{ errorFields }}</div>
                <table v-if="productFields.length" class="min-w-full divide-y divide-gray-200">
                  <thead>
                    <tr>
                      <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Наименование</th>
                      <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Действия</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="field in productFields" :key="field.id">
                      <td class="px-4 py-2">{{ field.field_name }}</td>
                      <td class="px-4 py-2 text-right">
                        <div class="flex justify-end gap-3">
                          <button @click="editField(field)" class="text-blue-600 hover:underline text-xs flex items-center gap-1 cursor-pointer" title="Редактировать"><Pencil class="w-4 h-4" /></button>
                          <button @click="confirmDeleteField(field)" class="text-red-600 hover:underline text-xs flex items-center gap-1 cursor-pointer" title="Удалить"><Trash2 class="w-4 h-4" /></button>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
                <div v-else class="text-gray-500 text-sm py-4">Нет кастомных полей</div>
              </div>
              <!-- Модалка подтверждения удаления -->
              <div v-if="showDeleteModal" class="fixed inset-0 bg-white/90 bg-opacity-30 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
                  <h3 class="text-lg font-semibold mb-4">Удалить поле?</h3>
                  <p class="mb-6 text-gray-700">Вы действительно хотите удалить поле <b>{{ fieldToDelete?.field_name }}</b>?</p>
                  <div class="flex justify-end gap-2">
                    <button @click="cancelDeleteField" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium px-4 py-2 rounded-lg">Нет</button>
                    <button @click="doDeleteField" class="bg-red-600 hover:bg-red-700 text-white font-medium px-4 py-2 rounded-lg">Да, удалить</button>
                  </div>
                </div>
              </div>
            </div>
            <!-- Модальное окно для добавления/редактирования -->
            <div v-if="showFieldModal" class="fixed inset-0 bg-white/90 bg-opacity-30 flex items-center justify-center z-50">
              <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
                <div class="flex items-center justify-between mb-4">
                  <h3 class="text-lg font-semibold">{{ editingField ? 'Редактировать поле' : 'Добавить поле' }}</h3>
                  <button @click="closeFieldModal" class="text-gray-400 hover:text-gray-700"><X class="w-6 h-6" /></button>
                </div>
                <form @submit.prevent="saveField">
                  <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Наименование поля</label>
                    <input v-model="fieldForm.field_name" type="text" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent" placeholder="Введите наименование" />
                  </div>
                  <div v-if="errorSaveField" class="text-red-600 text-sm mb-2">{{ errorSaveField }}</div>
                  <div class="flex justify-end gap-2">
                    <button type="button" @click="closeFieldModal" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium px-4 py-2 rounded-lg">Отмена</button>
                    <button type="submit" :disabled="savingField" class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2 rounded-lg flex items-center gap-2">
                      <Loader2 v-if="savingField" class="animate-spin w-4 h-4" />
                      Сохранить
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </section>
        </div>
      </div>
    </div>

    <!-- Скрытый input для загрузки файла -->
    <input 
      ref="fileInput"
      type="file" 
      accept="image/*" 
      @change="handleFileSelect"
      class="hidden"
    />
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, onUnmounted, computed, watch } from 'vue'
import { User, Building, Lock, Eye, EyeOff, ChevronDown, Loader2, X, Pencil, Trash2, Plus, List } from 'lucide-vue-next'
import Multiselect from '@vueform/multiselect'
import '@vueform/multiselect/themes/default.css'
import countriesData from '@/data/countries.json'
import timezonesData from '@/data/timezones.json'
import { apiRequest, getFileUrl } from '@/config/api'
import { autoDetectLocation, findCountryInList, findTimezoneInList } from '@/services/geolocation'
import { getPhoneMask, applyPhoneMask, extractDigits, validatePhone, formatPhoneInput } from '@/data/phoneMasks.js'

const emit = defineEmits(['avatar-updated'])

// Состояние навигации
const activeSection = ref('personal')

// Состояние аватара
const avatarUrl = ref('')
const fileInput = ref(null)



// Состояние форм
const personalData = reactive({
  firstName: '',
  position: '',
  phone: '',
  email: '',
  country: null,
  city: '',
  timezone: null,
  currency: 'UZS'
})

const companyData = reactive({
  name: '',
  inn: ''
})

const passwordData = reactive({
  oldPassword: '',
  newPassword: '',
  confirmPassword: ''
})

// Состояние загрузки данных
const loadingSettings = ref(false)

// Состояние загрузки
const savingPersonal = ref(false)
const savingCompany = ref(false)
const changingPassword = ref(false)

// Состояние отображения паролей
const showOldPassword = ref(false)
const showNewPassword = ref(false)
const showConfirmPassword = ref(false)

// Состояние выбора страны для телефона
const showCountrySelect = ref(false)
const countrySearch = ref('')
const selectedCountry = ref({
  id: 860,
  name: 'Uzbekistan',
  code: 'UZ',
  phone_code: '998',
  flag: 'https://flagcdn.com/w40/uz.png'
})

// Данные стран
const countries = ref(countriesData)

// Данные часовых поясов
const timezones = ref(timezonesData)

// Опции стран для Multiselect
const countryOptions = computed(() => {
  return countries.value.map(country => ({
    label: country.name,
    value: country.id,
    code: country.code,
    phone_code: country.phone_code,
    flag: country.flag
  }))
})

// Опции часовых поясов для Multiselect
const timezoneOptions = computed(() => {
  return timezones.value.map(timezone => ({
    label: timezone.label,
    value: timezone.value
  }))
})

// Опции валют для Multiselect
const currencyOptions = computed(() => {
  return [
    { label: 'AUD - Australian Dollar', value: 'AUD' },
    { label: 'CAD - Canadian Dollar', value: 'CAD' },
    { label: 'CHF - Swiss Franc', value: 'CHF' },
    { label: 'CNY - Chinese Yuan', value: 'CNY' },
    { label: 'EUR - Euro', value: 'EUR' },
    { label: 'GBP - British Pound Sterling', value: 'GBP' },
    { label: 'HKD - Hong Kong Dollar', value: 'HKD' },
    { label: 'JPY - Japanese Yen', value: 'JPY' },
    { label: 'NZD - New Zealand Dollar', value: 'NZD' },
    { label: 'RUB - Russian Ruble', value: 'RUB' },
    { label: 'USD - United States Dollar', value: 'USD' },
    { label: 'UZS - Uzbekistani Som', value: 'UZS' },
  ]
})

// Состояние автоопределения
const isDetectingLocation = ref(false)
const locationDetected = ref(false)

// Фильтрованные страны для поиска
const filteredCountries = computed(() => {
  if (!countrySearch.value) return countries.value
  return countries.value.filter(country => 
    country.name.toLowerCase().includes(countrySearch.value.toLowerCase()) ||
    country.code.toLowerCase().includes(countrySearch.value.toLowerCase())
  )
})

// Кастомные поля товаров
const productFields = ref([])
const loadingFields = ref(false)
const errorFields = ref('')

async function loadProductFields() {
  loadingFields.value = true
  errorFields.value = ''
  try {
    const response = await apiRequest('/product-fields', { method: 'GET' })
    if (response.ok && response.data.success) {
      productFields.value = response.data.data
    } else {
      errorFields.value = response.data.message || 'Ошибка загрузки полей'
    }
  } catch (e) {
    errorFields.value = 'Ошибка загрузки полей'
  } finally {
    loadingFields.value = false
  }
}

const showFieldModal = ref(false)
const editingField = ref(null)
const fieldForm = reactive({ field_name: '' })
const savingField = ref(false)
const errorSaveField = ref('')

function openAddFieldModal() {
  editingField.value = null
  fieldForm.field_name = ''
  showFieldModal.value = true
  errorSaveField.value = ''
}
function closeFieldModal() {
  showFieldModal.value = false
}
function editField(field) {
  editingField.value = field
  fieldForm.field_name = field.field_name
  showFieldModal.value = true
  errorSaveField.value = ''
}
async function saveField() {
  savingField.value = true
  errorSaveField.value = ''
  try {
    if (editingField.value) {
      // PUT
      const response = await apiRequest(`/product-fields/${editingField.value.id}`, {
        method: 'PUT',
        body: JSON.stringify({ field_name: fieldForm.field_name }),
        headers: { 'Content-Type': 'application/json' }
      })
      if (response.ok && response.data.success) {
        await loadProductFields()
        closeFieldModal()
      } else {
        errorSaveField.value = response.data.message || 'Ошибка сохранения'
      }
    } else {
      // POST
      const response = await apiRequest('/product-fields', {
        method: 'POST',
        body: JSON.stringify({ field_name: fieldForm.field_name }),
        headers: { 'Content-Type': 'application/json' }
      })
      if (response.ok && response.data.success) {
        await loadProductFields()
        closeFieldModal()
      } else {
        errorSaveField.value = response.data.message || 'Ошибка добавления'
      }
    }
  } catch (e) {
    errorSaveField.value = 'Ошибка сохранения'
  } finally {
    savingField.value = false
  }
}
async function deleteField(id) {
  if (!confirm('Удалить поле?')) return
  try {
    const response = await apiRequest(`/product-fields/${id}`, { method: 'DELETE' })
    if (response.ok && response.data.success) {
      await loadProductFields()
    } else {
      alert(response.data.message || 'Ошибка удаления')
    }
  } catch (e) {
    alert('Ошибка удаления')
  } finally {
    showDeleteModal.value = false
    fieldToDelete.value = null
  }
}

// Список стандартных необязательных полей products_sklad
const standardProductFields = [
  { key: 'description', label: 'Описание' },
  { key: 'country', label: 'Страна' },
  { key: 'supplier', label: 'Поставщик' },
  { key: 'article', label: 'Артикул' },
  { key: 'code', label: 'Код' },
  { key: 'external_code', label: 'Внешний код' },
  { key: 'weight', label: 'Вес' },
  { key: 'volume', label: 'Объем' },
  { key: 'vat', label: 'Ставка НДС' },
  { key: 'min_stock', label: 'Минимальный остаток' },
  { key: 'stock_type', label: 'Тип запаса' },
  { key: 'packing', label: 'Упаковка' },
  { key: 'accounting_type', label: 'Тип учета' },
  { key: 'traceable', label: 'Маркируемый' },
  { key: 'marking', label: 'Маркировка' },
  { key: 'product_type', label: 'Тип товара' },
  { key: 'barcode_type', label: 'Тип штрихкода' },
  { key: 'barcode', label: 'Штрихкод' },
  { key: 'cash_register_tax', label: 'Налог ККМ' },
  { key: 'cash_register_type', label: 'Тип ККМ' },
]

const productFieldsVisibility = reactive({})
const loadingVisibility = ref(false)
const errorVisibility = ref('')

async function loadProductFieldsVisibility() {
  loadingVisibility.value = true
  errorVisibility.value = ''
  try {
    const response = await apiRequest('/user/settings', { method: 'GET' })
    if (response.ok && response.data.success) {
      // Гарантируем правильный путь
      let vis = response.data.data.personal?.product_fields_visibility
      console.log('RAW product_fields_visibility:', vis)
      if (typeof vis === 'string') {
        try {
          vis = JSON.parse(vis)
          console.log('Parsed product_fields_visibility:', vis)
        } catch (e) {
          console.error('Ошибка парсинга product_fields_visibility:', vis, e)
          vis = {}
        }
      }
      if (typeof vis !== 'object' || vis === null) vis = {}
      console.log('vis до assign:', vis)
      const defaults = Object.fromEntries(standardProductFields.map(f => [f.key, true]))
      Object.assign(productFieldsVisibility, { ...defaults, ...vis })
      console.log('productFieldsVisibility итог:', JSON.parse(JSON.stringify(productFieldsVisibility)))
    } else {
      errorVisibility.value = response.data.message || 'Ошибка загрузки настроек видимости'
      Object.assign(productFieldsVisibility, Object.fromEntries(standardProductFields.map(f => [f.key, true])))
    }
  } catch (e) {
    errorVisibility.value = 'Ошибка загрузки настроек видимости'
    Object.assign(productFieldsVisibility, Object.fromEntries(standardProductFields.map(f => [f.key, true])))
  } finally {
    loadingVisibility.value = false
  }
}

async function saveProductFieldsVisibility(key) {
  // Не блокируем UI, не ставим loadingVisibility
  errorVisibility.value = ''
  try {
    const response = await apiRequest('/user/product-fields-visibility', {
      method: 'PUT',
      body: JSON.stringify({ product_fields_visibility: productFieldsVisibility }),
      headers: { 'Content-Type': 'application/json' }
    })
    if (!response.ok || !response.data.success) {
      errorVisibility.value = response.data.message || 'Ошибка сохранения настроек видимости'
    }
  } catch (e) {
    errorVisibility.value = 'Ошибка сохранения настроек видимости'
  }
}

function toggleFieldVisibility(key) {
  productFieldsVisibility[key] = !productFieldsVisibility[key]
  // Просто отправляем запрос, не перезагружаем настройки и не блокируем UI
  saveProductFieldsVisibility(key)
}

// Функции для работы с аватаром
const openAvatarUpload = () => {
  fileInput.value?.click()
}

const handleFileSelect = (event) => {
  const file = event.target.files[0]
  if (file) {
    const reader = new FileReader()
    reader.onload = (e) => {
      cropAndSetAvatar(e.target.result)
    }
    reader.readAsDataURL(file)
  }
  event.target.value = ''
}

async function cropAndSetAvatar(imageSrc) {
  const img = new window.Image()
  img.onload = async () => {
    const size = Math.min(img.width, img.height)
    const sx = (img.width - size) / 2
    const sy = (img.height - size) / 2
    const canvas = document.createElement('canvas')
    canvas.width = 400
    canvas.height = 400
    const ctx = canvas.getContext('2d')
    ctx.drawImage(img, sx, sy, size, size, 0, 0, 400, 400)
    
    const base64Image = canvas.toDataURL('image/jpeg', 0.8)
    
    try {
      // Отправляем аватар на сервер
      const response = await apiRequest('/user/avatar', {
        method: 'POST',
        body: JSON.stringify({
          avatar: base64Image
        })
      })
      
      if (response.ok && response.data.success) {
        console.log('Ответ сервера:', response.data)
        avatarUrl.value = response.data.data.avatar_url
        console.log('avatarUrl обновлен на:', avatarUrl.value)
        
        // Обновляем аватар в шапке через emit
        emit('avatar-updated', response.data.data.avatar_url)
        // Отправляем глобальное событие
        window.dispatchEvent(new CustomEvent('avatar-updated', {
          detail: response.data.data.avatar_url
        }))
        console.log('Аватар успешно загружен:', response.data.data.avatar_url)
      } else {
        console.error('Ошибка загрузки аватара:', response.data.message)
      }
    } catch (error) {
      console.error('Ошибка при загрузке аватара:', error)
    }
  }
  img.src = imageSrc
}

// Функции навигации
const scrollToSection = (sectionId) => {
  const element = document.getElementById(sectionId)
  if (element) {
    const rect = element.getBoundingClientRect()
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop
    const targetPosition = scrollTop + rect.top - 120 // Учитываем высоту шапки и отступ
    
    window.scrollTo({
      top: targetPosition,
      behavior: 'smooth'
    })
    activeSection.value = sectionId
  }
}

const selectCountry = (country) => {
  selectedCountry.value = country
  showCountrySelect.value = false
  countrySearch.value = ''
  
  // Применяем маску для новой страны
  const phoneMask = getPhoneMask(country.phone_code)
  
  // Если поле пустое или содержит только placeholder, устанавливаем новый placeholder
  if (!personalData.phone || personalData.phone.includes('_')) {
    personalData.phone = phoneMask.placeholder
  } else {
    // Если поле не пустое, применяем маску к существующему номеру
    const digits = extractDigits(personalData.phone)
    if (digits) {
      personalData.phone = applyPhoneMask('+' + country.phone_code + digits, country.phone_code)
    } else {
      personalData.phone = phoneMask.placeholder
    }
  }
}

// Функция форматирования номера телефона с маской
const formatPhoneNumber = () => {
  // Используем новую функцию форматирования
  const formatted = formatPhoneInput(personalData.phone, selectedCountry.value.phone_code)
  
  // Если результат пустой, устанавливаем placeholder
  if (!formatted) {
    const phoneMask = getPhoneMask(selectedCountry.value.phone_code)
    personalData.phone = phoneMask.placeholder
  } else {
    personalData.phone = formatted
  }
}

// Функция загрузки данных пользователя
const loadUserSettings = async () => {
  console.log('loadUserSettings вызвана')
  loadingSettings.value = true
  try {
    console.log('Отправляем запрос на /user/settings')
    const response = await apiRequest('/user/settings', {
      method: 'GET'
    })
    
    console.log('Ответ сервера:', response)
    
    if (response.ok && response.data.success) {
      const { personal, company } = response.data.data
      console.log('Полученные данные:', { personal, company })
      
      // Заполняем личные данные
      personalData.firstName = personal.firstName || ''
      personalData.position = personal.position || ''
      personalData.phone = personal.phone || ''
      personalData.email = personal.email || ''
      
      // Находим опцию страны для Multiselect
      console.log('Загружаем страну из данных:', personal.country)
      if (personal.country) {
        // Ищем по названию страны, так как в БД хранится название, а не ID
        const countryOption = countryOptions.value.find(option => 
          option.label.toLowerCase() === personal.country.toLowerCase()
        )
        console.log('Найдена опция страны:', countryOption)
        personalData.country = countryOption || null
      } else {
        console.log('Страна не найдена в данных')
        personalData.country = null
      }
      
      personalData.city = personal.city || ''
      
      // Находим опцию часового пояса для Multiselect
      console.log('Загружаем часовой пояс из данных:', personal.timezone)
      if (personal.timezone) {
        const timezoneOption = timezoneOptions.value.find(option => option.value === personal.timezone)
        console.log('Найдена опция часового пояса:', timezoneOption)
        personalData.timezone = timezoneOption || null
      } else {
        console.log('Часовой пояс не найден в данных')
        personalData.timezone = null
      }

      // Находим опцию валюты для select
      console.log('Загружаем валюту из данных:', personal.currency)
      if (personal.currency) {
        personalData.currency = personal.currency
      } else {
        personalData.currency = 'UZS' // Устанавливаем по умолчанию, если не найдена
      }
      
      console.log('Заполненные личные данные:', {
        firstName: personalData.firstName,
        position: personalData.position,
        phone: personalData.phone,
        email: personalData.email,
        country: personalData.country ? personalData.country.label : null,
        city: personalData.city,
        timezone: personalData.timezone ? personalData.timezone.value : null,
        currency: personalData.currency
      })
      
      // Заполняем данные компании
      companyData.name = company.name || ''
      companyData.inn = company.inn || ''
      
      // Устанавливаем аватар
      console.log('Проверяем аватар в данных:', personal.avatar_url)
      if (personal.avatar_url) {
        console.log('Устанавливаем аватар из данных пользователя:', personal.avatar_url)
        avatarUrl.value = personal.avatar_url
        console.log('avatarUrl установлен на:', avatarUrl.value)
      } else {
        console.log('Аватар не найден в данных пользователя')
        // Проверяем, есть ли аватар в localStorage
        const userData = localStorage.getItem('user')
        if (userData) {
          const user = JSON.parse(userData)
          if (user.avatar_url) {
            console.log('Найден аватар в localStorage:', user.avatar_url)
            avatarUrl.value = user.avatar_url
          }
        }
      }
      
      // Определяем страну для телефона
      if (personal.phone) {
        const phoneDigits = extractDigits(personal.phone)
        if (phoneDigits) {
          // Пытаемся найти страну по коду в номере
          const phoneCode = phoneDigits.substring(0, 4) // Берем первые 4 цифры
          const country = countries.value.find(c => phoneDigits.startsWith(c.phone_code))
          if (country) {
            selectedCountry.value = country
          }
        }
      }
      
      console.log('Данные пользователя загружены:', response.data.data)
    } else {
      console.error('Ошибка загрузки данных пользователя:', response.data.message)
    }
  } catch (error) {
    console.error('Ошибка загрузки данных пользователя:', error)
  } finally {
    loadingSettings.value = false
  }
}

// Функция автоопределения местоположения
const detectLocation = async () => {
  console.log('detectLocation вызвана')
  if (isDetectingLocation.value) {
    console.log('detectLocation уже выполняется, пропускаем')
    return
  }
  
  isDetectingLocation.value = true
  console.log('Начинаем автоопределение местоположения...')
  
  try {
    const location = await autoDetectLocation()
    
    if (location) {
      console.log('Полученные данные о местоположении:', location)
      
      // Находим страну в нашем списке
      const detectedCountry = findCountryInList(location, countries.value)
      if (detectedCountry) {
        console.log('Найдена страна:', detectedCountry)
        selectedCountry.value = detectedCountry
        // Находим соответствующую опцию для Multiselect
        const countryOption = countryOptions.value.find(option => option.value === detectedCountry.id)
        if (countryOption) {
          personalData.country = countryOption
        }
        
        // Автоматически применяем маску для телефона при автоопределении
        const phoneMask = getPhoneMask(detectedCountry.phone_code)
        if (!personalData.phone || personalData.phone.includes('_')) {
          personalData.phone = phoneMask.placeholder
        } else {
          const digits = extractDigits(personalData.phone)
          if (digits) {
            personalData.phone = applyPhoneMask('+' + detectedCountry.phone_code + digits, detectedCountry.phone_code)
          } else {
            personalData.phone = phoneMask.placeholder
          }
        }
      } else {
        console.log('Страна не найдена в списке для:', location.country)
      }
      
      // Устанавливаем город
      if (location.city) {
        console.log('Устанавливаем город:', location.city)
        personalData.city = location.city
      }
      
      // Находим часовой пояс
      const detectedTimezone = findTimezoneInList(location.timezone, timezones.value)
      if (detectedTimezone) {
        console.log('Найден часовой пояс:', detectedTimezone)
        // Находим соответствующую опцию для Multiselect
        const timezoneOption = timezoneOptions.value.find(option => option.value === detectedTimezone.value)
        if (timezoneOption) {
          personalData.timezone = timezoneOption
        }
      } else {
        console.log('Часовой пояс не найден для:', location.timezone)
      }
      
      locationDetected.value = true
      console.log('Местоположение определено:', location)
    } else {
      console.log('Не удалось определить местоположение')
    }
  } catch (error) {
    console.error('Ошибка автоопределения местоположения:', error)
  } finally {
    isDetectingLocation.value = false
  }
}

const savePersonalData = async () => {
  // Валидация телефона
  const phoneDigits = extractDigits(personalData.phone)
  if (phoneDigits && !validatePhone(personalData.phone, selectedCountry.value.phone_code)) {
    alert('Пожалуйста, введите корректный номер телефона')
    return
  }
  
  savingPersonal.value = true
  try {
    const response = await apiRequest('/user/personal', {
      method: 'PUT',
      body: JSON.stringify({
        firstName: personalData.firstName,
        position: personalData.position,
        phone: personalData.phone,
        email: personalData.email,
        country: personalData.country ? personalData.country.label : null,
        city: personalData.city,
        timezone: personalData.timezone ? personalData.timezone.value : null,
        currency: personalData.currency
      })
    })
    
    if (response.ok && response.data.success) {
      console.log('Личные данные сохранены:', response.data)
      
      // Обновляем данные пользователя в localStorage
      const userData = localStorage.getItem('user')
      if (userData) {
        const user = JSON.parse(userData)
        user.first_name = personalData.firstName
        user.position = personalData.position
        user.phone_number = personalData.phone
        user.email = personalData.email
        user.country = personalData.country ? personalData.country.label : null
        user.city = personalData.city
        user.timezone = personalData.timezone ? personalData.timezone.value : null
        user.currency = personalData.currency
        localStorage.setItem('user', JSON.stringify(user))
        
        // Уведомляем Header о необходимости обновления
        window.dispatchEvent(new CustomEvent('user-data-updated', { 
          detail: { user: user } 
        }))
      }
      
      // Показываем уведомление об успешном сохранении
      if (window.toastr) {
        window.toastr.success('Личные данные успешно сохранены')
      }
    } else {
      console.error('Ошибка сохранения личных данных:', response.data.message)
    }
  } catch (error) {
    console.error('Ошибка сохранения личных данных:', error)
  } finally {
    savingPersonal.value = false
  }
}

const saveCompanyData = async () => {
  savingCompany.value = true
  try {
    const response = await apiRequest('/user/company', {
      method: 'PUT',
      body: JSON.stringify({
        name: companyData.name,
        inn: companyData.inn
      })
    })
    
    if (response.ok && response.data.success) {
      console.log('Данные компании сохранены:', response.data)
    } else {
      console.error('Ошибка сохранения данных компании:', response.data.message)
    }
  } catch (error) {
    console.error('Ошибка сохранения данных компании:', error)
  } finally {
    savingCompany.value = false
  }
}

const changePassword = async () => {
  if (passwordData.newPassword !== passwordData.confirmPassword) {
    alert('Пароли не совпадают')
    return
  }
  
  changingPassword.value = true
  try {
    const response = await apiRequest('/user/password', {
      method: 'PUT',
      body: JSON.stringify({
        oldPassword: passwordData.oldPassword,
        newPassword: passwordData.newPassword,
        confirmPassword: passwordData.confirmPassword
      })
    })
    
    if (response.ok && response.data.success) {
      console.log('Пароль изменен')
      
      // Очищаем поля паролей
      passwordData.oldPassword = ''
      passwordData.newPassword = ''
      passwordData.confirmPassword = ''
    } else {
      console.error('Ошибка смены пароля:', response.data.message)
    }
  } catch (error) {
    console.error('Ошибка смены пароля:', error)
  } finally {
    changingPassword.value = false
  }
}

// Отслеживание активной секции при скролле
const handleScroll = () => {
  const sections = ['personal', 'company', 'password']
  const scrollPosition = window.scrollY + 0 // Учитываем высоту шапки и отступы

  for (const section of sections) {
    const element = document.getElementById(section)
    if (element) {
      const rect = element.getBoundingClientRect()
      const elementTop = rect.top + window.scrollY
      const elementBottom = elementTop + rect.height
      
      // Проверяем, находится ли текущая позиция скролла в пределах секции
      if (scrollPosition >= elementTop - 100 && scrollPosition < elementBottom - 100) {
        activeSection.value = section
        break
      }
    }
  }
}

// Обработчик кликов вне выпадающего списка стран
const handleClickOutside = (event) => {
  const countrySelect = event.target.closest('.country-select')
  if (!countrySelect && showCountrySelect.value) {
    showCountrySelect.value = false
    countrySearch.value = ''
  }
}

// Обработчики событий аватара
const handleAvatarError = (event) => {
  console.error('Ошибка загрузки аватара:', event.target.src)
}

const handleAvatarLoad = (event) => {
  console.log('Аватар успешно загружен:', event.target.src)
}

const showDeleteModal = ref(false)
const fieldToDelete = ref(null)

function confirmDeleteField(field) {
  fieldToDelete.value = field
  showDeleteModal.value = true
}
function cancelDeleteField() {
  showDeleteModal.value = false
  fieldToDelete.value = null
}
async function doDeleteField() {
  if (!fieldToDelete.value) return
  try {
    const response = await apiRequest(`/product-fields/${fieldToDelete.value.id}`, { method: 'DELETE' })
    if (response.ok && response.data.success) {
      await loadProductFields()
    } else {
      alert(response.data.message || 'Ошибка удаления')
    }
  } catch (e) {
    alert('Ошибка удаления')
  } finally {
    showDeleteModal.value = false
    fieldToDelete.value = null
  }
}

onMounted(async () => {
  // Устанавливаем заголовок страницы
  document.title = 'B2B SKLAD - Настройки аккаунта'
  
  // Добавляем обработчик скролла
  window.addEventListener('scroll', handleScroll)
  
  // Добавляем обработчик кликов вне выпадающего списка стран
  document.addEventListener('click', handleClickOutside)
  
  // Устанавливаем первую секцию как активную по умолчанию
  activeSection.value = 'personal'
  
  // Загружаем данные пользователя
  await loadUserSettings()
  
  // Инициализируем маску телефона для выбранной страны
  if (!personalData.phone || personalData.phone.includes('_')) {
    const phoneMask = getPhoneMask(selectedCountry.value.phone_code)
    personalData.phone = phoneMask.placeholder
  }
  
  // Устанавливаем часовой пояс по умолчанию, если не выбран
  if (!personalData.timezone) {
    const defaultTimezone = timezoneOptions.value.find(option => option.value === 'UTC+5')
    if (defaultTimezone) {
      personalData.timezone = defaultTimezone
    }
  }

  // Устанавливаем валюту по умолчанию, если не выбрана
  if (!personalData.currency) {
    personalData.currency = 'UZS'
  }
  
  // Автоматически определяем местоположение только если данные пустые
  const hasLocationData = (personalData.country && personalData.country.label) && personalData.city && (personalData.timezone && personalData.timezone.value)
  console.log('Проверка данных местоположения:', {
    country: personalData.country ? personalData.country.label : null,
    city: personalData.city,
    timezone: personalData.timezone ? personalData.timezone.value : null,
    hasLocationData
  })
  
  if (!hasLocationData) {
    console.log('Запускаем автоопределение местоположения...')
    // Добавляем небольшую задержку, чтобы данные успели загрузиться
    setTimeout(() => {
      detectLocation()
    }, 1000)
  } else {
    console.log('Данные уже заполнены, автоопределение не требуется')
  }

  // Загружаем кастомные поля товаров
  await loadProductFields()

  // Загружаем настройки видимости полей
  await loadProductFieldsVisibility()
})

onUnmounted(() => {
  // Удаляем обработчик скролла
  window.removeEventListener('scroll', handleScroll)
  
  // Удаляем обработчик кликов
  document.removeEventListener('click', handleClickOutside)
})
</script>

<style scoped>
.multiselect-custom,
.multiselect,
.multiselect__input,
.multiselect__option {
  font-size: 0.95rem !important;
}
.multiselect__content-wrapper {
  max-height: 400px !important;
}
</style> 