<template>
  <div class="min-h-screen bg-gray-50" style="margin-top: 72px;">
    <!-- Header -->
    <div class="bg-white shadow-sm ">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4 lg:py-6">
          <div class="flex items-center">
            <BookOpen class="h-6 w-6 lg:h-8 lg:w-8 text-blue-600 mr-2 lg:mr-3" />
            <h1 class="text-lg lg:text-2xl font-bold text-gray-900">API Документация</h1>
          </div>
          <div class="flex items-center space-x-2 lg:space-x-4">
            <div class="flex items-center space-x-1 lg:space-x-2">
              <div class="w-2 h-2 lg:w-3 lg:h-3 bg-green-500 rounded-full"></div>
              <span class="text-xs lg:text-sm text-gray-600">API Активно</span>
            </div>
            <span class="text-xs lg:text-sm text-gray-500">v1.0</span>
          </div>
        </div>
      </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 lg:py-8">
      <!-- Mobile Navigation Toggle -->
      <div class="lg:hidden mb-4">
        <button
          @click="toggleMobileNav"
          class="w-full bg-white rounded-lg shadow-sm p-4 flex items-center justify-between"
        >
          <span class="text-sm font-medium text-gray-900">Навигация API</span>
          <ChevronDown class="h-4 w-4 text-gray-500" :class="{ 'rotate-180': mobileNavOpen }" />
        </button>
      </div>

      <div class="flex flex-col lg:flex-row gap-4 lg:gap-8 min-h-screen">
        <!-- Sidebar Navigation -->
        <div class="w-full lg:w-80 flex-shrink-0">
          <div 
            class="bg-white rounded-lg shadow-sm lg:sticky lg:top-20"
            :class="{ 'hidden lg:block': !mobileNavOpen }"
          >
            <div class="p-4 lg:p-6 ">
              <h2 class="text-base lg:text-lg font-semibold text-gray-900">Навигация</h2>
            </div>
            <nav class="p-4" style="max-height: calc(100vh - 170px);overflow-y: auto;">
              <div class="space-y-3 lg:space-y-4">
                <!-- Auth Section -->
                <div>
                  <h3 class="text-sm font-medium text-gray-900 mb-2 flex items-center">
                    <Shield class="h-4 w-4 mr-2" />
                    Авторизация
                  </h3>
                  <ul class="space-y-1 ml-4">
                    <li v-for="method in authMethods" :key="method.id">
                      <button
                        @click="selectMethod(method)"
                        :class="[
                          'w-full text-left px-3 py-2 rounded-md text-sm transition-colors',
                          selectedMethod?.id === method.id
                            ? 'bg-blue-50 text-blue-700 border lue-200'
                            : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                        ]"
                      >
                        <div class="flex items-center justify-between">
                          <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium" :class="getMethodColor(method.method)">{{ method.method }}</span>
                          <span class="text-xs px-2 py-1 rounded bg-gray-100 truncate max-w-20 lg:max-w-none">{{ method.path }}</span>
                        </div>
                      </button>
                    </li>
                  </ul>
                </div>

                <!-- User Section -->
                <div>
                  <h3 class="text-sm font-medium text-gray-900 mb-2 flex items-center">
                    <User class="h-4 w-4 mr-2" />
                    Пользователь
                  </h3>
                  
                  <!-- User Subsection -->
                  <div class="ml-4">
                    <button
                      @click="toggleUserSection('user')"
                      class="w-full text-left px-3 py-2 rounded-md text-sm transition-colors flex items-center justify-between text-gray-600 hover:bg-gray-50 hover:text-gray-900"
                    >
                      <span class="flex items-center">
                        <User class="h-3 w-3 mr-2" />
                        Пользователь
                      </span>
                      <ChevronDown class="h-3 w-3 transition-transform" :class="{ 'rotate-180': openUserSections.user }" />
                    </button>
                    
                    <div v-if="openUserSections.user" class="mt-2 space-y-1">
                      <div v-for="method in userMethods" :key="method.id">
                        <button
                          @click="selectMethod(method)"
                          :class="[
                            'w-full text-left px-3 py-2 rounded-md text-sm transition-colors',
                            selectedMethod?.id === method.id
                              ? 'bg-blue-50 text-blue-700 border border-blue-200'
                              : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                          ]"
                        >
                          <div class="flex items-center justify-between">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium" :class="getMethodColor(method.method)">{{ method.method }}</span>
                            <span class="text-xs px-2 py-1 rounded bg-gray-100 truncate max-w-20 lg:max-w-none">{{ method.path }}</span>
                          </div>
                        </button>
                      </div>
                    </div>
                  </div>

                  <!-- Products Subsection -->
                  <div class="ml-4">
                    <button
                      @click="toggleUserSection('products')"
                      class="w-full text-left px-3 py-2 rounded-md text-sm transition-colors flex items-center justify-between text-gray-600 hover:bg-gray-50 hover:text-gray-900"
                    >
                      <span class="flex items-center">
                        <Package class="h-3 w-3 mr-2" />
                        Товары
                      </span>
                      <ChevronDown class="h-3 w-3 transition-transform" :class="{ 'rotate-180': openUserSections.products }" />
                    </button>
                    
                    <div v-if="openUserSections.products" class="mt-2 space-y-1">
                      <div v-for="method in productMethods" :key="method.id">
                        <button
                          @click="selectMethod(method)"
                          :class="[
                            'w-full text-left px-3 py-2 rounded-md text-sm transition-colors',
                            selectedMethod?.id === method.id
                              ? 'bg-blue-50 text-blue-700 border border-blue-200'
                              : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                          ]"
                        >
                          <div class="flex items-center justify-between">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium" :class="getMethodColor(method.method)">{{ method.method }}</span>
                            <span class="text-xs px-2 py-1 rounded bg-gray-100 truncate max-w-20 lg:max-w-none">{{ method.path }}</span>
                          </div>
                        </button>
                      </div>
                    </div>
                  </div>

                  <!-- Receipts Subsection -->
                  <div class="ml-4">
                    <button
                      @click="toggleUserSection('receipts')"
                      class="w-full text-left px-3 py-2 rounded-md text-sm transition-colors flex items-center justify-between text-gray-600 hover:bg-gray-50 hover:text-gray-900"
                    >
                      <span class="flex items-center">
                        <Receipt class="h-3 w-3 mr-2" />
                        Оприходования
                      </span>
                      <ChevronDown class="h-3 w-3 transition-transform" :class="{ 'rotate-180': openUserSections.receipts }" />
                    </button>
                    
                    <div v-if="openUserSections.receipts" class="mt-2 space-y-1">
                      <div v-for="method in receiptMethods" :key="method.id">
                        <button
                          @click="selectMethod(method)"
                          :class="[
                            'w-full text-left px-3 py-2 rounded-md text-sm transition-colors',
                            selectedMethod?.id === method.id
                              ? 'bg-blue-50 text-blue-700 border border-blue-200'
                              : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                          ]"
                        >
                          <div class="flex items-center justify-between">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium" :class="getMethodColor(method.method)">{{ method.method }}</span>
                            <span class="text-xs px-2 py-1 rounded bg-gray-100 truncate max-w-20 lg:max-w-none">{{ method.path }}</span>
                          </div>
                        </button>
                      </div>
                    </div>
                  </div>

                  <!-- Write-offs Subsection -->
                  <div class="ml-4">
                    <button
                      @click="toggleUserSection('writeOffs')"
                      class="w-full text-left px-3 py-2 rounded-md text-sm transition-colors flex items-center justify-between text-gray-600 hover:bg-gray-50 hover:text-gray-900"
                    >
                      <span class="flex items-center">
                        <FileText class="h-3 w-3 mr-2" />
                        Списания
                      </span>
                      <ChevronDown class="h-3 w-3 transition-transform" :class="{ 'rotate-180': openUserSections.writeOffs }" />
                    </button>
                    
                    <div v-if="openUserSections.writeOffs" class="mt-2 space-y-1">
                      <div v-for="method in writeOffMethods" :key="method.id">
                        <button
                          @click="selectMethod(method)"
                          :class="[
                            'w-full text-left px-3 py-2 rounded-md text-sm transition-colors',
                            selectedMethod?.id === method.id
                              ? 'bg-blue-50 text-blue-700 border border-blue-200'
                              : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                          ]"
                        >
                          <div class="flex items-center justify-between">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium" :class="getMethodColor(method.method)">{{ method.method }}</span>
                            <span class="text-xs px-2 py-1 rounded bg-gray-100 truncate max-w-20 lg:max-w-none">{{ method.path }}</span>
                          </div>
                        </button>
                      </div>
                    </div>
                  </div>

                  <!-- Inventories Subsection -->
                  <div class="ml-4">
                    <button
                      @click="toggleUserSection('inventories')"
                      class="w-full text-left px-3 py-2 rounded-md text-sm transition-colors flex items-center justify-between text-gray-600 hover:bg-gray-50 hover:text-gray-900"
                    >
                      <span class="flex items-center">
                        <ClipboardList class="h-3 w-3 mr-2" />
                        Инвентаризации
                      </span>
                      <ChevronDown class="h-3 w-3 transition-transform" :class="{ 'rotate-180': openUserSections.inventories }" />
                    </button>
                    
                    <div v-if="openUserSections.inventories" class="mt-2 space-y-1">
                      <div v-for="method in inventoryMethods" :key="method.id">
                        <button
                          @click="selectMethod(method)"
                          :class="[
                            'w-full text-left px-3 py-2 rounded-md text-sm transition-colors',
                            selectedMethod?.id === method.id
                              ? 'bg-blue-50 text-blue-700 border border-blue-200'
                              : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                          ]"
                        >
                          <div class="flex items-center justify-between">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium" :class="getMethodColor(method.method)">{{ method.method }}</span>
                            <span class="text-xs px-2 py-1 rounded bg-gray-100 truncate max-w-20 lg:max-w-none">{{ method.path }}</span>
                          </div>
                        </button>
                      </div>
                    </div>
                  </div>

                  <!-- Warehouses Subsection -->
                  <div class="ml-4">
                    <button
                      @click="toggleUserSection('warehouses')"
                      class="w-full text-left px-3 py-2 rounded-md text-sm transition-colors flex items-center justify-between text-gray-600 hover:bg-gray-50 hover:text-gray-900"
                    >
                      <span class="flex items-center">
                        <Warehouse class="h-3 w-3 mr-2" />
                        Склады
                      </span>
                      <ChevronDown class="h-3 w-3 transition-transform" :class="{ 'rotate-180': openUserSections.warehouses }" />
                    </button>
                    
                    <div v-if="openUserSections.warehouses" class="mt-2 space-y-1">
                      <div v-for="method in warehouseMethods" :key="method.id">
                        <button
                          @click="selectMethod(method)"
                          :class="[
                            'w-full text-left px-3 py-2 rounded-md text-sm transition-colors',
                            selectedMethod?.id === method.id
                              ? 'bg-blue-50 text-blue-700 border border-blue-200'
                              : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                          ]"
                        >
                          <div class="flex items-center justify-between">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium" :class="getMethodColor(method.method)">{{ method.method }}</span>
                            <span class="text-xs px-2 py-1 rounded bg-gray-100 truncate max-w-20 lg:max-w-none">{{ method.path }}</span>
                          </div>
                        </button>
                      </div>
                    </div>
                  </div>

                  <!-- Transfers Subsection -->
                  <div class="ml-4">
                    <button
                      @click="toggleUserSection('transfers')"
                      class="w-full text-left px-3 py-2 rounded-md text-sm transition-colors flex items-center justify-between text-gray-600 hover:bg-gray-50 hover:text-gray-900"
                    >
                      <span class="flex items-center">
                        <Truck class="h-3 w-3 mr-2" />
                        Перемещения
                      </span>
                      <ChevronDown class="h-3 w-3 transition-transform" :class="{ 'rotate-180': openUserSections.transfers }" />
                    </button>
                    
                    <div v-if="openUserSections.transfers" class="mt-2 space-y-1">
                      <div v-for="method in transferMethods" :key="method.id">
                        <button
                          @click="selectMethod(method)"
                          :class="[
                            'w-full text-left px-3 py-2 rounded-md text-sm transition-colors',
                            selectedMethod?.id === method.id
                              ? 'bg-blue-50 text-blue-700 border border-blue-200'
                              : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                          ]"
                        >
                          <div class="flex items-center justify-between">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium" :class="getMethodColor(method.method)">{{ method.method }}</span>
                            <span class="text-xs px-2 py-1 rounded bg-gray-100 truncate max-w-20 lg:max-w-none">{{ method.path }}</span>
                          </div>
                        </button>
                      </div>
                    </div>
                  </div>

                  <!-- Balances Subsection -->
                  <div class="ml-4">
                    <button
                      @click="toggleUserSection('balances')"
                      class="w-full text-left px-3 py-2 rounded-md text-sm transition-colors flex items-center justify-between text-gray-600 hover:bg-gray-50 hover:text-gray-900"
                    >
                      <span class="flex items-center">
                        <Package class="h-3 w-3 mr-2" />
                        Остатки
                      </span>
                      <ChevronDown class="h-3 w-3 transition-transform" :class="{ 'rotate-180': openUserSections.balances }" />
                    </button>
                    
                    <div v-if="openUserSections.balances" class="mt-2 space-y-1">
                      <div v-for="method in balanceMethods" :key="method.id">
                        <button
                          @click="selectMethod(method)"
                          :class="[
                            'w-full text-left px-3 py-2 rounded-md text-sm transition-colors',
                            selectedMethod?.id === method.id
                              ? 'bg-blue-50 text-blue-700 border border-blue-200'
                              : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                          ]"
                        >
                          <div class="flex items-center justify-between">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium" :class="getMethodColor(method.method)">{{ method.method }}</span>
                            <span class="text-xs px-2 py-1 rounded bg-gray-100 truncate max-w-20 lg:max-w-none">{{ method.path }}</span>
                          </div>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Admin Section -->
                <div>
                  <h3 class="text-sm font-medium text-gray-900 mb-2 flex items-center">
                    <Settings class="h-4 w-4 mr-2" />
                    Администратор
                  </h3>
                  
                  <!-- Admin Users Subsection -->
                  <div class="ml-4">
                    <button
                      @click="toggleAdminSection('users')"
                      class="w-full text-left px-3 py-2 rounded-md text-sm transition-colors flex items-center justify-between text-gray-600 hover:bg-gray-50 hover:text-gray-900"
                    >
                      <span class="flex items-center">
                        <User class="h-3 w-3 mr-2" />
                        Пользователи
                      </span>
                      <ChevronDown class="h-3 w-3 transition-transform" :class="{ 'rotate-180': openAdminSections.users }" />
                    </button>
                    
                    <div v-if="openAdminSections.users" class="mt-2 space-y-1">
                      <div v-for="method in adminUserMethods" :key="method.id">
                        <button
                          @click="selectMethod(method)"
                          :class="[
                            'w-full text-left px-3 py-2 rounded-md text-sm transition-colors',
                            selectedMethod?.id === method.id
                              ? 'bg-blue-50 text-blue-700 border border-blue-200'
                              : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                          ]"
                        >
                          <div class="flex items-center justify-between">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium" :class="getMethodColor(method.method)">{{ method.method }}</span>
                            <span class="text-xs px-2 py-1 rounded bg-gray-100 truncate max-w-20 lg:max-w-none">{{ method.path }}</span>
                          </div>
                        </button>
                      </div>
                    </div>
                  </div>

                  <!-- Admin Products Subsection -->
                  <div class="ml-4">
                    <button
                      @click="toggleAdminSection('products')"
                      class="w-full text-left px-3 py-2 rounded-md text-sm transition-colors flex items-center justify-between text-gray-600 hover:bg-gray-50 hover:text-gray-900"
                    >
                      <span class="flex items-center">
                        <Package class="h-3 w-3 mr-2" />
                        Товары
                      </span>
                      <ChevronDown class="h-3 w-3 transition-transform" :class="{ 'rotate-180': openAdminSections.products }" />
                    </button>
                    
                    <div v-if="openAdminSections.products" class="mt-2 space-y-1">
                      <div v-for="method in adminProductMethods" :key="method.id">
                        <button
                          @click="selectMethod(method)"
                          :class="[
                            'w-full text-left px-3 py-2 rounded-md text-sm transition-colors',
                            selectedMethod?.id === method.id
                              ? 'bg-blue-50 text-blue-700 border border-blue-200'
                              : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                          ]"
                        >
                          <div class="flex items-center justify-between">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium" :class="getMethodColor(method.method)">{{ method.method }}</span>
                            <span class="text-xs px-2 py-1 rounded bg-gray-100 truncate max-w-20 lg:max-w-none">{{ method.path }}</span>
                          </div>
                        </button>
                      </div>
                    </div>
                  </div>

                  <!-- Admin Receipts Subsection -->
                  <div class="ml-4">
                    <button
                      @click="toggleAdminSection('receipts')"
                      class="w-full text-left px-3 py-2 rounded-md text-sm transition-colors flex items-center justify-between text-gray-600 hover:bg-gray-50 hover:text-gray-900"
                    >
                      <span class="flex items-center">
                        <Receipt class="h-3 w-3 mr-2" />
                        Оприходования
                      </span>
                      <ChevronDown class="h-3 w-3 transition-transform" :class="{ 'rotate-180': openAdminSections.receipts }" />
                    </button>
                    
                    <div v-if="openAdminSections.receipts" class="mt-2 space-y-1">
                      <div v-for="method in adminReceiptMethods" :key="method.id">
                        <button
                          @click="selectMethod(method)"
                          :class="[
                            'w-full text-left px-3 py-2 rounded-md text-sm transition-colors',
                            selectedMethod?.id === method.id
                              ? 'bg-blue-50 text-blue-700 border border-blue-200'
                              : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                          ]"
                        >
                          <div class="flex items-center justify-between">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium" :class="getMethodColor(method.method)">{{ method.method }}</span>
                            <span class="text-xs px-2 py-1 rounded bg-gray-100 truncate max-w-20 lg:max-w-none">{{ method.path }}</span>
                          </div>
                        </button>
                      </div>
                    </div>
                  </div>

                  <!-- Admin Write-offs Subsection -->
                  <div class="ml-4">
                    <button
                      @click="toggleAdminSection('writeOffs')"
                      class="w-full text-left px-3 py-2 rounded-md text-sm transition-colors flex items-center justify-between text-gray-600 hover:bg-gray-50 hover:text-gray-900"
                    >
                      <span class="flex items-center">
                        <FileText class="h-3 w-3 mr-2" />
                        Списания
                      </span>
                      <ChevronDown class="h-3 w-3 transition-transform" :class="{ 'rotate-180': openAdminSections.writeOffs }" />
                    </button>
                    
                    <div v-if="openAdminSections.writeOffs" class="mt-2 space-y-1">
                      <div v-for="method in adminWriteOffMethods" :key="method.id">
                        <button
                          @click="selectMethod(method)"
                          :class="[
                            'w-full text-left px-3 py-2 rounded-md text-sm transition-colors',
                            selectedMethod?.id === method.id
                              ? 'bg-blue-50 text-blue-700 border border-blue-200'
                              : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                          ]"
                        >
                          <div class="flex items-center justify-between">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium" :class="getMethodColor(method.method)">{{ method.method }}</span>
                            <span class="text-xs px-2 py-1 rounded bg-gray-100 truncate max-w-20 lg:max-w-none">{{ method.path }}</span>
                          </div>
                        </button>
                      </div>
                    </div>
                  </div>

                  <!-- Admin Inventories Subsection -->
                  <div class="ml-4">
                    <button
                      @click="toggleAdminSection('inventories')"
                      class="w-full text-left px-3 py-2 rounded-md text-sm transition-colors flex items-center justify-between text-gray-600 hover:bg-gray-50 hover:text-gray-900"
                    >
                      <span class="flex items-center">
                        <ClipboardList class="h-3 w-3 mr-2" />
                        Инвентаризации
                      </span>
                      <ChevronDown class="h-3 w-3 transition-transform" :class="{ 'rotate-180': openAdminSections.inventories }" />
                    </button>
                    
                    <div v-if="openAdminSections.inventories" class="mt-2 space-y-1">
                      <div v-for="method in adminInventoryMethods" :key="method.id">
                        <button
                          @click="selectMethod(method)"
                          :class="[
                            'w-full text-left px-3 py-2 rounded-md text-sm transition-colors',
                            selectedMethod?.id === method.id
                              ? 'bg-blue-50 text-blue-700 border border-blue-200'
                              : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                          ]"
                        >
                          <div class="flex items-center justify-between">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium" :class="getMethodColor(method.method)">{{ method.method }}</span>
                            <span class="text-xs px-2 py-1 rounded bg-gray-100 truncate max-w-20 lg:max-w-none">{{ method.path }}</span>
                          </div>
                        </button>
                      </div>
                    </div>
                  </div>

                  <!-- Admin Balances Subsection -->
                  <div class="ml-4">
                    <button
                      @click="toggleAdminSection('balances')"
                      class="w-full text-left px-3 py-2 rounded-md text-sm transition-colors flex items-center justify-between text-gray-600 hover:bg-gray-50 hover:text-gray-900"
                    >
                      <span class="flex items-center">
                        <Package class="h-3 w-3 mr-2" />
                        Остатки
                      </span>
                      <ChevronDown class="h-3 w-3 transition-transform" :class="{ 'rotate-180': openAdminSections.balances }" />
                    </button>
                    
                    <div v-if="openAdminSections.balances" class="mt-2 space-y-1">
                      <div v-for="method in adminBalanceMethods" :key="method.id">
                        <button
                          @click="selectMethod(method)"
                          :class="[
                            'w-full text-left px-3 py-2 rounded-md text-sm transition-colors',
                            selectedMethod?.id === method.id
                              ? 'bg-blue-50 text-blue-700 border border-blue-200'
                              : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                          ]"
                        >
                          <div class="flex items-center justify-between">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium" :class="getMethodColor(method.method)">{{ method.method }}</span>
                            <span class="text-xs px-2 py-1 rounded bg-gray-100 truncate max-w-20 lg:max-w-none">{{ method.path }}</span>
                          </div>
                        </button>
                      </div>
                    </div>
                  </div>

                  <!-- Admin Transfers Subsection -->
                  <div class="ml-4">
                    <button
                      @click="toggleAdminSection('transfers')"
                      class="w-full text-left px-3 py-2 rounded-md text-sm transition-colors flex items-center justify-between text-gray-600 hover:bg-gray-50 hover:text-gray-900"
                    >
                      <span class="flex items-center">
                        <Truck class="h-3 w-3 mr-2" />
                        Перемещения
                      </span>
                      <ChevronDown class="h-3 w-3 transition-transform" :class="{ 'rotate-180': openAdminSections.transfers }" />
                    </button>
                    
                    <div v-if="openAdminSections.transfers" class="mt-2 space-y-1">
                      <div v-for="method in adminTransferMethods" :key="method.id">
                        <button
                          @click="selectMethod(method)"
                          :class="[
                            'w-full text-left px-3 py-2 rounded-md text-sm transition-colors',
                            selectedMethod?.id === method.id
                              ? 'bg-blue-50 text-blue-700 border border-blue-200'
                              : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                          ]"
                        >
                          <div class="flex items-center justify-between">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium" :class="getMethodColor(method.method)">{{ method.method }}</span>
                            <span class="text-xs px-2 py-1 rounded bg-gray-100 truncate max-w-20 lg:max-w-none">{{ method.path }}</span>
                          </div>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Public Section -->
                <div>
                  <h3 class="text-sm font-medium text-gray-900 mb-2 flex items-center">
                    <Globe class="h-4 w-4 mr-2" />
                    Публичные методы
                  </h3>
                  <ul class="space-y-1 ml-4">
                    <li v-for="method in publicMethods" :key="method.id">
                      <button
                        @click="selectMethod(method)"
                        :class="[
                          'w-full text-left px-3 py-2 rounded-md text-sm transition-colors',
                          selectedMethod?.id === method.id
                            ? 'bg-blue-50 text-blue-700 border border-blue-200'
                            : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                        ]"
                      >
                        <div class="flex items-center justify-between">
                          <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium" :class="getMethodColor(method.method)">{{ method.method }}</span>
                          <span class="text-xs px-2 py-1 rounded bg-gray-100 truncate max-w-20 lg:max-w-none">{{ method.path }}</span>
                        </div>
                      </button>
                    </li>
                  </ul>
                </div>
              </div>
            </nav>
          </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1">
          <div v-if="selectedMethod" class="space-y-6">
            <!-- Auth Methods -->
            <RegisterMethod v-if="selectedMethod?.id === 'register'" />
            <LoginMethod v-if="selectedMethod?.id === 'login'" />
            <LogoutMethod v-if="selectedMethod?.id === 'logout'" />
            
            <!-- User Methods -->
            <MeMethod v-if="selectedMethod?.id === 'me'" />
            <UserMethod v-if="selectedMethod?.id === 'user'" />
            <ProfileMethod v-if="selectedMethod?.id === 'profile'" />
            <UserAvatarMethod v-if="selectedMethod?.id === 'user-avatar'" />
            <UserSettingsMethod v-if="selectedMethod?.id === 'user-settings'" />
            <UserPersonalMethod v-if="selectedMethod?.id === 'user-personal'" />
            <UserCompanyMethod v-if="selectedMethod?.id === 'user-company'" />
            <UserPasswordMethod v-if="selectedMethod?.id === 'user-password'" />
            
            <!-- Product Methods -->
            <ProductsMethod v-if="selectedMethod?.id === 'products'" />
            <ProductDetailMethod v-if="selectedMethod?.id === 'product-detail'" />
            <ProductDeleteMethod v-if="selectedMethod?.id === 'product-delete'" />
            <ProductImagesPostMethod v-if="selectedMethod?.id === 'product-images-post'" />
            <ProductImagesGetMethod v-if="selectedMethod?.id === 'product-images-get'" />
            <ProductImageDeleteMethod v-if="selectedMethod?.id === 'product-image-delete'" />
            <ProductDraftMethod v-if="selectedMethod?.id === 'product-draft'" />
            <ProductUpdateMethod v-if="selectedMethod?.id === 'product-update'" />
            
            <!-- Receipt Methods -->
            <ReceiptsMethod v-if="selectedMethod?.id === 'receipts'" />
            <ReceiptDetailMethod v-if="selectedMethod?.id === 'receipt-detail'" />
            <ReceiptCreateMethod v-if="selectedMethod?.id === 'receipt-create'" />
            <ReceiptUpdateMethod v-if="selectedMethod?.id === 'receipt-update'" />
            <ReceiptDeleteMethod v-if="selectedMethod?.id === 'receipt-delete'" />
            <ReceiptFilesPostMethod v-if="selectedMethod?.id === 'receipt-files-post'" />
            <ReceiptFilesGetMethod v-if="selectedMethod?.id === 'receipt-files-get'" />
            <ReceiptFileDeleteMethod v-if="selectedMethod?.id === 'receipt-file-delete'" />
            <ReceiptFileDraftMethod v-if="selectedMethod?.id === 'receipt-file-draft'" />
            
            <!-- Write-off Methods -->
            <WriteOffsMethod v-if="selectedMethod?.id === 'write-offs'" />
            <WriteOffDetailMethod v-if="selectedMethod?.id === 'write-off-detail'" />
            <WriteOffCreateMethod v-if="selectedMethod?.id === 'write-off-create'" />
            <WriteOffUpdateMethod v-if="selectedMethod?.id === 'write-off-update'" />
            <WriteOffDeleteMethod v-if="selectedMethod?.id === 'write-off-delete'" />
            <WriteOffFilesPostMethod v-if="selectedMethod?.id === 'write-off-files-post'" />
            <WriteOffFilesGetMethod v-if="selectedMethod?.id === 'write-off-files-get'" />
            <WriteOffFileDeleteMethod v-if="selectedMethod?.id === 'write-off-file-delete'" />
            <WriteOffFileDraftMethod v-if="selectedMethod?.id === 'write-off-file-draft'" />
            
            <!-- Inventory Methods -->
            <InventoriesMethod v-if="selectedMethod?.id === 'inventories'" />
            <InventoryDetailMethod v-if="selectedMethod?.id === 'inventory-detail'" />
            <InventoryCreateMethod v-if="selectedMethod?.id === 'inventory-create'" />
            <InventoryUpdateMethod v-if="selectedMethod?.id === 'inventory-update'" />
            <InventoryDeleteMethod v-if="selectedMethod?.id === 'inventory-delete'" />
            <InventoryExportMethod v-if="selectedMethod?.id === 'inventory-export'" />
            <InventoryCalculateBalancesMethod v-if="selectedMethod?.id === 'inventory-calculate-balances'" />
            <InventoryFilesUploadMethod v-if="selectedMethod?.id === 'inventory-files-upload'" />
            <InventoryFileDraftMethod v-if="selectedMethod?.id === 'inventory-file-draft'" />
            <InventoryFileGetMethod v-if="selectedMethod?.id === 'inventory-file-get'" />
            <InventoryFileDeleteMethod v-if="selectedMethod?.id === 'inventory-file-delete'" />
            
            <!-- Warehouse Methods -->
            <WarehousesMethod v-if="selectedMethod?.id === 'warehouses'" />
            <WarehouseDetailMethod v-if="selectedMethod?.id === 'warehouse-detail'" />
            <WarehouseCreateMethod v-if="selectedMethod?.id === 'warehouse-create'" />
            <WarehouseUpdateMethod v-if="selectedMethod?.id === 'warehouse-update'" />
            <WarehouseDeleteMethod v-if="selectedMethod?.id === 'warehouse-delete'" />
            
            <!-- Transfer Methods -->
            <TransfersMethod v-if="selectedMethod?.id === 'transfers'" />
            <TransferFilterMethod v-if="selectedMethod?.id === 'transfer-filter'" />
            <TransferAvailableProductsMethod v-if="selectedMethod?.id === 'transfer-available-products'" />
            <TransferAllProductsMethod v-if="selectedMethod?.id === 'transfer-all-products'" />
            <TransferCreateMethod v-if="selectedMethod?.id === 'transfer-create'" />
            <TransferDetailMethod v-if="selectedMethod?.id === 'transfer-detail'" />
            <TransferUpdateMethod v-if="selectedMethod?.id === 'transfer-update'" />
            <TransferDeleteMethod v-if="selectedMethod?.id === 'transfer-delete'" />
            <TransferConfirmMethod v-if="selectedMethod?.id === 'transfer-confirm'" />
            <TransferCompleteMethod v-if="selectedMethod?.id === 'transfer-complete'" />
            <TransferCancelMethod v-if="selectedMethod?.id === 'transfer-cancel'" />
            
            <!-- Balance Methods -->
            <BalancesMethod v-if="selectedMethod?.id === 'balances'" />
            <BalanceCreateMethod v-if="selectedMethod?.id === 'balance-create'" />
            <BalanceSummaryMethod v-if="selectedMethod?.id === 'balance-summary'" />
            <BalanceSummaryPostMethod v-if="selectedMethod?.id === 'balance-summary-post'" />
            <BalanceByWarehouseMethod v-if="selectedMethod?.id === 'balance-by-warehouse'" />
            <BalanceByProductMethod v-if="selectedMethod?.id === 'balance-by-product'" />
            <BalanceLowStockMethod v-if="selectedMethod?.id === 'balance-low-stock'" />
            <BalanceOutOfStockMethod v-if="selectedMethod?.id === 'balance-out-of-stock'" />
            <BalanceMovementsMethod v-if="selectedMethod?.id === 'balance-movements'" />
            <BalanceMovementsPostMethod v-if="selectedMethod?.id === 'balance-movements-post'" />
            
            <!-- Category Methods -->
            <CategoriesMethod v-if="selectedMethod?.id === 'categories'" />
            <CategorySubcategoriesMethod v-if="selectedMethod?.id === 'category-subcategories'" />
            <SubcategoriesMethod v-if="selectedMethod?.id === 'subcategories'" />
            
            <!-- Admin Methods -->
            <AdminUsersMethod v-if="selectedMethod?.id === 'admin-users'" />
            <AdminUserDetailsMethod v-if="selectedMethod?.id === 'admin-user-details'" />
            <AdminRecentUsersMethod v-if="selectedMethod?.id === 'admin-recent-users'" />
            <AdminStatsMethod v-if="selectedMethod?.id === 'admin-stats'" />
            <AdminProductsMethod v-if="selectedMethod?.id === 'admin-products'" />
            <AdminProductsSearchMethod v-if="selectedMethod?.id === 'admin-products-search'" />
            <AdminSubcategoriesMethod v-if="selectedMethod?.id === 'admin-subcategories'" />
            <AdminReceiptsMethod v-if="selectedMethod?.id === 'admin-receipts'" />
            <AdminReceiptDetailsMethod v-if="selectedMethod?.id === 'admin-receipt-details'" />
            <AdminWriteOffsMethod v-if="selectedMethod?.id === 'admin-write-offs'" />
            <AdminWriteOffDetailsMethod v-if="selectedMethod?.id === 'admin-write-off-details'" />
            <AdminInventoriesMethod v-if="selectedMethod?.id === 'admin-inventories'" />
            <AdminInventoryDetailsMethod v-if="selectedMethod?.id === 'admin-inventory-details'" />
            <AdminBalancesMethod v-if="selectedMethod?.id === 'admin-balances'" />
            <AdminBalancesPostMethod v-if="selectedMethod?.id === 'admin-balances-post'" />
            <AdminBalanceMovementsMethod v-if="selectedMethod?.id === 'admin-balance-movements'" />
            <AdminTransfersMethod v-if="selectedMethod?.id === 'admin-transfers'" />
            <AdminTransferDetailsMethod v-if="selectedMethod?.id === 'admin-transfer-details'" />
          </div>
          
          <div v-else class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
            <div class="text-center">
              <BookOpen class="h-12 w-12 text-gray-400 mx-auto mb-4" />
              <h3 class="text-lg font-medium text-gray-900 mb-2">Добро пожаловать в API Документацию</h3>
              <p class="text-gray-600">
                Выберите метод из навигации слева, чтобы просмотреть подробную документацию
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import {
  BookOpen,
  Shield,
  User,
  Settings,
  Globe,
  Database,
  Send,
  CheckCircle,
  AlertCircle,
  Copy,
  ChevronDown,
  Package,
  Receipt,
  FileText,
  Warehouse,
  Truck,
  BarChart3,
  DollarSign,
  ClipboardList
} from 'lucide-vue-next'
import { authMethods } from './api/AuthMethods.js'
import { userMethods } from './api/UserMethods.js'
import { adminMethods } from './api/AdminMethods.js'
import { publicMethods } from './api/PublicMethods.js'
import { inventoryMethods } from './api/InventoryMethods.js'
import { warehouseMethods } from './api/WarehouseMethods.js'
import { transferMethods } from './api/TransferMethods.js'
import { balanceMethods } from './api/BalanceMethods.js'
import RegisterMethod from './api/RegisterMethod.vue'
import LoginMethod from './api/LoginMethod.vue'
import LogoutMethod from './api/LogoutMethod.vue'
import MeMethod from './api/MeMethod.vue'
import UserMethod from './api/UserMethod.vue'
import ProfileMethod from './api/ProfileMethod.vue'
import UserAvatarMethod from './api/UserAvatarMethod.vue'
import UserSettingsMethod from './api/UserSettingsMethod.vue'
import UserPersonalMethod from './api/UserPersonalMethod.vue'
import UserCompanyMethod from './api/UserCompanyMethod.vue'
import UserPasswordMethod from './api/UserPasswordMethod.vue'
import ProductsMethod from './api/ProductsMethod.vue'
import ProductDetailMethod from './api/ProductDetailMethod.vue'
import ProductDeleteMethod from './api/ProductDeleteMethod.vue'
import ProductImagesPostMethod from './api/ProductImagesPostMethod.vue'
import ProductImagesGetMethod from './api/ProductImagesGetMethod.vue'
import ProductImageDeleteMethod from './api/ProductImageDeleteMethod.vue'
import ProductDraftMethod from './api/ProductDraftMethod.vue'
import ProductUpdateMethod from './api/ProductUpdateMethod.vue'
import ReceiptsMethod from './api/ReceiptsMethod.vue'
import ReceiptDetailMethod from './api/ReceiptDetailMethod.vue'
import ReceiptCreateMethod from './api/ReceiptCreateMethod.vue'
import ReceiptUpdateMethod from './api/ReceiptUpdateMethod.vue'
import ReceiptDeleteMethod from './api/ReceiptDeleteMethod.vue'
import ReceiptFilesPostMethod from './api/ReceiptFilesPostMethod.vue'
import ReceiptFilesGetMethod from './api/ReceiptFilesGetMethod.vue'
import ReceiptFileDeleteMethod from './api/ReceiptFileDeleteMethod.vue'
import ReceiptFileDraftMethod from './api/ReceiptFileDraftMethod.vue'
import WriteOffsMethod from './api/WriteOffsMethod.vue'
import WriteOffDetailMethod from './api/WriteOffDetailMethod.vue'
import WriteOffCreateMethod from './api/WriteOffCreateMethod.vue'
import WriteOffUpdateMethod from './api/WriteOffUpdateMethod.vue'
import WriteOffDeleteMethod from './api/WriteOffDeleteMethod.vue'
import WriteOffFilesPostMethod from './api/WriteOffFilesPostMethod.vue'
import WriteOffFilesGetMethod from './api/WriteOffFilesGetMethod.vue'
import WriteOffFileDeleteMethod from './api/WriteOffFileDeleteMethod.vue'
import WriteOffFileDraftMethod from './api/WriteOffFileDraftMethod.vue'
import InventoriesMethod from './api/InventoriesMethod.vue'
import InventoryDetailMethod from './api/InventoryDetailMethod.vue'
import InventoryCreateMethod from './api/InventoryCreateMethod.vue'
import InventoryUpdateMethod from './api/InventoryUpdateMethod.vue'
import InventoryDeleteMethod from './api/InventoryDeleteMethod.vue'
import InventoryExportMethod from './api/InventoryExportMethod.vue'
import InventoryCalculateBalancesMethod from './api/InventoryCalculateBalancesMethod.vue'
import InventoryFilesUploadMethod from './api/InventoryFilesUploadMethod.vue'
import InventoryFileDraftMethod from './api/InventoryFileDraftMethod.vue'
import InventoryFileGetMethod from './api/InventoryFileGetMethod.vue'
import InventoryFileDeleteMethod from './api/InventoryFileDeleteMethod.vue'
import WarehousesMethod from './api/WarehousesMethod.vue'
import WarehouseDetailMethod from './api/WarehouseDetailMethod.vue'
import WarehouseCreateMethod from './api/WarehouseCreateMethod.vue'
import WarehouseUpdateMethod from './api/WarehouseUpdateMethod.vue'
import WarehouseDeleteMethod from './api/WarehouseDeleteMethod.vue'
import TransfersMethod from './api/TransfersMethod.vue'
import TransferFilterMethod from './api/TransferFilterMethod.vue'
import TransferAvailableProductsMethod from './api/TransferAvailableProductsMethod.vue'
import TransferAllProductsMethod from './api/TransferAllProductsMethod.vue'
import TransferCreateMethod from './api/TransferCreateMethod.vue'
import TransferDetailMethod from './api/TransferDetailMethod.vue'
import TransferUpdateMethod from './api/TransferUpdateMethod.vue'
import TransferDeleteMethod from './api/TransferDeleteMethod.vue'
import TransferConfirmMethod from './api/TransferConfirmMethod.vue'
import TransferCompleteMethod from './api/TransferCompleteMethod.vue'
import TransferCancelMethod from './api/TransferCancelMethod.vue'
import BalancesMethod from './api/BalancesMethod.vue'
import BalanceCreateMethod from './api/BalanceCreateMethod.vue'
import BalanceSummaryMethod from './api/BalanceSummaryMethod.vue'
import BalanceSummaryPostMethod from './api/BalanceSummaryPostMethod.vue'
import BalanceByWarehouseMethod from './api/BalanceByWarehouseMethod.vue'
import BalanceByProductMethod from './api/BalanceByProductMethod.vue'
import BalanceLowStockMethod from './api/BalanceLowStockMethod.vue'
import BalanceOutOfStockMethod from './api/BalanceOutOfStockMethod.vue'
import BalanceMovementsMethod from './api/BalanceMovementsMethod.vue'
import BalanceMovementsPostMethod from './api/BalanceMovementsPostMethod.vue'
import CategoriesMethod from './api/CategoriesMethod.vue'
import CategorySubcategoriesMethod from './api/CategorySubcategoriesMethod.vue'
import SubcategoriesMethod from './api/SubcategoriesMethod.vue'
import AdminUsersMethod from './api/AdminUsersMethod.vue'
import AdminUserDetailsMethod from './api/AdminUserDetailsMethod.vue'
import AdminRecentUsersMethod from './api/AdminRecentUsersMethod.vue'
import AdminStatsMethod from './api/AdminStatsMethod.vue'
import AdminProductsMethod from './api/AdminProductsMethod.vue'
import AdminProductsSearchMethod from './api/AdminProductsSearchMethod.vue'
import AdminSubcategoriesMethod from './api/AdminSubcategoriesMethod.vue'
import AdminReceiptsMethod from './api/AdminReceiptsMethod.vue'
import AdminReceiptDetailsMethod from './api/AdminReceiptDetailsMethod.vue'
import AdminWriteOffsMethod from './api/AdminWriteOffsMethod.vue'
import AdminWriteOffDetailsMethod from './api/AdminWriteOffDetailsMethod.vue'
import AdminInventoriesMethod from './api/AdminInventoriesMethod.vue'
import AdminInventoryDetailsMethod from './api/AdminInventoryDetailsMethod.vue'
import AdminBalancesMethod from './api/AdminBalancesMethod.vue'
import AdminBalancesPostMethod from './api/AdminBalancesPostMethod.vue'
import AdminBalanceMovementsMethod from './api/AdminBalanceMovementsMethod.vue'
import AdminTransfersMethod from './api/AdminTransfersMethod.vue'
import AdminTransferDetailsMethod from './api/AdminTransferDetailsMethod.vue'

export default {
  name: 'ApiDocumentationPage',
  components: {
    BookOpen,
    Shield,
    User,
    Settings,
    Globe,
    Database,
    Send,
    CheckCircle,
    AlertCircle,
    Copy,
    ChevronDown,
    Package,
    Receipt,
    FileText,
    Warehouse,
    Truck,
    BarChart3,
    DollarSign,
    ClipboardList,
    RegisterMethod,
    LoginMethod,
    LogoutMethod,
    MeMethod,
    UserMethod,
    ProfileMethod,
    UserAvatarMethod,
    UserSettingsMethod,
    UserPersonalMethod,
    UserCompanyMethod,
    UserPasswordMethod,
    ProductsMethod,
    ProductDetailMethod,
    ProductDeleteMethod,
    ProductImagesPostMethod,
    ProductImagesGetMethod,
    ProductImageDeleteMethod,
    ProductDraftMethod,
    ProductUpdateMethod,
    ReceiptsMethod,
    ReceiptDetailMethod,
    ReceiptCreateMethod,
    ReceiptUpdateMethod,
    ReceiptDeleteMethod,
    ReceiptFilesPostMethod,
    ReceiptFilesGetMethod,
    ReceiptFileDeleteMethod,
    ReceiptFileDraftMethod,
    WriteOffsMethod,
    WriteOffDetailMethod,
    WriteOffCreateMethod,
    WriteOffUpdateMethod,
    WriteOffDeleteMethod,
    WriteOffFilesPostMethod,
    WriteOffFilesGetMethod,
    WriteOffFileDeleteMethod,
    WriteOffFileDraftMethod,
    InventoriesMethod,
    InventoryDetailMethod,
    InventoryCreateMethod,
    InventoryUpdateMethod,
    InventoryDeleteMethod,
    InventoryExportMethod,
    InventoryCalculateBalancesMethod,
    InventoryFilesUploadMethod,
    InventoryFileDraftMethod,
    InventoryFileGetMethod,
    InventoryFileDeleteMethod,
    WarehousesMethod,
    WarehouseDetailMethod,
    WarehouseCreateMethod,
    WarehouseUpdateMethod,
    WarehouseDeleteMethod,
    TransfersMethod,
    TransferFilterMethod,
    TransferAvailableProductsMethod,
    TransferAllProductsMethod,
    TransferCreateMethod,
    TransferDetailMethod,
    TransferUpdateMethod,
    TransferDeleteMethod,
    TransferConfirmMethod,
    TransferCompleteMethod,
    TransferCancelMethod,
    BalancesMethod,
    BalanceCreateMethod,
    BalanceSummaryMethod,
    BalanceSummaryPostMethod,
    BalanceByWarehouseMethod,
    BalanceByProductMethod,
    BalanceLowStockMethod,
    BalanceOutOfStockMethod,
    BalanceMovementsMethod,
    BalanceMovementsPostMethod,
    CategoriesMethod,
    CategorySubcategoriesMethod,
    SubcategoriesMethod,
    AdminUsersMethod,
    AdminUserDetailsMethod,
    AdminRecentUsersMethod,
    AdminStatsMethod,
    AdminProductsMethod,
    AdminProductsSearchMethod,
    AdminSubcategoriesMethod,
    AdminReceiptsMethod,
    AdminReceiptDetailsMethod,
    AdminWriteOffsMethod,
    AdminWriteOffDetailsMethod,
    AdminInventoriesMethod,
    AdminInventoryDetailsMethod,
    AdminBalancesMethod,
    AdminBalancesPostMethod,
    AdminBalanceMovementsMethod,
    AdminTransfersMethod,
    AdminTransferDetailsMethod
  },
  data() {
    return {
      mobileNavOpen: false,
      selectedMethod: null,
      openUserSections: {
        user: false,
        products: false,
        receipts: false,
        writeOffs: false,
        inventories: false,
        warehouses: false,
        transfers: false,
        balances: false,
        currencies: false
      },
      openAdminSections: {
        users: false,
        products: false,
        receipts: false,
        writeOffs: false,
        inventories: false,
        balances: false,
        transfers: false
      },
      authMethods: authMethods,
      userMethods: userMethods,
      adminMethods: adminMethods,
      publicMethods: publicMethods,
      productMethods: [
        { id: 'products', method: 'GET', path: '/products' },
        { id: 'product-detail', method: 'GET', path: '/products/{id}' },
        { id: 'product-delete', method: 'DELETE', path: '/products/{id}' },
        { id: 'product-images-post', method: 'POST', path: '/products/{id}/images' },
        { id: 'product-images-get', method: 'GET', path: '/products/{id}/images' },
        { id: 'product-image-delete', method: 'DELETE', path: '/products/images/{id}' },
        { id: 'product-draft', method: 'POST', path: '/products/draft' },
        { id: 'product-update', method: 'PUT', path: '/products/{id}' }
      ],
      receiptMethods: [
        { id: 'receipts', method: 'GET', path: '/receipts' },
        { id: 'receipt-detail', method: 'GET', path: '/receipts/{id}' },
        { id: 'receipt-create', method: 'POST', path: '/receipts' },
        { id: 'receipt-update', method: 'PUT', path: '/receipts/{id}' },
        { id: 'receipt-delete', method: 'DELETE', path: '/receipts/{id}' },
        { id: 'receipt-files-post', method: 'POST', path: '/receipt-files' },
        { id: 'receipt-files-get', method: 'GET', path: '/receipt-files/{receiptId}' },
        { id: 'receipt-file-delete', method: 'DELETE', path: '/receipt-files/{id}' },
        { id: 'receipt-file-draft', method: 'POST', path: '/receipt-files/draft' }
      ],
      writeOffMethods: [
        { id: 'write-offs', method: 'GET', path: '/write-offs' },
        { id: 'write-off-detail', method: 'GET', path: '/write-offs/{id}' },
        { id: 'write-off-create', method: 'POST', path: '/write-offs' },
        { id: 'write-off-update', method: 'PUT', path: '/write-offs/{id}' },
        { id: 'write-off-delete', method: 'DELETE', path: '/write-offs/{id}' },
        { id: 'write-off-files-post', method: 'POST', path: '/write-off-files' },
        { id: 'write-off-files-get', method: 'GET', path: '/write-off-files/{writeOffId}' },
        { id: 'write-off-file-delete', method: 'DELETE', path: '/write-off-files/{id}' },
        { id: 'write-off-file-draft', method: 'POST', path: '/write-off-files/draft' }
      ],
      inventoryMethods: [
        { id: 'inventories', method: 'GET', path: '/inventories' },
        { id: 'inventory-detail', method: 'GET', path: '/inventories/{id}' },
        { id: 'inventory-create', method: 'POST', path: '/inventories' },
        { id: 'inventory-update', method: 'PUT', path: '/inventories/{id}' },
        { id: 'inventory-delete', method: 'DELETE', path: '/inventories/{id}' },
        { id: 'inventory-export', method: 'GET', path: '/inventories/{id}/export' },
        { id: 'inventory-calculate-balances', method: 'POST', path: '/inventories/calculate-balances' },
        { id: 'inventory-files-upload', method: 'POST', path: '/inventory-files/upload' },
        { id: 'inventory-file-draft', method: 'POST', path: '/inventory-files/upload-draft' },
        { id: 'inventory-file-get', method: 'GET', path: '/inventory-files/{id}' },
        { id: 'inventory-file-delete', method: 'DELETE', path: '/inventory-files/{id}' }
      ],
      warehouseMethods: warehouseMethods,
      transferMethods: transferMethods,
      balanceMethods: balanceMethods,
      adminUserMethods: [
        { id: 'admin-users', method: 'GET', path: '/admin/users' },
        { id: 'admin-user-details', method: 'GET', path: '/admin/users/{id}' },
        { id: 'admin-recent-users', method: 'GET', path: '/admin/recent-users' },
        { id: 'admin-stats', method: 'GET', path: '/admin/stats' }
      ],
      adminProductMethods: [
        { id: 'admin-products', method: 'GET', path: '/admin/products' },
        { id: 'admin-products-search', method: 'POST', path: '/admin/products/search' },
        { id: 'admin-subcategories', method: 'GET', path: '/admin/subcategories' }
      ],
      adminReceiptMethods: [
        { id: 'admin-receipts', method: 'GET', path: '/admin/receipts' },
        { id: 'admin-receipt-details', method: 'GET', path: '/admin/receipts/{id}' }
      ],
      adminWriteOffMethods: [
        { id: 'admin-write-offs', method: 'GET', path: '/admin/write-offs' },
        { id: 'admin-write-off-details', method: 'GET', path: '/admin/write-offs/{id}' }
      ],
      adminInventoryMethods: [
        { id: 'admin-inventories', method: 'GET', path: '/admin/inventories' },
        { id: 'admin-inventory-details', method: 'GET', path: '/admin/inventories/{id}' }
      ],
      adminBalanceMethods: [
        { id: 'admin-balances', method: 'GET', path: '/admin/balances' },
        { id: 'admin-balances-post', method: 'POST', path: '/admin/balances' },
        { id: 'admin-balance-movements', method: 'POST', path: '/admin/balances/movements' }
      ],
      adminTransferMethods: [
        { id: 'admin-transfers', method: 'GET', path: '/admin/transfers' },
        { id: 'admin-transfer-details', method: 'GET', path: '/admin/transfers/{id}' }
      ]
    }
  },
  methods: {
    toggleMobileNav() {
      this.mobileNavOpen = !this.mobileNavOpen
    },
    selectMethod(method) {
      this.selectedMethod = method
      // На мобильных устройствах закрываем навигацию после выбора метода
      if (window.innerWidth < 1024) {
        this.mobileNavOpen = false
      }
    },
    getMethodColor(method) {
      const colors = {
        GET: 'bg-green-100 text-green-800 border border-green-200',
        POST: 'bg-blue-100 text-blue-800 border border-blue-200',
        PUT: 'bg-yellow-100 text-yellow-800 border border-yellow-200',
        DELETE: 'bg-red-100 text-red-800 border border-red-200'
      }
      return colors[method] || 'bg-gray-100 text-gray-800 border border-gray-200'
    },
    async copyToClipboard(text) {
      try {
        await navigator.clipboard.writeText(text)
        // Можно добавить уведомление об успешном копировании
      } catch (err) {
        console.error('Failed to copy text: ', err)
      }
    },
    toggleUserSection(section) {
      this.openUserSections[section] = !this.openUserSections[section]
    },
    toggleAdminSection(section) {
      this.openAdminSections[section] = !this.openAdminSections[section]
    }
  }
}
</script>

<style scoped>
/* Дополнительные стили если нужны */
</style>

<style>
/* Скрываем футер на странице API документации */
footer, .bg-green-100:has(svg), .bg-blue-100:has(svg), .bg-yellow-100:has(svg), .bg-red-100:has(svg) {
  display: none !important;
}
</style> 